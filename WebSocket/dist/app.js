"use strict";
exports.__esModule = true;
var WebSocket_1 = require("./WebSocket");
var RabbitMq_1 = require("./RabbitMq");
var websocket = new WebSocket_1["default"]();
var rabbitMq = new RabbitMq_1["default"]();
rabbitMq.start();
websocket.start();
