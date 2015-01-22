=== KIA Subtitle ===
Contributors: helgatheviking
Donate link: https://inspirepay.com/pay/helgatheviking
Tags: subtitle, simple
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.6.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The KIA Subtitle plugin allows you to easily add a subtitle to your posts.

== Description ==

This plugin is adapted from [Luc Princen's The Subtitle plugin](http://www.to-wonder.com/the-subtitle).  It differs in its class-based organization, uses less jquery, and only saves the post meta when needed.

The subtitle allows you to easily add a subtitle to your posts and retrieve it in the loop in the same manner as the post title. By using `the_subtitle()` or `get_the_subtitle()`.

It adds a simple inputfield right under the title field of posts, pages and any custom post type.  It also add a subtitle column to the edit screen as well as to the quick edit.

You can also use the shortcode `[the-subtitle]` to display it within the post content.

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

1. This is what the input will look like on the post editor screen.

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

As an *example* if you wanted to display the subtitle on standard single posts, in the Twenty Twelve theme you'd edit the content.php ( or preferabbly override the template in a child theme ):

`
<header class="entry-header">
	<?php the_post_thumbnail(); ?>
	<?php if ( is_single() ) : ?>
	<h1 class="entry-title"><?php the_title(); ?></h1>
        <?php if( function_exists( 'the_subtitle' ) ) the_subtitle( '<h2 class="subtitle">', '</h2>' ); ?>
	<?php else : ?>
	<h1 class="entry-title">
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
	</h1>
	<?php endif; // is_single() ?>
	<?php if ( comments_open() ) : ?>
		<div class="comments-link">
			<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'twentytwelve' ) . '</span>', __( '1 Reply', 'twentytwelve' ), __( '% Replies', 'twentytwelve' ) ); ?>
		</div><!-- .comments-link -->
	<?php endif; // comments_open() ?>
</header><!-- .entry-header -->
`

= How do I style the subtitle? =

If you have wrapped the subtitle in an H2 tag with the class of subtitle like in the second example above, you can then style it any way you'd like.
`
h2.subtitle { color: pink; }
`

== Changelog ==

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