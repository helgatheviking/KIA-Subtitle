<?php
/*
Plugin Name: KIA Subtitle
Plugin URI: http://www.kathyisawesome.com/436/kia-subtitle/
Description: Adds a subtitle field to WordPress' Post editor
Version: 1.6.8
Author: Kathy Darling
Author URI: http://www.kathyisawesome.com
License: GPL2
Text Domain: kia-subtitle

Copyright 2017  Kathy Darling  (email: kathy@kathyisawesome.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Don't load directly.
if ( ! function_exists( 'is_admin' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}


if ( ! class_exists( 'KIA_Subtitle' ) ) :

class KIA_Subtitle {

	/**
	 * @var KIA_Subtitle The single instance of the class
	 * @since 1.6
	 */
	protected static $_instance = null;

	/**
	 * @var KIA_Subtitle The single instance of the class
	 * @since 1.6
	 */
	public $version = '1.6.8';

	/**
	* @constant string donate url
	* @since 1.5
	*/
	CONST DONATE_URL = "https://www.paypal.me/usathwnt/25";

	/**
	 * Main KIA_Subtitle Instance
	 *
	 * Ensures only one instance of KIA_Subtitle is loaded or can be loaded.
	 *
	 * @since 1.6
	 * @static
	 * @see KIA_Subtitle()
	 * @return KIA_Subtitle - Main instance
	 */
	public static function instance() {
	if ( is_null( self::$_instance ) ) {
		self::$_instance = new self();
	}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.6
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' , 'kia-subtitle' ), '1.6' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.6
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' , 'kia-subtitle' ), '1.6' );
	}

	/**
	 * KIA_Subtitle Constructor.
	 * @access public
	 * @return KIA_Subtitle
	 * @since  1.0
	 */
	public function __construct(){

		global $wp_version;

		// Set-up uninstall action.
		register_uninstall_hook( __FILE__, array( __CLASS__, 'delete_plugin_options' ) );

		// Load the textdomain.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'admin_init' ));

		// Add plugin options page.
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// Add settings link to plugins page.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ), 10, 2 );

		// Add Donate link to plugin.
		add_filter( 'plugin_row_meta', array( $this, 'add_meta_links' ), 10, 2 );

		// Register the shortcode.
		add_shortcode( 'the-subtitle', array( $this, 'shortcode' ) );

		// Backend functions.
	
		// Load the subtitle script.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

		// Add the input field.
		add_action( 'edit_form_after_title', array( $this, 'add_input' ) );

		// Save the subtitle as post meta.
		add_action( 'save_post', array( $this, 'meta_save' ) );
		add_action( 'edit_attachment', array( $this, 'meta_save' ) );

		// Edit Columns + Quickedit.
		$options = get_option( 'kia_subtitle_options', false );

		// Only show input if the post type was enabled in options
		if ( isset ( $options['post_types'] ) && is_array( $options[ 'post_types'] ) ) {

			foreach( $options['post_types'] as $post_type ) {
				add_action( "manage_{$post_type}_posts_columns", array( $this, 'column_header' ), 20 );
				add_filter( "manage_{$post_type}_posts_custom_column", array( $this, 'column_value'), 10, 2 );
			}

		}

		// Quick edit support.
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box'), 10 );

		// Upgrade routine.
		add_action( 'admin_init', array( $this, 'upgrade_routine' ) );

	}

	/**
	 * Delete options table entries ONLY when plugin deactivated AND deleted
	 * register_uninstall_hook( __FILE__, array( $this,'delete_plugin_options' ) );
	 * @since 1.4
	 */
	public static function delete_plugin_options() {
		$options = get_option( 'kia_subtitle_options', true );
		if( isset( $options['delete'] ) && $options['delete'] ) {
		delete_option( 'kia_subtitle_options' );
		delete_option( 'kia_subtitle_db_version' );
		}
	}

	/**
	 * Make Plugin Translation-ready
	 * @since 1.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'kia-subtitle', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register admin settings
	 * @since 1.4
	 */
	public function admin_init(){
		register_setting( 'kia_subtitle_options', 'kia_subtitle_options', array( $this, 'validate_options' ) );
	  }


	/**
	 * Add options page
	 * @since 1.4
	 */
	public function add_options_page() {
		add_options_page(__( 'KIA Subtitle Options Page', 'kia-subtitle' ), __( 'KIA Subtitle', 'kia-subtitle' ), 'manage_options', 'kia_subtitle', array( $this, 'render_form' ) );
	}

	/**
	 * Display a Settings link on the main Plugins page
	 * @param  array $links
	 * @param  string $file
	 * @return array
	 * @since  1.6.4
	 */
	public function add_action_links( $links, $file ) {

		$plugin_link = '<a href="'. add_query_arg( 'page', 'kia_subtitle', admin_url( 'options-general.php' ) ) . '">' . __( 'Settings', 'kia-subtitle' ) . '</a>';
	  	// make the 'Settings' link appear first
	  	array_unshift( $links, $plugin_link );

		return $links;
		
	  }

	/**
	 * Add donation link
	 * @param array $plugin_meta
	 * @param string $plugin_file
	 * @since 1.6.7
	 */
	public function add_meta_links( $plugin_meta, $plugin_file ) {
		if( $plugin_file == plugin_basename(__FILE__) ){
			$plugin_meta[] = '<a class="dashicons-before dashicons-awards" href="' . self::DONATE_URL . '" target="_blank">' . __( 'Donate', 'kia-subtitle' ) . '</a>';
		}
		return $plugin_meta;
	}

	/**
	 * Render the Plugin options form
	 * @since 1.4
	 */
	public function render_form(){
		include( 'inc/plugin-options.php' );
	}

	/**
	 * Sanitize and validate input.
	 * Accepts an array, return a sanitized array.
	 * @since 1.4
	 * @param array $input all posted data
	 * @return array $clean data that is allowed to be save
	 */
	public function validate_options( $input ){

		$clean = array();

		// Probably overkill, but make sure that the post type actually exists and is one we're cool with modifying.
		$args = ( array ) apply_filters( 'kia_subtitle_post_type_args', array( 'show_in_menu' => true ) );

		$post_types = get_post_types( $args );

		if( isset( $input['post_types'] ) && is_array( $input['post_types'] ) ) foreach ( $input['post_types'] as $post_type ){
			if( in_array( $post_type, $post_types ) ) $clean['post_types'][] = $post_type;
		}

		$clean['delete'] =  isset( $input['delete'] ) && $input['delete'] ? 1 : 0 ;  // Checkbox.

		return $clean;
	
	}

	/**
	 * Outputs the Subtitle
	 * @since 1.0
	 * @param  string $before any text that should be prepended to the subtitle
	 * @param  string $after any text that should be appended to the subtitle
	 * @param  boolean $echo should the subtitle be printed or returned
	 * @return string
	 */
	public function the_subtitle( $before = '', $after = '', $echo = true ){
		$subtitle = $this->get_the_subtitle();

		if ( strlen( $subtitle ) == 0 ){
			return;
		}

		$subtitle = $before . $subtitle . $after;

		if ( $echo ){
			echo $subtitle;
		} else {
			return $subtitle;
		}
	}

	/**
	 * Returns the Subtitle
	 * @since 1.0
	 * @param  int $post_id the post ID for which you want to retrieve the subtitle
	 * @return string
	 */
	public function get_the_subtitle( $post_id = null ){
		$post_id = !is_null ($post_id ) ? $post_id : get_the_ID();
		$sub = get_post_meta( $post_id, 'kia_subtitle', true );
		return apply_filters( 'the_subtitle', $sub, $post_id );
	}

	/**
	 * Callback for the Shortcode [the-subtitle]
	 * @return string
	 * @since 1.0
	 */
	public function shortcode(){
		return $this->get_the_subtitle();
	}


	/**
	 * Load Script in Admin
	 * Uses jquery to re-locate the subtitle text input to just below the Title input
	 * @since 1.0
	 * @param string $hook the name of the page we're on in the WP admin
	 * @return null
	 */
	public function load_scripts( $hook ) {

		// Conditionally add the styles and scripts.
		if( in_array( $hook, array ( 'post.php', 'post-new.php', 'edit.php' ) ) ) {

			// Only add style on post screens.
			if( $hook == 'post.php' || $hook == 'post-new.php' ){
				add_action( 'admin_head', array( $this, 'inline_style' ) );
			} else if ( $hook == 'edit.php' ) {
				add_action( 'admin_head', array( $this, 'subtitle_column_style' ) );
			}

			// Load the script.
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'kia_subtitle', plugins_url( 'js/subtitle'. $suffix . '.js', __FILE__ ), array( 'jquery' ), $this->version, true );

			$translation_array = array( 'subtitle' => __( 'Subtitle', 'kia-subtitle' ) );
			wp_localize_script( 'kia_subtitle', 'KIA_Subtitle', $translation_array );
		}

	}

	/**
	 * Style the Subtitle's text input
	 * @since 1.0
	 */

	public function inline_style(){ ?>
		<style type="text/css">
		#the_subtitle {
			margin: 5px 0px;
		}
		#the_subtitle.prompt {
			color: #BBB;
		}
		</style>
	<?php 
	}

	/**
	 * Add the text input on the post screen
	 * @since 1.0
	 */
	public function add_input(){

		global $post;

		$options = get_option( 'kia_subtitle_options' );

		// Only show input if the post type was not enabled in options.
		if ( isset ( $options['post_types'] ) && in_array( $post->post_type, $options[ 'post_types'] ) ) {

			// Create the meta field (don't use a metabox, we have our own styling).
			wp_nonce_field( plugin_basename( __FILE__ ), 'kia_subnonce' );

			// Get the subtitle value (if set).
			$sub = get_post_meta( get_the_ID(), 'kia_subtitle', true );

			// Echo the inputfield with the value.
			printf( '<input type="text" class="widefat" name="subtitle" placeholder="%s" value="%s" id="the_subtitle" />',
				__( 'Subtitle', 'kia-subtitle' ),
				esc_attr($sub) );

		}
	}

	/**
	 * Save the Meta Value
	 * @since 1.0
	 * @param  int $post_id the ID of the post we're saving
	 * @return null
	 */
	public function meta_save( $post_id ){

		// Check to see if this is an autosafe and if the nonce is verified.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return $post_id;
		}

		if ( ! isset( $_POST['kia_subnonce'] ) || ! wp_verify_nonce( $_POST['kia_subnonce'], plugin_basename( __FILE__ ) ) ){
			return $post_id;
		}

		// Check permissions.
		if ( 'page' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ){
			return $post_id;
		} else if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Don't save if the subtitle equals the default text.
		if( in_array ( trim($_POST['subtitle'] ), array( __( 'Subtitle', 'kia-subtitle' ), '' ) ) ) {
			delete_post_meta( $post_id, 'kia_subtitle' );
		} else {
			update_post_meta( $post_id, 'kia_subtitle', sanitize_post_field( 'post_title', $_POST['subtitle'], $post_id, 'db' ) );
		}

		return $post_id;
	}

	/**
	 * Style the Subtitle's column for WooCommerce products
	 * @since 1.6.8
	 */
	public function subtitle_column_style(){ ?>
		<style type="text/css">
		.post-type-product .column-subtitle {
			width: 20ch;
		}
		</style>
	<?php 
	}
	
	/**
	 * Create the Subtitle Column
	 * @since 1.1
	 * @param array $columns the columns for edit screen
	 * @return  array of new columns
	 */
	public function column_header( $columns ){

		// Insert after title column.
		if( isset( $columns['title'] ) || isset( $columns['name'] ) ){
		
			// The subtitle as an array for subsequent array manip.
			$subtitle = array( 'subtitle' => __( 'Subtitle', 'kia-subtitle' ) );

			// WooCommerce uses the "name" column for titles.
			$needle = isset( $columns['title'] ) ? 'title' : 'name';
			
			// Find the "title" column.
			$index =  array_search( $needle, array_keys( $columns) );

			// Reform the array.
			$columns = array_merge( array_slice( $columns, 0, $index + 1, true ), $subtitle, array_slice( $columns, $index, count( $columns ) - $index, true ) );
		
		// Or add to end.
		} else {
			$columns['subtitle'] = __( 'Subtitle', 'kia-subtitle' );
		}

		return $columns;
	}

	/**
	 * Add subtitle value to column data
	 * @since 1.1
	 * @param string $column_name
	 * @param int $post_id the post ID of the row
	 * @return  string
	 */
	public function column_value( $column_name, $post_id ){
		switch ( $column_name ) :
			case 'subtitle' :
				echo $sub = get_post_meta( get_the_ID(), 'kia_subtitle', true );
				echo '<div class="hidden kia-subtitle-value">' . esc_html($sub) . '</div>';
			break;
		endswitch;
	}

	/**
	 * Add Quick Edit Form
	 * @param string $column_name
	 * @return  string
	 */
	public function quick_edit_custom_box( $column_name ) {
		if( $column_name == 'subtitle' ) {

			global $post;

			$options = get_option( 'kia_subtitle_options' );

			// Only show input if the post type was enabled in options.
			if ( isset ( $options['post_types'] ) && in_array( $post->post_type, $options[ 'post_types'] ) ) { ?>

				<label class="kia-subtitle">
					<span class="title"><?php _e( 'Subtitle', 'kia-subtitle'   ) ?></span>
					<span class="input-text-wrap"><input type="text" name="<?php echo esc_attr($column_name); ?>" class="ptitle kia-subtitle-input" value=""></span>

					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'kia_subnonce' ); ?>

				</label>
	<?php   }
		}
	}


	/**
	 * Plugin Upgrade Routine
	 * previously used options to *exclude* post types from having subtitle
	 * going to switch to including them all by default
	 * @since 1.5
	 */
	public function upgrade_routine() {

		$db_version = get_option( 'kia_subtitle_db_version', false );

		if ( ! ( $db_version ) || version_compare( $db_version, '1.5', '<' ) ) {

			// Get any existing options.
			$options = get_option( 'kia_subtitle_options' );

			// Get all post types showing up in the menu.
			$args = array( 'show_in_menu' => true );
			$post_types = get_post_types( $args );
			ksort( $post_types );

			// We're going to switch any checked to *disable* options from previous version into unchecked to disable.
			if( isset( $options['post_types'] ) && is_array( $options['post_types'] ) ){
				$post_types = array_diff( $post_types, $options['post_types'] );
			}

			// Merge the new options with any old options.
			$update_options = array_merge( (array)$options, array ( 'post_types' => $post_types, 'db_version' => '1.5' ) );

			// Update the options.
			update_option( 'kia_subtitle_options', $update_options );
			
		}

		update_option( 'kia_subtitle_db_version', $this->version );

	}

} // End class.

endif; // End class_exists check.


/**
 * Launch the whole plugin
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  1.6
 * @return KIA_Subtitle
 */
function KIA_Subtitle() {
  return KIA_Subtitle::instance();
}

// Global for backwards compatibility.
$GLOBALS['KIA_Subtitle'] = KIA_Subtitle();

/**
* Public Shortcut Function to KIA_Subtitle::the_subtitle()
* do not compete with any other subtitle plugins
* @since 1.0
* @param  string $before any text that should be prepended to the subtitle
* @param  string $after any text that should be appended to the subtitle
* @param  boolean $echo should the subtitle be printed or returned
* @return string
*/
if( ! function_exists( 'the_subtitle' ) ){
	function the_subtitle( $before = '', $after = '', $echo = true ){
		return KIA_Subtitle()->the_subtitle( $before, $after, $echo );
	}
}

/**
* Public Shortcut Function to KIA_Subtitle::get_the_subtitle($post_id)
* do not compete with any other subtitle plugins
* @since 1.0
* @param  int $post_id the post ID for which you want to retrieve the subtitle
* @return string
*/
if( ! function_exists( 'get_the_subtitle' )){
	function get_the_subtitle( $post_id = null ){
		return KIA_Subtitle()->get_the_subtitle( $post_id );
	}
}
