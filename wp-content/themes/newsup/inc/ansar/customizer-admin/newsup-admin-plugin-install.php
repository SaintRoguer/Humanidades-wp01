<?php /**
 * AJAX handler to store the state of dismissible notices.
 */
function newsup_ajax_notice_handler() {
    if ( isset( $_POST['type'] ) ) {
        // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
        $type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
        // Store it in the options table
        update_option( 'dismissed-' . $type, TRUE );
    }
}

add_action( 'wp_ajax_newsup_dismissed_notice_handler', 'newsup_ajax_notice_handler' );

function newsup_deprecated_hook_admin_notice() {
        // Check if it's been dismissed...
        if ( ! get_option('dismissed-get_started', FALSE ) ) {
            // Added the class "notice-get-started-class" so jQuery pick it up and pass via AJAX,
            // and added "data-notice" attribute in order to track multiple / different notices
            // multiple dismissible notice states ?>
               <div class="newsup-notice-started updated notice notice-get-started-class is-dismissible" data-notice="get_started">
                <div class="newsup-notice clearfix">
                    <div class="newsup-notice-content">
                        
                        <div class="newsup-notice_text">
                            <div class="newsup-hello">
                                <?php esc_html_e( 'Hello ', 'newsup' ); 
                                $current_user = wp_get_current_user();
                                echo esc_html( $current_user->display_name );
                                ?>
                                <img draggable="false" role="img" class="emoji" alt="👋🏻" src="https://s.w.org/images/core/emoji/14.0.0/svg/1f44b-1f3fb.svg">                
                            </div>
                            <h1><?php
                                    $theme_info = wp_get_theme();
                                    printf( esc_html__('Welcome to %1$s', 'newsup'), esc_html( $theme_info->Name ), esc_html( $theme_info->Version ) ); ?>
                            </h1>
                            
                            <p><?php esc_html_e("Thank you for choosing Newsup theme. To take full advantage of the complete features of the theme click the Import Demo and Install and Activate the plugin then use the demo importer and install the Newsup Demo according to your need.", "newsup"); ?></p>

                            <div class="panel-column-6">
                                <div class="panel-column-one">
                                    <a class="newsup-btn-get-started button button-primary button-hero newsup-button-padding" href="#" data-name="" data-slug=""><?php esc_html_e( 'Import Demo', 'newsup' ) ?></a>

                                    <a class="newsup-btn-get-started-customize button button-primary button-hero newsup-button-padding" href="<?php echo esc_url( admin_url( '/customize.php' ) ); ?>" data-name="" data-slug=""><?php esc_html_e( 'Customize Site', 'newsup' ) ?></a>

                                    <a href="<?php echo esc_url( admin_url( 'themes.php?page=newsup-getting-started' ) ); ?>" class="button button-blue-secondary button_info" style="text-decoration: none;"><?php echo esc_html__('Get started with Newsup','newsup'); ?></a>
                                </div>
                                <div class="panel-column-two">
                                    <div class="newsup-documentation">
                                    <span aria-hidden="true" class="dashicons dashicons-external"></span>
                                    <a class="newsup-documentation" href="<?php echo esc_url('https://docs.themeansar.com/docs/newsup')?>" data-name="" data-slug=""><?php esc_html_e( 'View Documentation', 'newsup' ) ?></a>
                                    </div>

                                    <div class="newsup-demos">
                                    <span aria-hidden="true" class="dashicons dashicons-external"></span>
                                    <a class="newsup-demos" href="<?php echo esc_url('https://demos.themeansar.com/newsup-demos/')?>" data-name="" data-slug=""><?php esc_html_e( 'View Demos', 'newsup' ) ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="newsup-notice_image">
                             <img class="newsup-screenshot" src="<?php echo esc_url( get_template_directory_uri() . '/images/newsup.customize.webp' ); ?>" alt="<?php esc_attr_e( 'newsup', 'newsup' ); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        <?php }
}

add_action( 'admin_notices', 'newsup_deprecated_hook_admin_notice' );

/* Plugin Install */

add_action( 'wp_ajax_install_act_plugin', 'newsup_admin_info_install_plugin' );

function newsup_admin_info_install_plugin() {
    /**
     * Install Plugin.
     */
    include_once ABSPATH . '/wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    if ( ! file_exists( WP_PLUGIN_DIR . '/ansar-import' ) ) {
        $api = plugins_api( 'plugin_information', array(
            'slug'   => sanitize_key( wp_unslash( 'ansar-import' ) ),
            'fields' => array(
                'sections' => false,
            ),
        ) );

        $skin     = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $result   = $upgrader->install( $api->download_link );
    }

    // Activate plugin.
    if ( current_user_can( 'activate_plugin' ) ) {
        $result = activate_plugin( 'ansar-import/ansar-import.php' );
    }
}