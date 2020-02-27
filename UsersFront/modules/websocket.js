const WebSocket = require('ws');
const WEBSOCKET_URL = 'ws://websocket';
const WEBSOCKET_ID = 'users_front';
let websocket;

function connect() {
    return new Promise((resolve, reject) => {
        if (websocket === undefined) {
            websocket = new WebSocket(WEBSOCKET_URL + '/?' + WEBSOCKET_ID);
            websocket.on('open', function () {
                console.log('Connected to WebSocket: ' + WEBSOCKET_URL);
                resolve(websocket);
            });

            websocket.on('error', function (err) {
                console.log('Could not connect to WebSocket: ' + WEBSOCKET_URL);
                console.log(err);
                reject(err);
            });
        } else {
            resolve(websocket);
        }
    })
}

function tryUntilConnected(interval) {
    return new Promise((resolve) => {
        connect()
            .then(resolve)
            .catch(err => {
                console.log(err);
                console.log('Could not connect to WebSocket');
                console.log('Retrying');
                setTimeout(() => resolve(tryUntilConnected(interval)), interval)
            });
    });
}

module.exports = {
    connect,
    tryUntilConnected,
};

