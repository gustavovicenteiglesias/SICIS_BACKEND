# TAREA 007: Implementar ABM De Indicadores

## Estado

DONE

## Objetivo

Implementar endpoints para indicadores, sus versiones metodologicas y sus variables, siguiendo el contrato aprobado.

## Criterios De Aceptacion

- [x] CRUD de indicadores operativo.
- [x] CRUD de versiones metodologicas operativo.
- [x] CRUD de variables de indicador operativo.
- [x] Validaciones de unicidad y vigencia resueltas.
- [x] Relaciones cargadas de forma consistente en responses.
- [x] Proteccion por permisos aplicada.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/`
- `app/Models/Indicador.php`
- `app/Models/IndicadorVersion.php`
- `app/Models/IndicadorVariable.php`

## Notas

- Prestar atencion a `codigo_interno`, versionado y vigencia.
- Se resolvio `normas` dentro del payload del indicador.
- Se resolvio variables con endpoints propios bajo cada version.
- Se impide superposicion de versiones activas para un mismo indicador.
