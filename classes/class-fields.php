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

	function load_header( $field ) {
		$field['value'] = get_option( 'maiam_header', '' );
		return $field;
	}

	function load_ads( $field ) {
		$field['value'] = [];
		$ads            = get_option( 'maiam_ads', false );

		if ( $ads ) {
			foreach ( $ads as $id => $ad ) {
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

	function get_value( $ad, $key ) {
		return isset( $ad[ $key ] ) ? $ad[ $key ] : '';
	}

	function tablet_breakpoint_placeholder( $field ) {
		$field['placeholder'] = maiam_get_tablet_breakpoint();
		return $field;
	}

	function mobile_breakpoint_placeholder( $field ) {
		$field['placeholder'] = maiam_get_mobile_breakpoint();
		return $field;
	}

	function prepare_id( $field ) {
		$field['readonly'] = true;

		if ( ! $field['value'] ) {
			$field['value'] = $this->get_random_id();
		};

		return $field;
	}

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
		$head = get_field( 'maiam_header', 'option' );
		$ads  = get_field( 'maiam_ads', 'option' );
		$ads  = $this->get_formatted_data( (array) $ads );

		// // Save single option values.
		update_option( 'maiam_header', $head );
		update_option( 'maiam_ads', $ads );

		// Clear repeater field.
		update_field( 'maiam_ads', null, $post_id );

		// To delete.
		$options = [
			'options_maiam_header',
			'_options_maiam_header',
			'options_maiam_ads',
			'_options_maiam_ads',
		];

		// Delete remaining options manually.
		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}

	function get_formatted_data( $ads ) {
		if ( ! $ads ) {
			return $ads;
		}

		$data = [];

		foreach ( $ads as $ad ) {
			$id = isset( $ad['id'] ) ? $ad['id'] : $this->get_random_id();

			unset( $ad['id'] );

			$data[ $id ] = wp_parse_args( $ad,
				[
					'name'    => '',
					'code'    => '',
					'desktop' => [
						'width'  => '',
						'height' => '',
					],
					'tablet' => [
						'width'      => '',
						'height'     => '',
						'breakpoint' => '',
					],
					'mobile' => [
						'width'      => '',
						'height'     => '',
						'breakpoint' => '',
					],
				]
			);
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

