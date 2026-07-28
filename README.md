# AventuraLocal API

<p align="center">
<a href="https://github.com/fer-gc05/AventuraLocalApi"><img src="https://img.shields.io/badge/GitHub-AventuraLocalApi-blue" alt="GitHub Repository"></a>
<a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-v12.x-red" alt="Laravel Version"></a>
</p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
<img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
<img src="https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis&logoColor=white" alt="Redis">
<img src="https://img.shields.io/badge/JWT-000000?style=for-the-badge&logo=JSON%20web%20tokens&logoColor=white" alt="JWT">
<img src="https://img.shields.io/badge/Scramble-000000?style=for-the-badge&logo=scramble&logoColor=white" alt="Scramble">
</p>

## Acerca de AventuraLocal API

AventuraLocal API es una plataforma backend desarrollada con Laravel que proporciona servicios para una aplicación de turismo y aventura local. La API permite gestionar destinos, eventos, rutas, reservas y una comunidad de usuarios interesados en el turismo local.

## Características Principales

- Marketplace de tours guiados por locales independientes
- Perfiles de guía con verificación, especialidades e idiomas
- Gestión de tours con punto de encuentro, precio por persona, cupos e itinerario
- Calendario de disponibilidad por tour (`TourSchedule`) con control de cupos
- Sistema de reservas vinculado a un horario específico
- Sistema de reseñas y calificaciones (polimórfico)
- Gestión de destinos turísticos, rutas, eventos y comunidades
- Gestión de medios (imágenes y videos)
- Sistema de mensajería
- Etiquetado de contenido
- Autenticación JWT
- Documentación con Scramble
- Sistema de caché con Redis
- Gestión de permisos y roles (`admin`, `guide`, `traveler`)

## Requisitos del Sistema

- PHP >= 8.2
- Composer
- MySQL >= 5.7
- Redis >= 6.0
- Node.js & NPM

## Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/fer-gc05/AventuraLocalApi.git
```

2. Instalar dependencias de PHP:
```bash
composer install
```

3. Instalar dependencias de Node.js:
```bash
npm install
```

4. Configurar el archivo .env:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurar la base de datos en el archivo .env:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aventura_local
DB_USERNAME=root
DB_PASSWORD=
```

6. Configurar JWT:
```bash
php artisan jwt:secret
```

7. Ejecutar las migraciones:
```bash
php artisan migrate
```

8. Iniciar el servidor:
```bash
php artisan serve
```

## Modelo de Dominio

El backend está orientado a un marketplace de guías turísticos independientes:

- **User**: Cuenta de usuario genérica. Puede tener rol `admin`, `guide` o `traveler`.
- **GuideProfile**: Perfil profesional del guía, separado de la cuenta de usuario. Incluye biografía, idiomas, especialidades, estado de verificación y balances.
- **Tour**: Experiencia turística creada por un guía. Contiene título, descripción, precio por persona, punto de encuentro geolocalizado, cupos mínimos/máximos, idiomas, qué incluye/no incluye, etc.
- **TourSchedule**: Horario específico de un tour. Controla los cupos disponibles (`max_spots` / `booked_spots`).
- **Reservation**: Reserva de un turista para un `TourSchedule` específico. Incluye referencia, QR, estado de pago y estado de la reserva.
- **Review**: Reseña polimórfica que puede asociarse a tours, destinos, rutas o eventos.
- **Destination / Route / Event / Community**: Entidades secundarias mantenidas para enriquecer el catálogo de experiencias.

## Estructura del Proyecto

El proyecto está organizado en los siguientes módulos principales:

- **Models**: Contiene los modelos de datos (User, GuideProfile, Tour, TourSchedule, Reservation, Review, etc.)
- **Controllers**: Lógica de negocio y manejo de peticiones
- **Routes**: Definición de endpoints de la API
- **Requests**: Definición de validación de entrada de datos
- **Database**: Migraciones, factories y seeders

## Caché

El proyecto utiliza Redis como sistema de caché para mejorar el rendimiento. Las principales características incluyen:

- Caché de consultas frecuentes
- Caché de respuestas de API

Para configurar Redis:

1. Asegúrate de tener Redis instalado y ejecutándose
2. Configura las variables de entorno en `.env`:
```bash
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Autenticación

La API utiliza JWT (JSON Web Tokens) para la autenticación. Para acceder a los endpoints protegidos, necesitas:

1. Registrarte: `POST /api/auth/register`
2. Iniciar sesión: `POST /api/auth/login`
3. Usar el token recibido en el header de las peticiones: `Authorization: Bearer {token}`

## Documentación de la API

La documentación de la API está generada usando [Scramble](https://scramble.dedoc.co/), una herramienta moderna para documentar APIs Laravel. Para acceder a la documentación:

1. La documentación está disponible en: `/docs/api`

## Endpoints Principales

### Autenticación
- `POST /api/auth/register` - Registro de usuarios
- `POST /api/auth/login` - Inicio de sesión
- `POST /api/auth/logout` - Cierre de sesión
- `GET /api/auth/me` - Obtener información del usuario actual

### Destinos
- `GET /api/destinations` - Listar destinos
- `GET /api/destinations/popular` - Destinos populares
- `GET /api/destinations/{destination}` - Detalles de un destino
- `POST /api/destinations` - Crear destino (requiere autenticación)
- `PUT /api/destinations/{destination}` - Actualizar destino (requiere autenticación)
- `DELETE /api/destinations/{destination}` - Eliminar destino (requiere autenticación)

### Eventos
- `GET /api/events` - Listar eventos
- `GET /api/events/popular` - Eventos populares
- `GET /api/events/upcoming` - Próximos eventos
- `POST /api/events` - Crear evento (requiere autenticación)
- `PUT /api/events/{event}` - Actualizar evento (requiere autenticación)
- `DELETE /api/events/{event}` - Eliminar evento (requiere autenticación)

### Rutas
- `GET /api/routes` - Listar rutas
- `GET /api/routes/popular` - Rutas populares
- `POST /api/routes` - Crear ruta (requiere autenticación)
- `PUT /api/routes/{route}` - Actualizar ruta (requiere autenticación)
- `DELETE /api/routes/{route}` - Eliminar ruta (requiere autenticación)

### Comunidades
- `GET /api/communities` - Listar comunidades
- `GET /api/communities/popular` - Comunidades populares
- `POST /api/communities` - Crear comunidad (requiere autenticación)
- `PUT /api/communities/{community}` - Actualizar comunidad (requiere autenticación)
- `DELETE /api/communities/{community}` - Eliminar comunidad (requiere autenticación)

### Usuarios
- `GET /api/users` - Listar usuarios (requiere autenticación)
- `GET /api/users/{user}` - Ver perfil de usuario
- `PUT /api/users/{user}` - Actualizar perfil (requiere autenticación)
- `DELETE /api/users/{user}` - Eliminar usuario (requiere autenticación)

### Reseñas
- `GET /api/reviews` - Listar reseñas
- `POST /api/reviews` - Crear reseña (requiere autenticación)
- `PUT /api/reviews/{review}` - Actualizar reseña (requiere autenticación)
- `DELETE /api/reviews/{review}` - Eliminar reseña (requiere autenticación)

### Reservas
- `GET /api/reservations` - Listar reservas (requiere autenticación)
- `POST /api/reservations` - Crear reserva (requiere autenticación)
- `PUT /api/reservations/{reservation}` - Actualizar reserva (requiere autenticación)
- `DELETE /api/reservations/{reservation}` - Eliminar reserva (requiere autenticación)

## Contacto

Fernando Gil - [@fer-gc05](https://github.com/fer-gc05)

Link del Proyecto: [https://github.com/fer-gc05/AventuraLocalApi](https://github.com/fer-gc05/AventuraLocalApi)
