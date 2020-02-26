'use strict';

const express = require('express');
const bodyParser = require('body-parser');
const jwt = require('jsonwebtoken');
const rabbitmq = require('./rabbitmq.js');
const redis = require('./redis.js');
const users = require('./users.js');
const cors = require('cors');
const md5 = require('md5');

const PORT = 8080;

const app = express();

app.use(bodyParser.urlencoded({extended: false}));
app.use(bodyParser.json());
app.use(cors({
    origin: '*',
}));


app.get('/', (req, res) => {
    res.send('well helloo there');
});

app.post('/users', async (req, res) => {
    let username = req.body.username;
    let password = req.body.password;
    let passwordConf = req.body.passwordConf;

    let token = await redis.getUserJWT(username);

    if (token !== null) {
        res.status(400).send(JSON.stringify({error: 'UserAlreadyExistsException'}));
        return;
    }

    if (password !== passwordConf) {
        res.status(400).send(JSON.stringify({error: 'PasswordMismatchException'}));
        return;
    }

    if (token === null) {
        let registerDetails = {
            username: username,
            password: password,
            passwordConf: passwordConf,
        };

        rabbitmq.getChannel().sendToQueue('userRegisterRequest', Buffer.from(JSON.stringify(registerDetails)));
        res.status(200).send(JSON.stringify({status: 'WaitingConfirmation'}));
    }
});

app.post('/token', async (req, res) => {
    let username = req.body.username;
    let passwordHash = md5(req.body.password);

    let token = await redis.getUserJWT(username);

    if (token === null) {
        res.status(401).send(JSON.stringify({error: 'Unauthorized'}));
    }

    try {
        let tokenData = await users.decodeToken(token);

        if (tokenData) {
            res.status(200).send(JSON.stringify({token: token}));
        }
    } catch (e) {
        if (e.name === 'TokenExpiredError') {
            let tokenDecoded = jwt.decode(token);
            if (tokenDecoded.payload.passwordHash !== passwordHash) {
                res.status(401).send(JSON.stringify({error: 'Unauthorized'}));
                return;
            }

            redis.setUserJWT(username, jwt.sign(tokenDecoded.payload, redis.JWT_SECRET))
                .then(token => {
                    res.status(201).send(JSON.stringify({token: token}));
                    return;
                });
        } else {
            res.status(500).send(JSON.stringify({error: e.message}));
            return;
        }
    }
});


(async function () {
    await rabbitmq.connectToRabbitMQ();
    await rabbitmq.connectToChannels();
    await redis.connectToRedis();

    app.listen(PORT, () => {
        console.log(`Listening to port ${PORT}`);
    });
})();
