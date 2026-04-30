# Contratos API - Observabilidad Interna

Este documento define el contrato minimo del modulo de observabilidad del backend SICIS. Cubre auditoria, alertas y notificaciones internas asociadas a eventos relevantes del sistema.

## Alcance

Este bloque cubre:

1. `auditoria_logs`
2. `alertas_sistema`
3. `notificaciones_sistema`

Quedan fuera de este documento:

1. envio real por email, SMS o canales externos
2. reglas avanzadas de correlacion o monitoreo tecnico
3. dashboards visuales

## Permisos Propuestos

- Consulta de auditoria, alertas y notificaciones: `auditoria.ver`

## Convenciones Generales

- Todas las rutas viven bajo `/api/observabilidad`.
- Todas las respuestas son JSON.
- Los listados son paginados con `per_page`.
- Todas las operaciones requieren autenticacion Sanctum.
- Esta primera version expone lectura. La escritura se genera desde acciones del sistema.

## 1. Auditoria

### Rutas

```http
GET /api/observabilidad/auditoria
GET /api/observabilidad/auditoria/{log}
```

### Filtros

- `usuario_id`
- `tabla_afectada`
- `registro_id`
- `accion`
- `fecha_desde`
- `fecha_hasta`
- `per_page`

### Eventos Auditados En MVP

1. alta, modificacion y baja de usuarios
2. alta, modificacion y baja de roles
3. asignacion y quita de roles de usuario
4. asignacion y quita de permisos de rol
5. alta, modificacion, validacion y baja de valores de datos fuente
6. creacion, modificacion, ejecucion, aprobacion y publicacion de corridas
7. error de ejecucion de corrida

## 2. Alertas

### Rutas

```http
GET /api/observabilidad/alertas
GET /api/observabilidad/alertas/{alerta}
```

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

### Reglas Minimas De Generacion

1. Cuando un valor de dato fuente queda `OBSERVADO`, generar alerta de severidad `MEDIA`.
2. Cuando un valor de dato fuente queda `RECHAZADO`, generar alerta de severidad `ALTA`.
3. Cuando una corrida falla y queda `OBSERVADA`, generar alerta de severidad `ALTA`.
4. Cuando una corrida finaliza con resultados `SIN_DATOS` o `ERROR_CALCULO`, generar alerta de severidad `MEDIA`.

## 3. Notificaciones Internas

### Rutas

```http
GET /api/observabilidad/notificaciones
GET /api/observabilidad/notificaciones/{notificacion}
```

### Filtros

- `estado`
- `canal`
- `usuario_id`
- `alerta_id`
- `fecha_desde`
- `fecha_hasta`
- `per_page`

### Convencion MVP

1. Todas las notificaciones generadas por este modulo usan `canal = INTERNA`.
2. Se crean como soporte operativo asociado a alertas.
3. No implican envio automatico externo en esta etapa.

## Ejemplos

### Consulta de auditoria

```http
GET /api/observabilidad/auditoria?tabla_afectada=corridas&accion=EJECUTAR
```

### Consulta de alertas pendientes

```http
GET /api/observabilidad/alertas?estado=PENDIENTE
```

### Consulta de notificaciones de un usuario

```http
GET /api/observabilidad/notificaciones?usuario_id=1
```
