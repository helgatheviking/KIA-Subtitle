<?php
/**
 * Dynamic Block Template: Render the subtitle.
 *
 * @since 4.0.0
 * @version 4.0.0
 * @package KIA_Subtitle
 *
 * @param   array $attributes - A clean associative array of block attributes.
 * @param   array $block - All the block settings and attributes.
 * @param   string $content - The block inner HTML (usually empty unless using inner blocks).
 */
?>
<?php
if ( ! isset( $block->context['postId'] ) ) {
	return '';
}

/**
 * The `$post` argument is intentionally omitted so that changes are reflected when previewing a post.
 * See: https://github.com/WordPress/gutenberg/pull/37622#issuecomment-1000932816.
 */
$subtitle = get_the_subtitle();

if ( ! $subtitle ) {
	return '';
}

$tag_name = 'h3';
if ( isset( $attributes['level'] ) ) {
	$tag_name = 'h' . $attributes['level'];
}

if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
	$rel   = ! empty( $attributes['rel'] ) ? 'rel="' . esc_attr( $attributes['rel'] ) . '"' : '';
	$subtitle = sprintf( '<a href="%1$s" target="%2$s" %3$s>%4$s</a>', esc_url( get_the_permalink( $block->context['postId'] ) ), esc_attr( $attributes['linkTarget'] ), $rel, $subtitle );
}

$classes = array('subtitle');
if ( isset( $attributes['textAlign'] ) ) {
	$classes[] = 'has-text-align-' . $attributes['textAlign'];
}
if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
	$classes[] = 'has-link-color';
}
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

printf(
	'<%1$s %2$s>%3$s</%1$s>',
	$tag_name,
	$wrapper_attributes,
	$subtitle
);