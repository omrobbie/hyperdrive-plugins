<?php

/*
Plugin Name: Hyperdrive
Plugin URI: https://github.com/omrobbie/hyperdrive-plugins.git
Description: Including Josh functions to the plugin
Author: Josh Habdas
Version: 1.0
*/


/* Dequeue obsolete/unwanted scripts */
function wpdocs_dequeue_script() {
    wp_dequeue_script('twentyseventeen-skip-link-focus-fix');
    wp_dequeue_script('twentyseventeen-global');
    wp_dequeue_script('jquery-scrollto');
}
add_action( 'wp_print_scripts', 'wpdocs_dequeue_script', 100 );


/**
 * Load scripts using Fetch Inject instead of wp_enqueue_script
 * for for faster page loads and lower perceived latency.
 *
 * @since WordCamp Ubud 2017
 * @link https://wordpress.stackexchange.com/a/263733/117731
 * @link https://github.com/jhabdas/fetch-inject
 *
 */
function wc_add_inline_script() {
	$twentyseventeen_l10n = array(
	 	'quote' => twentyseventeen_get_svg( array( 'icon' => 'quote-right' ) ),
	 	'expand' => __( 'Expand child menu', 'twentyseventeen' ),
	 	'collapse' => __( 'Collapse child menu', 'twentyseventeen' ),
	 	'icon' => twentyseventeen_get_svg( array( 'icon' => 'angle-down', 'fallback' => true ) )
	 );
	$jquery_script_path = '/wp-includes/js/jquery/jquery.js';
	$jquery_dependent_script_paths = [
		get_theme_file_uri( '/assets/js/global.js' ),
		get_theme_file_uri( '/assets/js/jquery.scrollTo.js' ),
		get_theme_file_uri( '/assets/js/skip-link-focus-fix.js' ),
		get_theme_file_uri( '/assets/js/navigation.js' )
	];
	$webfonts_path = twentyseventeen_fonts_url();
	$screen_reader_text_object_name = 'twentyseventeenScreenReaderText';
	$twentyseventeen_l10n_data_json = json_encode($twentyseventeen_l10n);
	$jquery_dependent_script_paths_json = json_encode($jquery_dependent_script_paths);
	$inline_script = <<<EOD
window.{$screen_reader_text_object_name} = $twentyseventeen_l10n_data_json;
(function () {
	'use strict';
	if (!window.fetch) return;
	/**
	 * Fetch Inject v1.6.8
	 * Copyright (c) 2017 Josh Habdas
	 * @licence ISC
	 */
	var fetchInject=function(){"use strict";const e=function(e,t,n,r,o,i,c){i=t.createElement(n),c=t.getElementsByTagName(n)[0],i.type=r.blob.type,i.appendChild(t.createTextNode(r.text)),i.onload=o(r),c?c.parentNode.insertBefore(i,c):t.head.appendChild(i)},t=function(t,n){if(!t||!Array.isArray(t))return Promise.reject(new Error("`inputs` must be an array"));if(n&&!(n instanceof Promise))return Promise.reject(new Error("`promise` must be a promise"));const r=[],o=n?[].concat(n):[],i=[];return t.forEach(e=>o.push(window.fetch(e).then(e=>{return[e.clone().text(),e.blob()]}).then(e=>{return Promise.all(e).then(e=>{r.push({text:e[0],blob:e[1]})})}))),Promise.all(o).then(()=>{return r.forEach(t=>{i.push({then:n=>{"text/css"===t.blob.type?e(window,document,"style",t,n):e(window,document,"script",t,n)}})}),Promise.all(i)})};return t}();
	fetchInject([
		"{$webfonts_path}"
	])
	fetchInject(
		$jquery_dependent_script_paths_json
	, fetchInject([
		"{$jquery_script_path}"
	]));
})();
EOD;
	echo "<script>{$inline_script}</script>";
}
add_action('wp_head', 'wc_add_inline_script', 0);

?>