FROM php:7.4-cli

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install sockets pdo pdo_pgsql

COPY . /usr/src/app

WORKDIR /usr/src/app

CMD [ "php", "./app.php" ]
