<?php
/**
 * Redux Framework functions
 *
 * @package FRONT/ReduxFramework
 */

/**
 * Setup functions for theme options
 */
require_once get_template_directory() . '/inc/redux-framework/functions/general-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/header-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/footer-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/blog-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/shop-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/portfolio-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/job-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/docs-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/customer-story-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/404-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/style-functions.php';
require_once get_template_directory() . '/inc/redux-framework/functions/typography-functions.php';

if ( ! function_exists( 'front_redux_remove_custom_css_panel' ) ) {
	function front_redux_remove_custom_css_panel() {
		$custom_script = '
			wp.domReady( function() {
				wp.hooks.removeFilter( "editor.BlockEdit", "redux-custom-css/with-inspector-controls" );
			} );
		';

		wp_add_inline_script( 'wp-blocks', $custom_script );
	}
}

/**
 * Disables Demo mode of Redux Framework
 * 
 * @return void
 */
function redux_remove_demo_mode() { // Be sure to rename this function to something more unique
    if ( class_exists( 'Redux_Framework_Plugin' ) ) {
        $instance = Redux_Framework_Plugin::get_instance();
        remove_filter( 'plugin_row_meta', array( $instance , 'plugin_metalinks'), null, 2 );
        remove_action( 'admin_notices', array( $instance , 'admin_notices' ) );
        remove_filter( 'network_admin_plugin_action_links', array( $instance , 'add_settings_link' ), 1, 2 );
        remove_filter( 'plugin_action_links', array( $instance, 'add_settings_link' ), 1, 2 );
    }
}
