.PHONE: composer-init fix lint test test-integration test-core php-stant composer-autoload

DOCKER_IMAGE=composer:2

composer-init:
	docker-compose run --rm app composer update

fix:
	docker-compose run --rm app composer run-script --dev fix src

lint:
	docker-compose run --rm app composer run-script --dev lint src

test: test-php8.0 test-integration test-core

test-integration: composer-autoload
	docker-compose run $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite integration

test-core: composer-autoload
	docker-compose run $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite Cloudpbx

test-php8.0: test-integration-php8.0 test-core-php8.0

test-integration-php8.0: composer-autoload-php8.0
	docker-compose -p sdk-php8.0 -f docker-compose.php8.0.yml run $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite integration

test-core-php8.0: composer-autoload-php8.0
	docker-compose -p sdk-php8.0 -f docker-compose.php8.0.yml run $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite Cloudpbx

composer-autoload-php8.0:
	docker-compose -p sdk-php8.0 -f docker-compose.php8.0.yml run --rm app composer dump-autoload

composer-autoload:
	docker-compose run --rm app composer dump-autoload

psalm-init:
	docker-compose run --rm app composer run-script --dev -- psalm --init

psalm:
	docker-compose run --rm app composer run-script --dev -- psalm

commit:
	docker-compose run --rm app composer run-script --dev -- commit
