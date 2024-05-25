echo "apps:x:0:0:root:/root:/bin/bash" >> /etc/passwd
echo "alias ll='ls -l'" >> /root/.bashrc
apk update 
apk add --no-cache curl bash bash-completion shadow tzdata
apk add --no-cache docker
apk add --no-cache nginx nginx-mod-stream
apk add --no-cache git python3
python3 -m venv /opt/venv
mkdir -p /run/php
pip install wheel
pip install supervisor
pip install git+https://github.com/coderanger/supervisor-stdout
apk add --no-cache php82 php82-fpm php82-cli php82-phar php82-iconv php82-mbstring \
    php82-gd php82-xml php82-zip php82-curl php82-opcache \
    php82-fileinfo php82-session php82-dom php82-tokenizer php82-exif \
    php82-xmlreader php82-simplexml php82-xmlwriter \
    php82-sqlite3 php82-pdo_sqlite php82-openssl php82-redis php82-mysqli php82-pdo_mysql
ln -s /usr/sbin/php-fpm82 /usr/sbin/php-fpm

apk add certbot certbot-nginx --no-cache
groupmod -og 1000 nginx && \
usermod -ou 1000 -g 1000 nginx && \
apk del shadow git
rm -rf /tmp/* /var/cache/apk/* ~/.cache