'use strict';

const express = require('express');
const bodyParser = require('body-parser');
const redis = require('./redis.js');
const rabbitmq = require('./rabbitmq');

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

    redis.get('user_' + username, (err, username) => {
        console.log('Here');
        if (err) {
            throw err;
        }

        if (username) {
            res.send(JSON.stringify({ error: 'UserAlreadyExistsException' }));
            return;
        }

        if (password !== passwordConf) {
            res.send(JSON.stringify({ error: 'PasswordMismatchException' }));
            return;
        }


    });
});

app.post('/token', (req, res) => {

});

(async function () {
    await rabbitmq.connectToRabbitMQ();
    await redis.connectToRedis();

    app.listen(PORT, () => {
        console.log(`Listening to port ${PORT}`);
    });
})();