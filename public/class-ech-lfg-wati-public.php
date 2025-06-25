<?php 

class Ech_Lfg_Wati_Public
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


    public function lfg_WatiSendMsg() {
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
        
        //$epayData = urlencode(serialize($epayData));
        //$epayData = urlencode(json_encode($epayData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        // $epayData = urlencode(json_encode($epayData, JSON_UNESCAPED_SLASHES));
        $epayData = $this->encrypted_epay($epayData);


        $data['template_name'] = $_POST['wati_msg'];
        $epayParam = '';
        if(strpos($data['template_name'],"epay") !== false ){
            $epayParam = $epayData;
        }
        $data['broadcast_name'] = "string";
        $data['parameters'] = array( 
                                [ "name" => "name", "value" => $_POST['name'] ], 
                                [ "name" => "phone", "value" => $_POST['phone'] ], 
                                [ "name" => "email", "value" => $_POST['email'] ], 
                                [ "name" => "booking_date", "value" => $_POST['booking_date'] ], 
                                [ "name" => "booking_time", "value" => $_POST['booking_time'] ], 
                                [ "name" => "booking_item", "value" => $_POST['booking_item'] ], 
                                [ "name" => "booking_location", "value" => $_POST['booking_location'] ], 
                                [ "name" => "website_url", "value" => $_POST['website_url'] ], 
                                //[ "name" => "epaydata", "value" => http_build_query( array( 'param' => $epayData)) ], 
                                [ "name" => "epaydata", "value" => $epayData ], 
                            );

        $result	= $this->lfg_wati_curl("/api/v1/sendTemplateMessage?whatsappNumber=".$_POST['phone'], $data);
        echo json_encode(['result' => $result,'epayParam' => $epayParam]);
        wp_die();
    }


    /*********************************
     * WATI add Contact
     *********************************/
    /* public function lfg_WatiAddContact() {
        $data = array();

        $data['name'] = $_POST['name'];
        $data['customParams'] = array(
                                [ "name" => "email", "value" => $_POST['email'] ],
                                [ "name" => "source_url", "value" => $_POST['website_url'] ],
                                [ "name" => "tcode", "value" => $_POST['r'] ],
                            );
        
        $result = $this->lfg_wati_curl("/api/v1/addContact/" . $_POST['phone'], $data);
        echo $result;
        wp_die();
    } */




    private function lfg_wati_curl($api_link, $dataArr = null) {
        $ch = curl_init();

        $api_domain = get_option( 'ech_lfg_wati_api_domain' );

        curl_setopt($ch, CURLOPT_URL, $api_domain . $api_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Accept: */*';
        $headers[] = 'Authorization: '. get_option( 'ech_lfg_wati_key' );        
        $headers[] = 'Content-Type: application/json-patch+json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataArr) );

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
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



}