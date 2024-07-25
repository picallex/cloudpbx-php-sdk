.PHONE: composer-init fix lint test test-integration test-core php-stant composer-autoload

DOCKER_IMAGE=composer:2

composer-init:
	docker-compose run --rm app composer update

fix:
	docker-compose run -T --rm app composer run-script --dev fix src

lint:
	docker-compose run -T --rm app composer run-script --dev lint src

test: test-php8.2 test-integration test-core

test-integration: composer-autoload
	docker-compose run -T $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite integration

test-core: composer-autoload
	docker-compose run -T $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite Cloudpbx

test-php8.2: test-integration-php8.2 test-core-php8.2

test-integration-php8.2: composer-autoload-php8.2
	docker-compose -p sdk-php8 -f docker-compose.php8.2.yml run -T $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite integration

test-core-php8.2: composer-autoload-php8.2
	docker-compose -p sdk-php8 -f docker-compose.php8.2.yml run -T $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite Cloudpbx

composer-autoload-php8.2:
	docker-compose -p sdk-php8 -f docker-compose.php8.2.yml run -T --rm app composer dump-autoload

composer-autoload:
	docker-compose run -T --rm app composer dump-autoload

psalm-init:
	docker-compose run --rm app composer run-script --dev -- psalm --init

psalm:
	docker-compose run -T --rm app composer run-script --dev -- psalm

commit:
	docker-compose run -T --rm app composer run-script --dev -- commit
