const router = require('express').Router();

const IndexController = require('./Controllers/IndexController.js');

router.post('/users', IndexController.usersAction);
router.post('/token', IndexController.tokenAction);

module.exports = router;
