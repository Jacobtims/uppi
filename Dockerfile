FROM ghcr.io/janyksteenbeek/web-docker:latest
RUN apk del nodejs npm
RUN curl -fsSL https://bun.sh/install | bash

RUN rm -rf /app/public
COPY . /app

RUN sh -c "wget https://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"

RUN cd /app && /usr/local/bin/composer install --no-dev
RUN cd /app && bun install && bun run build && rm -rf node_modules

RUN chown -R www-data: /app
WORKDIR /app

EXPOSE 8888
CMD sh /startup.sh