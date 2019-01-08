<?php
class Social_Warfare_Advanced_Display extends Social_Warfare_Addon {
	public function __construct() {
		$this->name = 'Social Warfare - Advanced Display';
		$this->key = 'advanced_display';
		$this->product_id = 114481;
		$this->version = '1.1.0';
		$this->core_required = '3.0.0';
		parent::__construct();

		// if ( $this->is_registered ) {
			if ( version_compare(SWP_VERSION, $this->core_required) >= 0) {
				$this->add_options();
				add_filter( 'swp_addon_javascript_variables', array( $this, 'fetch_values' ) );
				add_filter( 'swp_footer_scripts', array($this, 'add_addon_javascript' ) );
			}
		// }
	}


	/**
	 * A function to add this plugins options to the options page
	 *
	 * @since  1.0.0
	 * @param  array $swp_options The array of options
	 * @return array $swp_options The modified array
	 */
	function add_options( ) {
		global $SWP_Options_Page;

		$emphasize_icon = new SWP_Option_Select( __( 'Emphasize Buttons','social-warfare' ), 'emphasized_icon' );
		$emphasize_icon->set_choices([
			'0' 	=> __( 'Don\'t Emphasize Any Buttons','social-warfare' ),
			'1' 	=> __( 'Emphasize the First Button','social-warfare' ),
			'2' 	=> __( 'Emphasize the First Two Buttons','social-warfare' )
		  ])->set_priority( 100 )
			->set_size( 'sw-col-300' )
			->set_default( '0' )
			->set_premium( $this->key );

		$SWP_Options_Page->tabs->display->sections->social_networks->add_option( $emphasize_icon );
	}

	function fetch_values( $addon_vars) {
		$data = array();
		$emphasized_icon = SWP_Utility::get_option('emphasized_icon');

		$data['emphasizedIcon'] = $emphasize_icon;

		$addon_vars['advancedDisplay'] = $data;
		return $addon_vars;
	}

	/**
	 * The Button Emphasizer Function
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 */
	function add_addon_javascript( $info ) {
		ob_start();
		?>
		console.log('Advanced Display js loaded')
		jQuery(window).on("pre_activate_buttons", swp_emphasize_buttons );
		jQuery(window).on("floating_bar_revealed", swp_emphasize_buttons );

		function swp_emphasize_buttons() {
			if (typeof socialWarfare == 'undefined' || typeof socialWarfare.advancedDisplay == 'undefined') {
				return;
			}

			if (typeof socialWarfare.advancedDisplay.emphasizedIcon == 'undefined') {
				return;
			}

			// Advanced Display is only good for desktop views.
			if (socialWarfare.isMobile()) {
				return;
			}

			setTimeout(function() {
				jQuery(".swp_social_panel:not(.swp_social_panel)").each(function(){
					jQuery(this).find(".nc_tweetContainer:not(.total_shares)").each(function(index, button) {
						if( index < socialWarfare.advancedDisplay.emphasizedIcon) {
							emphasizeIcon(button)
						}
					});
				});
			}, 25 );
		}

		function emphasizeIcon(button) {
			jQuery(button).addClass("swp_nohover");
			var term_width = jQuery(button).find(".swp_share").width();
			var icon_width = jQuery(button).find("i.sw").outerWidth();
			var container_width = jQuery(button).width();
			var percentage_change = 1 + ((term_width + 35) / container_width);
			jQuery(button).find(".iconFiller").width(term_width + icon_width + 25 + "px");
			jQuery(button).css({flex:percentage_change + " 1 0%"});
		}

		<?php
		$script = ob_get_contents();
		ob_end_clean();

		$info['footer_output'] .= $script;

		return $info;
	}
}
