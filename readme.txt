=== KIA Subtitle ===
Contributors: helgatheviking
Donate link: https://inspirepay.com/pay/helgatheviking
Tags: subtitle, simple
Requires at least: 3.4
Tested up to: 3.6
Stable tag: 1.5.1
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

= Where can I report bugs? =

Please report any issues at: https://github.com/helgatheviking/KIA-Subtitle/issues