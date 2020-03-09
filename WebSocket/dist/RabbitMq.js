"use strict";
exports.__esModule = true;
var amqp = require("amqplib/callback_api");
var amqplib_1 = require("amqplib");
var RABBITMQ_HOST = String(process.env.RMQ_HOST);
var RABBITMQ_PORT = String(process.env.RMQ_PORT);
var RABBITMQ_USER = String(process.env.RMQ_USER);
var RABBITMQ_PASS = String(process.env.RMQ_PASS);
var RabbitMq = /** @class */ (function () {
    function RabbitMq() {
    }
    RabbitMq.prototype.start = function () {
        var opt = { credentials: amqplib_1.credentials.plain(RABBITMQ_USER, RABBITMQ_PASS) };
        amqp.connect("amqp://" + RABBITMQ_HOST + ":" + RABBITMQ_PORT, opt, rabbitMQConnectionHandler);
    };
    return RabbitMq;
}());
exports["default"] = RabbitMq;
function rabbitMQConnectionHandler(conErr, connection) {
    if (conErr) {
        throw conErr;
    }
    createChannel(connection);
}
function createChannel(connection) {
    return new Promise(function (resolve, reject) {
        connection.createChannel(function (err, channel) {
            if (err) {
                console.log('Failed to create channel');
                setTimeout(function () { return resolve(createChannel(connection)); }, 3000);
            }
            resolve(channel);
        });
    });
}
