# AGENTS.md - Protocolo de Asistencia IA

## Proposito

Este repositorio contiene exclusivamente el backend API de SICIS Lujan.

El frontend, portal publico, pantallas, estilos, dashboards visuales y experiencia de usuario viven en otro repositorio/equipo. En este proyecto solo se implementan responsabilidades de API, datos, autenticacion, reglas de negocio, auditoria, exportaciones y soporte para integraciones.

## Regla Cero

No escanear todo el proyecto al iniciar una tarea. Primero leer la memoria del proyecto en `/ai`.

## Secuencia De Lectura

Antes de proponer o modificar codigo, leer en este orden:

1. `ai/workflow.md`
2. `ai/context.md`
3. `ai/decisions.md`
4. `ai/project-map.md`

Luego trabajar sobre la tarea indicada por el usuario o, si no hay una tarea explicita, sugerir la siguiente tarea pendiente de `ai/tasks/`.

## Reglas De Trabajo

- Explicar diagnostico antes de escribir codigo.
- Priorizar cambios minimos.
- Mantener consistencia Laravel 12.
- No introducir frontend en este repositorio.
- No agregar dependencias sin justificar.
- No cambiar decisiones de arquitectura documentadas sin confirmacion.
- Si una tarea es ambigua, preguntar antes de implementar.
