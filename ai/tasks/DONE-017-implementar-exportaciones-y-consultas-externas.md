# TAREA 017: Implementar Exportaciones Y Consultas Externas

## Estado

DONE

## Objetivo

Exponer salidas consumibles por terceros para el MVP: exportaciones simples y endpoints de consulta listos para integracion externa o BI.

## Criterios De Aceptacion

- [x] Definir endpoints de exportacion minima para indicadores y corridas.
- [x] Exponer formatos simples consumibles por herramientas externas.
- [x] Reutilizar vistas o consultas preparadas cuando convenga.
- [x] Aplicar permisos y filtros adecuados para datos publicables.
- [x] Documentar alcance y restricciones de estas salidas.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/`
- `app/Models/`
- `database/migrations/2026_04_28_215111_create_sicis_views.php`
- `docs/`

## Notas

- Priorizar JSON y, solo si hace falta para el MVP, CSV simple.
- No construir reporting complejo en esta etapa.
- Tener especial cuidado con datos sensibles y resultados no publicables.
- La primera implementacion usa JSON paginado por defecto y `format=csv` para exportacion simple.
