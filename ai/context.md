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
- Backlog inicial extendido para catalogos secundarios, indicadores, datos fuente, corridas, auditoria, pruebas y documentacion.

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
