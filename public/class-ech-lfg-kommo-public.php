<?php


class Ech_Lfg_Kommo_Public
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
     * The subdomain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $subdomain    The current subdomain of this plugin.
     */
    private $subdomain = 'csisc';

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

    public function lfg_KommoSendMsg()
    {
        $phone = preg_replace('/\D/', '', $_POST['phone']);

        $contact_id = $this->get_kommo_contact_by_phone($phone);
        $customer_data = [];
        if (!$contact_id) {
            $customer_data = [
                'name' => $_POST['name'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $phone,
            ];
            $contact_data = $this->create_kommo_contact($customer_data);
            if (isset($contact_data['error']) && $contact_data['error'] === true) {
                echo json_encode([
                    'error' => true,
                    'message' => $contact_data['message'],
                    'details' => $contact_data['kommo_response'],
                ]);
                wp_die();
            }
            $contact_id = $contact_data['_embedded']['contacts'][0]['id'];

        }
        $lead_data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $phone,
            'team_code' => $_POST['team_code'],
            'booking_date' => $_POST['booking_date'],
            'booking_time' => $_POST['booking_time'],
            'booking_item' => $_POST['booking_item'],
            'booking_location' => $_POST['booking_location'],
            'remarks' => $_POST['remarks'],
            'website_url' => $_POST['website_url'],
            'msg_template' => $_POST['wati_msg'],
        ];
        $epayParam = '';
        if(!empty($_POST['wati_msg']) && stripos($_POST['wati_msg'],"epay") !== false ){
            $epayData = array(
                "username" => $_POST['name'], 
                "phone" => $phone, 
                "email" => $_POST['email'], 
                "booking_date" => $_POST['booking_date'],
                "booking_time" => $_POST['booking_time'],
                "booking_item" => $_POST['booking_item'],
                "booking_location"=>$_POST['booking_location'],    
                "website_url" => $_POST['website_url'],
                "epay_refcode" => $_POST['epayRefCode']
            );
            $epayData = $this->encrypted_epay($epayData);
            $epayParam = $epayData;
            $lead_data['epay_url'] = $_POST['msg_button'].'?epay='.$epayData;
        }else{
            $lead_data['epay_url'] = $_POST['msg_button'];
        }
        
        $create_lead = $this->create_lead($contact_id, $lead_data);
        if (isset($create_lead['error']) && $create_lead['error'] === true) {
            echo json_encode([
                'error' => true,
                'message' => $create_lead['message'],
                'details' => $create_lead['kommo_response'],
            ]);
            wp_die();
        }
        echo json_encode(['result' => $create_lead,'epayParam' => $epayParam,'lead_data' => $lead_data,'post'=>$_POST]);
        wp_die();

    }

    private function get_kommo_contact_by_phone($phone_number)
    {
        $contact_api = "https://{$this->subdomain}.kommo.com/api/v4/contacts?query=" . urlencode($phone_number);
        $result = json_decode($this->lfg_kommo_curl($contact_api, null, 'GET'), true);
        $contact_id = '';
        if (!empty($result) && $result['_embedded']['contacts'][0]['id']) {
            $contact_id = $result['_embedded']['contacts'][0]['id'];
        }
        return $contact_id;
    }
    private function get_kommo_contact_by_id($contact_id)
    {
        $contact_api = "https://{$this->subdomain}.kommo.com/api/v4/contacts/{$contact_id}";
        $result = json_decode($this->lfg_kommo_curl($contact_api, null, 'GET'), true);
        $contact = [];
        if (!empty($result) && isset($result['_embedded'])) {
            $contact = $result['_embedded'];
        }
        return $contact;
    }

    private function create_kommo_contact($customer_data)
    {

        // $phone_field_id = null;
        // $email_field_id = null;

        // $fields = $this->get_kommo_entity_custom_fields('contacts');
        // if (!empty($fields)) {
        //     foreach ($fields as $key => $field) {
        //         if ($field['code'] === 'PHONE') {
        //             $phone_field_id = $field['id'];
        //         }
        //         if ($field['code'] === 'EMAIL') {
        //             $email_field_id = $field['id'];
        //         }
        //     }
        // }

        $contact_data = [[
            'name' => $customer_data['name'],
            'first_name' => $customer_data['first_name'],
            'last_name' => $customer_data['last_name'],
            'custom_fields_values' => [
                [
                    'field_code' => 'PHONE',
                    'values' => [['value' => $customer_data['phone']]],
                ],
                [
                    'field_code' => 'EMAIL',
                    'values' => [['value' => $customer_data['email']]],
                ],
            ],
        ]];
        //create contact
        $contact_api = "https://{$this->subdomain}.kommo.com/api/v4/contacts";
        $create_response = json_decode($this->lfg_kommo_curl($contact_api, $contact_data), true);
        if (isset($create_response['_embedded']['contacts'][0]['id'])) {
            return $create_response;
        } else {
            return [
                'error' => true,
                'message' => 'Failed to create Kommo contact.',
                'kommo_response' => $create_response,
            ];
        }
    }

    private function create_lead($contact_id, $lead_data)
    {
        $lead_api = "https://{$this->subdomain}.kommo.com/api/v4/leads";

        $fields = $this->get_kommo_entity_custom_fields('leads');


        $custom_fields = [];
        // 從 website_url 取得 UTM 和其他參數
        $query_params = [];
        if (!empty($lead_data['website_url'])) {
            $url_parts = parse_url($lead_data['website_url']);
            if (isset($url_parts['query'])) {
                parse_str($url_parts['query'], $query_params);
            }
        }
        foreach ($fields as $field) {

            if ($field['type'] === 'tracking_data' && isset($field['code'])) {
                $tracking_code = strtolower($field['code']);

                if (isset($query_params[$tracking_code])) {
                    $custom_fields[] = [
                        'field_code' => $field['code'],
                        'values' => [
                            ['value' => $query_params[$tracking_code]],
                        ],
                    ];
                }
            }
        }

        $datetime = new DateTime($lead_data['booking_date'] . ' ' . $lead_data['booking_time'], new DateTimeZone('Asia/Hong_Kong'));
        $booking_timestamp = $datetime->getTimestamp(); // Unix Timestamp

        $custom_fields[] = [
            'field_code' => 'LF_NAME',
            'values' => [
                ['value' => $lead_data['name']],
            ],
        ];
        $custom_fields[] = [
            'field_code' => 'LF_EAMIL',
            'values' => [
                ['value' => $lead_data['email']],
            ],
        ];
        $custom_fields[] = [
            'field_code' => 'LF_PHONE',
            'values' => [
                ['value' => $lead_data['phone']],
            ],
        ];
        $custom_fields[] = [
            'field_code' => 'LF_CHANNEL', // Channel
            'values' => [
                ['value' => 'Campaign (online landing forms)'],
            ],
        ];
        $custom_fields[] = [
            'field_code' => 'LF_TEAM_CODE',
            'values' => [
                ['value' => $lead_data['team_code']],
            ],
        ];
        $custom_fields[] = [
            'field_code' => 'LF_BOOKING_ITEM',
            'values' => [
                ['value' => $lead_data['booking_item']],
            ],
        ];
        $custom_fields[] = [
            'field_code' => 'LF_BOOKING_LOCATION',
            'values' => [
                ['value' => $lead_data['booking_location']],
            ],
        ];
        $custom_fields[] = [
            'field_code' => 'LF_BOOKING_DATE_TIME',
            'values' => [
                ['value' => $booking_timestamp],
            ],
        ];

        $custom_fields[] = [
            'field_code' => 'LF_REMARKS',
            'values' => [
                ['value' => $lead_data['remarks']],
            ],
        ];

        $custom_fields[] = [
            'field_code' => 'LF_WEBSITE_URL',
            'values' => [
                ['value' => $lead_data['website_url']],
            ],
        ];

        if(!empty($lead_data['epay_url'])){
            $custom_fields[] = [
                'field_code' => 'LF_EPAY_URL',
                'values' => [
                    ['value' => $lead_data['epay_url']],
                ],
            ];
        }

        $custom_fields[] = [
            'field_code' => 'LF_MSG_TEMPLATE',
            'values' => [
                ['value' => $lead_data['msg_template']],
            ],
        ];

        $hk_time = new DateTime("now", new DateTimeZone("Asia/Hong_Kong"));
        $lead_data = [[
            "name" => "Lead Form - " . $hk_time->format("Y-m-d H:i:s"),
            "pipeline_id" => intval(get_option('ech_lfg_kommo_pipeline_id')),
            "status_id" => intval(get_option('ech_lfg_kommo_status_id')),
            "_embedded" => [
                "contacts" => [
                    ["id" => $contact_id],
                ],
            ],
            "custom_fields_values" => $custom_fields,
        ]];

        $response = json_decode($this->lfg_kommo_curl($lead_api, $lead_data), true);

        if (isset($response['_embedded']['leads'][0]['id'])) {
            return $response;
        } else {
            return [
                'error' => true,
                'message' => 'Failed to create Kommo lead.',
                'kommo_response' => $response,
            ];
        }
    }

    private function update_kommo_contact($contact_id, $customer_data)
    {
        // $phone_field_id = null;
        // $email_field_id = null;

        // $field_ids = $this->get_kommo_entity_custom_fields('contacts');
        // if (!empty($field_ids)) {
        //     foreach ($fields as $key => $field) {
        //         if ($field['code'] === 'PHONE') {
        //             $phone_field_id = $field['id'];
        //         }
        //         if ($field['code'] === 'EMAIL') {
        //             $email_field_id = $field['id'];
        //         }
        //     }
        // }
        $update_data = [
            'name' => $customer_data['name'],
            'first_name' => $customer_data['first_name'],
            'last_name' => $customer_data['last_name'],
            'custom_fields_values' => [
                [
                    'field_code' => 'PHONE',
                    'values' => [['value' => $customer_data['phone']]],
                ],
                [
                    'field_code' => 'EMAIL',
                    'values' => [['value' => $customer_data['email']]],
                ],
            ],
        ];
        $contact_api = "https://{$this->subdomain}.kommo.com/api/v4/contacts/{$contact_id}";
        $update_contact = json_decode($this->lfg_kommo_curl($contact_api, $update_data, 'PATCH'), true);

        return $update_contact;
    }
    private function lfg_kommo_curl($api_link, $dataArr = null, $method = 'POST')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [];
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'authorization: Bearer ' . get_option('ech_lfg_kommo_token');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataArr));

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

    private function get_kommo_entity_custom_fields($entity)
    {

        $custom_fields_api = "https://{$this->subdomain}.kommo.com/api/v4/{$entity}/custom_fields";
        $fields_response = json_decode($this->lfg_kommo_curl($custom_fields_api, null, 'GET'), true);

        $fields = [];
        foreach ($fields_response['_embedded']['custom_fields'] as $key => $field) {
            $fields[$key] = $field;
        }

        return $fields;
    }

    private function update_lead($embedded_data, $lead_id)
    {
        $lead_api = "https://{$this->subdomain}.kommo.com/api/v4/leads/{$lead_id}";

        $lead_data = [[
            "_embedded" => $embedded_data,
        ]];

        return $this->lfg_kommo_curl($lead_api, $lead_data, 'PATCH');
    }

    private function update_kommo_leads_custom_fields($field_id)
    {

        //check phone/email custom field
        $custom_fields_api = "https://{$this->subdomain}.kommo.com/api/v4/leads/custom_fields/{$field_id}";
        $data = [
            // 'id' => ,
            // 'code' => '',
        ];
        $update_response = json_decode($this->lfg_kommo_curl($custom_fields_api, $data, 'PATCH'), true);


        return $update_response;
    }
    private function create_kommo_leads_custom_fields()
    {
        $custom_fields_api = "https://{$this->subdomain}.kommo.com/api/v4/leads/custom_fields";
        $data = [
            [
                'type' => 'text',
                'name' => 'Name',
                'code' => 'LF_NAME',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'text',
                'name' => 'Email',
                'code' => 'LF_EAMIL',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'text',
                'name' => 'Phone',
                'code' => 'LF_PHONE',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'text',
                'name' => 'Channel',
                'code' => 'LF_CHANNEL',
                'group_id' => 'leads_30151747629892',
                'is_api_only' => true,
            ],
            [
                'type' => 'textarea',
                'name' => 'Enquiry item',
                'code' => 'LF_BOOKING_ITEM',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'date_time',
                'name' => 'Booking Date & Time',
                'code' => 'LF_BOOKING_DATE_TIME',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'text',
                'name' => 'Booking Location',
                'code' => 'LF_BOOKING_LOCATION',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'text',
                'name' => 'Remarks',
                'code' => 'LF_REMARKS',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'url',
                'name' => 'Website URL',
                'code' => 'LF_WEBSITE_URL',
                'group_id' => 'leads_30151747629892',
            ],
            [
                'type' => 'url',
                'name' => 'Epay URL',
                'code' => 'LF_EPAY_URL',
                'group_id' => 'leads_30151747629892',
            ],
        ];
        $update_response = json_decode($this->lfg_kommo_curl($custom_fields_api, $data, 'POST'), true);


        return $update_response;
    }

}