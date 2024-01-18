<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

// Desktop: 1024, 768
// Tablet: 728, 480
// Mobile: up to 727.

return [
	'ad_units' => [
		// Keepers.
		'billboard' => [
			'sizes'         => [ [300, 50], [300, 100], [300, 250], [320, 50], [336, 280], [580, 400], [728, 90], [750, 100], [750, 200], [750, 300] ],
			'sizes_desktop' => [ [580, 400], [728, 90], [750, 100], [750, 200], [750, 300] ],
			'sizes_tablet'  => [ [300, 50], [300, 100], [300, 250], [320, 50], [336, 280], [580, 400], [728, 90] ],
			'sizes_mobile'  => [ [300, 50], [300, 100], [300, 250], [320, 50], [336, 280], [580, 400] ],
		],
		'billboard-wide' => [
			'sizes'         => [ [300, 50], [300, 100], [300, 250], [320, 50], [336, 280], [580, 400], [728, 90], [750, 100], [750, 200], [750, 300],  [970, 90], [970, 250] ],
			'sizes_desktop' => [ [580, 400], [728, 90], [750, 100], [750, 200], [750, 300], [970, 90], [970, 250] ],
			'sizes_tablet'  => [ [300, 50], [300, 100], [300, 250], [320, 50], [336, 280], [580, 400], [728, 90] ],
			'sizes_mobile'  => [ [300, 50], [300, 100], [300, 250], [320, 50], [336, 280], [580, 400] ],
		],
		'fullscreen' => [
			'sizes'         => [ [240, 400], [250, 250], [300, 250], [300, 600], [320, 480], [336, 280], [480, 320], [580, 400], [768, 1024], [1024, 768] ],
			'sizes_desktop' => [ [480, 320], [580, 400], [768, 1024], [1024, 768] ],
			'sizes_tablet'  => [ [320, 480], [468, 60], [480, 320], [580, 400] ],
			'sizes_mobile'  => [ [240, 400], [250, 250], [300, 250], [300, 600], [320, 480], [336, 280], [480, 320], [580, 400] ],
		],
		'leaderboard' => [
			'sizes'         => [ [300, 50], [300, 100], [320, 50], [468, 60], [728, 90], [750, 100] ],
			'sizes_desktop' => [ [728, 90], [750, 100] ],
			'sizes_tablet'  => [ [300, 50], [300, 100], [320, 50], [468, 60], [728, 90] ],
			'sizes_mobile'  => [ [300, 50], [300, 100], [320, 50], [468, 60] ],
		],
		'leaderboard-wide' => [
			'sizes'         => [ [300, 50], [300, 100], [320, 50], [468, 60], [728, 90], [750, 100], [970, 90] ],
			'sizes_desktop' => [ [728, 90], [750, 100], [970, 90] ],
			'sizes_tablet'  => [ [300, 50], [300, 100], [320, 50], [468, 60], [728, 90] ],
			'sizes_mobile'  => [ [300, 50], [300, 100], [320, 50], [468, 60] ],
		],
		'native' => [
			'sizes'         => [ 'native' ],
			'sizes_desktop' => [ 'native' ],
			'sizes_tablet'  => [ 'native' ],
			'sizes_mobile'  => [ 'native' ],
		],
		'rectangle-small' => [
			'sizes'         => [ [88, 31], [120, 60], [120, 90], [125, 125] ],
			'sizes_desktop' => [ [88, 31], [120, 60], [120, 90], [125, 125] ],
			'sizes_tablet'  => [ [88, 31], [120, 60], [120, 90], [125, 125] ],
			'sizes_mobile'  => [ [88, 31], [120, 60], [120, 90], [125, 125] ],
		],
		'rectangle-medium' => [
			'sizes'         => [ [200, 200], [250, 250], [300, 250], [300, 300] ],
			'sizes_desktop' => [ [200, 200], [250, 250], [300, 250], [300, 300] ],
			'sizes_tablet'  => [ [200, 200], [250, 250], [300, 250], [300, 300] ],
			'sizes_mobile'  => [ [200, 200], [250, 250], [300, 250], [300, 300] ],
		],
		'rectangle-large' => [
			'sizes'         => [ [200, 200], [250, 250], [300, 250], [300, 300], [320, 480], [336, 280], [580, 400 ] ],
			'sizes_desktop' => [ [200, 200], [250, 250], [300, 250], [300, 300], [320, 480], [336, 280], [580, 400 ] ],
			'sizes_tablet'  => [ [200, 200], [250, 250], [300, 250], [300, 300], [320, 480], [336, 280], [580, 400 ] ],
			'sizes_mobile'  => [ [200, 200], [250, 250], [300, 250], [300, 300], [320, 480], [336, 280], [580, 400 ] ],
		],
		'skyscraper' => [
			'sizes'         => [ [120, 600], [160, 600] ],
			'sizes_desktop' => [ [120, 600], [160, 600] ],
			'sizes_tablet'  => [ [120, 600], [160, 600] ],
			'sizes_mobile'  => [ [120, 600], [160, 600] ],
		],
		'skyscraper-wide' => [
			'sizes'         => [ [120, 600], [160, 600], [300, 600] ],
			'sizes_desktop' => [ [120, 600], [160, 600], [300, 600] ],
			'sizes_tablet'  => [ [120, 600], [160, 600], [300, 600] ],
			'sizes_mobile'  => [ [120, 600], [160, 600], [300, 600] ],
		],
		// To deprecate.
		'button' => [
			'sizes'         => [ [120, 60], [120, 90], [125, 125] ],
			'sizes_desktop' => [ [120, 60], [120, 90], [125, 125] ],
			'sizes_tablet'  => [ [120, 60], [120, 90], [125, 125] ],
			'sizes_mobile'  => [ [120, 60], [120, 90], [125, 125] ],
		],
		'footer' => [
			'sizes'         => [ [320, 50], [468, 60], [728, 90], [970, 90] ],
			'sizes_desktop' => [ [728, 90], [970, 90] ],
			'sizes_tablet'  => [ [320, 50], [468, 60] ],
			'sizes_mobile'  => [ [320, 50] ],
		],
		'halfpage' => [
			'sizes'         => [ [300, 600] ],
			'sizes_desktop' => [ [300, 600] ],
			'sizes_tablet'  => [ [300, 600] ],
			'sizes_mobile'  => [ [300, 600] ],
		],
		'header' => [
			'sizes'         => [ [320, 50], [468, 60], [728, 90], [970, 90] ],
			'sizes_desktop' => [ [728, 90], [970, 90] ],
			'sizes_tablet'  => [ [468, 60], [728, 90] ],
			'sizes_mobile'  => [ [320, 50]],
		],
		'incontent' => [
			'sizes'         => [ [300, 100], [300, 250], [320, 50], [336, 280], [750, 100], [750, 200], [750, 300] ],
			'sizes_desktop' => [ [300, 100], [300, 250], [320, 50], [336, 280], [750, 100], [750, 200], [750, 300] ],
			'sizes_tablet'  => [ [300, 100], [300, 250], [336, 280] ],
			'sizes_mobile'  => [ [300, 100], [300, 250], [336, 280] ],
		],
		'incontent-wide' => [
			'sizes'         => [ [300, 100], [300, 250], [320, 50], [336, 280], [750, 100], [750, 200], [750, 300], [970, 90], [970, 250] ],
			'sizes_desktop' => [ [300, 100], [300, 250], [320, 50], [336, 280], [750, 100], [750, 200], [750, 300], [970, 90], [970, 250] ],
			'sizes_tablet'  => [ [300, 100], [300, 250], [336, 280] ],
			'sizes_mobile'  => [ [300, 100], [300, 250], [336, 280] ],
		],
		'infeed' => [
			'sizes'         => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480], [580, 400] ],
			'sizes_desktop' => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480], [580, 400] ],
			'sizes_tablet'  => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480], [580, 400] ],
			'sizes_mobile'  => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480] ],
		],
		'inrecipe' => [
			'sizes'         => [ [200, 200], [250, 250], [300, 250], [300, 300], [336, 280] ],
			'sizes_desktop' => [ [200, 200], [250, 250], [300, 250], [300, 300], [336, 280] ],
			'sizes_tablet'  => [ [200, 200], [250, 250], [300, 250], [300, 300], [336, 280] ],
			'sizes_mobile'  => [ [200, 200], [250, 250], [300, 250], [300, 300], [336, 280] ],
		],
		'medium-rectangle' => [
			'sizes'         => [ [300, 250] ],
			'sizes_desktop' => [ [300, 250] ],
			'sizes_tablet'  => [ [300, 250] ],
			'sizes_mobile'  => [ [300, 250] ],
		],
		'micro-bar' => [
			'sizes'         => [ [88, 31] ],
			'sizes_desktop' => [ [88, 31] ],
			'sizes_tablet'  => [ [88, 31] ],
			'sizes_mobile'  => [ [88, 31] ],
		],
		'podcast-footer' => [
			'sizes'         => [ [320, 50], [468, 60], [728, 90], [970, 90] ],
			'sizes_desktop' => [ [728, 90], [970, 90] ],
			'sizes_tablet'  => [ [468, 60], [728, 90] ],
			'sizes_mobile'  => [ [320, 50] ],
		],
		'podcast-header' => [
			'sizes'         => [ [320, 50], [468, 60], [728, 90], [970, 90] ],
			'sizes_desktop' => [ [728, 90], [970, 90] ],
			'sizes_tablet'  => [ [468, 60], [728, 90] ],
			'sizes_mobile'  => [ [320, 50] ],
		],
		'sponsored-sidebar' => [
			'sizes'         => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_desktop' => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_tablet'  => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_mobile'  => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
		],
		'sidebar' => [
			'sizes'         => [ [120, 600], [160, 600], [300, 250], [300, 600] ],
			'sizes_desktop' => [ [120, 600], [160, 600], [300, 250], [300, 600] ],
			'sizes_tablet'  => [ [120, 600], [160, 600], [300, 250], [300, 600] ],
			'sizes_mobile'  => [ [120, 600], [160, 600], [300, 250], [300, 600] ],
		],
	],
];