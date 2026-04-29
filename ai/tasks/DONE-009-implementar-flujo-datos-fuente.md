# TAREA 009: Implementar Flujo De Datos Fuente

## Estado

DONE

## Objetivo

Implementar el flujo operativo manual de datos fuente: definicion, carga de valores, validacion basica y evidencias.

## Criterios De Aceptacion

- [x] CRUD de datos fuente operativo.
- [x] Alta y consulta de valores de datos fuente operativa.
- [x] Accion explicita de validacion de valores operativa.
- [x] Soporte basico de evidencias ligado a valores.
- [x] Estados de dato y validacion basica aplicados.
- [x] Proteccion por permisos aplicada.
- [x] Responses con relaciones minimas consistentes para dato, valor y evidencia.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/`
- `app/Models/DatoFuente.php`
- `app/Models/DatoFuenteValor.php`
- `app/Models/EvidenciaDato.php`
- `docs/contratos-api-datos-fuente.md`

## Notas

- Diferenciar claramente carga, validacion y consulta.
- Esta tarea cubre flujo manual y evidencias.
- Los conectores API y las importaciones externas se resuelven en una tarea separada.
- Reutilizar permisos propuestos en `docs/contratos-api-datos-fuente.md`.
- Se incorporaron permisos nuevos `datos_fuente.ver`, `datos_fuente.configurar`, `datos_fuente.cargar` y `datos_fuente.validar`.
- La validacion se resolvio con `POST /api/datos-fuente/{datoFuente}/valores/{valor}/validar`.
