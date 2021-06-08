.PHONE: composer-init

DOCKER_IMAGE=composer:2

composer-init:
	docker-compose run --rm app composer install

fix:
	docker-compose run --rm app composer run-script --dev fix src

lint:
	docker-compose run --rm app composer run-script --dev lint src

test:
	docker-compose run --rm app composer dump-autoload
	docker-compose run --rm app composer run-script --dev test

phpstan:
	docker-compose run --rm app composer run-script --dev phpstan
