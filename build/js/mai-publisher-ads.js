// define global PBJS and GPT libraries
window.pbjs      = window.pbjs || { que: [] };
window.googletag = window.googletag || {};
googletag.cmd    = googletag.cmd || [];

// Define global variables.
const ads              = maiPubAdsVars['ads'];
const adSlotIds        = [];
const adSlots          = [];
const gamBase          = maiPubAdsVars.gamBase;
const gamBaseClient    = maiPubAdsVars.gamBaseClient;
const refreshKey       = 'refresh';
const refreshValue     = maiPubAdsVars.targets.refresh;
const refreshTime      = 30; // Time in seconds.
const loadTimes        = {};
const currentlyVisible = {};
const timeoutIds       = {};
const bidderTimeout    = 3000;
const fallbackTimeout  = 3500; // Set global failsafe timeout ~500ms after DM UI bidder timeout.
const debug            = window.location.search.includes('dfpdeb') || window.location.search.includes('maideb') || window.location.search.includes('pbjs_debug=true');
const log              = maiPubAdsVars.debug;
let   timestamp        = Date.now();

// If debugging, log.
maiPubLog( 'v201' );

// If using Amazon UAM bids, add it. No need to wait for googletag to be loaded.
if ( maiPubAdsVars.amazonUAM ) {
	/**
	 * Amazon UAD.
	 * Debug via `apstag.debug('enableConsole')`.
	 * Disable debugging via `apstag.debug('disableConsole')`.
	 */
	!function(a9,a,p,s,t,A,g){if(a[a9])return;function q(c,r){a[a9]._Q.push([c,r])}a[a9]={init:function(){q("i",arguments)},fetchBids:function(){q("f",arguments)},setDisplayBids:function(){},targetingKeys:function(){return[]},_Q:[]};A=p.createElement(s);A.async=!0;A.src=t;g=p.getElementsByTagName(s)[0];g.parentNode.insertBefore(A,g)}("apstag",window,document,"script","//c.amazon-adsystem.com/aax2/apstag.js");

	// Initialize apstag.
	apstag.init({
		pubID: '79166f25-5776-4c3e-9537-abad9a584b43', // BB.
		adServer: 'googletag',
		// bidTimeout: prebidTimeout,
		// us_privacy: '-1', // https://ams.amazon.com/webpublisher/uam/docs/web-integration-documentation/integration-guide/uam-ccpa.html?source=menu
		// @link https://ams.amazon.com/webpublisher/uam/docs/reference/api-reference.html#configschain
		schain: {
			complete: 1, // Integer 1 or 0 indicating if all preceding nodes are complete.
			ver: '1.0', // Version of the spec used.
			nodes: [
				{
					asi: 'bizbudding.com', // Populate with the canonical domain of the advertising system where the seller.JSON file is hosted.
					sid: maiPubAdsVars.sellersId, // The identifier associated with the seller or reseller account within your advertising system.
					hp: 1, // 1 or 0, whether this node is involved in the payment flow.
					name: maiPubAdsVars.sellersName, // Name of the company paid for inventory under seller ID (optional).
					domain: maiPubAdsVars.domain, // Business domain of this node (optional).
				}
			]
		}
	});
}

// Add to googletag items.
googletag.cmd.push(() => {
	/**
	 * Set SafeFrame -- This setting will only take effect for subsequent ad requests made for the respective slots.
	 * To enable cross domain rendering for all creatives, execute setForceSafeFrame before loading any ad slots.
	 */
	// Disabled for now: https://developers.google.com/publisher-tag/reference#googletag.PubAdsService_setForceSafeFrame
	// googletag.pubads().setForceSafeFrame( true );

	// Set page-level targeting.
	if ( maiPubAdsVars.targets ) {
		Object.keys( maiPubAdsVars.targets ).forEach( key => {
			googletag.pubads().setTargeting( key, maiPubAdsVars.targets[key].toString() );
		});
	}

	// Make ads centered.
	googletag.pubads().setCentering( true );

	// Enable SRA and services.
	googletag.pubads().disableInitialLoad(); // Disable initial load for header bidding.
	googletag.pubads().enableSingleRequest();

	// Enable services.
	googletag.enableServices();

	// If no delay, run on DOMContentLoaded.
	if ( ! maiPubAdsVars.loadDelay ) {
		// Check if DOMContentLoaded has run.
		if ( 'loading' === document.readyState ) {
			// If it's still loading, wait for the event.
			document.addEventListener( 'DOMContentLoaded', maiPubDOMContentLoaded );
		} else {
			// If it's already loaded, execute maiPubDOMContentLoaded().
			maiPubDOMContentLoaded();
		}
	}
	// Delayed on window load.
	else {
		// On window load.
		window.addEventListener( 'load', () => {
			setTimeout( maiPubDOMContentLoaded, maiPubAdsVars.loadDelay );
		});
	}

	/**
	 * Set 30 refresh when an ad is in view.
	 */
	googletag.pubads().addEventListener( 'impressionViewable', (event) => {
		const slot   = event.slot;
		const slotId = slot.getSlotElementId();

		// Bail if not a refreshable slot.
		if ( ! maiPubIsRefreshable( slot ) ) {
			return;
		}

		// Set first load to current time.
		loadTimes[slotId] = Date.now();

		// Clear timeout if it exists.
		if ( timeoutIds[slotId] ) {
			clearTimeout( timeoutIds[slotId] );
		}

		// Set timeout to refresh ads for current visible ads.
		timeoutIds[slotId] = setTimeout(() => {
			// If debugging, log.
			maiPubLog( 'refreshed via impressionViewable: ' + slotId, slot );

			// Refresh the slot(s).
			maiPubRefreshSlots( [slot] );

		}, refreshTime * 1000 ); // Time in milliseconds.
	});

	/**
	 * Refreshes ads when scrolled back into view.
	 * Only refreshes if it's been n seconds since the ad was initially shown.
	 */
	googletag.pubads().addEventListener( 'slotVisibilityChanged', (event) => {
		const slot   = event.slot;
		const slotId = slot.getSlotElementId();
		const inView = event.inViewPercentage > 5;

		// Bail if not a refreshable slot.
		if ( ! maiPubIsRefreshable( slot ) ) {
			return;
		}

		// If in view and not currently visible, set to visible.
		if ( inView && ! currentlyVisible[slotId] ) {
			currentlyVisible[slotId] = true;
		}
		// If not in view and currently visible, set to not visible.
		else if ( ! inView && currentlyVisible[slotId] ) {
			currentlyVisible[slotId] = false;
		}
		// Not a change we care about.
		else {
			return;
		}

		// If scrolled out of view, clear timeout then bail.
		if ( ! currentlyVisible[slotId] ) {
			clearTimeout( timeoutIds[slotId] );
			return;
		}

		// Bail if loadTimes is undefined, or it hasn't been n seconds (in milliseconds).
		if ( ! loadTimes?.[slotId] || ( loadTimes[slotId] && Date.now() - loadTimes[slotId] < refreshTime * 1000 ) ) {
			return;
		}

		// If debugging, log.
		maiPubLog( 'refreshed via slotVisibilityChanged: ' + slotId, slot );

		// Refresh the slot(s).
		maiPubRefreshSlots( [slot] );
	});

	/**
	 * Checks if this is a client GAM ad and not the main plugin MCM ad,
	 * if it's a client ad and isEmpty, try to load the main plugin ad.
	 */
	googletag.pubads().addEventListener( 'slotRenderEnded', (event) => {
		// Bail if slot is not empty.
		if ( ! event.isEmpty ) {
			return;
		}

		// Bail if not one of our slots.
		if ( ! maiPubIsMaiSlot( event.slot ) ) {
			return;
		}

		// Get slug from slot ID.
		const slotId = event.slot.getSlotElementId();
		const slug   = slotId.replace( 'mai-ad-', '' );

		// Bail if it's not a client ad.
		if ( 'client' !== ads[slug]['context'] ) {
			return;
		}

		// Bail if no backfill ad with a backfill id.
		if ( ! ( ads?.[slug]?.['backfill'] && ads?.[slug]?.['backfillId'] ) ) {
			return;
		}

		// // If debugging, log.
		// maiPubLog( 'maipub backfilling with: ' + ads[slug]['backfill'], document.getElementById( slotId ).id );

		// // Set the ID to the backfill ID and define/display the backfill ad.
		// document.getElementById( slotId ).id = ads[slug]['backfillId'];

		// // Define and display the main plugin ad.
		// maiPubDisplaySlots( [ maiPubDefineSlot( ads[slug]['backfill'] ) ] );

		// // If debugging, log.
		// maiPubLog( 'maipub destroying: ' + slug );

		// // Unset ads[slug].
		// // delete ads[slug];

		// // Destroy the empty slot.
		// googletag.destroySlots( [ event.slot ] );
	});

	// If debugging, set listeners to log.
	if ( debug || log ) {
		// Log when a slot is requested/fetched.
		googletag.pubads().addEventListener( 'slotRequested', (event) => {
			maiPubLog( 'slotRequested:', event.slot, event );
		});

		// Log when a slot response is received.
		googletag.pubads().addEventListener( 'slotResponseReceived', (event) => {
			maiPubLog( 'slotResponseReceived:', event.slot, event );
		});

		// Log when a slot was loaded.
		googletag.pubads().addEventListener( 'slotOnload', (event) => {
			maiPubLog( 'slotOnload:', event.slot, event );
		});

		// Log when slot render has ended, regardless of whether ad was empty or not.
		googletag.pubads().addEventListener( 'slotRenderEnded', (event) => {
			maiPubLog( 'slotRenderEnded:', event.slot, event );
		});

		// Log when a slot ID visibility changed.
		// googletag.pubads().addEventListener( 'slotVisibilityChanged', (event) => {
		// 	maiPubLog( 'changed:', event.slot.getSlotElementId(), `${event.inViewPercentage}%` );
		// });
	}
}); // End `googletag.cmd.push`.

/**
 * DOMContentLoaded and IntersectionObserver handler.
 */
function maiPubDOMContentLoaded() {
	// Select all atf and btf ads.
	const toloadATF = [];
	const adsATF    = document.querySelectorAll( '.mai-ad-unit[data-ap="atf"], .mai-ad-unit[data-ap="bs"]' );
	const adsBTF    = document.querySelectorAll( '.mai-ad-unit:not([data-ap="atf"]):not([data-ap="bs"])' );

	// Add to queue, so they don't step on each other.
	googletag.cmd.push(() => {
		// Define ATF ads.
		adsATF.forEach( adATF => {
			// Get slug.
			const slug = adATF.getAttribute( 'id' ).replace( 'mai-ad-', '' );

			// Add to toloadATF array.
			toloadATF.push( maiPubDefineSlot( slug ) );

			// If debugging, add inline styling.
			if ( debug ) {
				adATF.style.outline = '2px dashed limegreen';

				// Add data-label attribute of slug.
				adATF.setAttribute( 'data-label', slug );
			}
		});

		// Display ATF ads.
		if ( toloadATF.length ) {
			maiPubDisplaySlots( toloadATF );
		}
	});

	// Create the IntersectionObserver.
	const observer  = new IntersectionObserver( (entries, observer) => {
		const toLoadBTF = [];

		// Loop through the entries.
		entries.forEach( entry => {
			// Skip if not intersecting.
			if ( ! entry.isIntersecting ) {
				return;
			}

			// Get slug.
			const slug = entry.target.getAttribute( 'id' ).replace( 'mai-ad-', '' );

			// If debugging, add inline styling.
			if ( debug ) {
				entry.target.style.outline = '2px dashed red';

				// Add data-label attribute of slug.
				entry.target.setAttribute( 'data-label', slug );
			}

			// Add to toLoadBTF array.
			toLoadBTF.push( slug );

			// Unobserve. GAM event listener will handle refreshes.
			observer.unobserve( entry.target );
		}); // End entries loop.

		// Bail if no slots to load.
		if ( ! toLoadBTF.length ) {
			return;
		}

		// Add to queue, so they don't step on each other.
		googletag.cmd.push(() => {
			// Define and display all slots in view.
			maiPubDisplaySlots( toLoadBTF.map( slug => maiPubDefineSlot( slug ) ) );
		});
	}, {
		root: null, // Use the viewport as the root.
		rootMargin: '600px 0px 600px 0px', // Trigger when the top of the element is X away from each part of the viewport.
		threshold: 0 // No threshold needed.
	});

	// Observe each BTF ad.
	adsBTF.forEach( adBTF => {
		observer.observe( adBTF );
	});
}

/**
 * Define a slot.
 *
 * @param {string} slug The ad slug.
 */
function maiPubDefineSlot( slug ) {
	let toReturn = null;

	// Get base from context.
	const base = ads?.[slug]?.['context'] && 'client' === ads[slug]['context'] ? gamBaseClient : gamBase;

	// Define slot ID.
	const slotId = base + ads[slug]['id'];

	// Get slot element ID.
	const slotElId = 'mai-ad-' + slug;

	// Check for existing slot.
	const existingSlot = adSlots.find( slot => slotElId == slot.getSlotElementId() );

	// If existing, return it.
	if ( existingSlot ) {
		maiPubLog( 'Slot already defined:', existingSlot );

		return existingSlot;
	}

	// Define ad slot. googletag.defineSlot( "/1234567/sports", [728, 90], "div-1" );
	const slot = googletag.defineSlot( slotId, ads[slug].sizes, 'mai-ad-' + slug );

	// Register the ad slot.
	// An ad will not be fetched until refresh is called,
	// due to the disableInitialLoad() method being called earlier.
	googletag.display( 'mai-ad-' + slug );

	// Add slot to our array.
	adSlotIds.push( slotId );
	adSlots.push( slot );

	// If debugging, log.
	maiPubLog( 'defineSlot() & display():', adSlots );

	// If amazon is enabled and ads[slug].sizes only contains a single size named 'fluid'.
	// if ( maiPubAdsVars.amazonUAM && 1 === ads[slug].sizes.length && 'fluid' === ads[slug].sizes[0] ) {
	// 	// If debugging, log.
	// 	maiPubLog( 'disabled safeframe: ' + slot.getSlotElementId() );

	// 	// Disabled SafeFrame for this slot.
	// 	slot.setForceSafeFrame( false );
	// }

	// Set refresh targeting.
	slot.setTargeting( refreshKey, refreshValue );

	// Set slot-level targeting.
	if ( ads[slug].targets ) {
		Object.keys( ads[slug].targets ).forEach( key => {
			slot.setTargeting( key, ads[slug].targets[key] );
		});
	}

	// Set split testing.
	if ( ads[slug].splitTest && 'rand' === ads[slug].splitTest ) {
		// Set 'st' to a value between 0-99.
		slot.setTargeting( 'st', Math.floor(Math.random() * 100) );
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

	// Set to return.
	toReturn = slot;

	return toReturn;
}

/**
 * Display slots.
 * The requestManager logic take from Magnite docs.
 *
 * @link https://help.magnite.com/help/web-integration-guide#parallel-header-bidding-integrations
 *
 * @param {array} slots An array of the defined slots objects.
 */
function maiPubDisplaySlots( slots ) {
	// Enable services.
	// This needs to run after defineSlot() but before display()/refresh().
	// If we did this in maiPubDefineSlot() it would run for every single slot, instead of batches.
	// NM, changed this when Magnites docs show it how we had it. Via: https://help.magnite.com/help/web-integration-guide
	// googletag.enableServices();

	// Object to manage each request state.
	const requestManager = {
		adserverRequestSent: false,
		dmBidsReceived: false,
		apsBidsReceived: false,
	};

	/**
	 * Send request to ad-server.
	 *
	 * @link https://help.magnite.com/help/web-integration-guide#parallel-header-bidding-integrations
	 */
	const sendAdserverRequest = function() {
		if ( ! requestManager.adserverRequestSent ) {
			requestManager.adserverRequestSent = true;
			googletag.pubads().refresh( slots );
		}
	}

	// Handle Magnite/DM bids.
	if ( maiPubAdsVars.magnite ) {
		// Force integers.
		maiPubAdsVars.ortb2.mobile         = parseInt( maiPubAdsVars.ortb2.mobile );
		maiPubAdsVars.ortb2.privacypolicy  = parseInt( maiPubAdsVars.ortb2.privacypolicy );
		maiPubAdsVars.ortb2.cattax         = parseInt( maiPubAdsVars.ortb2.cattax );
		maiPubAdsVars.ortb2.content.cattax = parseInt( maiPubAdsVars.ortb2.content.cattax );

		// Fetch bids from Magnite using Prebid.
		pbjs.que.push( function() {
			// Start the config.
			const pbjsConfig = {
				bidderTimeout: bidderTimeout,
				enableTIDs: true,
				ortb2: maiPubAdsVars.ortb2,
			};

			// If debugging or logging, enable debugging for magnite.
			// Disabled. Magnite said this was breaking their own debugging.
			// if ( debug || log ) {
			// 	pbjsConfig.debugging = {
			// 		enabled: true,
			// 	};
			// }

			// Log.
			maiPubLog( 'pbjsConfig', pbjsConfig );

			// Set the magnite config.
			pbjs.setConfig( pbjsConfig );

			// Request bids.
			pbjs.rp.requestBids( {
				gptSlotObjects: slots,
				callback: function() {
					pbjs.setTargetingForGPTAsync();

					requestManager.dmBidsReceived = true;

					if ( requestManager.apsBidsReceived ) {
						sendAdserverRequest();

						maiPubLog( 'refresh() with prebid: ' + uadSlots.map( slot => slot.slotID.replace( 'mai-ad-', '' ) ).join( ', ' ), uadSlots );
					}
				}
			});
		});
	}
	// No magnite.
	else {
		// Set the magnite demand manager request manager to true.
		requestManager.dmBidsReceived = true;
	}

	// Handle Amazon UAM bids.
	if ( maiPubAdsVars.amazonUAM ) {
		// Filter out ads[slug].sizes that only contain a single size named 'fluid'. This was throwing an error in amazon.
		// Filter out client ads.
		const uadSlots = slots
			.filter( slot => {
				const slug = slot.getSlotElementId().replace( 'mai-ad-', '' );

				return ! ( 1 === ads[slug].sizes.length && 'fluid' === ads[slug].sizes[0] ) && 'client' !== ads[slug]['context'];
			})
			.map( slot => {
				const elId = slot.getSlotElementId();
				const slug = elId.replace( 'mai-ad-', '' );

				return {
					slotID: elId,
					slotName: gamBase + ads[slug]['id'],
					sizes: ads[slug].sizes,
				};
			});

		// If we have uadSlots.
		if ( uadSlots.length ) {
			// Set the amazon config.
			const amazonConfig = {
				slots: uadSlots,
				timeout: bidderTimeout,
				params: {
					adRefresh: '1', // Must be string.
				}
			};

			// Log.
			maiPubLog( 'amazonConfig', amazonConfig );

			// Fetch bids from Amazon UAM using apstag.
			apstag.fetchBids( amazonConfig, function( bids ) {
				// Set apstag bids, then trigger the first request to GAM.
				apstag.setDisplayBids();

				// Set the request manager to true.
				requestManager.apsBidsReceived = true;

				// If we have all bids, send the adserver request.
				if ( requestManager.dmBidsReceived ) {
					sendAdserverRequest();

					maiPubLog( 'refresh() with amazon fetch: ' + uadSlots.map( slot => slot.slotID.replace( 'mai-ad-', '' ) ).join( ', ' ), uadSlots );
				}

				// Log if debugging.
				if ( debug || log ) {
					// Check bid responses for errors.
					bids.forEach((bid) => {
						if ( bid.error ) {
							maiPubLog( 'apstag.fetchBids error:', bid );
						}
					});
				}
			});
		}
		// No UAD, but we have others.
		else if ( slots.length ) {
			// Set the request manager to true.
			requestManager.apsBidsReceived = true;

			// If we have all bids, send the adserver request.
			if ( requestManager.dmBidsReceived ) {
				sendAdserverRequest();

				maiPubLog( 'refresh() without amazon slots to fetch: ' + slots.map( slot => slot.getSlotElementId() ).join( ', ' ), slots );
			}
		}
	}
	// Standard GAM.
	else {
		// Bail if no slots.
		if ( ! slots.length ) {
			return;
		}

		// Set the request manager to true.
		requestManager.apsBidsReceived = true;

		// If we have all bids, send the adserver request.
		if ( requestManager.dmBidsReceived ) {
			sendAdserverRequest();
		}

		maiPubLog( 'refresh() with GAM:' + slots.map( slot => slot.getSlotElementId() ).join( ', ' ), slots );
	}

	// Start the failsafe timeout.
	setTimeout(() => {
		// Log if no adserver request has been sent.
		if ( ! requestManager.adserverRequestSent ) {
			maiPubLog( 'refresh() with failsafe timeout: ' + slots.map( slot => slot.getSlotElementId() ).join( ', ' ), slots );
		}

		// Maybe send request.
		sendAdserverRequest();

	}, fallbackTimeout );
}

/**
 * Check if a slot is a defined mai ad slot.
 * Checks if the ad slot ID is in our array of ad slot IDs.
 *
 * @param {object} slot The ad slot.
 *
 * @return {boolean} True if a mai ad slot.
 */
function maiPubIsMaiSlot( slot ) {
	return slot && adSlotIds.includes( slot.getAdUnitPath() );
}

/**
 * Check if a slot is refreshable.
 * Checks if we have a defined mai ad slot that has targetting set to refresh.
 *
 * @param {object} slot The ad slot.
 *
 * @return {boolean} True if refreshable.
 */
function maiPubIsRefreshable( slot ) {
	return maiPubIsMaiSlot( slot ) && Boolean( slot.getTargeting( refreshKey ).shift() );
}

/**
 * Refreshes slots.
 *
 * @param {array} slots The defined slots.
 */
function maiPubRefreshSlots( slots ) {
	if ( maiPubAdsVars.amazonUAM ) {
		maiPubLog( 'setDisplayBids via apstag: ' + slots.map( slot => slot.getSlotElementId() ).join( ', ' ), slots );
		apstag.setDisplayBids();
	}

	// googletag.pubads().refresh( slots );
	googletag.pubads().refresh( slots, { changeCorrelator: false } );
}

/**
 * Log if debugging.
 *
 * @param {mixed} mixed The data to log.
 */
function maiPubLog( ...mixed ) {
	if ( ! ( debug || log ) ) {
		return;
	}

	// Set log variables.
	let timer = 'maipub ';

	// Set times.
	const current = Date.now();
	const now     = new Date().toLocaleTimeString( 'en-US', { hour12: true } );

	// If first, start.
	if ( timestamp === current ) {
		timer += 'start';
	}
	// Not first, add time since.
	else {
		timer += current - timestamp + 'ms';
	}

	// Log the combined message.
	console.log( `${timer} ${now}`, mixed );
}