VMTag=bangunsite:1.0
VMName=bangunsite

build-vm:
	docker build . --tag ${VMTag}
up-vm: build-vm
	if [ -z `docker network ls -qf name=cloudflared_bangunsoft` ]; then docker network create -d bridge cloudflared_bangunsoft; fi
	if [ ! -d ./data ]; then mkdir -p ./data/logs/nginx && mkdir ./data/logs/php82; fi
	@make precommit
	docker-compose --compatibility up -d
down-vm:
	make clear-cache
	docker-compose down --remove-orphans
clean-vm: down-vm
	docker rmi -f $(shell docker images -q ${VMTag})
	sudo rm -r ./data && sudo rm -r ./web/vendor
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

install-template:
	chmod +x ./infra/template/install.sh
	if [ ! -d ./template ]; then sh ./infra/template/install.sh; fi
run-template: install-template
	cd template && php -S localhost:9111

prune-images:
	docker builder prune
	docker image prune

precommit:
	cp pre-commit .git/hooks/
	chmod +x .git/hooks/pre-commit

force-composer:
	cp infra/platform_check.php web/vendor/composer/
