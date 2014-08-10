# WordPress Whit Stillman theme

This is the WordPress theme used on [WhitStillman.org](http://www.whitstillman.org). 

It is a child theme of the standard Twenty Thirteen WP theme.

These files should be in a directory in `wp-content/themes/`. There *must* also be the `wp-content/themes/twentythirteen/` directory present.

## Notes

When customising the theme in the WP admin screens, select 'Randomize suggested headers'. 

Then, to add a new header:

1. Put the image (1600x230) and its thumbnail version (460x66) in `images/headers/`.

2. Add an entry for that image in the `whitstillman_get_headers()` function in `functsion.php`.

