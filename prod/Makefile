VMTag=bangunsite:latest
VMName=bangunsite

install:
	@make up-vm
	@make cp-db
	@make migrate

up-vm:
	if [ -z `docker network ls -qf name=cloudflared_bangunsoft` ]; then docker network create -d bridge cloudflared_bangunsoft; fi
	if [ ! -d ./data ]; then \
		mkdir -p ./data/logs/nginx \
		&& mkdir ./data/logs/php82 \
		&& mkdir ./data/grafana/lib \
		&& mkdir ./data/grafana/provisioning; \
	fi
	@make precommit
	docker-compose --compatibility up -d bangunsite
restart-vm: down
	docker-compose up -d bangunsite
logs-vm:
	docker-compose logs -f -n 100
down-vm:
	docker-compose down --remove-orphans
clear-cache:
	docker exec -i ${VMName} artisan optimize:clear && \
	docker exec -i ${VMName} artisan view:clear && \
	docker exec -i ${VMName} artisan session:flush
bash-vm:
	docker exec -it ${VMName} bash
sh-vm:
	docker exec -it ${VMName} sh
cp-db:
	docker cp ./infra/db.sqlite ${VMName}:/app/database/db.sqlite
migrate:
	docker exec -i ${VMName} php artisan migrate

force-composer:
	cp infra/platform_check.php web/vendor/composer/

setup-prod:
	docker load < bangunsite.tar.gz
	