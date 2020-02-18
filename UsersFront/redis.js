const redis = require('redis');
let redisClient = undefined;

function connectToRedis() {
    return new Promise((resolve, reject) => {
        const redisClient = redis.createClient({
            host: 'redis'
        });

        redisClient.on('connect', () => {
            console.log('Connected to redis server');
            redisClient = redisClient;
            resolve(redisClient);
        });
    });
};

module.exports = {
    connection: redisClient,
    connectToRedis
};