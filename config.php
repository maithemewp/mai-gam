<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

// Desktop: 1024, 768
// Tablet: 728, 480
// Mobile: up to 727.

return [
	'ad_units' => [
		'billboard' => [
			'sizes'         => [ [970, 250] ],
			'sizes_desktop' => [ [970, 250] ],
			'sizes_tablet'  => [ [0, 0] ],
			'sizes_mobile'  => [ [0, 0] ],
		],
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
		'fullscreen' => [
			'sizes'         => [ 'fluid', [320, 480], [468, 60], [480, 320], [768, 1024], [1024, 768] ],
			'sizes_desktop' => [ 'fluid', [768, 1024], [1024, 768] ],
			'sizes_tablet'  => [ 'fluid', [468, 60], [480, 320] ],
			'sizes_mobile'  => [ 'fluid', [320, 50] ],
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
		'infeed' => [
			'sizes'         => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480], [580,400] ],
			'sizes_desktop' => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480], [580,400] ],
			'sizes_tablet'  => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480], [580,400] ],
			'sizes_mobile'  => [ 'fluid', [240, 400], [300, 250], [300, 600], [320, 480] ],
		],
		'inrecipe' => [
			'sizes'         => [ [160, 600], [200, 200], [250, 250], [300, 250], [300, 300], [300, 600], [336, 280] ],
			'sizes_desktop' => [ [160, 600], [200, 200], [250, 250], [300, 250], [300, 300], [300, 600], [336, 280] ],
			'sizes_tablet'  => [ [160, 600], [200, 200], [250, 250], [300, 250], [300, 300], [300, 600], [336, 280] ],
			'sizes_mobile'  => [ [160, 600], [200, 200], [250, 250], [300, 250], [300, 300], [300, 600], [336, 280] ],
		],
		'leaderboard' => [
			'sizes'         => [ [300, 50], [320, 50], [336, 280], [728, 90], [970, 90], [970, 250] ],
			'sizes_desktop' => [ [728, 90], [970, 90], [970, 250] ],
			'sizes_tablet'  => [ [300, 50], [320, 50], [728, 90] ],
			'sizes_mobile'  => [ [300, 50], [320, 50], [336, 280] ],
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
		'skyscraper' => [
			'sizes'         => [ [120, 600], [160, 600] ],
			'sizes_desktop' => [ [120, 600], [160, 600] ],
			'sizes_tablet'  => [ [120, 600], [160, 600] ],
			'sizes_mobile'  => [ [120, 600], [160, 600] ],
		],
		'sponsored-sidebar' => [
			'sizes'         => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_desktop' => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_tablet'  => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_mobile'  => [ [160, 600], [300, 250], [300, 600], [320, 50] ],
		],
		'sidebar' => [
			'sizes'         => [ [120, 600], [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_desktop' => [ [120, 600], [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_tablet'  => [ [120, 600], [160, 600], [300, 250], [300, 600], [320, 50] ],
			'sizes_mobile'  => [ [120, 600], [160, 600], [300, 250], [300, 600], [320, 50] ],
		],
	],
];