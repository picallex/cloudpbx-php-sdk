# Cloudpbx PHP SDK

# Requerimientos

 - php: `^7.3 || ^8.0` (toda la linea 8.x, incluido php 8.5)
 - ext-curl

El suite unitario se corre en php 8.2, 8.4 y 8.5 (`make test-matrix`). En php 7.3,
el piso soportado, solo se valida que `src/` siga siendo parseable (`make lint-php73`),
porque el toolchain de desarrollo (psalm 6) ya exige php >= 8.1.

# Requerimientos desarrollo

 - docker
 - docker-compose
 - gnu make
 - pre-commit

# Uso

ver **tests/integration/ClientCurlTest.php** ejemplos de uso.

~~~php
 // instanciar cliente
 $client = \Cloudpbx\Sdk::createDefaultClient($base, $api_key);

 // consultar customers ver `\Cloudpbx\Sdk\Customer` para mas detalles
 $customers = $client->customers->all();

 // consultar usuario/extension ver `\Cloudpbx\Sdk\User` para mas detalle
 $users = $client->users->all($customers[0]->id);

 // consultar relacion
 $customer_of_user = $client->preload($users[0]->customer);
~~~

# Contribuir

1. `make composer-init`
2. `make test`
3. `make test-matrix`

Los targets usan php 8.5 por defecto. Para trabajar sobre otra version:

~~~bash
$ make composer-init PHP_VERSION=8.2
$ make test-core PHP_VERSION=8.2
~~~

Los errores de psalm preexistentes estan capturados en **psalm-baseline.xml**;
se regenera con `make psalm-baseline`.

inicializar precommit

~~~bash
$ pre-commit install
~~~

### confirmar funcionamiento api externa

crear archivo **.env.test** con las variables **cloudpbx_api_base** y **cloudpbx_api_key**.
