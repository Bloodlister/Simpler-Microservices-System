"use strict";
exports.__esModule = true;
var Socket_1 = require("./Socket");
var RabbitMq_1 = require("./RabbitMq");
Socket_1["default"].start();
RabbitMq_1["default"].start();
