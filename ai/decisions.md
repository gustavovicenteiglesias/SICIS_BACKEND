# Decisiones De Arquitectura

## Alcance

1. Este repositorio es solo backend API.
2. El frontend se desarrolla en otro proyecto.
3. Cualquier requerimiento visual debe traducirse a contrato API, no a componentes UI.

## Stack

1. Laravel 12.
2. PHP 8.2 o superior.
3. MySQL/MariaDB como base principal.
4. Sanctum para tokens de API.

## Base De Datos

1. Las migrations son la fuente de verdad del esquema.
2. El SQL original sirve como referencia funcional y estructural.
3. Evitar cambios manuales directos en base sin migration.
4. Usar nombres cortos explicitos para indices compuestos por limite de MySQL.

## Convenciones Backend

1. Modelos en `app/Models`.
2. Controllers en `app/Http/Controllers`.
3. Rutas API en `routes/api.php`.
4. Seeders en `database/seeders`.
5. No agregar dependencias salvo necesidad clara.
6. Mantener cambios pequenos y revisables.

## Auth

1. El login recibe `usuario` desde la API.
2. Internamente se busca contra `usuarios.nombre_usuario`.
3. Los roles se asignan por tabla pivot `usuarios_roles`.

## Frontend

1. No usar Vite en este repositorio.
2. No usar Tailwind en este repositorio.
3. No crear vistas Blade para producto.
4. `/` puede responder JSON simple de healthcheck.

## Catalogos API

1. Los catalogos del MVP se exponen bajo `/api/catalogos`.
2. El primer bloque prioriza categorias, categorias tematicas, unidades, periodicidades, estados y modalidades.
3. La lectura requiere autenticacion y permiso de consulta.
4. La escritura requiere permiso de configuracion de indicadores.
5. El contrato propuesto vive en `docs/contratos-api-catalogos.md`.

## Indicadores API

1. Los endpoints del modulo indicadores viven bajo `/api/indicadores`.
2. La lectura requiere `indicadores.ver`.
3. La escritura requiere `indicadores.configurar`.
4. Se separa el catalogo del indicador de sus versiones metodologicas y sus variables.
5. El contrato propuesto vive en `docs/contratos-api-indicadores.md`.
6. Las normas se asignan dentro del `POST/PUT` de indicadores mediante el arreglo `normas`.
7. Las variables se administran con endpoints propios anidados bajo la version metodologica.
8. No se permiten versiones activas superpuestas para un mismo indicador.

## Datos Fuente API

1. Los endpoints del modulo datos fuente viven bajo `/api/datos-fuente`.
2. Se separan permisos de consulta, configuracion, carga y validacion.
3. La validacion de valores se expone como accion explicita sobre el valor cargado.
4. Las evidencias se administran como recurso hijo de `datos_fuente_valores`.
5. Las configuraciones API se administran como recurso hijo de `datos_fuente`.
6. El contrato propuesto vive en `docs/contratos-api-datos-fuente.md`.
7. Los endpoints de configuracion y lectura de conectores API se protegen con `datos_fuente.configurar`.
8. La accion `importar` se protege con `datos_fuente.cargar`.
9. Si la API no devuelve jurisdiccion o periodo en un path usable, `importar` admite fallbacks por request.

## Seguridad Interna API

1. Los endpoints administrativos de usuarios, roles y permisos viven bajo `/api/seguridad`.
2. La gestion de usuarios requiere `usuarios.gestionar`.
3. La gestion de roles y permisos requiere `roles.gestionar`.
4. La asignacion de roles se resuelve mediante endpoints propios sobre `usuarios_roles`.
5. Los permisos efectivos de usuario se exponen como lectura diagnostica.
6. El contrato propuesto vive en `docs/contratos-api-usuarios-roles.md`.

## Corridas API

1. Los endpoints del modulo corridas viven bajo `/api/corridas`.
2. La lectura de corridas reutiliza `indicadores.ver`.
3. La creacion, edicion y ejecucion operativa requieren `corridas.ejecutar`.
4. La aprobacion requiere `corridas.aprobar`.
5. La publicacion requiere `resultados.publicar`.
6. La primera implementacion resuelve formulas simples, priorizando `RATIO_CONSTANTE` y casos de una variable.
7. La ejecucion usa solo valores `VALIDADO` y `vigente` para la `jurisdiccion` y el `periodo_referencia` de la corrida.
8. Cada corrida persiste snapshots de datos efectivamente usados y snapshots de resultados por indicador/version.
9. El contrato propuesto vive en `docs/contratos-api-corridas.md`.

## Observabilidad API

1. Los endpoints de observabilidad viven bajo `/api/observabilidad`.
2. La consulta de auditoria, alertas y notificaciones requiere `auditoria.ver`.
3. La escritura de auditoria no se expone por API; se genera desde acciones del sistema.
4. Las alertas del MVP se generan sobre observaciones o rechazos de datos fuente y sobre problemas operativos de corridas.
5. Las notificaciones del MVP son internas y se asocian a alertas, sin canales externos automaticos.
6. El contrato propuesto vive en `docs/contratos-api-observabilidad.md`.

## Consultas Externas Y Exportaciones API

1. Los endpoints de consultas externas viven bajo `/api/externo`.
2. La consulta reutiliza `indicadores.ver`.
3. El formato por defecto es JSON paginado.
4. Las exportaciones CSV se resuelven con `format=csv`.
5. Se reutilizan las vistas `vw_indicadores_vigentes` y `vw_resultados_publicos` cuando corresponde.
6. Solo se exponen resultados publicados, publicables y no sensibles para consumo externo.
7. El contrato propuesto vive en `docs/contratos-api-exportaciones.md`.

## Manejo De Errores Y Observabilidad Tecnica

1. La API expone errores estandarizados en JSON para `401`, `403`, `404`, `422` y `500`.
2. Todas las respuestas de error incluyen `request_id` y cabecera `X-Request-Id`.
3. Las validaciones de negocio recuperables priorizan `422`.
4. Los permisos insuficientes se exponen como `403` consistente mediante `AuthorizationException`.
5. Los errores internos no devuelven detalles sensibles al cliente.
6. Las excepciones no esperadas se registran con log estructurado incluyendo `request_id`, `path` y `usuario_id`.
7. La convencion operativa vive en `docs/convenciones-api-errores.md`.
