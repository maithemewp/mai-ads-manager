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
		add_filter( 'acf/load_field/key=maiam_header', [ $this, 'load_header' ] );
		add_filter( 'acf/load_field/key=maiam_ads', [ $this, 'load_ads' ] );
		add_filter( 'acf/load_field/key=maiam_tablet_breakpoint', [ $this, 'tablet_breakpoint_placeholder' ] );
		add_filter( 'acf/load_field/key=maiam_mobile_breakpoint', [ $this, 'mobile_breakpoint_placeholder' ] );
		add_filter( 'acf/prepare_field/key=maiam_id', [ $this, 'prepare_id' ] );
		add_action( 'acf/save_post', [ $this, 'save' ], 99 );
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
						'maiam_desktop_width'     => $this->get_value( $desktop, 'width' ),
						'maiam_desktop_height'    => $this->get_value( $desktop, 'height' ),
					],
					'maiam_tablet_group' => [
						'maiam_tablet_width'      => $this->get_value( $tablet, 'width' ),
						'maiam_tablet_height'     => $this->get_value( $tablet, 'height' ),
						'maiam_tablet_breakpoint' => $this->get_value( $tablet, 'breakpoint' ),
					],
					'maiam_mobile_group' => [
						'maiam_mobile_width'      => $this->get_value( $mobile, 'width' ),
						'maiam_mobile_height'     => $this->get_value( $mobile, 'height' ),
						'maiam_mobile_breakpoint' => $this->get_value( $mobile, 'breakpoint' ),
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
	 * Gets default placeholder value.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function tablet_breakpoint_placeholder( $field ) {
		$field['placeholder'] = maiam_get_tablet_breakpoint();
		return $field;
	}

	/**
	 * Gets default placeholder value.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function mobile_breakpoint_placeholder( $field ) {
		$field['placeholder'] = maiam_get_mobile_breakpoint();
		return $field;
	}

	/**
	 * Sets ID feild as readonly and gets random string if value is empty.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function prepare_id( $field ) {
		$field['readonly'] = true;

		if ( ! $field['value'] ) {
			$field['value'] = $this->get_random_id();
		};

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
		$options = [
			'header' => wp_kses_post( get_field( 'maiam_header', 'option' ) ),
			'label'  => esc_html( get_field( 'maiam_label', 'option' ) ),
			'ads'    => $this->get_formatted_data( (array) get_field( 'maiam_ads', 'option' ) ),
		];

		update_option( 'mai_ad_manager', $options );

		$first = maiam_get_option( 'first-version' );

		if ( ! $first ) {
			maiam_update_option( 'first-version', MAI_ADS_MANAGER_VERSION );
		}

		// Clear repeater field.
		update_field( 'maiam_ads', null, $post_id );

		// To delete.
		$options = [
			'options_maiam_header',
			'_options_maiam_header',
			'options_maiam_label',
			'_options_maiam_label',
			'options_maiam_ads',
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

		foreach ( $ads as $ad ) {
			$id = isset( $ad['id'] ) ? $ad['id'] : $this->get_random_id();

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

