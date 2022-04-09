=== KIA Subtitle ===
Contributors: helgatheviking
Donate link: https://www.paypal.com/fundraiser/charity/1451316
Tags: subtitle, simple
Requires at least: 4.5
Tested up to: 5.9.3
Stable tag: 3.0.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The KIA Subtitle plugin allows you to easily add a subtitle to your posts.

== Description ==

KIA subtitle allows you to easily add a subtitle to your posts and retrieve it in the loop in the same manner as the post title. By using `the_subtitle()` or `get_the_subtitle()`.

It adds a simple input field right under the title field of posts, pages and any custom post type.  It also add a subtitle column to the edit screen as well as to the quick edit.

You can also use the shortcode `[the-subtitle]` to display it within the post content.

= Displaying the subtitle on the front-end =

This plugin does _not_ attempt to output the subtitle. With an infinite number of themes, it is not possible for us to support that. The onus is on the user to customize their theme accordingly.

This plugin creates an `the_subtitle()` template tag that can be used in your theme's templates as follows:

`
if( function_exists( 'the_subtitle' ) ) the_subtitle();
`

You can wrap the string in some markup using the *$before* and *$after* parameters.
`
if( function_exists( 'the_subtitle' ) ) the_subtitle( '<h2 class="subtitle">', '</h2>' );
`

= WooCommerce support =

There is a small [bridge plugin](https://github.com/helgatheviking/kia-subtitle-woocommerce-bridge) you can install and activate to automatically display the subtitle in most WooCommerce locations. This will work for all themes that are using WooCommerce's default hooks.

*NB:* It's known that the Ocean WP theme has it's own hooks in the WooCommerce templates. You will need to alter the bridge plugin... please take a look at this [support thread](https://wordpress.org/support/topic/compatibility-with-latest-wp-and-wc/#post-15456180).


= WPML Ready =

KIA Subtitle has been tested by WPML and will allow you to translate the subtitle on multilingual sites.

= Support =

Support is handled in the [WordPress forums](http://wordpress.org/support/plugin/kia-subtitle). Please note that support is limited and does not cover any custom implementation of the plugin.

Please report any bugs, errors, warnings, code problems to [Github](https://github.com/helgatheviking/KIA-Subtitle/issues)

== Installation ==

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the 'the_subtitle()' tag to your theme:
		`if(function_exists('the_subtitle')) the_subtitle();`
1. if you need to 'return' the value, you can use `get_the_subtitle()` which accepts a `$post_id` parameter if you need to use it outside the loop
		`if(function_exists('the_subtitle')) $subtitle = get_the_subtitle( $post_id );`
1. As of version 1.2 `the_subtitle` accepts 3 parameters: `the_subtitle( $before = Null, $after = Null, $echo = True );`
1. As of version 1.3.4, there is a filter for `the_subtitle`

== Screenshots ==

1. This is what the input will look like in the Block Editor.
1. This is what the input will look like in the Classic Editor.

== Frequently Asked Questions ==

= How do I display the subtitle in my theme? =

The simplest way is with the `the_subtitle()` template tag as follows:
`
if( function_exists( 'the_subtitle' ) ) the_subtitle();
`

You can wrap the string in some markup using the *$before* and *$after* parameters.
`
if( function_exists( 'the_subtitle' ) ) the_subtitle( '<h2 class="subtitle">', '</h2>' );
`

= Where do I add this code? =

Unfortunately, I cannot tell you *exactly* what file to place the above code in because 1. I don't know where you want to display the subtitle and 2. every theme's structure is different.

However, in general, `the_subtitle()` is a template tag so you will want to put it in a template file.  Probably, you are looking for the file that contains your post loop.  For most themes it's *single.php* ( or *page.php* for pages ), but for many it could also be *content.php*.  Assuming you want the subtitle to display directly after your main title, you'd place the above code after:

`
<h1 class="entry-title"><?php the_title(); ?></h1>
`

As an *example* if you wanted to display the subtitle on standard single posts, in the Twenty Twenty theme you'd create a copy of the entry-header.php template in your child theme and modify it as shown in this [gist](https://gist.github.com/helgatheviking/6754a8a381ace9aef325ca3f7b4128c1)

= How do I style the subtitle? =

If you have wrapped the subtitle in an H2 tag with the class of subtitle like in the gist above, you can then style it any way you'd like.
`
.subtitle { color: pink; }
`

= Can I display the subtitle for my WooCommmerce products =

Yes! You can use this [bridge plugin](https://github.com/helgatheviking/kia-subtitle-woocommerce-bridge) to automatically display the subtitle in most WooCommerce locations.

= Can I add the subtitle to the Page Title Meta tag =
`
function kia_add_subtitle_to_wp_title( $title ) {
	if ( is_single() && function_exists('get_the_subtitle')) && $subtitle == get_the_subtitle( get_the_ID() ) ) {
	$title .= $subtitle;
	}
}
add_filter('wp_title','kia_add_subtitle_to_wp_title');
`

= Is this translation ready? =
WPML now supports KIA Subtitle!

== Changelog ==

= 3.0.3 =
* Fix: Check subtitle is set before updating.

= 3.0.2 =
* Fix: Do not load Gutenberg assets if CPT does not support 'custom-fields'. Replace with fallback metabox.

= 3.0.1 =
* Fix: Remove the duplicate metabox for post types using Classic Editor.

= 3.0.0 =
* Add subtitle as a panel in the Gutenberg editor

= 2.0.0 =
* Add subtitle as a metabox that is compatible with Gutenberg editor

= 1.6.8 =
* Add width to column for WooCommerce products

= 1.6.7 =
* Update donation link
* Update required and tested against versions
* Fix column location for WooCommerce products
* Minify admin script

= 1.6.6 =
* Insert subtitle after title, or at end if subtitle does not exist

= 1.6.5 =
* Add wpml-config.xml for compatibility with WPML

= 1.6.4 =
* Add link to plugin settings
* testing against WP4.4

= 1.6.3 =
* fix docblock

= 1.6.2 =
* save subtitles on attachments. Apparently attachments don't fire save_post hook

= 1.6.1 =
* resolve PHP warnings in strict-standards mode

= 1.6 =
* switch to KIA_Subtitle() instance versus global variable

= 1.5.4 =
* restored accidentally deleted script for quick edit

= 1.5.4 =
* remove unneeded script code now that input is using 'placeholder'
* remove tabindex on input (wasn't doing anything anyway)
* add script to tab from title to subtitle, to content. props @Giuseppe Mazzapica
* add readme.md

= 1.5.3 =
* verify WP3.8 compatibility
* remove backcompat on edit_form_after_title hook
* better docbloc

= 1.5.2 =
* Move changelog back to readme.txt #facepalm

= 1.5.1 =
* Switch sanitization to less restrictive sanitize_post_field, which matches how the main post title is sanitized by WordPress
* Move changelog to separate file

= 1.5 =
* Switch options to "check to enable" instead of "check to disable" (all post types are enabled by default)
* Include upgrade routine to switch any old options to new format
* Update FAQ with example for Twenty Twelve

= 1.4.3 =
* Adjust $args for get_post_types()
* Fix buggy conditional logic for users with no post types excluded

= 1.4.2 =
* Adjust $args for get_post_types()
* switch 'kia_subtitle_post_types' filter to 'kia_subtitle_post_type_args'

= 1.4.1 =
* Adjust $args for get_post_types()
* add 'kia_subtitle_post_types' filter to plugin's options

= 1.4 =
* Add ability to exclude subtitle from certain post types

= 1.3.4 =
* Add filter `the_subtitle` to allow subtitle content to be modified

= 1.3.3 =
* Fix Notice: Undefined property
* Clean up enqueue scripts

= 1.3.2 =
* Fix for back-compatibility

= 1.3.1 =
* Add example code to FAQ

= 1.3 =
* Better escaping of HTML attributes thanks to @nealpoole
* Take advantage of new action hook in WP 3.5

= 1.2 =
* Mimic the_title(), so the_subtitle() now accepts before, after and echo parameters:
	`the_subtitle( $before = '', $after = '', $echo = true )`

= 1.1.2 =
* Fixed quick edit refresh ( second click on quick edit for same item and the value still reflected the original )

= 1.1.1 =
* Fix ability to remove subtitle

= 1.1 =
* Add column to edit.php screen
* Add subtitle to quick edit
* Load script on edit.php screen again

= 1.0.2 =
* update donate link

= 1.0.1 =
* Don't load script on edit.php screen

= 1.0 =
* Initial release.
