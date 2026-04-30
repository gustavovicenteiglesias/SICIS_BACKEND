# Contratos API \- Observabilidad Interna

Este documento define el contrato mínimo del módulo de observabilidad del backend SICIS. Cubre auditorías, alertas y notificaciones internas asociadas a eventos relevantes del sistema.

## Alcance

Este bloque cubre:

1. `auditoria_logs`  
2. `alertas_sistema`  
3. `notificaciones_sistema`

Quedan fuera de este documento:

1. envio real por email, SMS o canales externos  
2. reglas avanzadas de correlación o monitoreo técnico  
3. dashboards visuales

## Permisos Propuestos

- Consulta de auditoría, alertas y notificaciones: `auditoria.ver`

## Convenciones Generales

- Todas las rutas viven bajo `/api/observabilidad`.  
- Todas las respuestas son JSON.  
- Los listados son paginados con `per_page`.  
- Todas las operaciones requieren autenticación Sanctum.  
- Esta primera versión expone lectura. La escritura se genera desde acciones del sistema.

## 1. Auditoría

### Rutas

GET /api/observabilidad/auditoria

GET /api/observabilidad/auditoria/{log}

### Filtros

- `usuario_id`  
- `tabla_afectada`  
- `registro_id`  
- `accion`  
- `fecha_desde`  
- `fecha_hasta`  
- `per_page`

### Eventos Auditados En MVP

1. alta, modificación y baja de usuarios  
2. alta, modificación y baja de roles  
3. asignación y quita de roles de usuario  
4. asignación y quita de permisos de rol  
5. alta, modificación, validación y baja de valores de datos fuente  
6. creación, modificación, ejecución, aprobación y publicación de corridas  
7. error de ejecución de corrida

## 2. Alertas

### Rutas

GET /api/observabilidad/alertas

GET /api/observabilidad/alertas/{alerta}

### Filtros

- `estado`  
- `tipo_alerta`  
- `severidad`  
- `usuario_asignado_id`  
- `entidad_tipo`  
- `entidad_id`  
- `resuelta`  
- `fecha_desde`  
- `fecha_hasta`  
- `per_page`

### Reglas Mínimas De Generación

1. Cuando un valor de dato fuente queda `OBSERVADO`, generar alerta de severidad `MEDIA`.  
2. Cuando un valor de dato fuente queda `RECHAZADO`, generar alerta de severidad `ALTA`.  
3. Cuando una corrida falla y queda `OBSERVADA`, generar alerta de severidad `ALTA`.  
4. Cuando una corrida finaliza con resultados `SIN_DATOS` o `ERROR_CALCULO`, generar alerta de severidad `MEDIA`.

## 3. Notificaciones Internas

### Rutas

GET /api/observabilidad/notificaciones

GET /api/observabilidad/notificaciones/{notificacion}

### Filtros

- `estado`  
- `canal`  
- `usuario_id`  
- `alerta_id`  
- `fecha_desde`  
- `fecha_hasta`  
- `per_page`

### Convención MVP

1. Todas las notificaciones generadas por este módulo usan `canal = INTERNA`.  
2. Se crean como soporte operativo asociado a alertas.  
3. No implican envio automático externo en esta etapa.

## Ejemplos

### Consulta de auditoría

GET /api/observabilidad/auditoria?tabla\_afectada=corridas\&accion=EJECUTAR

### Consulta de alertas pendientes

GET /api/observabilidad/alertas?estado=PENDIENTE

### Consulta de notificaciones de un usuario

GET /api/observabilidad/notificaciones?usuario\_id=1  