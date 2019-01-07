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
				// add_filter( 'swp_options', [$this, 'add_options'], 1, 1 );
				add_filter( 'swp_footer_scripts', array($this, 'emphasize_buttons' ) );
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

		$emphasize_icons = new SWP_Option_Select( __( 'Emphasize Buttons','social-warfare' ), 'emphasize_icons' );
		$emphasize_icons->set_choices([
			'0' 	=> __( 'Don\'t Emphasize Any Buttons','social-warfare' ),
			'1' 	=> __( 'Emphasize the First Button','social-warfare' ),
			'2' 	=> __( 'Emphasize the First Two Buttons','social-warfare' )
		  ])->set_priority( 100 )
			->set_size(  'sw-col-460' )
			->set_default( '0' )
			->set_premium( $this->key );

		$SWP_Options_Page->tabs->styles->sections->buttons_preview_section->add_option( $emphasize_icons );

	}

	/**
	 * The Button Emphasizer Function
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 */
	function emphasize_buttons( $info ) {
		ob_start();
		?>
console.log('Advanced Display js loaded')
		jQuery(window).on("pre_activate_buttons", swp_emphasize_buttons );
		jQuery(window).on("floating_bar_revealed", swp_emphasize_buttons );

		function swp_emphasize_buttons() {
			console.log('swp_emphasize_buttons')
			// *Disable on mobile devices.
			if (socialWarfare.isMobile()) {
				console.log('is moible')
				return;
			}

			var emphasize_icons = jQuery(".swp_social_panel:not(.swp_social_panelSide)").attr("data-emphasize");
			setTimeout(function() {
				jQuery(".swp_social_panel:not(.swp_social_panel)").each(function(){
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
			}, 25 );
		}

		<?php
		$script = ob_get_contents();
		ob_end_clean();

		$info['footer_output'] .= $script;

		return $info;
	}
}
