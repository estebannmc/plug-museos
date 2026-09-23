<?php
/**
 * Plugin settings screen.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles global plugin settings.
 */
final class CCP_Settings {
	public const OPTION_NAME = 'ccp_settings';

	/**
	 * Registers hooks.
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Default settings.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults(): array {
		return array(
			'slider_autoplay'    => true,
			'slider_interval'    => 4500,
			'slider_pause_hover' => true,
			'slider_limit'       => -1,
			'slider_categories'  => array(),
			'slider_statuses'    => array( 'activa', 'proximamente' ),
			'slider_title'       => '',
		);
	}

	/**
	 * Returns sanitized settings with defaults.
	 *
	 * @return array<string,mixed>
	 */
	public static function get(): array {
		$stored = get_option( self::OPTION_NAME, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return self::sanitize( array_merge( self::defaults(), $stored ) );
	}

	/**
	 * Adds the settings page.
	 */
	public function settings_page(): void {
		add_submenu_page(
			'edit.php?post_type=' . CCP_Post_Type::POST_TYPE,
			__( 'Ajustes del slider', 'capital-cultural-programacion' ),
			__( 'Ajustes del slider', 'capital-cultural-programacion' ),
			'manage_options',
			'ccp-ajustes-slider',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registers the WordPress option.
	 */
	public function register_settings(): void {
		register_setting(
			'ccp_settings',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Renders the settings page.
	 */
	public function render_settings_page(): void {
		$settings   = self::get();
		$categories = self::available_categories();
		?>
		<div class="wrap ccp-settings-page">
			<h1><?php esc_html_e( 'Ajustes del slider', 'capital-cultural-programacion' ); ?></h1>
			<form method="post" action="options.php" class="ccp-admin-fields">
				<?php settings_fields( 'ccp_settings' ); ?>
				<p class="ccp-admin-field ccp-admin-field--inline">
					<label for="ccp-slider-autoplay">
						<input id="ccp-slider-autoplay" type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_autoplay]" value="1" <?php checked( (bool) $settings['slider_autoplay'] ); ?>>
						<?php esc_html_e( 'Activar movimiento automático', 'capital-cultural-programacion' ); ?>
					</label>
				</p>
				<p class="ccp-admin-field">
					<label for="ccp-slider-interval"><?php esc_html_e( 'Velocidad del movimiento', 'capital-cultural-programacion' ); ?></label>
					<input id="ccp-slider-interval" type="number" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_interval]" min="2000" max="15000" step="500" value="<?php echo esc_attr( (string) $settings['slider_interval'] ); ?>">
					<span class="description"><?php esc_html_e( 'Tiempo entre slides en milisegundos. Recomendado: 4500.', 'capital-cultural-programacion' ); ?></span>
				</p>
				<p class="ccp-admin-field ccp-admin-field--inline">
					<label for="ccp-slider-pause-hover">
						<input id="ccp-slider-pause-hover" type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_pause_hover]" value="1" <?php checked( (bool) $settings['slider_pause_hover'] ); ?>>
						<?php esc_html_e( 'Pausar cuando el cursor o el foco están sobre el slider', 'capital-cultural-programacion' ); ?>
					</label>
				</p>
				<p class="ccp-admin-field">
					<label for="ccp-slider-limit"><?php esc_html_e( 'Cantidad máxima de slides', 'capital-cultural-programacion' ); ?></label>
					<input id="ccp-slider-limit" type="number" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_limit]" min="-1" max="50" value="<?php echo esc_attr( (string) $settings['slider_limit'] ); ?>">
					<span class="description"><?php esc_html_e( 'Usa -1 para mostrar todas las propuestas que coincidan.', 'capital-cultural-programacion' ); ?></span>
				</p>
				<fieldset class="ccp-admin-field">
					<legend><?php esc_html_e( 'Tags/categorías incluidas por defecto', 'capital-cultural-programacion' ); ?></legend>
					<?php foreach ( $categories as $slug => $label ) : ?>
						<label class="ccp-admin-check">
							<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_categories][]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( $slug, $settings['slider_categories'], true ) ); ?>>
							<?php echo esc_html( $label ); ?>
						</label>
					<?php endforeach; ?>
					<span class="description"><?php esc_html_e( 'Se usan cuando el shortcode no fuerza una categoría propia. Si no seleccionás ninguno, se muestran todas.', 'capital-cultural-programacion' ); ?></span>
				</fieldset>
				<fieldset class="ccp-admin-field">
					<legend><?php esc_html_e( 'Estados incluidos', 'capital-cultural-programacion' ); ?></legend>
					<label class="ccp-admin-check">
						<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_statuses][]" value="activa" <?php checked( in_array( 'activa', $settings['slider_statuses'], true ) ); ?>>
						<?php esc_html_e( 'Activas', 'capital-cultural-programacion' ); ?>
					</label>
					<label class="ccp-admin-check">
						<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_statuses][]" value="proximamente" <?php checked( in_array( 'proximamente', $settings['slider_statuses'], true ) ); ?>>
						<?php esc_html_e( 'Próximamente', 'capital-cultural-programacion' ); ?>
					</label>
					<span class="description"><?php esc_html_e( 'Por defecto se muestran activas y próximamente. Desmarcá una opción si querés dejar afuera ese estado.', 'capital-cultural-programacion' ); ?></span>
				</fieldset>
				<p class="ccp-admin-field">
					<label for="ccp-slider-title"><?php esc_html_e( 'Título por defecto', 'capital-cultural-programacion' ); ?></label>
					<input id="ccp-slider-title" type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slider_title]" value="<?php echo esc_attr( (string) $settings['slider_title'] ); ?>">
					<span class="description"><?php esc_html_e( 'Dejalo vacío para usar el título de cada shortcode.', 'capital-cultural-programacion' ); ?></span>
				</p>
				<?php submit_button( __( 'Guardar ajustes', 'capital-cultural-programacion' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitizes settings.
	 *
	 * @param mixed $value Raw option value.
	 * @return array<string,mixed>
	 */
	public static function sanitize( mixed $value ): array {
		$defaults = self::defaults();
		$value    = is_array( $value ) ? $value : array();

		$categories = isset( $value['slider_categories'] ) && is_array( $value['slider_categories'] )
			? array_map( 'sanitize_title', $value['slider_categories'] )
			: array();
		$categories = array_values( array_intersect( $categories, array_keys( self::available_categories() ) ) );

		$statuses = isset( $value['slider_statuses'] ) && is_array( $value['slider_statuses'] )
			? array_map( 'sanitize_key', $value['slider_statuses'] )
			: array();
		$statuses = array_values( array_intersect( $statuses, array( 'activa', 'proximamente' ) ) );

		return array(
			'slider_autoplay'    => ! empty( $value['slider_autoplay'] ),
			'slider_interval'    => max( 2000, min( 15000, absint( $value['slider_interval'] ?? $defaults['slider_interval'] ) ) ),
			'slider_pause_hover' => ! empty( $value['slider_pause_hover'] ),
			'slider_limit'       => self::sanitize_limit( $value['slider_limit'] ?? $defaults['slider_limit'] ),
			'slider_categories'  => $categories,
			'slider_statuses'    => empty( $statuses ) ? $defaults['slider_statuses'] : $statuses,
			'slider_title'       => sanitize_text_field( (string) ( $value['slider_title'] ?? '' ) ),
		);
	}

	/**
	 * Sanitizes a global slider limit.
	 *
	 * @param mixed $value Raw limit.
	 */
	private static function sanitize_limit( mixed $value ): int {
		$limit = (int) $value;
		if ( -1 === $limit ) {
			return -1;
		}

		return max( 1, min( 50, $limit ) );
	}

	/**
	 * Returns category slugs and labels available for slider settings.
	 *
	 * @return array<string,string>
	 */
	private static function available_categories(): array {
		if ( ! taxonomy_exists( CCP_Taxonomies::TAX_CATEGORIA ) ) {
			return CCP_Taxonomies::initial_categories();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => CCP_Taxonomies::TAX_CATEGORIA,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return CCP_Taxonomies::initial_categories();
		}

		$categories = array();
		foreach ( $terms as $term ) {
			$categories[ $term->slug ] = $term->name;
		}

		return $categories;
	}
}
