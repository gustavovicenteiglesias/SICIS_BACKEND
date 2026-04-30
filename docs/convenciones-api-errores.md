# Convenciones API - Errores Y Observabilidad

Este documento resume las convenciones técnicas de errores y trazabilidad adoptadas en el backend SICIS para mejorar operación, diagnóstico y consistencia entre módulos.

## Objetivo

Unificar la forma en que la API responde ante errores comunes y dejar una referencia simple para desarrollo, QA e integraciones.

## Cabecera Operativa

Todas las respuestas del backend incluyen:

- `X-Request-Id`

Ese valor sirve para correlacionar un error devuelto al cliente con el registro correspondiente en logs.

## Forma Base De Error

Las respuestas de error siguen esta estructura:

```json
{
  "ok": false,
  "message": "Mensaje operativo",
  "code": "CODIGO_ESTABLE",
  "errors": {
    "campo": [
      "Detalle de validación"
    ]
  },
  "request_id": "uuid",
  "path": "api/ruta"
}
```

Notas:

1. `errors` solo aparece cuando aplica, principalmente en `422`.
2. `request_id` siempre debe preservarse para soporte técnico.
3. `path` ayuda a diagnóstico rápido en logs y QA.

## Códigos Y Estados

### 401

- `code`: `AUTH_REQUIRED`
- mensaje: autenticación faltante o inválida

### 403

- `code`: `FORBIDDEN`
- mensaje: permiso insuficiente

### 404

- `code`: `NOT_FOUND`
- mensaje: recurso inexistente

### 422

- `code`: `VALIDATION_ERROR`
- mensaje: validación fallida o restricción de negocio expuesta como validación

### 500

- `code`: `INTERNAL_ERROR`
- mensaje: error interno inesperado

## Criterios Prácticos

1. Las validaciones de negocio recuperables deben priorizar `422`.
2. Los problemas de permiso deben priorizar `403`.
3. Los errores no esperados no deben devolver trazas ni detalles sensibles al cliente.
4. Los detalles técnicos completos deben quedar en logs y asociarse por `request_id`.

## Logging Transversal

Se registra log estructurado para excepciones no esperadas con:

- `request_id`
- `method`
- `path`
- `usuario_id`
- clase de excepción
- mensaje

## Observaciones Por Módulo

1. Corridas mantiene auditoría y alertas de negocio propias.
2. Datos fuente mantiene trazabilidad específica de importaciones API.
3. La capa global de errores no reemplaza la auditoría funcional; la complementa.
