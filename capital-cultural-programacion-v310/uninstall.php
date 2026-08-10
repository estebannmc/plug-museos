<?php
/**
 * Uninstall handling for Capital Cultural – Programación de Museos.
 *
 * By default this file preserves proposals, taxonomies and metadata.
 * To allow a complete cleanup, define CCP_DELETE_ALL_DATA as true before uninstalling
 * or return true from the ccp_delete_all_data_on_uninstall filter.
 *
 * @package CapitalCulturalProgramacion
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$delete_all = defined( 'CCP_DELETE_ALL_DATA' ) && CCP_DELETE_ALL_DATA;

if ( function_exists( 'apply_filters' ) ) {
	$delete_all = (bool) apply_filters( 'ccp_delete_all_data_on_uninstall', $delete_all );
}

if ( ! $delete_all ) {
	return;
}

$posts = get_posts(
	array(
		'post_type'      => 'cc_propuesta',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);

foreach ( $posts as $post_id ) {
	wp_delete_post( (int) $post_id, true );
}

foreach ( array( 'cc_espacio', 'cc_categoria' ) as $taxonomy ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);

	if ( is_wp_error( $terms ) ) {
		continue;
	}

	foreach ( $terms as $term_id ) {
		wp_delete_term( (int) $term_id, $taxonomy );
	}
}

delete_option( 'ccp_version' );
