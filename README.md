# SICIS Backend

Backend Laravel para SICIS Lujan. El proyecto expone una API y administra el nucleo de usuarios, roles, catalogos, indicadores, datos fuente, corridas, snapshots, alertas y auditoria.

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

En PowerShell, si `cp` no esta disponible:

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

Crear la base de datos vacia en MySQL antes de migrar:

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

El login usa el campo `nombre_usuario`, aunque en la API se envia como `usuario` para mantener simple el contrato del frontend.

Los seeders tambien cargan roles, permisos, areas municipales, normas, unidades, periodicidades, estados, modalidades, tipos de jurisdiccion, jurisdicciones y categorias base.

## Levantar El Proyecto

Servidor Laravel:

```bash
php artisan serve
```

Por defecto queda en:

```text
http://127.0.0.1:8000
```

Tambien se puede usar el script de Composer:

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
- `401`: token ausente, invalido o expirado en rutas protegidas.

Healthcheck simple:

```http
GET /
```

## Comandos Utiles

Ver rutas:

```bash
php artisan route:list
```

Limpiar caches de configuracion:

```bash
php artisan optimize:clear
```

Ejecutar tests:

```bash
php artisan test
```

Formatear codigo con Pint:

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
- Los modelos Eloquent estan en `app/Models`.
- Las rutas de API estan en `routes/api.php`.
- Los controllers estan en `app/Http/Controllers`.
- Sanctum maneja los tokens de API.
- El frontend vive fuera de este repositorio.

## Trabajo Con IA

Antes de usar un agente de IA sobre este repo, leer [docs/guia-trabajo-con-ia.md](docs/guia-trabajo-con-ia.md).

Regla corta: pedirle siempre al agente que lea `AGENTS.md` antes de diagnosticar o tocar codigo.

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
git config --global --add safe.directory C:/Users/Gustavo/OneDrive/Documentos/Laravel/sicis-backend
```

Si MySQL rechaza una tabla ya existente, revisar que se este usando la base correcta en `.env` y volver a correr `migrate:fresh --seed` solo si se puede borrar la base.
