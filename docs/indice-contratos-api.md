# Indice De Contratos API

Este documento resume los modulos disponibles del backend SICIS y apunta al contrato detallado de cada bloque. Sirve como mapa rapido para frontend, QA o integraciones.

## Modulos Disponibles

| Modulo | Prefijo | Documento |
|---|---|---|
| Auth | `/api` | `README.md` |
| Catalogos | `/api/catalogos` | [contratos-api-catalogos.md](./contratos-api-catalogos.md) |
| Indicadores | `/api/indicadores` | [contratos-api-indicadores.md](./contratos-api-indicadores.md) |
| Datos fuente | `/api/datos-fuente` | [contratos-api-datos-fuente.md](./contratos-api-datos-fuente.md) |
| Corridas | `/api/corridas` | [contratos-api-corridas.md](./contratos-api-corridas.md) |
| Observabilidad | `/api/observabilidad` | [contratos-api-observabilidad.md](./contratos-api-observabilidad.md) |
| Seguridad interna | `/api/seguridad` | [contratos-api-usuarios-roles.md](./contratos-api-usuarios-roles.md) |
| Consultas externas | `/api/externo` | [contratos-api-exportaciones.md](./contratos-api-exportaciones.md) |

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

### 2. Alta De Categoria

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

### 4. Validacion De Dato Fuente

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

### 5. Ejecucion De Corrida

```http
POST /api/corridas/{corrida}/ejecutar
Authorization: Bearer <token>
Content-Type: application/json
```

```json
{
  "observaciones": "Ejecucion inicial"
}
```

### 6. Resultados Publicos

```http
GET /api/externo/resultados-publicos?periodo_desde=2026-01-01&periodo_hasta=2026-12-31
Authorization: Bearer <token>
```

Si se necesita exportacion:

```http
GET /api/externo/resultados-publicos?format=csv
Authorization: Bearer <token>
```

## Regla Practica

Para detalle de payloads, validaciones, filtros y reglas de negocio, usar siempre el contrato del modulo correspondiente. Este indice es una puerta de entrada, no reemplaza esos documentos.
