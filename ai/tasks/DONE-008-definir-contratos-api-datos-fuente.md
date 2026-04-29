# TAREA 008: Definir Contratos API De Datos Fuente

## Estado

DONE

## Objetivo

Diseñar el contrato API para datos fuente, valores, evidencias y configuraciones API antes de implementar la capa operativa.

## Criterios De Aceptacion

- [x] Documentar endpoints para datos fuente.
- [x] Documentar endpoints para carga manual de valores.
- [x] Documentar endpoints para evidencias.
- [x] Documentar endpoints para configuracion de consumo API.
- [x] Definir roles y permisos de carga, validacion y consulta.

## Archivos Involucrados

- `docs/`
- `app/Models/DatoFuente.php`
- `app/Models/DatoFuenteValor.php`
- `app/Models/EvidenciaDato.php`
- `app/Models/DatoFuenteApiConfig.php`

## Notas

- Hay que contemplar carga manual y fallback para importaciones externas.
- No implementar hasta aprobar contrato.
- Se propuso `POST /validar` como accion explicita sobre cada valor.
- Se propuso separar permisos de ver, configurar, cargar y validar.
- Se documento `probar` e `importar` para conectores API sin cerrar aun la estrategia exacta de persistencia.
