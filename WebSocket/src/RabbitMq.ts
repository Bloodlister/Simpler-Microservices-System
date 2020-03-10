import * as amqp from 'amqplib/callback_api';
import { credentials } from 'amqplib';
import WebSocket from './WebSocket';

const RABBITMQ_HOST = String(process.env.RMQ_HOST);
const RABBITMQ_PORT = String(process.env.RMQ_PORT);
const RABBITMQ_USER = String(process.env.RMQ_USER);
const RABBITMQ_PASS = String(process.env.RMQ_PASS);


export default class RabbitMq {
    public start(): void {
        const opt = { credentials: credentials.plain(RABBITMQ_USER, RABBITMQ_PASS) };
        amqp.connect(`amqp://${RABBITMQ_HOST}:${RABBITMQ_PORT}`, opt, rabbitMQConnectionHandler);
    }
}

function rabbitMQConnectionHandler(conErr: any, connection: amqp.Connection): void {
    if (conErr) {
        throw conErr;
    }

    createChannel(connection).then((channel: amqp.Channel) => createQueues(channel));
}

function createChannel(connection: amqp.Connection): Promise<amqp.Channel> {
    return new Promise(resolve => {
        connection.createChannel((err: any, channel: amqp.Channel) => {
            if (err) {
                console.log('Failed to create channel');
                setTimeout(() => resolve(createChannel(connection)), 3000);
            }

            resolve(channel);
        })
    });
}

function createQueues(channel: amqp.Channel): void {
    channel.assertQueue('websocket', { durable: false });
    channel.consume('websocket', messageHandler, { noAck: true })
}

function messageHandler(message: amqp.Message): void {
    const messageData = JSON.parse(message.content.toString());

    const receiver = messageData.receiver;
    const payloadData = messageData.data;

    WebSocket.sendToWS(receiver, JSON.stringify(payloadData));
}
