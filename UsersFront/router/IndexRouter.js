const router = require('express').Router();

const IndexController = require('./Controllers/IndexController.js');

router.get('/init', IndexController.initAction);
router.post('/users', IndexController.usersAction);
router.post('/token', IndexController.tokenAction);
router.get('/testWS', IndexController.testWSAction);

module.exports = router;
