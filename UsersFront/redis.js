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
    redisClient.get(username, (err, result) => {
      if (err) reject(err);

      resolve(result);
    });
  });
}

function setUserJWT(username, token) {
  return new Promise((resolve, reject) => {
    redisClient.set(username, token, (err) => {
      if (err) reject(err);

      resolve(token);
    })
  })
}

module.exports = {
  JWT_SECRET,
  connection: redisClient,
  connectToRedis,
  getUserJWT,
  setUserJWT,
  getUserPasswordHash,
};
