FROM ghcr.io/janyksteenbeek/web-docker:latest

RUN rm -rf /app/public
COPY . /app

RUN sh -c "wget https://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"

RUN cd /app && /usr/local/bin/composer install --no-dev

RUN chown -R www-data: /app
WORKDIR /app

EXPOSE 8888
CMD sh /startup.sh