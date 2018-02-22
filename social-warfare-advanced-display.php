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

/**
 * Define plugin constants for use throughout the plugin (Version, Directories and Dependancies)
 */
define( 'SWAD_VERSION' , '1.0.0' );
define( 'SWAD_PLUGIN_FILE', __FILE__ );
define( 'SWAD_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SWAD_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'SWAD_ITEM_ID', 114481 );
define( 'SWAD_CORE_VERSION_REQUIRED' , '2.3.2');
if ( !defined( 'SWP_ACTIVATE_REGISTRATION_TAB' )) :
    define( 'SWP_ACTIVATE_REGISTRATION_TAB', true );
endif;


/**
 * Add a registration key for the registration functions
 *
 * @param Array An array of registrations for each paid addon
 * @return Array An array modified to add this new registration key
 *
 */
add_filter('swp_registrations' , 'social_warfare_enhanced_display_registration_key' , 20);
function social_warfare_enhanced_display_registration_key($array) {

    // Make sure core is on a version that contains our dependancies
    if (defined('SWP_VERSION') && version_compare(SWP_VERSION , SWAD_CORE_VERSION_REQUIRED) >= 0){

        // Add this plugin to the registrations array
        $array['advanced_display'] = array(
            'plugin_name' => 'Social Warfare - Advanced Display',
            'key' => 'advanced_display',
            'product_id' => SWAD_ITEM_ID
        );
    }

    // Return the modified or unmodified array
    return $array;
}

/**
 * A function to check for updates to this addon
 *
 * @since 1.0.0
 * @param none
 * @return none
 *
 */
add_action( 'plugins_loaded' , 'swad_update_checker' , 20 );
function swad_update_checker() {

    // Make sure core is on a version that contains our dependancies
    if (defined('SWP_VERSION') && version_compare(SWP_VERSION , SWAD_CORE_VERSION_REQUIRED) >= 0){

        // Check if the plugin is registered
        if( is_swp_addon_registered( 'advanced_display' ) ) {

            // retrieve our license key from the DB
            $license_key = swp_get_license_key('advanced_display');
            $website_url = swp_get_site_url();

            // setup the updater
            $swad_updater = new SWP_Plugin_Updater( SWP_STORE_URL , __FILE__ , array(
            	'version'   => SWAD_VERSION,		// current version number
            	'license'   => $license_key,	// license key
            	'item_id'   => SWAD_ITEM_ID,	// id of this plugin
            	'author'    => 'Warfare Plugins',	// author of this plugin
            	'url'       => $website_url,
                'beta'      => false // set to true if you wish customers to receive update notifications of beta releases
                )
            );
        }
    }
}

/**
 * A function to add this plugins options to the options page
 *
 * @since  1.0.0
 * @param  array $swp_options The array of options
 * @return array $swp_options The modified array
 */
add_filter('swp_options', 'SWAD_add_options' , 10 );
function SWAD_add_options($swp_options) {

    if (defined('SWP_VERSION') && version_compare(SWP_VERSION , SWAD_CORE_VERSION_REQUIRED) >= 0){

        // Check if the plugin is registered
        if( is_swp_addon_registered( 'advanced_display' ) ) {

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

        }
    }

    return $swp_options;
}

/**
 * The Button Emphasizer Function
 *
 * @since  1.0.0
 * @access public
 * @param  array $info An array of footer script information.
 * @return array $info A modified array of footer script information.
 */
add_filter( 'swp_footer_scripts' , 'swp_emphasize_buttons' );
function swp_emphasize_buttons( $info ) {

    // Ensure that core is installed and that it is an updated/compatible version
    if (defined('SWP_VERSION') && version_compare(SWP_VERSION , SWAD_CORE_VERSION_REQUIRED) >= 0){

        // Check if the plugin is registered
        if( is_swp_addon_registered( 'advanced_display' ) ) {

        	$info['footer_output'] .= PHP_EOL . '
                jQuery(window).on("pre_activate_buttons", swp_emphasize_buttons );
                jQuery(window).on("floating_bar_revealed", swp_emphasize_buttons );

                function swp_emphasize_buttons() {
                    // *Disable on mobile devices.
                    if (jQuery("body").width() < 576) return;

                    var emphasize_icons = jQuery(".nc_socialPanel:not(.nc_socialPanelSide)").attr("data-emphasize");
                    if( ! swp_isMobile.phone ) {
                        setTimeout( function () {
                            jQuery(".nc_socialPanel:not(.nc_socialPanelSide)").each(function(){
                                var i = 1;
                                jQuery(this).find(".nc_tweetContainer:not(.totes)").each(function(){
                                    if(i <= emphasize_icons) {
                                        jQuery(this).addClass("swp_nohover");
                                        var term_width = jQuery(this).find(".swp_share").width();
                                        var icon_width = jQuery(this).find("i.sw").outerWidth();
                            			var container_width = jQuery(this).width();
                            			var percentage_change = 1 + ((term_width + 35) / container_width);
                                        jQuery(this).find(".iconFiller").width(term_width + icon_width + 25 + "px");
                                        jQuery(this).css({flex:percentage_change + " 1 0%"});
                                    }
                                    ++i;
                                });
                            });
                        } , 25 );
                    }
                }
            ';

        }
    }

	return $info;
}
