<?php
/**
 * Shortcode handling and frontend rendering.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers generic and alias shortcodes.
 */
final class CCP_Shortcodes {
	/**
	 * Registers shortcode hooks.
	 */
	public function init(): void {
		add_shortcode( 'cc_programacion', array( $this, 'render_generic' ) );
		add_shortcode( 'cc_propuestas_activas', array( $this, 'render_active_proposals_slider' ) );

		foreach ( self::active_category_aliases() as $shortcode => $config ) {
			add_shortcode(
				$shortcode,
				function ( $atts = array() ) use ( $config, $shortcode ) {
					$atts = is_array( $atts ) ? $atts : array();

					return $this->render_active_slider(
						$atts,
						$shortcode,
						$config['title'],
						$config['empty_text'],
						array( $config['category'] )
					);
				}
			);
		}

		// Registered late on 'init' (after taxonomy registration) so every
		// cultural space, including ones created from the admin UI, gets a
		// working shortcode alias without editing PHP.
		add_action( 'init', array( $this, 'register_space_aliases' ), 20 );
	}

	/**
	 * Registers a shortcode alias for every existing cultural space.
	 *
	 * Known spaces keep their historic shortcode name (from aliases()).
	 * Any space created afterwards from Programación de Museos > Espacios
	 * culturales automatically gets an alias derived from its slug, with
	 * no code changes required.
	 */
	public function register_space_aliases(): void {
		$slug_to_tag = array_flip( self::aliases() );

		foreach ( $this->get_space_slugs() as $slug ) {
			$tag = $slug_to_tag[ $slug ] ?? $this->slug_to_shortcode_tag( $slug );

			if ( '' === $tag || shortcode_exists( $tag ) ) {
				continue;
			}

			add_shortcode(
				$tag,
				function ( $atts = array() ) use ( $slug ) {
					$atts            = is_array( $atts ) ? $atts : array();
					$atts['espacio'] = $slug;

					return $this->render_generic( $atts );
				}
			);
		}
	}

	/**
	 * Returns the slugs of every registered cultural space.
	 *
	 * @return string[]
	 */
	private function get_space_slugs(): array {
		if ( ! taxonomy_exists( CCP_Taxonomies::TAX_ESPACIO ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => CCP_Taxonomies::TAX_ESPACIO,
				'hide_empty' => false,
				'fields'     => 'slugs',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		return $terms;
	}

	/**
	 * Converts a space slug into a shortcode tag by stripping hyphens,
	 * e.g. "museo-de-la-ciudad" becomes "museodelaciudad".
	 *
	 * @param string $slug Space slug.
	 */
	private function slug_to_shortcode_tag( string $slug ): string {
		return sanitize_key( str_replace( '-', '', $slug ) );
	}

	/**
	 * Shortcode aliases keyed by shortcode tag.
	 *
	 * @return array<string,string>
	 */
	public static function aliases(): array {
		return array(
			'sorjosefa'        => 'sor-josefa',
			'museodelteatro'   => 'museo-del-teatro',
			'museoconstitucion' => 'museo-de-la-constitucion',
			'cesarlopezclaro'  => 'cesar-lopez-claro',
			'cec'              => 'cec',
			'fotogaleria'      => 'fotogaleria',
			'museodelaciudad'  => 'museo-de-la-ciudad',
			'casadelbrigadier' => 'casa-del-brigadier',
			'museoinmaculada'  => 'museo-inmaculada',
		);
	}

	/**
	 * Active slider aliases keyed by shortcode tag.
	 *
	 * @return array<string,array{category:string,title:string,empty_text:string}>
	 */
	public static function active_category_aliases(): array {
		return array(
			'cc_muestras_activas'        => array(
				'category'   => 'muestra',
				'title'      => __( 'Muestras activas', 'capital-cultural-programacion' ),
				'empty_text' => __( 'No hay muestras activas en este momento.', 'capital-cultural-programacion' ),
			),
			'cc_muestras_activas_slider' => array(
				'category'   => 'muestra',
				'title'      => __( 'Muestras activas', 'capital-cultural-programacion' ),
				'empty_text' => __( 'No hay muestras activas en este momento.', 'capital-cultural-programacion' ),
			),
			'cc_actividades_activas'     => array(
				'category'   => 'actividad',
				'title'      => __( 'Actividades activas', 'capital-cultural-programacion' ),
				'empty_text' => __( 'No hay actividades activas en este momento.', 'capital-cultural-programacion' ),
			),
			'cc_talleres_activos'        => array(
				'category'   => 'taller',
				'title'      => __( 'Talleres activos', 'capital-cultural-programacion' ),
				'empty_text' => __( 'No hay talleres activos en este momento.', 'capital-cultural-programacion' ),
			),
			'cc_talleres_activas'        => array(
				'category'   => 'taller',
				'title'      => __( 'Talleres activos', 'capital-cultural-programacion' ),
				'empty_text' => __( 'No hay talleres activos en este momento.', 'capital-cultural-programacion' ),
			),
			'cc_cursos_activos'          => array(
				'category'   => 'curso',
				'title'      => __( 'Cursos activos', 'capital-cultural-programacion' ),
				'empty_text' => __( 'No hay cursos activos en este momento.', 'capital-cultural-programacion' ),
			),
			'cc_cursos_activas'          => array(
				'category'   => 'curso',
				'title'      => __( 'Cursos activos', 'capital-cultural-programacion' ),
				'empty_text' => __( 'No hay cursos activos en este momento.', 'capital-cultural-programacion' ),
			),
		);
	}

	/**
	 * Renders a homepage slider with all active proposals or selected categories.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 */
	public function render_active_proposals_slider( array $atts = array() ): string {
		return $this->render_active_slider(
			$atts,
			'cc_propuestas_activas',
			__( 'Propuestas activas', 'capital-cultural-programacion' ),
			__( 'No hay propuestas activas en este momento.', 'capital-cultural-programacion' )
		);
	}

	/**
	 * Renders the legacy active exhibitions slider.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 */
	public function render_active_exhibitions_slider( array $atts = array() ): string {
		return $this->render_active_slider(
			$atts,
			'cc_muestras_activas',
			__( 'Muestras activas', 'capital-cultural-programacion' ),
			__( 'No hay muestras activas en este momento.', 'capital-cultural-programacion' ),
			array( 'muestra' )
		);
	}

	/**
	 * Renders an active proposals slider.
	 *
	 * @param array<string,mixed> $atts              Shortcode attributes.
	 * @param string              $shortcode         Shortcode tag.
	 * @param string              $default_title     Default title.
	 * @param string              $default_empty     Default empty text.
	 * @param string[]|null       $forced_categories Fixed category slugs for aliases.
	 */
	private function render_active_slider(
		array $atts,
		string $shortcode,
		string $default_title,
		string $default_empty,
		?array $forced_categories = null
	): string {
		$settings           = CCP_Settings::get();
		$default_categories = null !== $forced_categories ? '' : implode( ',', $settings['slider_categories'] );
		$default_statuses   = implode( ',', $settings['slider_statuses'] );
		$default_title      = '' !== $settings['slider_title'] ? (string) $settings['slider_title'] : $default_title;
		$atts = shortcode_atts(
			array(
				'espacio'         => '',
				'cantidad'        => (string) $settings['slider_limit'],
				'categoria'       => '',
				'categorias'      => $default_categories,
				'estados'         => $default_statuses,
				'mostrar_extracto' => 'si',
				'titulo'          => $default_title,
				'texto_vacio'     => $default_empty,
				'movimiento'      => $settings['slider_autoplay'] ? 'si' : 'no',
				'velocidad'       => (string) $settings['slider_interval'],
				'pausar_hover'    => $settings['slider_pause_hover'] ? 'si' : 'no',
			),
			$atts,
			$shortcode
		);

		$space_slug   = sanitize_title( (string) $atts['espacio'] );
		$limit        = $this->sanitize_limit( $atts['cantidad'] );
		$show_excerpt = 'no' !== strtolower( sanitize_text_field( (string) $atts['mostrar_extracto'] ) );
		$title        = sanitize_text_field( (string) $atts['titulo'] );
		$empty_text   = sanitize_text_field( (string) $atts['texto_vacio'] );
		$categories   = null !== $forced_categories
			? $this->sanitize_category_slugs( implode( ',', $forced_categories ) )
			: $this->sanitize_category_slugs( (string) $atts['categorias'] . ',' . (string) $atts['categoria'] );
		$statuses     = $this->sanitize_status_slugs( (string) $atts['estados'] );
		$autoplay     = $this->sanitize_yes_no( (string) $atts['movimiento'] );
		$interval     = max( 2000, min( 15000, absint( $atts['velocidad'] ) ) );
		$pause_hover  = $this->sanitize_yes_no( (string) $atts['pausar_hover'] );

		if ( '' !== $space_slug ) {
			$term = get_term_by( 'slug', $space_slug, CCP_Taxonomies::TAX_ESPACIO );
			if ( ! $term || is_wp_error( $term ) ) {
				return $this->admin_only_notice(
					sprintf(
						/* translators: %s: space slug. */
						__( 'No existe un espacio cultural con el slug "%s".', 'capital-cultural-programacion' ),
						$space_slug
					)
				);
			}
		}

		foreach ( $categories as $category_slug ) {
			$category = get_term_by( 'slug', $category_slug, CCP_Taxonomies::TAX_CATEGORIA );
			if ( ! $category || is_wp_error( $category ) ) {
				return $this->admin_only_notice(
					sprintf(
						/* translators: %s: category slug. */
						__( 'No existe una categoría de programación con el slug "%s".', 'capital-cultural-programacion' ),
						$category_slug
					)
				);
			}
		}

		CCP_Assets::enqueue_frontend();

		$posts = $this->get_active_posts( $space_slug, $categories, $statuses );
		$posts = CCP_Status::sort_posts( $posts );

		if ( $limit > -1 ) {
			$posts = array_slice( $posts, 0, $limit );
		}

		if ( empty( $posts ) ) {
			return $this->render_template(
				'no-results.php',
				array(
					'empty_text' => $empty_text,
				)
			);
		}

		static $slider_instance = 0;
		++$slider_instance;

		return $this->render_template(
			'programacion-slider.php',
			array(
				'posts'        => $posts,
				'show_excerpt' => $show_excerpt,
				'instance'     => $slider_instance,
				'title'        => $title,
				'autoplay'     => $autoplay,
				'interval'     => $interval,
				'pause_hover'  => $pause_hover,
			)
		);
	}

	/**
	 * Renders the generic programming shortcode.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 */
	public function render_generic( array $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'espacio'         => '',
				'cantidad'        => '-1',
				'columnas'        => '5',
				'mostrar_extracto' => 'si',
				'texto_vacio'     => __( 'No hay propuestas cargadas para este espacio.', 'capital-cultural-programacion' ),
			),
			$atts,
			'cc_programacion'
		);

		$space_slug   = sanitize_title( (string) $atts['espacio'] );
		$limit        = $this->sanitize_limit( $atts['cantidad'] );
		$columns      = max( 1, min( 6, absint( $atts['columnas'] ) ) );
		$show_excerpt = 'no' !== strtolower( sanitize_text_field( (string) $atts['mostrar_extracto'] ) );
		$empty_text   = sanitize_text_field( (string) $atts['texto_vacio'] );

		if ( '' === $space_slug ) {
			return $this->admin_only_notice( __( 'El shortcode cc_programacion necesita el atributo espacio.', 'capital-cultural-programacion' ) );
		}

		$term = get_term_by( 'slug', $space_slug, CCP_Taxonomies::TAX_ESPACIO );
		if ( ! $term || is_wp_error( $term ) ) {
			return $this->admin_only_notice(
				sprintf(
					/* translators: %s: space slug. */
					__( 'No existe un espacio cultural con el slug "%s".', 'capital-cultural-programacion' ),
					$space_slug
				)
			);
		}

		CCP_Assets::enqueue_frontend();

		$posts = $this->get_posts_for_space( $space_slug );
		$posts = CCP_Status::sort_posts( $posts );

		if ( $limit > -1 ) {
			$posts = array_slice( $posts, 0, $limit );
		}

		if ( empty( $posts ) ) {
			return $this->render_template(
				'no-results.php',
				array(
					'empty_text' => $empty_text,
				)
			);
		}

		static $instance = 0;
		++$instance;

		return $this->render_template(
			'programacion-grid.php',
			array(
				'posts'        => $posts,
				'columns'      => $columns,
				'show_excerpt' => $show_excerpt,
				'instance'     => $instance,
			)
		);
	}

	/**
	 * Queries published proposals for a cultural space.
	 *
	 * @param string $space_slug Space slug.
	 * @return \WP_Post[]
	 */
	private function get_posts_for_space( string $space_slug ): array {
		$query = new \WP_Query(
			array(
				'post_type'              => CCP_Post_Type::POST_TYPE,
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'orderby'                => 'meta_value',
				'meta_key'               => '_ccp_fecha_inicio',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => true,
				'tax_query'              => array(
					array(
						'taxonomy' => CCP_Taxonomies::TAX_ESPACIO,
						'field'    => 'slug',
						'terms'    => $space_slug,
					),
				),
			)
		);

		return $query->posts;
	}

	/**
	 * Queries active proposals, optionally scoped by cultural space and categories.
	 *
	 * @param string   $space_slug     Optional space slug.
	 * @param string[] $category_slugs Optional category slugs.
	 * @return \WP_Post[]
	 */
	private function get_active_posts( string $space_slug = '', array $category_slugs = array(), array $status_slugs = array( 'activa' ) ): array {
		$tax_query = array();

		if ( ! empty( $category_slugs ) ) {
			$tax_query[] = array(
				'taxonomy' => CCP_Taxonomies::TAX_CATEGORIA,
				'field'    => 'slug',
				'terms'    => $category_slugs,
			);
		}

		if ( '' !== $space_slug ) {
			$tax_query[] = array(
				'taxonomy' => CCP_Taxonomies::TAX_ESPACIO,
				'field'    => 'slug',
				'terms'    => $space_slug,
			);
		}

		$query_args = array(
			'post_type'              => CCP_Post_Type::POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'meta_value',
			'meta_key'               => '_ccp_fecha_inicio',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		);

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		}

		$query = new \WP_Query( $query_args );

		return array_values(
			array_filter(
				$query->posts,
				static function ( \WP_Post $post ) use ( $status_slugs ): bool {
					$status = CCP_Status::get_status( $post->ID );

					return in_array( $status['key'], $status_slugs, true );
				}
			)
		);
	}

	/**
	 * Sanitizes comma-separated category slugs.
	 *
	 * @param string $value Raw category attribute.
	 * @return string[]
	 */
	private function sanitize_category_slugs( string $value ): array {
		$slugs = array_map( 'sanitize_title', explode( ',', $value ) );
		$slugs = array_filter(
			$slugs,
			static function ( string $slug ): bool {
				return '' !== $slug;
			}
		);

		return array_values( array_unique( $slugs ) );
	}

	/**
	 * Sanitizes comma-separated status slugs for active sliders.
	 *
	 * @param string $value Raw status attribute.
	 * @return string[]
	 */
	private function sanitize_status_slugs( string $value ): array {
		$slugs = array_map( 'sanitize_key', explode( ',', $value ) );
		$slugs = array_values( array_intersect( $slugs, array( 'activa', 'proximamente' ) ) );

		return empty( $slugs ) ? array( 'activa' ) : array_values( array_unique( $slugs ) );
	}

	/**
	 * Sanitizes Spanish yes/no shortcode attributes.
	 *
	 * @param string $value Raw value.
	 */
	private function sanitize_yes_no( string $value ): bool {
		return in_array( strtolower( sanitize_text_field( $value ) ), array( '1', 'si', 'sí', 'true', 'yes' ), true );
	}

	/**
	 * Sanitizes the amount attribute.
	 *
	 * @param mixed $value Attribute value.
	 */
	private function sanitize_limit( mixed $value ): int {
		$limit = (int) $value;

		if ( -1 === $limit ) {
			return -1;
		}

		return max( 1, $limit );
	}

	/**
	 * Returns a notice only to administrators.
	 *
	 * @param string $message Notice.
	 */
	private function admin_only_notice( string $message ): string {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return '';
		}

		return '<div class="ccp-notice ccp-notice--admin">' . esc_html( $message ) . '</div>';
	}

	/**
	 * Renders a template file.
	 *
	 * @param string              $template Template name.
	 * @param array<string,mixed> $data     Template data.
	 */
	private function render_template( string $template, array $data ): string {
		$file = CCP_PLUGIN_DIR . 'templates/' . $template;
		if ( ! file_exists( $file ) ) {
			return '';
		}

		ob_start();
		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		include $file;

		return (string) ob_get_clean();
	}
}
