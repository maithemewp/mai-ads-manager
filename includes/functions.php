<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// add_action( 'genesis_before', function() {
// 	$ads = maiam_get_ads();
// 	ray( $ads );
// });

function maiam_do_ad( $args ) {
	$ad = new Mai_Ad( $args );
	$ad->render();
}

function maiam_get_ad( $args ) {
	$ad = new Mai_Ad( $args );
	return $ad->get();
}

function maiam_get_tablet_breakpoint() {
	return function_exists( 'mai_get_breakpoint' ) ? mai_get_breakpoint( 'lg' ) : 1000;
}

function maiam_get_mobile_breakpoint() {
	return function_exists( 'mai_get_breakpoint' ) ? mai_get_breakpoint( 'sm' ) : 600;
}
