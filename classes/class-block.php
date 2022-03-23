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
		add_action( 'acf/init',                       [ $this, 'register' ] );
		add_filter( 'acf/load_field/key=maiam_ad_id', [ $this, 'load_ads' ], 10, 3 );
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
				'description'     => __( 'A custom ad block.', 'mai-ad-manager' ),
				'render_callback' => [ $this, 'do_ad' ],
				'category'        => 'widget',
				'keywords'        => [ 'ad', 'mai' ],
				'icon'            => 'media-code',
				'mode'            => 'preview',
				'enqueue_assets'  => function() {
					if ( is_admin() ) {
						$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
						wp_enqueue_style( 'mai-ad-editor', MAI_ADS_MANAGER_PLUGIN_URL . "assets/css/mai-ad-editor{$suffix}.css" );
					}
				},
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
		$args = [];
		$id   = get_field( 'id' );
		$ads  = (array) maiam_get_option( 'ads' );

		if ( $id && isset( $ads[ $id ] ) ) {
			$args = $ads[ $id ];
			$hide = get_field( 'hide_label' );

			if ( $hide ) {
				$args['label'] = '';
			}
		}

		if ( isset( $block['className'] ) && ! empty( $block['className'] ) ) {
			$args['class'] = $block['className'];
		}

		if ( ! $args && $is_preview ) {
			$args = [
				'code' => sprintf( '<p style="text-align:center;color:var(--body-color,inherit);font-family:var(--body-font-family,inherit);font-weight:var(--body-font-weight,inherit);font-size:var(--body-font-size,inherit);opacity:0.62;">%s</p>', __( 'Click here to choose an ad in block sidebar.', 'mai-ad-manager' ) ),
			];
		}

		if ( ! $args ) {
			return;
		}

		maiam_do_ad( $args );
	}

	/**
	 * Load ad options.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function load_ads( $field ) {
		$field['choices'] = [];
		$ads              = maiam_get_option( 'ads' );

		if ( $ads ) {
			foreach ( $ads as $id => $ad ) {
				$field['choices'][ $id ] = isset( $ad['name'] ) ? $ad['name'] : $id;
			}
		} else {
			// TODO: TEST WITH NO ADS!
			$field['instructions'] = sprintf( __( 'No ads available.%sGo %s now.', 'mai-ad-manager' ), '<br />', sprintf( '<a href="%s">%s</a>', '#', __( 'create a new ad', 'mai-ad-manager' ) ) );
		}

		return $field;
	}
}
