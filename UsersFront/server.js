'use strict';

const {setupApp, connectToExternal} = require('./serverSetup');
const express = require('express');
const router = require('./router/IndexRouter.js');
const PORT = 8080;

const app = express();

setupApp(app);
app.use('/', router);

(async function () {
    connectToExternal().then(() => {
        app.listen(PORT, () => {
            console.log(`Listening to port ${PORT}`);
        });
    }).catch();
})();
