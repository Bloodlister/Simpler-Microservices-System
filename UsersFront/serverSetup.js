const bodyParser = require('body-parser');
const cors = require('cors');
const cookieParser = require('cookie-parser');
const redis = require('./modules/redis.js');
const rabbitmq = require('./modules/rabbitmq.js');

module.exports.setupApp = function (app) {
    app.use(bodyParser.urlencoded({extended: false}));
    app.use(bodyParser.json());
    app.use(cookieParser());
    app.use(cors({
        origin: 'http://front.simple.com',
        credentials: true
    }));
};

async function connectToExternal() {
    return new Promise(async (resolve) => {
        await rabbitmq.tryUntilConnected(3000);
        await rabbitmq.connectToChannels();
        await redis.tryUntilConnected(3000);

        resolve();
    });
}

module.exports.connectToExternal = connectToExternal;

function tryUntilFullyConnected() {
    return new Promise(resolve => {
        connectToExternal()
            .then(resolve)
            .catch(err => {
                console.log(err);
                setTimeout(() => {resolve(tryUntilFullyConnected())}, 3000);
            })
    })
}

module
