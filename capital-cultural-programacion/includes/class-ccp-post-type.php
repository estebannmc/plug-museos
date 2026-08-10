<?php
/**
 * Custom post type registration and admin list columns.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Programación de Museos proposals.
 */
final class CCP_Post_Type {
	public const POST_TYPE = 'cc_propuesta';

	/**
	 * Registers hooks.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'render_column' ), 10, 2 );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'sort_admin_columns' ) );
		add_action( 'restrict_manage_posts', array( $this, 'admin_filters' ) );
		add_filter( 'parse_query', array( $this, 'apply_admin_filters' ) );
	}

	/**
	 * Registers the custom post type.
	 */
	public function register(): void {
		$labels = array(
			'name'               => __( 'Programación de Museos', 'capital-cultural-programacion' ),
			'singular_name'      => __( 'Propuesta', 'capital-cultural-programacion' ),
			'menu_name'          => __( 'Programación de Museos', 'capital-cultural-programacion' ),
			'name_admin_bar'     => __( 'Propuesta cultural', 'capital-cultural-programacion' ),
			'add_new'            => __( 'Agregar nueva', 'capital-cultural-programacion' ),
			'add_new_item'       => __( 'Agregar nueva propuesta', 'capital-cultural-programacion' ),
			'new_item'           => __( 'Nueva propuesta', 'capital-cultural-programacion' ),
			'edit_item'          => __( 'Editar propuesta', 'capital-cultural-programacion' ),
			'view_item'          => __( 'Ver propuesta', 'capital-cultural-programacion' ),
			'all_items'          => __( 'Todas las propuestas', 'capital-cultural-programacion' ),
			'search_items'       => __( 'Buscar propuestas', 'capital-cultural-programacion' ),
			'not_found'          => __( 'No se encontraron propuestas.', 'capital-cultural-programacion' ),
			'not_found_in_trash' => __( 'No hay propuestas en la papelera.', 'capital-cultural-programacion' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'        => $labels,
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-art',
				'has_archive'   => false,
				'rewrite'       => false,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author' ),
				'taxonomies'    => array( CCP_Taxonomies::TAX_ESPACIO, CCP_Taxonomies::TAX_CATEGORIA ),
				'capability_type' => 'post',
			)
		);
	}

	/**
	 * Defines admin columns.
	 *
	 * @param array<string,string> $columns Columns.
	 * @return array<string,string>
	 */
	public function columns( array $columns ): array {
		return array(
			'cb'               => $columns['cb'] ?? '',
			'ccp_image'        => __( 'Imagen', 'capital-cultural-programacion' ),
			'title'            => __( 'Titulo', 'capital-cultural-programacion' ),
			'ccp_espacio'      => __( 'Espacio', 'capital-cultural-programacion' ),
			'ccp_categoria'    => __( 'Categoría', 'capital-cultural-programacion' ),
			'ccp_fecha_inicio' => __( 'Fecha de inicio', 'capital-cultural-programacion' ),
			'ccp_fecha_fin'    => __( 'Fecha de finalización', 'capital-cultural-programacion' ),
			'ccp_estado'       => __( 'Estado', 'capital-cultural-programacion' ),
			'ccp_destacada'    => __( 'Destacada', 'capital-cultural-programacion' ),
			'date'             => $columns['date'] ?? __( 'Fecha', 'capital-cultural-programacion' ),
		);
	}

	/**
	 * Renders admin column content.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 */
	public function render_column( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'ccp_image':
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, array( 56, 56 ), array( 'class' => 'ccp-admin-thumb' ) );
				} else {
					echo '<span class="ccp-admin-thumb ccp-admin-thumb--empty" aria-hidden="true"></span>';
				}
				break;
			case 'ccp_espacio':
				echo esc_html( CCP_Taxonomies::get_first_term_name( $post_id, CCP_Taxonomies::TAX_ESPACIO ) );
				break;
			case 'ccp_categoria':
				echo esc_html( CCP_Taxonomies::get_first_term_name( $post_id, CCP_Taxonomies::TAX_CATEGORIA ) );
				break;
			case 'ccp_fecha_inicio':
				echo esc_html( get_post_meta( $post_id, '_ccp_fecha_inicio', true ) );
				break;
			case 'ccp_fecha_fin':
				echo esc_html( get_post_meta( $post_id, '_ccp_fecha_fin', true ) );
				break;
			case 'ccp_estado':
				$status = CCP_Status::get_status( $post_id );
				echo esc_html( $status['label'] );
				break;
			case 'ccp_destacada':
				echo get_post_meta( $post_id, '_ccp_destacada', true ) ? esc_html__( 'Si', 'capital-cultural-programacion' ) : esc_html__( 'No', 'capital-cultural-programacion' );
				break;
		}
	}

	/**
	 * Registers sortable date columns.
	 *
	 * @param array<string,string> $columns Columns.
	 * @return array<string,string>
	 */
	public function sortable_columns( array $columns ): array {
		$columns['ccp_fecha_inicio'] = 'ccp_fecha_inicio';
		$columns['ccp_fecha_fin']    = 'ccp_fecha_fin';

		return $columns;
	}

	/**
	 * Applies admin sorting for date columns.
	 *
	 * @param \WP_Query $query Query.
	 */
	public function sort_admin_columns( \WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() || self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( 'ccp_fecha_inicio' === $orderby ) {
			$query->set( 'meta_key', '_ccp_fecha_inicio' );
			$query->set( 'orderby', 'meta_value' );
		}

		if ( 'ccp_fecha_fin' === $orderby ) {
			$query->set( 'meta_key', '_ccp_fecha_fin' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/**
	 * Adds taxonomy filters to the admin list.
	 */
	public function admin_filters(): void {
		global $typenow;

		if ( self::POST_TYPE !== $typenow ) {
			return;
		}

		$this->taxonomy_dropdown( CCP_Taxonomies::TAX_ESPACIO, __( 'Todos los espacios', 'capital-cultural-programacion' ) );
		$this->taxonomy_dropdown( CCP_Taxonomies::TAX_CATEGORIA, __( 'Todas las categorías', 'capital-cultural-programacion' ) );
	}

	/**
	 * Renders a taxonomy dropdown.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $label    Default label.
	 */
	private function taxonomy_dropdown( string $taxonomy, string $label ): void {
		$selected = isset( $_GET[ $taxonomy ] ) ? sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		wp_dropdown_categories(
			array(
				'show_option_all' => $label,
				'taxonomy'        => $taxonomy,
				'name'            => $taxonomy,
				'orderby'         => 'name',
				'selected'        => $selected,
				'hierarchical'    => true,
				'depth'           => 3,
				'show_count'      => false,
				'hide_empty'      => false,
				'value_field'     => 'slug',
			)
		);
	}

	/**
	 * Applies taxonomy filters from admin dropdowns.
	 *
	 * @param \WP_Query $query Query.
	 */
	public function apply_admin_filters( \WP_Query $query ): void {
		global $pagenow;

		if ( ! is_admin() || 'edit.php' !== $pagenow || self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		foreach ( array( CCP_Taxonomies::TAX_ESPACIO, CCP_Taxonomies::TAX_CATEGORIA ) as $taxonomy ) {
			if ( empty( $_GET[ $taxonomy ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				continue;
			}

			$term = sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$query->query_vars[ $taxonomy ] = $term;
		}
	}
}
