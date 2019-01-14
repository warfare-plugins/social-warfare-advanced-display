<?php
class Social_Warfare_Advanced_Display extends Social_Warfare_Addon {
	public function __construct() {
		$this->name = 'Social Warfare - Advanced Display';
		$this->key = 'advanced_display';
		$this->product_id = 259301;
		$this->version = '1.1.0';
		$this->core_required = '3.0.0';
		parent::__construct();

		if ( !$this->is_registered ) {
			return;
		}

		$this->add_options();

		is_admin()
			? add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) )
			: add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		add_filter( 'swp_addon_javascript_variables', array( $this, 'fetch_values' ) );
		add_filter( 'swp_footer_scripts', array($this, 'add_addon_javascript' ) );
	}


	public function enqueue_assets() {
		wp_enqueue_style(
			'swad-style',
			plugins_url('social-warfare-advanced-display/assets/swad-style.css'),
			['social_warfare'], // the core Social Warfare stylesheet.
			false,
			false
		);
	}


	/**
	 * A function to add this plugins options to the options page
	 *
	 * @since  1.0.0 | JAN 08 2018 | Created.
	 * @param  array $swp_options The array of options
	 * @return array $swp_options The modified array
	 *
	 */
	public function add_options( ) {
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


	/**
	 * Prepares plugin options for frontend JavaScript.
	 *
	 * @since  1.0.0 | 08 JAN 2018 | Created.
	 * @access public
	 * @hook   filter | swp_footer_scripts | @see social-warfare\lib\frontent-output\SWP_Script.php
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 *
	 */
	public function fetch_values( $addon_vars) {
		$data = array();
		$emphasized_icon = SWP_Utility::get_option('emphasized_icon');

		$data['emphasizedIcon'] = $emphasized_icon;

		$addon_vars['advancedDisplay'] = $data;
		return $addon_vars;
	}


	/**
	 * Adds JavaScript to the filtered Social Warfare $info array.
	 *
	 * @since  1.0.0 | 08 JAN 2018 | Created.
	 * @access public
	 * @hook   filter | swp_footer_scripts | @see social-warfare\lib\frontent-output\SWP_Script.php
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 *
	 */
	public function add_addon_javascript( $info ) {
		ob_start();
		?>
		jQuery(window).on('load', swp_emphasize_buttons);

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

			jQuery(".swp_social_panel:not(.swp_social_panelSide)").each(function(i, panel){
				jQuery(panel).find(".nc_tweetContainer:not(.total_shares)").each(function(index, button) {
					if( index < socialWarfare.advancedDisplay.emphasizedIcon) {
						emphasizeIcon(button)
					}
				});
			});
		}

		function emphasizeIcon(button) {
			button = jQuery(button)
			button.addClass("swp_nohover swp-emphasize");


			/*
			var shareWidth = button.find(".swp_share").width();
			var iconWidth = button.find("i.sw").outerWidth();
			var containerWidth = jQuery(button).width();
			var change = 1 + ((shareWidth + 35) / containerWidth);
			*/

			//button.find(".iconFiller").width(shareWidth + iconWidth + 25 + "px");
			//button.css({flex:change + " 1 0%"});
		}

		<?php
		$script = ob_get_contents();
		ob_end_clean();

		$info['footer_output'] .= $script;

		return $info;
	}
}
