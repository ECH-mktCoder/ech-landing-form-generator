(function( $ ) {
	'use strict';
	$(function(){


		/************* GENERAL FORM **************/
		$('#lfg_gen_settings_form').on('submit', function(e){
			e.preventDefault();
			$('.statusMsg').removeClass('error');
			$('.statusMsg').removeClass('updated');

			var statusMsg = '';
			var validStatus = false;

			var brandName = $('#ech_lfg_brand_name').val();

			// form validation
			if( brandName == '') {
				validStatus = false;
				statusMsg += 'Brand name is missing <br>';
			} else {
				validStatus = true;
			}

			// set error status msg
			if ( !validStatus ) {
				$('.statusMsg').html(statusMsg);
				$('.statusMsg').addClass('error');
				return;
			} else {
				$('#lfg_gen_settings_form').attr('action', 'options.php');
				$('#lfg_gen_settings_form')[0].submit();
				// output success msg
				statusMsg += 'Settings updated <br>';
				$('.statusMsg').html(statusMsg);
				$('.statusMsg').addClass('updated');
			}
		});
		/************* (END) GENERAL FORM **************/




		/************* reCAPTCHA FORM **************/
		$('#lfg_recapt_form').on('submit', function(e){
			e.preventDefault();
			$('.statusMsg').removeClass('error');
			$('.statusMsg').removeClass('updated');

			var statusMsg = '';
			var validStatus = false;

			var siteKey = $('#ech_lfg_recapt_site_key').val();
			var secretKey = $('#ech_lfg_recapt_secret_key').val();
			var recaptScore = $('#ech_lfg_recapt_score').val();

			if ( $('#ech_lfg_apply_recapt').val() == "1") {

				// if apply reCAPT, check ... 
				if ( siteKey == '' || secretKey == '' || recaptScore == '' ) {
					validStatus = false;
				} else {
					validStatus = true;
				}

				// set status msg
				if ( $('#ech_lfg_recapt_site_key').val() == '' ) {
					statusMsg += 'reCAPTCHA site key is missing <br>';
				} 
				if ( $('#ech_lfg_recapt_secret_key').val() == '' ) {
					statusMsg += 'reCAPTCHA secret key is missing <br>';
				} 
				if ( $('#ech_lfg_recapt_score').val() == '' ) {
					statusMsg += 'reCAPTCHA score is missing <br>';
				}

				if ( !validStatus ) {
					$('.statusMsg').html(statusMsg);
					$('.statusMsg').addClass('error');
					return;
				} else {
					statusMsg += 'Settings updated <br>';
					$('.statusMsg').html(statusMsg);
					$('.statusMsg').addClass('updated');

					$('#lfg_recapt_form').attr('action', 'options.php');
					$('#lfg_recapt_form')[0].submit();
				}
				
			} else {				
				$('#lfg_recapt_form').attr('action', 'options.php');
				$('#lfg_recapt_form')[0].submit();
				// output success msg
				statusMsg += 'Settings updated <br>';
				$('.statusMsg').html(statusMsg);
				$('.statusMsg').addClass('updated');
			}
		});
		/************* (END)reCAPTCHA FORM **************/



		/************* COPY SAMPLE SHORTCODE **************/
		$('#copyShortcode').click(function(){

			var shortcode = $('#sample_shortcode').text();

			navigator.clipboard.writeText(shortcode).then(
				function(){
					$('#copyMsg').html('');
					$('#copyShortcode').html('Copied !'); 
					setTimeout(function(){
						$('#copyShortcode').html('Copy Shortcode'); 
					}, 3000);
				},
				function() {
					$('#copyMsg').html('Unable to copy, try again ...');
				}
			);
		});
		/************* (END)COPY SAMPLE SHORTCODE **************/



	}); // doc ready

})( jQuery );
