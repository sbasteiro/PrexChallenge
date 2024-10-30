# Code Challange Prex

## Challange
El desafío es integrar una API existente a una una API REST propia que desarrollemos exponiendo un conjunto de servicios. Se deberán entregar distintos diagramas que representen la solución.

## Requisitos
  - Integrarse con la API de GHIPY
  - Desarrollar un API REST con autenticación OAuth2.0, usar los códigos HTTP según corresponda. La API debe contar con 4 servicios:

    | Login | Permite la autenticación para operar con la API |
    | -- | -- |
    | Entrada | email \| password |
    | Salida | token con expiración de 30 minutos |

    | ** Buscar GIFS | Buscar gifs por una frase o término |
    | -- | -- |
    | Entrada | Filtros:<br> `QUERY` : requerido\|cadena<br> `LIMIT` : opcional\|numérico<br> `OFFSET` opcional\|numérico |
    | Salida | Colección con resultados de búsqueda |

    | ** Buscar GIF por ID | Obtener información de un gift específico |
    | -- | -- |
    | Entrada | `ID` : requerido\|numérico |
    | Salida | Datos del recurso consultado |

    | ** Guardar GIF favorito | Almacenar para un usuario un gif favorito |
    | -- | -- |
    | Entrada | `GIF_ID `: requerido\|numérico<br> `ALIAS` : requerido\|cadena<br> `USER_ID` : requerido\|numérico |
    | Salida | mensaje de éxito |

  - Toda la interacción con los servicios deberá persistirse registrando la siguiente información:
    - Usuario que realizo la petición
    - Servicio consultado
    - Cuerpo de la petición
    - Código HTTP de la respuesta
    - Cuerpo de la respuesta
    - IP de origen de la consulta

  - Aplicar test unitarios o features

## Tecnologías a aplicar:
  - PHP v8.2 en adelante
  - Laravel Framework v10
  - MariaDB ó MySQL
  - UML
  - Docker

## Entrega
Una vez finalizado el desafío el mismo deberá subirse a un repositorio GIT (GitHub, Bitbucket) y contar con un archivo README.md que describa el despliegue del proyecto que incluya:

  - Diagrama de Casos de Uso
  - Diagrama de Secuencia que refleje todos los casos de uso
  - Diagrama de Datos o DER
  - Colección POSTMAN con la descripción de los servicios. Integrando además automatización para que el token resultante del login se almacene en el environment
  - Dockerfile para levantar el proyecto


<br>

## Solución

### Contenerización
Para la solución de este projecto se creó un entorno de desarrollo con docker-compose que incluye la definición de tres contenedores:
 - Un contenedor de aplicación para laravel
 - Un contenedor para la base de datos mysql
 - Un contenedor para el webserver con nginx

### Instruciones para levantar en ambiente local
  - Clonar este repositorio: `git clone git@github.com:sbasteiro/PrexChallenge.git`
  - Copiar el archivo .env.example a .env: `cp .env.example .env`  
  - Levantar docker compose: `docker-compose up`
  - Abrir nueva terminal e Instalar las dependencias de composer dentro del contenedor de aplicación:
    - `docker exec -it [NOMBRE DEL CONTENEDOR DE APP:prexlaravel-app-1] bash`
  - Corregir permisos (aún dentro del contenedor de aplicación):
    - `chown -R www-data:www-data storage bootstrap/cache`
    - `chmod -R 775 storage bootstrap/cache`
  - Con esto listo, podemos descargar el composer, dentro del contenedor de aplicación:
    - `composer install`  
  - Para ver los name de los contenedores, fuera de los mismos poner `docker ps`
  - Dentro del archivo `.env` modificar con el nombre del contenedor de base de datos la variable `DB_HOST=`   
  - Correr las migraciones para la base de datos, con seed que crea el usuario (aún dentro del contenedor de aplicación):
    - `php artisan migrate --seed`
  - Crear client personal, siguiendo las indicaciones al poner (aún dentro del contenedor de aplicación):
    - `php artisan passport:client --personal`  
  - Instalar passport para la auth2.0 (aún dentro del contenedor de aplicación):
    - `php artisan passport:install`
  - Ingresando a http://localhost:8081/ debería poder ver la portada de laravel


### Integración con la API de GIPHY
Será necesario crear un usuario y obtener una clave `Api Key` que se debera ingresar en el archivo de variables de entorno `.env`.<br>
Para eso puede ingrear a https://developers.giphy.com/dashboard/?create=true, seleccionar `Create an API Key` y luego se seguir las instrucciones hasta conseguir la misma.<br>
Para este ejemplo utilizamos una de prueba, pero tenga en cuenta que tiene un límite de 100 consumos por hora.

### Desarrollar un API REST con autenticación OAuth2.0
Utilizamos el paquete `passport` nativo de laravel v10, que nos permite utilizar el protocolo Auth2.0 como método de autenticación de usuario.<br>
El modo de autenticación es enviando el email y password, la API devolverá el `token` de accesso, el mismo se utilizará para todos los servicios.

### Servicios

#### POST /api/login
Este servicio recibe un email y un password, devuelve un token de acceso.
Cuerpo de mensaje
```
{
    "email": "prueba@example.com",
    "password": "prueba"
}
```

Respuesta de la API:
```
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZGVhZDIxMmNjNDNjYzg0MDI4MmExOTdlZGE0YWI0MzI4OTlhNWNkZWZmZDgzZjdjMWNlNGFiMDY3ZDc0MGRlOWNlNmQxNDhiMGFjYjhmM2IiLCJpYXQiOjE3MzAyNDM4ODguNDI1ODM0LCJuYmYiOjE3MzAyNDM4ODguNDI1ODM2LCJleHAiOjE3NjE3Nzk4ODguNDE5NzM0LCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.CEIGNp-k3ljAwxxpXFyp3CL6IS8PP3zw2YuCakCW0vFqPU7bDyqBaSYfmGZKkBzWe0tz1yPauLmm12DTUjFBLWOKaGRHqyfGjt_KcRha-UoeaR-khXwnDG8MIxEW2TatSJJvAPrRBxYYLuxxUfAAbGBNPr_u3YaSwqe25SPJ5b5QIPKlEgPTogsybUtvLDkUiMmQINI8v451LEB_LLNOSRKxAkGKbUxmEaaX7LHlL1LaZXk4xVlLudLGpXJ3UuuKAlG3pgY5cembIN4s85xd5agMWuozRHvdSkb2KC_Cgc0bWrRDOEMs-p_Ji_o-HfHAWS_Hpiyl8jic5TVn55a5XJQa4gsdM55mWIaqrxCtnNITQ_KUgAmC6kBaE-1MsVUdYULI0fIfJP7y9ewTc0wkIm-n31P2VXT-qw3NvRsFRxAal5Z3S3ti-OFQkRdncbE3Ze2rFze6hMm3KwqVmAE5luRarDF9SG2cfpgDTOfY5sozK3VVPSSMimg1EixHuBzHNOlH-EUUKnTN8VKO0wVSVSsxuIVwg_M07ZEHk0lrwQd6fGAsQDn_iKJ9Guh7bUCxpnLWoXAngwdPjdodo0A2-j9Ll23pC2d3nqTdXM4wyF4D2x12ihN5Ox--RL_jZGeCcWb8zIkeorjNvzuNIhcAJMmemQ-JqDv0MtZ5RRPBCqc"
}
```

#### GET /api/gifs/search?query=[QUERY]&limit=[25]&offset=[OFFSET]
Este servicio recibe tres parámetros: query, limit y offset. Consulta a la API de GIPHY y retorna todos los registros de gifs que coincidan con los filtros enviados.
<br> Se agregó la automatización para que el token resultante del login se almacene en el environment, así que no necesita autorización, pero si correr primero el de login.

Respuesta de la API:
```
{
    "data": {
        "type": "gif",
        "id": "0r9AnKXGbeWPHdqPiE",
        "url": "https://giphy.com/gifs/SWAG-0r9AnKXGbeWPHdqPiE",
        "slug": "SWAG-0r9AnKXGbeWPHdqPiE",
        "bitly_gif_url": "https://gph.is/g/ZP073w7",
        "bitly_url": "https://gph.is/g/ZP073w7",
        "embed_url": "https://giphy.com/embed/0r9AnKXGbeWPHdqPiE",
        "username": "SWAG",
        }
}        
        .... continúa"
```

#### GET /api/gifs/[GIF_ID]
Este servicio recibe un ID (puede encontrar un id disponible, en la respuesta del servicio anterior) y consulta la API de GIPHY, devuelve un Json con la información del gif pedido. <br> Colocar en Autorización-> Bearer Token, el generado para todos los servicios.
<br> Se agregó la automatización para que el token resultante del login se almacene en el environment, así que no necesita autorización, pero si correr primero el de login.

Respuesta de la API:
```
{
    "data": {
        "type": "gif",
        "id": "0r9AnKXGbeWPHdqPiE",
        "url": "https://giphy.com/gifs/SWAG-0r9AnKXGbeWPHdqPiE",
        "slug": "SWAG-0r9AnKXGbeWPHdqPiE",
        "bitly_gif_url": "https://gph.is/g/ZP073w7",
        "bitly_url": "https://gph.is/g/ZP073w7",
        "embed_url": "https://giphy.com/embed/0r9AnKXGbeWPHdqPiE",
        "username": "SWAG",
        }
}        
        .... continúa"
```

#### POST /api/gifs/favorites
Este servicio permite agrear gifs favoritos para el usuario logueado, con un alias para recordarlos.
<br> Se agregó la automatización para que el token resultante del login se almacene en el environment, así que no necesita autorización, pero si correr primero el de login.

Cuerpo del mensaje:
```
{
    "gif_id": "l3fQf1OEAq0iri9RC",
    "alias": "Mi GIF Favorito"
}

```
Respuesta de la API:
```
{
    "message": "GIF agregado a favoritos."
}
```

### Collecion de servicios en postman
Importe esta colección para probar las llamadas a los servicios desde postman: [postman_collection.json](postman_collection.json)

### Registrando de interacción de servicios
Para esto hemos creado la tabla `service_interactions` y un middleware `LogServiceInteraction.php` que registra la informacion solicitada para cada llamada de servicio.<br>
La estructura de la tabla esta definida en esta [migracion](./database/migrations/2024_10_29_184243_create_service_interactions_table.php)

### Test unitarios o features
Para esto hemos creado 7 [feature tests](tests/Feature/GifControllerTest.php), que evaluan los siguientes casos:
  - test_user_can_search_gifs
  - test_user_cannot_search_gifs_with_empty_query
  - test_user_can_get_gif_by_id
  - test_user_can_store_favorite_gif
  - test_user_cannot_get_gif_by_invalid_id
  - test_user_cannot_store_duplicate_favorite_gif

### Tecnologías aplicadas:
  - PHP v8.3
  - Laravel Framework v10
  - MySQL
  - UML
  - Docker / Docker Compose

### Diagramas

#### UML
![UML](./diagram/UML_login.png)
![UML](./diagram/UML_GetGifs.png)
![UML](./diagram/UML_GetGifByID.png)
![UML](./diagram/UML_Favourite.png)

#### Casos de uso
![CASOSDEUSO](./diagram/casosdeuso.png)

#### Secuencia
![SECUENCIA](./diagram/secuencia.png)

#### DER
![DER](./diagram/der.png)