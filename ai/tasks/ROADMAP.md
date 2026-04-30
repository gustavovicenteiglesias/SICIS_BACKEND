# Roadmap De Tareas Backend

Este archivo ordena las tareas pendientes del backend SICIS para que cualquier persona o agente pueda retomar el trabajo sin depender del contexto conversacional previo.

## Hecho

- `DONE-001` Estabilizar migrations y seeders
- `DONE-002` Definir contratos API auth
- `DONE-003` Planificar ABM catalogos
- `DONE-004` Implementar ABM catalogos base
- `DONE-005` Implementar ABM catalogos secundarios
- `DONE-006` Definir contratos API indicadores
- `DONE-007` Implementar ABM indicadores
- `DONE-008` Definir contratos API datos fuente
- `DONE-009` Implementar flujo de datos fuente
- `DONE-010` Implementar corridas y snapshots
- `DONE-011` Implementar auditoria y alertas
- `DONE-012` Armar pruebas API smoke
- `DONE-017` Implementar exportaciones y consultas externas
- `DONE-018` Ampliar pruebas de integracion y permisos
- `DONE-013` Documentar contratos y colecciones API
- `DONE-019` Endurecer observabilidad y manejo de errores
- `DONE-020` Cierre MVP y checklist de release
- `DONE-014` Implementar conectores API de datos fuente
- `DONE-015` Definir contratos API de usuarios y roles
- `DONE-016` Implementar administracion de usuarios y roles

## Orden Recomendado

### Fase 1 - Datos Fuente Operativos

- Completada con `DONE-009` y `DONE-014`

### Fase 2 - Seguridad Operativa Interna

- Completada con `DONE-015` y `DONE-016`

### Fase 3 - Corridas Y Trazabilidad

- Completada con `DONE-010`, `DONE-011` y `DONE-017`

### Fase 4 - Calidad, Cierre Y Entrega

- Completada con `DONE-012`, `DONE-013`, `DONE-018`, `DONE-019` y `DONE-020`

## Criterio General

- Primero contrato o decision, despues implementacion.
- Primero API y persistencia, nunca frontend en este repositorio.
- Antes de cerrar una fase, actualizar `ai/context.md` y la documentacion asociada.
- Si una tarea cambia de alcance, actualizar su archivo antes de seguir.
