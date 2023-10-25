window.googletag = window.googletag || {};
googletag.cmd    = googletag.cmd || [];

if ( window.googletag && googletag.apiReady ) {
	const ads           = maiPubAdsVars['ads'];
	const refreshKey    = 'refresh';
	const refreshvalue  = 'true';
	const prebidTimeout = 2000;

	googletag.cmd.push(() => {
		const gamBase = maiPubAdsVars['gamBase'];

		// Loop through maiPubAdsVars getting key and values.
		Object.keys( ads ).forEach( slug => {
			// Define ad slot.
			const slot = googletag.defineSlot( gamBase + slug, ads[slug].sizes, 'mai-ad-' + slug );

			// Set refresh targeting.
			slot.setTargeting( refreshKey, refreshvalue )

			// Set slot-level targeting.
			if ( ads[slug].targeting ) {
				Object.keys( ads[slug].targeting ).forEach( key => {
					slot.setTargeting( key, ads[slug].targeting[key] );
				});
			}

			// Get it running.
			slot.addService( googletag.pubads() );

			/**
			 * Define size mapping.
			 * If these breakpoints change, make sure to update the breakpoints in the mai-publisher.css file.
			 */
			slot.defineSizeMapping(
				googletag.sizeMapping()
				.addSize( [ 1024, 768 ], ads[slug].sizesDesktop )
				.addSize( [ 728, 480 ], ads[slug].sizesTablet )
				.addSize( [ 0, 0 ], ads[slug].sizesMobile )
				.build()
			);
		});

		// Set page-level targeting.
		if ( maiPubAdsVars.targeting ) {
			Object.keys( maiPubAdsVars.targeting ).forEach( key => {
				googletag.pubads().setTargeting( key, maiPubAdsVars.targeting[key].toString() );
			});
		}

		/**
		 * Lazy loading.
		 * @link https://developers.google.com/publisher-tag/reference?utm_source=lighthouse&utm_medium=lr#googletag.PubAdsService_enableLazyLoad
		 */
		googletag.pubads().enableLazyLoad({
			// Fetch slots within 2 viewports.
			fetchMarginPercent: 200,
			// Render slots within 1.5 viewports.
			renderMarginPercent: 150,
			// Double the above values on mobile.
			// mobileScaling: 2.0,
		});

		/**
		 * Set SafeFrame -- This setting will only take effect for subsequent ad requests made for the respective slots.
		 * To enable cross domain rendering for all creatives, execute setForceSafeFrame before loading any ad slots.
		 */
		googletag.pubads().setForceSafeFrame( true );

		// Make ads centered.
		googletag.pubads().setCentering( true );

		// Enable SRA and services.
		// googletag.pubads().disableInitialLoad(); // Disable initial load for header bidding.
		googletag.pubads().enableSingleRequest();
		googletag.enableServices();
	});

	// Set currently visible ads and timeout ids objects.
	const currentlyVisible = {};
	const timeoutIds       = {};

	/**
	 * Refreshes ads when they become visible.
	 */
	// googletag.pubads().addEventListener( 'slotVisibilityChanged', (event) => {
	// 	// let   timeoutId = null;
	// 	const slot      = event.slot;
	// 	const slotId    = slot.getSlotElementId();
	// 	const inView    = event.inViewPercentage > 50;

	// 	// Bail if not refreshing.
	// 	if ( slot.getTargeting( refreshKey ).indexOf( refreshvalue ) < 0 ) {
	// 		return;
	// 	}

	// 	// If in view and not currently visible, set to visible.
	// 	if ( inView && ! currentlyVisible[slotId] ) {
	// 		currentlyVisible[slotId] = true;
	// 	}
	// 	// If not in view and currently visible, set to not visible.
	// 	else if ( ! inView && currentlyVisible[slotId] ) {
	// 		currentlyVisible[slotId] = false;
	// 	}
	// 	// Not a change we care about.
	// 	else {
	// 		return;
	// 	}

	// 	// If not currently visible, clear timeout.
	// 	if ( ! currentlyVisible[slotId] ) {
	// 		clearTimeout( timeoutIds[slotId] );
	// 	}

	// 	// Loop through timeoutIds and clear all of them.
	// 	// Object.keys( timeoutIds ).forEach( id => {
	// 	// 	clearTimeout( timeoutIds[id] );
	// 	// });

	// 	// Set timeout to refresh ads for current visible ads.
	// 	timeoutIds[slotId] = setTimeout(() => {
	// 		googletag.pubads().refresh( [slot] );
	// 	}, 30 * 1000 ); // 30 seconds.

	// 	console.log( timeoutIds );
	// });
}