window.googletag = window.googletag || {};
googletag.cmd    = googletag.cmd || [];

if ( window.googletag && googletag.apiReady ) {
	googletag.cmd.push(() => {
		const ads          = maiPubVars['ads'];
		const refreshKey   = 'refresh';
		const refreshvalue = 'true';

		// Loop through maiPubVars getting key and values.
		for ( const id in ads ) {
			// Define ad slot.
			const slot = googletag
				.defineSlot( '/22487526518/' + maiPubVars['gam_domain'] + '/' + id, ads[id].sizes, 'mai-ad-' + id )
				.addService( googletag.pubads() )
				.setTargeting( refreshKey, refreshvalue );

			/**
			 * Define size mapping.
			 * If these breakpoints change, make sure to update the breakpoints in the mai-publisher.css file.
			 */
			slot.defineSizeMapping(
				googletag.sizeMapping()
				.addSize( [ 1024, 768 ], ads[id].sizesDesktop )
				.addSize( [ 640, 480 ], ads[id].sizesTablet )
				.addSize( [ 0, 0 ], ads[id].sizesMobile )
				.build()
			);
		}

		// TODO: Configure page-level targeting.
		// googletag.pubads().setTargeting( 'interests', 'basketball' );

		/**
		 * Lazy loading.
		 * @link https://developers.google.com/publisher-tag/reference?utm_source=lighthouse&utm_medium=lr#googletag.PubAdsService_enableLazyLoad
		 */
		googletag.pubads().enableLazyLoad({
			// Fetch slots within 5 viewports.
			fetchMarginPercent: 500,
			// Render slots within 2 viewports.
			renderMarginPercent: 200,
			// Double the above values on mobile.
			mobileScaling: 2.0,
		});

		/**
		 * Set SafeFrame -- This setting will only take effect for subsequent ad requests made for the respective slots.
		 * To enable cross domain rendering for all creatives, execute setForceSafeFrame before loading any ad slots.
		 */
		googletag.pubads().setForceSafeFrame( true );

		// Make ads centered.
		googletag.pubads().setCentering( true );

		// Refresh ads only when they are in view and after expiration of refreshSeconds.
		googletag.pubads().addEventListener( 'impressionViewable', function( event ) {
			const slot = event.slot;

			if ( slot.getTargeting( refreshKey ).indexOf( refreshvalue ) >= 0 ) {
				setTimeout( function() {
					googletag.pubads().refresh([slot]);
				}, 30 * 1000 ); // 30 seconds.
			}
		});

		// Enable SRA and services.
		googletag.pubads().enableSingleRequest();
		googletag.enableServices();
	});
}