const rabbitmq = require('amqplib/callback_api');
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

            mqChannel.assertQueue('userRegisterResults', {
                durable: false
            });

            resolve(channel);
        });
    });
}

module.exports = {
    connection,
    connectToRabbitMQ,
    connectToChannels,
    channel,
};
