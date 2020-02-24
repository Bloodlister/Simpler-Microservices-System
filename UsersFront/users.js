const jwt = require('jsonwebtoken');
const reddis = require('./redis.js');
const md5 = require('md5');

const JWT_SECRET = 'secret';

function tokenIsValid(token) {
  return new Promise(resolve, reject => {
    jwt.verify(token, JWT_SECRET, (err, data) => {
      if (err && err.name === 'TokenExpiredError') {

      }
    });
  });
}

module.exports = {
  JWT_SECRET,

};
