const redis = require('redis');
let redisClient = undefined;

function connectToRedis() {
  return new Promise((resolve, reject) => {
    redisClient = redis.createClient({
      host: 'redis'
    });

    redisClient.on('connect', () => {
      console.log('Connected to redis server');
      resolve(redisClient);
    });
  });
};

function tryUntilConnected(interval) {
  return new Promise(resolve => {
    connectToRedis()
        .then(res => resolve(res))
        .catch(err => {
          console.log(err);
          setTimeout(() => {
            tryUntilConnected(interval);
          }, interval)
        });
  });
}

function getUser(username) {
  return new Promise((resolve, reject) => {
    redisClient.get(username, (err, result) => {
      if (err) reject(err);

      resolve(result);
    });
  });
}

function getUserJWT(username) {
  return new Promise((resolve, reject) => {
    redisClient.get('jwt_' + username, (err, result) => {
      if (err) reject(err);

      resolve(result);
    });
  });
}

function setUserJWT(username, token) {
  return new Promise((resolve, reject) => {
    redisClient.set('jwt_' + username, token, (err) => {
      if (err) reject(err);

      resolve(token);
    })
  })
}

module.exports = {
  connection: redisClient,
  connectToRedis,
  tryUntilConnected,
  getUserJWT,
  setUserJWT,
};
