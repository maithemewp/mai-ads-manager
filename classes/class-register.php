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
		add_action( 'acf/init', [ $this, 'register' ] );
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
				'key'    => 'group_615617fc82546',
				'title'  => __( 'Mai Ads Manager', 'mai-ads-manager' ),
				'fields' => [
					[
						'label'        => __( 'Header Code', 'mai-ads-manager' ),
						'key'          => 'maiam_header',
						'name'         => 'maiam_header',
						'type'         => 'textarea',
						'instructions' => __( 'Add any global header code here.', 'mai-ads-manager' ),
					],
					[
						'label'         => __( 'Label', 'mai-ads-manager' ),
						'key'           => 'maiam_label',
						'name'          => 'name',
						'type'          => 'text',
						'instructions'  => __( 'Optional label to display above ads.', 'mai-ads-manager' ),
						'default_value' => __( 'Advertisement', 'mai-ads-manager' ),
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
								'key'       => 'maiam_message',
								'type'      => 'message',
								'message'   => __( 'Sizes are used to build a predefined max container size to avoid cumulative layout shift (CLS). Breakpoint is the max screen width to display ad container in these dimensions. Leave breakpoint empty to use theme default.', 'mai-ads-manager' ),
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
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_desktop_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => __( 'Height', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
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
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_tablet_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => __( 'Height', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_tablet_breakpoint',
										'name'         => 'breakpoint',
										'type'         => 'number',
										'instructions' => __( 'Breakpoint', 'mai-ads-manager' ),
										'placeholder'  => '1000',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
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
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_mobile_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => __( 'Height', 'mai-ads-manager' ),
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_mobile_breakpoint',
										'name'         => 'breakpoint',
										'type'         => 'number',
										'instructions' => __( 'Breakpoint', 'mai-ads-manager' ),
										'placeholder'  => '600',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
								],
							],
							[
								'key'          => 'maiam_id',
								'label'        => 'ID',
								'name'         => 'id',
								'type'         => 'text',
								'instructions' => __( 'Reference ID. Cannot edit.', 'mai-ads-manager' ),
								'required'     => 1,
								'wrapper'      => [
									'width' => '100',
								],
							],
						],
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
				'key'    => 'group_615f636b97886',
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
						'label' => '',
						'key'   => 'maiam_ad_hide_label',
						'name'  => 'hide_label',
						'type'  => 'true_false',
						'message' => __( 'Hide label', 'mai-ads-manager' ),
						'ui'    => 0,
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
}
