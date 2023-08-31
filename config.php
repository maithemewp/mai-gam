<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

return [
	'ad_units' => [
		'billboard' =? [
			'sizes'         => [ '970x250' ],
			'sizes_desktop' => [ '970x250' ],
			'sizes_tablet'  => [ '1x1' ],
			'sizes_mobile'  => [ '1x1' ],
			'post_title'    => __( 'Billboard', 'mai-gam' ),
			'post_content'  => '',
		],
		'button' =? [
			'sizes'         => [ '125x125', '120x90', '120x60' ],
			'sizes_desktop' => [ '125x125', '120x90', '120x60' ],
			'sizes_tablet'  => [ '125x125', '120x90', '120x60' ],
			'sizes_mobile'  => [ '125x125', '120x90', '120x60' ],
			'post_title'    => __( 'Button', 'mai-gam' ),
			'post_content'  => '',
		],
		'footer' => [
			'sizes'         => [ '320x50', '468x60', '728x90', '970x90' ],
			'sizes_desktop' => [ '728x90', '970x90' ],
			'sizes_tablet'  => [ '320x50', '468x60' ],
			'sizes_mobile'  => [ '320x50' ],upd
			'post_title'    => __( 'Footer', 'mai-gam' ),
			'post_content'  => '',
		],
		'fullscreen' => [
			'sizes'         => [ '320x480', '468x60', '480x320', '768x1024', '1024x768' ],
			'sizes_desktop' => [ '768x1024', '1024x768' ],
			'sizes_tablet'  => [ '468x60', '480x320' ],
			'sizes_mobile'  => [ '320x50' ],
			'post_title'    => __( 'Fullscreen', 'mai-gam' ),
			'post_content'  => '',
		],
		'halfpage' =? [
			'sizes'         => [ '300x600' ],
			'sizes_desktop' => [ '300x600' ],
			'sizes_tablet'  => [ '300x600' ],
			'sizes_mobile'  => [ '300x600' ],
			'post_title'    => __( 'Halfpage', 'mai-gam' ),
			'post_content'  => '',
		],
		'header' => [
			'sizes'         => [ '320x50', '468x60', '728x90', '970x90', '970x250' ],
			'sizes_desktop' => [ '970x250', '970x90' ],
			'sizes_tablet'  => [ '728x90', '468x60' ],
			'sizes_mobile'  => [ '320x50'],
			'post_title'    => __( 'Header', 'mai-gam' ),
			'post_content'  => '',
		],
		'incontent' => [
			'sizes'         => [ '300x100', '300x250', '336x280', '750x100', '750x200', '750x300', '970x66', '970x250' ],
			'sizes_desktop' => [ '970x250', '750x300', '750x200', '750x100', '970x66' ],
			'sizes_tablet'  => [ '336x280', '300x250', '300x100' ],
			'sizes_mobile'  => [ '336x280', '300x250', '300x100' ],
			'post_title'    => __( 'In-content', 'mai-gam' ),
			'post_content'  => '',
		],
		'infeed' => [
			'sizes'         => [ '240x400', '300x250', '300x600' ],
			'sizes_desktop' => [ '300x600', '300x250', '240x400' ],
			'sizes_tablet'  => [ '300x600', '300x250', '240x400' ],
			'sizes_mobile'  => [ '300x600', '300x250', '240x400' ],
			'post_title'    => __( 'In-feed', 'mai-gam' ),
			'post_content'  => '',
		],
		'inrecipe' => [
			'sizes'         => [ '200x200', '250x250', '300x300', '400x400' ],
			'sizes_desktop' => [ '400x400', '300x300', '250x250', '200x200' ],
			'sizes_tablet'  => [ '400x400', '300x300', '250x250', '200x200' ],
			'sizes_mobile'  => [ '300x300', '250x250', '200x200'],
			'post_title'    => __( 'In-recipe', 'mai-gam' ),
			'post_content'  => '',
		],
		'leaderboard' =? [
			'sizes'         => [ '970x90', '728x90', '320x50', '300x50' ],
			'sizes_desktop' => [ '970x90', '728x90' ],
			'sizes_tablet'  => [ '728x90' ],
			'sizes_mobile'  => [ '320x50', '300x50' ],
			'post_title'    => __( 'Leaderboard', 'mai-gam' ),
			'post_content'  => '',
		]
		'medium-rectangle' =? [
			'sizes'         => [ '300x250' ],
			'sizes_desktop' => [ '300x250' ],
			'sizes_tablet'  => [ '300x250' ],
			'sizes_mobile'  => [ '300x250' ],
			'post_title'    => __( 'Medium Rectangle', 'mai-gam' ),
			'post_content'  => '',
		],
		'micro-bar' =? [
			'sizes'         => [ '88x31' ],
			'sizes_desktop' => [ '88x31' ],
			'sizes_tablet'  => [ '88x31' ],
			'sizes_mobile'  => [ '88x31' ],
			'post_title'    => __( 'Micro Bar', 'mai-gam' ),
			'post_content'  => '',
		],
		'podcast-footer' => [
			'sizes'         => [ '320x50', '468x60', '728x90', '970x90' ],
			'sizes_desktop' => [ '970x90', '728x90' ],
			'sizes_tablet'  => [ '728x90', '468x60' ],
			'sizes_mobile'  => [ '320x50' ],
			'post_title'    => __( 'Podcast Footer', 'mai-gam' ),
			'post_content'  => '',
		],
		'podcast-header' => [
			'sizes'         => [ '320x50', '468x60', '728x90', '970x90' ],
			'sizes_desktop' => [ '970x90', '728x90' ],
			'sizes_tablet'  => [ '728x90', '468x60' ],
			'sizes_mobile'  => [ '320x50' ],
			'post_title'    => __( 'Podcast Header', 'mai-gam' ),
			'post_content'  => '',
		],
		'skyscraper' =? [
			'sizes'         => [ '160x600', '120x600' ],
			'sizes_desktop' => [ '160x600', '120x600' ],
			'sizes_tablet'  => [ '160x600', '120x600' ],
			'sizes_mobile'  => [ '160x600', '120x600' ],
			'post_title'    => __( 'Skyscraper', 'mai-gam' ),
			'post_content'  => '',
		],
		'sponsored-sidebar' =? [
			'sizes'         => [ '320x50', '300x600', '300x250', '160x600' ],
			'sizes_desktop' => [ '320x50', '300x600', '300x250', '160x600' ],
			'sizes_tablet'  => [ '320x50', '300x600', '300x250', '160x600' ],
			'sizes_mobile'  => [ '320x50', '300x600', '300x250', '160x600' ],
			'post_title'    => __( 'Sponsored Sidebar', 'mai-gam' ),
			'post_content'  => '',
		]
	]
];