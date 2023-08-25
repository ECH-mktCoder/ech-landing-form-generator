<?php 

class Ech_Lfg_Fb_Capi_Public
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


  public function lfg_FBCapi() {

		$current_page = $_POST['website_url'];
		$user_ip = $_POST['user_ip'];
		$user_agent = $_POST['user_agent'];
		$user_email = $_POST['user_email'];
		$user_phone = $_POST['user_phone'];
		$user_fn = $_POST['user_fn'];
		$user_ln = $_POST['user_ln'];
	
		$param_data1 = '{
										"data": [
												{
														"event_name": "Lead",
														"event_time": '.time().',
														"action_source": "website",
														"event_source_url": "'.$current_page.'",
														"user_data": {
																"client_ip_address": "'.$user_ip.'",
																"client_user_agent": "'.$user_agent.'",
																"em": ["'.hash('sha256', $user_email).'"],
																"ph": ["'.hash('sha256', $user_phone).'"],
																"fn": ["'.hash('sha256', $user_fn).'"],
																"ln": ["'.hash('sha256', $user_ln).'"]
														}
												}
										]
								}'; //param_data1

		$param_data2 = '{
				"data": [
						{
								"event_name": "Purchase",
								"event_time": '.time().',
								"action_source": "website",
								"event_source_url": "'.$current_page.'",
								"custom_data":{
									"content_name": "lead",
									"currency": "HKD",
									"value": "0"
								},
								"user_data": {
										"client_ip_address": "'.$user_ip.'",
										"client_user_agent": "'.$user_agent.'",
										"em": ["'.hash('sha256', $user_email).'"],
										"ph": ["'.hash('sha256', $user_phone).'"],
										"fn": ["'.hash('sha256', $user_fn).'"],
										"ln": ["'.hash('sha256', $user_ln).'"]
								}
						}
				]
		}'; //param_data2

		if(!empty($user_phone)) {
			$lead	= $this->fb_curl($param_data1);
			$purchase	= $this->fb_curl($param_data2);

			$result_ary = array(
				'lead' => json_decode($lead),
				'purchase' => json_decode($purchase)
			);

			$result = json_encode($result_ary);
			echo $result;

		} else {
			echo '0';
		}  
		
		wp_die();
	}

	public function FB_capi_wtsapp_btn_click() {

		$current_page = $_POST['website_url'];
		// $user_ip = $_POST['user_ip'];
		$user_ip = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_POST['user_agent'];
		$param_data1 = '{
				"data": [
						{
								"event_name": "Whatsapp_button",
								"event_time": '.time().',
								"action_source": "website",
								"event_source_url": "'.$current_page.'",
								"user_data": {
										"client_ip_address": "'.$user_ip.'",
										"client_user_agent": "'.$user_agent.'"
								}
						}
				]
		}'; //param_data1

		$param_data2 = '{
				"data": [
						{
								"event_name": "Purchase",
								"event_time": '.time().',
								"action_source": "website",
								"event_source_url": "'.$current_page.'",
								"custom_data":{
                    "content_name": "whatsapp",
                    "currency": "HKD",
                    "value": "0"
                },
								"user_data": {
										"client_ip_address": "'.$user_ip.'",
										"client_user_agent": "'.$user_agent.'"
								}
						}
				]
		}'; //param_data2
		$wts	= $this->fb_curl($param_data1);
		$purchase	= $this->fb_curl($param_data2);

		$result_ary = array(
			'wts' => json_decode($wts),
			'purchase' => json_decode($purchase)
		);
		$result = json_encode($result_ary);
		echo $result;

		wp_die();
	
	}


	private function fb_curl($param_data) {
    $ch = curl_init();

		$fbAPI_version = "v11.0";
		$pixel_id = get_option( 'ech_lfg_pixel_id' );
		$fb_access_token= get_option( 'ech_lfg_fb_access_token' );
		$fb_graph_link = "https://graph.facebook.com/".$fbAPI_version."/".$pixel_id."/events?access_token=".$fb_access_token;

    $headers = array(
        "content-type: application/json",
    );

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $fb_graph_link);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
	}



}