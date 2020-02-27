let socket = undefined;
import toastr from 'toastr';

class MessageHandler {
    static handle(message) {
        try {
            let messageData = JSON.parse(message.data);
            MessageHandler[messageData.action](messageData.data);
        } catch (e) {
            console.log(e);
        }
    }

    static toastr_notification(notification) {
        toastr[notification.status](notification.desc, notification.title, {
            timeOut: 50000
        });
    }
}

export function connectToSocket(uid) {
    return new Promise(resolve => {
        if (socket === undefined) {
            socket = new WebSocket('ws://ws.simple.com?' + uid);
            socket.onopen = function () {
                console.log('Open');
                resolve(socket);
            };
            socket.onerror = function(err) {
                console.log(err);
                console.log('Error')
            };

            socket.onclose = function(err) {
                console.log(err);
                console.log('Closed');
            }
            socket.onmessage = function(message) {
                MessageHandler.handle(message);
            }
        } else {
            resolve(socket);
        }
    });
}

export function disconnectFromSocket() {
    socket.close();
}
