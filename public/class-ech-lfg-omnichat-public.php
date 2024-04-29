<?php 

class Ech_Lfg_Omnichat_Public
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


    public function lfg_OmnichatSendMsg() {
        $domain = get_site_url();
        $data = array();
        $epayData = array(
                        "username" => $_POST['name'], 
                        "phone" => $_POST['phone'], 
                        "email" => $_POST['email'], 
                        "booking_date" => $_POST['booking_date'],
                        "booking_time" => $_POST['booking_time'],
                        "booking_item" => $_POST['booking_item'],
                        "booking_location"=>$_POST['booking_location'],    
                        "website_url" => $_POST['website_url'],
                        "epay_refcode" => $_POST['epayRefCode']
                    );
        
        $epayData = urlencode(json_encode($epayData, JSON_UNESCAPED_SLASHES));
        
        $data['trackId'] = $_POST['wati_msg'];
        $data['platform'] = "whatsapp";
        $data['channelId'] = get_option( 'ech_lfg_brand_whatsapp' );
        $data['to'] = $_POST['phone'];
        $data['tags'] = [$_POST['wati_msg']];

        $buttonComponent= [];
        if(strpos($_POST['wati_msg'],"epay") !== false ){
            $buttonComponent = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0',
                'parameters' => [
                    ['type' => 'text','text' => $domain.'/epay-landing/?epay='.$epayData]
                ]
            ];
        }
        if(!empty($buttonComponent)){
            $messages = [
                'type' => 'whatsappTemplate',
                'whatsappTemplate' => [
                    'name' => $_POST['wati_msg'],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $_POST['booking_date']],
                                ['type' => 'text', 'text' => $_POST['booking_time']],
                                ['type' => 'text', 'text' => $_POST['booking_location']]
                            ]
                        ],
                        $buttonComponent
                    ]
                ]
            ];
        }else{
            $messages = [
                'type' => 'whatsappTemplate',
                'whatsappTemplate' => [
                    'name' => $_POST['wati_msg'],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $_POST['name']],
                                ['type' => 'text', 'text' => $_POST['booking_location']],
                                ['type' => 'text', 'text' => $_POST['booking_item']]
                            ]
                        ],
                    ]
                ]
                
            ];
        }
        $data['messages'] = [$messages];

        $result	= $this->lfg_omnichat_curl("https://open-api.omnichat.ai/v1/direct-messages", $data);
        echo $result;
        wp_die();
    }

    private function lfg_omnichat_curl($api_link, $dataArr = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Accept: */*';
        $headers[] = 'Authorization: '. get_option( 'ech_lfg_omnichat_token' );        
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataArr) );

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
	}



}