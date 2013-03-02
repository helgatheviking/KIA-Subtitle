<?php
/*
Plugin Name: KIA Subtitle
Plugin URI: http://www.kathyisawesome.com/436/kia-subtitle/
Description: Adds a subtitle field to WordPress' Post editor
Version: 1.3.3
Author: Kathy Darling
Author URI: http://www.kathyisawesome.com
License: GPL2

    Copyright 2012  Kathy Darling  (email: kathy.darling@gmail.com)

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


// don't load directly
if ( ! function_exists( 'is_admin' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}


if ( ! class_exists( "KIA_Subtitle" ) ) :

class KIA_Subtitle {

    static $min_wp = '3.5';

    function __construct(){

        global $wp_version;

        // load the textdomain
        add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );

        // register the shortcode:
        add_shortcode( 'the-subtitle', array( __CLASS__, 'shortcode' ) );

        // Backend functions:
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );

        // @ Todo : in next version of WP remove this back-compatibility
        if ( version_compare( $wp_version, self::$min_wp, '>=' ) ) {
            add_action( 'edit_form_after_title', array( __CLASS__, 'add_input' ) );
        } else {
            add_action( 'edit_form_advanced', array( __CLASS__, 'add_input' ) );
            add_action( 'edit_page_form', array( __CLASS__, 'add_input' ) );
        }

        add_action( 'save_post', array( __CLASS__, 'meta_save' ) );

        // Edit Columns + Quickedit:
        add_action( 'manage_posts_columns', array( __CLASS__, 'column_header' ) );
        add_action( 'manage_pages_columns', array( __CLASS__, 'column_header' ) );
        add_filter( 'manage_posts_custom_column', array( __CLASS__, 'column_value'), 10, 2 );
        add_filter( 'manage_pages_custom_column', array( __CLASS__, 'column_value'), 10, 2 );
        add_action( 'quick_edit_custom_box', array( __CLASS__, 'quick_edit_custom_box'), 10, 2 );

    }

    /**
     * Make Plugin Translation-ready
     * CALLBACK FUNCTION FOR:  add_action( 'plugins_loaded', array(__CLASS__,'load_textdomain' ) );
     * @since 1.0
     */

    function load_textdomain() {
        load_plugin_textdomain( 'kia_subtitle', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
    /**
     * Outputs the Subtitle
     * @output string
     * @since 1.0
     */
    function the_subtitle( $before = '', $after = '', $echo = true ){
      $subtitle = self::get_the_subtitle();

      if ( strlen( $subtitle ) == 0 )
        return;

      $subtitle = $before . $subtitle . $after;

      if ( $echo )
        echo $subtitle;
      else
        return $subtitle;
    }
    /**
     * Returns the Subtitle
     * @param integer $post_id the post ID for which you'd like to retrieve the
     * @return string
     * @since 1.0
     */
    function get_the_subtitle( $post_id = null ){
        $post_id = !is_null ($post_id ) ? $post_id : get_the_ID();
        $sub = get_post_meta( $post_id, 'kia_subtitle', true );
        return $sub;
    }
    /**
     * Callback for the Shortcode [the-subtitle]
     * @return string
     * @since 1.0
     */
    function shortcode(){
        return self::get_the_subtitle();
    }

    /**
     * Load Script in Admin
     * CALLBACK FUNCTION FOR:  add_action( 'admin_enqueue_scripts', array(__CLASS__,'load_scripts' ));
     * Uses jquery to re-locate the subtitle text input to just below the Title input
     * @since 1.0
     */
    function load_scripts( $hook ) {

        // conditionally add the styles and scripts:
        if( in_array( $hook, array ( 'post.php', 'post-new.php', 'edit.php' ) ) ) {

            // only add style on post screens
            if( $hook == 'post.php' || $hook == 'post-new.php' )
                add_action('admin_head',array(__CLASS__,'inline_style'));

            // load the script
            wp_enqueue_script('kia_subtitle', plugins_url('/scripts/subtitle.js', __FILE__), array('jquery'), '1.3.3', true);

            $translation_array = array( 'subtitle' => __( 'Subtitle', 'kia_subtitle' ) );
            wp_localize_script( 'kia_subtitle', 'KIA_Subtitle', $translation_array );
        }

    }

    /**
     * Style the Subtitle's text input
     * CALLBACK FUNCTION FOR:  add_action( 'admin_head', array(__CLASS__,'inline_style' ) );
     * @since 1.0
     */

    function inline_style(){ ?>
        <style type="text/css">
            #the_subtitle {
                margin: 5px 0px 15px;
                width: 100%;
                padding: 3px 8px;
                font-size: 1.3em;
            }
            #the_subtitle.prompt {
                color: #BBB;
            }
        </style>
    <?php }

    /**
     * Add the text input on the post screen
     * CALLBACK FUNCTION FOR:  add_action( 'edit_form_advanced', array(__CLASS__,'add_input' ));
     * CALLBACK FUNCTION FOR:  add_action( 'edit_page_form', array(__CLASS__,'add_input' ));
     * @since 1.0
     */
    function add_input(){

        //create the meta field (don't use a metabox, we have our own styling):
        wp_nonce_field( plugin_basename( __FILE__ ), 'kia_subnonce' );

        //get the subtitle value (if set)
        if ( $sub = get_post_meta( get_the_ID(), 'kia_subtitle', true ) ) {
            $prompt = '';
        } else {
            $sub = __( 'Subtitle','kia_subtitle' );
            $prompt = 'prompt';
        }

        // echo the inputfield with the value.
        echo '<input type="text" class="widefat '.$prompt.'" name="subtitle" value="'.esc_attr($sub).'" id="the_subtitle" tabindex="1"/>';
    }

    /**
     * Save the Meta Value
     * CALLBACK FUNCTION FOR:  add_action( 'save_post', array(__CLASS__,'meta_save' ));
     * @since 1.0
     */
    function meta_save( $post_id ){

        //check to see if this is an autosafe and if the nonce is verified:
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( ! isset( $_POST['kia_subnonce'] ) || ! wp_verify_nonce( $_POST['kia_subnonce'], plugin_basename( __FILE__ ) ) )
            return;

        // Check permissions
        if ( 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) ) return;
        } else {
            if ( !current_user_can( 'edit_post', $post_id ) ) return;
        }

        //don't save if the subtitle equals the default text (ideally we'd use the placeholder html5 attribute)
        if( in_array ( trim($_POST['subtitle'] ), array( __( 'Subtitle', 'kia_subtitle' ), '' ) ) ) {
            delete_post_meta( $post_id, 'kia_subtitle' );
        } else {
            update_post_meta( $post_id, 'kia_subtitle', sanitize_text_field( $_POST['subtitle'] ) );
        }
        return;
    }


    /**
     * Create the Subtitle Column
     * CALLBACK FUNCTION FOR:  add_action( 'manage_posts_columns', array(__CLASS__, 'column_header' ));
     * @since 1.1
     */

    function column_header( $columns ){
        $new_columns = array();
         foreach( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;
            if ( $key == 'title' )
               $new_columns['subtitle'] = __( 'Subtitle', 'kia_subtitle' );
         }

         return $new_columns;
    }

    /**
     * Add subtitle value to column data
     * CALLBACK FUNCTION FOR:  add_action( 'manage_posts_custom_column', array(__CLASS__,'column_value' ), 10, 2);
     * @since 1.1
     */

    function column_value( $column_name, $post_id ){
        switch ( $column_name ) :
            case 'subtitle' :
                echo $sub = get_post_meta( get_the_ID(), 'kia_subtitle', true );
                echo '<div class="hidden kia-subtitle-value">' . esc_html($sub) . '</div>';
            break;
        endswitch;
    }

    /**
     * Add Quick Edit Form
     * CALLBACK FUNCTION FOR:  add_action( 'quick_edit_custom_box', array(__CLASS__, 'quick_edit_custom_box'), 10, 2 );
     * @since 1.1
     */

    function quick_edit_custom_box( $column_name, $screen ) {
        if( $column_name != 'subtitle' ) return false;
    ?>
            <label class="kia-subtitle">
                <span class="title"><?php _e( 'Subtitle', 'kia_subtitle' ) ?></span>
                <span class="input-text-wrap"><input type="text" name="<?php echo esc_attr($column_name); ?>" class="ptitle kia-subtitle-input" value=""></span>

                <?php wp_nonce_field( plugin_basename( __FILE__ ), 'kia_subnonce' ); ?>

            </label>
    <?php
    }

} // end class

endif; // class_exists check


/**
* Launch the whole plugin
*/
global $KIA_Subtitle;
$KIA_Subtitle = new KIA_Subtitle();


/**
* Public Shortcut Function to KIA_Subtitle::the_subtitle()
* do not compete with any other subtitle plugins
* @output string
* @since 1.0
*/
if( ! function_exists( 'the_subtitle' ) ){
    function the_subtitle( $before = '', $after = '', $echo = true ){
        return KIA_Subtitle::the_subtitle( $before, $after, $echo );
    }
}

/**
* Public Shortcut Function to KIA_Subtitle::get_the_subtitle($post_id)
* do not compete with any other subtitle plugins
* @params integer $post_id
* @return string
* @since 1.0
*/
if( ! function_exists( 'get_the_subtitle' )){
    function get_the_subtitle( $post_id = null ){
        return KIA_Subtitle::get_the_subtitle( $post_id );
    }
}
?>
