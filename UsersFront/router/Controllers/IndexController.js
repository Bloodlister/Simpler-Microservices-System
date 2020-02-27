const md5 = require('md5');
const jwt = require('jsonwebtoken');
const redis = require('../../modules/redis.js');
const rabbitmq = require('../../modules/rabbitmq.js');
const users = require('../../modules/users.js');
const websocket = require('../../modules/websocket.js');

module.exports = class IndexController {
    static initAction(req, res) {
        if (req.cookies.uid === undefined) {
            let uniqueId = uuid();
            res.cookie('uid', uniqueId);
            res.status(200).json({uid: uniqueId});
        } else {
            res.status(200).json({uid: req.cookies.uid});
        }
    }

    static testWSAction(req, res) {
        websocket.connect()
            .then(socket => {
                socket.send(JSON.stringify({
                    receiver: req.cookies.uid,
                    data: {
                        message: 'Hello'
                    }
                }))
            });

        res.send('sent');
    }

    static async usersAction(req, res) {
        let username = req.body.username;
        let password = req.body.password;
        let passwordConf = req.body.passwordConf;

        let token = await redis.getUserJWT(username);

        if (token !== null) {
            res.status(400).json({error: 'UserAlreadyExistsException'});
            return;
        }

        if (password !== passwordConf) {
            res.status(400).json({error: 'PasswordMismatchException'});
            return;
        }

        let registerDetails = {
            issuer: req.cookies.uid,
            username: username,
            password: password,
            passwordConf: passwordConf,
        };

        rabbitmq.getChannel().sendToQueue('userRegisterRequest', Buffer.from(JSON.stringify(registerDetails)));
        res.status(200).json({status: 'WaitingConfirmation'});
    }

    static async tokenAction(req, res) {
        let username = req.body.username;
        let passwordHash = md5(req.body.password);

        let token = await redis.getUserJWT(username);

        if (token === null) {
            res.status(401).json({error: 'Unauthorized'});
        }

        try {
            let tokenData = await users.decodeToken(token);

            if (tokenData && tokenData.passwordHash === passwordHash) {
                res.status(200).json({token: token});
            } else {
                res.status(401).json({error: 'Unauthorized'});
            }
        } catch (e) {
            if (e.name === 'TokenExpiredError') {
                let tokenDecoded = jwt.decode(token);

                if (tokenDecoded.passwordHash !== passwordHash) {
                    res.status(401).json({error: 'Unauthorized'});
                }

                redis.setUserJWT(username, jwt.sign({passwordHash: passwordHash}, users.JWT_SECRET, {expiresIn: '60000'}))
                    .then(token => {
                        res.status(201).json({token: token});
                    });
            } else {
                res.status(500).send(JSON.stringify({error: e.message}));
            }
        }
    }
};
