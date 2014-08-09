<?php
/**
 * Twenty Thirteen Child Theme functions and definitions.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.1
 */

/* This is loaded BEFORE the parent's functions.php. */


function whitstillman_load_scripts() {
	wp_enqueue_script('whitstillman-script-fonts', get_stylesheet_directory_uri().'/js/fonts.js');
}
add_action( 'wp_enqueue_scripts', 'whitstillman_load_scripts' );


/**
 * We don't use the Google Fonts the parent theme uses, so get rid of them.
 * Note: The admin bar uses Open Sans, so if you're logged in then that will 
 * still be loaded from Google.
 */
function whitstillman_deregister_fonts(){
	wp_deregister_style('twentythirteen-fonts');
}
add_action( 'wp_enqueue_scripts', 'whitstillman_deregister_fonts', 100 );


/**
 * Remove the parent theme's default header images.
 */
function whitstillman_remove_default_headers () {
	unregister_default_headers (array('circle', 'diamond', 'star'));
}
add_action ('after_setup_theme', 'whitstillman_remove_default_headers', 100);


function whitstillman_add_headers () {
	register_default_headers( array(
		'header1' => array(
			'url' => '%2$s/images/headers/metropolitan1.jpg',
			'thumbnail_url' => '%2$s/images/headers/metropolitan1-thumbnail.jpg',
			'description' => __( 'Metropolitan 1', 'twentythirteen' )
		),
		'header2' => array(
			'url' => '%2$s/images/headers/metropolitan2.jpg',
			'thumbnail_url' => '%2$s/images/headers/metropolitan2-thumbnail.jpg',
			'description' => __( 'Metropolitan 2', 'twentythirteen' )
		),
		'header3' => array(
			'url' => '%2$s/images/headers/damsels1.jpg',
			'thumbnail_url' => '%2$s/images/headers/damsels1-thumbnail.jpg',
			'description' => __( 'Damsels 1', 'twentythirteen' )
		),
	)); // end of array
} // end of main function
add_action ('after_setup_theme', 'whitstillman_add_headers');


?>
