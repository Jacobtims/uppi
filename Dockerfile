FROM ghcr.io/janyksteenbeek/web-docker:latest
RUN apk del nodejs npm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.2/install.sh | PROFILE="${BASH_ENV}" bash
RUN echo node > .nvmrc
RUN nvm install

RUN rm -rf /app/public
COPY . /app

RUN sh -c "wget https://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"

RUN cd /app && /usr/local/bin/composer install --no-dev
RUN cd /app && npm install && npm run build && rm -rf node_modules

RUN chown -R www-data: /app
WORKDIR /app

EXPOSE 8888
CMD sh /startup.sh