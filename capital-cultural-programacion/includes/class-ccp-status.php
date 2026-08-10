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
				'label' => __( 'YA SUCEDIÓ', 'capital-cultural-programacion' ),
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

		return $posts;
	}
}
