=== KIA Subtitle ===
Contributors: helgatheviking
Donate link: https://inspirepay.com/pay/helgatheviking
Tags: subtitle, simple
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The KIA Subtitle plugin  allows you to easily add a subtitle to your posts.

== Description ==

This plugin is adapted from Luc Princen's The Subtitle plugin http://www.to-wonder.com/the-subtitle.  It differs in its class-based organization, uses less jquery, and only saves the post meta when needed.

The subtitle allows you to easily add a subtitle to your posts and retrieve it in the loop in the same manner as the post title. By using the_subtitle() or get_the_subtitle().

It adds a simple inputfield right under the title field of posts, pages and any custom post type.  It also add a subtitle column to the edit screen as well as to the quick edit.

You can also use the shortcode [the-subtitle] to display it within the post content.


== Installation ==

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the 'the_subtitle()' tag to your theme:
		`if(function_exists('the_subtitle')) the_subtitle();`
1. if you need to 'return' the value, you can use `get_the_subtitle()` which accepts a `$post_id` parameter if you need to use it outside the loop
		`if(function_exists('the_subtitle')) $subtitle = get_the_subtitle( $post_id );`
1. As of version 1.2 `the_subtitle` accepts 3 parameters: `the_subtitle( $before = Null, $after = Null, $echo = True );`

== Screenshots ==

1. This is what the input will look like on the post editor screen.

== Bug Reporting ==

Please report any issues at: https://github.com/helgatheviking/KIA-Subtitle/issues

== FAQ ==

= How do I display the subtitle in my theme? =

The simplest way is as follows.
`
if( function_exists( 'the_subtitle' ) ) the_subtitle();
`

You can wrap the string in some markup using the *$before* and *$after* parameters.
`
if( function_exists( 'the_subtitle' ) ) the_subtitle( '<h2 class="subtitle">', '</h2>' );
`

= How do I style the subtitle? =

If you have wrapped the subtitle in an H2 tag with the class of subtitle like in the second example above, you can then style it any way you'd like.
`
h2.subtitle { color: pink; }
`

== Changelog ==

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