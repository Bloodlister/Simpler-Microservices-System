"use strict";
exports.__esModule = true;
var WebSocket_1 = require("./WebSocket");
function socketCloseHandler() {
    WebSocket_1["default"].clearSocket(this.id);
}
exports.socketCloseHandler = socketCloseHandler;
