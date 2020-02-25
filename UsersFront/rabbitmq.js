const rabbitmq = require('amqplib/callback_api');
const redis = require('./redis');
let connection;
let channel;

function connectToRabbitMQ() {
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

function connectToChannels() {
    return new Promise((resolve, reject) => {
        connection.createChannel((err, mqChannel) => {
            if (err) reject(err);
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


    channel.consume('userRegisterResults', (msg) => {
        let msgData = JSON.stringify(msg.content.toString());
        if (msgData.success) {
            redis.setUserJWT(msgData.username, {passwordHash: msgData.passwordHash});
        }
    });
}

module.exports = {
    connectToRabbitMQ,
    connectToChannels,
};
