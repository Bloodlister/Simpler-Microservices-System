FROM nginx:1.17

WORKDIR /usr/src/app

COPY ./nignx.conf /etc/nginx/conf.d/default.conf
COPY ./dist /usr/src/app/dist

EXPOSE 80
