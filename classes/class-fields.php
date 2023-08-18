<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Ads_Manager_Fields {

	/**
	 * Mai_Ads_Manager_Fields constructor.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Runs hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function hooks() {
		add_action( 'wp_head',                              [ $this, 'header' ] );
		add_action( 'wp_footer',                            [ $this, 'footer' ], 20 );
		add_action( 'acf/render_field/key=maiam_header',    [ $this, 'admin_css' ] );
		add_filter( 'acf/load_field/key=maiam_header',      [ $this, 'load_header' ] );
		add_filter( 'acf/load_field/key=maiam_footer',      [ $this, 'load_footer' ] );
		add_filter( 'acf/load_field/key=maiam_label',       [ $this, 'load_label' ] );
		add_filter( 'acf/load_field/key=maiam_breakpoints', [ $this, 'load_breakpoints' ] );
		add_filter( 'acf/load_field/key=maiam_gam',         [ $this, 'load_gam' ] );
		add_filter( 'acf/load_field/key=maiam_gam_domain',  [ $this, 'load_gam_domain' ] );
		add_filter( 'acf/load_field/key=maiam_ads',         [ $this, 'load_ads' ] );
		add_filter( 'acf/prepare_field/key=maiam_id',       [ $this, 'prepare_id' ] );
		add_action( 'acf/save_post',                        [ $this, 'save' ], 99 );
		add_filter( 'acf/load_field/key=hide_elements',     [ $this, 'hide_elements' ], 20 );
	}

	/**
	 * Adds custom CSS in the first field.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	function admin_css( $field ) {
		echo '<style>
		.acf-field-maiam-ads .acf-repeater .acf-actions .button-primary {
			display: block;
			width: 100%;
			margin: 16px 0 0;
			padding: 8px 16px;
			text-align: center;
		}
		</style>';
	}

	/**
	 * Outputs header.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function header() {
		$header = maiam_get_option( 'header', '' );

		if ( ! $header ) {
			return;
		}

		echo $header;
	}

	/**
	 * Outputs footer.
	 *
	 * @since 0.7.0
	 *
	 * @return void
	 */
	function footer() {
		$footer = maiam_get_option( 'footer', '' );

		if ( ! $footer ) {
			return;
		}

		echo $footer;
	}

	/**
	 * Loads header field value from our custom option.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_header( $field ) {
		$field['value'] = maiam_get_option( 'header', '' );
		return $field;
	}

	/**
	 * Loads footer field value from our custom option.
	 *
	 * @since 0.7.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_footer( $field ) {
		$field['value'] = maiam_get_option( 'footer', '' );
		return $field;
	}

	/**
	 * Loads label field value from our custom option.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_label( $field ) {
		$field['value'] = maiam_get_option( 'label', '' );
		return $field;
	}

	/**
	 * Loads breakpoints values.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_breakpoints( $field ) {
		$breakpoints    = maiam_get_option( 'breakpoints', '' );
		$field['value'] = [
			'maiam_tablet_breakpoint' => isset( $breakpoints['tablet'] ) ? $breakpoints['tablet'] : '',
			'maiam_mobile_breakpoint' => isset( $breakpoints['mobile'] ) ? $breakpoints['mobile'] : '',
		];
		return $field;
	}

	/**
	 * Loads GAM field value from our custom option.
	 *
	 * @since 0.11.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_gam( $field ) {
		$gam            = maiam_get_option( 'gam' );
		$gam            = isset( $gam['enabled'] ) ? (bool) $gam['enabled'] : false;
		$field['value'] = $gam;
		return $field;
	}

	/**
	 * Loads GAM domain field value from our custom option.
	 *
	 * @since 0.11.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_gam_domain( $field ) {
		$field['value'] = maiam_get_gam_domain();
		return $field;
	}

	/**
	 * Loads ads repeater field values from our custom option.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_ads( $field ) {
		$field['value'] = [];
		$options        = maiam_get_options();

		if ( $options && isset( $options['ads'] ) && $options['ads'] ) {
			foreach ( $options['ads'] as $id => $ad ) {
				$desktop = isset( $ad['desktop'] ) ? $ad['desktop'] : [];
				$tablet  = isset( $ad['tablet'] ) ? $ad['tablet'] : [];
				$mobile  = isset( $ad['mobile'] ) ? $ad['mobile'] : [];

				$field['value'][] = [
					'maiam_id'            => $id,
					'maiam_code'          => $this->get_value( $ad, 'code' ),
					'maiam_name'          => $this->get_value( $ad, 'name' ),
					'maiam_desktop_group' => [
						'maiam_desktop_width'  => $this->get_value( $desktop, 'width' ),
						'maiam_desktop_height' => $this->get_value( $desktop, 'height' ),
					],
					'maiam_tablet_group' => [
						'maiam_tablet_width'  => $this->get_value( $tablet, 'width' ),
						'maiam_tablet_height' => $this->get_value( $tablet, 'height' ),
					],
					'maiam_mobile_group' => [
						'maiam_mobile_width'  => $this->get_value( $mobile, 'width' ),
						'maiam_mobile_height' => $this->get_value( $mobile, 'height' ),
					],
				];
			}
		}

		return $field;
	}

	/**
	 * Gets a value from array if it is set.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $ad  The ad data.
	 * @param string $key The ad string to get.
	 *
	 * @return mixed
	 */
	function get_value( array $ad, $key ) {
		return isset( $ad[ $key ] ) ? $ad[ $key ] : '';
	}

	/**
	 * Sets ID feild as readonly.
	 *
	 * @since 0.1.0
	 * @since 0.6.0 Can't set random id here because it duplicates the value per row.
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function prepare_id( $field ) {
		$field['readonly'] = true;

		return $field;
	}

	/**
	 * Updates and deletes options when saving the settings page.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $post_id The post ID from ACF.
	 *
	 * @return void
	 */
	function save( $post_id ) {
		// Bail if no data.
		if ( ! isset( $_POST['acf'] ) || empty( $_POST['acf'] ) ) {
			return;
		}

		// Bail if not saving an options page.
		if ( 'options' !== $post_id ) {
			return;
		}

		// Current screen.
		$screen = get_current_screen();

		// Bail if not our options page.
		if ( ! $screen || false === strpos( $screen->id, 'mai-ads-manager' ) ) {
			return;
		}

		// Get formatted data.
		$header      = get_field( 'maiam_header', 'option' );
		$header      = current_user_can( 'unfiltered_html' ) ? trim( $header ) : wp_kses_post( trim( $header ) );
		$footer      = get_field( 'maiam_footer', 'option' );
		$footer      = current_user_can( 'unfiltered_html' ) ? trim( $footer ) : wp_kses_post( trim( $footer ) );
		$label       = esc_html( get_field( 'maiam_label', 'option' ) );
		$breakpoints = get_field( 'maiam_breakpoints', 'option' );
		$gam         = (bool) get_field( 'maiam_gam', 'option' );
		$gam_domain  = esc_html( get_field( 'maiam_gam_domain', 'option' ) );
		$ads         = $this->get_formatted_data( (array) get_field( 'maiam_ads', 'option' ) );
		$import      = trim( get_field( 'maiam_import', 'option' ) );
		$import      = $import ? json_decode( $import, true ) : [];

		// Import ads.
		if ( $import ) {
			$ads = $import;
		}

		// Format domain.
		if ( $gam_domain ) {
			$gam_domain = (string) wp_parse_url( esc_url( $gam_domain ), PHP_URL_HOST );
			$gam_domain = str_replace( 'www.', '', $gam_domain );
		}

		// Build options array.
		$options = [
			'header'      => $header,
			'footer'      => $footer,
			'label'       => $label,
			'breakpoints' => $breakpoints,
			'gam'         => [
				'enabled' => $gam,
				'domain'  => $gam_domain,
			],
			'ads'         => $ads,
		];

		update_option( 'mai_ads_manager', $options );

		$first = maiam_get_option( 'first-version' );

		if ( ! $first ) {
			maiam_update_option( 'first-version', MAI_ADS_MANAGER_VERSION );
		}

		// Clear repeater field.
		update_field( 'maiam_ads', null, $post_id );

		// To delete.
		$options = [
			'options_maiam_header',
			'options_maiam_footer',
			'options_maiam_label',
			'options_maiam_breakpoints',
			'options_maiam_breakpoints_tablet',
			'options_maiam_breakpoints_mobile',
			'options_maiam_gam',
			'options_maiam_gam_domain',
			'options_maiam_ads',
			'options_maiam_import',
			'options_maiam_export',
			'_options_maiam_header',
			'_options_maiam_footer',
			'_options_maiam_label',
			'_options_maiam_breakpoints',
			'_options_maiam_breakpoints_tablet',
			'_options_maiam_breakpoints_mobile',
			'_options_maiam_gam',
			'_options_maiam_gam_domain',
			'_options_maiam_ads',
			'_options_maiam_import',
			'_options_maiam_export',
		];

		// Delete remaining options manually.
		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}

	/**
	 * Gets formatted data ready to update option.
	 *
	 * @since 0.1.0
	 *
	 * @param array $ads The ad data from ACF.
	 *
	 * @return array
	 */
	function get_formatted_data( $ads ) {
		if ( ! $ads ) {
			return $ads;
		}

		$data = [];

		foreach ( $ads as $index => $ad ) {
			$id = isset( $ad['id'] ) && $ad['id'] ? esc_html( $ad['id'] ) : $this->get_random_id();

			unset( $ad['id'] );

			$args = maiam_get_parsed_ad_args( $ad );

			// Bail if not name and code, otherwise we have an empty row in the ACF UI.
			if ( ! ( $args['name'] && $args['code'] ) ) {
				continue;
			}

			$data[ $id ] = $args;

			// This is global and shouldn't be saved to the db for each ad.
			unset( $data[ $id ]['label'] );
		}

		return $data;
	}

	/**
	 * Gets a random ID.
	 *
	 * @since 0.1.0
	 *
	 * @param int $length The character count.
	 *
	 * @return string
	 */
	function get_random_id( $length = 8 ) {
		return substr( str_shuffle( str_repeat( $x='0123456789abcdefghijklmnopqrstuvwxyz', ceil( $length / strlen( $x ) ) ) ), 1, $length );
	}

	/**
	 * Adds Mai Ads as an option to Hide Elements metabox in Mai Theme v2.
	 *
	 * @since 0.10.0
	 *
	 * @param array $field The field data.
	 *
	 * @return void
	 */
	function hide_elements( $field ) {
		$field['choices'][ 'mai_ads' ] = __( 'Mai Ad Blocks', 'mai-ads-manager' );

		return $field;
	}
}