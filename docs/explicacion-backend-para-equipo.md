# Explicación Del Backend SICIS Para El Equipo

Este documento explica cómo pensar y recorrer el backend SICIS si ya trabajás en sistemas o desarrollo, pero no tenés mucha experiencia con PHP o Laravel.

La idea no es enseñar Laravel completo. La idea es que puedas entender este proyecto, ubicarte en el código y seguir el flujo funcional del sistema sin perderte.

## 1. Qué Resuelve Este Proyecto

SICIS es un sistema para administrar indicadores de ciudad.

Este backend se encarga de:

- autenticar usuarios  
- aplicar permisos  
- administrar catálogos del sistema  
- definir indicadores y sus versiones metodológicas  
- registrar datos fuente  
- validar datos  
- ejecutar corridas de cálculo  
- guardar snapshots históricos  
- aprobar y publicar resultados  
- dejar auditoría y alertas  
- exponer datos para consumo externo o BI

Este repo no hace frontend.

No tiene pantallas ni portal visual. Todo lo que vive acá son reglas, datos, endpoints API y trazabilidad.

## 2. Cómo Pensar El Proyecto Sin Saber Laravel

Si venís de otros stacks, una forma simple de traducir Laravel es esta:

- `routes/api.php`  
    
  - define las rutas HTTP disponibles  
  - es la puerta de entrada de la API


- `app/Http/Controllers`  
    
  - contiene los controladores  
  - un controller recibe la request, valida, coordina reglas y devuelve JSON


- `app/Models`  
    
  - contiene los modelos Eloquent  
  - un modelo representa una tabla o una vista de base  
  - también define relaciones entre entidades


- `database/migrations`  
    
  - define el esquema de base de datos en código  
  - en este proyecto, las migrations son la fuente de verdad


- `database/seeders`  
    
  - cargan datos iniciales  
  - por ejemplo roles, permisos, estados, categorías base y usuario admin


- `tests`  
    
  - contiene pruebas automatizadas  
  - hoy hay smoke tests y algunos tests de integración/permisos

## 3. Estructura Mental Del Negocio

Una forma útil de entender SICIS es pensar el dominio en capas.

### Capa 1. Seguridad

Primero existe la seguridad interna:

- usuarios  
- roles  
- permisos

Sin eso no hay operación del backoffice.

### Capa 2. Catálogos

Después vienen los catálogos que ordenan el sistema:

- categorías  
- categorías temáticas  
- unidades de medida  
- periodicidades  
- estados  
- modalidades  
- áreas municipales  
- normas  
- tipos  
- jurisdicciones

Estos catálogos son la base para cargar indicadores y datos.

### Capa 3. Indicadores

Un indicador no es solo un nombre.

En este backend un indicador tiene:

- una definición general  
- una o varias versiones metodológicas  
- variables asociadas a cada versión

Eso permite que el método de cálculo cambie en el tiempo sin perder la historia.

### Capa 4. Datos Fuente

Los indicadores se alimentan con datos fuente.

Cada dato fuente tiene:

- una definición base  
- valores cargados  
- estado de validación  
- evidencias  
- eventualmente una configuración para importar desde una API externa

### Capa 5. Corridas

La corrida es el momento donde el sistema toma datos validados y calcula resultados.

Una corrida:

- se hace para una jurisdicción y un periodo  
- usa versiones activas de indicadores  
- toma datos fuente validados  
- guarda snapshots de los datos usados  
- guarda snapshots de resultados  
- puede aprobarse y publicarse

### Capa 6. Observabilidad

Además del negocio, el sistema deja:

- auditoría  
- alertas  
- notificaciones internas

Eso ayuda a operar y diagnosticar.

### Capa 7. Salidas Externas

Finalmente, el backend expone consultas simples para terceros:

- indicadores vigentes  
- resultados públicos  
- corridas publicadas

## 4. Cómo Viaja Una Request

Ejemplo:

`POST /api/datos-fuente/{datoFuente}/valores/{valor}/validar`

El recorrido mental es:

1. La ruta vive en `routes/api.php`  
2. La ruta pasa por middleware  
3. El middleware chequea autenticación y permiso  
4. Entra al controller correspondiente  
5. El controller valida el body  
6. El controller busca el modelo y aplica reglas de negocio  
7. El modelo persiste cambios en base  
8. El controller devuelve JSON  
9. Si corresponde, también genera auditoría o alertas

Eso se repite bastante en casi todo el proyecto.

## 5. Dónde Está Cada Módulo

### Auth

- `app/Http/Controllers/AuthController.php`  
- rutas en `routes/api.php`

Hace login por `nombre_usuario`, devuelve token Sanctum y expone perfil.

### Catálogos

- `app/Http/Controllers/Catalogos/`

Muchos catálogos reutilizan un controller base:

- `BaseCatalogController.php`

Eso evita repetir CRUD simple.

### Indicadores

- `app/Http/Controllers/Indicadores/`

Acá se administra:

- indicador  
- versión metodológica  
- variable

### Datos Fuente

- `app/Http/Controllers/DatosFuente/`

Acá viven:

- catálogo de datos fuente  
- valores  
- validación  
- evidencias  
- conectores API

### Corridas

- `app/Http/Controllers/Corridas/CorridaController.php`

Es uno de los puntos más importantes del sistema porque une indicadores con datos validados y genera resultados.

### Observabilidad

- `app/Http/Controllers/Observabilidad/`  
- `app/Support/Observability/Observability.php`

La parte de soporte transversal vive en `app/Support`.

### Seguridad Interna

- `app/Http/Controllers/Seguridad/`

Administra usuarios, roles, permisos efectivos y asignaciones.

### Consultas Externas

- `app/Http/Controllers/Externo/ConsultaExternaController.php`

Expone JSON y CSV para consumo externo.

## 6. Cómo Se Organiza La Base

La base no se piensa desde SQL suelto sino desde migrations.

Archivos clave:

- `2026_04_28_215107_create_usuarios_table.php`  
- `2026_04_28_215108_create_sicis_catalog_tables.php`  
- `2026_04_28_215109_create_sicis_core_tables.php`  
- `2026_04_28_215110_create_sicis_operations_tables.php`  
- `2026_04_28_215111_create_sicis_views.php`

Una lectura simple sería:

- `catalog tables` = catálogos base  
- `core tables` = indicadores y datos fuente  
- `operations tables` = corridas, alertas, auditoría, integraciones  
- `views` = vistas para consulta externa

## 7. Cómo Funciona La Seguridad

Hay dos conceptos separados:

### Autenticación

Responde a:

"quién sos"

Se resuelve con login y token Sanctum.

### Autorización

Responde a:

"qué podés hacer"

Se resuelve con permisos.

Ejemplos:

- `indicadores.ver`  
- `indicadores.configurar`  
- `datos_fuente.cargar`  
- `datos_fuente.validar`  
- `corridas.ejecutar`  
- `corridas.aprobar`  
- `resultados.publicar`

El middleware `CheckPermission` chequea eso antes de entrar al controller.

## 8. Qué Es Una Corrida En Términos Simples

Si alguien del equipo pregunta "qué hace una corrida", una forma corta de explicarlo es esta:

Una corrida toma:

- una jurisdicción  
- un periodo  
- indicadores activos  
- la versión vigente de cada indicador  
- datos fuente validados para ese contexto

Y produce:

- resultados calculados  
- snapshot de datos usados  
- snapshot de resultados

Después esos resultados pueden:

- quedar ejecutados  
- aprobarse  
- publicarse

## 9. Qué Son Los Snapshots

Un snapshot es una foto histórica del momento del cálculo.

Se guarda para que más adelante puedas saber:

- qué datos exactos se usaron  
- qué fórmula/versión aplicaba  
- qué resultado se obtuvo

Eso evita que un cambio posterior en los datos o en la metodología borre la trazabilidad histórica.

## 10. Cómo Está Pensada La Observabilidad

El proyecto tiene dos niveles de observabilidad:

### Nivel funcional

- auditoría  
- alertas  
- notificaciones internas

Sirve para seguir acciones del negocio.

### Nivel técnico

- respuestas de error estandarizadas  
- `request_id`  
- logging estructurado

Sirve para diagnosticar problemas de API y soporte.

## 11. Cómo Empezar A Leer El Código

Si alguien entra nuevo al proyecto, este es un orden razonable:

1. `README.md`  
2. `docs/indice-contratos-api.md`  
3. `routes/api.php`  
4. `app/Http/Controllers/AuthController.php`  
5. `app/Http/Middleware/CheckPermission.php`  
6. `app/Models/Usuario.php`  
7. controllers por módulo:  
   - `Catálogos`  
   - `Indicadores`  
   - `DatosFuente`  
   - `Corridas`  
   - `Observabilidad`  
   - `Seguridad`  
   - `Externo`  
8. migrations principales  
9. tests feature

Ese recorrido da una imagen bastante buena sin tener que leer todo el repo de golpe.

## 12. Cómo Pensar Los Tests

Hoy los tests sirven más como validación de flujos clave que como cobertura total.

Hay tres bloques importantes:

- smoke tests de auth y catálogos  
- integración de indicadores  
- permisos y validación de datos fuente  
- consistencia de errores

Archivos útiles:

- `tests/Feature/AuthAndCatalogSmokeTest.php`  
- `tests/Feature/IndicadoresIntegrationTest.php`  
- `tests/Feature/DatosFuentePermissionsTest.php`  
- `tests/Feature/ErrorHandlingConsistencyTest.php`

Si querés entender el comportamiento esperado del sistema, leer esos tests ayuda bastante.

## 13. Glosario Rápido

### Indicador

Definición conceptual de una medición del sistema.

### Versión metodológica

Versión concreta de cómo se calcula un indicador en cierto período.

### Variable

Dato fuente que participa en la formula de una versión.

### Dato fuente

Fuente operativa de información que alimenta indicadores.

### Valor de dato fuente

Medición concreta cargada para una jurisdicción y un período.

### Corrida

Proceso de cálculo de resultados para un período/jurisdicción.

### Snapshot

Foto histórica de datos o resultados usados en una corrida.

### Publicable

Marca que indica si algo puede exponerse hacia consumos externos.

### Sensible

Marca que indica que el dato no debería exponerse públicamente.

## 14. Idea Final Para El Equipo

La forma más sana de pensar este proyecto no es "un proyecto PHP", sino:

"un backend de dominio bastante ordenado, montado sobre Laravel"

Si ya entendés:

- API REST  
- autenticación  
- permisos  
- entidades y relaciones  
- validación  
- procesos batch o de cálculo  
- trazabilidad

entonces ya entendés gran parte del sistema.

Laravel en este repo es, sobre todo, la estructura que organiza esas piezas.  