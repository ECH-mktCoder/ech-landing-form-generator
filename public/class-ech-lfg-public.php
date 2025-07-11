<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/public
 * @author     Toby Wong <tobywong@prohaba.com>
 */
class Ech_Lfg_Public
{

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ech-lfg-public.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '_jqueryUI', plugin_dir_url(__FILE__) . 'lib/jquery-ui-1.12.1/jquery-ui.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '_timepicker', plugin_dir_url(__FILE__) . 'lib/jquery-timepicker/jquery.timepicker.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name . '_jqueryUI', plugin_dir_url(__FILE__) . 'lib/jquery-ui-1.12.1/jquery-ui.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . '_timepicker', plugin_dir_url(__FILE__) . 'lib/jquery-timepicker/jquery.timepicker.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ech-lfg-public.js', array('jquery'), $this->version, false);
	}


	// ^^^ ECH LFG shortcode
	public function display_ech_lfg($atts)
	{
		// get the general settings 
		$getBrandName = get_option('ech_lfg_brand_name');
		if (empty($getBrandName)) {
			return '<div class="code_error">Settings Error: Must set brand name at dashboard settings</div>';
		}

		$paraArr = shortcode_atts(array(
			'default_r'	=> 'T1494',				// default r
			'default_r_code' => null,			// default r token
			'r' => null,						// tcode	
			'r_code' => null,					// tcode token
			'last_name_required' => '1',			// last_name_required. 0 = false, 1 = true
			'has_gender' => '0',				// has gender field. 0 = false, 1 = true
			'has_age' => '0',				// has age field. 0 = false, 1 = true
			'age_option' => null,				// age option
			'age_code' => null,				// age MSP token
			'email_required' => '1',			// email_required. 0 = false, 1 = true
			'booking_date_required' => '1',			// booking_date_required. 0 = false, 1 = true
			'item' => null,						// item checkbox
			'item_code' => null,				// item MSP token
			'item_label' => $this->form_echolang(['*Select Item','*查詢項目','*查询项目']),		 // item label
			'item_required' => '1',		 // item_required. 0 = false, 1 = true
			'is_item_limited' => '0',			// are the items limited. 0 = false, 1 = true
			'item_limited_num' => '1',			// No. of options can the user choose
			'has_select_dr' => '0',				// has Select Doctor field. 0 = false, 1 = true
			'dr' => null,						// Doctor value
			'dr_code' => null,					// Doctor code
			'shop' => null,						// shop
			'shop_code' => null,				// shop MSP token
			'shop_label' => $this->form_echolang(['*Select Shop','*請選擇診所','*请选择诊所']),		 // shop label
			'form_type' => '0',				// Choose form type field. 0 = false, 1 = true
			'has_textarea' => '0',				// has textarea field. 0 = false, 1 = true
			'textarea_label' => $this->form_echolang(['Other professional consultation','其他專業諮詢','其他专业咨询']),	 // textarea label
			'extra_radio_remark'=> null, // extra single choice questions
			'has_hdyhau' => '0',				// has "How did you hear about us" field. 0 = false, 1 = true
			'hdyhau_label' =>$this->form_echolang(['How did you hear about us ?','從何得知?','从何得知？']),
			'hdyhau_item' => null,				// "How did you hear about us" items
			'seminar'=>'0',           //Health Talk
			'seminar_date'=> null,   //Health Talk Time Option
			'has_participant'=>'0',  //Health Talk participant field
			'quota_required'=> '0',              //booking quota field. 0 = false, 1 = true
			'submit_label'=> $this->form_echolang(['Submit','提交','提交']), //submit button label
			'brand' => $getBrandName,			// for MSP, website name value
			'tks_para' => null,					// parameters need to pass to thank you page
			// Wati data
			'wati_send' => 0,
			'wati_msg' => null,
			'msg_header'=> null,        // parameters need to pass to omnichat api
			'msg_body'=> null,					// parameters need to pass to omnichat api
			'msg_button'=> null,				// parameters need to pass to omnichat api
			//FB Capi
			'fbcapi_send'=>'0', 		  // fbcapi_required. 0 = false, 1 = true
			'note_required' => '0',		//note_required. 0 = false, 1 = true
			// Email
			'email_send' =>'0',
			'email_receiver' => get_option( 'ech_lfg_email_receiver' )

		), $atts);


		if ($paraArr['default_r_code'] == null) {
			return '<div class="code_error">shortcode error - default_r_code not specified</div>';
		}
		if (($paraArr['r'] != null) && $paraArr['r_code'] == null) {
			return '<div class="code_error">shortcode error - r_code not specified</div>';
		}
		if (($paraArr['r_code'] != null) && $paraArr['r'] == null) {
			return '<div class="code_error">shortcode error - r not specified</div>';
		}



		if ($paraArr['item'] == null) {
			return '<div class="code_error">shortcode error - item not specified</div>';
		}
		if ($paraArr['item_code'] == null) {
			return '<div class="code_error">shortcode error - item_code not specified</div>';
		}
		if ($paraArr['shop'] == null) {
			return '<div class="code_error">shortcode error - shop not specified</div>';
		}
		if ($paraArr['shop_code'] == null) {
			return '<div class="code_error">shortcode error - shop_code not specified</div>';
		}
		if ($paraArr['brand'] == null) {
			return '<div class="code_error">shortcode error - brand not specified</div>';
		}

		// Parse type into an array. Whitespace will be stripped.
		//$paraArr['r'] = array_map('trim', str_getcsv($paraArr['r'], '|'));
		$paraArr['r'] = array_map('trim', array_map('strtolower', str_getcsv($paraArr['r'], '|')) );
		$paraArr['r'] = array_filter($paraArr['r']); // remove empty value
		// Child values. Parse type into an array
		foreach ($paraArr['r'] as $key => $val) {
			$paraArr['r'][$key] = array_map('trim', str_getcsv($paraArr['r'][$key], ','));
		}


		$paraArr['r_code'] = array_map('trim', str_getcsv($paraArr['r_code'], ','));


		$paraArr['item'] = array_map('trim', str_getcsv($paraArr['item'], ','));
		$paraArr['item_code'] = array_map('trim', str_getcsv($paraArr['item_code'], ','));
		
		$paraArr['shop'] = array_map('trim', str_getcsv($paraArr['shop'], ','));
		$paraArr['shop_code'] = array_map('trim', str_getcsv($paraArr['shop_code'], ','));

		$has_dr = htmlspecialchars(str_replace(' ', '', $paraArr['has_select_dr']));
		if ($has_dr == "1") {
			$has_dr_bool = true;
		} else {
			$has_dr_bool = false;
		}
		$paraArr['dr'] = !empty($paraArr['dr']) ? array_map('trim', str_getcsv($paraArr['dr'], ',')) : [];
		$paraArr['dr_code'] = !empty($paraArr['dr_code']) ? array_map('trim', str_getcsv($paraArr['dr_code'], ',')) : [];



		if (count($paraArr['r_code']) != count($paraArr['r'])) {
			return '<div class="code_error">shortcode error - r_code and r count array value is not the same. They must be corresponding to each other.</div>';
		}

		if (count($paraArr['item']) != count($paraArr['item_code'])) {
			return '<div class="code_error">shortcode error - item and item_code must be corresponding to each other</div>';
		}

		if (count($paraArr['shop']) != count($paraArr['shop_code'])) {
			return '<div class="code_error">shortcode error - shop and shop_code must be corresponding to each other</div>';
		}
		if (count($paraArr['dr']) != count($paraArr['dr_code'])) {
			return '<div class="code_error">shortcode error - dr and dr_code must be corresponding to each other</div>';
		}

		$has_gender = htmlspecialchars(str_replace(' ', '', $paraArr['has_gender']));
		$has_age = htmlspecialchars(str_replace(' ', '', $paraArr['has_age']));
		if ($has_age == "1" && empty($paraArr['age_option'])) {
			return '<div class="code_error">shortcode error - at least one or more age_options</div>';
		}

		$has_hdyhau = htmlspecialchars(str_replace(' ', '', $paraArr['has_hdyhau']));
		if ($has_hdyhau == "1" && empty($paraArr['hdyhau_item'])) {
			return '<div class="code_error">shortcode error - at least one or more hdyhau_items</div>';
		}

		$seminar = htmlspecialchars(str_replace(' ', '', $paraArr['seminar']));
		if ($seminar == "1" && empty($paraArr['seminar_date'])) {
			return '<div class="code_error">shortcode error - at least one or more seminar_date</div>';
		}

		$has_participant = htmlspecialchars(str_replace(' ', '', $paraArr['has_participant']));
		if ($has_participant == "1") {
			$has_participant_bool = true;
		} else {
			$has_participant_bool = false;
		}

		$default_r = htmlspecialchars(str_replace(' ', '', $paraArr['default_r']));
		$default_r_code = htmlspecialchars(str_replace(' ', '', $paraArr['default_r_code']));
		$get_tks_para = htmlspecialchars(str_replace(' ', '', $paraArr['tks_para']));
		if ($get_tks_para == null) {
			$tks_para = "";
		} else {
			$tks_para = $get_tks_para;
		}

		$last_name_required = htmlspecialchars(str_replace(' ', '', $paraArr['last_name_required']));
		if ($last_name_required == "1") {
			$last_name_required_bool = true;
		} else {
			$last_name_required_bool = false;
		}

		$email_required = htmlspecialchars(str_replace(' ', '', $paraArr['email_required']));
		if ($email_required == "1") {
			$email_required_bool = true;
		} else {
			$email_required_bool = false;
		}

		$booking_date_required = htmlspecialchars(str_replace(' ', '', $paraArr['booking_date_required']));
		if ($booking_date_required == "1") {
			$booking_date_required_bool = true;
		} else {
			$booking_date_required_bool = false;
		}

		$item_limited_num = htmlspecialchars(str_replace(' ', '', $paraArr['item_limited_num']));
		$is_item_limited = htmlspecialchars(str_replace(' ', '', $paraArr['is_item_limited']));
		if ($is_item_limited == "1") {
			$limited_bool = true;
		} else {
			$limited_bool = false;
		}
		$item_required = htmlspecialchars(str_replace(' ', '', $paraArr['item_required']));

		$brand = htmlspecialchars(str_replace(' ', '', $paraArr['brand']));
		$item_label = htmlspecialchars(str_replace(' ', '', $paraArr['item_label']));
		$shop_label = htmlspecialchars(str_replace(' ', '', $paraArr['shop_label']));
		$submit_label = htmlspecialchars(str_replace(' ', '', $paraArr['submit_label']));

		$form_type = htmlspecialchars(str_replace(' ', '', $paraArr['form_type']));
		if ($form_type == "1") {
			$form_type_bool = true;
		} else {
			$form_type_bool = false;
		}

		$has_textarea = htmlspecialchars(str_replace(' ', '', $paraArr['has_textarea']));
		if ($has_textarea == "1") {
			$has_textarea_bool = true;
		} else {
			$has_textarea_bool = false;
		}
		$textarea_label = htmlspecialchars(str_replace(' ', '', $paraArr['textarea_label']));

		if ($has_gender == "1") {
			$has_gender_bool = true;
		} else {
			$has_gender_bool = false;
		}

		if ($has_age == "1") {
			$has_age_bool = true;
		} else {
			$has_age_bool = false;
		}
		$paraArr['age_option'] = !empty($paraArr['age_option']) ? array_map('trim', str_getcsv($paraArr['age_option'], ',')) : [];
		$paraArr['age_code'] = !empty($paraArr['age_code']) ? array_map('trim', str_getcsv($paraArr['age_code'], ',')) : [];

		if (count($paraArr['age_option']) != count($paraArr['age_code'])) {
			return '<div class="code_error">shortcode error - age_option and age_code must be corresponding to each other</div>';
		}

		if ($has_hdyhau == "1") {
			$has_hdyhau_bool = true;
		} else {
			$has_hdyhau_bool = false;
		}
		$hdyhau_label = htmlspecialchars(str_replace(' ', '', $paraArr['hdyhau_label']));

		$paraArr['hdyhau_item'] = !empty($paraArr['hdyhau_item']) ? array_map('trim', str_getcsv($paraArr['hdyhau_item'], ',')) : [];

		$paraArr['seminar_date'] = !empty($paraArr['seminar_date']) ? array_map('trim', str_getcsv($paraArr['seminar_date'], ',')) : [];


		if($paraArr['extra_radio_remark']){
			$extra_radio_remark = array_map('trim', str_getcsv($paraArr['extra_radio_remark'], ','));
		}

		$quota_required = htmlspecialchars(str_replace(' ', '', $paraArr['quota_required']));

		// Wati 
		$wati_send = htmlspecialchars(str_replace(' ', '', $paraArr['wati_send']));
		$wati_msg = htmlspecialchars(str_replace(' ', '', $paraArr['wati_msg'] ?? ''));
		$msg_header = htmlspecialchars(str_replace(' ', '', $paraArr['msg_header'] ?? ''));
		$msg_body = htmlspecialchars(str_replace(' ', '', $paraArr['msg_body'] ?? ''));
		$msg_button = htmlspecialchars(str_replace(' ', '', $paraArr['msg_button'] ?? ''));
		$msg_send_api="";
		if ( $wati_send == 1 ) {

			$msg_send_api = get_option( 'ech_lfg_msg_api' );
			if(empty($msg_send_api)){
				return '<div class="code_error">Sending Message Api error - Sending Message Api Should be choose. Please setup in dashboard. </div>';
			}

			if ($wati_msg == null && $msg_send_api != 'kommo') {
				error_log($msg_send_api);
				return '<div class="code_error">wati_send error - wati_send enabled, wati_msg cannot be empty</div>';
			}
			
			if($msg_send_api == 'wati'){
				$get_watiKey = get_option( 'ech_lfg_wati_key' );
				$get_watiAPI = get_option( 'ech_lfg_wati_api_domain' );
				if ( empty($get_watiKey) || empty($get_watiAPI) ) {
					return '<div class="code_error">Wati error - Wati Key or Wati API are empty. Please setup in dashboard. </div>';
				}
			}elseif($msg_send_api == 'omnichat'){
				$get_brandWtsNo = get_option( 'ech_lfg_brand_whatsapp' );
				$get_omnichat_token = get_option( 'ech_lfg_omnichat_token' );
				if ( empty($get_brandWtsNo) || empty($get_omnichat_token) ) {
					return '<div class="code_error">Omnichat error - Brand Whatsapp Number or Omnichat Token are empty. Please setup in dashboard. </div>';
				}
			}elseif($msg_send_api == 'sleekflow'){
				$wati_msg_ary = array_filter(array_map('trim', array_map('strtolower', str_getcsv($wati_msg, '|'))));
				if(count($wati_msg_ary) != 2){
					return '<div class="code_error">wati_msg error - Sleekflow objectKey or Wati API are empty.</div>';
				}
				$get_brandWtsNo = get_option( 'ech_lfg_brand_whatsapp' );	
				$get_sleekflow_token = get_option( 'ech_lfg_sleekflow_token' );
				if ( empty($get_brandWtsNo) || empty($get_sleekflow_token) ) {
					return '<div class="code_error">SleekFlow error - Brand Whatsapp Number or SleekFlow Token are empty. Please setup in dashboard. </div>';
				}
			}elseif($msg_send_api == 'kommo'){
				$get_brandWtsNo = get_option( 'ech_lfg_brand_whatsapp' );	
				$get_kommo_token = get_option( 'ech_lfg_kommo_token' );
				$get_kommo_pipeline_id = get_option( 'ech_lfg_kommo_pipeline_id' );
				$get_kommo_status_id = get_option( 'ech_lfg_kommo_status_id' );
				if ( empty($get_brandWtsNo) || empty($get_kommo_token) || empty($get_kommo_pipeline_id) || empty($get_kommo_status_id)) {
					return '<div class="code_error">Kommo error - Brand Whatsapp Number or Kommo Token or Kommo Pipeline ID or Status ID are empty. Please setup in dashboard. </div>';
				}
			}
		}
		// FB Capi 
		$fbcapi_send = htmlspecialchars(str_replace(' ', '', $paraArr['fbcapi_send']));
		$accept_pll = get_option( 'ech_lfg_accept_pll' );
		if($fbcapi_send){
			$get_pixelId = get_option( 'ech_lfg_pixel_id' );
			$get_fbAccessToken = get_option( 'ech_lfg_fb_access_token' );

			if ( empty($get_pixelId) || empty($get_fbAccessToken) ) {
				return '<div class="code_error">FB Capi error - Pixel id or FB Access Token are empty. Please setup in dashboard. </div>';
			}
		}
		$note_required = htmlspecialchars(str_replace(' ', '', $paraArr['note_required']));
		if($note_required){
			$note_phone = get_option( 'ech_lfg_note_phone' );
			$note_whatapps_link = get_option( 'ech_lfg_note_whatapps_link' );

			if ( empty($note_phone) || empty($note_whatapps_link) ) {
				return '<div class="code_error">Note error - Note Phone or Whatsapp Link are empty. Please setup in dashboard. </div>';
			}
		}


		// Vet 

		// Email
		$email_send = htmlspecialchars(str_replace(' ', '', $paraArr['email_send']));
		//$email_receiver = trim($paraArr['email_receiver']);
		$email_receiver = htmlspecialchars(str_replace(' ', '', $paraArr['email_receiver']));
		if($email_send){
			//$paraArr['email_receiver'] = array_map('trim', str_getcsv($paraArr['email_receiver'], ','));			
			if ( empty($email_receiver) ) {
				return '<div class="code_error">email_receiver error - email_receiver is empty. Please setup in dashboard or shortcode attributes . </div>';
			}
		}


		$ip = $_SERVER['REMOTE_ADDR'];





		if (isset($_GET['r'])) {
			$get_r = strtolower($_GET['r']);
		} else {
			$get_r = $default_r;
		}


		if (!empty($paraArr['r'])) {
			$sourceArr =  $paraArr['r'];
			foreach ($sourceArr as $key => $rValArr) {
				// Search if $get_r value exist in child array
				if (in_array($get_r, $rValArr)) {
					$r = $get_r;

					$parentArr_key = $key;
					$c_token = $paraArr['r_code'][$parentArr_key];
					break;
				}

				$r = $default_r;
				$c_token = $default_r_code;
			}
		} else {
			$r = $default_r;
			$c_token = $default_r_code;
		}

		$shop_count = count($paraArr['shop']);
		

		$rand = $this->genRandomString(); // For ePay refcode

		$output = '';
		/***** FOR TESTING OUTPUT *****/
		
		/*
		$output .= '<div><pre>';
		$output .= 'GET[r]: ' . $_GET['r'] . '<br>';
		$output .= '$r: ' . $r . '<br>';
		$output .= '$c_token: ' . $c_token . '<br>';
		//$output .= '$parentArr_key: ' . $parentArr_key . '<br>';

		$output .= '---------------------------<br>';
		$output .= 'r: ' . print_r($paraArr['r'], true) . '<br>';

		$output .= 'r_code: ' . print_r($paraArr['r_code'], true) . '<br>';
		$output .= 'default_r: ' . $default_r . '<br>';
		$output .= 'default_r_code: ' . $default_r_code . '<br>';
		$output .= 'item: ' . print_r($paraArr['item'], true) . '<br>';
		$output .= 'item code: ' . print_r($paraArr['item_code'], true) . '<br>';
		$output .= 'is_item_limited: ' . $is_item_limited . '<br>';
		$output .= 'limited_bool: ' . var_export($limited_bool, true);
		$output .= '<br>';

		$output .= 'shop: ' . print_r($paraArr['shop'], true) . '<br>';
		$output .= 'shop_code: ' . print_r($paraArr['shop_code'], true) . '<br>';
		$output .= 'brand: ' . $brand . '<br>';
		$output .= 'tks_para: ' . $tks_para . '<br>';
		$output .= '<br><br>';
		$output .= 'current r: ' . $r . ' | current token: ' . $c_token;
		$output .= '<br><br>';
		$output .= 'has_hdyhau: ' . $has_hdyhau . ' | hdyhau_item: ' . print_r($paraArr['hdyhau_item'], true);
		$output .= '</pre></div>';
		*/
		
		/***** (END) FOR TESTING OUTPUT *****/

		// *********** Custom styling ***************/
		if ( !empty(get_option( 'ech_lfg_submitBtn_color' )) || !empty(get_option( 'ech_lfg_submitBtn_hoverColor') || !empty(get_option('ech_lfg_submitBtn_text_color')) || !empty(get_option('ech_lfg_submitBtn_text_hoverColor')) ) ) {
			$output .= '<style>';
			
			$output .= '.ech_lfg_form #submitBtn { ';
				( !empty(get_option( 'ech_lfg_submitBtn_color' )) ) ? $output .= 'background:'. get_option( 'ech_lfg_submitBtn_color' ).';' : '';
				( !empty(get_option( 'ech_lfg_submitBtn_text_color' )) ) ? $output .= 'color:'. get_option( 'ech_lfg_submitBtn_text_color' ).';' : '';
			$output .= '}';

			$output .= '.ech_lfg_form #submitBtn:hover { ';
				( !empty(get_option( 'ech_lfg_submitBtn_hoverColor' )) ) ? $output .= 'background:'. get_option( 'ech_lfg_submitBtn_hoverColor' ).';' : '';
				( !empty(get_option( 'ech_lfg_submitBtn_text_hoverColor' )) ) ? $output .= 'color:'. get_option( 'ech_lfg_submitBtn_text_hoverColor' ).';' : '';
			$output .= '}';


			$output .= '</style>';
		}
		// *********** (END) Custom styling ****************/


		// *********** Check if connected to test msp api ***************/
		if ( get_option('ech_lfg_apply_test_msp') == "1" && current_user_can( 'manage_options' ) ) {
			$output .= '<div style="background: #ff6a6a;color: #fff">Please note that all the LFG plugin forms are connected to TEST MSP API</div>';
		}
		// *********** (END) Check if connected to test msp api ***************/



		// *********** Check if apply reCAPTCHA v3 ***************/
		if ( get_option('ech_lfg_apply_recapt') == "1" ) {
			$output .= '<script src="https://www.google.com/recaptcha/api.js?render='. get_option( 'ech_lfg_recapt_site_key' ) .'"></script>';
		}
		// *********** (END) Check if apply reCAPTCHA v3 ***************/


		// set verification for real amount value using WP nonce to prevent changing value in DevTool
		$s = $_SERVER['SERVER_NAME']; // add for more difficulties to hack
		$lfg_email_nonce = wp_create_nonce($email_receiver . $s);


		$output .= '
		<form class="ech_lfg_form" id="ech_lfg_form" action="" method="post" data-limited-no="' . $item_limited_num . '" data-r="' . $r . '" data-c-token="' . $c_token . '" data-shop-label="' . $shop_label . '" data-shop-count="' . $shop_count . '" data-ajaxurl="' . get_admin_url(null, 'admin-ajax.php') . '" data-ip="' . $ip . '" data-url="https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" has_participant="' . $has_participant . '" data-has-textarea="' . $has_textarea . '" data-has-select-dr="' . $has_dr . '" data-item-label="' . $item_label . '" data-item-required="' . $item_required . '" data-tks-para="' . $tks_para . '" data-brand="' . $brand . '" data-has-gender="' . $has_gender . '" data-has-age="' . $has_age . '" data-has-hdyhau="' . $has_hdyhau . '" data-apply-recapt="'.get_option('ech_lfg_apply_recapt').'" data-recapt-site-key="'. get_option('ech_lfg_recapt_site_key') .'" data-recapt-score="'.get_option('ech_lfg_recapt_score').'" data-msg-send-api="'.$msg_send_api.'" data-wati-send="'. $wati_send .'" data-wati-msg="'.$wati_msg.'" data-msg-header="'.$msg_header.'" data-msg-body="'.$msg_body.'" data-msg-button="'.$msg_button.'" data-epay-refcode="LPE_'.trim($brand).$rand.time().'" data-fbcapi-send="'. $fbcapi_send .'" data-accept-pll="'. $accept_pll .'" data-seminar="'.$seminar.'" data-email-send="'.$email_send.'" data-email-receiver="'.$email_receiver.'" >
			<div class="form_row lfg_formMsg"></div>
			<div class="form_row" data-ech-field="hidden">
				<input type="hidden" name="booking_time" value="">
				<input type="hidden" name="lfg_email_nonce" value="'.$lfg_email_nonce.'">
			</div>
			';

			if ($form_type == "1") {
				$output .= '
				<div class="form_row" data-ech-field="form_type">
					<div>請選擇預約或查詢: </div>
					<div class="radio_label">
						<input type="radio" name="lfg_form_type" id="booking_type" value="booking" checked="checked"/>
						<label for="booking_type" class="form_type_lable">預約</label>
						<input type="radio" name="lfg_form_type" id="enquiry_type" value="enquiry"/>
						<label for="enquiry_type" class="form_type_lable">查詢</label>
					</div>
				</div>';
			}

			if ($last_name_required_bool) {
				$output .='
				<div class="form_row" data-ech-field="last_name">
					<input type="text" name="last_name" id="last_name"  class="form-control"  placeholder="'.$this->form_echolang(['*Last Name','*姓氏','*姓氏']).'" pattern="[ A-Za-z\u3000\u3400-\u4DBF\u4E00-\u9FFF]{1,}"  size="40" required >
				</div>
				';
			} else {
				$output .='
				<div class="form_row"  data-ech-field="last_name" style="display:none;">
					<input type="text" name="last_name" id="last_name"  class="form-control"  placeholder="*姓氏" pattern="[ A-Za-z\u3000\u3400-\u4DBF\u4E00-\u9FFF]{1,}"  size="40">
				</div>
				';
			}
			$output .= '
			<div class="form_row" data-ech-field="first_name">
				<input type="text" name="first_name" id="first_name" class="form-control" placeholder="'.$this->form_echolang(['*First Name','*名字','*名字']).'" pattern="[ A-Za-z\u3000\u3400-\u4DBF\u4E00-\u9FFF]{1,}" size="40" required >
			</div>
			';
			//**** Gender
			if ($has_gender_bool) {
				$output .= '
				<div class="form_row" data-ech-field="gender">
					<select  class="form-control" name="gender" id="gender" style="width: 100%;" >
						<option disabled="" selected="" value="">'.$this->form_echolang(['*Gender','*性別','*性别']).'</option>
						<option value="male">'.$this->form_echolang(['Male','男性','男性']).'</option>
						<option value="female">'.$this->form_echolang(['Female','女性','女性']).'</option>
					</select>
				</div>';
			}
			//**** (END) Gender

			//**** Age
			if ($has_age_bool) {
				$output .= '
				<div class="form_row" data-ech-field="age">
					<select  class="form-control" name="age" id="age" style="width: 100%;" >
						<option disabled="" selected="" value="">'.$this->form_echolang(['Age','年齡','年龄']).'</option>';
						for ($i = 0; $i < count($paraArr['age_option']); $i++) {
							$output .= '<option value="' . $paraArr['age_code'][$i] . '">' . $paraArr['age_option'][$i] . '</option>';
						}
					$output .= '
					</select>
				</div>';
			}
			//**** (END) Age

			//**** Tel Prefix
			$output .= '
			<div class="form_row" data-ech-field="telPrefix" style="display:none;">
				<select  class="form-control" name="telPrefix" id="tel_prefix" style="width: 100%;" required >
					<option disabled="" value="">*請選擇</option>
					<option value="+852" selected>+852</option>
					<option value="+853">+853</option>
					<option value="+86">+86</option> 
				</select>
			</div>';
			//**** (END) Tel Prefix

			//**** Tel
			$output .= '
			<div class="form_row" data-ech-field="tel">
				<input type="text" name="tel" placeholder="'.$this->form_echolang(['*Phone','*電話','*电话']).'"  class="form-control" size="30" id="tel" pattern="[0-9]{8,11}" required >
			</div>';
			//**** (END) Tel
			
			//**** Email
			$output .= '
			<div class="form_row" data-ech-field="email">';
				if ($email_required_bool) {
					$output .= '<input type="email" name="email" id="email" placeholder="'.$this->form_echolang(['*Email','*電郵','*电邮']).'" class="form-control" size="40" required>';
				} else {
					$output .= '<input type="email" name="email" id="email" placeholder="'.$this->form_echolang(['Email','電郵','电邮']).'" class="form-control" size="40" >';
				}
			$output .= '
			</div>';
			//**** (END) Email




			//******* Choose doctor if any
			if ($has_dr_bool) {
				$output .= '
				<div class="form_row" data-ech-field="select_dr">
					<select  class="form-control" name="select_dr" id="select_dr" style="width: 100%;" required >
						<option disabled="" selected="" value="">'.$this->form_echolang(['*Select Doctor','*請選擇醫生','*请选择医生']).'</option>';
						for ($i = 0; $i < count($paraArr['dr']); $i++) {
							$output .= '<option value="' . $paraArr['dr_code'][$i] . '">' . $paraArr['dr'][$i] . '</option>';
						}
					$output .= '
					</select>
				</div>';
			}
			//******* (END) Choose doctor if any

			// Booking Date and Time
			if ($booking_date_required_bool) {
				$output .= '
				<div class="form_row" data-ech-field="booking_date">
					<input type="text" placeholder="'.$this->form_echolang(['*Booking Date','*預約日期','*预约日期']).'" class="form-control lfg_datepicker" name="booking_date" autocomplete="off" value="" size="40" required>
				</div>
				<div class="form_row" data-ech-field="booking_time">
						<input type="text" placeholder="'.$this->form_echolang(['*Booking Time','*預約時間','*预约时间']).'" id="booking_time" class="form-control lfg_timepicker ui-timepicker-input" name="booking_time" autocomplete="off" value="" size="40" required="">
				</div>';
			}else{
				$output .= '
				<div class="form_row" data-ech-field="booking_date" style="display:none">
					<input type="text" placeholder="'.$this->form_echolang(['*Booking Date','*預約日期','*预约日期']).'" class="form-control lfg_datepicker" name="booking_date" autocomplete="off" value="" size="40">
				</div>
				<div class="form_row" data-ech-field="booking_time" style="display:none">
						<input type="text" placeholder="'.$this->form_echolang(['*Booking Time','*預約時間','*预约时间']).'" id="booking_time" class="form-control lfg_timepicker ui-timepicker-input" name="booking_time" autocomplete="off" value="" size="40">
				</div>';
			}


			//**** Location Options
			$output .= '
			<div class="form_row" data-ech-field="shop">';
				if ($shop_count <= 3) {
					// radio
					if ($shop_count == 1) {
						$output .= '<label class="radio_label" style="display: none;"><input type="radio" value="' . $paraArr['shop_code'][0] . '" data-shop-text-value="' . $paraArr['shop'][0] . '" name="shop" checked onclick="return false;">' . $paraArr['shop'][0] . '</label>';
					} else {
						$output .= '<div>' . $shop_label . '</div>';
						for ($i = 0; $i < $shop_count; $i++) {
							$output .= '<label class="radio_label"><input type="radio" value="' . $paraArr['shop_code'][$i] . '" name="shop" data-shop-text-value="' . $paraArr['shop'][$i] . '" required>' . $paraArr['shop'][$i] . '</label>';
						}
					}
				} else {
					// select
					$output .= '
					<select class="form-control" name="shop" id="shop" style="width: 100%;" required >
						<option disabled="" selected="" value="">' . $shop_label . '</option>';
						for ($i = 0; $i < $shop_count; $i++) {
							$output .= '<option value="' . $paraArr['shop_code'][$i] . '">' . $paraArr['shop'][$i] . '</option>';
						}
					$output .= '
					</select>';
				}
			$output .= '
			</div>';

			//**** (END) Location Options

			//**** Health Talk
			if ($seminar) {
				$weekdays = [
					'Monday'    => '一',
					'Tuesday'   => '二',
					'Wednesday' => '三',
					'Thursday'  => '四',
					'Friday'    => '五',
					'Saturday'  => '六',
					'Sunday'    => '日',
				];
				$output .= '
				<div class="form_row" data-ech-field="select_seminar">
					<select  class="form-control" name="select_seminar" id="select_seminar" style="width: 100%;" required>
						<option selected="selected" value="" >'.$this->form_echolang(['*Sessions','*講座場次','*讲座场次']).'</option>';
				
						for ($i = 0; $i < count($paraArr['seminar_date']); $i++) {
							$item = array_map('trim', str_getcsv($paraArr['seminar_date'][$i], '|'));
							$date = array_map('trim', str_getcsv($item[1], ' '))[0];
							$time = array_map('trim', str_getcsv($item[1], ' '))[1];
							$startTime = array_map('trim', str_getcsv($time, '-'))[0];
							$endTime = array_map('trim', str_getcsv($time, '-'))[1];
							$dateTime = DateTime::createFromFormat("Y-m-d H:i", $date." ".$startTime);
					
							if ($dateTime !== false) {
									$weekday = $this->form_echolang(['l',$weekdays[$dateTime->format("l")],$weekdays[$dateTime->format("l")]]);
									$midday = ($dateTime->format('H') > 12)?$this->form_echolang(['pm','下午','下午']):$this->form_echolang(['am','上午','上午']);
									$formattedString_en = $dateTime->format("Y-m-d（".$weekday."） H:i ");
									$formattedString_zh = $dateTime->format("Y年m月d日（".$weekday."）".$midday."H:i");
									$formattedString_sc = $dateTime->format("Y年m月d日（".$weekday."）".$midday."H:i");
									$otherStr = "";
									if($endTime!=""){
										$otherStr .= $this->form_echolang([" - ".$endTime." ".$midday,"-".$endTime,"-".$endTime]);
									}
									for ($k=0; $k < count($item) ; $k++) { 
										if($k > 1){
											$otherStr.=" ".$item[$k];
										}
									}
									$formattedString =$this->form_echolang([$formattedString_en.$midday." ".$otherStr,$formattedString_zh.$otherStr,$formattedString_sc." ".$otherStr]);
							}else{
								return '<div class="code_error">seminar date format error</div>';
							}
							$output .= '<option data-shop="' . $item[0] . '" value="' . $item[1] . '" disabled>' . $formattedString . '</option>';
							// $output .= '<option value="' . $paraArr['seminar_date'][$i] . '" disabled>' . $paraArr['seminar_date'][$i] . '</option>';
						}
					$output .= '
					</select>
				</div>';
			}
			//**** (END) Health Talk

			//**** Health Talk participant
			if($has_participant_bool){
				$output .= '
				<div class="form_row" data-ech-field="participant">
					<input type="number" name="participant" placeholder="*'.$this->form_echolang(['Participant','人數','人数']).'" class="form-control" min="1" max="10" required>
				</div>';
			}
			//**** (END) Health Talk participant
			



			//**** Item Options
			$styleStr = (count($paraArr['item']) == 1)?'style="display:none;"':'';
			$output .= '
			<div class="form_row" data-ech-field="item" '.$styleStr.'>';

				if (count($paraArr['item']) == 1) {
					$output .= '<div>' . $item_label . '</div>';
					$output .= '<label class="checkbox_label"><input type="checkbox" value="' . $paraArr['item_code'][0] . '" name="item" data-text-value="' . $paraArr['item'][0] . '" checked onclick="return false;">' . $paraArr['item'][0] . '</label>';
				} else if (count($paraArr['item']) < 7) {
					$output .= '<div>' . $item_label . '</div>';
					for ($i = 0; $i < count($paraArr['item']); $i++) {
						$output .= '<label class="checkbox_label"><input type="checkbox" class="';
						if ($limited_bool) {
							$output .= 'limited_checkbox';
						} else {
							$output .= '';
						}
						$output .= '" value="' . $paraArr['item_code'][$i] . '" name="item" data-text-value="' . $paraArr['item'][$i] . '">' . $paraArr['item'][$i] . '</label><br>';
					}
				} else {
					// dropdown list checkbox 
					$output .= '
					<div class="lfg_checkbox_dropdown">
						<div class="lfg_dropdown_title">' . $item_label . '</div>
							<ul class="lfg_checkbox_dropdown_list">';
								for ($i = 0; $i < count($paraArr['item']); $i++) {
									$output .= '
									<li>
										<label class="checkbox_label">
											<input type="checkbox" class="';
											if ($limited_bool) {
												$output .= 'limited_checkbox';
											} else {
												$output .= '';
											}
											$output .= '" value="' . $paraArr['item_code'][$i] . '" name="item" data-text-value="' . $paraArr['item'][$i] . '">' . $paraArr['item'][$i] . '</label>
									</li>';
								} // for loop
							$output .= '
							</ul>'; //lfg_checkbox_dropdown_list
						$output .= '
					</div>'; // lfg_checkbox_dropdown

				} // count($paraArr['item'])
			$output .= '
			</div> <!-- form_row -->';
			//**** (END) Item Options
			if(!empty($extra_radio_remark)){
				for ($i=0; $i < count($extra_radio_remark); $i++) { 
					$radio_ary =  array_map('trim', str_getcsv($extra_radio_remark[$i], '|'));
					$question = $radio_ary[0];
					$radio_item_1 = $radio_ary[1];
					$radio_item_2 = $radio_ary[2];

					$output .= '
					<div class="form_row" data-ech-field="extra_radio_remark">
						<div class="radio_question">'.$question.'</div>
						<div>
							<input type="radio" class="extra_radio_remark" id="extra_radio_remark_'.$i.'_1" name="extra_radio_remark_'.$i.'" value="'.$radio_item_1.'" checked>
							<label for="extra_radio_remark_'.$i.'_1">'.$radio_item_1.'</label>
						</div>
						<div>
							<input type="radio" class="extra_radio_remark" id="extra_radio_remark_'.$i.'_2" name="extra_radio_remark_'.$i.'" value="'.$radio_item_2.'">
							<label for="extra_radio_remark_'.$i.'_2">'.$radio_item_2.'</label>
						</div>

					</div>';

				}
			}



			//**** TEXTAREA 
			if ($has_textarea_bool) {
				$output .= '
				<div class="form_row" data-ech-field="remarks">
						<textarea class="form-control" type="textarea" name="remarks" id="remarks" placeholder="' . $textarea_label . '" maxlength="140" rows="7"></textarea>
				</div>
				<!-- form_row -->
				';
			}
			//**** (END) TEXTAREA 



			//**** HOW DID YOU HEAR ABOUT US
			if ($has_hdyhau_bool) {
				$output .= '
				<div class="form_row" data-ech-field="select_hdyhau">
					<select  class="form-control" name="select_hdyhau" id="select_hdyhau" style="width: 100%;" required>
						<option disabled="" selected="" value="">'.$hdyhau_label.'</option>';
						for ($i = 0; $i < count($paraArr['hdyhau_item']); $i++) {
							$output .= '<option value="' . $paraArr['hdyhau_item'][$i] . '">' . $paraArr['hdyhau_item'][$i] . '</option>';
						}
					$output .= '
					</select>
				</div>';
			}
			//**** (END) HOW DID YOU HEAR ABOUT US


			$privacyPolicyUrl = get_option( 'ech_lfg_privacy_policy' );
			$output .= ' 
			<div class="form_row" data-ech-field="info_remark">
				<div class="redWord">'.$this->form_echolang(['Our center will contact you to confirm the details before confirming the reservation.','本中心將與您聯絡確認詳情，方為確實是次預約。','本中心将与您联络确认详情，方为确实是次预约。']).'</div>
				<label><input type="checkbox" class="agree"  value="agreed_policy" name="info_remark[]" checked required > '.$this->form_echolang(['* I have read and agreed with the terms and conditions of <a class="ech-pp-url" href="'.$privacyPolicyUrl.'" target="_blank">Privacy Policy.</a>','*本人已閱讀並同意有關<a class="ech-pp-url" href="'.$privacyPolicyUrl.'" target="_blank">私隱政策聲明</a>','*本人已阅读并同意有关<a class="ech-pp-url" href="'.$privacyPolicyUrl.'" target="_blank">私隐政策声明</a>']).'。</label>
				<div><small>'.$this->form_echolang(['*Required','*必需填寫','*必需填写']).'</small></div>
			</div>';

			if($note_required){
				$output .= '
				<div class="form_row" data-ech-field="note">
					<div>'.$this->form_echolang(['For same day reservation, please <a href="tel:tel:'.$note_phone.'">call</a> or message us on <a class="wtsL" href="'.$note_whatapps_link.'" target="_blank">WhatsApp</a>.','當天預約請<a href="tel:'.$note_phone.'">致電</a>或透過<a class="wtsL" href="'.$note_whatapps_link.'" target="_blank">WhatsApp</a>聯繫我們。','当天预约请<a href="tel:'.$note_phone.'">致电</a>或透过<a class="wtsL" href="'.$note_whatapps_link.'" target="_blank">WhatsApp</a>联系我们。']).'</div>
				</div><!-- form_row -->';
			}
			

			$output .= '
			<div class="form_row" data-ech-btn="submit">
					<button type="submit" id= "submitBtn" >' . $submit_label . '</button>
			</div><!-- form_row -->';

			if($quota_required == '1'){
				date_default_timezone_set('Asia/Taipei');
				$currentHour = date('H');
				$quota="1";
				$quota_str="";
				if ($currentHour >= 6 && $currentHour < 13) {
						$quota="7";
				} elseif ($currentHour >= 13 && $currentHour < 18) {
						$quota="4";
				} elseif ($currentHour >= 18) {
						$quota="3";
				}
				$string_zh ="<h6>預約名額，尚餘 <b>".$quota."</b> 個​</h6><h6>由於預約眾多，名額設限</h6><h6>不便之處，敬請原諒</h6>";
				$string_en ="<h6>Only <b>".$quota."</b> slots remaining for booking</h6><h6>Due to high demand,<br>limited slots are available</h6><h6>We apologize for any inconvenience</h6>";
				$string_sc ="<h6>预约名额，尚余<b>".$quota."</b> 个​</h6><h6>由于预约众多，名额设限</h6><h6>不便之处，敬请原谅</ h6>";
				$quota_str = $this->form_echolang([$string_en,$string_zh,$string_sc]);
				$output .= '<div class="form_row" data-ech-field="quota">'. $quota_str .'</div><!-- form_row -->';
			}
		$output .= '
		</form>';
		return $output;
	} // function display_ech_lfg()




	public function lfg_formToMSP() {

		$crData = array();
		$crData['token'] = $_POST['token'] ?? '';
		$crData['source'] = $_POST['source'] ?? '';
		$crData['name'] = $_POST['name'] ?? '';
		$crData['user_ip'] = $_POST['user_ip'] ?? '';
		$crData['website_name'] = $_POST['website_name'] ?? '';
		$crData['website_url'] = $_POST['website_url'] ?? '';
		$crData['enquiry_item'] = $_POST['enquiry_item'] ?? '';
		$crData['tel_prefix'] =	$_POST['tel_prefix'] ?? '';
		$crData['tel'] = $_POST['tel'] ?? '';
		$crData['email'] = $_POST['email'] ?? '';
		$crData['age_group'] =	$_POST['age_group'] ?? '';
		$crData['shop_area_code'] = $_POST['shop_area_code'] ?? '';
		$crData['booking_date'] = $_POST['booking_date'] ?? '';
		$crData['booking_time'] = $_POST['booking_time'] ?? '';
		$crData['remarks'] = $_POST['remarks'] ?? '';
		

		if (get_option('ech_lfg_apply_test_msp') == "1") {
			// connect to DEV MSP API
			$result	= $this->lfg_curl('https://msp-dev.echealthcare.com/api/third_party_service/Offical/submitEnquiryForm', $crData, true);
		} else {
			// connect to LIVE MSP API
			$result	= $this->lfg_curl('https://msp.prohaba.com:8003/api/third_party_service/Offical/submitEnquiryForm', $crData, true);
		}
		

		echo $result; // return json format
		wp_die(); // prevent ajax return 0
	}


	public function lfg_recaptVerify() {
		$crData = array();
		$crData['response'] = $_POST['recapt_token'];
		$crData['secret'] = get_option('ech_lfg_recapt_secret_key');

		$result	= $this->lfg_curl('https://www.google.com/recaptcha/api/siteverify', $crData, true);
		echo $result;
		wp_die();
	}



	/****************************************************
	 * For generate epay ref code that pass to MSP and 
	 * epay landing url 
	 ****************************************************/
	public function genRandomString($length = 5) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[random_int(0, $charactersLength - 1)];
		}
		return $randomString;
	}


	private function lfg_curl($i_url, $i_fields = null, $i_isPOST = 0) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $i_url);
		curl_setopt($ch, CURLOPT_POST, $i_isPOST);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ($i_fields != null && is_array($i_fields))
		{
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($i_fields));
		}
		$rs = curl_exec($ch);
		curl_close($ch);
	
		return $rs;
	}

	public function form_echolang($stringArr) {
		global $TRP_LANGUAGE;

		switch ($TRP_LANGUAGE) {
			case 'zh_HK':
				$langString = $stringArr[1];
				break;
			case 'zh_CN':
				$langString = $stringArr[2];
				break;
			default:
				$langString = $stringArr[0];
		}

		if(empty($langString) || $langString == '' || $langString == null) {
			$langString = $stringArr[1]; //zh_HK
		}

		return $langString;

	}


	


}