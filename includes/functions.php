<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Displays an ad.
 *
 * @param array $args Ad args. See Mai_Ad class for available args.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maiam_do_ad( $args ) {
	$ad = new Mai_Ad( $args );
	$ad->render();
}

/**
 * Returns an ad.
 *
 * @param array $args Ad args. See Mai_Ad class for available args.
 *
 * @since 0.1.0
 *
 * @return string
 */
function maiam_get_ad( $args ) {
	$ad = new Mai_Ad( $args );
	return $ad->get();
}

/**
 * Returns the GAM domain.
 *
 * @since 0.11.0
 *
 * @return string
 */
function maiam_get_gam_domain() {
	$gam    = maiam_get_option( 'gam' );
	$domain = isset( $gam['domain'] ) && $gam['domain'] ? (string) wp_parse_url( esc_url( $gam['domain'] ), 1 ) : '';
	$domain = $domain ? $domain : (string) wp_parse_url( esc_url( home_url() ), PHP_URL_HOST );
	$domain = str_replace( 'www.', '', $domain );
	return $domain;
}

/**
 * Gets parsed and sanitized ad args.
 *
 * @access private
 *
 * @param array $args  Ad args. See Mai_Ad class for available args.
 * @param bool  $front Whether this is for front end dislay or admin fields.
 *
 * @since 0.1.0
 *
 * @return string
 */
function maiam_get_parsed_ad_args( $args, $front = false ) {
	$args = wp_parse_args( $args,
		[
			'class'   => '',
			'label'   => maiam_get_option( 'label', '' ),
			'name'    => '',
			'code'    => '',
			'desktop' => [],
			'tablet'  => [],
			'mobile'  => [],
		]
	);

	foreach ( [ 'desktop', 'tablet', 'mobile' ] as $break ) {
		$args[ $break ] = wp_parse_args( $args[ $break ],
			[
				'width'  => '',
				'height' => '',
			]
		);
	}

	$args['class']   = esc_html( $args['class'] );
	$args['label']   = wp_kses_post( trim( $args['label'] ) );
	$args['name']    = wp_kses_post( trim( $args['name'] ) );
	$args['code']    = $front || current_user_can( 'unfiltered_html' ) ? trim( $args['code'] ) : wp_kses_post( trim( $args['code'] ) );
	$args['desktop'] = array_map( 'esc_html', $args['desktop'] );
	$args['tablet']  = array_map( 'esc_html', $args['tablet'] );
	$args['mobile']  = array_map( 'esc_html', $args['mobile'] );

	return $args;
}

/**
 * Gets all option values.
 *
 * @since 0.1.0
 *
 * @return array
 */
function maiam_get_options() {
	static $options = null;

	if ( ! is_null( $options ) ) {
		return $options;
	}

	return (array) get_option( 'mai_ads_manager', [] );
}

/**
 * Gets a single option value.
 *
 * @since 0.1.0
 *
 * @return string
 */
function maiam_get_option( $option ) {
	$options = maiam_get_options();
	return isset( $options[ $option ] ) ? $options[ $option ] : null;
}

/**
 * Updates a single option from array of options.
 *
 * @since 0.1.0
 *
 * @param string $option Option name.
 * @param mixed  $value  Option value.
 *
 * @return void
 */
function maiam_update_option( $option, $value ) {
	$handle  = 'mai_ads_manager';
	$options = (array) get_option( $handle, [] );

	$options[ $option ] = $value;

	update_option( $handle, $options );
}

/**
 * Gets a breakpoint.
 *
 * @since 0.1.0
 *
 * @param string $breakpoint The breakpoint name.
 * @param string $suffix     The unit value suffix.
 *
 * @return array
 */
function maiam_get_breakpoint( $breakpoint, $suffix = '' ) {
	$breakpoints = maiam_get_breakpoints();
	$breakpoint  = isset( $breakpoints[ $breakpoint ] ) ? (int) filter_var( $breakpoints[ $breakpoint ], FILTER_SANITIZE_NUMBER_INT ) : 0;

	return $breakpoint . $suffix;
}

/**
 * Gets breakpoints.
 *
 * @since 0.1.0
 *
 * @return array
 */
function maiam_get_breakpoints() {
	static $breakpoints = null;

	if ( ! is_null( $breakpoints ) ) {
		return $breakpoints;
	}

	$defaults    = maiam_get_default_breakpoints();
	$breakpoints = maiam_get_option( 'breakpoints' );
	$breakpoints = [
		'tablet' => isset( $breakpoints['tablet'] ) && $breakpoints['tablet'] ? $breakpoints['tablet'] : $defaults['tablet'],
		'mobile' => isset( $breakpoints['mobile'] ) && $breakpoints['mobile'] ? $breakpoints['mobile'] : $defaults['mobile'],
	];

	$breakpoints = apply_filters( 'maiam_breakpoints', $breakpoints );

	return $breakpoints;
}

/**
 * Gets a default breakpoint.
 *
 * @since 0.1.0
 *
 * @param string $breakpoint The breakpoint name.
 * @param string $suffix     The unit value suffix.
 *
 * @return array
 */
function maiam_get_default_breakpoint( $breakpoint, $suffix = '' ) {
	$breakpoints = maiam_get_default_breakpoints();
	$breakpoint  = isset( $breakpoints[ $breakpoint ] ) ? (int) filter_var( $breakpoints[ $breakpoint ], FILTER_SANITIZE_NUMBER_INT ) : 0;

	return $breakpoint . $suffix;
}

/**
 * Gets default breakpoints.
 *
 * @since 0.1.0
 *
 * @return array
 */
function maiam_get_default_breakpoints() {
	static $breakpoints = null;

	if ( ! is_null( $breakpoints ) ) {
		return $breakpoints;
	}

	$breakpoints = [
		'tablet' => function_exists( 'mai_get_breakpoint' ) ? mai_get_breakpoint( 'lg' ) : 1000,
		'mobile' => function_exists( 'mai_get_breakpoint' ) ? mai_get_breakpoint( 'sm' ) : 600,
	];

	$breakpoints = apply_filters( 'maiam_default_breakpoints', $breakpoints );

	return $breakpoints;
}

/**
 * Gets settings link.
 *
 * @since 0.4.0
 *
 * @return string
 */
function maiam_get_settings_link( $text ) {
	$url  = esc_url( admin_url( sprintf( '%s.php?page=mai-ads-manager', class_exists( 'Mai_Engine' ) ? 'admin' : 'options-general' ) ) );
	$link = sprintf( '<a href="%s">%s</a>', $url, $text );

	return $link;
}
