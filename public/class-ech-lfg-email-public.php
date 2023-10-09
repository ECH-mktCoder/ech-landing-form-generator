<?php 

class Ech_Lfg_Email_Public
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
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


    public function lfg_emailSend() {

        // verification of the posted email receiver value
		$post_receiver = $_POST['email_receiver'];
		$s = $_SERVER['SERVER_NAME'];
		$post_nonce = wp_create_nonce($post_receiver . $s);

		$protected_nonce = $_POST['lfg_email_nonce'];

		if ( $post_nonce != $protected_nonce ) {
			echo json_encode( array('result' => 0, 'msg'=> 'somethings wrong! Email wont be sent...') );
		} else {
            // send email action
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'Reply-To: ' . get_option( 'ech_lfg_admin_contact_email' )
            );
            $toEmail = $post_receiver;
            $subject = get_option( 'ech_lfg_brand_name' ) . ' Website Form Submission';
            $message = '<p><b>Source:</b> '. $_POST['source'].'</p>
                        <p><b>MSP Token:</b> '. $_POST['msp_token'].'</p>
                        <p><b>Name:</b> '. $_POST['name'].'</p>
                        <p><b>User IP:</b> '. $_POST['user_ip'].'</p>
                        <p><b>Website URL:</b> '. $_POST['website_url'].'</p>
                        <p><b>Booking Item:</b> '. $_POST['booking_item'].'</p>
                        <p><b>Phone:</b> '. $_POST['phone'].'</p>
                        <p><b>Email:</b> '. $_POST['email'].'</p>
                        <p><b>Age Group:</b> '. $_POST['age_group'].'</p>
                        <p><b>Booking Location:</b> '. $_POST['booking_location'].'</p>
                        <p><b>Booking Date:</b> '. $_POST['booking_date'].'</p>
                        <p><b>Booking Time:</b> '. $_POST['booking_time'].'</p>
                        <p><b>Remarks:</b> '. $_POST['remarks'].'</p>
                    '; 



            // send email
            $isEmailSent = wp_mail( $toEmail, $subject, $message, $headers);


            echo json_encode( array('result'=> 1, 'msg'=> 'Email sent', 'isEmailSent' => $isEmailSent ) );
        } 
        
        wp_die();
        
    } // lfg_emailSend


}
