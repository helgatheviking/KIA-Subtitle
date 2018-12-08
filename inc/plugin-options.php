<div class="wrap">
  <div id="tabs">

  <style>
    #nav-tabs { overflow: hidden; margin: 0 0 -1px 0;}
    #nav-tabs li { float: left; margin-bottom: 0;}
    .ui-tabs-nav a { color: #aaa;}
    #nav-tabs li.ui-state-active a { border-bottom: 2px solid white; color: #464646; }
    h2.nav-tab-wrapper { margin-bottom: 1em;}
  </style>

  <h2><?php _e( 'KIA Subtitle', 'kia-subtitle' );?></h2>

  <!-- Beginning of the Plugin Options Form -->
  <form method="post" action="<?php echo admin_url( 'options.php' );?>">
    <?php settings_fields( 'kia_subtitle_options' ); ?>
    <?php $options = get_option( 'kia_subtitle_options' ); ?>

    <div id="general">
        <fieldset>
              <table class="form-table">
                    <tr>
                      <th scope="row"><?php _e( 'Enable on Post Types', 'kia-subtitle' );?></th>
                      <td>

                        <?php

                        $args = ( array ) apply_filters( 'kia_subtitle_post_type_args', array( 'show_in_menu' => true ) );

                        $post_types = get_post_types( $args, 'objects' );

                        ksort( $post_types );

                        if( ! is_wp_error( $post_types ) ) {

                          foreach ($post_types as $i=>$post_type)  { ?>
                            <input type="checkbox" name="kia_subtitle_options[post_types][]" value="<?php echo $i;?>" <?php checked( isset( $options['post_types'] ) && is_array($options['post_types']) && in_array($i, $options['post_types']), 1 ); ?> /> <?php echo $post_type->labels->name; ?><br/>

                          <?php
                              }

                        } ?>

                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><?php _e('Completely remove options on plugin removal', 'kia-subtitle' );?></th>
                      <td>
                        <input type="checkbox" name="kia_subtitle_options[delete]" value="1" <?php checked( isset( $options['delete'] ) && $options['delete'], 1 );?> />
                      </td>
                    </tr>
                  </table>
          </fieldset>
      </div>

          <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'kia-subtitle' ); ?>" />
          </p>
    </form>
  </div>
</div>