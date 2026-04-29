# Workflow De Trabajo Con IA

## Filosofia

- Primero entender, despues tocar.
- Diagnostico antes de codigo.
- Cambios minimos, modulares y reversibles.
- Backend API solamente.
- Cada cambio debe poder explicarse por impacto en el MVP.

## Protocolo Antes De Codificar

Responder con:

1. Diagnostico: estado actual.
2. Faltante: que falta para cumplir la tarea.
3. Impacto: archivos a tocar.
4. Plan: cambios propuestos.

Si el usuario confirma o pide avanzar, implementar.

## Gestion De Tareas

Las tareas viven en `ai/tasks/`.

Estados:

- `TODO-...md`: pendiente.
- `DOING-...md`: en curso.
- `BLOCKED-...md`: bloqueada.
- `DONE-...md`: terminada.

Al terminar un hito importante:

- Actualizar `ai/context.md` si cambio el estado del proyecto.
- Crear o actualizar una tarea en `ai/tasks/`.
- Mantener la documentacion corta y util.

## Validacion

Cuando sea posible:

- Ejecutar `php artisan migrate:fresh --seed`.
- Ejecutar `php artisan test`.
- Revisar `php artisan route:list` para endpoints.

Si no se puede ejecutar algo, documentar el motivo.
