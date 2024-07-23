# API de Ranking de Canciones

Bienvenido a la API de Ranking de Canciones. Esta API permite gestionar un ranking de canciones, incluyendo la capacidad de agregar, actualizar, eliminar y obtener información sobre canciones. La API está construida utilizando Slim Framework y sigue una arquitectura MVC (Modelo-Vista-Controlador).

## Tabla de Contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Rutas de la API](#rutas-de-la-api)
- [Mensajes de Error](#mensajes-de-error)

## Requisitos

- PHP 7.4 o superior
- Composer
- Extensión JSON de PHP

## Instalación

1. Clona el repositorio:

    ```bash
    git clone https://github.com/Juan-Gines/api-ranking.git
    ```

2. Navega al directorio del proyecto:

    ```bash
    cd api-ranking
    ```

3. Instala las dependencias:

    ```bash
    composer install
    ```

4. Luego para probarlo tienes 2 opciones:

  Utilizando el servidor de desarrollo de PHP

  ```bash
    composer dev
   ```

  O simplemente instalandolo en la carpeta de un servidor apache

## Rutas de la API

### Obtener el ranking de canciones

- **URL**: `/ranking`
- **Método**: GET
- **Descripción**: Obtiene el ranking de canciones.
- **Parámetros**:
  - `limit` (opcional): Número máximo de canciones a obtener. Por defecto es 500.
  
### Obtener el ranking de canciones por país

- **URL**: `/ranking/{country}`
- **Método**: GET
- **Descripción**: Obtiene el ranking de canciones para un país específico.
- **Parámetros**:
  - `limit` (opcional): Número máximo de canciones a obtener. Por defecto es 500.

### Agregar una nueva canción

- **URL**: `/song`
- **Método**: POST
- **Descripción**: Agrega una nueva canción al ranking.
- **Body - Json**:
  - `title`: Título de la canción (requerido).
  - `country`: País de origen de la canción (requerido).

### Obtener información de una canción

- **URL**: `/song/{id}`
- **Método**: GET
- **Descripción**: Obtiene información de una canción específica.

### Actualizar una canción

- **URL**: `/song/{id}`
- **Método**: PUT
- **Descripción**: Actualiza la información de una canción específica.
- **Body - Json**:
  - `title`: Nuevo título de la canción (requerido).
  - `country`: Nuevo país de origen de la canción (requerido).

### Subir el ranking de una canción

- **URL**: `/song/touch/{id}`
- **Método**: PATCH
- **Descripción**: Sube un puesto en el ranking, actualizando su score.

### Eliminar una canción

- **URL**: `/song/{id}`
- **Método**: DELETE
- **Descripción**: Elimina una canción del ranking.

## Mensajes de Error

Los mensajes de error se gestionan en el archivo `config/messages.php` para facilitar su mantenimiento y traducción.
