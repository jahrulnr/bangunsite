VMTag=bangunsite:1.0
VMName=bangunsite

build:
	docker build . --tag ${VMTag} -f Dockerfile --progress=plain --network=host
rebuild: build
	@make up
	docker compose logs -f bangunsite
up: 
	if [ -z `docker images -q services` ]; then make build; fi
	if [ -z `docker network ls -qf name=services` ]; then docker network create --driver=overlay --attachable services; fi
	if [ ! -d ./data ]; then \
		mkdir -p ./data/logs/nginx \
		&& mkdir ./data/logs/php \
		&& mkdir ./data/www; \
	fi
	@make precommit
	docker compose --compatibility up -d bangunsite
	docker compose logs -f bangunsite
logs:
	docker logs -f -n 100 bangunsite
down:
	make clear-cache
	docker compose down --remove-orphans
clean: down
	docker rmi -f $(shell docker images -q ${VMTag})
	sudo rm -r ./data && sudo rm -r ./web/vendor
clear-cache:
	docker exec -i ${VMName} artisan optimize:clear && \
	docker exec -i ${VMName} artisan view:clear && \
	docker exec -i ${VMName} artisan session:flush
bash:
	docker exec -it ${VMName} bash
migrate:
	docker exec -i ${VMName} php artisan migrate

# install-template:
# 	chmod +x ./config/template/install.sh
# 	if [ ! -d ./template ]; then sh ./config/template/install.sh; fi
# run-template: install-template
# 	cd template && php -S localhost:9111

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