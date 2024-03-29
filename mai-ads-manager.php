<?php

/**
 * Plugin Name:     Mai Ads Manager
 * Plugin URI:      https://bizbudding.com
 * Description:     Manage ad header and display code in one location. Works great with Mai Conditional Content Areas plugin.
 * Version:         0.11.0-beta.8
 *
 * Author:          BizBudding
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Must be at the top of the file.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Main Mai_Ads_Manager Class.
 *
 * @since 0.1.0
 */
final class Mai_Ads_Manager {

	/**
	 * @var   Mai_Ads_Manager The one true Mai_Ads_Manager
	 * @since 0.1.0
	 */
	private static $instance;

	/**
	 * Main Mai_Ads_Manager Instance.
	 *
	 * Insures that only one instance of Mai_Ads_Manager exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @uses    Mai_Ads_Manager::setup_constants() Setup the constants needed.
	 * @uses    Mai_Ads_Manager::includes() Include the required files.
	 * @uses    Mai_Ads_Manager::hooks() Activate, deactivate, etc.
	 * @see     Mai_Ads_Manager()
	 * @return  object | Mai_Ads_Manager The one true Mai_Ads_Manager
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup.
			self::$instance = new Mai_Ads_Manager;
			// Methods.
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-ads-manager' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-ads-manager' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'MAI_ADS_MANAGER_VERSION' ) ) {
			define( 'MAI_ADS_MANAGER_VERSION', '0.11.0-beta.8' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_ADS_MANAGER_PLUGIN_DIR' ) ) {
			define( 'MAI_ADS_MANAGER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Includes Path.
		if ( ! defined( 'MAI_ADS_MANAGER_INCLUDES_DIR' ) ) {
			define( 'MAI_ADS_MANAGER_INCLUDES_DIR', MAI_ADS_MANAGER_PLUGIN_DIR . 'includes/' );
		}

		// Plugin Classes Path.
		if ( ! defined( 'MAI_ADS_MANAGER_CLASSES_DIR' ) ) {
			define( 'MAI_ADS_MANAGER_CLASSES_DIR', MAI_ADS_MANAGER_PLUGIN_DIR . 'classes/' );
		}

		// Plugin Folder URL.
		if ( ! defined( 'MAI_ADS_MANAGER_PLUGIN_URL' ) ) {
			define( 'MAI_ADS_MANAGER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'MAI_ADS_MANAGER_PLUGIN_FILE' ) ) {
			define( 'MAI_ADS_MANAGER_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name
		if ( ! defined( 'MAI_ADS_MANAGER_BASENAME' ) ) {
			define( 'MAI_ADS_MANAGER_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function includes() {
		// Include vendor libraries.
		require_once __DIR__ . '/vendor/autoload.php';
		// Includes.
		foreach ( glob( MAI_ADS_MANAGER_INCLUDES_DIR . '*.php' ) as $file ) { include $file; }
		// Classes.
		foreach ( glob( MAI_ADS_MANAGER_CLASSES_DIR . '*.php' ) as $file ) { include $file; }
	}

	/**
	 * Run the hooks.
	 *
	 * @since   0.1.0
	 * @return  void
	 */
	public function hooks() {
		add_action( 'plugins_loaded', [ $this, 'updater' ], 12 );
		add_action( 'plugins_loaded', [ $this, 'classes' ], 99 );
	}

	/**
	 * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @since 0.1.0
	 *
	 * @uses https://github.com/YahnisElsts/plugin-update-checker/
	 *
	 * @return void
	 */
	public function updater() {
		// Bail if plugin updater is not loaded.
		if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}

		// Setup the updater.
		$updater = PucFactory::buildUpdateChecker( 'https://github.com/maithemewp/mai-ads-manager/', __FILE__, 'mai-ads-manager' );

		// Maybe set github api token.
		if ( defined( 'MAI_GITHUB_API_TOKEN' ) ) {
			$updater->setAuthentication( MAI_GITHUB_API_TOKEN );
		}

		// Add icons for Dashboard > Updates screen.
		if ( function_exists( 'mai_get_updater_icons' ) && $icons = mai_get_updater_icons() ) {
			$updater->addResultFilter(
				function ( $info ) use ( $icons ) {
					$info->icons = $icons;
					return $info;
				}
			);
		}
	}

	/**
	 * Instantiate classes.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function classes() {
		if ( ! class_exists( 'acf_pro' ) ) {
			return;
		}

		$register = new Mai_Ads_Manager_Register;
		$fields   = new Mai_Ads_Manager_Fields;
		$block    = new Mai_Ad_Block;

		if ( maiam_get_option( 'gam' ) ) {
			$gam = new Mai_Ads_Manager_GAM;
		}
	}
}

/**
 * The main function for that returns Mai_Ads_Manager
 *
 * The main function responsible for returning the one true Mai_Ads_Manager
 * Instance to functions everywhere.
 *
 * @since 0.1.0
 *
 * @return object|Mai_Ads_Manager The one true Mai_Ads_Manager Instance.
 */
function mai_ads_manager() {
	return Mai_Ads_Manager::instance();
}

// Get Mai_Ads_Manager Running.
mai_ads_manager();
