# Guía Para Trabajar Con IA En Este Backend

Este documento es para el equipo humano. `AGENTS.md` es para los agentes de IA.

La idea es que cualquier persona del equipo pueda usar Codex, Cursor, Claude, Windsurf u otra herramienta sin que cada sesion arranque desde cero ni invente reglas nuevas.

## Regla Principal

Antes de pedir cambios de código a una IA, indicarle que lea y respete:

AGENTS.md

Mensaje recomendado:

Lee AGENTS.md en la raiz del proyecto y obedece sus instrucciones antes de proponer cambios.

## Que Debe Hacer La IA

El agente debe leer:

1. `ai/workflow.md`  
2. `ai/context.md`  
3. `ai/decisions.md`  
4. `ai/project-map.md`

Después de eso puede diagnosticar, proponer o implementar segun la tarea.

## Que No Debe Hacer La IA

- No debe crear frontend en este repo.  
- No debe agregar Vite, React, Tailwind, vistas Blade ni componentes UI.  
- No debe escanear todo el proyecto sin necesidad.  
- No debe modificar la arquitectura documentada sin avisar.  
- No debe reescribir archivos completos si alcanza con cambios puntuales.  
- No debe agregar dependencias sin justificar.

## Cómo Pedir Una Tarea

Formato recomendado:

Lee AGENTS.md. Quiero trabajar sobre [objetivo].

Primero dame diagnóstico, faltante, impacto y plan.

No escribas código hasta que confirme.

Ejemplo:

Lee AGENTS.md. Quiero definir los endpoints de ABM de categorías.

Primero dame diagnóstico, faltante, impacto y plan.

No escribas código hasta que confirme.

## Cómo Usar Las Tareas

Las tareas viven en:

ai/tasks/

Estados:

- `TODO-...md`: pendiente.  
- `DOING-...md`: en curso.  
- `BLOCKED-...md`: bloqueada.  
- `DONE-...md`: terminada.

Cuando una IA empieza una tarea, debería renombrarla de `TODO` a `DOING`. Cuando termina, debería pasarla a `DONE` y marcar los criterios de aceptación.

## Cuando Crear Una Tarea Nueva

Crear una tarea nueva si:

- El cambio toca más de un archivo importante.  
- Hay decisiones de contrato API.  
- Hay riesgo de romper migrations, auth o permisos.  
- El trabajo depende de la validación del equipo.  
- Conviene dejar trazabilidad para otro dev.

Usar como base:

ai/tasks/template.md

## Decisión De Alcance

Este repositorio es solo backend API.

El equipo backend se ocupa de:

- Datos.  
- Auth.  
- Roles/permisos.  
- Reglas de negocio.  
- Endpoints.  
- Validaciones.  
- Corridas.  
- Snapshots.  
- Auditoría.  
- Exportaciones.  
- Contratos para que el frontend consuma.

El equipo frontend u otro repositorio se ocupa de:

- React.  
- Pantallas.  
- Estilos.  
- Portal publico visual.  
- Dashboard visual.  
- Componentes UI.

Si una tarea visual llega a este repo, se traduce a contrato API o se deriva al repo correspondiente.

## Checklist Antes De Mergear

Antes de cerrar una tarea backend:

- [ ] La IA leyó `AGENTS.md`.  
- [ ] El cambio respeta `ai/decisions.md`.  
- [ ] No se agregó frontend.  
- [ ] Se actualizaron docs/tareas si cambió el contexto.  
- [ ] Se corrió o se intentó correr la validación correspondiente.  
- [ ] El README sigue siendo coherente con la forma real de levantar el proyecto.
