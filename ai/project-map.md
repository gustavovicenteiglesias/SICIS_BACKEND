# Mapa Del Proyecto

## Directorios Principales

```text
app/
  Http/Controllers/     Controllers HTTP/API
  Models/               Modelos Eloquent

bootstrap/              Arranque de Laravel
config/                 Configuracion Laravel y paquetes
database/
  migrations/           Esquema de base
  seeders/              Datos iniciales
  factories/            Factories de testing

public/                 Entrada publica de Laravel
routes/
  api.php               Rutas API
  web.php               Healthcheck JSON minimo

docs/                   Guias para el equipo humano
storage/                Logs/cache/archivos runtime
tests/                  Tests automatizados
```

## Archivos Clave

- `routes/api.php`: endpoints API consumidos por el frontend externo.
- `routes/web.php`: healthcheck simple, sin UI.
- `app/Http/Controllers/AuthController.php`: login API.
- `app/Models/Usuario.php`: usuario autenticable con Sanctum.
- `app/Models/Rol.php`: roles y permisos.
- `database/migrations/2026_04_28_215105_create_areas_municipales_table.php`: catalogo de areas.
- `database/migrations/2026_04_28_215106_create_roles_table.php`: roles.
- `database/migrations/2026_04_28_215107_create_usuarios_table.php`: usuarios.
- `database/migrations/2026_04_28_215108_create_sicis_catalog_tables.php`: catalogos base.
- `database/migrations/2026_04_28_215109_create_sicis_core_tables.php`: indicadores y datos fuente.
- `database/migrations/2026_04_28_215110_create_sicis_operations_tables.php`: API externa, corridas, alertas, auditoria.
- `database/migrations/2026_04_28_215111_create_sicis_views.php`: vistas de consulta.
- `database/seeders/DatabaseSeeder.php`: punto de entrada de seeders.
- `README.md`: guia de puesta en marcha.
- `docs/guia-trabajo-con-ia.md`: guia humana para usar agentes de IA de forma consistente.

## Restricciones

- No reintroducir `package.json`, Vite ni assets frontend.
- No crear pantallas en `resources/views`.
- No mover carpetas base de Laravel sin motivo.
- No escanear `vendor/`, `node_modules/` ni dumps grandes salvo necesidad puntual.
