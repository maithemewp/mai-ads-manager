<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Ad_Block {

	/**
	 * Mai_Ad_Block constructor.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'acf/init', [ $this, 'register' ] );
		add_filter( 'acf/load_field/key=field_615f63749dff7', [ $this, 'load_ads' ], 10, 3 );

	}

	/**
	 * Registers the ad block.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function register() {
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}

		acf_register_block_type(
			[
				'name'            => 'mai-ad',
				'title'           => __( 'Mai Ad', 'mai-ad-manager' ),
				'description'     => __( 'A custom columns block.', 'mai-ad-manager' ),
				'render_callback' => [ $this, 'do_ad' ],
				'category'        => 'widget',
				'keywords'        => [ 'ad', 'mai' ],
				'icon'            => 'media-code',
				'supports'        => [
					'align' => [],
					'mode'  => false,
					'jsx'   => false,
				],
			]
		);
	}

	/**
	 * Callback function to render the block.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $block      The block settings and attributes.
	 * @param string $content    The block inner HTML (empty).
	 * @param bool   $is_preview True during AJAX preview.
	 * @param int    $post_id    The post ID this block is saved to.
	 *
	 * @return void
	 */
	function do_ad( $block, $content = '', $is_preview = false, $post_id = 0 ) {
		// TODO: THIS FIELD DOESN'T EXIST YET.
		$id = get_field( 'maiam_id' );

		if ( ! $id ) {
			return;
		}

		$ads = (array) get_option( 'maiam_ads' );

		if ( ! ( $ads && isset( $ads[ $id ] ) ) ) {
			if ( $is_preview ) {
				printf( '<p>%s</p>', __( 'Please choose a valid ad.', 'mai-ad-manager' ) );
			}

			return;
		}

		maiam_do_ad( $ads[ $id ] );
	}

	/**
	 * Load ad options.
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_ads( $field ) {
		$field['choices'] = [];
		$ads              = get_option( 'maiam_ads' );

		if ( $ads ) {
			foreach ( $ads as $id => $ad ) {
				$field['choices'][ $id ] = isset( $ad['label'] ) ? $ad['label'] : $id;
			}
		} else {
			// TODO: TEST WITH NO ADS!
			$field['instructions'] = sprintf( __( 'No ads available. Go %s now.', 'mai-ad-manager' ), sprintf( '<a href="%s">%s</a>', '#', __( 'create a new ad', 'mai-ad-manager' ) ) );
		}

		return $field;
	}
}
