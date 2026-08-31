.PHONY: composer-init composer-autoload fix lint psalm psalm-baseline \
	test test-core test-integration test-matrix lint-php73 commit

# Versiones de php sobre las que se valida el sdk.
PHP_VERSIONS ?= 8.2 8.4 8.5

# Version usada por los targets individuales (composer-init, test, psalm, ...).
PHP_VERSION ?= 8.5

PROJECT = sdk-php$(subst .,,$(PHP_VERSION))
COMPOSE = PHP_VERSION=$(PHP_VERSION) docker-compose -p $(PROJECT)

composer-init:
	$(COMPOSE) run --rm app composer update

composer-autoload:
	$(COMPOSE) run -T --rm app composer dump-autoload

fix:
	$(COMPOSE) run -T --rm app composer run-script --dev fix src

lint:
	$(COMPOSE) run -T --rm app composer run-script --dev lint src

psalm:
	$(COMPOSE) run -T --rm app composer run-script --dev -- psalm

psalm-init:
	$(COMPOSE) run --rm app composer run-script --dev -- psalm --init

# Regenera el baseline con los errores preexistentes que psalm todavia reporta.
psalm-baseline:
	$(COMPOSE) run -T --rm app composer run-script --dev -- psalm --set-baseline=psalm-baseline.xml

test: test-core test-integration

test-core: composer-autoload
	$(COMPOSE) run -T $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite Cloudpbx

test-integration: composer-autoload
	$(COMPOSE) run -T $(DOCKER_ARGS) --rm app composer run-script --dev -- test --testsuite integration

# Corre el suite unitario en cada version de php soportada.
test-matrix:
	@for v in $(PHP_VERSIONS); do \
		echo "==> php $$v"; \
		$(MAKE) test-core PHP_VERSION=$$v || exit 1; \
	done

# php 7.3 es el piso soportado en composer.json pero ya no puede instalar el
# toolchain de desarrollo (psalm 6 exige php >= 8.1), asi que solo validamos
# que el codigo de src/ siga siendo parseable en esa version.
lint-php73:
	docker run --rm -v "$(CURDIR)":/app -w /app php:7.3-cli \
		sh -c 'set -e; for f in $$(find src -name "*.php"); do php -l "$$f" > /dev/null; done; echo "src ok en php 7.3"'

commit:
	$(COMPOSE) run -T --rm app composer run-script --dev -- commit
