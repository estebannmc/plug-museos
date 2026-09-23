=== Capital Cultural – Programación de Museos ===
Contributors: Esteban Maximiliano Córdoba
Tags: museos, cultura, agenda, shortcode, santa fe
Requires at least: 6.5
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 3.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gestión centralizada de muestras, actividades, talleres y cursos de museos y espacios culturales de la ciudad de Santa Fe.

== Descripción ==

Capital Cultural – Programación de Museos crea un tipo de contenido propio para cargar propuestas culturales y mostrarlas automáticamente mediante shortcodes. No depende de ACF, Elementor Pro ni otros plugins externos.

El plugin incluye:

* Tipo de contenido "Programación de Museos".
* Taxonomía de espacios culturales.
* Taxonomía de categorías de programación.
* Campos de fecha, horarios, ubicación, enlace externo, destacado, estado manual y encuadre de imagen.
* Cards responsive con cinco columnas por defecto en desktop.
* Modal accesible con información completa.
* Shortcode genérico y alias para los espacios iniciales.
* Slider de propuestas activas para el inicio del sitio, con filtro opcional por categoría y movimiento configurable.
* Pantallas administrativas "Ajustes del slider" y "Ayuda y shortcodes".

Autor: Esteban Maximiliano Córdoba.

== Instalación ==

1. Subí el archivo `capital-cultural-programacion.zip` desde Plugins > Agregar nuevo > Subir plugin.
2. Activá "Capital Cultural – Programación de Museos".
3. Entrá a Programación de Museos > Agregar nueva.
4. Cargá título, descripción, imagen destacada, extracto, espacio, categoría y fecha de inicio.
5. Publicá la propuesta.
6. Colocá el shortcode correspondiente en Elementor Free usando el widget Shortcode o en el editor de WordPress.

== Shortcodes disponibles ==

= Museo Sor Josefa =

Abreviado:

`[sorjosefa]`

Genérico equivalente:

`[cc_programacion espacio="sor-josefa"]`

= Museo del Teatro =

Abreviado:

`[museodelteatro]`

Genérico equivalente:

`[cc_programacion espacio="museo-del-teatro"]`

= Museo de la Constitución Nacional =

Abreviado:

`[museoconstitucion]`

Genérico equivalente:

`[cc_programacion espacio="museo-de-la-constitucion"]`

= Casa Museo César López Claro =

Abreviado:

`[cesarlopezclaro]`

Genérico equivalente:

`[cc_programacion espacio="cesar-lopez-claro"]`

= Centro Experimental del Color =

Abreviado:

`[cec]`

Genérico equivalente:

`[cc_programacion espacio="cec"]`

= Fotogalería Municipal =

Abreviado:

`[fotogaleria]`

Genérico equivalente:

`[cc_programacion espacio="fotogaleria"]`

= Museo de la Ciudad =

Abreviado:

`[museodelaciudad]`

Genérico equivalente:

`[cc_programacion espacio="museo-de-la-ciudad"]`

= Casa del Brigadier Estanislao López =

Abreviado:

`[casadelbrigadier]`

Genérico equivalente:

`[cc_programacion espacio="casa-del-brigadier"]`

= Museo del Colegio Inmaculada =

Abreviado:

`[museoinmaculada]`

Genérico equivalente:

`[cc_programacion espacio="museo-inmaculada"]`

= Shortcode genérico para nuevos espacios =

`[cc_programacion espacio="nuevo-espacio"]`

Cuando agregues un nuevo espacio desde WordPress, podés usar su slug inmediatamente con el shortcode genérico. No hace falta modificar código.

= Slider de propuestas activas para el inicio =

Muestra propuestas publicadas cuyo estado calculado sea `ACTIVA` o `PRÓXIMAMENTE`. Puede mostrar todas las categorías, una o varias categorías elegidas, y tomar la configuración por defecto desde Programación de Museos > Ajustes del slider.

Shortcode principal:

`[cc_propuestas_activas]`

Mostrar solo muestras:

`[cc_propuestas_activas categoria="muestra"]`

Mostrar talleres:

`[cc_propuestas_activas categoria="taller"]`

Mostrar varias categorías:

`[cc_propuestas_activas categorias="muestra,taller"]`

Atajos disponibles:

`[cc_muestras_activas]`

`[cc_actividades_activas]`

`[cc_talleres_activos]`

`[cc_cursos_activos]`

Alias de compatibilidad:

`[cc_muestras_activas_slider]`

`[cc_talleres_activas]`

`[cc_cursos_activas]`

Ejemplo limitando cantidad:

`[cc_propuestas_activas categoria="muestra" cantidad="6"]`

Ejemplo filtrando por espacio:

`[cc_propuestas_activas categoria="muestra" espacio="sor-josefa"]`

Ejemplo sin título interno, útil cuando Elementor ya tiene un encabezado:

`[cc_propuestas_activas titulo=""]`

Ejemplo con movimiento y próximas incluidas:

`[cc_propuestas_activas categorias="muestra,taller" estados="activa,proximamente" movimiento="si" velocidad="4500"]`

== Atributos ==

`espacio=""`

Slug del espacio cultural. Se usa en `[cc_programacion]` y opcionalmente en `[cc_propuestas_activas]` para filtrar el slider.

`cantidad="-1"`

Cantidad de propuestas a mostrar. `-1` muestra todas.

`columnas="5"`

Cantidad de columnas de desktop. Acepta valores de 1 a 6. En tablet y mobile el responsive tiene prioridad.

`categoria=""`

Slug de una categoría para el slider de propuestas activas, por ejemplo `muestra`, `actividad`, `taller` o `curso`.

`categorias=""`

Lista de slugs separados por coma para el slider de propuestas activas, por ejemplo `muestra,taller`. Si no se define ninguna categoría en el shortcode ni en el panel, el slider muestra todas.

`estados="activa,proximamente"`

Estados incluidos en el slider. Por defecto se muestran activas y próximamente. Acepta `activa`, `proximamente` o `activa,proximamente`. También se puede restringir desde Programación de Museos > Ajustes del slider.

`mostrar_extracto="si"`

Usá `si` para mostrar extracto o `no` para ocultarlo.

`texto_vacio="No hay propuestas cargadas para este espacio."`

Mensaje cuando no hay propuestas publicadas para el espacio.

`titulo="Propuestas activas"`

Título interno del slider de propuestas activas. Puede dejarse vacío con `titulo=""`.

`movimiento="si"`

Activa o desactiva el movimiento automático del slider. También se puede configurar desde el panel del plugin.

`velocidad="4500"`

Tiempo entre movimientos del slider, en milisegundos.

`pausar_hover="si"`

Pausa el movimiento cuando el cursor o el foco están sobre el slider.

== Ejemplos ==

Mostrar todas:

`[sorjosefa cantidad="-1"]`

Limitar cantidad:

`[sorjosefa cantidad="10"]`

Cinco columnas:

`[sorjosefa columnas="5"]`

Cuatro columnas:

`[sorjosefa columnas="4"]`

Tres columnas:

`[sorjosefa columnas="3"]`

Dos columnas:

`[sorjosefa columnas="2"]`

Una columna:

`[sorjosefa columnas="1"]`

Ocultar el extracto:

`[sorjosefa mostrar_extracto="no"]`

Mostrar el extracto:

`[sorjosefa mostrar_extracto="si"]`

Cambiar el mensaje vacío:

`[sorjosefa texto_vacio="Próximamente se publicarán nuevas propuestas."]`

Combinar atributos:

`[sorjosefa cantidad="10" columnas="5" mostrar_extracto="no" texto_vacio="Próximamente se publicarán nuevas propuestas."]`

Ejemplo genérico completo:

`[cc_programacion espacio="sor-josefa" cantidad="10" columnas="5" mostrar_extracto="si" texto_vacio="No hay propuestas cargadas para este espacio."]`

Slider para el inicio:

`[cc_propuestas_activas categorias="muestra,taller" cantidad="6" mostrar_extracto="si" titulo="Propuestas activas"]`

== Campos disponibles ==

* Fecha de inicio: obligatoria.
* Fecha de finalización: opcional.
* Horarios: opcional.
* Sala o ubicación: opcional.
* Enlace externo: opcional.
* Texto del botón externo: opcional, por defecto "Más información".
* Destacar propuesta: opcional.
* Muestra principal: opcional, permite ubicar una actividad inmediatamente antes de la muestra en la que sucede.
* Estado manual: Automático, Próximamente, Activa, Finalizada, Permanente, Suspendida o Reprogramada.
* Encuadre de imagen: foco horizontal y vertical para mejorar el recorte visual sin modificar la imagen original.

== Recomendaciones para la imagen destacada ==

El plugin usa una sola imagen destacada por propuesta y la recorta con CSS para verse bien en grilla, slider y modal, sin generar ni modificar archivos nuevos. Para que el recorte se vea nítido en el modal (donde la imagen ocupa más espacio en pantallas de escritorio), subí fotos de al menos 1200 píxeles en su lado más corto. Con imágenes más chicas, el navegador tiene que agrandarlas para cubrir el recuadro y se ven pixeladas.

== Ordenamiento ==

Las propuestas publicadas se muestran siempre, incluidas las históricas. El orden es:

1. Activas.
2. Próximamente.
3. Permanentes.
4. Reprogramadas.
5. Suspendidas.
6. Finalizadas.

Dentro de cada grupo aparecen primero las destacadas. Luego se ordenan por fecha de inicio, salvo las finalizadas, que se ordenan por fecha final descendente. Las actividades vinculadas a una muestra aparecen inmediatamente antes de su muestra principal. Si no se selecciona una relación, las actividades de un día se vinculan automáticamente cuando suceden dentro de las fechas de una muestra del mismo museo.

== Agregar nuevos espacios culturales ==

1. Entrá a Programación de Museos > Espacios culturales.
2. Creá el espacio y definí su slug.
3. El plugin genera automáticamente un shortcode abreviado a partir del slug, quitando los guiones. Por ejemplo, un espacio con slug `centro-cultural-nuevo` habilita `[centroculturalnuevo]` de inmediato, sin tocar código.
4. También podés usar siempre el shortcode genérico: `[cc_programacion espacio="slug-del-espacio"]`.

== Agregar un alias de shortcode con nombre personalizado ==

Si preferís que el abreviado no coincida con el slug sin guiones (como pasa con `[museoconstitucion]`), editá `includes/class-ccp-shortcodes.php` y agregá una entrada al método `aliases()`:

`'nuevoalias' => 'slug-del-espacio',`

Ese nombre personalizado tiene prioridad sobre el generado automáticamente.

== Estructura general ==

* `capital-cultural-programacion.php`: bootstrap y cabecera del plugin.
* `includes/`: clases de CPT, taxonomías, metaboxes, ajustes, shortcodes, estados, fechas, assets y avisos.
* `templates/`: render de grilla, card, modal y mensaje vacío.
* `assets/css/`: estilos frontend y admin.
* `assets/js/frontend.js`: modal y slider sin jQuery.
* `assets/js/admin.js`: vista previa del encuadre de imagen.
* `uninstall.php`: conserva datos por defecto.

== Desinstalación ==

Por defecto no se eliminan propuestas, taxonomías ni metadatos para evitar pérdida accidental de información cultural.

Para permitir limpieza completa, definí `CCP_DELETE_ALL_DATA` como `true` antes de desinstalar o usá el filtro `ccp_delete_all_data_on_uninstall`.

== Changelog ==

= 3.1.0 =
Los shortcodes de propuestas activas (slider) ahora incluyen "Próximamente" además de "Activa" por defecto, tanto en el ajuste global como en cada shortcode. El modal de detalle usa un recorte de imagen más moderado y con altura acotada en escritorio, para que no se vea forzado ni pixelado en pantallas muy altas.

= 3.0.1 =
Los espacios culturales nuevos ahora generan su shortcode abreviado automáticamente al crearse, sin necesidad de editar el código PHP.

= 3.0.0 =
Slider con movimiento automático configurable desde el panel del plugin, selección de categorías y estados por defecto, y encuadre no destructivo de imagen por propuesta.

= 2.1.7 =
Vinculación de actividades con muestras, etiqueta "Finalizada", mejoras de modal e imágenes, y editor clásico para propuestas.

= 1.0.0 =
Versión inicial.
