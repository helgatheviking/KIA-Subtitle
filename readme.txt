=== KIA Subtitle ===
Contributors: Kathy Darling 
Donate link: http://kathyisawesome/kia-subtitle
Tags: subtitle, simple
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 1.0

The subtitle allows you to easily add a subtitle to your posts and retrieve it in the loop in the same manner as the post title. By using the_subtitle() or get_the_subtitle().

== Description ==

This plugin is adapted from Luc Princen's The Subtitle plugin http://www.to-wonder.com/the-subtitle.  It differs in its class-based organization and fixes some issues that I had with that version. 

The subtitle allows you to easily add a subtitle to your posts and retrieve it in the loop in the same manner as the post title. By using the_subtitle() or get_the_subtitle(). 

It adds a simple inputfield right under the title field of posts, pages and any custom post type.

You can also use the shortcode [the-subtitle] to display it.


== Installation ==

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the 'the_subtitle()' tag to your theme: if(function_exists('the_subtitle')) the_subtitle();

== Changelog ==

= 1.0 =
* Initial release.