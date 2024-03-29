<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Ad {
	public $args;
	public $style;
	public $has_ratio;

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
		$this->args  = maiam_get_parsed_ad_args( $args, true );
		$this->style = $this->get_style();
	}

	/**
	 * Renders an ad.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function render() {
		if ( ! $this->args['code'] ) {
			return;
		}

		echo $this->get();
	}

	/**
	 * Returns an ad.
	 *
	 * @since 0.1.0
	 * @since 0.10.0 Added support for Hide Elements metabox in Mai Theme v2.
	 *
	 * @return string
	 */
	function get() {
		if ( function_exists( 'mai_is_element_hidden' ) && mai_is_element_hidden( 'mai_ads' ) ) {
			return;
		}

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
		$class = 'mai-ad';

		if ( $this->args['class'] ) {
			$class .= ' ' . trim( $this->args['class'] );
		}

		$html = sprintf( '<div class="%s"%s%s>', $class, $this->get_label(), $this->style );
			$html .= $this->get_css();
			$html .= sprintf( '<div class="mai-ad-inner%s">', $this->has_ratio ? ' mai-ad-has-ratio' : '' );
				$html .= '<div class="mai-ad-content">';
					$html .= $this->args['code'];
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		// Filters the ad content.
		$html = apply_filters( 'maiam_ad', $html, $this->args );

		return $html;
	}

	/**
	 * Gets ad css link if it hasn't been loaded yet.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_css() {
		static $loaded = false;

		if ( $loaded ) {
			return;
		}

		$css   = '';
		$admin = is_admin();

		if ( $admin || ( ! is_admin() && did_action( 'wp_body_open' ) ) ) {
			$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$desktop      = MAI_ADS_MANAGER_PLUGIN_URL . "assets/css/mai-ad{$suffix}.css";
			$mobile       = MAI_ADS_MANAGER_PLUGIN_URL . "assets/css/mai-ad-mobile{$suffix}.css";
			$tablet       = MAI_ADS_MANAGER_PLUGIN_URL . "assets/css/mai-ad-tablet{$suffix}.css";
			$mobile_break = maiam_get_breakpoint( 'mobile' );
			$tablet_break = maiam_get_breakpoint( 'tablet' );
			$css         .= sprintf( '<link rel="stylesheet" href="%s" />', $desktop );
			$css         .= sprintf( '<link rel="stylesheet" media="screen and (max-width: %spx)" href="%s" />', $mobile_break, $mobile );
			$css         .= sprintf( '<link rel="stylesheet" media="screen and (min-width: %spx) and (max-width: %spx)" href="%s" />', ($mobile_break + 1), $tablet_break, $tablet );
			$loaded       = ! $admin; // Load on every block in admin.
		}

		return $css;
	}

	/**
	 * Gets ad label.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_label() {
		return $this->args['label'] ? sprintf( ' data-label="%s"', wptexturize( $this->args['label'] ) ) : '';
	}

	/**
	 * Gets ad style attribute.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_style() {
		$this->has_ratio = false;
		$styles          = '';
		$breaks          = [
			'mobile',
			'tablet',
			'desktop',
		];

		foreach ( $breaks as $break ) {
			if ( $this->args[ $break ]['width'] ) {
				$styles .= sprintf( '--mai-ad-max-width-%s:%s;', $break, $this->get_unit_value( $this->args[ $break ]['width'] ) );

				if ( $this->args[ $break ]['height'] ) {
					$styles .= sprintf( '--mai-ad-aspect-ratio-%s:%s/%s;', $break, $this->args[ $break ]['width'], $this->args[ $break ]['height'] );
					$this->has_ratio = true;
				}
			}
		}

		return $styles ? sprintf( ' style="%s"', $styles ) : '';
	}

	/**
	 * Gets the unit value.
	 * If only a number value, use the fallback.
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
