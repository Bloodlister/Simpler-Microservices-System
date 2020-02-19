const jwt = require('jsonwebtoken');
const reddis = require('./redis.js');
const md5 = require('md5');

const JWT_SECRET = 'secret';

function getUserToken(username, passwordHash) {
  return new Promise((resolve, reject) => {

    reddis.getUserJWT(username)
      .then(token => {
        if (token === null) {
          reject({
            name: 'User does not exist',
            message: 'Could not find user'
          });
        }


        jwt.verify(token, JWT_SECRET, (error, result) => {
          if (userData.password !== passwordHash) {
            reject({
              name: 'PasswordMismatch',
              message: 'Passwords do not match'
            });
          }

          if (error && error.name === 'TokenExpiredError') {
            let newToken = jwt.sign({ username, passwordHash });
            reddis.setUserJWT(username, newToken)
              .then(token => {
                resolve({
                  status: 'new',
                  token: newToken
                });
              });

          }

          resolve({
            status: 'old',
            token: token
          });
        });
      });
  })
}