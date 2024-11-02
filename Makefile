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
	docker build . --tag ${VMTag} -f Dockerfile --progress=plain --network=host
rebuild-vm: build-vm
	@make up-vm
	docker-compose logs -f bangunsite
up-vm: 
	if [ -z `docker images -q bangunsite` ]; then make build-vm; fi
	if [ -z `docker network ls -qf name=cloudflared_bangunsoft` ]; then docker network create -d bridge cloudflared_bangunsoft; fi
	if [ ! -d ./data ]; then \
		mkdir -p ./data/logs/nginx \
		&& mkdir ./data/logs/php \
		&& mkdir ./data/www; \
	fi
	@make precommit
	docker-compose --compatibility up -d bangunsite mail-server
	docker-compose logs -f bangunsite
restart-vm:
	docker-compose down && docker-compose up -d bangunsite mail-server
logs-vm:
	docker logs -f -n 100 bangunsite
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
	docker cp ./config/db.sqlite ${VMName}:/storage/db.sqlite
migrate:
	docker exec -i ${VMName} php artisan migrate

install-template:
	chmod +x ./config/template/install.sh
	if [ ! -d ./template ]; then sh ./config/template/install.sh; fi
run-template: install-template
	cd template && php -S localhost:9111

prune-images:
	docker builder prune
	docker image prune

precommit:
	cp pre-commit .git/hooks/
	chmod +x .git/hooks/pre-commit

force-composer:
	cp config/platform_check.php web/vendor/composer/

bangunsite=`docker container inspect -f '{{.State.Running}}' ${VMName}`
test-image: 
	docker build . --tag ${VMName} --file Dockerfile
	docker run -d --name bangunsite-prod ${VMName}
	sleep 5
	docker exec -i bangunsite-prod curl localhost/healty.php -s --connect-timeout 10
	docker exec -i bangunsite-prod artisan key:generate > /dev/null && sleep 2
	docker exec -i bangunsite-prod curl localhost:8000/healty -sf --connect-timeout 10
	docker stop bangunsite-prod > /dev/null && docker rm bangunsite-prod > /dev/null
	docker rmi ${VMName} > /dev/null