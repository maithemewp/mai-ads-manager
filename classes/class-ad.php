<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Ad {
	/**
	 * Args.
	 *
	 * @var array $args
	 */
	public $args;

	/**
	 * Mai_Ad constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param $args The block args.
	 *
	 * @return void
	 */
	public function __construct( $args ) {
		$args = wp_parse_args( $args,
			[
				'code'    => '',
				'desktop' => [],
				'tablet'  => [],
				'mobile'  => [],
			]
		);

		$args['desktop'] = wp_parse_args( $args['desktop'],
			[
				'width'  => '',
				'height' => '',
			]
		);

		$args['tablet'] = wp_parse_args( $args['tablet'],
			[
				'width'      => '',
				'height'     => '',
				'breakpoint' => maiam_get_tablet_breakpoint(),
			]
		);

		$args['mobile'] = wp_parse_args( $args['mobile'],
			[
				'width'      => '',
				'height'     => '',
				'breakpoint' => maiam_get_mobile_breakpoint(),
			]
		);

		$args['desktop'] = array_map( 'esc_html', $args['desktop'] );
		$args['tablet']  = array_map( 'esc_html', $args['tablet'] );
		$args['mobile']  = array_map( 'esc_html', $args['mobile'] );
		$args['code']    = trim( $args['code'] );

		$this->args = $args;
	}

	/**
	 * Renders an ad.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function render() {
		if ( ! $this->args['ad'] ) {
			return;
		}

		echo $this->get();
	}

	/**
	 * Returns an ad.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function get() {
		return $this->get_ad();
	}

	/**
	 * Gets ad html.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_ad() {
		$html  = $this->get_css();
		$html .= $this->args['code'];

		if ( ! function_exists( 'genesis_markup' ) ) {
			return sprintf( '<div class="mai-ad">%s</div>', $html );
		}

		$atts = [
			'class'      => 'mai-ad',
			'data-label' => $this->args['label'],
			'style'      => '',
		];

		if ( $this->args['width'] ) {
			$width          = $this->get_unit_value( $this->args['width'] );
			$atts['style'] .= sprintf( 'max-width:%s;', $width );
		}

		if ( $this->args['height'] ) {
			$height         = $this->get_unit_value( $this->args['height'] );
			$atts['style'] .= sprintf( 'max-height:%s;', $height );
		}

		if ( $atts['style'] ) {
			$atts['style'] = trim( $atts['style'] );
		} else {
			unset( $atts['style'] );
		}

		return genesis_markup(
			[
				'open'    => '<div %s>',
				'close'   => '</div>',
				'content' => $html,
				'context' => 'mai-ad',
				'echo'    => false,
				'atts'    => $atts,
			]
		);
	}

	function get_css() {
		static $loaded = false;

		if ( $loaded ) {
			return;
		}

		$loaded = true;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$url    = MAI_ADS_MANAGER_PLUGIN_URL . "assets/css/mai-ad{$suffix}.css";

		return sprintf( '<link rel="stylesheet" type="text/css" href="%s">', $url );
	}

	/**
	 * Get the unit value.
	 * If only a number value, use the fallback..
	 *
	 * @since 0.1.0
	 *
	 * @param  string $value    The value. Could be integer 24 or with type 24px, 2rem, etc.
	 * @param  string $fallback The fallback unit value.
	 *
	 * @return string
	 */
	function get_unit_value( $value, $fallback = 'px' ) {
		if ( function_exists( 'mai_get_unit_value' ) ) {
			return mai_get_unit_value( $value, $fallback );
		}

		if ( empty( $value ) || is_numeric( $value ) ) {
			return sprintf( '%s%s', (int) $value, $fallback );
		}

		return trim( $value );
	}
}
