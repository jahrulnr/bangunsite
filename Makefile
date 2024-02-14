VMTag=bangunsite:1.0
VMName=bangunsite

install: build-vm
### makesure to make down-vm before run this command
###	sudo rm -r data template web/vendor web/.env web/tmp
	@make up-vm
	@make cp-db
	@docker exec -i bangunsite artisan key:generate
	@make migrate

build-vm:
	docker build . --tag ${VMTag} -f Dockerfile --progress=plain
rebuild-vm: build-vm
	@make up-vm
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
restart-vm:
	docker-compose down && docker-compose up -d bangunsite
logs-vm:
	docker-compose logs -f -n 100
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

bangunsite=`docker container inspect -f '{{.State.Running}}' ${VMName}`
test-image: 
	if [ "${bangunsite}" = "true" ]; then make down-vm; fi
	docker build . --tag ${VMTag} --file Dockerfile-prod
	docker run -d --name bangunsite ${VMTag}
	sleep 5
	docker exec -i bangunsite curl localhost/healty.php -s --connect-timeout 10
	docker exec -i bangunsite artisan key:generate > /dev/null && sleep 2
	docker exec -i bangunsite curl localhost:8000/healty -sf --connect-timeout 10
	docker stop bangunsite > /dev/null && docker rm bangunsite > /dev/null

setup-prod:
	cp -r infra prod/
	chmod +x web/artisan
	sed -i "s#APP_KEY=.*#APP_KEY=${shell web/artisan app:generate-key}#g" ./prod/infra/.env-prod
	docker build . --tag ${VMName}:latest --file Dockerfile-prod
	docker save bangunsite:latest | gzip > prod/bangunsite.tar.gz
	zip production.zip prod/*
	