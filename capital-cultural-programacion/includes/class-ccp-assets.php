<?php
/**
 * Asset registration.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and enqueues plugin CSS and JavaScript.
 */
final class CCP_Assets {
	/**
	 * Registers hooks.
	 */
	public function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
	}

	/**
	 * Registers frontend assets without loading them globally.
	 */
	public function register_frontend(): void {
		wp_register_style(
			'ccp-frontend',
			CCP_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			CCP_VERSION
		);

		wp_register_script(
			'ccp-frontend',
			CCP_PLUGIN_URL . 'assets/js/frontend.js',
			array(),
			CCP_VERSION,
			true
		);
	}

	/**
	 * Enqueues frontend assets when a shortcode is rendered.
	 */
	public static function enqueue_frontend(): void {
		wp_enqueue_style( 'ccp-frontend' );
		wp_enqueue_script( 'ccp-frontend' );
	}

	/**
	 * Enqueues admin CSS on plugin screens.
	 *
	 * @param string $hook Current admin hook.
	 */
	public function enqueue_admin( string $hook ): void {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$is_plugin_screen = CCP_Post_Type::POST_TYPE === $screen->post_type
			|| false !== strpos( $screen->id, 'ccp-ayuda-shortcodes' )
			|| false !== strpos( $screen->id, CCP_Taxonomies::TAX_ESPACIO )
			|| false !== strpos( $screen->id, CCP_Taxonomies::TAX_CATEGORIA );

		if ( ! $is_plugin_screen ) {
			return;
		}

		wp_enqueue_style(
			'ccp-admin',
			CCP_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			CCP_VERSION
		);
	}
}
