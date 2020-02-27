const rabbitmq = require('amqplib/callback_api');
const redis = require('./redis');
const jwt = require('jsonwebtoken');
const users = require('./users.js');

let connection;
let channel;

async function connectToRabbitMQ() {
    return new Promise((resolve, reject) => {
        rabbitmq.connect({
            hostname: 'rabbitmq',
            username: 'rabbitmq_user',
            password: 'password'
        }, (error, rabbitmqConnection) => {
            if (error) reject(error);

            console.log('Connected to rabbitmq');
            connection = rabbitmqConnection;

            resolve(connection);
        })
    });
}

function tryUntilConnected(interval) {
    return new Promise(resolve => {
        connectToRabbitMQ()
            .then(res => resolve(res))
            .catch(err => {
                console.log(err);
                setTimeout(() => {
                    resolve(tryUntilConnected(interval));
                }, interval);
            })
    });
}

function connectToChannels() {
    return new Promise(resolve => {
        connection.createChannel((err, mqChannel) => {
            if (err) {
                setTimeout(() => {
                    connectToChannels()
                        .then(resolve);
                }, 3000);
            }
            channel = mqChannel;
            listenForUserRegistrations(channel);

            resolve(channel);
        });
    });
}

function listenForUserRegistrations(channel) {
    channel.assertQueue('userRegisterResults', {
        durable: false
    });

    channel.consume('userRegisterResult', (msg) => {
        let msgData = JSON.parse(msg.content.toString());
        redis.setUserJWT(msgData.username, jwt.sign({passwordHash: msgData.password}, users.JWT_SECRET, {expiresIn: '60000'}));

    }, { noAck: true });

    console.log('Listening to channels');
}

function getChannel() {
    return channel;
}

module.exports = {
    connectToRabbitMQ,
    tryUntilConnected,
    connectToChannels,
    getChannel,
};
