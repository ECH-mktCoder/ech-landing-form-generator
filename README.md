# ech-landing-form-generator
### with WATI auto send Whatsapp msg function
A Wordpress plugin to generate a responsive lead form for ECH company's brand websites. It is integrated with ECH marketing system (MSP). Form data will be passed and stored in the MSP for future use.   


## Usage
To generate a lead form, you need to enter a brand name in dashboard setting page first. It is a value required to pass to MSP. Then, you may copy the below shortcode sample to start generate a lead form. 
```
[ech_lfg default_r_code="t200" r="t575, t575google | t127, T127fb" r_code="TCODETOKEN575, TCODETOKEN127" item="Item 1" item_code="ITEMCODE123" shop="Causeway Bay, Kowloon" shop_code="HK001, KW001"]
```

## Shortcode attributes
Based on the form requirments and MSP campaigns, change the attributes or values if necessary.

Attribute | Description
----------|-------------
`default_r` (String) | Default tcode, default is t200
`default_r_code` (String)(*)[^1] | Default tcode MSP token
`r` (Strings)[^2] | Tcode eg.`"t575, t127"`. If there are more than one tcodes using the same tcode token, use `\|` to separate them. Eg. `"t575,t575g\|t127,t127fb"`. All case insensitive.
`r_code` (Strings) | Tcode token. Eg. `"TCODE1234, TCOED5678"`
`last_name_required` (INT) | 0 = false, 1 = true. Default is 1.
`email_required` (INT) | 0 = false, 1 = true. Default is 1.
`has_gender` (INT) | Has Gender field. 0 = false, 1 = true. Default is 0.
`has_age` (INT) | Has Age field. 0 = false, 1 = true. Default is 0.
`age_option` (Strings) | "Age" options. Use comma to separate. Eg. `"Under 18, 19 - 23 years old"` 
`age_code` (Strings)(*) | Age MSP token. Use comma to separate.
`booking_date_required` (INT) | 0 = false, 1 = true. Default is 1.
`item` (Strings)(*)[^1] | Item checkbox. Use comma to separate.
`item_code` (Strings)(*)[^1] | Item MSP token. Use comma to separate.
`item_label` (String) | Item label. Default is "*查詢項目"
`item_required` (INT) | 0 = false, 1 = true. Default is 1.
`is_item_limited` (INT) | Are the items limited. 0 = false, 1 = true. Default is 0
`item_limited_num` (INT) | No. of options can the user choose. Default is 1
`shop` (trings)(*)[^1] | Shop options. Use comma to separate.
`shop_code` (Strings)(*)[^1] | Shop MSP token. Use comma to separate.
`shop_label` (String) | Shop label. Default is "*請選擇診所"
`form_type` (INT) | Choose Form Type. 0 = false, 1 = true. Default is 0.
`has_textarea` (INT) | Has textarea field. 0 = false, 1 = true. Default is 0.
`textarea_label` (String) | Textarea placeholder. Default is "其他專業諮詢" 
`has_hdyhau` (INT) | Has "How did you hear about us" field. 0 = false, 1 = true. Default is 0. 
`hdyhau_item` (Strings) | "How did you hear about us" items. Use comma to separate. Eg. `"Facebook, Google"` 
`extra_radio_remark` (Strings) | Extra single choice question. Use comma to separate question. Use `\|` to separate two answers. Eg. `"是否恒健牙科現有客戶? \| 是 \| 否, 是否已參加企業牙科保健計劃? \| 是 \| 否"` 
`seminar` (INT) | Has Seminar field. 0 = false, 1 = true. Default is 0.
`seminar_date` (Strings) | Seminar Session items. Use comma to separate item. Use `\|` to separate Shop code and Date. Eg. `"HK09 \| 2023-10-14-11:00, NT04 \| 2023-10-07 15:00-16:00 \| SMILE 微笑矯視講座 (旺角)"` 
`has_participant` (INT) | Heath Talk participant. 0 = false, 1 = true. Default is 0
`quota_required` (INT) | Booking Quota. 0 = false, 1 = true. Default is 0
`submit_label` (String) | Submit label. Default is "提交" 
`brand` (String) | This will override the global setting "brand name" value which is set in the dashboard. 
`tks_para` (String) | URL parameter needs to pass to thank you page, usually product/treatment name. It is used for traffic tracking. Eg. `https://xxx.com/thanks?prod=TKS_PARA_VALUE`
`wati_send` (INT) | Enable or disable the WATI auto send Whatsapp msg function. 0 = disable, 1 = enable. Default is 0.
`wati_msg` (String) | Insert wati msg template name (provided by marketers)
`msg_header` (String) | wati msg template header parameters, if template header setup image, video, document, the field is required. Eg. `image \| https://nymg.com.hk/wp-content/uploads/2024/04/NYMG.jpg`
`msg_body` (String) | wati msg template body parameters, if special ranking is required, the field is required. Eg. `name, booking_location, booking_item`
`msg_button` (String) | wati msg template button parameters, if template button setup, the field is required. Eg. `https://nymg.com.hk/epay-landing/`, `https://example.com, https://example2.com`
`fbcapi_send` (INT) | Enable or disable the FB Capi . 0 = disable, 1 = enable. Default is 0.
`note_required` (INT) | Enable or disable to show `For same day reservation, please call or message us on WhatsApp.` 0 = disable, 1 = enable. Default is 0.
`email_send` (INT) | Enable or disable sending email. 0 = disable, 1 = enable. Default is 0. 
`email_receiver` (String) | Email receivers. Use comma to separate. This will override the Email Receiver Settings "Email Receiver" value in the dashboard.


Below attributes values must be corresponding to each other, otherwise no form will be generated:
1. r and r_code
2. item and item_code
3. shop and shop_code


[^1]: attribute is required


### Remarks
- If the shop selection field is more than 3 options or the item selection field is more than 7 options, those fields will be displayed as a dropdown selection.
