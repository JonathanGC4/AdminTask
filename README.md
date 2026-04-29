# 📋 Task Manager API — Backend

> API REST para gestión de tareas con sistema de roles, construida con Laravel y MySQL.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Sanctum](https://img.shields.io/badge/Laravel_Sanctum-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

---

## 📌 Descripción

API REST que gestiona usuarios con roles (Admin / Employee) y sus tareas. El Admin puede crear employees, asignarles tareas y eliminarlas. El Employee solo puede ver sus tareas y actualizar su estado.

---

## ✨ Características

- 🔐 Autenticación stateless con **Laravel Sanctum** (tokens)
- 👥 Sistema de **roles**: Admin y Employee
- ✅ **CRUD completo** de tareas con asignación por usuario
- 🔍 **Filtros** por estado, búsqueda por título y filtro por usuario
- 📄 **Paginación** configurable
- 🛡️ **Middleware personalizado** de verificación de rol Admin
- 📋 **Form Requests** para validaciones desacopladas
- 🔗 **Relaciones Eloquent** entre modelos
- ⚠️ **Manejo global de errores** con respuestas JSON consistentes

---

## 🛠️ Stack

| Tecnología | Versión | Uso |
|---|---|---|
| PHP | 8.2+ | Lenguaje de programación |
| Laravel | 10+ | Framework principal |
| Laravel Sanctum | 3.x | Autenticación por tokens |
| MySQL | 8.0+ | Base de datos |

---

## 🗂️ Estructura del proyecto

```
task-manager-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Login, register, logout, perfil
│   │   │   ├── TaskController.php      # CRUD de tareas
│   │   │   └── UserController.php      # Gestión de employees
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php   # Protección de rutas admin
│   │   └── Requests/
│   │       ├── StoreTaskRequest.php    # Validación al crear tarea
│   │       └── UpdateTaskRequest.php   # Validación al actualizar tarea
│   └── Models/
│       ├── User.php                    # Modelo usuario con rol
│       └── Task.php                    # Modelo tarea con relaciones
├── database/
│   └── migrations/
│       ├── create_users_table.php
│       ├── create_tasks_table.php
│       ├── add_role_to_users_table.php
│       └── add_assigned_by_to_tasks_table.php
└── routes/
    └── api.php                         # Definición de endpoints
```

---

## ⚙️ Instalación

### Requisitos previos

- PHP 8.2+
- Composer
- MySQL 8.0+

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/task-manager-api.git
cd task-manager-api

# 2. Instalar dependencias
composer install

# 3. Copiar entorno
cp .env.example .env

# 4. Generar clave de aplicación
php artisan key:generate
```

Edita `.env` con tus credenciales:

```env
APP_NAME=TaskManagerAPI
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager_db
DB_USERNAME=root
DB_PASSWORD=tu_password
```

```bash
# 5. Crear base de datos
mysql -u root -p -e "CREATE DATABASE task_manager_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Ejecutar migraciones
php artisan migrate

# 7. Crear usuario Admin inicial
php artisan tinker
```

```php
\App\Models\User::create([
    'name'     => 'Administrador',
    'email'    => 'admin@taskmanager.com',
    'password' => bcrypt('password123'),
    'role'     => 'admin',
]);
exit
```

```bash
# 8. Levantar el servidor
php artisan serve
```

API disponible en `http://127.0.0.1:8000`

---

## 📡 Endpoints

### Públicos

| Método | Endpoint | Descripción | Body |
|--------|----------|-------------|------|
| POST | `/api/register` | Registro de usuario | `name, email, password, password_confirmation` |
| POST | `/api/login` | Iniciar sesión | `email, password` |

### Protegidos (requiere `Authorization: Bearer {token}`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/logout` | Cerrar sesión |
| PUT | `/api/profile` | Actualizar nombre y email |
| PUT | `/api/profile/password` | Cambiar contraseña |

### Tareas

| Método | Endpoint | Admin | Employee | Descripción |
|--------|----------|-------|----------|-------------|
| GET | `/api/tasks` | Todas | Solo las suyas | Listar tareas |
| POST | `/api/tasks` | ✅ | ❌ | Crear y asignar tarea |
| GET | `/api/tasks/{id}` | ✅ | Solo las suyas | Ver tarea |
| PATCH | `/api/tasks/{id}` | ✅ completo | Solo `status` | Actualizar tarea |
| DELETE | `/api/tasks/{id}` | ✅ | ❌ | Eliminar tarea |

### Usuarios (solo Admin)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/users` | Listar employees |
| POST | `/api/users` | Crear employee |
| DELETE | `/api/users/{id}` | Eliminar employee |

### Parámetros de filtrado (GET /api/tasks)

```
?status=pending         → Tareas pendientes
?status=completed       → Tareas completadas
?search=laravel         → Buscar por título
?per_page=5             → Resultados por página
?user_id=2              → Filtrar por employee (solo admin)
```

---

## 📦 Ejemplos de requests y responses

### Login
```json
// POST /api/login
// Body:
{
    "email": "admin@taskmanager.com",
    "password": "password123"
}

// Response 200:
{
    "message": "Login exitoso",
    "user": {
        "id": 1,
        "name": "Administrador",
        "email": "admin@taskmanager.com",
        "role": "admin"
    },
    "access_token": "1|abc123...",
    "token_type": "Bearer"
}
```

### Crear tarea (Admin)
```json
// POST /api/tasks
// Body:
{
    "title": "Revisar documentación",
    "description": "Leer y validar los docs del proyecto",
    "status": "pending",
    "user_id": 2
}

// Response 201:
{
    "message": "Tarea asignada correctamente",
    "data": {
        "id": 1,
        "title": "Revisar documentación",
        "status": "pending",
        "user_id": 2,
        "assigned_by": 1
    }
}
```

### Actualizar status (Employee)
```json
// PATCH /api/tasks/1
// Body:
{
    "status": "completed"
}

// Response 200:
{
    "message": "Tarea actualizada correctamente",
    "data": {
        "id": 1,
        "status": "completed"
    }
}
```

---

## 🗄️ Base de datos

### Tabla `users`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | Primary key |
| name | varchar | Nombre del usuario |
| email | varchar | Email único |
| password | varchar | Contraseña hasheada |
| role | enum | `admin` o `employee` |
| created_at | timestamp | Fecha de creación |

### Tabla `tasks`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | Primary key |
| title | varchar | Título de la tarea |
| description | text | Descripción opcional |
| status | enum | `pending` o `completed` |
| user_id | bigint | FK → Employee asignado |
| assigned_by | bigint | FK → Admin que asignó |
| created_at | timestamp | Fecha de creación |

---

## 🔑 Sistema de roles

```
Admin
├── Crear / eliminar employees
├── Asignar tareas a employees
├── Ver todas las tareas
├── Editar cualquier tarea
└── Eliminar cualquier tarea

Employee
├── Ver sus tareas asignadas
└── Cambiar status de sus tareas (pending ↔ completed)
```

---

## 🧪 Pruebas con Postman

1. Crea un environment con la variable `base_url = http://127.0.0.1:8000/api`
2. Agrega este script en los requests de login/register:

```javascript
const response = pm.response.json();
if (response.access_token) {
    pm.environment.set("token", response.access_token);
}
```

3. En los requests protegidos usa: `Authorization: Bearer {{token}}`

---

## 👨‍💻 Autor

Desarrollado como proyecto de portafolio.

---

## 📄 Licencia

MIT
