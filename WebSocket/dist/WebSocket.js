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
                Object.assign(socket, { id: id });
                WebSocket.connections[id].push(socket);
                WebSocket.sendId(socket);
            }
            else {
                Object.assign(socket, { id: id });
                if (WebSocket.connections[id] === undefined) {
                    socket.close();
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
            WebSocket.clearSocket(this);
        };
    };
    WebSocket.sendId = function (socket) {
        WebSocket.connections[socket.id].forEach(function (socket) {
            socket.send(JSON.stringify({ action: 'init', data: { newId: socket.id } }));
        });
    };
    WebSocket.addSocket = function (socket) {
        WebSocket.connections[socket.id].push(socket);
        console.log(Object.keys(WebSocket.connections).length);
    };
    WebSocket.clearSocket = function (socket) {
        if (!WebSocket.connections[socket.id]) {
            return;
        }
        WebSocket.connections[socket.id].filter(function (wsSocket) {
            if (wsSocket === socket) {
                return true;
            }
            return false;
        });
        if (WebSocket.connections[socket.id].length === 0) {
            delete WebSocket.connections[socket.id];
        }
        console.log(Object.keys(WebSocket.connections).length);
    };
    WebSocket.connections = {};
    return WebSocket;
}());
exports["default"] = WebSocket;
