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
define( 'SWAD_VERSION', '2.0.0' );
define( 'SWAD_CORE_VERSION_REQUIRED', '3.0.0' );
define( 'SWAD_PLUGIN_FILE', __FILE__ );
define( 'SWAD_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SWAD_PLUGIN_DIR', dirname( __FILE__ ) );

add_action('plugins_loaded' , 'initialize_social_warfare_advanced_display' , 20 );

function initialize_social_warfare_advanced_display() {


   /**
	* Social Warfare (Core) is missing.
	*
	* If core is not loaded, we leave the plugin active, but we do not proceed
	* to load up, activate, or instantiate any of the addon's features. Instead we
	* simply activate a dashboard notification to let the user know that they
	* need to activate core.
	*
	*/
   if ( !defined( 'SWP_VERSION' ) ) {
	   add_action( 'admin_notices', 'swp_needs_core' );
   }

   /**
	* Version Compatibility
	*
	* As of 3.3.0 (3.2.90 is the beta), we are making the plugin backwards
	* compatible back to this version. Addons prior to 3.3.0 will still need
	* to be an exact match, everything on or after this version will simply
	* run using existence checks. This will allow that the bulk of the plugin
	* continues to run smoothly. Only features that are missing their
	* dependencies will gracefully deactivate until the other plugin is updated.
	*
	*/

   if( class_exists( 'Social_Warfare_Addon' ) && version_compare( SWP_VERSION , '3.4.9' ) >= 0 ) {
	   require_once 'Social_Warfare_Advanced_Display.php';
	   new Social_Warfare_Advanced_Display();
   }

   /**
   * If core is simply too far out of date, we will create a dashboard notice
   * to inform the user that they need to update core to the appropriate
   * version in order to get access to the addon.
   *
   */
   else {
	   add_filter( 'swp_admin_notices', 'swp_advanced_display_update_notification' );
   }


   /**
	* The plugin update checker
	*
	* This is the class for the plugin update checker. It is not dependent on
	* a certain version of core existing. Instead, it simply checks if the class
	* exists, and if so, it uses it to check for updates from GitHub.
	*
	*/
   if ( class_exists( 'Puc_v4_Factory') ) {
	   $update_checker = Puc_v4_Factory::buildUpdateChecker(
		   'https://github.com/warfare-plugins/social-warfare-advanced-display',
		   __FILE__,
		   'social-warfare-advanced-display'
	   );
	   $update_checker->getVcsApi()->enableReleaseAssets();
   }
}


/**
* Notificiation that Social Warfare (core) is needed.
*
* This is the dashboard notification that will alert users that in order to
* use the features of this plugin, they will need to have the core plugin
* installed and activated.
*
* @since  1.0.0 | 01 JAN 1970 | Created
* @param  void
* @return void
*
*/
if ( !function_exists( 'swp_needs_core' ) ) {
   function swp_needs_core() {
	   echo '<div class="update-nag notice is-dismissable"><p><b>Important:</b> You currently have Social Warfare - Advanced Display installed without our Core plugin installed.<br/>Please download the free core version of our plugin from the <a href="https://wordpress.org/plugins/social-warfare/" target="_blank">WordPress plugins repository</a>.</p></div>';
   }
}


/**
* Notify users that the versions of Social Warfare and Social Warfare Advanced Display are
* are currently on incompatible versions with each other.
*
* @since  2.2.0 | Unknown | Created
* @param  array $notices An array of notices to which we add our notice.
* @return void
*
*/
function swp_advanced_display_update_notification( $notices = array() ) {
	if (is_string( $notices ) ) {
		$notices = array();
	}

	$notices[] = array(
		'key'   => 'update_notice_ad_' . SWAD_VERSION, // database key unique to this version.
		'message'   => 'Looks like your copy of Social Warfare - Advanced Display isn\'t up to date with Core. While you can still use both of these plugins, we highly recommend you keep both Core and Advanced Display up-to-date for the best of what we have to offer.',
		'ctas'  => array(
			array(
				'action'    => 'Remind me in a week.',
				'timeframe' => 7 // dismiss for one week.
			),
			array(
				'action'    => 'Thanks for letting me know.',
				'timeframe' => 0 // permadismiss for this version.
			)
		)
	);

	return $notices;
}
