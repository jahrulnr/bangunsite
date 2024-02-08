FROM alpine:3.19

ENV PS1="\[\e]0;\u@\h: \w\a\]${whoami}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ "
RUN apk update && apk add --no-cache curl bash bash-completion shadow \
    && apk add --no-cache nginx \
    && apk add --no-cache git python3 vnstat \
    && python3 -m venv /opt/venv \
    && export PATH="/opt/venv/bin:$PATH" \
    && mkdir -p /run/php \
    && pip install wheel \
    && pip install supervisor \
    && pip install git+https://github.com/coderanger/supervisor-stdout \
    && apk add --no-cache php82 php82-fpm php82-cli php82-phar php82-iconv php82-mbstring \ 
        php82-gd php82-xml php82-zip php82-curl php82-opcache \
        php82-fileinfo php82-session php82-dom php82-tokenizer php82-exif \
        php82-xmlreader php82-simplexml php82-xmlwriter \
        php82-sqlite3 php82-pdo_sqlite php82-openssl php82-redis \
    \
    && groupmod -og 1000 nginx \ 
    && usermod -ou 1000 -g 1000 nginx \
    \
    && apk add certbot --no-cache \
    \
    && apk del shadow git \
    && curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/* /var/cache/apk/* ~/.cache

COPY ./infra/cron.txt /tmp/
RUN cat /tmp/cron.txt >> /etc/crontabs/root && rm /tmp/cron.txt

ENV PATH="/opt/venv/bin:/app:/app/vendor/bin:$PATH"
COPY ./infra/nginx/nginx.conf /etc/nginx/
COPY ./infra/nginx/default.conf /etc/nginx/http.d/
COPY ./infra/php/php.ini /etc/php82/
COPY ./infra/php/php-fpm.conf /etc/php82/
COPY ./infra/php/www.conf /etc/php82/php-fpm.d/
COPY ./infra/supervisord.conf /etc/supervisord.conf
COPY ./infra/start.sh /run/
RUN chmod +x /run/start.sh

ADD --chown=nginx:nginx ./web /app
ADD ./infra/db.sqlite /app/database/
COPY --chown=nginx:nginx ./infra/.env /app/.env
COPY --chown=nginx:nginx ./infra/nginx/index.html /www/default/
COPY --chown=nginx:nginx ./infra/php/healty.php /www/default/
RUN chmod +x /app/artisan

WORKDIR /app
USER nginx
RUN composer update --no-cache --optimize-autoloader
RUN if [ ! -f /app/public/storage ] && [ ! -d /app/public/storage ]; then php artisan storage:link; fi
USER root

EXPOSE 80
EXPOSE 8000

CMD [ "/run/start.sh" ]
HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 CMD [ "curl", "--fail", "localhost/healt" ]