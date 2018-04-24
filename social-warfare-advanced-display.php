<?php
/**
 * Plugin Name: Social Warfare - Advanced Display
 * Plugin URI:  http://warfareplugins.com
 * Description: A plugin that allows you enahnced control over the display of Social Warfare on your website.
 * Version:     1.0.0
 * Author:      Warfare Plugins
 * Author URI:  http://warfareplugins.com
 * Text Domain: social-warfare
 */

defined( 'WPINC' ) || die;
define( 'SWAD_CORE_VERSION_REQUIRED', '3.0.0' );
define( 'SWAD_PLUGIN_FILE', __FILE__ );
define( 'SWAD_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SWAD_PLUGIN_DIR', dirname( __FILE__ ) );

add_action('plugins_loaded' , 'initialize_social_warfare_advanced_display' , 20 );
function initialize_social_warfare_advanced_display() {
	if( defined('SWP_VERSION') && SWP_VERSION === SWAD_CORE_VERSION_REQUIRED ):
		require_once 'Social_Warfare_Advanced_Display.php';
        $addon = new Social_Warfare_Advanced_Display();
        add_filter( 'swp_registrations', [$addon, 'add_self']  );
	endif;
}
