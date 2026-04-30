# Índice De Contratos API

Este documento resume los módulos disponibles del backend SICIS y apunta al contrato detallado de cada bloque. Sirve como mapa rápido para frontend, QA o integraciones.

## Módulos Disponibles

| Módulo | Prefijo | Documento |
| --- | --- | --- |
| Auth | `/api` | `README.md` |
| Catálogos | `/api/catalogos` | [Contratos de catálogos](./contratos-api-catalogos.md) |
| Indicadores | `/api/indicadores` | [Contratos de indicadores](./contratos-api-indicadores.md) |
| Datos fuente | `/api/datos-fuente` | [Contratos de datos fuente](./contratos-api-datos-fuente.md) |
| Corridas | `/api/corridas` | [Contratos de corridas](./contratos-api-corridas.md) |
| Observabilidad | `/api/observabilidad` | [Contratos de observabilidad](./contratos-api-observabilidad.md) |
| Seguridad interna | `/api/seguridad` | [Contratos de seguridad interna](./contratos-api-usuarios-roles.md) |
| Consultas externas | `/api/externo` | [Contratos de exportaciones](./contratos-api-exportaciones.md) |

## Endpoints Clave

### 1. Login

```http
POST /api/login
Content-Type: application/json
```

```json
{
  "usuario": "admin",
  "password": "12345678"
}
```

Respuesta esperada:

```json
{
  "mensaje": "Login exitoso",
  "token": "plain-text-token",
  "usuario": {
    "id": 1,
    "nombre_usuario": "admin",
    "roles": [
      {
        "codigo": "ADMINISTRADOR_GENERAL"
      }
    ]
  }
}
```

### 2. Alta De Categoría

```http
POST /api/catalogos/categorias
Authorization: Bearer <token>
Content-Type: application/json
```

```json
{
  "nombre": "Tecnologia Civica",
  "descripcion": "Indicadores de innovacion y transformacion digital",
  "orden": 50,
  "activa": true
}
```

### 3. Alta De Indicador

```http
POST /api/indicadores
Authorization: Bearer <token>
Content-Type: application/json
```

```json
{
  "codigo_interno": "IND-001",
  "categoria_id": 1,
  "nombre": "Tasa de ejemplo",
  "descripcion": "Indicador de ejemplo",
  "publicable": true,
  "sensible": false,
  "activo": true,
  "orden": 100
}
```

### 4. Validación De Dato Fuente

```http
POST /api/datos-fuente/{datoFuente}/valores/{valor}/validar
Authorization: Bearer <token>
Content-Type: application/json
```

```json
{
  "estado_dato_id": 4,
  "valor_utilizado": 95,
  "vigente": true
}
```

### 5. Ejecución De Corrida

```http
POST /api/corridas/{corrida}/ejecutar
Authorization: Bearer <token>
Content-Type: application/json
```

```json
{
  "observaciones": "Ejecución inicial"
}
```

### 6. Resultados Públicos

```http
GET /api/externo/resultados-publicos?periodo_desde=2026-01-01&periodo_hasta=2026-12-31
Authorization: Bearer <token>
```

Si se necesita exportación:

```http
GET /api/externo/resultados-publicos?format=csv
Authorization: Bearer <token>
```

## Regla Práctica

Para detalle de payloads, validaciones, filtros y reglas de negocio, usar siempre el contrato del módulo correspondiente. Este índice es una puerta de entrada, no reemplaza esos documentos.
