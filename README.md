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

 // consultar relacion
 $customer_of_user = $client->preload($users[0]->customer);
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

crear archivo **.env.test** con las variables **cloudpbx_api_base** y **cloudpbx_api_key**.
