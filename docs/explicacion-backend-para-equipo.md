# Explicacion Del Backend SICIS Para El Equipo

Este documento explica como pensar y recorrer el backend SICIS si ya trabajas en sistemas o desarrollo, pero no tenes mucha experiencia con PHP o Laravel.

La idea no es enseñar Laravel completo. La idea es que puedas entender este proyecto, ubicarte en el codigo y seguir el flujo funcional del sistema sin perderte.

## 1. Que Resuelve Este Proyecto

SICIS es un sistema para administrar indicadores de ciudad.

Este backend se encarga de:

- autenticar usuarios
- aplicar permisos
- administrar catalogos del sistema
- definir indicadores y sus versiones metodologicas
- registrar datos fuente
- validar datos
- ejecutar corridas de calculo
- guardar snapshots historicos
- aprobar y publicar resultados
- dejar auditoria y alertas
- exponer datos para consumo externo o BI

Este repo no hace frontend.

No tiene pantallas ni portal visual. Todo lo que vive aca son reglas, datos, endpoints API y trazabilidad.

## 2. Como Pensar El Proyecto Sin Saber Laravel

Si venis de otros stacks, una forma simple de traducir Laravel es esta:

- `routes/api.php`
  - define las rutas HTTP disponibles
  - es la puerta de entrada de la API

- `app/Http/Controllers`
  - contiene los controllers
  - un controller recibe la request, valida, coordina reglas y devuelve JSON

- `app/Models`
  - contiene los modelos Eloquent
  - un modelo representa una tabla o una vista de base
  - tambien define relaciones entre entidades

- `database/migrations`
  - define el esquema de base de datos en codigo
  - en este proyecto, las migrations son la fuente de verdad

- `database/seeders`
  - cargan datos iniciales
  - por ejemplo roles, permisos, estados, categorias base y usuario admin

- `tests`
  - contiene pruebas automatizadas
  - hoy hay smoke tests y algunos tests de integracion/permisos

## 3. Estructura Mental Del Negocio

Una forma util de entender SICIS es pensar el dominio en capas.

### Capa 1. Seguridad

Primero existe la seguridad interna:

- usuarios
- roles
- permisos

Sin eso no hay operacion del backoffice.

### Capa 2. Catalogos

Despues vienen los catalogos que ordenan el sistema:

- categorias
- categorias tematicas
- unidades de medida
- periodicidades
- estados
- modalidades
- areas municipales
- normas
- tipos
- jurisdicciones

Estos catalogos son la base para cargar indicadores y datos.

### Capa 3. Indicadores

Un indicador no es solo un nombre.

En este backend un indicador tiene:

- una definicion general
- una o varias versiones metodologicas
- variables asociadas a cada version

Eso permite que el metodo de calculo cambie en el tiempo sin perder historia.

### Capa 4. Datos Fuente

Los indicadores se alimentan con datos fuente.

Cada dato fuente tiene:

- una definicion base
- valores cargados
- estado de validacion
- evidencias
- eventualmente una configuracion para importar desde una API externa

### Capa 5. Corridas

La corrida es el momento donde el sistema toma datos validados y calcula resultados.

Una corrida:

- se hace para una jurisdiccion y un periodo
- usa versiones activas de indicadores
- toma datos fuente validados
- guarda snapshots de los datos usados
- guarda snapshots de resultados
- puede aprobarse y publicarse

### Capa 6. Observabilidad

Ademas del negocio, el sistema deja:

- auditoria
- alertas
- notificaciones internas

Eso ayuda a operar y diagnosticar.

### Capa 7. Salidas Externas

Finalmente, el backend expone consultas simples para terceros:

- indicadores vigentes
- resultados publicos
- corridas publicadas

## 4. Como Viaja Una Request

Ejemplo:

`POST /api/datos-fuente/{datoFuente}/valores/{valor}/validar`

El recorrido mental es:

1. La ruta vive en `routes/api.php`
2. La ruta pasa por middleware
3. El middleware chequea autenticacion y permiso
4. Entra al controller correspondiente
5. El controller valida el body
6. El controller busca el modelo y aplica reglas de negocio
7. El modelo persiste cambios en base
8. El controller devuelve JSON
9. Si corresponde, tambien genera auditoria o alertas

Eso se repite bastante en casi todo el proyecto.

## 5. Donde Esta Cada Modulo

### Auth

- `app/Http/Controllers/AuthController.php`
- rutas en `routes/api.php`

Hace login por `nombre_usuario`, devuelve token Sanctum y expone perfil.

### Catalogos

- `app/Http/Controllers/Catalogos/`

Muchos catalogos reutilizan un controller base:

- `BaseCatalogController.php`

Eso evita repetir CRUD simple.

### Indicadores

- `app/Http/Controllers/Indicadores/`

Aca se administra:

- indicador
- version metodologica
- variable

### Datos Fuente

- `app/Http/Controllers/DatosFuente/`

Aca viven:

- catalogo de datos fuente
- valores
- validacion
- evidencias
- conectores API

### Corridas

- `app/Http/Controllers/Corridas/CorridaController.php`

Es uno de los puntos mas importantes del sistema porque une indicadores con datos validados y genera resultados.

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

## 6. Como Se Organiza La Base

La base no se piensa desde SQL suelto sino desde migrations.

Archivos clave:

- `2026_04_28_215107_create_usuarios_table.php`
- `2026_04_28_215108_create_sicis_catalog_tables.php`
- `2026_04_28_215109_create_sicis_core_tables.php`
- `2026_04_28_215110_create_sicis_operations_tables.php`
- `2026_04_28_215111_create_sicis_views.php`

Una lectura simple seria:

- `catalog tables` = catalogos base
- `core tables` = indicadores y datos fuente
- `operations tables` = corridas, alertas, auditoria, integraciones
- `views` = vistas para consulta externa

## 7. Como Funciona La Seguridad

Hay dos conceptos separados:

### Autenticacion

Responde a:

"quien sos"

Se resuelve con login y token Sanctum.

### Autorizacion

Responde a:

"que podes hacer"

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

## 8. Que Es Una Corrida En Terminos Simples

Si alguien del equipo pregunta "que hace una corrida", una forma corta de explicarlo es esta:

Una corrida toma:

- una jurisdiccion
- un periodo
- indicadores activos
- la version vigente de cada indicador
- datos fuente validados para ese contexto

Y produce:

- resultados calculados
- snapshot de datos usados
- snapshot de resultados

Despues esos resultados pueden:

- quedar ejecutados
- aprobarse
- publicarse

## 9. Que Son Los Snapshots

Un snapshot es una foto historica del momento de calculo.

Se guarda para que mas adelante puedas saber:

- que datos exactos se usaron
- que formula/version aplicaba
- que resultado se obtuvo

Eso evita que un cambio posterior en los datos o en la metodologia borre la trazabilidad historica.

## 10. Como Esta Pensada La Observabilidad

El proyecto tiene dos niveles de observabilidad:

### Nivel funcional

- auditoria
- alertas
- notificaciones internas

Sirve para seguir acciones del negocio.

### Nivel tecnico

- respuestas de error estandarizadas
- `request_id`
- logging estructurado

Sirve para diagnosticar problemas de API y soporte.

## 11. Como Empezar A Leer El Codigo

Si alguien entra nuevo al proyecto, este es un orden razonable:

1. `README.md`
2. `docs/indice-contratos-api.md`
3. `routes/api.php`
4. `app/Http/Controllers/AuthController.php`
5. `app/Http/Middleware/CheckPermission.php`
6. `app/Models/Usuario.php`
7. controllers por modulo:
   - `Catalogos`
   - `Indicadores`
   - `DatosFuente`
   - `Corridas`
   - `Observabilidad`
   - `Seguridad`
   - `Externo`
8. migrations principales
9. tests feature

Ese recorrido da una imagen bastante buena sin tener que leer todo el repo de golpe.

## 12. Como Pensar Los Tests

Hoy los tests sirven mas como validacion de flujos clave que como cobertura total.

Hay tres bloques importantes:

- smoke tests de auth y catalogos
- integracion de indicadores
- permisos y validacion de datos fuente
- consistencia de errores

Archivos utiles:

- `tests/Feature/AuthAndCatalogSmokeTest.php`
- `tests/Feature/IndicadoresIntegrationTest.php`
- `tests/Feature/DatosFuentePermissionsTest.php`
- `tests/Feature/ErrorHandlingConsistencyTest.php`

Si queres entender el comportamiento esperado del sistema, leer esos tests ayuda bastante.

## 13. Glosario Rapido

### Indicador

Definicion conceptual de una medicion del sistema.

### Version metodologica

Version concreta de como se calcula un indicador en cierto periodo.

### Variable

Dato fuente que participa en la formula de una version.

### Dato fuente

Fuente operativa de informacion que alimenta indicadores.

### Valor de dato fuente

Medicion concreta cargada para una jurisdiccion y un periodo.

### Corrida

Proceso de calculo de resultados para un periodo/jurisdiccion.

### Snapshot

Foto historica de datos o resultados usados en una corrida.

### Publicable

Marca que indica si algo puede exponerse hacia consumos externos.

### Sensible

Marca que indica que el dato no deberia exponerse publicamente.

## 14. Idea Final Para El Equipo

La forma mas sana de pensar este proyecto no es "un proyecto PHP", sino:

"un backend de dominio bastante ordenado, montado sobre Laravel"

Si ya entendes:

- API REST
- autenticacion
- permisos
- entidades y relaciones
- validacion
- procesos batch o de calculo
- trazabilidad

entonces ya entendes gran parte del sistema.

Laravel en este repo es, sobre todo, la estructura que organiza esas piezas.
