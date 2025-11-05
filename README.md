# Sistema de Gestión de Biblioteca - API REST

API REST desarrollada con Symfony 5.4 para gestionar libros, autores y categorías de una biblioteca.

## 🚀 Características

- CRUD completo para Autores, Libros y Categorías
- Relaciones entre entidades (Autor → Libros, Categoría → Libros)
- Validación de datos
- Respuestas JSON
- Base de datos MySQL con Doctrine ORM

## 📋 Requisitos

- PHP >= 7.2.5
- MySQL 8.0
- Composer
- Symfony CLI

## 🔧 Instalación

1. Clonar el repositorio
2. Instalar dependencias:
```bash
composer install
```

3. Configurar base de datos en `.env`:
```
DATABASE_URL="mysql://root:manuel123@127.0.0.1:3306/library_db?serverVersion=8.0&charset=utf8mb4"
```

4. Crear base de datos y ejecutar migraciones:
```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

5. Iniciar servidor:
```bash
symfony server:start
```

## 📚 Entidades

### Author (Autor)
- `id` (PK)
- `name` (string)
- `nationality` (string)

### Category (Categoría)
- `id` (PK)
- `name` (string)

### Book (Libro)
- `id` (PK)
- `title` (string)
- `publicationYear` (integer)
- `author_id` (FK → Author)
- `category_id` (FK → Category)

## 🌐 Endpoints de la API

### Autores

#### Listar todos los autores
```http
GET /api/authors
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "name": "Gabriel García Márquez",
    "nationality": "Colombiano"
  }
]
```

#### Obtener un autor por ID
```http
GET /api/authors/{id}
```

#### Crear un autor
```http
POST /api/authors
Content-Type: application/json

{
  "name": "Gabriel García Márquez",
  "nationality": "Colombiano"
}
```

#### Actualizar un autor
```http
PUT /api/authors/{id}
Content-Type: application/json

{
  "name": "Gabriel García Márquez",
  "nationality": "Colombiano"
}
```

#### Eliminar un autor
```http
DELETE /api/authors/{id}
```

---

### Categorías

#### Listar todas las categorías
```http
GET /api/categories
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "name": "Ficción"
  }
]
```

#### Obtener una categoría por ID
```http
GET /api/categories/{id}
```

#### Crear una categoría
```http
POST /api/categories
Content-Type: application/json

{
  "name": "Ficción"
}
```

#### Actualizar una categoría
```http
PUT /api/categories/{id}
Content-Type: application/json

{
  "name": "Ciencia Ficción"
}
```

#### Eliminar una categoría
```http
DELETE /api/categories/{id}
```

---

### Libros

#### Listar todos los libros (con autor y categoría)
```http
GET /api/books
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "title": "Cien años de soledad",
    "publicationYear": 1967,
    "author": {
      "id": 1,
      "name": "Gabriel García Márquez",
      "nationality": "Colombiano"
    },
    "category": {
      "id": 1,
      "name": "Ficción"
    }
  }
]
```

#### Obtener un libro por ID (con autor y categoría)
```http
GET /api/books/{id}
```

#### Crear un libro
```http
POST /api/books
Content-Type: application/json

{
  "title": "Cien años de soledad",
  "publicationYear": 1967,
  "authorId": 1,
  "categoryId": 1
}
```

#### Actualizar un libro
```http
PUT /api/books/{id}
Content-Type: application/json

{
  "title": "Cien años de soledad",
  "publicationYear": 1967,
  "authorId": 1,
  "categoryId": 1
}
```

#### Eliminar un libro
```http
DELETE /api/books/{id}
```

## ✅ Validaciones

Todos los campos son requeridos:
- **Author**: `name`, `nationality`
- **Category**: `name`
- **Book**: `title`, `publicationYear`, `authorId`, `categoryId`

Si falta algún campo requerido, la API responderá con un error 400 y los mensajes de validación correspondientes.

## 🧪 Ejemplos de uso con cURL

### Crear un autor
```bash
curl -X POST http://127.0.0.1:8001/api/authors \
  -H "Content-Type: application/json" \
  -d '{"name":"Gabriel García Márquez","nationality":"Colombiano"}'
```

### Crear una categoría
```bash
curl -X POST http://127.0.0.1:8001/api/categories \
  -H "Content-Type: application/json" \
  -d '{"name":"Ficción"}'
```

### Crear un libro
```bash
curl -X POST http://127.0.0.1:8001/api/books \
  -H "Content-Type: application/json" \
  -d '{"title":"Cien años de soledad","publicationYear":1967,"authorId":1,"categoryId":1}'
```

### Listar todos los libros
```bash
curl http://127.0.0.1:8001/api/books
```

## 📁 Estructura del proyecto

```
library-test-api/
├── config/
│   ├── packages/
│   └── routes/
├── migrations/
├── src/
│   ├── Controller/
│   │   ├── AuthorController.php
│   │   ├── BookController.php
│   │   └── CategoryController.php
│   ├── Entity/
│   │   ├── Author.php
│   │   ├── Book.php
│   │   └── Category.php
│   └── Repository/
│       ├── AuthorRepository.php
│       ├── BookRepository.php
│       └── CategoryRepository.php
├── var/
├── vendor/
├── .env
├── composer.json
└── README.md
```

## 🛠️ Tecnologías utilizadas

- **Symfony 5.4** - Framework PHP
- **Doctrine ORM** - Mapeo objeto-relacional
- **MySQL 8.0** - Base de datos
- **Symfony Validator** - Validación de datos
- **Symfony Serializer** - Serialización JSON

## 📝 Notas

- El servidor de desarrollo está configurado en `http://127.0.0.1:8001`
- Todas las respuestas son en formato JSON
- Los endpoints siguen las convenciones REST
- Las relaciones se cargan automáticamente al obtener libros

## 🔒 Seguridad

⚠️ **Importante**: Este proyecto está configurado para desarrollo. Para producción:
- Cambiar `APP_ENV=prod` en `.env`
- Usar contraseñas seguras en la base de datos
- Configurar CORS si es necesario
- Implementar autenticación/autorización
