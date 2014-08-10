<?php
/**
 * Twenty Thirteen Child Theme functions and definitions.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.1
 */

/* This is loaded BEFORE the parent's functions.php. */

/**
 * We don't use theme thumbnails.
 */
function whitstillman_setup() {
	remove_theme_support('post-thumbnails');
}
add_action( 'after_setup_theme', 'whitstillman_setup', 100 );

/**
 * Description: Custom function to prevent post thumbnails from loading on
 * search, category, and archive pages.
 * @param string|array $metadata - Always null for post metadata.
 * @param int $object_id - Post ID for post metadata
 * @param string $meta_key - metadata key.
 * @param bool $single - Indicates if processing only a single $metadata value or array of values.
 * @return Original or Modified $metadata.
 */
function remove_post_thumbnail($metadata, $object_id, $meta_key, $single){

	// Return false if the current filter is that of a post thumbnail.
	// Otherwise, return the original $content value.
	return ( isset($meta_key) && '_thumbnail_id' === $meta_key ) ? false : $metadata;
}
add_filter('get_post_metadata', 'remove_post_thumbnail', true, 4);


/**
 * Add the font JavaScript for the title fonts.
 */
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
	// One array per film/show/book/etc.
	return array(
		// BARCELONA.
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
		// COSMOPOLITANS
		array(
			'tags'			=> array('cosmopolitans', 'the cosmopolitans'),
			'title_words'	=> array('cosmopolitans'),
			'images'		=> array('cosmopolitans1'),
		),
		// DAMSELS IN DISTRESS.
		array(
			'tags'			=> array('damsels in distress'),
			'title_words'	=> array('damsels'),
			'images'		=> array('damsels1'),
		),
		// LAST DAYS OF DISCO.
		array(
			'tags'			=> array('the last days of disco', 'last days of disco'),
			'title_words'	=> array('disco'),
			'images'		=> array('disco1'),
		),
		// METROPOLITAN.
		array(
			'tags'			=> array('metropolitan'),
			'title_words'	=> array('metropolitan'),
			'images'		=> array('metropolitan3', 'metropolitan4'),
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



/**
 * A copy of the parent's function, removing the category and tag elements.
 */
function twentythirteen_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'twentythirteen' ) . '</span>';

	if ( ! has_post_format( 'link' ) && 'post' == get_post_type() )
		twentythirteen_entry_date();

	// Post author
	if ( 'post' == get_post_type() ) {
		printf( '<span class="author vcard">By <a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'twentythirteen' ), get_the_author() ) ),
			get_the_author()
		);
	}
}

/**
 * The category and tag elements from twentythirteen_entry_meta(), which we 
 * want to output separately.
 * Used in content.php.
 */
function whitstillman_entry_meta_extra() {
	// Translators: used between list items, there is a space after the comma.
	//$categories_list = get_the_category_list( __( ', ', 'twentythirteen' ) );
	//if ( $categories_list ) {
		//echo '<span class="categories-links">Category: ' . $categories_list . '</span>';
	//}

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'twentythirteen' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">Tagged: ' . $tag_list . '</span>';
	}
}
?>
