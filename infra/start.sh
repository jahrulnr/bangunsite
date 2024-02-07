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

if [ ! -d /app/vendor ]; then
    composer update --no-cache --optimize-autoloader
    chown -R nginx: /app/vendor
fi

supervisord -n -c /etc/supervisord.conf