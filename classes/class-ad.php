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
		$mobile = maiam_get_breakpoint( 'mobile' );
		$tablet = maiam_get_breakpoint( 'tablet' );

		ob_start();
		?>
		<style>
		.mai-ad {
			max-width: var(--mai-ad-max-width, var(--mai-ad-max-width-desktop, unset));
			margin-right: auto;
			margin-bottom: var(--mai-ad-margin-bottom, 0);
			margin-left: auto;
		}

		.mai-ad[data-label]::before {
			display: block;
			margin-bottom: var(--mai-ad-label-margin-bottom, 6px);
			color: var(--mai-ad-label-color, rgba(0, 0, 0, 0.5));
			font-size: var(--mai-ad-label-font-size, 0.9rem);
			font-variant: all-petite-caps;
			line-height: 1;
			letter-spacing: 1px;
			text-align: var(--mai-ad-label-text-align, center);
			content: attr(data-label);
		}

		.mai-ad-inner {
			display: flex;
		}

		.mai-ad-inner::before {
			display: block;
			width: 1px;
			height: 0;
			margin-left: -1px;
			padding-bottom: calc(100% / (var(--mai-ad-aspect-ratio, var(--mai-ad-aspect-ratio-desktop, 0))));
			content: "";
		}

		.mai-ad-content {
			flex: 1;
			background: var(--mai-ad-background, repeating-conic-gradient(rgba(0, 0, 0, 0.03) 0% 25%, rgba(0, 0, 0, 0.01) 0% 50%) 50% / 20px 20px);
			background-position: top left;
		}

		@media only screen and (max-width: <?php echo $mobile . 'px'; ?>) {

			.mai-ad {
				--mai-ad-max-width: var(--mai-ad-max-width-mobile, var(--mai-ad-max-width-desktop, unset));
				--mai-ad-aspect-ratio: var(--mai-ad-aspect-ratio-mobile, var(--mai-ad-aspect-ratio-desktop, 0));
			}
		}

		@media only screen and (min-width: <?php echo ($mobile + 1). 'px'; ?>) and (max-width: <?php echo $tablet . 'px'; ?>) {

			.mai-ad {
				--mai-ad-max-width: var(--mai-ad-max-width-tablet, var(--mai-ad-max-width-desktop, unset));
				--mai-ad-aspect-ratio: var(--mai-ad-aspect-ratio-tablet, var(--mai-ad-aspect-ratio-desktop, 0));
			}
		}

		.entry-content {
			--mai-ad-margin-bottom: var(--spacing-md, 24px);
		}
		</style>
		<?php
		return ob_get_clean();
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
