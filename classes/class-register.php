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
				'title'  => 'Mai Ads Manager',
				'fields' => [
					[
						'label'        => 'Header Code',
						'key'          => 'maiam_header',
						'name'         => 'maiam_header',
						'type'         => 'textarea',
						'instructions' => 'Add any global header code here.',
					],
					[
						'label'        => 'Ads',
						'key'          => 'maiam_ads',
						'name'         => 'maiam_ads',
						'type'         => 'repeater',
						'instructions' => '',
						'collapsed'    => 'maiam_name',
						'min'          => 0,
						'max'          => 0,
						'layout'       => 'block',
						'button_label' => 'Create New Ad',
						'sub_fields'   => [
							[
								'label' => 'Ad Code',
								'key'   => 'maiam_tab',
								'type'  => 'tab',
							],
							[
								'label'        => 'Name',
								'key'          => 'maiam_name',
								'name'         => 'name',
								'type'         => 'text',
								'instructions' => 'Used in Mai Ad block ad picker.',
								'required'     => 1,
							],
							[
								'label'    => 'Ad Code',
								'key'      => 'maiam_code',
								'name'     => 'code',
								'type'     => 'textarea',
								'required' => 1,
								'rows'     => 4,
							],
							[
								'label' => 'Details',
								'key'   => 'maiam_details_tab',
								'type'  => 'tab',
							],
							[
								'key'       => 'maiam_message',
								'type'      => 'message',
								'message'   => 'Sizes are used to build a predefined max container size to avoid cumulative layout shift (CLS). Breakpoint is the max screen width to display ad container in these dimensions. Leave breakpoint empty to use theme default.',
								'new_lines' => 'wpautop',
								'esc_html'  => 0,
							],
							[
								'label'      => 'Default',
								'key'        => 'maiam_desktop_group',
								'name'       => 'desktop',
								'type'       => 'group',
								'layout'     => 'block',
								'sub_fields' => [
									[
										'key'          => 'maiam_desktop_width',
										'name'         => 'width',
										'type'         => 'number',
										'instructions' => 'Width',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_desktop_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => 'Height',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
								],
							],
							[
								'label'      => 'Tablet',
								'key'        => 'maiam_tablet_group',
								'name'       => 'tablet',
								'type'       => 'group',
								'layout'     => 'block',
								'sub_fields' => [
									[
										'key'          => 'maiam_tablet_width',
										'name'         => 'width',
										'type'         => 'number',
										'instructions' => 'Width',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_tablet_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => 'Height',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_tablet_breakpoint',
										'name'         => 'breakpoint',
										'type'         => 'number',
										'instructions' => 'Breakpoint',
										'placeholder'  => '1000',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
								],
							],
							[
								'label'      => 'Mobile',
								'key'        => 'maiam_mobile_group',
								'name'       => 'mobile',
								'type'       => 'group',
								'layout'     => 'block',
								'sub_fields' => [
									[
										'key'          => 'maiam_mobile_width',
										'name'         => 'width',
										'type'         => 'number',
										'instructions' => 'Width',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_mobile_height',
										'name'         => 'height',
										'type'         => 'number',
										'instructions' => 'Height',
										'append'       => 'px',
										'wrapper'      => [
											'width' => '33.333333',
										],
									],
									[
										'key'          => 'maiam_mobile_breakpoint',
										'name'         => 'breakpoint',
										'type'         => 'number',
										'instructions' => 'Breakpoint',
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
								'instructions' => 'Reference ID. Cannot edit.',
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
	}
}
