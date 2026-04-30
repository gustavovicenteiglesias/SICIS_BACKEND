# Contexto Del Proyecto

## Proyecto

SICIS Backend - API para el Sistema de Indicadores de Ciudad Inteligente y Sostenible de la Municipalidad de Lujan.

El sistema administra el nucleo de indicadores, datos fuente, validaciones, corridas, snapshots historicos, auditoria, roles, usuarios, integraciones simples y datos disponibles para consumo externo.

## Stack Principal

- Backend: PHP 8.2+ / Laravel 12
- API auth: Laravel Sanctum
- Base de datos: MySQL o MariaDB
- Frontend: fuera de este repositorio
- UI/portal publico: fuera de este repositorio

## Estado Actual

- Proyecto Laravel inicializado como backend API.
- Frontend del starter eliminado.
- Migrations principales creadas desde el SQL base SICIS.
- Modelos Eloquent creados para las entidades principales.
- Auth inicial con usuario administrador y roles.
- README convertido en guia de puesta en marcha backend.
- Guia de trabajo con IA creada para el equipo humano.
- Contratos iniciales de auth documentados.
- Contrato propuesto para ABM de catalogos base documentado.
- Seeders de catalogos base agregados.
- `php artisan migrate:fresh --seed` validado correctamente por el usuario en su servidor de pruebas.
- Primer bloque de ABM de catalogos base implementado bajo `/api/catalogos`.
- Middleware de permisos por codigo implementado para proteger lectura y escritura.
- Catalogos secundarios implementados bajo `/api/catalogos`, incluyendo areas, fuentes, normas, tipos y jurisdicciones.
- Contrato inicial del modulo indicadores documentado.
- ABM de indicadores implementado bajo `/api/indicadores`, incluyendo indicadores, versiones metodologicas y variables.
- Contrato inicial del modulo datos fuente documentado, cubriendo catalogo, valores, evidencias y conectores API.
- Flujo manual de datos fuente implementado bajo `/api/datos-fuente`, incluyendo catalogo, valores, validacion y evidencias.
- Conectores API de datos fuente implementados con CRUD de configuraciones y paths, prueba tecnica, importacion y trazabilidad de intentos.
- Contrato inicial del modulo de seguridad interna documentado, cubriendo usuarios, roles, asignaciones y permisos efectivos.
- Modulo de seguridad interna implementado bajo `/api/seguridad`, con CRUD de usuarios y roles, asignaciones y permisos efectivos.
- Contrato inicial del modulo de corridas documentado, cubriendo creacion, ejecucion, aprobacion, publicacion y snapshots.
- Circuito minimo de corridas implementado bajo `/api/corridas`, con persistencia de snapshots de datos usados y resultados de indicadores.
- Modulo minimo de observabilidad implementado bajo `/api/observabilidad`, con auditoria, alertas y notificaciones internas de consulta.
- Bloque de consultas externas implementado bajo `/api/externo`, con JSON paginado y exportacion CSV para indicadores vigentes, resultados publicos y corridas publicadas.
- Suite minima de smoke tests agregada para auth, proteccion, permisos y catalogos base.
- Cobertura de integracion ampliada con suites para indicadores, versiones, variables y permisos diferenciados de datos fuente.
- Documentacion consumible por frontend y QA consolidada con indice de contratos, guia corta de Postman y coleccion actualizada.
- Capa transversal de errores y observabilidad endurecida con `request_id`, respuestas de error estandarizadas y logging estructurado de fallos no esperados.
- Cierre tecnico del MVP documentado con checklist de release, riesgos abiertos y resumen ejecutivo de estado.
- Backlog ampliado y ordenado en `ai/tasks/ROADMAP.md` para permitir retomar el proyecto sin depender del contexto conversacional previo.

## Objetivo MVP Backend

El backend debe soportar:

- Autenticacion interna.
- Usuarios y roles.
- Catalogos de categorias, tematicas, unidades, periodicidades y estados.
- Indicadores y versionado metodologico.
- Datos fuente y valores.
- Carga manual de datos.
- Consumo simple de APIs externas bajo demanda.
- Validacion de datos y estados.
- Corridas de calculo.
- Snapshots historicos.
- Aprobacion y publicacion de corridas.
- Auditoria y trazabilidad.
- Exportaciones y datos consumibles por herramientas externas.

## Fuera De Alcance De Este Repositorio

- React.
- Pantallas.
- Estilos.
- Dashboard visual.
- Portal publico visual.
- Componentes UI.
- Maquetacion.

Este backend puede proveer endpoints para esas necesidades, pero no implementa la interfaz.
