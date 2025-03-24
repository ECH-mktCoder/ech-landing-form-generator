<?php 

class Ech_Lfg_Sleekflow_Public
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


    public function lfg_SleekflowSendMsg() {
        $data = array();
        $epayData = array(
                        "username" => $_POST['name'], 
                        "phone" => preg_replace('/\D/', '', $_POST['phone']), 
                        "email" => $_POST['email'], 
                        "booking_date" => $_POST['booking_date'],
                        "booking_time" => $_POST['booking_time'],
                        "booking_item" => $_POST['booking_item'],
                        "booking_location"=>$_POST['booking_location'],    
                        "website_url" => $_POST['website_url'],
                        "epay_refcode" => $_POST['epayRefCode']
                    );
        $epayData = $this->encrypted_epay($epayData);

        $data['channel'] = "whatsappcloudapi";
        $data['from'] = get_option( 'ech_lfg_brand_whatsapp' );
        $data['to'] = preg_replace('/\D/', '', $_POST['phone']);
        $data['messageType'] = "template";
        $components = [];

        $msg_header = '';
        $media_type=['image','video','document'];
        $headerComponent = [];
        if(isset($_POST['msg_header']) && !empty($_POST['msg_header'])){
            $msg_header = $_POST['msg_header'];
            $type = explode('|',$msg_header)[0];
            $content = explode('|',$msg_header)[1];
            if(in_array($type,$media_type)){
                $headerComponent = [
                    'type' => 'header',
                    'parameters' => [
                                        [
                                            'type' => $type,
                                            $type => ['link' => $content],
                                            'filename' => 'filename'
                        ]
                    ]
                ];
            }else{
                $headerComponent = [
                    'type' => 'header',
                    'parameters' => [
                        ['type' => $type,'text' => $content]
                    ]
                ];
            }
            array_push($components,$headerComponent);
        }

        $msg_body = '';
        $bodyComponent= [
            'type' => 'body',
            'parameters' => []
        ];

        if(isset($_POST['msg_body']) && !empty($_POST['msg_body'])){
            $msg_body = $_POST['msg_body'];
            foreach (explode(',',$msg_body) as $value) {
                $temp = ['type' => 'text', 'text' => $_POST[$value]];
                array_push($bodyComponent['parameters'],$temp);
            }
        }else{
            if(strpos($_POST['wati_msg'],"epay") !== false ){
                $bodyComponent = [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => $_POST['booking_date']],
                        ['type' => 'text', 'text' => $_POST['booking_time']],
                        ['type' => 'text', 'text' => $_POST['booking_location']],
                    ]
                ];
            }else{
                $bodyComponent = [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => $_POST['name']],
                        ['type' => 'text', 'text' => $_POST['booking_location']],
                        ['type' => 'text', 'text' => $_POST['booking_item']],
                    ]
                ];
            }
        }
        array_push($components,$bodyComponent);

        $msg_button = '';
        if(isset($_POST['msg_button']) && !empty($_POST['msg_button'])){
            $msg_button = $_POST['msg_button'];
            if(strpos($_POST['wati_msg'],"epay") !== false ){
                
                $buttonComponent = [
                    'type' => 'button',
                    'sub_type' => 'URL',
                    'index' => '0',
                    'parameters' => [
                        ['type' => 'text','text' => "?epay=".$epayData]
                    ]
                ];
                array_push($components,$buttonComponent);
            }else{
                foreach (explode(',',$msg_button) as $key => $value) {
                    $temp = [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => $key,
                        'parameters' => [
                            ['type' => 'text','text' => $value]
                        ]
                    ];
                    array_push($components,$temp);
                }
            }
        }

        $data['extendedMessage'] = [
            'WhatsappCloudApiTemplateMessageObject' => [
                'templateName' => $_POST['wati_msg'],
                'language'=> 'zh_HK',
                'components' => $components
            ]
        ];
        $data['analyticTags'] = [
            "tag1",
            "tag2"
        ];

        $result	= $this->lfg_sleekflow_curl("https://api.sleekflow.io/api/message/send/json", $data);
        echo $result;
        wp_die();
    }

    private function encrypted_epay($epayData){
        $secretKey = get_option( 'ech_lfg_epay_secret_key' );

        $jsonString = json_encode($epayData);
        $compressedData = gzcompress($jsonString);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedData = openssl_encrypt($compressedData, 'aes-256-cbc', $secretKey, 0, $iv);
        $encryptedPayload = base64_encode($encryptedData . "::" . base64_encode($iv));

        return $encryptedPayload;
    }
    private function lfg_sleekflow_curl($api_link, $dataArr = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Sleekflow-Api-Key: '. get_option( 'ech_lfg_sleekflow_token' );        
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