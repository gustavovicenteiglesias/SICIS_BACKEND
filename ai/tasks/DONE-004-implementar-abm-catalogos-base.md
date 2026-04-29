# TAREA 004: Implementar ABM De Catalogos Base

## Estado

DONE

## Objetivo

Implementar el primer bloque de endpoints CRUD para catalogos base segun el contrato aprobado.

## Criterios De Aceptacion

- [x] Implementar rutas bajo `/api/catalogos`.
- [x] Implementar controllers para catalogos prioritarios.
- [x] Validar requests de alta y modificacion.
- [x] Respetar permisos definidos o dejar middleware/policies preparados.
- [x] Mantener respuestas JSON consistentes con `docs/contratos-api-catalogos.md`.
- [x] No tocar frontend.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/`
- `app/Models/`
- `docs/contratos-api-catalogos.md`

## Notas

- Se implemento middleware `permission` para lectura y escritura.
- La validacion automatica con `php artisan` sigue pendiente en el entorno de Codex por falta de `php` en PATH.
