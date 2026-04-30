# Contratos API \- Exportaciones Y Consultas Externas

Este documento define el contrato mínimo del bloque de salidas consumibles por terceros para el MVP SICIS. Cubre consultas JSON y exportaciones CSV listas para integración externa o consumo desde herramientas BI.

## Alcance

Este bloque cubre:

1. indicadores vigentes  
2. resultados públicos de corridas publicadas  
3. listado de corridas publicadas

Quedan fuera de este documento:

1. cubos analíticos complejos  
2. reporting paramétrico avanzado  
3. acceso anonimo publico

## Convenciones Generales

- Todas las rutas viven bajo `/api/externo`.  
- Todas las operaciones requieren autenticación Sanctum.  
- La consulta reutiliza el permiso `indicadores.ver`.  
- El formato por defecto es JSON paginado.  
- Si `format=csv`, la respuesta se exporta completa en CSV.  
- Solo se exponen datos aptos para integración externa dentro del alcance MVP.

## Vistas Reutilizadas

1. `vw_indicadores_vigentes`  
2. `vw_resultados_publicos`

## 1. Indicadores Vigentes

### Ruta

GET /api/externo/indicadores-vigentes

### Filtros

- `codigo_interno`  
- `categoria`  
- `categoria_tematica`  
- `tipo_indicador`  
- `periodicidad`  
- `publicable`  
- `sensible`  
- `per_page`  
- `format=csv`

## 2. Resultados Públicos

### Ruta

GET /api/externo/resultados-publicos

### Filtros

- `jurisdiccion`  
- `codigo_interno`  
- `indicador`  
- `categoria`  
- `periodo_desde`  
- `periodo_hasta`  
- `publicada_desde`  
- `publicada_hasta`  
- `per_page`  
- `format=csv`

### Restricciones

La vista solo devuelve resultados:

1. asociados a corridas `PUBLICADA`  
2. con `publicada_at` no nulo  
3. de indicadores `publicable = true`  
4. con `publicable_en_corrida = true`  
5. de indicadores `sensible = false`

## 3. Corridas Publicadas

### Ruta

GET /api/externo/corridas-publicadas

### Filtros

- `jurisdiccion_id`  
- `titulo`  
- `periodo_desde`  
- `periodo_hasta`  
- `publicada_desde`  
- `publicada_hasta`  
- `per_page`  
- `format=csv`

### Campo Operativo Relevante

- `resultados_publicables_count`: cantidad de resultados asociados marcados como publicables en la corrida

## Ejemplos

### JSON de resultados públicos

GET /api/externo/resultados-publicos?jurisdiccion=Lujan\&periodo\_desde=2026-01-01\&periodo\_hasta=2026-12-31

### CSV de indicadores vigentes

GET /api/externo/indicadores-vigentes?publicable=true\&format=csv

### CSV de corridas publicadas

GET /api/externo/corridas-publicadas?publicada\_desde=2026-01-01\&format=csv  