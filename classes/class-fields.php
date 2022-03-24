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
		add_action( 'acf/render_field/key=maiam_header',    [ $this, 'admin_css' ] );
		add_filter( 'acf/load_field/key=maiam_header',      [ $this, 'load_header' ] );
		add_filter( 'acf/load_field/key=maiam_label',       [ $this, 'load_label' ] );
		add_filter( 'acf/load_field/key=maiam_breakpoints', [ $this, 'load_breakpoints' ] );
		add_filter( 'acf/load_field/key=maiam_ads',         [ $this, 'load_ads' ] );
		add_filter( 'acf/prepare_field/key=maiam_id',       [ $this, 'prepare_id' ] );
		add_action( 'acf/save_post',                        [ $this, 'save' ], 99 );
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
		$label       = esc_html( get_field( 'maiam_label', 'option' ) );
		$breakpoints = get_field( 'maiam_breakpoints', 'option' );
		$ads         = $this->get_formatted_data( (array) get_field( 'maiam_ads', 'option' ) );

		// Build options array.
		$options = [
			'header'      => $header,
			'label'       => $label,
			'breakpoints' => $breakpoints,
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
			'options_maiam_label',
			'options_maiam_breakpoints',
			'options_maiam_breakpoints_tablet',
			'options_maiam_breakpoints_mobile',
			'options_maiam_ads',
			'_options_maiam_header',
			'_options_maiam_label',
			'_options_maiam_breakpoints',
			'_options_maiam_breakpoints_tablet',
			'_options_maiam_breakpoints_mobile',
			'_options_maiam_ads',
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

			$data[ $id ] = maiam_get_parsed_ad_args( $ad );

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
}

