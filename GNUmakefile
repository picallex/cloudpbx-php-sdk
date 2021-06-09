.PHONE: composer-init fix lint test test-integration test-core php-stant composer-autoload

DOCKER_IMAGE=composer:2

composer-init:
	docker-compose run --rm app composer install

fix:
	docker-compose run --rm app composer run-script --dev fix src

lint:
	docker-compose run --rm app composer run-script --dev lint src

test: test-integration test-core

test-integration: composer-autoload
	docker-compose run $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite integration

test-core: composer-autoload
	docker-compose run $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite Cloudpbx

composer-autoload:
	docker-compose run --rm app composer dump-autoload

phpstan:
	docker-compose run --rm app composer run-script --dev phpstan
