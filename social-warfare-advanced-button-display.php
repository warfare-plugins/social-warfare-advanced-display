<?php
/**
 * Plugin Name: Social Warfare - Advanced Button Display
 * Plugin URI:  http://warfareplugins.com
 * Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
 * Version:     1.0.0
 * Author:      Warfare Plugins
 * Author URI:  http://warfareplugins.com
 * Text Domain: social-warfare
 */

defined( 'WPINC' ) || die;

/**
 * Define plugin constants for use throughout the plugin (Version and Directories)
 */
define( 'ABD_PLUGIN_FILE', __FILE__ );
define( 'ABD_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'ABD_PLUGIN_DIR', dirname( __FILE__ ) );

/**
 * A function to add this plugins options to the options page
 *
 * @since  1.0.0
 * @param  array $swp_options The array of options
 * @return array $swp_options The modified array
 */
add_filter('swp_options', 'abd_add_options' , 10 );
function abd_add_options($swp_options) {
    $option['emphasize_icons'] = array(
        'type'		=> 'select',
        'size'		=> 'two-thirds',
        'content'	=> array(
            '0' 	=> __( 'Don\'t Emphasize Any Buttons' ,'social-warfare' ),
    		'1' 	=> __( 'Emphasize the First Button' ,'social-warfare' ),
    		'2' 	=> __( 'Emphasize the First Two Buttons' ,'social-warfare' )
    	),
    	'default'	=> '0',
    	'name'		=> __( 'Emphasize Buttons' ,'social-warfare' ),
    	'premium'	=> true
    );

    $swp_options = swp_insert_option( $swp_options , 'swp_display' , 'buttons_divider' , $option , 'before' );

    return $swp_options;
}
