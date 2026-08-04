<?php
/**
 * Main plugin coordinator.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wires all plugin modules.
 */
final class CCP_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Returns the plugin singleton.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers WordPress hooks.
	 */
	public function init(): void {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		( new CCP_Settings() )->init();
		( new CCP_Post_Type() )->init();
		( new CCP_Taxonomies() )->init();
		( new CCP_Meta_Boxes() )->init();
		( new CCP_Shortcodes() )->init();
		( new CCP_Assets() )->init();
		( new CCP_Admin_Notices() )->init();
	}

	/**
	 * Loads translations.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'capital-cultural-programacion',
			false,
			dirname( CCP_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Activation tasks.
	 */
	public static function activate(): void {
		( new CCP_Post_Type() )->register();
		( new CCP_Taxonomies() )->register();
		CCP_Taxonomies::create_initial_terms();
		update_option( 'ccp_version', CCP_VERSION );
		flush_rewrite_rules();
	}
}
