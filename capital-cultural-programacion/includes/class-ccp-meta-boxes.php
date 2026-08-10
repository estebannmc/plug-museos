<?php
/**
 * Proposal meta boxes and admin validation.
 *
 * @package CapitalCulturalProgramacion
 */

namespace CapitalCultural\Programacion;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles custom proposal information fields.
 */
final class CCP_Meta_Boxes {
	private const NONCE_ACTION = 'ccp_save_propuesta';
	private const NONCE_NAME   = 'ccp_propuesta_nonce';

	/**
	 * Registers hooks.
	 */
	public function init(): void {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_' . CCP_Post_Type::POST_TYPE, array( $this, 'save' ), 10, 2 );
		add_filter( 'wp_insert_post_data', array( $this, 'validate_before_publish' ), 20, 2 );
		add_action( 'admin_menu', array( $this, 'help_page' ) );
	}

	/**
	 * Adds proposal meta boxes.
	 */
	public function add_meta_boxes(): void {
		add_meta_box(
			'ccp-info-propuesta',
			__( 'Información de la propuesta', 'capital-cultural-programacion' ),
			array( $this, 'render_info_box' ),
			CCP_Post_Type::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'ccp-espacio-propuesta',
			__( 'Espacio cultural', 'capital-cultural-programacion' ),
			array( $this, 'render_space_box' ),
			CCP_Post_Type::POST_TYPE,
			'side',
			'high'
		);

		add_meta_box(
			'ccp-categoria-propuesta',
			__( 'Categoría principal', 'capital-cultural-programacion' ),
			array( $this, 'render_category_box' ),
			CCP_Post_Type::POST_TYPE,
			'side',
			'high'
		);

		add_meta_box(
			'ccp-ayuda-shortcodes-box',
			__( 'Ayuda y shortcodes', 'capital-cultural-programacion' ),
			array( $this, 'render_help_box' ),
			CCP_Post_Type::POST_TYPE,
			'side',
			'low'
		);
	}

	/**
	 * Renders the proposal information fields.
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function render_info_box( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		$values = array(
			'_ccp_fecha_inicio'  => (string) get_post_meta( $post->ID, '_ccp_fecha_inicio', true ),
			'_ccp_fecha_fin'     => (string) get_post_meta( $post->ID, '_ccp_fecha_fin', true ),
			'_ccp_horarios'      => (string) get_post_meta( $post->ID, '_ccp_horarios', true ),
			'_ccp_ubicacion'     => (string) get_post_meta( $post->ID, '_ccp_ubicacion', true ),
			'_ccp_enlace'        => (string) get_post_meta( $post->ID, '_ccp_enlace', true ),
			'_ccp_texto_boton'   => (string) get_post_meta( $post->ID, '_ccp_texto_boton', true ),
			'_ccp_estado_manual' => (string) get_post_meta( $post->ID, '_ccp_estado_manual', true ),
		);

		if ( '' === $values['_ccp_texto_boton'] ) {
			$values['_ccp_texto_boton'] = __( 'Más información', 'capital-cultural-programacion' );
		}

		if ( '' === $values['_ccp_estado_manual'] ) {
			$values['_ccp_estado_manual'] = 'automatico';
		}

		?>
		<div class="ccp-admin-fields">
			<p class="ccp-admin-field">
				<label for="ccp-fecha-inicio"><?php esc_html_e( 'Fecha de inicio', 'capital-cultural-programacion' ); ?> <span class="ccp-required">*</span></label>
				<input id="ccp-fecha-inicio" type="date" name="_ccp_fecha_inicio" value="<?php echo esc_attr( $values['_ccp_fecha_inicio'] ); ?>" required>
				<span class="description"><?php esc_html_e( 'Indica cuándo comienza la propuesta.', 'capital-cultural-programacion' ); ?></span>
			</p>
			<p class="ccp-admin-field">
				<label for="ccp-fecha-fin"><?php esc_html_e( 'Fecha de finalización', 'capital-cultural-programacion' ); ?></label>
				<input id="ccp-fecha-fin" type="date" name="_ccp_fecha_fin" value="<?php echo esc_attr( $values['_ccp_fecha_fin'] ); ?>">
				<span class="description"><?php esc_html_e( 'Dejala vacía cuando la propuesta no tenga una fecha de cierre definida.', 'capital-cultural-programacion' ); ?></span>
			</p>
			<p class="ccp-admin-field">
				<label for="ccp-horarios"><?php esc_html_e( 'Horarios', 'capital-cultural-programacion' ); ?></label>
				<input id="ccp-horarios" type="text" name="_ccp_horarios" value="<?php echo esc_attr( $values['_ccp_horarios'] ); ?>" placeholder="<?php esc_attr_e( 'Martes a viernes de 9 a 12:30 y de 15 a 19 horas', 'capital-cultural-programacion' ); ?>">
			</p>
			<p class="ccp-admin-field">
				<label for="ccp-ubicacion"><?php esc_html_e( 'Sala o ubicación', 'capital-cultural-programacion' ); ?></label>
				<input id="ccp-ubicacion" type="text" name="_ccp_ubicacion" value="<?php echo esc_attr( $values['_ccp_ubicacion'] ); ?>">
			</p>
			<p class="ccp-admin-field">
				<label for="ccp-enlace"><?php esc_html_e( 'Enlace externo', 'capital-cultural-programacion' ); ?></label>
				<input id="ccp-enlace" type="url" name="_ccp_enlace" value="<?php echo esc_url( $values['_ccp_enlace'] ); ?>" placeholder="https://">
			</p>
			<p class="ccp-admin-field">
				<label for="ccp-texto-boton"><?php esc_html_e( 'Texto del botón externo', 'capital-cultural-programacion' ); ?></label>
				<input id="ccp-texto-boton" type="text" name="_ccp_texto_boton" value="<?php echo esc_attr( $values['_ccp_texto_boton'] ); ?>">
			</p>
			<p class="ccp-admin-field ccp-admin-field--inline">
				<label for="ccp-destacada">
					<input id="ccp-destacada" type="checkbox" name="_ccp_destacada" value="1" <?php checked( (bool) get_post_meta( $post->ID, '_ccp_destacada', true ) ); ?>>
					<?php esc_html_e( 'Destacar propuesta', 'capital-cultural-programacion' ); ?>
				</label>
			</p>
			<p class="ccp-admin-field">
				<label for="ccp-estado-manual"><?php esc_html_e( 'Estado manual', 'capital-cultural-programacion' ); ?></label>
				<select id="ccp-estado-manual" name="_ccp_estado_manual">
					<?php foreach ( CCP_Status::manual_options() as $value => $status ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $values['_ccp_estado_manual'], $value ); ?>>
							<?php echo esc_html( $status['label'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<span class="description"><?php esc_html_e( 'Usa Automático para calcular el estado según las fechas.', 'capital-cultural-programacion' ); ?></span>
			</p>
		</div>
		<?php
	}

	/**
	 * Renders the single cultural space selector.
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function render_space_box( \WP_Post $post ): void {
		$this->render_taxonomy_select(
			$post->ID,
			CCP_Taxonomies::TAX_ESPACIO,
			'ccp_espacio',
			__( 'Selecciona el museo o espacio donde se realiza esta propuesta.', 'capital-cultural-programacion' )
		);
	}

	/**
	 * Renders the single category selector.
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function render_category_box( \WP_Post $post ): void {
		$this->render_taxonomy_select(
			$post->ID,
			CCP_Taxonomies::TAX_CATEGORIA,
			'ccp_categoria',
			__( 'Seleccioná la categoría principal que se mostrará como tag.', 'capital-cultural-programacion' )
		);
	}

	/**
	 * Renders the shortcodes help metabox.
	 */
	public function render_help_box(): void {
		echo '<p>' . esc_html__( 'Shortcodes principales para copiar en Elementor o en el editor:', 'capital-cultural-programacion' ) . '</p>';
		echo '<pre class="ccp-admin-shortcodes">' . esc_html( implode( "\n", self::shortcode_examples() ) ) . '</pre>';
	}

	/**
	 * Saves proposal metadata and single taxonomy terms.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 */
	public function save( int $post_id, \WP_Post $post ): void {
		if ( ! $this->can_save( $post_id ) ) {
			return;
		}

		$start = $this->posted_date( '_ccp_fecha_inicio' );
		$end   = $this->posted_date( '_ccp_fecha_fin' );

		if ( '' !== $end && '' !== $start && $end < $start ) {
			$end = '';
			CCP_Admin_Notices::add( __( 'La fecha de finalización no puede ser anterior a la fecha de inicio. Se guardó vacía.', 'capital-cultural-programacion' ), 'warning' );
		}

		update_post_meta( $post_id, '_ccp_fecha_inicio', $start );
		update_post_meta( $post_id, '_ccp_fecha_fin', $end );
		update_post_meta( $post_id, '_ccp_horarios', $this->posted_text( '_ccp_horarios' ) );
		update_post_meta( $post_id, '_ccp_ubicacion', $this->posted_text( '_ccp_ubicacion' ) );
		update_post_meta( $post_id, '_ccp_enlace', $this->posted_url( '_ccp_enlace' ) );
		update_post_meta( $post_id, '_ccp_texto_boton', $this->posted_text( '_ccp_texto_boton', __( 'Más información', 'capital-cultural-programacion' ) ) );
		update_post_meta( $post_id, '_ccp_destacada', isset( $_POST['_ccp_destacada'] ) ? '1' : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		update_post_meta( $post_id, '_ccp_estado_manual', $this->posted_status() );

		$this->save_single_term( $post_id, CCP_Taxonomies::TAX_ESPACIO, 'ccp_espacio' );
		$this->save_single_term( $post_id, CCP_Taxonomies::TAX_CATEGORIA, 'ccp_categoria' );
	}

	/**
	 * Forces invalid published proposals back to draft.
	 *
	 * @param array<string,mixed> $data    Post data.
	 * @param array<string,mixed> $postarr Raw post array.
	 * @return array<string,mixed>
	 */
	public function validate_before_publish( array $data, array $postarr ): array {
		if ( CCP_Post_Type::POST_TYPE !== ( $data['post_type'] ?? '' ) ) {
			return $data;
		}

		if ( ! in_array( $data['post_status'] ?? '', array( 'publish', 'future' ), true ) ) {
			return $data;
		}

		$errors  = array();
		$post_id = isset( $postarr['ID'] ) ? absint( $postarr['ID'] ) : 0;
		$title   = trim( wp_strip_all_tags( (string) ( $data['post_title'] ?? '' ) ) );
		$start   = $this->posted_date( '_ccp_fecha_inicio', $post_id );
		$end     = $this->posted_date( '_ccp_fecha_fin', $post_id );

		if ( '' === $title ) {
			$errors[] = __( 'Falta el titulo de la propuesta.', 'capital-cultural-programacion' );
		}

		if ( ! $this->has_posted_or_existing_term( $post_id, CCP_Taxonomies::TAX_ESPACIO, 'ccp_espacio' ) ) {
			$errors[] = __( 'Falta seleccionar un espacio cultural.', 'capital-cultural-programacion' );
		}

		if ( ! $this->has_posted_or_existing_term( $post_id, CCP_Taxonomies::TAX_CATEGORIA, 'ccp_categoria' ) ) {
			$errors[] = __( 'Falta seleccionar una categoría.', 'capital-cultural-programacion' );
		}

		if ( '' === $start ) {
			$errors[] = __( 'Falta cargar una fecha de inicio valida.', 'capital-cultural-programacion' );
		}

		if ( '' !== $end && '' !== $start && $end < $start ) {
			$errors[] = __( 'La fecha de finalización no puede ser anterior a la fecha de inicio.', 'capital-cultural-programacion' );
		}

		if ( empty( $errors ) ) {
			return $data;
		}

		$data['post_status'] = 'draft';
		foreach ( $errors as $error ) {
			CCP_Admin_Notices::add( $error, 'error' );
		}
		CCP_Admin_Notices::add( __( 'La propuesta se mantuvo como borrador hasta completar los datos obligatorios.', 'capital-cultural-programacion' ), 'warning' );

		return $data;
	}

	/**
	 * Adds the admin help page.
	 */
	public function help_page(): void {
		add_submenu_page(
			'edit.php?post_type=' . CCP_Post_Type::POST_TYPE,
			__( 'Ayuda y shortcodes', 'capital-cultural-programacion' ),
			__( 'Ayuda y shortcodes', 'capital-cultural-programacion' ),
			'edit_posts',
			'ccp-ayuda-shortcodes',
			array( $this, 'render_help_page' )
		);
	}

	/**
	 * Renders the admin help page.
	 */
	public function render_help_page(): void {
		?>
		<div class="wrap ccp-help-page">
			<h1><?php esc_html_e( 'Ayuda y shortcodes', 'capital-cultural-programacion' ); ?></h1>
			<p><?php esc_html_e( 'Copia estos shortcodes en un widget Shortcode de Elementor Free o en el editor de WordPress.', 'capital-cultural-programacion' ); ?></p>
			<h2><?php esc_html_e( 'Shortcodes disponibles', 'capital-cultural-programacion' ); ?></h2>
			<pre class="ccp-admin-shortcodes"><?php echo esc_html( implode( "\n", self::shortcode_examples() ) ); ?></pre>
			<h2><?php esc_html_e( 'Atributos compatibles', 'capital-cultural-programacion' ); ?></h2>
			<pre class="ccp-admin-shortcodes"><?php echo esc_html( "cantidad=\"-1\"\ncolumnas=\"5\"\ncategoria=\"muestra\"\ncategorias=\"muestra,taller\"\nmostrar_extracto=\"si\"\ntitulo=\"Muestras activas\"\ntexto_vacio=\"No hay propuestas cargadas para este espacio.\"" ); ?></pre>
			<h2><?php esc_html_e( 'Ejemplo combinado', 'capital-cultural-programacion' ); ?></h2>
			<pre class="ccp-admin-shortcodes"><?php echo esc_html( '[sorjosefa cantidad="10" columnas="5" mostrar_extracto="no" texto_vacio="Próximamente se publicarán nuevas propuestas."]' ); ?></pre>
			<h2><?php esc_html_e( 'Slider para inicio', 'capital-cultural-programacion' ); ?></h2>
			<pre class="ccp-admin-shortcodes"><?php echo esc_html( "[cc_propuestas_activas]\n[cc_propuestas_activas categoria=\"muestra\"]\n[cc_propuestas_activas categorias=\"muestra,taller\"]\n[cc_muestras_activas]\n[cc_actividades_activas]\n[cc_talleres_activos]\n[cc_cursos_activos]\n[cc_muestras_activas espacio=\"sor-josefa\" titulo=\"\"]" ); ?></pre>
		</div>
		<?php
	}

	/**
	 * Checks if the save request is valid.
	 *
	 * @param int $post_id Post ID.
	 */
	private function can_save( int $post_id ): bool {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
			return false;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		return current_user_can( 'edit_post', $post_id );
	}

	/**
	 * Renders a select field for a single taxonomy term.
	 *
	 * @param int    $post_id   Post ID.
	 * @param string $taxonomy  Taxonomy.
	 * @param string $field     Field name.
	 * @param string $help_text Help text.
	 */
	private function render_taxonomy_select( int $post_id, string $taxonomy, string $field, string $help_text ): void {
		$current = CCP_Taxonomies::get_first_term( $post_id, $taxonomy );
		$terms   = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		echo '<p class="description">' . esc_html( $help_text ) . '</p>';
		echo '<select class="widefat" name="' . esc_attr( $field ) . '">';
		echo '<option value="">' . esc_html__( 'Seleccionar', 'capital-cultural-programacion' ) . '</option>';

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				printf(
					'<option value="%1$d" %2$s>%3$s</option>',
					absint( $term->term_id ),
					selected( $current ? $current->term_id : 0, $term->term_id, false ),
					esc_html( $term->name )
				);
			}
		}

		echo '</select>';
	}

	/**
	 * Saves a single selected term.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @param string $field    Field name.
	 */
	private function save_single_term( int $post_id, string $taxonomy, string $field ): void {
		$term_id = isset( $_POST[ $field ] ) ? absint( $_POST[ $field ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $term_id && term_exists( $term_id, $taxonomy ) ) {
			wp_set_object_terms( $post_id, array( $term_id ), $taxonomy, false );
			return;
		}

		wp_set_object_terms( $post_id, array(), $taxonomy, false );
	}

	/**
	 * Reads and validates a posted date.
	 *
	 * @param string $field   Field name.
	 * @param int    $post_id Optional post ID fallback.
	 */
	private function posted_date( string $field, int $post_id = 0 ): string {
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' === $value && $post_id ) {
			$value = (string) get_post_meta( $post_id, $field, true );
		}

		return CCP_Date_Formatter::is_valid_date( $value ) ? $value : '';
	}

	/**
	 * Reads a posted text field.
	 *
	 * @param string $field   Field name.
	 * @param string $default Default value.
	 */
	private function posted_text( string $field, string $default = '' ): string {
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		return '' === $value ? $default : $value;
	}

	/**
	 * Reads a posted URL.
	 *
	 * @param string $field Field name.
	 */
	private function posted_url( string $field ): string {
		$value = isset( $_POST[ $field ] ) ? esc_url_raw( wp_unslash( $_POST[ $field ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		return wp_http_validate_url( $value ) ? $value : '';
	}

	/**
	 * Reads a posted manual status.
	 */
	private function posted_status(): string {
		$value   = isset( $_POST['_ccp_estado_manual'] ) ? sanitize_key( wp_unslash( $_POST['_ccp_estado_manual'] ) ) : 'automatico'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$options = CCP_Status::manual_options();

		return isset( $options[ $value ] ) ? $value : 'automatico';
	}

	/**
	 * Checks if a taxonomy field has a posted or existing term.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @param string $field    Field name.
	 */
	private function has_posted_or_existing_term( int $post_id, string $taxonomy, string $field ): bool {
		if ( isset( $_POST[ $field ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$term_id = absint( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return $term_id > 0 && (bool) term_exists( $term_id, $taxonomy );
		}

		return $post_id > 0 && null !== CCP_Taxonomies::get_first_term( $post_id, $taxonomy );
	}

	/**
	 * Shortcode examples for help screens.
	 *
	 * @return string[]
	 */
	private static function shortcode_examples(): array {
		return array(
			'[sorjosefa]',
			'[museodelteatro]',
			'[museoconstitucion]',
			'[cesarlopezclaro]',
			'[cec]',
			'[fotogaleria]',
			'[museodelaciudad]',
			'[casadelbrigadier]',
			'[museoinmaculada]',
			'[cc_programacion espacio="slug-del-espacio"]',
			'[cc_propuestas_activas]',
			'[cc_muestras_activas]',
			'[cc_muestras_activas_slider]',
			'[cc_actividades_activas]',
			'[cc_talleres_activos]',
			'[cc_cursos_activos]',
		);
	}
}
