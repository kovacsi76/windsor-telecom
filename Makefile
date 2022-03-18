container=php-fpm
folder=docker

build: rm up composer-install
rm:
	cd ${folder} && docker-compose stop
	cd ${folder} && docker-compose rm -v -f
	cd ${folder} && docker-compose build
stop:
	cd ${folder} && docker-compose stop
up:
	cd ${folder} && docker-compose up -d
bash:
	cd ${folder} && docker-compose exec ${container} /bin/bash
composer-install:
	cd ${folder} && docker-compose run --rm ${container} sh -lc 'composer install'
test:
	cd ${folder} && docker-compose run --rm ${container} sh -lc 'bin/phpunit'
