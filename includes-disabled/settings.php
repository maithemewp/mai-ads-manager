<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Ad_Manager_Settings {
	/**
	 * Gets it started.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function __construct() {
		acf_add_options_sub_page(
			[
				'title'      => 'Ad Manager',
				'parent'     => class_exists( 'Mai_Engine' ) ? 'mai-theme' : 'options-general.php',
				'menu_slug'  => 'mai-ad-manager',
				'capability' => 'manage_options',
				'position'   => 4,
			]
		);
		// add_action( 'admin_menu', [ $this, 'add_page' ], 12 );
		// add_action( 'admin_init', [ $this, 'register_settings' ], 999 );
	}


}

add_action( 'acf/init', function() {
	$settings = new Mai_Ad_Manager_Settings;
}, 20 );
