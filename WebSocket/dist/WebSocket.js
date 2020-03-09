"use strict";
exports.__esModule = true;
var ws = require("ws");
var WS_PORT = Number(process.env.WS_PORT) || 8080;
var WebSocket = /** @class */ (function () {
    function WebSocket() {
    }
    WebSocket.prototype.start = function () {
        this.server = this.createServer();
        this.server.on('connection', this.initializeConnection());
    };
    WebSocket.prototype.createServer = function () {
        return new ws.Server({
            port: WS_PORT
        });
    };
    WebSocket.prototype.initializeConnection = function () {
        var _this = this;
        return function (socket, req) {
            var id = req.url.slice(1);
            if (!id) {
                id = Math.random().toString(36).slice(-11);
                if (!WebSocket.connections[id]) {
                    WebSocket.connections[id] = [];
                }
                socket.id = id;
                WebSocket.connections[id].push(socket);
                WebSocket.sendId(socket);
            }
            else {
                socket.id = id;
                if (WebSocket.connections[id] === undefined) {
                    socket.destroy();
                    return;
                }
                WebSocket.connections[id].push(socket);
            }
            socket.on('close', _this.socketCloseHandler());
            WebSocket.addSocket(socket);
        };
    };
    WebSocket.sendToWS = function (receiver, message) {
        WebSocket.connections[receiver].forEach(function (socket) {
            socket.send(message);
        });
    };
    WebSocket.prototype.socketCloseHandler = function () {
        return function () {
            WebSocket.clearSocket(this.id);
        };
    };
    WebSocket.sendId = function (socket) {
        WebSocket.connections[socket.id].forEach(function (socket) {
            socket.send(JSON.stringify({ newId: socket.id }));
        });
    };
    WebSocket.addSocket = function (socket) {
        WebSocket.connections[socket.id] = socket;
        console.log(Object.keys(WebSocket.connections).length);
    };
    WebSocket.clearSocket = function (id) {
        delete WebSocket.connections[id];
        console.log(Object.keys(WebSocket.connections).length);
    };
    WebSocket.connections = {};
    return WebSocket;
}());
exports["default"] = WebSocket;
