FROM golang:1.24.9-alpine3.22 AS gobuilder
# build proxy for ssl
WORKDIR /proxy
COPY ./proxy /proxy
RUN go mod tidy && go build -o main main.go

FROM nginx:1.29-bookworm

# Set default shell to bash
SHELL ["/bin/bash", "-c"]

ENV PS1="\[\e]0;\u@\h: \w\a\]${whoami}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ "

ENV PATH="/opt/venv/bin:/app:/app/vendor/bin:$PATH"
ENV TZ="Asia/Jakarta"

# Install necessary packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl wget ca-certificates gnupg2 \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ bookworm main" | tee /etc/apt/sources.list.d/php.list \
    && apt-get update && apt-get install -y --no-install-recommends \
        curl \
        tzdata \
        sysstat openssl make \
        zip unzip \
        vim \
        docker.io \
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
        s3fs \
        rsync \
        supervisor \
    && ln -s /bin/ss /bin/netstat \
    && mkdir -p /run/php \
    && ln -s /usr/sbin/php-fpm8.2 /usr/sbin/php-fpm \
    && apt-get install -y certbot python3-certbot-nginx \
    && groupadd -g 1000 apps \
    && useradd -u 1000 -g 1000 apps \
    && apt-get remove --purge -y git software-properties-common gnupg2 \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /etc/nginx/conf.d /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/composer-setup.php

# setup /storage
RUN mkdir /storage \
    && mkdir -p /var/setup \
    && mv /etc/nginx /var/setup/ \
    && mv /etc/php /var/setup/ \
    && rm -r /etc/letsencrypt \
    && rm /etc/fstab 

# Copy the binary from the builder stage
COPY --from=gobuilder /proxy/main /usr/bin/server-proxy

COPY ./config/nginx /var/setup/nginx
COPY ./config/php /var/setup/php
COPY ./config/webconfig /var/setup/webconfig
COPY ./config/supervisord.conf /etc/
COPY ./config/fstab_mounter.sh /run/
COPY ./config/start.sh /run/

COPY --chown=apps:apps ./web /app
RUN chmod +x /app/artisan /run/start.sh /run/fstab_mounter.sh

EXPOSE 80
EXPOSE 443
EXPOSE 8080

WORKDIR /app
CMD [ "/run/start.sh" ]
HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 CMD [ "curl", "--fail", "localhost:80" ]