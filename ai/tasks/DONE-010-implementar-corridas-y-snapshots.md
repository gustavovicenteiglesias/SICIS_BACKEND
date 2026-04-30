# TAREA 010: Implementar Corridas Y Snapshots

## Estado

DONE

## Objetivo

Implementar el circuito inicial de corridas, snapshots de datos y snapshots de indicadores.

## Criterios De Aceptacion

- [x] Crear contrato de ejecucion de corrida.
- [x] Persistir corridas con estado.
- [x] Persistir snapshot de datos usados.
- [x] Persistir snapshot de resultados de indicadores.
- [x] Definir endpoints de aprobacion y publicacion.
- [x] Proteger ejecucion, aprobacion y publicacion con permisos diferenciados.
- [x] Dejar trazabilidad minima de errores o resultados observados.

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
- Se implemento un ejecutor minimo que prioriza versiones activas por periodo, valores validados y formulas simples.
- La primera version soporta `RATIO_CONSTANTE`, casos de una variable y agregacion simple cuando no hay expresion completa.
