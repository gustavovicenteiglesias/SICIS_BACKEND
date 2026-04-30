# Guia Corta De Postman

Esta guia propone un orden practico para probar el backend SICIS sin perderse entre modulos.

## Variables De Coleccion Recomendadas

- `base_url`
- `token`
- `categoria_id`
- `indicador_id`
- `indicador_version_id`
- `dato_fuente_id`
- `dato_fuente_valor_id`
- `corrida_id`
- `usuario_id`
- `rol_id`

## Orden Recomendado De Prueba

1. `Auth`
   - `Login`
   - `Perfil`

2. `Catalogos`
   - `Categorias / Listar`
   - `Categorias / Crear`

3. `Indicadores`
   - `Crear indicador`
   - `Crear version`
   - `Crear variable`

4. `Datos Fuente`
   - `Crear dato fuente`
   - `Cargar valor`
   - `Validar valor`

5. `Corridas`
   - `Crear corrida`
   - `Ejecutar corrida`
   - `Aprobar corrida`
   - `Publicar corrida`

6. `Observabilidad`
   - `Auditoria`
   - `Alertas`
   - `Notificaciones`

7. `Externo`
   - `Indicadores vigentes`
   - `Resultados publicos`
   - `Corridas publicadas`

## Sugerencia Operativa

- Ejecutar siempre `Login` primero para refrescar `token`.
- Guardar IDs importantes desde la pestaña `Tests` de Postman.
- Si una request devuelve `403`, revisar el permiso asociado al modulo antes de asumir un error de codigo.
- Si una request devuelve `422`, revisar el contrato del modulo y los IDs referenciados.

## Documentos Para Tener Abiertos

- [Indice de contratos](./indice-contratos-api.md)
- [Contratos de catalogos](./contratos-api-catalogos.md)
- [Contratos de indicadores](./contratos-api-indicadores.md)
- [Contratos de datos fuente](./contratos-api-datos-fuente.md)
- [Contratos de corridas](./contratos-api-corridas.md)
- [Contratos de observabilidad](./contratos-api-observabilidad.md)
- [Contratos de exportaciones](./contratos-api-exportaciones.md)
