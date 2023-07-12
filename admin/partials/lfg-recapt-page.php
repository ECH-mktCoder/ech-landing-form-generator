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
    <h1>LFG reCAPTCHA v3</h1>

    <div class="form_container">
        <form method="post" id="lfg_recapt_form">
            <?php 
                settings_fields( 'lfg_recapt' );
                do_settings_sections( 'lfg_recapt' );
            ?>

            <div class="form_row">
                <?php              
                    $getApplyRecapt = get_option( 'ech_lfg_apply_recapt' );        
                    if(empty($getApplyRecapt) || !$getApplyRecapt ) {
                        add_option( 'ech_lfg_apply_recapt', 0 );
                    }
                ?>
                <label>Apply reCAPTCHA V3 : </label>
                <select name="ech_lfg_apply_recapt" id="ech_lfg_apply_recapt">
                    <option value="0" <?= ($getApplyRecapt == "0") ? 'selected' : '' ?>> No </option>
                    <option value="1" <?= ($getApplyRecapt == "1") ? 'selected' : '' ?>> Yes </option>
                </select>
            </div>

            <div class="form_row">
                <label>reCAPTCHA Site Key : </label>
                <input type="text" name="ech_lfg_recapt_site_key" id="ech_lfg_recapt_site_key" value="<?= get_option( 'ech_lfg_recapt_site_key' )?>" pattern="[A-Za-z0-9_-]{1,}" > 
            </div>

            <div class="form_row"> 
                <label>reCAPTCHA Secret Key : </label>
                <input type="text" name="ech_lfg_recapt_secret_key" id="ech_lfg_recapt_secret_key" value="<?= get_option( 'ech_lfg_recapt_secret_key' )?>" pattern="[A-Za-z0-9_-]{1,}" >
            </div>

            <div class="form_row">
                <label>reCAPTCHA Score (0-1, recommend to set 0.7): </label>
                <input type="text" name="ech_lfg_recapt_score" id="ech_lfg_recapt_score" value="<?= get_option( 'ech_lfg_recapt_score' )?>" pattern="^0?\.[1-9]|[1]|[0]" >
            </div>

            <div class="form_row">
                <button type="submit"> Save </button>
            </div>
        </form>

        <div class="statusMsg"></div>
        
    </div> <!-- form_container -->

</div>
