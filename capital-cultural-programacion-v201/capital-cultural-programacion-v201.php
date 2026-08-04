<?php
/**
 * Plugin Name: Capital Cultural – Programación de Museos
 * Description: Gestión centralizada de muestras, actividades, talleres y cursos de los museos y espacios culturales de la ciudad de Santa Fe.
 * Version: 3.0.0
 * Author: Esteban Maximiliano Córdoba
 * Text Domain: capital-cultural-programacion
 * Domain Path: /languages
 * Requires at least: 6.5
 * Requires PHP: 8.0
 *
 * @package CapitalCulturalProgramacion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CCP_VERSION', '3.0.0' );
define( 'CCP_PLUGIN_FILE', __FILE__ );
define( 'CCP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CCP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CCP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once CCP_PLUGIN_DIR . 'includes/class-ccp-admin-notices.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-date-formatter.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-status.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-settings.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-assets.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-taxonomies.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-post-type.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-meta-boxes.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-shortcodes.php';
require_once CCP_PLUGIN_DIR . 'includes/class-ccp-plugin.php';

register_activation_hook(
	__FILE__,
	static function () {
		\CapitalCultural\Programacion\CCP_Plugin::activate();
	}
);

\CapitalCultural\Programacion\CCP_Plugin::instance()->init();
