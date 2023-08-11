<?php

/**
 * Helper class for Mai Ads Manager GAM integration.
 * Original class below:
 *
 * @link https://gist.github.com/JiveDig/11f5d1a59406823298bddce019b14b18
 */
class Mai_Ads_Manager_GAM {
	/**
	 * Constructs the class.
	 *
	 * @since 0.11.0
	 *
	 * @return void
	 */
	function __construct() {
		// Bail if Mai Engine is not running.
		if ( ! class_exists( 'Mai_Engine' ) ) {
			return;
		}

		// Bail if Mai Ads Manager is not running.
		if ( ! class_exists( 'Mai_Ads_Manager' ) ) {
			return;
		}

		add_filter( 'mai_performance_enhancer_skip_scripts', [ $this, 'mai_perf' ] );
		// add_filter( 'wp_preload_resources',                  [ $this, 'preload' ] );
		// add_filter( 'script_loader_tag',                     [ $this, 'add_async' ], 10, 2 );
		// add_action( 'template_redirect',                     [ $this, 'header' ] );
		add_action( 'wp_enqueue_scripts',                    [ $this, 'enqueue_script' ], 0 );
	}

	/**
	 * Makes sure our new scripts are not
	 * forced to footer in Mai Performance Enhancer.
	 *
	 * @since 0.11.0
	 *
	 * @param array $skips
	 *
	 * @return array
	 */
	function mai_perf( $skips ) {
		$skips[] = 'google-gpt';
		$skips[] = 'maiAds'; // Localized vars.
		$skips[] = 'mai-ads'; // Our scripts.

		return array_unique( $skips );
	}

	function preload( $resources ) {
		if ( ! $this->has_file() ) {
			return $resources;
		}

		$resources[] = [
			'href'        => 'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
			'as'          => 'script',
			'crossorigin' => 'anonymous',
		];

		$resources[] = [
			'href' => $this->get_file_data( 'url' ),
			'as'   => 'script',
		];

		return $resources;
	}

	/**
	 * Add async tag to our scripts.
	 *
	 * @since 0.11.0
	 *
	 * @param string $tag    The script markup.
	 * @param string $handle The script handle.
	 *
	 * @return string
	 */
	function add_async( $tag, $handle ) {
		// Bail if not our scripts.
		if ( ! in_array( $handle, [ 'google-gpt', 'mai-ads-helper' ] ) ) {
		// if ( ! in_array( $handle, [ 'mai-ads-helper' ] ) ) {
		// if ( ! in_array( $handle, [ 'google-gpt' ] ) ) {
			return $tag;
		}

		// Do it the old way if older WP.
		if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
			return str_replace( ' src', ' async src', $tag );
		}

		// Add async tag.
		$tags = new WP_HTML_Tag_Processor( $tag );

		while ( $tags->next_tag( [ 'tag_name' => 'script' ] ) ) {
			$tags->set_attribute( 'async', '' );
		}

		return $tags->get_updated_html();
	}

	function header() {
		if ( ! $this->has_file() ) {
			return;
		}

		$url = $this->get_file_data( 'url' );

		// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Link
		$header = "Link: <{$url}>; rel=preload; as=script; crossorigin";
		header( $header, false );
	}

	/**
	 * Enqueue JS for GPT ads.
	 *
	 * @since 0.11.0
	 *
	 * @return void
	 */
	function enqueue_script() {
		$slot_ids = $this->get_slot_ids();

		if ( ! $slot_ids ) {
			return;
		}

		wp_enqueue_script( 'google-gpt', 'https://securepubads.g.doubleclick.net/tag/js/gpt.js', [],  $this->get_file_data( 'version' ), false );
		wp_enqueue_script( 'mai-ads-manager', $this->get_file_data( 'url' ), [ 'google-gpt' ],  $this->get_file_data( 'version' ), false );
		wp_localize_script( 'mai-ads-manager', 'maiAdsHelperVars',
			[
				'slot_ids' => $slot_ids,
			]
		);
	}

	/**
	 * Gets file URL.
	 *
	 * @since 0.11.0
	 *
	 * @param string $key The specific key to return
	 *
	 * @return array|string
	 */
	function get_file_data( $key = '' ) {
		static $cache = null;

		if ( ! is_null( $cache ) ) {
			if ( $key ) {
				return $cache[ $key ];
			}

			return $cache;
		}

		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$file      = "assets/js/mai-ads{$suffix}.js";
		$file_path = MAI_ADS_MANAGER_PLUGIN_DIR . $file;
		$file_url  = MAI_ADS_MANAGER_PLUGIN_URL . $file;
		$version   = MAI_ANALYTICS_VERSION;
		$version  .= '.' . date( 'njYHi', filemtime( $file_path ) );
		$cache     = [
			'path'    => $file_path,
			'url'     => $file_url,
			'version' => $version,
		];

		if ( $key ) {
			return $cache[ $key ];
		}

		return $cache;
	}

	/**
	 * Gets slot IDs.
	 *
	 * @since 0.11.0
	 *
	 * @return array
	 */
	function get_slot_ids() {
		// Get ads from Mai Ads Manager settings.
		$ads = maiam_get_option( 'ads' );

		// Bail if none.
		if ( ! $ads ) {
			return;
		}

		// Set empty array for ad IDs.
		$ad_ids = [];

		// Get ads from post content.
		$ad_ids = is_singular() ? array_merge( $ad_ids, $this->get_ad_block_ids( get_post_field( 'post_content', get_the_ID() ) ) ) : $ad_ids;

		// Get ads from global ccas in Mai Engine.
		$ccas = mai_get_template_parts();

		foreach ( $ccas as $slug => $cca ) {
			if ( ! $cca ) {
				continue;
			}

			if ( ! mai_has_template_part( $slug ) ) {
				continue;
			}

			$ad_ids = array_merge( $ad_ids, $this->get_ad_block_ids( $cca ) );
		}

		// Get ads from custom ccas in Mai Custom Content Areas.
		$ccas = function_exists( 'maicca_get_page_ccas' ) ? maicca_get_page_ccas() : [];

		foreach ( $ccas as $cca ) {
			if ( ! isset( $cca['content'] ) || empty( $cca['content'] ) ) {
				continue;
			}

			$ad_ids = array_merge( $ad_ids, $this->get_ad_block_ids( $cca['content'] ) );
		}

		// Get ads from Mai Archive Pages.
		if ( function_exists( 'maiap_get_archive_page' ) ) {
			$pages = [
				maiap_get_archive_page( true ),
				maiap_get_archive_page( false ),
			];

			$pages = array_filter( $pages );

			if ( $pages ) {
				foreach ( $pages as $page ) {
					$ad_ids = array_merge( $ad_ids, $this->get_ad_block_ids( $page->post_content ) );
				}
			}
		}

		// Remove duplicates.
		$ad_ids = array_unique( $ad_ids );

		// Bail if no ads.
		if ( ! $ad_ids ) {
			return;
		}

		// Get rendered ad ids that are in the Mai Ads Manger ads.
		$ads = array_intersect_key( $ads, array_flip( $ad_ids ) );

		// Bail if none.
		if ( ! $ads ) {
			return;
		}

		// Get slot ids from ads.
		$slot_ids = [];

		foreach ( $ads as $ad ) {
			$id = mai_get_string_between_strings( $ad['code'], 'googletag.display(', ')' );

			if ( ! $id ) {
				continue;
			}

			$id = trim( $id );
			$id = ltrim( $id, "'" );
			$id = ltrim( $id, '"' );
			$id = rtrim( $id, "'" );
			$id = rtrim( $id, '"' );

			$slot_ids[] = $id;
		}

		return array_unique( $slot_ids );
	}

	/**
	 * Gets ad block IDs.
	 *
	 * @since 0.11.0
	 *
	 * @param string|array $input Post content or parsed blocks.
	 *
	 * @return array
	 */
	function get_ad_block_ids( $input ) {
		$ad_ids = [];
		$blocks = is_array( $input ) ? $input : parse_blocks( $input );

		foreach ( $blocks as $block ) {
			if ( 'acf/mai-ad' === $block['blockName'] && isset( $block['attrs']['data']['id'] ) && ! empty( $block['attrs']['data']['id'] ) ) {
				$ad_ids[] = $block['attrs']['data']['id'];
			}

			if ( isset( $block['innerBlocks'] ) && $block['innerBlocks'] ) {
				$ad_ids = array_merge( $ad_ids, $this->get_ad_block_ids( $block['innerBlocks'] ) );
			}
		}

		return $ad_ids;
	}
}
