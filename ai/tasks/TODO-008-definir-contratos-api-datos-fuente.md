# TAREA 008: Definir Contratos API De Datos Fuente

## Estado

TODO

## Objetivo

Diseñar el contrato API para datos fuente, valores, evidencias y configuraciones API antes de implementar la capa operativa.

## Criterios De Aceptacion

- [ ] Documentar endpoints para datos fuente.
- [ ] Documentar endpoints para carga manual de valores.
- [ ] Documentar endpoints para evidencias.
- [ ] Documentar endpoints para configuracion de consumo API.
- [ ] Definir roles y permisos de carga, validacion y consulta.

## Archivos Involucrados

- `docs/`
- `app/Models/DatoFuente.php`
- `app/Models/DatoFuenteValor.php`
- `app/Models/EvidenciaDato.php`
- `app/Models/DatoFuenteApiConfig.php`

## Notas

- Hay que contemplar carga manual y fallback para importaciones externas.
- No implementar hasta aprobar contrato.
