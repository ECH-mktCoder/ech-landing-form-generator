(function( $ ) {
	'use strict';


	$(function(){
		/*********** Checkbox limitation ***********/
		var limit = jQuery('.ech_lfg_form').data("limited-no");
		jQuery('.ech_lfg_form input.limited_checkbox').on('change', function(evt) {
		   if(jQuery(".ech_lfg_form input.limited_checkbox[name='item']:checked").length > limit) {
			   this.checked = false;
		   }
		});
		/*********** (END) Checkbox limitation ***********/
	
		/*********** Datepicker & Timepicker ***********/
		jQuery('.lfg_timepicker').timepicker({'minTime': '11:00am','maxTime': '7:30pm'});
		jQuery( ".lfg_datepicker" ).datepicker({ 
			beforeShowDay: nosunday, 
			dateFormat: 'yy-mm-dd', 
			minDate: 1 // T+1
		});
		jQuery('#ui-datepicker-div').addClass('skiptranslate notranslate');
		/*********** (END) Datepicker & Timepicker ***********/
	
	
		/*********** Checkbox dropdown list ***********/
		if (jQuery(".lfg_checkbox_dropdown")[0]) { // if class exist
	
			jQuery(".lfg_checkbox_dropdown").click(function () {
				jQuery(this).toggleClass("lfg_is_active");
			});
			
			jQuery(".lfg_checkbox_dropdown ul").click(function(e) {
				e.stopPropagation();
			});
	
			
			
			jQuery('.ech_lfg_form input.limited_checkbox').on('change', function(evt) {
				var tempArr = [];
				jQuery(".ech_lfg_form input.limited_checkbox[name='item']:checked").each(function(){
					var itemText = jQuery(this).parent().text();
					tempArr[tempArr.length] = itemText;
				});
				//console.log(tempArr);
				if(tempArr.length == 0) {
					var item_label = jQuery("#ech_lfg_form").data("item-label");
					jQuery(".lfg_dropdown_title").html(item_label);
				} else {
					jQuery(".lfg_dropdown_title").html(tempArr.join());
				}
				
	
				// auto scroll up
				if(jQuery(".ech_lfg_form input.limited_checkbox[name='item']:checked").length == limit) {
					jQuery(".lfg_checkbox_dropdown").toggleClass("lfg_is_active");
				}
			});
	
		} 
		/*********** (END) Checkbox dropdown list ***********/
		
	
		/*********** Form Submit ***********/
		jQuery('.ech_lfg_form').on("submit", function(e){
			e.preventDefault();
	
			var r = jQuery(this).data("r");
			var c_token = jQuery(this).data("c-token");
			var ip = jQuery(this).data("ip");
			var url = jQuery(this).data("url");
			var ajaxurl = jQuery(this).data("ajaxurl");
			var tks_para = jQuery(this).data("tks-para");
			var shop_count = jQuery(this).data("shop-count");
			var brand = jQuery(this).data("brand");
			var has_textarea = jQuery(this).data("has-textarea");
			var has_select_dr = jQuery(this).data("has-select-dr");
			var has_hdyhau = jQuery(this).data("has-hdyhau");
			var has_wati_send = jQuery(this).data("wati-send");
			
	
			var items = [];
			jQuery.each(jQuery(this).find("input[name='item']:checked"), function(){
				items.push(jQuery(this).val());
			});
	
			if(has_select_dr == 1) {
				var _selectDr = jQuery(this).find("select[name='select_dr']").val();
				items.push(_selectDr);
			}
	
			var _name = jQuery(this).find("input[name='last_name']").val() + " " + jQuery(this).find("input[name='first_name']").val(),
				_user_ip = ip,
				_source = r,
				_token = c_token,
				_website_name = brand,
				_website_url = url,
				_tel_prefix = jQuery(this).find("select[name='telPrefix']").val(),
				_tel = jQuery(this).find("input[name='tel']").val(),
				_email = jQuery(this).find("input[name='email']").val(),
				_age_group = jQuery(this).find("input[name='age']").val(),
				_booking_date = jQuery(this).find(".lfg_datepicker").val(),
				_booking_time = jQuery(this).find(".lfg_timepicker").val(),
				_remarks = "";
	
			if (shop_count <=3){
				var _shop_area_code = jQuery(this).find('input[name=shop]:checked').val();
			} else {
				var _shop_area_code = jQuery(this).find('select[name=shop]').val();
			}
	
			
			if(has_textarea == 1) {
				_remarks += jQuery(this).find("textarea[name='remarks']").val();
			}
	
			if(has_hdyhau == 1) {
				_remarks += " | 途徑得知: " + jQuery(this).find("select[name='select_hdyhau']").val();
			}

			if(has_wati_send == 1) {
				_remarks += " | ePay Ref Code: " + jQuery("#ech_lfg_form").data("epay-refcode");
			}
	
	
			if(( _tel_prefix == "+852" && _tel.length != 8 ) || ( _tel_prefix == "+853" && _tel.length != 8 ) ) {
				jQuery(this).find(".lfg_formMsg").html("+852, +853電話必需8位數字(沒有空格)");
				return false;
			} else if((_tel_prefix == "+86" && _tel.length != 11)) {
				jQuery(this).find(".lfg_formMsg").html("+86電話必需11位數字(沒有空格)");
				return false;
			} else {
				var checked_item_count = jQuery(this).find("input[name='item']:checked").length;
				if( checked_item_count == 0) {
					jQuery(this).find(".lfg_formMsg").html("請選擇咨詢項目");
					return false;
				} else {
					jQuery(".ech_lfg_form button[type=submit]").prop('disabled', true);
					jQuery(this).find(".lfg_formMsg").html("提交中...");
					jQuery(".ech_lfg_form button[type=submit]").html("提交中...");

					// if apply reCAPTCHA
					var applyRecapt = jQuery(this).data("apply-recapt");
					if ( applyRecapt == "1") {
						var recaptSiteKey = jQuery(this).data("recapt-site-key");
						var recaptScore = jQuery(this).data("recapt-score");
						grecaptcha.ready(function() {
							grecaptcha.execute(recaptSiteKey, {action: 'submit'}).then(function(recapt_token) {
								var recaptData = {
									'action': 'lfg_recaptVerify',
									'recapt_token': recapt_token
								};
								$.post(ajaxurl, recaptData, function(recapt_msg) {
									var recaptObj = JSON.parse(recapt_msg);
									if(recaptObj.success && recaptObj.score >= recaptScore) {
										// if recapt success then send to MSP
										lfg_dataSendToMSP(_token, _source, _name, _user_ip, _website_name, _website_url, items, _tel_prefix, _tel, _email, _age_group, _shop_area_code, _booking_date, _booking_time, _remarks, ajaxurl, tks_para);
									}
								});
							}); // grecaptcha.execute.then
						}); //grecaptcha.ready
							
					} else {
						// if recapt is disabled, send to msp
						lfg_dataSendToMSP(_token, _source, _name, _user_ip, _website_name, _website_url, items, _tel_prefix, _tel, _email, _age_group, _shop_area_code, _booking_date, _booking_time, _remarks, ajaxurl, tks_para);

						
					}

				} // checked_item_count
			}//_tel_prefix
		}); // onclick
		/*********** (END) Form Submit ***********/
	});



	function lfg_dataSendToMSP(_token, _source, _name, _user_ip, _website_name, _website_url, items, _tel_prefix, _tel, _email, _age_group, _shop_area_code, _booking_date, _booking_time, _remarks, ajaxurl, tks_para) {
		var data = {'action': 'lfg_formToMSP',
					'token': _token, 
					'source': _source, 
					'name': _name, 
					'user_ip': _user_ip,
					'website_name': _website_name,
					'website_url': _website_url,
					'enquiry_item': items,
					'tel_prefix': _tel_prefix,
					'tel': _tel,
					'email': _email,
					'age_group': _age_group,
					'shop_area_code': _shop_area_code,
					'booking_date': _booking_date,
					'booking_time': _booking_time,
					'remarks': _remarks
				};
	
		jQuery.post(ajaxurl, data, function(msg) {
			var jsonObj = JSON.parse(msg);
			//console.log(jsonObj);
			
			if (jsonObj.result == 0) {
				var origin   = window.location.origin;

				// check if wati pay is enabled
				var wati_send = jQuery("#ech_lfg_form").data("wati-send");				
				if (wati_send == 1) {
					var _phone = _tel_prefix + _tel;
					var _wati_msg = jQuery("#ech_lfg_form").data("wati-msg");
					
					
					var itemsTEXT = [];
					jQuery.each(jQuery("#ech_lfg_form input[name='item']:checked"), function(){
						itemsTEXT.push(jQuery(this).data('text-value'));
					});
					var wati_booking_item = itemsTEXT.join(", ");

					var wati_booking_location = jQuery("#ech_lfg_form input[name='shop']:checked").data("shop-text-value");

					// Wati Send
					lfg_watiSendMsg(_wati_msg, _name, _phone, _email, _booking_date, _booking_time, wati_booking_item, wati_booking_location, _website_url);
				} // if wati enabled 

				// redirect to landing thank you page
				if (tks_para != null) {
					window.location.replace(origin+'/thanks?prod='+tks_para);
				} else {
					window.location.replace(origin+'/thanks');
				}
				

			} else {
				alert("無法提交閣下資料, 請重試");
				location.reload(true);
			} 

		});  // end post ajax
	}


	function lfg_watiSendMsg(_watiMsg, _name, _phone, _email, _booking_date, _booking_time, _booking_item, _booking_location, _website_url) {

		var ajaxurl = jQuery("#ech_lfg_form").data("ajaxurl");
		var _epayRefCode = jQuery("#ech_lfg_form").data("epay-refcode");
		var watiData = {
			'action': 'lfg_WatiSendMsg',
			'wati_msg': _watiMsg,
			'name': _name, 
			'phone': _phone,
			'email': _email,
			'booking_date': _booking_date,
			'booking_time': _booking_time,
			'booking_item': _booking_item,
			'booking_location': _booking_location,
			'website_url': _website_url,
			'epayRefCode': _epayRefCode
		};
		
		jQuery.post(ajaxurl, watiData, function(wati_msg) {
			var watiObj = JSON.parse(wati_msg);
			//console.log(watiObj);
			if (watiObj.result) {
				console.log('wtsapp msg sent');
			} else {
				console.log('wati send error');
			}
			
		}).fail(function(xhr, status, error) {
			// error handling
			console.log("post error - xhr: " + JSON.stringify(xhr) + " status: " + status + " error: " + error)
		});
	} // lfg_watiSendMsg



	function lfg_watiAddContact(_name, _phone, _email, _website_url, _source) {
		var ajaxurl = jQuery("#ech_lfg_form").data("ajaxurl");
		var watiContactData = {
			'action': 'lfg_WatiAddContact',
			'name': _name, 
			'phone': _phone,
			'email': _email,
			'website_url': _website_url,
			'r': _source
		};

		console.log(watiContactData);
		
		jQuery.post(ajaxurl, watiContactData, function(wati_msg) {
			var watiObj = JSON.parse(wati_msg);
			console.log(watiObj);
			if (watiObj.result) {
				console.log('wati contact added');
			} else {
				console.log('wati contact add fail');
			}
			
		}).fail(function(xhr, status, error) {
			// error handling
			console.log("post error - xhr: " + JSON.stringify(xhr) + " status: " + status + " error: " + error)
		});
	} // lfg_watiAddContact

})( jQuery );




function nosunday(date) {
    var day = date.getDay(); 
    return [(day > 0), ''];	
}
	
