<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/includes
 * @author     Toby Wong <tobywong@prohaba.com>
 */
class Ech_Lfg {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ech_Lfg_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ECH_LFG_VERSION' ) ) {
			$this->version = ECH_LFG_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ech-lfg';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ech_Lfg_Loader. Orchestrates the hooks of the plugin.
	 * - Ech_Lfg_i18n. Defines internationalization functionality.
	 * - Ech_Lfg_Admin. Defines all hooks for the admin area.
	 * - Ech_Lfg_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ech-lfg-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ech-lfg-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ech-lfg-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ech-lfg-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ech-lfg-wati-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ech-lfg-omnichat-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ech-lfg-sleekflow-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ech-lfg-kommo-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ech-lfg-fb-capi-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ech-lfg-email-public.php';

		$this->loader = new Ech_Lfg_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ech_Lfg_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ech_Lfg_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ech_Lfg_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// ^^^ Add admin menu items
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'lfg_admin_menu' );

		// ^^^ Register our plugin settings
		$this->loader->add_action('admin_init', $plugin_admin, 'reg_lfg_general_settings');
		$this->loader->add_action('admin_init', $plugin_admin, 'reg_lfg_recaptcha_settings');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ech_Lfg_Public( $this->get_plugin_name(), $this->get_version() );
		$lfg_wati_public = new Ech_Lfg_Wati_Public( $this->get_plugin_name(), $this->get_version() );
		$lfg_omnichat_public = new Ech_Lfg_Omnichat_Public( $this->get_plugin_name(), $this->get_version() );
		$lfg_sleekflow_public = new Ech_Lfg_Sleekflow_Public( $this->get_plugin_name(), $this->get_version() );
		$lfg_kommo_public = new Ech_Lfg_Kommo_Public( $this->get_plugin_name(), $this->get_version() );
		$lfg_fb_capi_public = new Ech_Lfg_Fb_Capi_Public( $this->get_plugin_name(), $this->get_version() );
		$lfg_email_public = new Ech_Lfg_Email_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// ^^^ register lfg_formToMSP function
		$this->loader->add_action( 'wp_ajax_lfg_formToMSP', $plugin_public, 'lfg_formToMSP' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_formToMSP', $plugin_public, 'lfg_formToMSP' );
		
		// ^^^ register lfg_recaptVerify function
		$this->loader->add_action( 'wp_ajax_lfg_recaptVerify', $plugin_public, 'lfg_recaptVerify' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_recaptVerify', $plugin_public, 'lfg_recaptVerify' );
		
		// ^^^ register Wati functions
		$this->loader->add_action( 'wp_ajax_lfg_WatiSendMsg', $lfg_wati_public, 'lfg_WatiSendMsg' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_WatiSendMsg', $lfg_wati_public, 'lfg_WatiSendMsg' );
		
		$this->loader->add_action( 'wp_ajax_lfg_WatiAddContact', $lfg_wati_public, 'lfg_WatiAddContact' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_WatiAddContact', $lfg_wati_public, 'lfg_WatiAddContact' );
		
		// ^^^ register Omnichat functions
		$this->loader->add_action( 'wp_ajax_lfg_OmnichatSendMsg', $lfg_omnichat_public, 'lfg_OmnichatSendMsg' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_OmnichatSendMsg', $lfg_omnichat_public, 'lfg_OmnichatSendMsg' );

		// ^^^ register SleekFlow functions
		$this->loader->add_action( 'wp_ajax_lfg_SleekflowSendMsg', $lfg_sleekflow_public, 'lfg_SleekflowSendMsg' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_SleekflowSendMsg', $lfg_sleekflow_public, 'lfg_SleekflowSendMsg' );

		// ^^^ register Kommo functions
		$this->loader->add_action( 'wp_ajax_lfg_KommoSendMsg', $lfg_kommo_public, 'lfg_KommoSendMsg' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_KommoSendMsg', $lfg_kommo_public, 'lfg_KommoSendMsg' );

		// ^^^ register FB Lead CAPI
		$this->loader->add_action( 'wp_ajax_lfg_FBCapi', $lfg_fb_capi_public, 'lfg_FBCapi' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_FBCapi', $lfg_fb_capi_public, 'lfg_FBCapi' );


		// ^^^ register email send 
		$this->loader->add_action( 'wp_ajax_lfg_emailSend', $lfg_email_public, 'lfg_emailSend' );
		$this->loader->add_action( 'wp_ajax_nopriv_lfg_emailSend', $lfg_email_public, 'lfg_emailSend' );

		// ^^^ Add shortcodes
		$this->loader->add_shortcode( 'ech_lfg', $plugin_public, 'display_ech_lfg');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ech_Lfg_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
