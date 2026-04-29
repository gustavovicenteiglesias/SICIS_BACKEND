# TAREA 010: Implementar Corridas Y Snapshots

## Estado

TODO

## Objetivo

Implementar el circuito inicial de corridas, snapshots de datos y snapshots de indicadores.

## Criterios De Aceptacion

- [ ] Crear contrato de ejecucion de corrida.
- [ ] Persistir corridas con estado.
- [ ] Persistir snapshot de datos usados.
- [ ] Persistir snapshot de resultados de indicadores.
- [ ] Definir endpoints de aprobacion y publicacion.
- [ ] Proteger ejecucion, aprobacion y publicacion con permisos diferenciados.
- [ ] Dejar trazabilidad minima de errores o resultados observados.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/`
- `app/Models/Corrida.php`
- `app/Models/CorridaSnapshotDato.php`
- `app/Models/CorridaSnapshotIndicador.php`

## Notas

- Resolver primero el circuito minimo, no el motor completo de formulas.
- Depende de tener indicadores y datos fuente operativos.
- Si el motor de formula completo no entra, priorizar un ejecutor minimo y deterministicamente trazable.
