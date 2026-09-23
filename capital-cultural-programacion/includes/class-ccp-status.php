<?php
/**
 * Proposal status calculation.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculates automatic and manual temporal states.
 */
final class CCP_Status {
	/**
	 * Manual statuses keyed by meta value.
	 *
	 * @return array<string,array{label:string,key:string,order:int}>
	 */
	public static function manual_options(): array {
		return array(
			'automatico'   => array(
				'label' => __( 'Automático', 'capital-cultural-programacion' ),
				'key'   => 'automatico',
				'order' => 0,
			),
			'proximamente' => array(
				'label' => __( 'PRÓXIMAMENTE', 'capital-cultural-programacion' ),
				'key'   => 'proximamente',
				'order' => 20,
			),
			'activa'       => array(
				'label' => __( 'ACTIVA', 'capital-cultural-programacion' ),
				'key'   => 'activa',
				'order' => 10,
			),
			'ya-sucedio'   => array(
				'label' => __( 'FINALIZADA', 'capital-cultural-programacion' ),
				'key'   => 'ya-sucedio',
				'order' => 60,
			),
			'permanente'   => array(
				'label' => __( 'PERMANENTE', 'capital-cultural-programacion' ),
				'key'   => 'permanente',
				'order' => 30,
			),
			'suspendida'   => array(
				'label' => __( 'SUSPENDIDA', 'capital-cultural-programacion' ),
				'key'   => 'suspendida',
				'order' => 50,
			),
			'reprogramada' => array(
				'label' => __( 'REPROGRAMADA', 'capital-cultural-programacion' ),
				'key'   => 'reprogramada',
				'order' => 40,
			),
		);
	}

	/**
	 * Gets the display status for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return array{label:string,key:string,order:int}
	 */
	public static function get_status( int $post_id ): array {
		$manual  = (string) get_post_meta( $post_id, '_ccp_estado_manual', true );
		$options = self::manual_options();

		if ( '' !== $manual && 'automatico' !== $manual && isset( $options[ $manual ] ) ) {
			return $options[ $manual ];
		}

		$start = (string) get_post_meta( $post_id, '_ccp_fecha_inicio', true );
		$end   = (string) get_post_meta( $post_id, '_ccp_fecha_fin', true );
		if ( '' === $end && has_term( 'actividad', CCP_Taxonomies::TAX_CATEGORIA, $post_id ) ) {
			$end = $start;
		}

		if ( ! CCP_Date_Formatter::is_valid_date( $start ) ) {
			return $options['ya-sucedio'];
		}

		$today = current_datetime()->format( 'Y-m-d' );

		if ( $today < $start ) {
			return $options['proximamente'];
		}

		if ( '' !== $end && CCP_Date_Formatter::is_valid_date( $end ) && $today > $end ) {
			return $options['ya-sucedio'];
		}

		return $options['activa'];
	}

	/**
	 * Returns the sortable reference date for a status.
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $status_key Status key.
	 */
	public static function sort_date( int $post_id, string $status_key ): string {
		$start = (string) get_post_meta( $post_id, '_ccp_fecha_inicio', true );
		$end   = (string) get_post_meta( $post_id, '_ccp_fecha_fin', true );
		if ( '' === $end && has_term( 'actividad', CCP_Taxonomies::TAX_CATEGORIA, $post_id ) ) {
			$end = $start;
		}

		if ( 'ya-sucedio' === $status_key && CCP_Date_Formatter::is_valid_date( $end ) ) {
			return $end;
		}

		return CCP_Date_Formatter::is_valid_date( $start ) ? $start : '9999-12-31';
	}

	/**
	 * Sorts posts by plugin ordering rules.
	 *
	 * @param \WP_Post[] $posts Posts.
	 * @return \WP_Post[]
	 */
	public static function sort_posts( array $posts ): array {
		usort(
			$posts,
			static function ( \WP_Post $a, \WP_Post $b ): int {
				$status_a = self::get_status( $a->ID );
				$status_b = self::get_status( $b->ID );

				if ( $status_a['order'] !== $status_b['order'] ) {
					return $status_a['order'] <=> $status_b['order'];
				}

				$featured_a = get_post_meta( $a->ID, '_ccp_destacada', true ) ? 0 : 1;
				$featured_b = get_post_meta( $b->ID, '_ccp_destacada', true ) ? 0 : 1;

				if ( $featured_a !== $featured_b ) {
					return $featured_a <=> $featured_b;
				}

				$date_a = self::sort_date( $a->ID, $status_a['key'] );
				$date_b = self::sort_date( $b->ID, $status_b['key'] );

				if ( 'ya-sucedio' === $status_a['key'] ) {
					return strcmp( $date_b, $date_a );
				}

				return strcmp( $date_a, $date_b );
			}
		);

		return self::group_posts_by_parent_show( $posts );
	}

	/**
	 * Places related activities immediately before their parent show.
	 *
	 * An explicit relation takes priority. As a fallback, a one-day activity is
	 * associated with a show in the same space when its date falls within the
	 * show's start and end dates.
	 *
	 * @param \WP_Post[] $posts Already sorted posts.
	 * @return \WP_Post[]
	 */
	private static function group_posts_by_parent_show( array $posts ): array {
		$posts_by_id = array();
		$shows       = array();
		$show_ids    = array();

		foreach ( $posts as $post ) {
			$posts_by_id[ $post->ID ] = $post;
			if ( has_term( 'muestra', CCP_Taxonomies::TAX_CATEGORIA, $post ) ) {
				$shows[]              = $post;
				$show_ids[ $post->ID ] = true;
			}
		}

		$children = array();
		foreach ( $posts as $post ) {
			if ( isset( $show_ids[ $post->ID ] ) ) {
				continue;
			}

			$parent_id = absint( get_post_meta( $post->ID, '_ccp_muestra_principal', true ) );
			if ( ! isset( $posts_by_id[ $parent_id ] ) || ! has_term( 'muestra', CCP_Taxonomies::TAX_CATEGORIA, $parent_id ) ) {
				$parent_id = self::infer_parent_show( $post, $shows );
			}

			if ( $parent_id ) {
				$children[ $parent_id ][] = $post;
			}
		}

		if ( empty( $children ) ) {
			return $posts;
		}

		$child_ids = array();
		foreach ( $children as $related_posts ) {
			foreach ( $related_posts as $related_post ) {
				$child_ids[ $related_post->ID ] = true;
			}
		}

		$grouped = array();
		foreach ( $posts as $post ) {
			if ( isset( $child_ids[ $post->ID ] ) ) {
				continue;
			}

			if ( isset( $children[ $post->ID ] ) ) {
				array_push( $grouped, ...$children[ $post->ID ] );
			}
			$grouped[] = $post;
		}

		return $grouped;
	}

	/**
	 * Finds a matching parent show for a one-day activity.
	 *
	 * @param \WP_Post   $post  Activity post.
	 * @param \WP_Post[] $shows Available shows.
	 */
	private static function infer_parent_show( \WP_Post $post, array $shows ): int {
		if ( ! has_term( 'actividad', CCP_Taxonomies::TAX_CATEGORIA, $post ) ) {
			return 0;
		}

		$activity_start = (string) get_post_meta( $post->ID, '_ccp_fecha_inicio', true );
		$activity_end   = (string) get_post_meta( $post->ID, '_ccp_fecha_fin', true );
		if ( ! CCP_Date_Formatter::is_valid_date( $activity_start ) || ( '' !== $activity_end && $activity_end !== $activity_start ) ) {
			return 0;
		}

		$activity_spaces = wp_get_post_terms( $post->ID, CCP_Taxonomies::TAX_ESPACIO, array( 'fields' => 'ids' ) );
		if ( is_wp_error( $activity_spaces ) || empty( $activity_spaces ) ) {
			return 0;
		}

		foreach ( $shows as $show ) {
			$show_start = (string) get_post_meta( $show->ID, '_ccp_fecha_inicio', true );
			$show_end   = (string) get_post_meta( $show->ID, '_ccp_fecha_fin', true );
			if ( ! CCP_Date_Formatter::is_valid_date( $show_start ) || ! CCP_Date_Formatter::is_valid_date( $show_end ) ) {
				continue;
			}

			$show_spaces = wp_get_post_terms( $show->ID, CCP_Taxonomies::TAX_ESPACIO, array( 'fields' => 'ids' ) );
			if ( is_wp_error( $show_spaces ) || empty( array_intersect( $activity_spaces, $show_spaces ) ) ) {
				continue;
			}

			if ( $activity_start >= $show_start && $activity_start <= $show_end ) {
				return $show->ID;
			}
		}

		return 0;
	}
}
