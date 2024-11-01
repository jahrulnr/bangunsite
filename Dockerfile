FROM golang:1.22.5-alpine3.20 as gobuilder
WORKDIR /app
COPY infra/proxy /app
RUN go build -o main main.go

FROM jahrulnr/nginx-pagespeed:1.26.2 AS plugin
FROM debian:bookworm-slim AS base

ENV PS1="\[\e]0;\u@\h: \w\a\]${whoami}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ "

ENV PATH="/opt/venv/bin:/app:/app/vendor/bin:$PATH"
ENV TZ="Asia/Jakarta"
RUN echo "alias ll='ls -l'" >> /root/.bashrc \
    && apt-get update && apt-get install -y --no-install-recommends \
         curl wget ca-certificates gnupg2 \
    && curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb \
    && dpkg -i /tmp/debsuryorg-archive-keyring.deb \
    && rm -f /tmp/debsuryorg-archive-keyring.deb \
    && sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ bookworm main" > /etc/apt/sources.list.d/php.list' \
    && curl https://nginx.org/keys/nginx_signing.key | gpg --dearmor | tee /usr/share/keyrings/nginx-archive-keyring.gpg >/dev/null \
    && gpg --dry-run --quiet --no-keyring --import --import-options import-show /usr/share/keyrings/nginx-archive-keyring.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/nginx-archive-keyring.gpg] \
        http://nginx.org/packages/debian bookworm nginx" \
        | tee /etc/apt/sources.list.d/nginx.list \
    && apt-get update && apt-get install -y --no-install-recommends \
        php8.2-fpm \
        php8.2-cli \
        php8.2-opcache \
        php8.2-mysqli \
        php8.2-sqlite3 \
        php8.2-pgsql \
        php8.2-curl \
        php8.2-soap \
        php8.2-xml \
        php8.2-fileinfo \
        php8.2-phar \
        php8.2-intl \
        php8.2-dom \
        php8.2-xmlreader \
        php8.2-ctype \
        php8.2-iconv \
        php8.2-tokenizer \
        php8.2-zip \
        php8.2-simplexml \
        php8.2-mbstring \
        php8.2-gd \
        php8.2-pdo \
        php8.2-xmlwriter \
        php8.2-sockets \
        php8.2-bcmath \
        php8.2-intl \
        php8.2-xml \
        php8.2-amqp \
        php8.2-apcu \
        php8.2-redis \
        php8.2-uploadprogress \
        php8.2 \
        nginx=1.26.2-1~bookworm \
        tzdata \
        cron \
        zip unzip \
        vim \
        sysstat openssl make \
        --upgrade supervisor \
        certbot python3-certbot-nginx \
    && rm -rf /etc/nginx/conf.d/default.conf /etc/supervisor/supervisord.conf \
    && mkdir -p /run/php \
    && ln -s /usr/sbin/php-fpm8.2 /usr/sbin/php-fpm \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    # Clean up
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* && \
    # set user & group id
    usermod -ou 1000 nginx && \
    groupmod -og 1000 nginx && \
    groupadd -rog 0 apps && \
    useradd -rog 0 -u 0 apps && \
    mkdir -p /var/cache/ngx_pagespeed && \
    chown nginx:nginx /var/cache/ngx_pagespeed

COPY --from=gobuilder /app/main /usr/bin/server-proxy
COPY --from=plugin /usr/lib/nginx/modules/ngx_pagespeed.so /usr/lib/nginx/modules/ngx_pagespeed.so

COPY ./infra/nginx/nginx.conf /etc/nginx/
COPY ./infra/nginx/default.conf /etc/nginx/conf.d/
COPY ./infra/nginx/custom.d /etc/nginx/custom.d
COPY ./infra/php/php.ini /etc/php/8.2/fpm/
COPY ./infra/php/php.ini /etc/php/8.2/cli/
COPY ./infra/php/php-fpm.conf /etc/php/8.2/cli/
COPY ./infra/php/www.conf /etc/php/8.2/fpm/pool.d/
COPY ./infra/supervisord.conf /etc/supervisord.conf
COPY ./infra/start.sh /run/
RUN chmod +x /run/start.sh

COPY --chown=nginx:nginx ./web /app
COPY ./infra/db.sqlite /app/database/
COPY --chown=nginx:nginx ./infra/.env /app/.env
RUN chmod +x /app/artisan

WORKDIR /app
USER nginx
RUN composer install --no-cache --optimize-autoloader
RUN if [ ! -f /app/public/storage ] && [ ! -d /app/public/storage ]; then php artisan storage:link; fi
USER apps

EXPOSE 80
EXPOSE 443
EXPOSE 8000

CMD [ "/run/start.sh" ]
HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 CMD [ "curl", "--fail", "localhost:10001/ping" ]