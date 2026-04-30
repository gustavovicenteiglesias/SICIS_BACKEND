# TAREA 011: Implementar Auditoria Y Alertas

## Estado

DONE

## Objetivo

Habilitar trazabilidad tecnica y funcional minima a traves de auditoria, alertas y notificaciones internas del sistema.

## Criterios De Aceptacion

- [x] Definir cuando se escribe en auditoria.
- [x] Crear endpoints de consulta de auditoria.
- [x] Crear endpoints de consulta de alertas.
- [x] Definir reglas minimas de generacion de alertas.
- [x] Aplicar permisos de consulta.
- [x] Incluir notificaciones internas basicas asociadas a alertas cuando corresponda.

## Archivos Involucrados

- `app/Models/AuditoriaLog.php`
- `app/Models/AlertaSistema.php`
- `app/Models/NotificacionSistema.php`
- `routes/api.php`
- `app/Http/Controllers/`

## Notas

- Priorizar observabilidad interna antes que notificaciones complejas.
- No implementar canales externos complejos si no son necesarios para el MVP.
- Pensar este modulo como soporte transversal a corridas, validaciones y administracion.
- La primera implementacion prioriza lectura y generacion automatica desde acciones internas, no una bandeja operativa completa.
