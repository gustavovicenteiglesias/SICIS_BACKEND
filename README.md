# SICIS Backend

Backend Laravel para SICIS Luján. El proyecto expone una API y administra el núcleo de usuarios, roles, catálogos, indicadores, datos fuente, corridas, snapshots, alertas y auditorías.

## Requisitos

- PHP 8.2 o superior
- Composer
- MySQL o MariaDB
- Git

En Windows, si `php` no responde en PowerShell, hay que agregar el ejecutable de PHP al `PATH` o usar la terminal del entorno que lo incluya.

## Puesta En Marcha

Clonar el repositorio:

```bash
git clone <URL_DEL_REPOSITORIO>
cd <CARPETA_DEL_PROYECTO>
```

Instalar dependencias PHP:

```bash
composer install
```

Crear el archivo de entorno:

```bash
cp .env.example .env
```

En PowerShell, si `cp` no está disponible:

```powershell
Copy-Item .env.example .env
```

Generar la clave de Laravel:

```bash
php artisan key:generate
```

Configurar la base en `.env`. Ejemplo MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sicis_lujan
DB_USERNAME=root
DB_PASSWORD=
```

Crear la base de datos vacía en MySQL antes de migrar:

```sql
CREATE DATABASE sicis_lujan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Ejecutar migrations y seeders:

```bash
php artisan migrate:fresh --seed
```

Esto recrea todas las tablas y carga datos iniciales. Usarlo solo en entorno local o cuando se pueda borrar la base.

Antes de correrlo, revisar que `.env` no apunte a una base remota o compartida. `migrate:fresh` elimina las tablas existentes.

## Usuario Inicial

El seeder crea un usuario administrador:

```text
usuario: admin
password: 12345678
```

El login usa el campo `nombre_usuario`, aunque en la API se envía como `usuario` para mantener simple el contrato del frontend.

Los seeders también cargan roles, permisos, áreas municipales, normas, unidades, periodicidades, estados, modalidades, tipos de jurisdicción, jurisdicciones y categorías base.

## Levantar El Proyecto

Servidor Laravel:

```bash
php artisan serve
```

Por defecto queda en:

```text
http://127.0.0.1:8000
```

También se puede usar el script de Composer:

```bash
composer run dev
```

Ese comando levanta el servidor local de Laravel.

## Endpoints Base

Login:

```http
POST /api/login
Content-Type: application/json
```

Body esperado:

```json
{
  "usuario": "admin",
  "password": "12345678"
}
```

Respuesta exitosa:

```json
{
  "mensaje": "Login exitoso",
  "token": "plain-text-token",
  "usuario": {
    "id": 1,
    "nombre_usuario": "admin",
    "nombre": "Admin",
    "apellido": "Sistema",
    "email": "admin@lujan.gob.ar",
    "activo": true,
    "roles": [
      {
        "id": 1,
        "codigo": "ADMINISTRADOR_GENERAL",
        "nombre": "Administrador general"
      }
    ]
  }
}
```

Perfil autenticado:

```http
GET /api/perfil
Authorization: Bearer <token>
```

Respuesta:

```json
{
  "usuario": {
    "id": 1,
    "nombre_usuario": "admin",
    "nombre": "Admin",
    "apellido": "Sistema",
    "email": "admin@lujan.gob.ar",
    "activo": true,
    "roles": [
      {
        "id": 1,
        "codigo": "ADMINISTRADOR_GENERAL",
        "nombre": "Administrador general"
      }
    ]
  }
}
```

Errores esperados:

- `422`: faltan campos, credenciales incorrectas o usuario inactivo.
- `401`: token ausente, inválido o expirado en rutas protegidas.

Healthcheck simple:

```http
GET /
```

## Documentación API

- [Indice de contratos](docs/indice-contratos-api.md)
- [Explicación del backend para el equipo](docs/explicacion-backend-para-equipo.md)
- [Guía corta de Postman](docs/guia-postman.md)
- [Colección Postman](docs/SICIS-Backend.postman_collection.json)
- [Convenciones de errores y observabilidad](docs/convenciones-api-errores.md)
- [Checklist de release MVP](docs/checklist-release-mvp.md)
- [Contratos de catálogos](docs/contratos-api-catalogos.md)
- [Contratos de indicadores](docs/contratos-api-indicadores.md)
- [Contratos de datos fuente](docs/contratos-api-datos-fuente.md)
- [Contratos de corridas](docs/contratos-api-corridas.md)
- [Contratos de observabilidad](docs/contratos-api-observabilidad.md)
- [Contratos de seguridad interna](docs/contratos-api-usuarios-roles.md)
- [Contratos de exportaciones y consultas externas](docs/contratos-api-exportaciones.md)

## Comandos Útiles

Ver rutas:

```bash
php artisan route:list
```

Limpiar caches de configuración:

```bash
php artisan optimize:clear
```

Ejecutar tests:

```bash
php artisan test
```

Formatear código con Pint:

```bash
./vendor/bin/pint
```

En Windows:

```powershell
vendor\bin\pint
```

## Notas Laravel Para El Equipo

- Las tablas se definen en `database/migrations`.
- Los datos iniciales se cargan desde `database/seeders`.
- Los modelos Eloquent están en `app/Models`.
- Las rutas de API están en `routes/api.php`.
- Los controllers están en `app/Http/Controllers`.
- Sanctum maneja los tokens de API.
- El frontend vive fuera de este repositorio.

## Trabajo Con IA

Antes de usar un agente de IA sobre este repo, leer [docs/guia-trabajo-con-ia.md](docs/guia-trabajo-con-ia.md).

Regla corta: pedirle siempre al agente que lea `AGENTS.md` antes de diagnosticar o tocar código.

## Estado MVP

El backend MVP queda cerrado con:

- auth interna
- seguridad por roles y permisos
- catálogos
- indicadores
- datos fuente
- corridas y snapshots
- observabilidad mínima
- exportaciones y consultas externas
- documentación de contratos
- colección Postman
- base de pruebas automatizadas

Para revisar el cierre técnico y los pendientes conocidos, ver [docs/checklist-release-mvp.md](docs/checklist-release-mvp.md).

## Problemas Frecuentes

Si cambia `.env` y Laravel parece ignorarlo:

```bash
php artisan optimize:clear
```

Si una migration falla a mitad de camino en desarrollo:

```bash
php artisan migrate:fresh --seed
```

Si Git marca `dubious ownership`:

```bash
git config --global --add safe.directory <RUTA_ABSOLUTA_DEL_PROYECTO>
```

Ejemplo:

```bash
git config --global --add safe.directory C:/Users/Gustavo/OneDrive/Documentos/Laravel/sicis-backend
```

Si MySQL rechaza una tabla ya existente, revisar que se esté usando la base correcta en `.env` y volver a correr `migrate:fresh --seed` solo si se puede borrar la base.
