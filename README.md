# Cloudpbx PHP SDK

# Requerimientos

 - docker
 - docker-compose
 - gnu make
 - pre-commit

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
