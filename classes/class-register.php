<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Ads_Manager_Register {

	/**
	 * Mai_Ads_Manager_Register constructor.
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
		add_action( 'acf/init',                        [ $this, 'register' ] );
		add_filter( 'acf/load_field/key=maiam_export', [ $this, 'load_export_field' ] );
		add_filter( 'plugin_action_links_mai-ads-manager/mai-ads-manager.php', [ $this, 'add_settings_link' ], 10, 4 );
	}

	/**
	 * Registers options page and field groups from settings and custom block.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function register() {
		acf_add_options_sub_page(
			[
				'title'      => __( 'Ads Manager', 'mai-ads-manager' ),
				'parent'     => class_exists( 'Mai_Engine' ) ? 'mai-theme' : 'options-general.php',
				'menu_slug'  => 'mai-ads-manager',
				'capability' => 'manage_options',
				'position'   => 4,
			]
		);

		acf_add_local_field_group(
			[
				'key'    => 'maiam_options',
				'title'  => __( 'Mai Ads Manager', 'mai-ads-manager' ),
				'fields' => [
					[
						'label' => __( 'Ad Code', 'mai-ads-manager' ),
						'key'   => 'maiam_tab_ads',
						'type'  => 'tab',
					],
					[
						'label'        => __( 'Ads', 'mai-ads-manager' ),
						'key'          => 'maiam_ads',
						'name'         => 'maiam_ads',
						'type'         => 'repeater',
						'instructions' => '',
						'collapsed'    => 'maiam_name',
						'min'          => 0,
						'max'          => 0,
						'layout'       => 'block',
						'button_label' => __( 'Create New Ad', 'mai-ads-manager' ),
						'sub_fields'   => [
							[
								'label' => __( 'Ad Code', 'mai-ads-manager' ),
								'key'   => 'maiam_tab',
								'type'  => 'tab',
							],
							[
								'label'        => __( 'Name', 'mai-ads-manager' ),
								'key'          => 'maiam_name',
								'name'         => 'name',
								'type'         => 'text',
								'instructions' => __( 'Used in Mai Ad block ad picker.', 'mai-ads-manager' ),
								'required'     => 1,
							],
							[
								'label'    => __( 'Ad Code', 'mai-ads-manager' ),
								'key'      => 'maiam_code',
								'name'     => 'code',
								'type'     => 'textarea',
								'required' => 1,
								'rows'     => 4,
							],
							[
								'label' => __( 'Details', 'mai-ads-manager' ),
								'key'   => 'maiam_details_tab',
								'type'  => 'tab',
							],
							[
								'key'       => 'maiam_details_message',
								'type'      => 'message',
								'message'   => __( 'Sizes are used to build a predefined max container size and aspect ratio to avoid cumulative layout shift (CLS).', 'mai-ads-manager' ),
								'new_lines' => 'wpautop',
								'esc_html'  => 0,
							],
							[
								'label'      => __( 'Default', 'mai-ads-manager' ),
								'key'        => 'maiam_desktop_group',
								'name'       => 'desktop',
								'type'       => 'group',
								'layout'     => 'block',
								'sub_fields' => [
									[
										'key'          => 'maiam_desktop_width',
										'name'         => 'width',
										'type'         => 'number',
										'instructions' => __( 'Width', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '50',
										],
									],
									[
										'key'          => 'maiam_desktop_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => __( 'Height', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '50',
										],
									],
								],
							],
							[
								'label'      => __( 'Tablet', 'mai-ads-manager' ),
								'key'        => 'maiam_tablet_group',
								'name'       => 'tablet',
								'type'       => 'group',
								'layout'     => 'block',
								'sub_fields' => [
									[
										'key'          => 'maiam_tablet_width',
										'name'         => 'width',
										'type'         => 'number',
										'instructions' => __( 'Width', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '50',
										],
									],
									[
										'key'          => 'maiam_tablet_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => __( 'Height', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '50',
										],
									],
								],
							],
							[
								'label'      => __( 'Mobile', 'mai-ads-manager' ),
								'key'        => 'maiam_mobile_group',
								'name'       => 'mobile',
								'type'       => 'group',
								'layout'     => 'block',
								'sub_fields' => [
									[
										'key'          => 'maiam_mobile_width',
										'name'         => 'width',
										'type'         => 'number',
										'instructions' => __( 'Width', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '50',
										],
									],
									[
										'key'          => 'maiam_mobile_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => __( 'Height', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '50',
										],
									],
								],
							],
							[
								'key'          => 'maiam_id',
								'label'        => 'ID',
								'name'         => 'id',
								'type'         => 'text',
								'instructions' => __( 'Reference ID. Not editable.', 'mai-ads-manager' ),
								'required'     => 0,
								'wrapper'      => [
									'width' => '100',
								],
							],
						],
					],
					[
						'label' => __( 'Scripts', 'mai-ads-manager' ),
						'key'   => 'maiam_tab_scripts',
						'type'  => 'tab',
					],
					[
						'label'        => __( 'Header Code', 'mai-ads-manager' ),
						'key'          => 'maiam_header',
						'name'         => 'maiam_header',
						'type'         => 'textarea',
						'instructions' => __( 'Add any global header code here.', 'mai-ads-manager' ),
					],
					[
						'label'        => __( 'Footer Code', 'mai-ads-manager' ),
						'key'          => 'maiam_footer',
						'name'         => 'maiam_footer',
						'type'         => 'textarea',
						'instructions' => __( 'Add any global footer code here.', 'mai-ads-manager' ),
					],
					[
						'label'        => __( 'Settings', 'mai-ads-manager' ),
						'key'          => 'maiam_tab_settings',
						'type'         => 'tab',
					],
					[
						'label'         => __( 'Label', 'mai-ads-manager' ),
						'key'           => 'maiam_label',
						'name'          => 'maiam_label',
						'type'          => 'text',
						'instructions'  => __( 'Optional label to display above ads.', 'mai-ads-manager' ),
						'default_value' => __( 'Advertisement', 'mai-ads-manager' ),
					],
					[
						'label'        => __( 'Breakpoints', 'mai-ads-manager' ),
						'instructions' => __( 'The max screen width to display ad container in these dimensions. Leave empty to use theme default.', 'mai-ads-manager' ),
						'key'          => 'maiam_breakpoints',
						'name'         => 'maiam_breakpoints',
						'type'         => 'group',
						'layout'       => 'block',
						'sub_fields'   => [
							[
								'key'           => 'maiam_tablet_breakpoint',
								'name'          => 'tablet',
								'type'          => 'number',
								'instructions'  => __( 'Tablet', 'mai-ads-manager' ),
								'placeholder'   => maiam_get_default_breakpoint( 'tablet' ),
								'append'        => 'px',
								'wrapper'       => [
									'width' => '50',
								],
							],
							[
								'key'           => 'maiam_mobile_breakpoint',
								'name'          => 'mobile',
								'type'          => 'number',
								'instructions'  => __( 'Mobile', 'mai-ads-manager' ),
								'placeholder'   => maiam_get_default_breakpoint( 'mobile' ),
								'append'        => 'px',
								'wrapper'       => [
									'width' => '50',
								],
							],
						],
					],
					[
						'label'        => __( 'Import', 'mai-ads-manager' ),
						'key'          => 'maiam_tab_import',
						'type'         => 'tab',
					],
					[
						'label'        => __( 'Import', 'mai-ads-manager' ),
						'instructions' => __( 'Paste the export code from another website and hit the Import button to import all ads.', 'mai-ads-manager' ),
						'key'          => 'maiam_import',
						'name'         => 'maiam_import',
						'type'         => 'textarea',
						'rows'         => 10,
						'new_lines'    => '',
					],
					[
						'label'        => __( 'Export', 'mai-ads-manager' ),
						'key'          => 'maiam_tab_export',
						'type'         => 'tab',
					],
					[
						'label'        => __( 'Export', 'mai-ads-manager' ),
						'instructions' => __( 'Copy and paste this code into the Import section of another website to migrate all ads.', 'mai-ads-manager' ),
						'key'          => 'maiam_export',
						'name'         => 'maiam_export',
						'type'         => 'textarea',
						'rows'         => 10,
						'new_lines'    => '',
					],
				],
				'location' => [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'mai-ads-manager',
						],
					],
				],
			]
		);

		acf_add_local_field_group(
			[
				'key'    => 'maiam_ad_block',
				'title'  => __( 'Mai Ad', 'mai-ads-manager' ),
				'fields' => [
					[
						'label'        => __( 'Ad to display', 'mai-ads-manager' ),
						'key'          => 'maiam_ad_id',
						'name'         => 'id',
						'type'         => 'select',
						'instructions' => '',
						'required'     => 0,
						'choices'      => [],
						'allow_null'   => 1,
					],
					[
						'label'        => '',
						'key'          => 'maiam_ad_hide_label',
						'name'         => 'hide_label',
						'type'         => 'true_false',
						'message'      => __( 'Hide label', 'mai-ads-manager' ),
						'ui'           => 0,
					],
					[
						'label'        => '',
						'key'          => 'maiam_ad_settings_link',
						'name'         => '',
						'type'         => 'message',
						'message'      => maiam_get_settings_link( __( 'Ads Manager →', 'mai-ads-manager' ) ),
						'new_lines'    => '',
						'esc_html'     => 0,
					],
				],
				'location' => [
					[
						[
							'param'    => 'block',
							'operator' => '==',
							'value'    => 'acf/mai-ad',
						],
					],
				],
			]
		);
	}

	/**
	 * Loads JSON ads values for copying.
	 *
	 * @since TBD
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	function load_export_field( $field ) {
		$field['value']    = wp_json_encode( (array) maiam_get_option( 'ads' ) );
		$field['readonly'] = 'readonly';

		return $field;
	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin is active.
	 *
	 * @since 0.2.0
	 *
	 * @param array  $actions     Associative array of action names to anchor tags
	 * @param string $plugin_file Plugin file name, ie my-plugin/my-plugin.php
	 * @param array  $plugin_data Associative array of plugin data from the plugin file headers
	 * @param string $context     Plugin status context, ie 'all', 'active', 'inactive', 'recently_active'
	 *
	 * @return array associative array of plugin action links
	 */
	function add_settings_link( $actions, $plugin_file, $plugin_data, $context ) {
		$actions['settings'] = maiam_get_settings_link( __( 'Settings', 'mai-ads-manager' ) );

		return $actions;
	}
}