# TAREA 012: Armar Pruebas API Smoke

## Estado

DONE

## Objetivo

Construir una base minima de pruebas automatizadas para auth y catalogos, y dejar preparado el terreno para ampliar cobertura del backend.

## Criterios De Aceptacion

- [x] Test de login exitoso.
- [x] Test de acceso protegido sin token.
- [x] Test de permiso insuficiente con 403.
- [x] Test de listado de categorias.
- [x] Test de alta de categoria con usuario autorizado.
- [x] Dejar factory o helpers de autenticacion reutilizables para las siguientes pruebas.

## Archivos Involucrados

- `tests/`
- `database/seeders/`
- `app/Http/Controllers/`
- `routes/api.php`

## Notas

- Mantener alcance chico al principio.
- No intentar cubrir todo el dominio en una sola tarea.
- Esta tarea es el piso minimo antes de ampliar cobertura por modulo.
- Se implemento un helper reutilizable de autenticacion para futuras suites feature.
