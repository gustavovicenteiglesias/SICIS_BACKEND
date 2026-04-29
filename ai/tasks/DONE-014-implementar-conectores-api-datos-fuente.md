# TAREA 014: Implementar Conectores API De Datos Fuente

## Estado

DONE

## Objetivo

Implementar la configuracion y ejecucion basica de conectores HTTP para datos fuente, incluyendo prueba tecnica e importacion con trazabilidad.

## Criterios De Aceptacion

- [x] CRUD de `datos_fuente_api_configs` operativo.
- [x] CRUD de `datos_fuente_api_paths` operativo.
- [x] Endpoint de prueba tecnica de conector operativo.
- [x] Endpoint de importacion con registro en `datos_fuente_api_importaciones` operativo.
- [x] Manejo basico de errores HTTP, parseo y paths alternativos resuelto.
- [x] Proteccion por permisos aplicada.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/`
- `app/Models/DatoFuenteApiConfig.php`
- `app/Models/DatoFuenteApiPath.php`
- `app/Models/DatoFuenteApiImportacion.php`
- `docs/contratos-api-datos-fuente.md`

## Notas

- No agregar integraciones demasiado sofisticadas en esta etapa.
- Priorizar conectores simples tipo `GET` con headers y params JSON.
- Toda importacion debe dejar trazabilidad aunque falle.
- La lectura y administracion de conectores quedaron bajo `datos_fuente.configurar`.
- La accion `importar` quedo bajo `datos_fuente.cargar`.
- La importacion usa fallbacks por request para `jurisdiccion_id` y `periodo_referencia` si la API no entrega esos datos de forma usable.
