# TAREA 017: Implementar Exportaciones Y Consultas Externas

## Estado

TODO

## Objetivo

Exponer salidas consumibles por terceros para el MVP: exportaciones simples y endpoints de consulta listos para integracion externa o BI.

## Criterios De Aceptacion

- [ ] Definir endpoints de exportacion minima para indicadores y corridas.
- [ ] Exponer formatos simples consumibles por herramientas externas.
- [ ] Reutilizar vistas o consultas preparadas cuando convenga.
- [ ] Aplicar permisos y filtros adecuados para datos publicables.
- [ ] Documentar alcance y restricciones de estas salidas.

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
