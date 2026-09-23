# Capital Cultural – Programación de Museos

Plugin de WordPress para gestionar y publicar la agenda de los museos y espacios culturales de la ciudad de Santa Fe: muestras, actividades, talleres y cursos.

Cada propuesta se carga una sola vez desde el panel de WordPress y se muestra automáticamente en la página de cada museo y en el slider del inicio, mediante shortcodes. No depende de ACF, Elementor Pro ni otros plugins externos, así que funciona con Elementor Free o con el editor de WordPress.

**Versión actual:** 3.1.0 · **Requiere:** WordPress 6.5+ y PHP 8.0+

## Qué hace

- Crea el tipo de contenido **Programación de Museos**, con las taxonomías **Espacios culturales** y **Categorías** (muestra, actividad, taller, curso).
- Agrega campos propios: fecha de inicio y de finalización, horarios, sala, enlace externo, propuesta destacada, estado manual y encuadre de imagen.
- **Calcula el estado de cada propuesta a partir de sus fechas**: Próximamente, Activa o Finalizada. También se puede fijar a mano: Permanente, Suspendida o Reprogramada.
- Ordena las propuestas por estado, luego las destacadas y luego por fecha. Las actividades aparecen junto a la muestra en la que suceden.
- Muestra una grilla de cards responsive (5 columnas por defecto) con un **modal accesible** con la información completa.
- Incluye un **slider de propuestas activas y próximas** para el inicio, con movimiento automático configurable desde *Programación de Museos > Ajustes del slider*.
- Aplica un **encuadre de imagen no destructivo**: el foco horizontal y vertical se elige por propuesta, sin generar ni modificar archivos.
- Usa JavaScript sin jQuery.

## Instalación

1. Descargá o cloná este repositorio.
2. Comprimí la carpeta del plugin:
   ```bash
   zip -r capital-cultural-programacion.zip capital-cultural-programacion
   ```
3. En WordPress, andá a **Plugins > Agregar nuevo > Subir plugin**, subí el `.zip` y activá *Capital Cultural – Programación de Museos*.
   (También podés copiar la carpeta `capital-cultural-programacion/` directo en `wp-content/plugins/`.)
4. Entrá a **Programación de Museos > Agregar nueva** y cargá título, descripción, imagen destacada, espacio, categoría y fecha de inicio.
5. Pegá el shortcode que corresponda en la página del museo, con el widget *Shortcode* de Elementor o con el editor de WordPress.

## Shortcodes disponibles

### Programación por espacio

Shortcode genérico, sirve para cualquier espacio:

```
[cc_programacion espacio="slug-del-espacio"]
```

Atajos incluidos para los espacios iniciales:

| Espacio | Atajo | Equivalente |
|---|---|---|
| Museo Sor Josefa | `[sorjosefa]` | `[cc_programacion espacio="sor-josefa"]` |
| Museo del Teatro | `[museodelteatro]` | `[cc_programacion espacio="museo-del-teatro"]` |
| Museo de la Constitución Nacional | `[museoconstitucion]` | `[cc_programacion espacio="museo-de-la-constitucion"]` |
| Casa Museo César López Claro | `[cesarlopezclaro]` | `[cc_programacion espacio="cesar-lopez-claro"]` |
| Centro Experimental del Color | `[cec]` | `[cc_programacion espacio="cec"]` |
| Fotogalería Municipal | `[fotogaleria]` | `[cc_programacion espacio="fotogaleria"]` |
| Museo de la Ciudad | `[museodelaciudad]` | `[cc_programacion espacio="museo-de-la-ciudad"]` |
| Casa del Brigadier Estanislao López | `[casadelbrigadier]` | `[cc_programacion espacio="casa-del-brigadier"]` |
| Museo del Colegio Inmaculada | `[museoinmaculada]` | `[cc_programacion espacio="museo-inmaculada"]` |

Cuando creás un espacio nuevo, el plugin genera su atajo automáticamente a partir del slug, sin los guiones. Por ejemplo, `centro-cultural-nuevo` pasa a ser `[centroculturalnuevo]`. No hace falta tocar código.

**Atributos:**

| Atributo | Default | Descripción |
|---|---|---|
| `espacio` | — | Slug del espacio cultural. |
| `cantidad` | `-1` | Cantidad de propuestas (`-1` = todas). |
| `columnas` | `5` | Columnas en desktop (1 a 6). |
| `mostrar_extracto` | `si` | `si` / `no`. |
| `texto_vacio` | *No hay propuestas cargadas para este espacio.* | Mensaje cuando no hay propuestas. |

```
[sorjosefa cantidad="10" columnas="4" mostrar_extracto="no"]
```

### Slider de propuestas activas (inicio)

```
[cc_propuestas_activas]
```

Muestra las propuestas en estado *Activa* y *Próximamente*. Atajos por categoría:

- `[cc_muestras_activas]`
- `[cc_actividades_activas]`
- `[cc_talleres_activos]`
- `[cc_cursos_activos]`

**Atributos:**

| Atributo | Descripción |
|---|---|
| `categoria` / `categorias` | Una categoría (`muestra`) o varias separadas por coma (`muestra,taller`). |
| `estados` | `activa`, `proximamente` o `activa,proximamente` (default). |
| `espacio` | Filtra por espacio cultural. |
| `cantidad` | Cantidad máxima de propuestas. |
| `titulo` | Título del slider. Se oculta con `titulo=""`. |
| `movimiento` | `si` / `no`: movimiento automático. |
| `velocidad` | Milisegundos entre movimientos (ej. `4500`). |
| `pausar_hover` | `si` / `no`: pausa al pasar el cursor. |
| `mostrar_extracto` | `si` / `no`. |

Los valores por defecto se configuran en **Programación de Museos > Ajustes del slider**, y cualquier atributo del shortcode los pisa.

```
[cc_propuestas_activas categorias="muestra,taller" cantidad="6" movimiento="si" velocidad="4500"]
```

## Capturas

<!-- Reemplazar por capturas reales, por ejemplo en docs/screenshots/ -->

| Grilla de un museo | Modal de detalle |
|---|---|
| ![Grilla](docs/screenshots/grilla.png) | ![Modal](docs/screenshots/modal.png) |

| Slider del inicio | Carga de una propuesta (admin) |
|---|---|
| ![Slider](docs/screenshots/slider.png) | ![Admin](docs/screenshots/admin.png) |

## Estructura

```
capital-cultural-programacion/
├── capital-cultural-programacion.php   # Bootstrap y cabecera del plugin
├── includes/                           # CPT, taxonomías, metaboxes, ajustes, shortcodes, estados, fechas
├── templates/                          # Grilla, card, modal, slider y mensaje vacío
├── assets/css/                         # Estilos frontend y admin
├── assets/js/                          # Modal/slider (frontend) y vista previa de encuadre (admin)
├── uninstall.php
└── readme.txt                          # Documentación completa en formato WordPress
```

La documentación completa (todos los ejemplos, el ordenamiento y el changelog) está en [`capital-cultural-programacion/readme.txt`](capital-cultural-programacion/readme.txt).

## Desinstalación

Por defecto, al desinstalar el plugin se conservan las propuestas, las taxonomías y los metadatos. Para borrar todo, definí `CCP_DELETE_ALL_DATA` como `true` antes de desinstalar, o usá el filtro `ccp_delete_all_data_on_uninstall`.

## Autor

Esteban Maximiliano Córdoba
