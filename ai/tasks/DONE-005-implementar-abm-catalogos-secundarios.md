# TAREA 005: Implementar ABM De Catalogos Secundarios

## Estado

DONE

## Objetivo

Implementar el segundo bloque de catalogos del MVP: areas municipales, fuentes institucionales, tipos de indicador, normas, tipos de jurisdiccion y jurisdicciones.

## Criterios De Aceptacion

- [x] Definir y documentar las rutas faltantes del bloque secundario.
- [x] Implementar controllers siguiendo el patron de catalogos ya creado.
- [x] Resolver validaciones particulares de jerarquias en jurisdicciones.
- [x] Proteger lectura y escritura con el middleware `permission`.
- [x] Mantener consistencia con `docs/contratos-api-catalogos.md` o extenderlo si hace falta.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/Catalogos/`
- `app/Models/`
- `docs/contratos-api-catalogos.md`

## Notas

- Jurisdicciones tiene autoreferencia y necesita validacion cuidadosa.
- Backend API solamente.
- La validacion automatica con `php artisan` sigue pendiente en el entorno de Codex por falta de `php` en PATH.
