FROM golang:1.22.5-alpine3.20 as gobuilder
WORKDIR /app
COPY infra/proxy /app
RUN go build -o main main.go

FROM alpine:3.20

ENV PS1="\[\e]0;\u@\h: \w\a\]${whoami}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ "

ENV PATH="/opt/venv/bin:/app:/app/vendor/bin:$PATH"
ENV TZ="Asia/Jakarta"
COPY infra/apk.sh /tmp/apk.sh
RUN \
    chmod +x /tmp/apk.sh && sh /tmp/apk.sh && rm -rf /tmp/apk.sh && \
    curl -o /tmp/composer-setup.php https://getcomposer.org/installer && \
    php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer

COPY --from=gobuilder /app/main /usr/bin/server-proxy

COPY ./infra/cron.txt /tmp/
RUN cat /tmp/cron.txt >> /etc/crontabs/root && rm /tmp/cron.txt

COPY ./infra/nginx/nginx.conf /etc/nginx/
COPY ./infra/nginx/default.conf /etc/nginx/http.d/
COPY ./infra/nginx/stream.d /etc/nginx/stream.d
COPY ./infra/nginx/custom.d /etc/nginx/custom.d
COPY ./infra/php/php.ini /etc/php82/
COPY ./infra/php/php-fpm.conf /etc/php82/
COPY ./infra/php/www.conf /etc/php82/php-fpm.d/
COPY ./infra/supervisord.conf /etc/supervisord.conf
COPY ./infra/start.sh /run/
RUN chmod +x /run/start.sh

COPY --chown=nginx:nginx ./web /app
COPY ./infra/db.sqlite /app/database/
COPY --chown=nginx:nginx ./infra/.env /app/.env
RUN chmod +x /app/artisan

WORKDIR /app
USER nginx
RUN composer update --no-cache --optimize-autoloader
RUN if [ ! -f /app/public/storage ] && [ ! -d /app/public/storage ]; then php artisan storage:link; fi
USER apps

EXPOSE 80
EXPOSE 8000

CMD [ "/run/start.sh" ]
HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 CMD [ "curl", "--fail", "localhost:10001/ping" ]