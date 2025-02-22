FROM ghcr.io/janyksteenbeek/web-docker:latest

COPY . /app

RUN chown -R www-data: /app
WORKDIR /app

EXPOSE 8888
CMD sh /startup.sh