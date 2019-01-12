FROM php:7.3.1-cli-alpine

RUN apk update && apk upgrade && apk add bash

# mbstring, xml, pdo_sqlite, curl already included in base image
RUN docker-php-ext-install sockets bcmath pdo_mysql

WORKDIR /app

CMD ["/app/chatbot.sh"]
