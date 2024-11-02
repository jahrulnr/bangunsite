#!/bin/bash

echo "--- Set working dir ---"
WDIR=`pwd`

echo "--- Setup /storage ---"
mkdir -p /storage/laravel/app/public > /storage/app.log
mkdir -p /storage/laravel/framework/cache/data >> /storage/app.log
mkdir -p /storage/laravel/framework/sessions >> /storage/app.log
mkdir -p /storage/laravel/framework/views >> /storage/app.log
mkdir -p /storage/laravel/logs >> /storage/app.log
mkdir /storage/www >> /storage/app.log
if [ ! -d /storage/webconfig ]; then 
    if [ -d /var/setup/webconfig ]; then
        cp -vr /var/setup/webconfig /storage/ >> /storage/app.log
    fi
fi
if [ ! -f /storage/fstab ]; then
    touch /storage/fstab >> /storage/app.log
fi
if [ ! -d /storage/nginx ]; then
    cp -vr /var/setup/nginx /storage/ >> /storage/app.log
fi
if [ ! -d /storage/php ]; then
    cp -vr /var/setup/php /storage/ >> /storage/app.log
    rm -vf /storage/php/fpm/php.ini /storage/php/8.2/cli/php.ini >> /storage/app.log
    ln -s /storage/php/php.ini /storage/php/8.2/fpm/php.ini >> /storage/app.log
    ln -s /storage/php/php.ini /storage/php/8.2/cli/php.ini >> /storage/app.log
fi
# rm -vr /var/setup >> /storage/app.log

echo "--- Symlink to /storage ---"
ln -s /storage/.env /app/ >> /storage/app.log
ln -s /storage/fstab /etc/ >> /storage/app.log
ln -s /storage/nginx /etc/ >> /storage/app.log
ln -s /storage/php /etc/ >> /storage/app.log
ln -s /storage/www / >> /storage/app.log
if [ ! -L /etc/letsencrypt ]; then
    ln -s /storage/webconfig/ssl /etc/letsencrypt >> /storage/app.log
fi
if [ ! -L /app/storage ]; then
    ln -s /storage/laravel /app/storage >> /storage/app.log
fi
chown -Rv apps:apps /storage/ >> /storage/app.log

echo "--- Optimizing nginx.conf ---"
procs=$(cat /proc/cpuinfo | grep processor | wc -l)
sed -i -e "s/worker_processes auto/worker_processes $procs/" /etc/nginx/nginx.conf >> /storage/app.log

if [ ! -f /www/default/index.html ]; then
    echo "--- Generate default /www ---"
    mkdir -p /www/default/
    cp -v /storage/webconfig/index.html /www/default/  >> /storage/app.log
    cp -v /storage/webconfig/healty.php /www/default/  >> /storage/app.log
    chown -vR apps:apps /www/ >> /storage/app.log
fi

if [ ! -d /app/vendor ]; then
    echo "--- Install Laravel Vendor ---"
    environment=`printenv ENV`
    if [ "$ENV" = "production" ]; then 
        composer install --no-cache --optimize-autoloader --no-dev >> /storage/app.log
    else
        composer install --no-cache --optimize-autoloader >> /storage/app.log
    fi
    chown -vR apps:apps /app/vendor/ >> /storage/app.log
fi

if [ ! -f /storage/.env ]; then 
    cp /app/.env.example /storage/.env >> /storage/app.log
    echo "--- Generate Laravel Key ---"
    /app/artisan key:generate >> /storage/app.log
fi

if [ ! -f /storage/db.sqlite ]; then
    echo "--- Install Database (SQLite) ---"
    touch /storage/db.sqlite
    cd /app && artisan migrate --force >> /storage/app.log
fi

if [ ! -f /storage/webconfig/ssl/live/default/cert.pem ]; then
    echo "--- Setup default SSL ---"
    cd /storage/webconfig/ssl/live/default/
    make build >> /storage/app.log
    chown -v apps:apps cert.pem key.pem >> /storage/app.log
    cd $WDIR
fi

echo "--- Generate Laravel Storage Link ---"
# I know this is useless
/app/artisan storage:link >> /storage/app.log

echo "--- Mounting FSTAB --"
/run/fstab_mounter.sh

echo "--- Start Server ---"
supervisord -n -c /etc/supervisord.conf