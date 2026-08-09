# AventuraLocal API

<p align="center">
<a href="https://github.com/fer-gc05/AventuraLocalApi"><img src="https://img.shields.io/badge/GitHub-AventuraLocalApi-blue" alt="GitHub Repository"></a>
<a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-v12.x-red" alt="Laravel Version"></a>
</p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
<img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
<img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
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
- Gestión de destinos turísticos con búsqueda nearby (Haversine)
- Gestión de rutas, eventos y comunidades
- Gestión de medios (imágenes y videos)
- Sistema de mensajería
- Autenticación JWT con middleware personalizado
- Documentación con Scramble
- Gestión de permisos y roles (`Administrator`, `Guide`, `Traveler`)
- **Arquitectura Repository Pattern** con contratos, implementaciones y Services layer
- Comando `make:repository` para generar repositorios automáticamente
- Script de testing completo (`test_api.sh`) - 118/124 endpoints verificados

## Requisitos del Sistema

- PHP >= 8.5
- Composer
- SQLite (desarrollo) o MySQL >= 5.7 (producción)
- Node.js & NPM (opcional, para frontend)

## Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/fer-gc05/AventuraLocalApi.git
cd AventuraLocalApi
```

2. Instalar dependencias:
```bash
composer install --ignore-platform-req=php
```

3. Configurar el archivo .env:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar la base de datos en `.env`:
```bash
# Desarrollo (SQLite - por defecto)
DB_CONNECTION=sqlite

# Producción (MySQL)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=aventura_local
# DB_USERNAME=root
# DB_PASSWORD=
```

5. Configurar JWT:
```bash
php artisan jwt:secret
```

6. Ejecutar migraciones con datos de prueba:
```bash
php artisan migrate:fresh --seed
```

7. Crear usuarios de prueba:
```bash
php artisan tinker
```
```php
\App\Models\User::create(['name'=>'Admin','email'=>'admin@test.com','password'=>Hash::make('password')]);
\App\Models\User::create(['name'=>'Guide','email'=>'guide@test.com','password'=>Hash::make('password')]);
```

8. Iniciar el servidor:
```bash
php artisan serve --port=8100
```

9. Ejecutar tests:
```bash
bash test_api.sh
```

## Modelo de Dominio

El backend está orientado a un marketplace de guías turísticos independientes:

- **User**: Cuenta de usuario genérica. Puede tener rol `Administrator`, `Guide` o `Traveler`.
- **GuideProfile**: Perfil profesional del guía, separado de la cuenta de usuario. Incluye biografía, idiomas, especialidades, estado de verificación y balances.
- **Tour**: Experiencia turística creada por un guía. Contiene título, descripción, precio por persona, punto de encuentro geolocalizado, cupos mínimos/máximos, idiomas, qué incluye/no incluye, etc.
- **TourSchedule**: Horario específico de un tour. Controla los cupos disponibles (`max_spots` / `booked_spots`).
- **Reservation**: Reserva de un turista para un `TourSchedule` específico. Incluye referencia, QR, estado de pago y estado de la reserva.
- **Review**: Reseña polimórfica que puede asociarse a tours, destinos, rutas o eventos.
- **Destination / Route / Event / Community**: Entidades secundarias mantenidas para enriquecer el catálogo de experiencias.
- **Category / Tag**: Clasificación y etiquetado de destinos y eventos.

## Estructura del Proyecto

```
app/
├── Console/Commands/
│   └── MakeRepositoryCommand.php        # Comando para generar repositorios
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CommunityController.php
│   │   │   ├── DestinationController.php
│   │   │   ├── EventController.php
│   │   │   ├── GuideProfileController.php
│   │   │   ├── ReservationController.php
│   │   │   ├── ReviewController.php
│   │   │   ├── RouteController.php
│   │   │   ├── TagController.php
│   │   │   ├── TourController.php
│   │   │   ├── TourScheduleController.php
│   │   │   └── UserController.php
│   │   └── Auth/
│   │       └── AuthController.php
│   ├── Middleware/
│   │   └── JwtAuthenticate.php          # JWT middleware personalizado
│   └── Requests/                         # Form Requests con validación
├── Models/                               # Modelos Eloquent
├── Providers/
│   └── AppServiceProvider.php           # Repository bindings
├── Repositories/
│   ├── Contracts/                        # Interfaces de repositorios
│   └── Implementations/                  # Clases concretas
├── Services/                             # Lógica de negocio
│   ├── BookingService.php
│   ├── DestinationService.php
│   ├── EventService.php
│   └── TourService.php
└── Traits/
    └── HasNearbyScope.php               # Haversine formula (SQLite)
```

## Arquitectura

El proyecto sigue el patrón **Repository + Service Layer**, con tres capas bien definidas:

| Capa | Responsabilidad | Ubicación |
|---|---|---|
| **Models** | Estructura de datos, casts y relaciones entre tablas | `app/Models/` |
| **Repositories** | Acceso a datos, consultas y persistencia (CRUD) | `app/Repositories/` |
| **Services** | Lógica de negocio, reglas y orquestación | `app/Services/` |

### Generar un repositorio nuevo

```bash
php artisan make:repository              # menú interactivo para seleccionar modelo
php artisan make:repository TourSchedule  # genera contrato + implementación
```

Esto crea automáticamente dos archivos:
- `app/Repositories/Contracts/TourScheduleRepositoryInterface.php`
- `app/Repositories/Implementations/TourScheduleRepositoryImplement.php`

## Autenticación

La API utiliza JWT (JSON Web Tokens) para la autenticación. Para acceder a los endpoints protegidos, necesitas:

1. Registrarte: `POST /api/auth/register`
2. Iniciar sesión: `POST /api/auth/login`
3. Usar el token recibido en el header: `Authorization: Bearer {token}`

**Roles disponibles:** `Administrator`, `Guide`, `Traveler`

## Endpoints Principales

### Autenticación
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/register` | Registro de usuarios | No |
| POST | `/api/auth/login` | Inicio de sesión | No |
| POST | `/api/auth/logout` | Cierre de sesión | Sí |
| GET | `/api/auth/me` | Usuario actual | Sí |

### Categorías
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/categories` | Listar categorías | No |
| GET | `/api/categories/popular` | Categorías populares | No |
| GET | `/api/categories/trashed` | Categorías eliminadas | Sí |
| GET | `/api/categories/{category}` | Detalles de categoría | No |
| POST | `/api/categories` | Crear categoría | Sí |
| PUT | `/api/categories/{category}` | Actualizar categoría | Sí |
| DELETE | `/api/categories/{category}` | Eliminar categoría | Sí |
| POST | `/api/categories/{category}/restore` | Restaurar categoría | Sí |

### Tags
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/tags` | Listar tags | No |
| GET | `/api/tags/trashed` | Tags eliminados | Sí |
| GET | `/api/tags/{tag}` | Detalles de tag | No |
| POST | `/api/tags` | Crear tag | Sí |
| PUT | `/api/tags/{tag}` | Actualizar tag | Sí |
| DELETE | `/api/tags/{tag}` | Eliminar tag | Sí |
| POST | `/api/tags/{tag}/restore` | Restaurar tag | Sí |

### Destinos
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/destinations` | Listar destinos | No |
| GET | `/api/destinations/popular` | Destinos populares | No |
| GET | `/api/destinations/nearby` | Destinos cercanos (Haversine) | No |
| GET | `/api/destinations/trashed` | Destinos eliminados | Sí |
| GET | `/api/destinations/{destination}` | Detalles de destino | No |
| GET | `/api/destinations/{destination}/reviews` | Reseñas del destino | No |
| GET | `/api/destinations/{destination}/routes` | Rutas del destino | No |
| GET | `/api/destinations/{destination}/events` | Eventos del destino | No |
| GET | `/api/destinations/{destination}/statistics` | Estadísticas | Sí |
| POST | `/api/destinations` | Crear destino | Sí |
| PUT | `/api/destinations/{destination}` | Actualizar destino | Sí |
| DELETE | `/api/destinations/{destination}` | Eliminar destino | Sí |
| POST | `/api/destinations/{destination}/restore` | Restaurar destino | Sí |

### Guide Profiles
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/guide-profiles` | Listar perfiles | No |
| GET | `/api/guide-profiles/verified` | Perfiles verificados | No |
| GET | `/api/guide-profiles/{profile}` | Detalles de perfil | No |
| POST | `/api/guide-profiles` | Crear perfil (solo Guide) | Sí |
| PUT | `/api/guide-profiles/{profile}` | Actualizar perfil | Sí |
| DELETE | `/api/guide-profiles/{profile}` | Eliminar perfil | Sí |

### Eventos
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/events` | Listar eventos | No |
| GET | `/api/events/popular` | Eventos populares | No |
| GET | `/api/events/upcoming` | Próximos eventos | No |
| GET | `/api/events/nearby` | Eventos cercanos | No |
| GET | `/api/events/calendar` | Calendario de eventos | No |
| GET | `/api/events/recommendations` | Recomendaciones | Sí |
| GET | `/api/events/trashed` | Eventos eliminados | Sí |
| GET | `/api/events/{event}` | Detalles de evento | No |
| GET | `/api/events/{event}/attendees` | Asistentes | Sí |
| GET | `/api/events/{event}/statistics` | Estadísticas | Sí |
| POST | `/api/events` | Crear evento | Sí |
| PUT | `/api/events/{event}` | Actualizar evento | Sí |
| DELETE | `/api/events/{event}` | Eliminar evento | Sí |
| POST | `/api/events/{event}/attend` | Asistir al evento | Sí |
| POST | `/api/events/{event}/cancel-attendance` | Cancelar asistencia | Sí |
| POST | `/api/events/{event}/restore` | Restaurar evento | Sí |

### Tours
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/tours` | Listar tours | No |
| GET | `/api/tours/trashed` | Tours eliminados | Sí |
| GET | `/api/tours/{tour}` | Detalles de tour | No |
| POST | `/api/tours` | Crear tour | Sí |
| PUT | `/api/tours/{tour}` | Actualizar tour | Sí |
| DELETE | `/api/tours/{tour}` | Eliminar tour | Sí |
| POST | `/api/tours/{tour}/restore` | Restaurar tour | Sí |

### Tour Schedules
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/tour-schedules` | Listar horarios | Sí |
| GET | `/api/tour-schedules/{schedule}` | Detalles de horario | Sí |
| POST | `/api/tour-schedules` | Crear horario | Sí |
| PUT | `/api/tour-schedules/{schedule}` | Actualizar horario | Sí |
| DELETE | `/api/tour-schedules/{schedule}` | Eliminar horario | Sí |

### Rutas
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/routes` | Listar rutas | No |
| GET | `/api/routes/popular` | Rutas populares | No |
| GET | `/api/routes/trashed` | Rutas eliminadas | Sí |
| GET | `/api/routes/{route}` | Detalles de ruta | No |
| GET | `/api/routes/{route}/reviews` | Reseñas de la ruta | Sí |
| GET | `/api/routes/{route}/communities` | Comunidades de la ruta | Sí |
| GET | `/api/routes/{route}/events` | Eventos de la ruta | Sí |
| POST | `/api/routes` | Crear ruta | Sí |
| PUT | `/api/routes/{route}` | Actualizar ruta | Sí |
| DELETE | `/api/routes/{route}` | Eliminar ruta | Sí |
| POST | `/api/routes/{route}/restore` | Restaurar ruta | Sí |
| POST | `/api/routes/{route}/communities/attach` | Asociar comunidad | Sí |
| POST | `/api/routes/{route}/communities/detach` | Desasociar comunidad | Sí |
| POST | `/api/routes/{route}/events/attach` | Asociar evento | Sí |
| POST | `/api/routes/{route}/events/detach` | Desasociar evento | Sí |

### Reseñas
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/reviews` | Listar reseñas | No |
| GET | `/api/reviews/{review}` | Detalles de reseña | Sí |
| POST | `/api/reviews` | Crear reseña | Sí |
| PUT | `/api/reviews/{review}` | Actualizar reseña | Sí |
| DELETE | `/api/reviews/{review}` | Eliminar reseña | Sí |
| POST | `/api/reviews/{review}/restore` | Restaurar reseña | Sí |

### Comunidades
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/communities` | Listar comunidades | No |
| GET | `/api/communities/popular` | Comunidades populares | No |
| GET | `/api/communities/trashed` | Comunidades eliminadas | Sí |
| GET | `/api/communities/recommendations` | Recomendaciones | Sí |
| GET | `/api/communities/{community}` | Detalles de comunidad | No |
| GET | `/api/communities/{community}/events` | Eventos de la comunidad | Sí |
| GET | `/api/communities/{community}/routes` | Rutas de la comunidad | Sí |
| POST | `/api/communities` | Crear comunidad | Sí |
| PUT | `/api/communities/{community}` | Actualizar comunidad | Sí |
| DELETE | `/api/communities/{community}` | Eliminar comunidad | Sí |
| POST | `/api/communities/{community}/join` | Unirse a comunidad | Sí |
| POST | `/api/communities/{community}/leave` | Salir de comunidad | Sí |
| POST | `/api/communities/{id}/restore` | Restaurar comunidad | Sí |

### Usuarios
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/users` | Listar usuarios | Sí |
| GET | `/api/users/trashed` | Usuarios eliminados | Sí |
| GET | `/api/users/{user}` | Ver perfil de usuario | Sí |
| POST | `/api/users` | Crear usuario | Sí |
| PUT | `/api/users/{user}` | Actualizar perfil | Sí |
| DELETE | `/api/users/{user}` | Eliminar usuario | Sí |
| POST | `/api/users/{user}/restore` | Restaurar usuario | Sí |
| GET | `/api/users/{user}/communities` | Comunidades del usuario | Sí |
| GET | `/api/users/{user}/event-history` | Historial de eventos | Sí |
| GET | `/api/users/{user}/favorite-routes` | Rutas favoritas | Sí |
| GET | `/api/users/{user}/favorite-destinations` | Destinos favoritos | Sí |
| GET | `/api/users/{user}/reviews` | Reseñas del usuario | Sí |
| GET | `/api/users/{user}/statistics` | Estadísticas del usuario | Sí |
| POST | `/api/users/destinations/{destination}/toggle-favorite` | Toggle favorito destino | Sí |
| POST | `/api/users/routes/{route}/toggle-favorite` | Toggle favorito ruta | Sí |
| POST | `/api/users/routes/{route}/update-status` | Actualizar estado ruta | Sí |

### Reservas
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/reservations` | Listar reservas | Sí |
| GET | `/api/reservations/trashed` | Reservas eliminadas | Sí |
| GET | `/api/reservations/{reservation}` | Detalles de reserva | Sí |
| POST | `/api/reservations` | Crear reserva | Sí |
| POST | `/api/reservations/{reservation}/confirm` | Confirmar reserva | Sí |
| POST | `/api/reservations/{reservation}/cancel` | Cancelar reserva | Sí |
| DELETE | `/api/reservations/{reservation}` | Eliminar reserva | Sí |
| POST | `/api/reservations/{reservation}/restore` | Restaurar reserva | Sí |

## Documentación de la API

La documentación está generada con [Scramble](https://scramble.dedoc.co/) y disponible en: `/docs/api`

## Contacto

Fernando Gil - [@fer-gc05](https://github.com/fer-gc05)

Link del Proyecto: [https://github.com/fer-gc05/AventuraLocalApi](https://github.com/fer-gc05/AventuraLocalApi)
