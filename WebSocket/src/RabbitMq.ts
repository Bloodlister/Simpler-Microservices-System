import * as amqp from 'amqplib/callback_api';
import { credentials } from 'amqplib';

const RABBITMQ_HOST = String(process.env.RMQ_HOST);
const RABBITMQ_PORT = String(process.env.RMQ_PORT);
const RABBITMQ_USER = String(process.env.RMQ_USER);
const RABBITMQ_PASS = String(process.env.RMQ_PASS);


export default class RabbitMq {
    public start() {
        const opt = { credentials: credentials.plain(RABBITMQ_USER, RABBITMQ_PASS) };
        amqp.connect(`amqp://${RABBITMQ_HOST}:${RABBITMQ_PORT}`, opt, function (err, conn) {
            if (err) {
                throw err;
            }
        });
    }
}