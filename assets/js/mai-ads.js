// Begin Mai Ads GAM settings for sugarmakers.org.
window.googletag = window.googletag || {cmd: []};
googletag.cmd.push(function() {
	var REFRESH_KEY   = 'refresh';
	var REFRESH_VALUE = 'true';

	// Number of seconds to wait after the slot becomes viewable before we refresh the ad.
	var SECONDS_TO_WAIT_AFTER_VIEWABILITY = 30;

	// Define header ad unit mappings.
	var headerAll     = [ [970,250], [970,90], [728,90], [468, 60], [320, 50] ];
	var headerDesktop = [ [970, 250], [970, 90], [728, 90] ];
	var headerTablet  = [ [468, 60], [320, 50] ];
	var headerMobile  = [ [320, 50] ];
	var headerSizeMap = googletag.sizeMapping()
		.addSize( [1024, 768], headerDesktop )
		.addSize( [640, 480], headerTablet )
		.addSize( [0, 0], headerMobile )
		.build();

	// Define footer ad unit  mappings.
	var footerAll     = [ [970,90], [728,90], [468, 60], [320, 50] ];
	var footerDesktop = [ [970, 90], [728, 90] ];
	var footerTablet  = [ [468, 60], [320, 50] ];
	var footerMobile  = [ [320, 50] ];
	var footerSizeMap = googletag.sizeMapping()
	.addSize( [1024, 768], footerDesktop )
	.addSize( [640, 480], footerTablet )
	.addSize( [0, 0], footerMobile )
	.build();

	// Define incontent ad unit mappings.
	//
	var incontentAll     = [ [970, 250], [970, 66], [750, 300], [750, 200], [750, 100], [336, 280], [300, 250], [300, 100] ];
	var incontentDesktop = [ [970, 250], [970, 66], [750, 300], [750, 200], [750, 100] ];
	var incontentTablet  = [ [336, 280], [300, 250], [300, 100] ];
	var incontentMobile  = [ [300, 250], [300, 100] ];
	var incontentSizeMap = googletag.sizeMapping()
		.addSize( [1024, 768], incontentDesktop )
		.addSize( [640, 480], incontentTablet )
		.addSize( [0, 0], incontentMobile )
		.build();

	// Define infeed ad unit mappings.
	//
	var infeedAll     = [ [300, 600], [300, 250], [240, 400] ];
	var infeedDesktop = [ [300, 600], [300, 250] ];
	var infeedTablet  = [ [300, 250], [240, 400] ];
	var infeedMobile  = [ [300, 250], [240, 400] ];
	var infeedSizeMap = googletag.sizeMapping()
		.addSize( [1024, 768], infeedDesktop )
		.addSize( [640, 480], infeedTablet )
		.addSize( [0, 0], infeedMobile )
		.build();


	// Set SafeFrame -- This setting will only take effect for subsequent ad requests made for the respective slots.
	// To enable cross domain rendering for all creatives, execute setForceSafeFrame before loading any ad slots.
	googletag.pubads().setForceSafeFrame( true );

	// Make ads centered.
	googletag.pubads().setCentering( true );

	// Define Ad Slots.  These Ad Slots are defined using the Ad Unit Mappings above.

	// console.log( maiAdsHelperVars.slot_ids );
//
	if ( maiAdsHelperVars.slot_ids.includes( 'div-mai-ad-header' ) ) {
		var header = googletag.defineSlot( '/22487526518/sugarmakers.org/header', headerAll, 'div-mai-ad-header' )
						.addService( googletag.pubads() );

		header.defineSizeMapping( headerSizeMap );
	}

	if ( maiAdsHelperVars.slot_ids.includes( 'div-mai-ad-footer' ) ) {
		var footer = googletag.defineSlot( '/22487526518/sugarmakers.org/footer', footerAll, 'div-mai-ad-footer' )
						.setTargeting( REFRESH_KEY, REFRESH_VALUE )
						.addService( googletag.pubads() );

		footer.defineSizeMapping( footerSizeMap );
	}

	if ( maiAdsHelperVars.slot_ids.includes( 'div-mai-ad-incontent-1' ) ) {
		var incontent1 = googletag.defineSlot( '/22487526518/sugarmakers.org/incontent', incontentAll, 	'div-mai-ad-incontent-1' )
						.addService( googletag.pubads() );

		incontent1.defineSizeMapping( incontentSizeMap );
	}

	if ( maiAdsHelperVars.slot_ids.includes( 'div-mai-ad-incontent-2' ) ) {
		var incontent2 = googletag.defineSlot( '/22487526518/sugarmakers.org/incontent', incontentAll, 	'div-mai-ad-incontent-2' )
						.addService( googletag.pubads() );

		incontent2.defineSizeMapping( incontentSizeMap );
	}

	if ( maiAdsHelperVars.slot_ids.includes( 'div-mai-ad-infeed-1' ) ) {
		var infeed1 = googletag.defineSlot( '/22487526518/sugarmakers.org/infeed', infeedAll, 'div-mai-ad-infeed-1' )
						.addService( googletag.pubads() );

		infeed1.defineSizeMapping( infeedSizeMap );
	}

	// refresh ads only when they are in view and after expiration of SECONDS_TO_WAIT_AFTER_VIEWABILITY
	googletag.pubads().addEventListener('impressionViewable', function(event) {
		var slot = event.slot;
		if (slot.getTargeting(REFRESH_KEY).indexOf(REFRESH_VALUE) > -1) {
			setTimeout(function() {
				googletag.pubads().refresh([slot]);
			}, SECONDS_TO_WAIT_AFTER_VIEWABILITY * 1000);
		}
	});

	// Enable SRA and services.
	googletag.pubads().enableSingleRequest();
	googletag.enableServices();
});
// End Mai Ads GAM Settings.