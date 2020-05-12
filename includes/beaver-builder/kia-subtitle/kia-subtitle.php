<?php
/**
 * Beaver Builder Module
 *
 * @class   KIA_Subtitle_Module
 * @package KIA Subtitle/Classes
 * @since   3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Heading module alias for the post subtitle.
 */
FLBuilder::register_module_alias( 'kia-post-subtitle', array(
    'module'      => 'heading',
    'name'        => __( 'Post Subtitle', 'kia-subtitle' ),
    'description' => __( 'Displays the subtitle for the current post.', 'kia-subtitle' ),
    'category'    => __( 'Post Modules', 'fl-theme-builder' ),
    'enabled'     => FLThemeBuilderLayoutData::current_post_is( 'singular' ),
    'settings'    => array(
        'tag'         => 'h1',
        'connections' => array(
            'heading' => (object) array(
                'object' => 'post',
                'property' => 'kia_subtitle',
                'field' => 'text',
            ),
        ),
    ),
) );

