<?php
/**
 * Spanish date formatting helpers.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formats proposal dates without relying on server locale.
 */
final class CCP_Date_Formatter {
	/**
	 * Month names in Spanish.
	 *
	 * @return array<int,string>
	 */
	private static function months(): array {
		return array(
			1  => __( 'enero', 'capital-cultural-programacion' ),
			2  => __( 'febrero', 'capital-cultural-programacion' ),
			3  => __( 'marzo', 'capital-cultural-programacion' ),
			4  => __( 'abril', 'capital-cultural-programacion' ),
			5  => __( 'mayo', 'capital-cultural-programacion' ),
			6  => __( 'junio', 'capital-cultural-programacion' ),
			7  => __( 'julio', 'capital-cultural-programacion' ),
			8  => __( 'agosto', 'capital-cultural-programacion' ),
			9  => __( 'septiembre', 'capital-cultural-programacion' ),
			10 => __( 'octubre', 'capital-cultural-programacion' ),
			11 => __( 'noviembre', 'capital-cultural-programacion' ),
			12 => __( 'diciembre', 'capital-cultural-programacion' ),
		);
	}

	/**
	 * Validates a date in Y-m-d format.
	 *
	 * @param string $date Date.
	 */
	public static function is_valid_date( string $date ): bool {
		if ( '' === $date ) {
			return false;
		}

		$parts = explode( '-', $date );
		if ( 3 !== count( $parts ) ) {
			return false;
		}

		return checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] );
	}

	/**
	 * Formats a proposal date range.
	 *
	 * @param string $start Start date.
	 * @param string $end   End date.
	 */
	public static function format_range( string $start, string $end = '' ): string {
		if ( ! self::is_valid_date( $start ) ) {
			return '';
		}

		if ( '' !== $end && ! self::is_valid_date( $end ) ) {
			$end = '';
		}

		$start_parts = self::parts( $start );
		if ( '' === $end || $start === $end ) {
			if ( '' === $end ) {
				return sprintf(
					/* translators: %s: formatted date. */
					__( 'Desde el %s', 'capital-cultural-programacion' ),
					self::format_full_date( $start_parts )
				);
			}

			return self::format_full_date( $start_parts );
		}

		$end_parts = self::parts( $end );

		if ( $start_parts['year'] === $end_parts['year'] && $start_parts['month'] === $end_parts['month'] ) {
			return sprintf(
				/* translators: 1: start day, 2: end day, 3: month, 4: year. */
				__( 'Del %1$d al %2$d de %3$s de %4$d', 'capital-cultural-programacion' ),
				$start_parts['day'],
				$end_parts['day'],
				self::month_name( $start_parts['month'] ),
				$start_parts['year']
			);
		}

		if ( $start_parts['year'] === $end_parts['year'] ) {
			return sprintf(
				/* translators: 1: start day, 2: start month, 3: end day, 4: end month, 5: year. */
				__( 'Del %1$d de %2$s al %3$d de %4$s de %5$d', 'capital-cultural-programacion' ),
				$start_parts['day'],
				self::month_name( $start_parts['month'] ),
				$end_parts['day'],
				self::month_name( $end_parts['month'] ),
				$start_parts['year']
			);
		}

		return sprintf(
			/* translators: 1: start date, 2: end date. */
			__( 'Del %1$s al %2$s', 'capital-cultural-programacion' ),
			self::format_full_date( $start_parts ),
			self::format_full_date( $end_parts )
		);
	}

	/**
	 * Returns date parts.
	 *
	 * @param string $date Date.
	 * @return array{year:int,month:int,day:int}
	 */
	private static function parts( string $date ): array {
		$parts = explode( '-', $date );

		return array(
			'year'  => (int) $parts[0],
			'month' => (int) $parts[1],
			'day'   => (int) $parts[2],
		);
	}

	/**
	 * Formats a full date.
	 *
	 * @param array{year:int,month:int,day:int} $parts Date parts.
	 */
	private static function format_full_date( array $parts ): string {
		return sprintf(
			/* translators: 1: day, 2: month, 3: year. */
			__( '%1$d de %2$s de %3$d', 'capital-cultural-programacion' ),
			$parts['day'],
			self::month_name( $parts['month'] ),
			$parts['year']
		);
	}

	/**
	 * Returns a month name.
	 *
	 * @param int $month Month number.
	 */
	private static function month_name( int $month ): string {
		$months = self::months();

		return $months[ $month ] ?? '';
	}
}
