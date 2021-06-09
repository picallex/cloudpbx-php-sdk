# Cloudpbx PHP SDK

# Requerimientos

 - php 7.3

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
~~~

# Contribuir

inicializar composer

~~~bash
$ make composer-init
~~~

inicializar precommit

~~~bash
$ pre-commit install
~~~

### confirmar funcionamiento api externa

usamos **php-vcr** para realizar las pruebas de la api externa,
para confirmar si ahi cambios de la api o esta aun es vigente, proceder:

  * eliminar los archivos de **tests/cassettes**
  * crear archivo **.env.test** con las variables **cloudpbx_api_base** y **cloudpbx_api_key**.
  * realizar prueba con **make test-integration**
