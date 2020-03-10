import * as ws from 'ws';

const WS_PORT: number = Number(process.env.WS_PORT) || 8080;

export default class WebSocket {
    private server: ws.Server;

    private static connections = {};

    public start() {
        this.server = this.createServer();
        this.server.on('connection', this.initializeConnection())
    }

    private createServer(): ws.Server {
        return new ws.Server({
            port: WS_PORT,
        });
    }

    private initializeConnection() {
        return (socket: ws.Server, req) => {
            let id = req.url.slice(1);
            if (!id) {
                id = Math.random().toString(36).slice(-11);
                if (!WebSocket.connections[id]) {
                    WebSocket.connections[id] = [];
                }

                Object.assign(socket, { id: id });
                WebSocket.connections[id].push(socket);

                WebSocket.sendId(socket);
            } else {
                Object.assign(socket, { id: id });

                if (WebSocket.connections[id] === undefined) {
                    socket.close();
                    return;
                }

                WebSocket.connections[id].push(socket);
            }

            socket.on('close', this.socketCloseHandler());

            WebSocket.addSocket(socket);
        }
    }

    public static sendToWS(receiver: string, message: string): void {
        WebSocket.connections[receiver].forEach(socket => {
            socket.send(message);
        });
    }

    private socketCloseHandler() {
        return function () {
            WebSocket.clearSocket(this);
        }
    }

    public static sendId(socket): void {
        WebSocket.connections[socket.id].forEach((socket) => {
            socket.send(JSON.stringify({ action: 'init', data: {newId: socket.id} }));
        })
    }

    public static addSocket(socket): void {
        WebSocket.connections[socket.id].push(socket);
        console.log(Object.keys(WebSocket.connections).length);
    }

    public static clearSocket(socket): void {
        if (!WebSocket.connections[socket.id]) {
            return;
        }

        WebSocket.connections[socket.id].filter((wsSocket) => {
            if (wsSocket === socket) {
                return true;
            }

            return false;
        });

        if (WebSocket.connections[socket.id].length === 0) {
            delete WebSocket.connections[socket.id];
        }

        console.log(Object.keys(WebSocket.connections).length);
    }
}
