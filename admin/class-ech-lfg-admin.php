<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/admin
 * @author     Toby Wong <tobywong@prohaba.com>
 */
class Ech_Lfg_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ech_Lfg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ech_Lfg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ech-lfg-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ech_Lfg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ech_Lfg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ech-lfg-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * ^^^ Add LFG Admin menu
	 *
	 * @since    1.0.0
	 */
	public function lfg_admin_menu() {
		add_menu_page( 'LFG Plugin Settings', 'ECH Form', 'manage_options', 'ech_lfg_general_settings', array($this, 'lfg_admin_page'), 'dashicons-buddicons-activity', 110 );
		add_submenu_page('ech_lfg_general_settings', 'ECH Form reCaptcha', 'reCaptcha', 'manage_options', 'ech_lfg_recaptcha', array( $this , 'lfg_recapt_page' ));
	}

	// return views
	public function lfg_admin_page() {
		require_once ('partials/ech-lfg-admin-display.php');
	}
	public function lfg_recapt_page() {
		require_once ('partials/lfg-recapt-page.php');
	}


	/**
	 * ^^^ Register custom fields for plugin settings
	 *
	 * @since    1.0.0
	 */
	public function reg_lfg_general_settings() {
		// Register all settings for general setting page
		register_setting( 'lfg_gen_settings', 'ech_lfg_apply_test_msp');
		register_setting( 'lfg_gen_settings', 'ech_lfg_brand_name');
		register_setting( 'lfg_gen_settings', 'ech_lfg_submitBtn_color');
		register_setting( 'lfg_gen_settings', 'ech_lfg_submitBtn_hoverColor');
		register_setting( 'lfg_gen_settings', 'ech_lfg_submitBtn_text_color');
		register_setting( 'lfg_gen_settings', 'ech_lfg_submitBtn_text_hoverColor');
		register_setting( 'lfg_gen_settings', 'ech_lfg_wati_key');
		register_setting( 'lfg_gen_settings', 'ech_lfg_wati_api_domain');
	}

	public function reg_lfg_recaptcha_settings() {
		register_setting( 'lfg_recapt', 'ech_lfg_apply_recapt');
		register_setting( 'lfg_recapt', 'ech_lfg_recapt_site_key');
		register_setting( 'lfg_recapt', 'ech_lfg_recapt_secret_key');
		register_setting( 'lfg_recapt', 'ech_lfg_recapt_score');
	}

	public function option_handler_ech_lfg_submitBtn_color($old_val, $new_val, $option) {
		/* if ($old_val !== $new_val) {
			add_settings_error( 'ech_lfg_submitBtn_color', 'ech_lfg_submitBtn_color', 'Submit button color has been updated', 'success' );
		}	 */	
		$statusMsg = 'handler';
		echo $statusMsg;
	}

}
