<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Ech_Lfg
 * @subpackage Ech_Lfg/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->




<div class="echPlg_wrap">
    <h1>LFG General Settings</h1>

    <div class="lfg_intro">
        <p>To generate a lead form, you need to enter a brand name in the below form first. You may copy the below shortcode sample to start generate a lead form. More shortcode attributes and guidelines, visit <a href="https://github.com/ECH-mktCoder/ech-landing-form-generator" target="_blank">Github</a>. </p>
        <div class="shtcode_container">
            <pre id="sample_shortcode">[ech_lfg default_r_code="t200" r="t575, t575google | t127, T127fb" r_code="TCODETOKEN575, TCODETOKEN127" item="Item 1" item_code="ITEMCODE123" shop="Causeway Bay, Kowloon" shop_code="HK001, KW001"]</pre>
            <div id="copyMsg"></div>
            <button id="copyShortcode">Copy Shortcode</button>
        </div>
    </div>

    <div class="form_container">
        <form method="post" id="lfg_gen_settings_form">
        <?php 
            settings_fields( 'lfg_gen_settings' );
            do_settings_sections( 'lfg_gen_settings' );
        ?>
            <h2>General</h2>
            <div class="form_row">
                <?php 
                    $getApplyTestMSP = get_option( 'ech_lfg_apply_test_msp' );
                    if(empty($getApplyTestMSP) || !$getApplyTestMSP ) {
                        add_option( 'ech_lfg_apply_test_msp', 0 );
                    }
                ?>
                <label>Connect to <strong>testing</strong> MSP API : </label>
                <select name="ech_lfg_apply_test_msp" id="">
                    <option value="0" <?= ($getApplyTestMSP == "0") ? 'selected' : '' ?>> No </option>
                    <option value="1" <?= ($getApplyTestMSP == "1") ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>

            <div class="form_row">
                <label>Brand Name : </label>
                <input type="text" name="ech_lfg_brand_name" value="<?= get_option( 'ech_lfg_brand_name' )?>" id="ech_lfg_brand_name" pattern="[ A-Za-z0-9]{1,}">
            </div>
            
            <div class="form_row">
                <?php 
                    $getAcceptPll = get_option( 'ech_lfg_accept_pll' );
                    if(empty($getAcceptPll)) {
                        add_option( 'ech_lfg_accept_pll', 1 );
                        $getAcceptPll = get_option( 'ech_lfg_accept_pll' );
                    }
                ?>
                <label>接收PII : </label>
                <select name="ech_lfg_accept_pll" id="">
                    <option value="1" <?= ($getAcceptPll == "1") ? 'selected' : '' ?>>ON</option>
                    <option value="0" <?= ($getAcceptPll == "0") ? 'selected' : '' ?>> OFF</option>
                </select>
            </div>


            <h2>Global Styling</h2>

            <h3>Submit button general color</h3>
            <div class="form_row">
                <label>Submit Button Color (HEX code only): </label>
                <input type="text" name="ech_lfg_submitBtn_color" value="<?= htmlspecialchars(get_option( 'ech_lfg_submitBtn_color' ))?>" id="" pattern="^(#)[A-Za-z0-9]{3,6}" id="ech_lfg_submitBtn_color">
            </div>
            <div class="form_row">
                <label>Submit Button Text Color (HEX code only): </label>
                <input type="text" name="ech_lfg_submitBtn_text_color" value="<?= htmlspecialchars(get_option( 'ech_lfg_submitBtn_text_color' ))?>" id="" pattern="^(#)[A-Za-z0-9]{3,6}">
            </div>        
            

            <h3>Submit button hover color</h3>
            <div class="form_row">
                <label>Submit Button Hover Color (HEX code only): </label>
                <input type="text" name="ech_lfg_submitBtn_hoverColor" value="<?= htmlspecialchars(get_option( 'ech_lfg_submitBtn_hoverColor' ))?>" id="" pattern="^(#)[A-Za-z0-9]{3,6}">
            </div>
            <div class="form_row">
                <label>Submit Button Text Hover Color (HEX code only): </label>
                <input type="text" name="ech_lfg_submitBtn_text_hoverColor" value="<?= htmlspecialchars(get_option( 'ech_lfg_submitBtn_text_hoverColor' ))?>" id="" pattern="^(#)[A-Za-z0-9]{3,6}">
            </div>

            <h2>Sending Message Api</h2>
            <div class="form-row">
                <select name="ech_lfg_msg_api">
                    <option value="">請選擇</option>
                    <option value="wati" <?= (get_option( 'ech_lfg_msg_api' ) == "wati") ? 'selected' : '' ?>>Wati</option>
                    <option value="omnichat" <?= (get_option( 'ech_lfg_msg_api' ) == "omnichat") ? 'selected' : '' ?>>Omnichat</option>
                </select>
            </div>
            
            <h2>Wati Settings</h2>
            <div class="form_row">
                <label>Wati Key: </label>
                <input type="text" name="ech_lfg_wati_key" value="<?= htmlspecialchars(get_option( 'ech_lfg_wati_key' ))?>" id="" />
            </div>

            <div class="form_row">
                <label>Wati API domain: </label>
                <input type="text" name="ech_lfg_wati_api_domain" value="<?= htmlspecialchars(get_option( 'ech_lfg_wati_api_domain' ))?>" id="" />
            </div>

            <h2>Omnichat Settings</h2>
            <div class="form_row">
                <label>Brand Whatsapp Number: </label>
                <input type="text" name="ech_lfg_brand_whatsapp" value="<?= htmlspecialchars(get_option( 'ech_lfg_brand_whatsapp' ))?>" id="" />
            </div>
            <div class="form_row">
                <label>Omnichat Token: </label>
                <input type="text" name="ech_lfg_omnichat_token" value="<?= htmlspecialchars(get_option( 'ech_lfg_omnichat_token' ))?>" id="" />
            </div>

            <h2>FB Capi Settings</h2>
            <div class="form_row">
                <label>Pixel id: </label>
                <input type="text" name="ech_lfg_pixel_id" value="<?= htmlspecialchars(get_option( 'ech_lfg_pixel_id' ))?>" id="" />
            </div>

            <div class="form_row">
                <label>FB Access Token: </label>
                <input type="text" name="ech_lfg_fb_access_token" value="<?= htmlspecialchars(get_option( 'ech_lfg_fb_access_token' ))?>" id="" />
            </div>

            <h2>Form Note Settings</h2>
            <div class="form_row">
                <label>Phone: </label>
                <input type="text" name="ech_lfg_note_phone" value="<?= htmlspecialchars(get_option( 'ech_lfg_note_phone' ))?>" id="" />
            </div>

            <div class="form_row">
                <label>Whatsapp Link: </label>
                <input type="text" name="ech_lfg_note_whatapps_link" value="<?= htmlspecialchars(get_option( 'ech_lfg_note_whatapps_link' ))?>" id="" />
            </div>

            <h2>Email Receiver Settings</h2>
            <div class="form_row">
                <label>Admin Contact Email: </label>
                <input type="text" name="ech_lfg_admin_contact_email" value="<?= htmlspecialchars(get_option( 'ech_lfg_admin_contact_email' ))?>" id="" />
            </div>
            <div class="form_row">
                <label>Email Receiver (use comma to separate) : </label>
                <input type="text" name="ech_lfg_email_receiver" value="<?= htmlspecialchars(get_option( 'ech_lfg_email_receiver' ))?>" id="" />
            </div>

            

            <div class="form_row">
                <button type="submit"> Save </button>
            </div>
        </form>
        <div class="statusMsg"></div>
    </div> <!-- form_container -->
</div>

