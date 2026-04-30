# TAREA 018: Ampliar Pruebas De Integracion Y Permisos

## Estado

DONE

## Objetivo

Ampliar la cobertura automatizada sobre modulos criticos del MVP: indicadores, datos fuente, permisos y flujos protegidos.

## Criterios De Aceptacion

- [x] Tests de indicadores y versiones metodologicas cubiertos.
- [x] Tests de variables de indicador cubiertos.
- [x] Tests de datos fuente y validacion de valores cubiertos.
- [x] Tests de permisos diferenciados cubiertos.
- [x] Helpers o fixtures reutilizables consolidados.

## Archivos Involucrados

- `tests/`
- `database/seeders/`
- `app/Http/Controllers/`
- `routes/api.php`

## Notas

- Esta tarea extiende la base creada en la tarea 012.
- Priorizar casos de negocio y seguridad antes que cobertura cosmetica.
- Se consolidaron helpers para usuarios con permisos especificos dentro de la suite de tests.
