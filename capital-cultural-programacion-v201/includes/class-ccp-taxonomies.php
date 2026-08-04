<?php
/**
 * Taxonomy registration and initial terms.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers cultural spaces and programming categories.
 */
final class CCP_Taxonomies {
	public const TAX_ESPACIO   = 'cc_espacio';
	public const TAX_CATEGORIA = 'cc_categoria';

	/**
	 * Registers hooks.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Registers taxonomies.
	 */
	public function register(): void {
		register_taxonomy(
			self::TAX_ESPACIO,
			array( CCP_Post_Type::POST_TYPE ),
			array(
				'labels'            => array(
					'name'          => __( 'Espacios culturales', 'capital-cultural-programacion' ),
					'singular_name' => __( 'Espacio cultural', 'capital-cultural-programacion' ),
					'menu_name'     => __( 'Espacios culturales', 'capital-cultural-programacion' ),
					'all_items'     => __( 'Espacios culturales', 'capital-cultural-programacion' ),
					'edit_item'     => __( 'Editar espacio cultural', 'capital-cultural-programacion' ),
					'add_new_item'  => __( 'Agregar nuevo espacio cultural', 'capital-cultural-programacion' ),
				),
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => false,
				'show_in_rest'      => true,
				'meta_box_cb'       => false,
				'rewrite'           => false,
			)
		);

		register_taxonomy(
			self::TAX_CATEGORIA,
			array( CCP_Post_Type::POST_TYPE ),
			array(
				'labels'            => array(
					'name'          => __( 'Categorías de programación', 'capital-cultural-programacion' ),
					'singular_name' => __( 'Categoría de programación', 'capital-cultural-programacion' ),
					'menu_name'     => __( 'Categorías', 'capital-cultural-programacion' ),
					'all_items'     => __( 'Categorías', 'capital-cultural-programacion' ),
					'edit_item'     => __( 'Editar categoría', 'capital-cultural-programacion' ),
					'add_new_item'  => __( 'Agregar nueva categoría', 'capital-cultural-programacion' ),
				),
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => false,
				'show_in_rest'      => true,
				'meta_box_cb'       => false,
				'rewrite'           => false,
			)
		);
	}

	/**
	 * Creates the initial cultural spaces and categories.
	 */
	public static function create_initial_terms(): void {
		foreach ( self::initial_spaces() as $slug => $name ) {
			self::maybe_insert_term( $name, self::TAX_ESPACIO, $slug );
		}

		foreach ( self::initial_categories() as $slug => $name ) {
			self::maybe_insert_term( $name, self::TAX_CATEGORIA, $slug );
		}
	}

	/**
	 * Initial spaces keyed by slug.
	 *
	 * @return array<string,string>
	 */
	public static function initial_spaces(): array {
		return array(
			'sor-josefa'                => __( 'Museo Municipal de Artes Visuales “Sor Josefa Díaz y Clucellas”', 'capital-cultural-programacion' ),
			'museo-del-teatro'         => __( 'Museo del Teatro', 'capital-cultural-programacion' ),
			'museo-de-la-constitucion' => __( 'Museo de la Constitución Nacional', 'capital-cultural-programacion' ),
			'cesar-lopez-claro'        => __( 'Casa Museo César López Claro', 'capital-cultural-programacion' ),
			'cec'                      => __( 'Centro Experimental del Color', 'capital-cultural-programacion' ),
			'fotogaleria'              => __( 'Fotogalería Municipal', 'capital-cultural-programacion' ),
			'museo-de-la-ciudad'       => __( 'Museo de la Ciudad', 'capital-cultural-programacion' ),
			'casa-del-brigadier'       => __( 'Casa del Brigadier Estanislao López', 'capital-cultural-programacion' ),
			'museo-inmaculada'         => __( 'Museo del Colegio Inmaculada', 'capital-cultural-programacion' ),
		);
	}

	/**
	 * Initial categories keyed by slug.
	 *
	 * @return array<string,string>
	 */
	public static function initial_categories(): array {
		return array(
			'muestra'   => __( 'Muestra', 'capital-cultural-programacion' ),
			'actividad' => __( 'Actividad', 'capital-cultural-programacion' ),
			'taller'    => __( 'Taller', 'capital-cultural-programacion' ),
			'curso'     => __( 'Curso', 'capital-cultural-programacion' ),
		);
	}

	/**
	 * Inserts a term only if its slug does not exist.
	 *
	 * @param string $name     Term name.
	 * @param string $taxonomy Taxonomy.
	 * @param string $slug     Term slug.
	 */
	private static function maybe_insert_term( string $name, string $taxonomy, string $slug ): void {
		if ( term_exists( $slug, $taxonomy ) ) {
			return;
		}

		wp_insert_term(
			$name,
			$taxonomy,
			array(
				'slug' => $slug,
			)
		);
	}

	/**
	 * Gets the first assigned term name.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy.
	 */
	public static function get_first_term_name( int $post_id, string $taxonomy ): string {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}

		return $terms[0]->name;
	}

	/**
	 * Gets the first assigned term object.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @return \WP_Term|null
	 */
	public static function get_first_term( int $post_id, string $taxonomy ): ?\WP_Term {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return null;
		}

		return $terms[0];
	}
}
