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
        $objectKey = $_POST['wati_msg'];
        $phone = preg_replace('/\D/', '', $_POST['phone']);
        $email = $_POST['email'];

        $customer_id = $this->get_sleekflow_contact_id($phone);

        if(!$customer_id){

            $customer_id = create_sleekflow_contact($phone, $email);

            if (is_array($customer_id) && isset($customer_id['error'])) {
                echo json_encode([
                    'success' => false,
                    'message' => '無法建立 SleekFlow 聯絡人',
                    'error' => $customer_id['error'],
                    'api_response' => $customer_id['response'] ?? null
                ]);
                wp_die();
            }
        }

        $data = [
            // 'primaryPropertyValue' => null,
            'propertyValues' => [
                'client_name' => $_POST['name'],
                'booking_location' => $_POST['booking_location'],
                'booking_item' => $_POST['booking_item']
                
            ],
            'referencedUserProfileId' => $customer_id
        ];

        $result	= $this->lfg_sleekflow_curl($objectKey, $data);
        echo $result;
        wp_die();
    }

    private function lfg_sleekflow_curl($objectKey, $dataArr = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.sleekflow.io/api/customObjects/'.$objectKey.'/records');
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

    private function get_sleekflow_contact_id($phone) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sleekflow.io/api/contact?limit=1&offset=0&phoneNumber='.$phone);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , 'GET');

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Sleekflow-Api-Key: '. get_option( 'ech_lfg_sleekflow_token' );        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (!empty($data) && isset($data[0]['id'])) {
            return $data[0]['id'];
        }

        return null;
    }

    private function create_sleekflow_contact($phone, $email) {
        $ch = curl_init();
        $data = json_encode([
            'phoneNumber' => $phone,
            'email' => $email
        ]);
    
        curl_setopt($ch, CURLOPT_URL, 'https://api.sleekflow.io/api/contact/addOrUpdate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Sleekflow-Api-Key: '. get_option( 'ech_lfg_sleekflow_token' );        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        if (!empty($data) && isset($data[0]['id'])) {
            return $data[0]['id'];
        } else {
            return ['error' => 'Failed to create or update contact', 'response' => $response];
        }

    }

}