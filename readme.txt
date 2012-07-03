=== KIA Subtitle ===
Contributors: helgatheviking
Donate link: http://www.kathyisawesome.com/436/kia-subtitle/
Tags: subtitle, simple
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The KIA Subtitle plugin  allows you to easily add a subtitle to your posts.

== Description ==

This plugin is adapted from Luc Princen's The Subtitle plugin http://www.to-wonder.com/the-subtitle.  It differs in its class-based organization, uses less jquery, and only saves the post meta when needed. 

The subtitle allows you to easily add a subtitle to your posts and retrieve it in the loop in the same manner as the post title. By using the_subtitle() or get_the_subtitle(). 

It adds a simple inputfield right under the title field of posts, pages and any custom post type.

You can also use the shortcode [the-subtitle] to display it within the post content.


== Installation ==

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the 'the_subtitle()' tag to your theme: 
		`if(function_exists('the_subtitle')) the_subtitle();`
1. if you need to 'return' the value, you can use get_the_subtitle() which accepts a $post_id parameter if you need to use it outside the loop
		`if(function_exists('the_subtitle')) $subtitle = get_the_subtitle($post_id);`

== Screenshots ==

1. This is what the input will look like on the post editor screen.

== Changelog ==

= 1.0 =
* Initial release.