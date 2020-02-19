const redis = require('redis');
let redisClient = undefined;

function connectToRedis() {
  return new Promise((resolve, reject) => {
    redisClient = redis.createClient({
      host: 'redis'
    });

    redisClient.on('connect', () => {
      console.log('Connected to redis server');
      redisClient = redisClient;
      resolve(redisClient);
    });
  });
};

function getUserJWT(username) {
  return new Promise((resolve, reject) => {
    connection.get('jwt_' + username, function (err, result) {
      if (err) reject(err);

      resolve(result);
    });
  });
}

function setUserJWT(username, token) {
  return new Promise((resolve, reject) => {
    connection.set('jwt_' + username, token, (err) => {
      if (err) reject(err);

      resolve(token);
    })
  })
}

function getUserPasswordHash(username) {
  return new Promise((resolve, reject) => {
    connection.get('pass_' + username, function (err, result) {
      if (err) reject(err);

      resolve(result);
    });
  });
}

module.exports = {
  connection: redisClient,
  connectToRedis,
  getUserJWT,
  setUserJWT,
  getUserPasswordHash,
};