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
 * Information about all the possible header images.
 * Each image should be in themename/images/headers/.
 * It should have a 1600x230 image and a 460x66 thumbnail.
 * If the big image is 'damsels1.jpg' the thumbnail is 'damsels1-thumbnail.jpg'
 */
function whitstillman_get_headers() {
	return array(
		// One array per film/show/book/etc.
		array(
			// An array of lowercased tags that we'll try and match to the 
			// page/post. If one of them does, we use an image from this set.
			'tags'			=> array('barcelona'),
			// If no tags match then we'll try and match lowercased
			// words/phrases from this array.
			'title_words'	=> array('barcelona'),
			// A list of the images for this property.
			// eg, if 'barcelona1' is in this array, there should be 
			// a 'barcelona1.jpg' image available.
			'images'		=> array('barcelona1'),	
		),
		array(
			'tags'			=> array('damsels in distress'),
			'title_words'	=> array('damsels'),
			'images'		=> array('damsels1'),
		),
		array(
			'tags'			=> array('metropolitan'),
			'title_words'	=> array('metropolitan'),
			'images'		=> array('metropolitan1', 'metropolitan2'),
		),
	);
};


/**
 * Remove the parent theme's default header images.
 */
function whitstillman_remove_default_headers () {
	unregister_default_headers (array('circle', 'diamond', 'star'));
}
add_action ('after_setup_theme', 'whitstillman_remove_default_headers', 100);


/**
 * Make the array for a single image, used to register_default_headers.
 * $name is like 'barcelona1', if the image file is 'barcelona1.jpg'.
 */
function whitstillman_make_header_data($name) {
	return array(
		'url' => '%2$s/images/headers/'.$name.'.jpg',
		'thumbnail_url' => '%2$s/images/headers/'.$name.'-thumbnail.jpg',
		'description' => __( $name, 'twentythirteen' )
	);
}


/**
 * Add all the possible Whit Stillman site headers.
 */
function whitstillman_add_headers () {
	$ws_headers = whitstillman_get_headers();
	$headers = array();

	foreach ($ws_headers as $header_data) {
		foreach ($header_data['images'] as $image) {
			$headers[$image] = whitstillman_make_header_data($image);
		};
	};
	register_default_headers( $headers );
}
add_action ('after_setup_theme', 'whitstillman_add_headers', 100);


/**
 * Having already set all the possible header images, we now see if this 
 * post/page is about a specific property. If so we choose an image for that.
 * Otherwise, we do whatever WP would otherwise do (eg, randomise the header).
 */
function whitstillman_set_specific_header() {
	$ws_headers = whitstillman_get_headers();
	// This will be the name of the specific image we use, if any.
	$chosen_image = FALSE;

	$post_tags = get_the_tags();
	if ($post_tags) {
		foreach($post_tags as $tag) {
			foreach($ws_headers as $header_data) {
				foreach($header_data['tags'] as $header_tag) {
					if ($header_tag == strtolower($tag->name)) {
						// This page has a tag that matches one of the tags for 
						// this header image property. So choose a random image
						// from it.
						$chosen_image = $header_data['images'][ array_rand($header_data['images']) ];
						break 3;
					}
				}
			}
		}
	}
	// If there are no tags for the page, or we didn't find a matching image,
	// we try the text of the page title.
	if ( ! $post_tags || $chosen_image === FALSE) {
		$title = get_the_title();
		if ($title) {
			foreach($ws_headers as $header_data) {
				foreach($header_data['title_words'] as $word) {
					if (strpos(strtolower($title), $word) !== FALSE) {
						// The page title contains a word/phrase that was set 
						// for this header image property. So choose a random 
						// image from it.
						$chosen_image = $header_data['images'][ array_rand($header_data['images']) ];
						break 2;
					}
				}
			}
		}
	};

	if ($chosen_image) {
		// We have a specific image to use.
		// So we want to unregister all of the images OTHER THAN this one.
		// This will just be an array of the image names:
		$headers_to_unregister = array();
		foreach($ws_headers as $header_data) {
			foreach($header_data['images'] as $image) {
				if ($image != $chosen_image) {
					array_push($headers_to_unregister, $image);
				}
			}
		};
		unregister_default_headers($headers_to_unregister);

		// And now just register our one chosen image.
		register_default_headers( array(
			$chosen_image => whitstillman_make_header_data($chosen_image)
		));
	}
}
// No idea if this is the ideal place to run this, but it seems to work.
add_action('get_header', 'whitstillman_set_specific_header');

?>