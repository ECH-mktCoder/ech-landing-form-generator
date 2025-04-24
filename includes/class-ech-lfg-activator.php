<?php

/**
 * Fired during plugin activation
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/includes
 * @author     Toby Wong <tobywong@prohaba.com>
 */
class Ech_Lfg_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$getApplyTestMSP = get_option( 'ech_lfg_apply_test_msp' );
		if(empty($getApplyTestMSP) || !$getApplyTestMSP ) {
			add_option( 'ech_lfg_apply_test_msp', 0 );
		}

		$getApplyRecapt = get_option( 'ech_lfg_apply_recapt' );   
		if(empty($getApplyRecapt) || !$getApplyRecapt ) {
			add_option( 'ech_lfg_apply_recapt', 0 );
		}

		$privacyPolicyUrl = get_option( 'ech_lfg_privacy_policy' );   
		if(empty($privacyPolicyUrl) || !$privacyPolicyUrl ) {
			add_option( 'ech_lfg_privacy_policy', 'https://www.echealthcare.com/zh/privacy-policy/' );
		}

		$getAcceptPll = get_option( 'ech_lfg_accept_pll' );   
		if(empty($getAcceptPll)) {
			add_option( 'ech_lfg_accept_pll', 1);
		}
	}

}
