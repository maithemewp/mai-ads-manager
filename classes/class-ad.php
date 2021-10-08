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
		$this->args = maiam_get_parsed_ad_args( $args );
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
		$html .= sprintf( '<div class="mai-ad"%s%s>', $this->get_label(), $this->get_style() );
			$html .= '<div class="mai-ad-inner">';
				$html .= '<div class="mai-ad-content">';
					if ( $this->args['label'] ) {
						$html .= sprintf( '<span class="mai-ad-label" data-label="%s"></span>', $this->args['label'] );
					}

					$html .= $this->args['code'];
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

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

		$loaded = true;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$url    = MAI_ADS_MANAGER_PLUGIN_URL . "assets/css/mai-ad{$suffix}.css";

		return sprintf( '<link rel="stylesheet" type="text/css" href="%s">', $url );
	}

	/**
	 * Gets ad label.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_label() {
		return $this->args['label'] ? sprintf( ' data-label="%s"', $this->args['label'] ) : '';
	}

	/**
	 * Gets ad style attribute.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_style() {
		$styles = '';
		$breaks = [
			'mobile',
			'tablet',
			'desktop',
		];

		foreach ( $breaks as $break ) {
			if ( $this->args[ $break ]['width'] ) {
				$styles .= sprintf( '--mai-ad-max-width-%s:%s;', $break, $this->get_unit_value( $this->args[ $break ]['width'] ) );

				if ( $this->args[ $break ]['height'] ) {
					$styles .= sprintf( '--mai-ad-aspect-ratio-%s:%s/%s;', $break, $this->args[ $break ]['width'], $this->args[ $break ]['height'] );
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
