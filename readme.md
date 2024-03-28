# KIA Subtitle #

The KIA Subtitle plugin allows you to add a subtitle to your posts.

This is a developmental repo. Clone this repo and run `npm install && npm run build`   
OR    
|[Download latest release](https://github.com/helgatheviking/KIA-Subtitle/releases/latest)|


## Description ##

KIA subtitle allows you to add a subtitle to your posts and retrieve it in the loop in the same manner as the post title. By using `the_subtitle()` or `get_the_subtitle()`.

It adds a simple input field right under the title field of posts, pages and any custom post type.  It also add a subtitle column to the edit screen as well as to the quick edit.

You can also use the shortcode `[the-subtitle]` to display it within the post content.

### WPML Ready ###

KIA Subtitle has been tested by WPML and will allow you to translate the subtitle multilingual sites.

### Support ###

Support is handled in the [WordPress forums](http://wordpress.org/support/plugin/kia-subtitle). Please note that support is limited and does not cover any custom implementation of the plugin.

Please report any bugs, errors, warnings, code problems to [Github](https://github.com/helgatheviking/KIA-Subtitle/issues)

## Installation ##

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the 'the_subtitle()' tag to your theme:
		`if (function_exists( 'the_subtitle' ) ) the_subtitle();`
1. if you need to 'return' the value, you can use `get_the_subtitle()` which accepts a `$post_id` parameter if you need to use it outside the loop
		`if (function_exists( 'the_subtitle' ) ) $subtitle = get_the_subtitle( $post_id );`
1. As of version 1.2 `the_subtitle` accepts 3 parameters: `the_subtitle( $before = Null, $after = Null, $echo = True );`
1. As of version 1.3.4, there is a filter for `the_subtitle`

## Screenshots ##

1. This is what the input will look like on the post editor screen.

## Frequently Asked Questions ##

### How do I display the subtitle in my theme? ###

The simplest way is with the `the_subtitle()` template tag as follows:

	if ( function_exists( 'the_subtitle' ) ) the_subtitle();


You can wrap the string in some markup using the *$before* and *$after* parameters.

	if ( function_exists( 'the_subtitle' ) ) the_subtitle( '<h2 class="subtitle">', '</h2>' );


### Where do I add this code? ###

Unfortunately, I cannot tell you *exactly* what file to place the above code in because 1. I don't know where you want to display the subtitle and 2. every theme's structure is different.

However, in general, `the_subtitle()` is a template tag so you will want to put it in a template file.  Probably, you are looking for the file that contains your post loop.  For most themes it's *single.php* ( or *page.php* for pages ), but for many it could also be *content.php*.  Assuming you want the subtitle to display directly after your main title, you'd place the above code after:


	<h1 class="entry-title"><?php the_title(); ?></h1>


As an *example* if you wanted to display the subtitle on standard single posts, in the Twenty Twelve theme you'd edit the content.php ( or preferabbly override the template in a child theme ):


	<header class="entry-header">
		<?php the_post_thumbnail(); ?>
		<?php if ( is_single() ) : ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php if ( function_exists( 'the_subtitle' ) ) the_subtitle( '<h2 class="subtitle">', '</h2>' ); ?>
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


### How do I style the subtitle? ###

If you have wrapped the subtitle in an H2 tag with the class of subtitle like in the second example above, you can then style it any way you'd like.

	h2.subtitle { color: pink; }

### Can I display the subtitle for my WooCommmerce products ###

Yes! You can use this [bridge plugin](https://github.com/helgatheviking/kia-subtitle-woocommerce-bridge) to automatically display the subtitle in most WooCommerce locations.

### Can I add the subtitle to the Page Title Meta tag ###

	function kia_add_subtitle_to_wp_title( $title ) {
		if ( is_single() && function_exists( 'get_the_subtitle' ) ) && $subtitle == get_the_subtitle( get_the_ID() ) ) {
		$title .= $subtitle;
		}
	}
	add_filter( 'wp_title', 'kia_add_subtitle_to_wp_title' );


### Is this translation ready? ###

WPML now supports KIA Subtitle!

### The Subtitle is not after the product title in WooCommerce ###

WooCommerce calls their product title column "name" and completely removes the default "title" column, so KIA Subtitle inserts the subtitle at the end. You can add the following to your child theme's `functions.php` or preferably a site-specific snippets plugin and re-arrange the products posts column order.


	add_filter( 'manage_product_posts_columns', 'kia_reorder_woocommerce_columns', 99 );
	
	function kia_reorder_woocommerce_columns( $columns ){
		if ( isset( $columns['subtitle'] ) && isset( $columns['name'] ) ){
	
			// remove and stash the subtitle column
			$subtitle = array( 'subtitle' => $columns['subtitle'] );
			unset( $columns['subtitle'] );
	
			// find the "name" column
			$index =  array_search( "name", array_keys( $columns) );
	
			// reform the array
			$columns = array_merge( array_slice( $columns, 0, $index + 1, true ), $subtitle, array_slice( $columns, $index, count( $columns ) - $index, true ) );
		}
		return $columns;
	}

