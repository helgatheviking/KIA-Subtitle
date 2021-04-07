<?php
/*
Plugin Name: KIA Subtitle
Plugin URI: http://www.kathyisawesome.com/436/kia-subtitle/
Description: Adds a subtitle field to WordPress' Post editor
Version: 3.0.3
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
	public $version = '3.0.3';

	/**
	* @constant string donate url
	* @since 1.5
	*/
	CONST DONATE_URL = 'https://www.paypal.com/fundraiser/charity/1451316';

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
		_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.' , 'kia-subtitle' ), '1.6' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.6
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.' , 'kia-subtitle' ), '1.6' );
	}

	/**
	 * KIA_Subtitle Constructor.
	 * 
	 * @return KIA_Subtitle
	 * @since  1.0
	 */
	public function __construct() {

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

		// Add metaboxes for CPTs that don't support custom-fields in Gutenberg.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// Add the input field in Classic Editor.
		add_action( 'edit_form_after_title', array( $this, 'add_input' ) );
		
		// Hide key from Custom Fields
		add_filter( 'is_protected_meta', array( $this, 'make_key_private' ), 10, 2 );
		
		// Save the subtitle as post meta.
		add_action( 'save_post', array( $this, 'meta_save' ) );
		add_action( 'edit_attachment', array( $this, 'meta_save' ) );

		// Edit Columns + Quickedit.
		foreach( self::get_enabled_post_types() as $post_type ) {
			add_action( "manage_{$post_type}_posts_columns", array( $this, 'column_header' ), 20 );
			add_filter( "manage_{$post_type}_posts_custom_column", array( $this, 'column_value'), 10, 2 );
		}

		// Quick edit support.
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box'), 10 );

		// Upgrade routine.
		add_action( 'admin_init', array( $this, 'upgrade_routine' ) );

		// Add Block Editor compatibility.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_assets' ) );

		// Register meta key in REST
		add_action( 'init', array( $this, 'register_meta' ) );

	}

	/**
	 * Delete options table entries ONLY when plugin deactivated AND deleted
	 * register_uninstall_hook( __FILE__, array( $this,'delete_plugin_options' ) );
	 * 
	 * @since 1.4
	 */
	public static function delete_plugin_options() {
		$options = get_option( 'kia_subtitle_options' );
		if( isset( $options['delete'] ) && $options['delete'] ) {
			delete_option( 'kia_subtitle_options' );
			delete_option( 'kia_subtitle_db_version' );
		}
	}

	/**
	 * Make Plugin Translation-ready
	 * 
	 * @since 1.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'kia-subtitle', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Register admin settings
	 * 
	 * @since 1.4
	 */
	public function admin_init() {
		register_setting( 'kia_subtitle_options', 'kia_subtitle_options', array( $this, 'validate_options' ) );
	  }


	/**
	 * Add options page
	 * 
	 * @since 1.4
	 */
	public function add_options_page() {
		add_options_page(__( 'KIA Subtitle Options Page', 'kia-subtitle' ), __( 'KIA Subtitle', 'kia-subtitle' ), 'manage_options', 'kia_subtitle', array( $this, 'render_form' ) );
	}

	/**
	 * Display a Settings link on the main Plugins page
	 *
	 * @since  1.6.4
	 * 
	 * @param  array $links
	 * @param  string $file
	 * @return array
	 */
	public function add_action_links( $links, $file ) {

		$plugin_link = '<a href="'. add_query_arg( 'page', 'kia_subtitle', admin_url( 'options-general.php' ) ) . '">' . __( 'Settings', 'kia-subtitle' ) . '</a>';
	  	// make the 'Settings' link appear first
	  	array_unshift( $links, $plugin_link );

		return $links;
		
	  }

	/**
	 * Add donation link
	 *
	 * @since 1.6.7
	 * 
	 * @param array $plugin_meta
	 * @param string $plugin_file
	 */
	public function add_meta_links( $plugin_meta, $plugin_file ) {
		if( $plugin_file === plugin_basename(__FILE__) ) {
			$plugin_meta[] = '<a class="dashicons-before dashicons-awards" href="' . self::DONATE_URL . '" target="_blank">' . __( 'Donate', 'kia-subtitle' ) . '</a>';
		}
		return $plugin_meta;
	}

	/**
	 * Render the Plugin options form
	 * 
	 * @since 1.4
	 */
	public function render_form() {
		include( 'includes/views/plugin-options.php' );
	}

	/**
	 * Sanitize and validate input.
	 * Accepts an array, return a sanitized array.
	 * 
	 * @since 1.4
	 * 
	 * @param array $input all posted data
	 * @return array $clean data that is allowed to be save
	 */
	public function validate_options( $input ) {

		$clean = array();

		// Probably overkill, but make sure that the post type actually exists and is one we're cool with modifying.
		$args = ( array ) apply_filters( 'kia_subtitle_post_type_args', array( 'show_in_menu' => true ) );

		$post_types = get_post_types( $args );

		if( isset( $input['post_types'] ) && is_array( $input['post_types'] ) ) foreach ( $input['post_types'] as $post_type ) {
			if( in_array( $post_type, $post_types ) ) $clean['post_types'][] = $post_type;
		}

		$clean['delete'] =  isset( $input['delete'] ) && $input['delete'] ? 1 : 0 ;  // Checkbox.

		return $clean;
	
	}

	/**
	 * Outputs the Subtitle
	 * 
	 * @since 1.0
	 * 
	 * @param  array $args. In 3.1 converted to array that accepts multiple keys.
	 *         array(
	 *              'post_id' => int
	 *              'before'  => string - Any text that should be prepended to the subtitle
	 *              'after'   => string - Any text that should be appended to the subtitle.
	 *              'echo'    => bool 
	 *         )
	 * @param  mixed $after - Deprecated in 3.1
	 * @param  boolean $echo - Deprecated in 3.1
	 * @return string
	 */
	public function the_subtitle( $args = array(), $after = false, $echo = null ) {

		$new_args = array();

		if( is_array( $args ) ) {
			$new_args = $args;
		} else {
			_deprecated_argument( __FUNCTION__, '3.1.0', 'All arguments are now passed as a single array parameter.' );
			$new_args['before'] = $args;
		}

		if( is_string( $after ) ) {
			_deprecated_argument( __FUNCTION__, '3.1.0', 'All arguments are now passed as a single array parameter.' );
			$new_args['after'] = $after;
		}

		if( ! is_null( $echo ) ) {
			_deprecated_argument( __FUNCTION__, '3.1.0', 'All arguments are now passed as a single array parameter.' );
			$new_args['echo'] = $echo;
		}

		$defaults = 
			array( 
				'post_id' => null,
				'before'  => '',
				'after'   => '',
				'echo'    => true
			);

		$args = wp_parse_args( $new_args, $defaults );

		$subtitle = $this->get_the_subtitle( $args['post_id'] );

		if ( strlen( $subtitle ) === 0 ) {
			return;
		}

		$subtitle = $args['before'] . $subtitle . $args['after'];

		if ( $args['echo'] ) {
			echo $subtitle;
		} else {
			return $subtitle;
		}
	}

	/**
	 * Returns the Subtitle
	 * 
	 * @since 1.0
	 * 
	 * @param  int $post_id the post ID for which you want to retrieve the subtitle
	 * @return string
	 */
	public function get_the_subtitle( $post_id = null ) {
		$post_id = ! is_null( $post_id ) ? intval( $post_id ) : get_the_ID();
		$sub = get_post_meta( $post_id, 'kia_subtitle', true );
		return apply_filters( 'the_subtitle', $sub, $post_id );
	}

	/**
	 * Callback for the Shortcode [the-subtitle]
	 *
	 * @since 1.0
	 * 
	 * @return string
	 */
	public function shortcode( $atts ) {

		$atts = shortcode_atts(
			array( 
				'post_id' => null,
				'before'  => '<h2 class="subtitle">',
				'after'   => '</h2>'
			), $atts, 'the-subtitle' );

		$atts['echo'] = false;

		return $this->the_subtitle( $atts );
	}


	/**
	 * Load Script in Admin
	 * Uses jquery to re-locate the subtitle text input to just below the Title input
	 * 
	 * @since 1.0
	 * 
	 * @param string $hook the name of the page we're on in the WP admin
	 */
	public function load_scripts( $hook ) {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$current_screen = get_current_screen();
		
		wp_register_script( 'kia_subtitle', plugins_url( 'js/subtitle'. $suffix . '.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		
		// Add styles and scripts for classic editor.
    	if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) && self::is_enabled_for_post_type( $current_screen->post_type ) && method_exists( $current_screen, 'is_block_editor' ) && ! $current_screen->is_block_editor() ) {
			add_action( 'admin_head', array( $this, 'inline_style' ) );
			wp_enqueue_script( 'kia_subtitle' );
		// Add scripts and styles for edit screen.
		} elseif ( $hook === 'edit.php' && self::is_enabled_for_post_type( $current_screen->post_type ) ) {
			add_action( 'admin_head', array( $this, 'subtitle_column_style' ) );
			wp_enqueue_script( 'kia_subtitle' );
		}

	}

	/**
	 * Style the Subtitle's text input
	 * 
	 * @since 1.0
	 */

	public function inline_style() { ?>
		<style type="text/css">
			#the_subtitle {
				margin: 5px 0px;
			}
		</style>
	<?php 
	}

	/**
	 * Add the text input as a metabox in Gutenberg
	 * 
	 * @since 2.0
	 */
	public function add_meta_box() {

		global $post;

		$current_screen = get_current_screen();

		// Classic editor screens don't need metaboxes since they use the edit_form_after_title hook.
		if ( is_callable( array( $current_screen, 'is_block_editor' ) ) && ! $current_screen->is_block_editor() ) {
			return;
		}

        if ( $post && in_array( $post->post_type, self::get_enabled_post_types() ) && ! post_type_supports( $post->post_type, 'custom-fields' ) ) {

			add_meta_box(
				'kia_subtitle_meta_box',
				__( 'Subtitle', 'kia-subtitle' ),
				array( $this, 'render_meta_box_content' ),
				$post->post_type,
				'advanced',
				'high',
				array(
				    '__block_editor_compatible_meta_box' => true,
				 )
			);

		}
        
    }


    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post = false ) {

    	$post_id = $post === false ? get_the_ID() : $post->ID;
 
		// Create the meta field (don't use a metabox, we have our own styling).
		wp_nonce_field( plugin_basename( __FILE__ ), 'kia_subnonce' );

		// Get the subtitle value (if set).
		$value = get_post_meta( $post_id, 'kia_subtitle', true );

		// Display the form, using the current value.
		?>
		<label for="the_subtitle" class="screen-reader-text">
			<?php _e( 'Subtitle', 'kia-subtitle' ); ?>
		</label>
		<input type="text" id="the_subtitle" class="widefat" name="subtitle" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo __( 'Enter subtitle', 'kia-subtitle' ); ?>"  />

		<?php
    }


	/**
	 * Add the text input on the post screen
	 * 
	 * @since 1.0
	 */
	public function add_input() {

		global $post;

		// Only show input if the post type was enabled in options.
		if( self::is_enabled_for_post_type( $post->post_type ) ) {
			$this->render_meta_box_content( $post );
		}
	}


	/**
	 * Make the meta key private
	 * 
	 * @since 2.0
	 * 
	 * @param  bool 	$is_private
	 * @param  string	$key
	 * @return bool
	 */
	public function make_key_private( $is_private, $key ) {
		if( 'kia_subtitle' === $key ) {
			$is_private = true;
		}
		return $is_private;
	}
	

	/**
	 * Save the Meta Value
	 * 
	 * @since 1.0
	 * 
	 * @param  int $post_id the ID of the post we're saving
	 * @return int
	 */
	public function meta_save( $post_id ) {

		// Check to see if this is an autosafe and if the nonce is verified.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! isset( $_POST['kia_subnonce'] ) || ! wp_verify_nonce( $_POST['kia_subnonce'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Check permissions.
		if ( 'page' === $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} else if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Save if set.
		if( isset( $_POST['subtitle'] ) ) {
			update_post_meta( $post_id, 'kia_subtitle', sanitize_post_field( 'post_title', $_POST['subtitle'], $post_id, 'db' ) );
		}

		return $post_id;
	}

	/**
	 * Style the Subtitle's column for WooCommerce products
	 * 
	 * @since 1.6.8
	 */
	public function subtitle_column_style() { ?>
		<style type="text/css">
		.post-type-product .column-subtitle {
			width: 20ch;
		}
		</style>
	<?php 
	}
	
	/**
	 * Create the Subtitle Column
	 * 
	 * @since 1.1
	 * 
	 * @param array $columns the columns for edit screen
	 * @return  array of new columns
	 */
	public function column_header( $columns ) {

		// Insert after title column.
		if( isset( $columns['title'] ) || isset( $columns['name'] ) ) {
		
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
	 * 
	 * @since 1.1
	 * 
	 * @param string $column_name
	 * @param int $post_id the post ID of the row
	 * @return  string
	 */
	public function column_value( $column_name, $post_id ) {
		switch ( $column_name ) :
			case 'subtitle' :
				echo $sub = get_post_meta( get_the_ID(), 'kia_subtitle', true );
				echo '<div class="hidden kia-subtitle-value">' . esc_html($sub) . '</div>';
			break;
		endswitch;
	}

	/**
	 * Add Quick Edit Form
	 * 
	 * @param string $column_name
	 * @return  string
	 */
	public function quick_edit_custom_box( $column_name ) {
		if( $column_name === 'subtitle' ) {

			global $post;

			// Only show input if the post type was enabled in options.
			if( self::is_enabled_for_post_type( $post->post_type ) ) { ?>

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
	 * 
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
			$post_types = array_diff( $post_types, self::get_enabled_post_types() );

			// Merge the new options with any old options.
			$update_options = array_merge( (array)$options, array ( 'post_types' => $post_types, 'db_version' => '1.5' ) );

			// Update the options.
			update_option( 'kia_subtitle_options', $update_options );
			
		}

		update_option( 'kia_subtitle_db_version', $this->version );

	}

	/*-----------------------------------------------------------------------------------*/
	/* Block Editor Functions */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Load Script in Block Editor
	 * 
	 * @since 3.0
	 * 
	 * @param string $hook the name of the page we're on in the WP admin
	 * @return null
	 */
	public function enqueue_assets( $hook ) {

		$current_screen = get_current_screen();
				
		// Add styles and scripts for block editor.
    	if ( self::is_enabled_for_post_type( $current_screen->post_type ) && post_type_supports( $current_screen->post_type, 'custom-fields' ) ) {
			wp_enqueue_script( 'kia-subtitle-gutenberg-sidebar', plugins_url( 'js/dist/index.js', __FILE__ ), array( 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' ), $this->version );
		}

	}

	/**
	 * Register meta key for block editor.
	 * 
	 * @since 3.0
	 */
	public function register_meta() {

		register_meta('post', 'kia_subtitle', array(
			'show_in_rest' => true,
			'type' => 'string',
			'single' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function() { 
				return current_user_can( 'edit_posts' );
			}
		));

	}

		
	/*-----------------------------------------------------------------------------------*/
	/* Helper Functions */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Version compare for WordPress core.
	 * 
	 * @since 3.0
	 *
	 * @param  string $version - The version to test against.
	 * @return  bool
	 */
	public static function is_wp_gte( $version = '5.3' ) {
		global $wp_version;
		return version_compare( $wp_version, '4.3', '>=' );
	}

	/**
	 * Get enabled post types.
	 * 
	 * @since 3.0
	 *
	 * @return  array
	 */
	public static function get_enabled_post_types() {
		$options = get_option( 'kia_subtitle_options', false );
		return isset( $options['post_types'] ) && is_array( $options[ 'post_types'] ) ? $options['post_types'] : array();
	}

	/**
	 * Is a post type enabled?
	 * 
	 * @since 3.0
	 *
	 * @param  string $type - The post type to check is enabled.
	 * @return  bool
	 */
	public static function is_enabled_for_post_type( $type ) {
		return in_array( $type, self::get_enabled_post_types() );
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

add_action( 'plugins_loaded', 'KIA_Subtitle' );

/**
* Public Shortcut Function to KIA_Subtitle::the_subtitle()
* do not compete with any other subtitle plugins
* 
* @since 1.0
* 
* @param  string $before any text that should be prepended to the subtitle
* @param  string $after any text that should be appended to the subtitle
* @param  boolean $echo should the subtitle be printed or returned
* @return string
*/
if( ! function_exists( 'the_subtitle' ) ) {
	function the_subtitle( $before = '', $after = '', $echo = true ) {
		$args = array(
			'before' => $before,
			'after'  => $after,
			'echo'   => $echo
		);
		return KIA_Subtitle()->the_subtitle( $args );
	}
}

/**
* Public Shortcut Function to KIA_Subtitle::get_the_subtitle($post_id)
* do not compete with any other subtitle plugins
* 
* @since 1.0
* 
* @param  int $post_id the post ID for which you want to retrieve the subtitle
* @return string
*/
if( ! function_exists( 'get_the_subtitle' )) {
	function get_the_subtitle( $post_id = null ) {
		return KIA_Subtitle()->get_the_subtitle( $post_id );
	}
}
