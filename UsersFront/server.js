'use strict';

const express = require('express');
const bodyParser = require('body-parser');
const jwt = require('jsonwebtoken');
const rabbitmq = require('./rabbitmq.js');
const redis = require('./redis.js');
const users = require('./users.js');

const PORT = 8080;

const app = express();

app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

app.get('/', (req, res) => {
    res.send('well helloo there');
});

app.post('/users', async (req, res) => {
    let username = req.body.username;
    let password = req.body.password;
    let passwordConf = req.body.passwordConf;

    redis.get(username, (err, username) => {
        if (err) {
            throw err;
        }

        if (username) {
            res.status(400).send(JSON.stringify({ error: 'UserAlreadyExistsException' }));
            return;
        }

        if (password !== passwordConf) {
            res.status(400).send(JSON.stringify({ error: 'PasswordMismatchException' }));
            return;
        }

        if (username === null) {
            let registerDetails = {
                username: username,
                password: password,
                passwordConf: passwordConf,
            };

            rabbitmq.channel.sendToQueue('userRegisterRequest', Buffer.from(JSON.stringify(registerDetails)));
            res.status(200).send(JSON.stringify({ status: 'WaitingConfirmation' }));
        }
    });
});

app.post('/token', async (req, res) => {
    let token = await redis.getUserJWT(req.body.username, req.body.passwordHash);
    if (users.tokenIsValid(token)) {
        res.status(200).send(JSON.stringify({ token: token }));
    }
});

(async function () {
    await rabbitmq.connectToRabbitMQ();
    await redis.connectToRedis();

    app.listen(PORT, () => {
        console.log(`Listening to port ${PORT}`);
    });
})();
