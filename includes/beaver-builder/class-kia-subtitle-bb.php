<?php
/**
 * @package		KIA Subtitle
 * @category	Compatibility Class
 * @version 	3.1.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class KIA_Subtitle_BB {

	/**
	 * Boot up Beaver Builder compat.
	 * @access public
	 * @return KIA_Subtitle
	 * @since  1.0
	 */
	public static function init() {

		// Add Beaver Builder Module.
		add_action( 'wp', array( __CLASS__, 'register_bb_module' ) );
		add_action( 'fl_page_data_add_properties', array( __CLASS__, 'add_bb_property' ) );

	}

	/*-----------------------------------------------------------------------------------*/
	/* Block Editor Functions */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Register a custom module for Beaver Builder
	 * 
	 * @since 3.1
	 * @return null
	 */
	public static function register_bb_module() {
		if ( class_exists( 'FLThemeBuilderLoader' ) ) {
			FLThemeBuilderLoader::load_modules( kia_subtitle::get_plugin_path() . '/includes/beaver-builder/' );
		}
	}

	/**
	 * Add subtitle as a post property
	 * 
	 * @since 3.1
	 * @return null
	 */
	public static function add_bb_property() {

		FLPageData::add_post_property( 'kia_subtitle', array(
			'label'       => __( 'Post Subtitle', 'kia-subtitle' ),
			'group'       => 'posts',
			'type'        => 'string',
			'getter'      => 'get_the_subtitle',
			'placeholder' => 'Lorem Ipsum Dolor',
		) );

	}


} // End class.

KIA_Subtitle_BB::init();

