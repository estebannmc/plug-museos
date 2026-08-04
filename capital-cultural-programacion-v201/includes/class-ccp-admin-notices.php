<?php
/**
 * Admin notices helper.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores and renders clear admin notices.
 */
final class CCP_Admin_Notices {
	private const TRANSIENT_PREFIX = 'ccp_admin_notices_';

	/**
	 * Registers hooks.
	 */
	public function init(): void {
		add_action( 'admin_notices', array( $this, 'render' ) );
	}

	/**
	 * Adds a notice for the current admin user.
	 *
	 * @param string $message Notice message.
	 * @param string $type    Notice type.
	 */
	public static function add( string $message, string $type = 'error' ): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		$key     = self::TRANSIENT_PREFIX . $user_id;
		$notices = get_transient( $key );
		if ( ! is_array( $notices ) ) {
			$notices = array();
		}

		$notices[] = array(
			'message' => $message,
			'type'    => $type,
		);

		set_transient( $key, $notices, MINUTE_IN_SECONDS * 5 );
	}

	/**
	 * Renders stored notices.
	 */
	public function render(): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		$key     = self::TRANSIENT_PREFIX . $user_id;
		$notices = get_transient( $key );
		if ( empty( $notices ) || ! is_array( $notices ) ) {
			return;
		}

		delete_transient( $key );

		foreach ( $notices as $notice ) {
			$type    = ! empty( $notice['type'] ) ? sanitize_html_class( $notice['type'] ) : 'error';
			$message = ! empty( $notice['message'] ) ? (string) $notice['message'] : '';
			if ( '' === $message ) {
				continue;
			}

			printf(
				'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
				esc_attr( $type ),
				esc_html( $message )
			);
		}
	}
}
