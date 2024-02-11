#!/bin/bash

procs=$(cat /proc/cpuinfo | grep processor | wc -l)
sed -i -e "s/worker_processes auto/worker_processes $procs/" /etc/nginx/nginx.conf

# docker volume fix
if [ ! -d /var/log/nginx/ ]; then
    mkdir -p /var/log/nginx
    chown -R nginx: /var/log/nginx
fi
if [ ! -d /var/log/php/ ]; then
    mkdir -p /var/log/php82
    touch /var/log/php82/error.log
    chown -R nginx: /var/log/php82
fi

if [ ! -f /www/default/index.html ]; then
    mkdir -p /www/default/
    cp /app/storage/webconfig/index.html /www/default/ 
    cp /app/storage/webconfig/healty.php /www/default/ 
    chown -R nginx:nginx /www/default/
fi

if [ ! -d /app/vendor ]; then
    composer update --no-cache --optimize-autoloader
    chown -R nginx: /app/vendor
fi

if [ ! -f /app/database/db.sqlite ]; then
    touch /app/database/db.sqlite
    cd /app && artisan migrate --force
fi

supervisord -n -c /etc/supervisord.conf