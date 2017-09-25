<?php
/*
 * global_config.inc.php
 *
 * PHP Toolkit for PayPal v0.51
 * http://www.paypal.com/pdn
 *
 * Copyright (c) 2004 PayPal Inc
 *
 * Released under Common Public License 1.0
 * http://opensource.org/licenses/cpl.php
 *
 */

//create variable names to perform additional order processing

/**
 * @return array
 */
function create_local_variables()
{
    $array_name = [];
    $variables  = [
        'business',
        'receiver_email',
        'receiver_id',
        'item_name',
        'item_number',
        'quantity',
        'invoice',
        'custom',
        'memo',
        'tax',
        'option_selection1',
        'option_name1',
        'option_selection2',
        'option_name2',
        'num_cart_items',
        'mc_gross',
        'mc_fee',
        'mc_currency',
        'settle_amount',
        'settle_currency',
        'exchange_rate',
        'payment_gross',
        'payment_fee',
        'payment_status',
        'pending_reason',
        'reason_code',
        'payment_date',
        'txn_id',
        'txn_type',
        'payment_type',
        'for_auction',
        'auction_buyer_id',
        'auction_closing_date',
        'auction_multi_item',
        'first_name',
        'last_name',
        'payer_business_name',
        'address_name',
        'address_street',
        'address_city',
        'address_state',
        'address_zip',
        'address_country',
        'address_status',
        'payer_email',
        'payer_id',
        'payer_status',
        'notify_version',
        'verify_sign'
    ];

    foreach ($variables as $k => $v) {
        if (isset($_POST[$v])) {
            $array_name[$v] = "$_POST[$v]";
        } else {
            $array_name[$v] = '';
        }
    }

    //print_r($array_name);
    return $array_name;
}

//post transaction data using curl

/**
 * @param $url
 * @param $data
 * @return array|string|\XoopsFormElementTray|\XoopsFormText
 */
function curlPost($url, $data)
{
    global $paypal, $info;

    //build post string

    foreach ($data as $i => $v) {
        $postdata .= $i . '=' . urlencode($v) . '&';
    }

    $postdata .= 'cmd=_notify-validate';

    //execute curl on the command line

    exec("$paypal[curl_location] -d \"$postdata\" $url", $info);

    $info = implode(',', $info);

    return $info;
}

//posts transaction data using libCurl

/**
 * @param $url
 * @param $data
 * @return string
 */
function libCurlPost($url, $data)
{
    //build post string

    foreach ($data as $i => $v) {
        $postdata .= $i . '=' . urlencode($v) . '&';
    }

    $postdata .= 'cmd=_notify-validate';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

    //Start ob to prevent curl_exec from displaying stuff.
    ob_start();
    curl_exec($ch);

    //Get contents of output buffer
    $info = ob_get_contents();
    curl_close($ch);

    //End ob and erase contents.
    ob_end_clean();

    return $info;
}

//posts transaction data using fsockopen.
/**
 * @param $url
 * @param $data
 * @return array|string
 */
function fsockPost($url, $data)
{
    //Parse url
    $web = parse_url($url);

    $postdata = '';
    //build post string
    foreach ($data as $i => $v) {
        $postdata .= $i . '=' . urlencode($v) . '&';
    }

    $postdata .= 'cmd=_notify-validate';

    //Set the port number
    if ('https' === $web['scheme']) {
        $web['port'] = '443';
        $ssl         = 'ssl://';
    } else {
        $web['port'] = '80';
    }

    //Create paypal connection
    $fp = @fsockopen($ssl . $web['host'], $web['port'], $errnum, $errstr, 30);

    //Error checking
    if (!$fp) {
        echo "$errnum: $errstr";
    } //Post Data
    else {
        fwrite($fp, "POST $web[path] HTTP/1.1\r\n");
        fwrite($fp, "Host: $web[host]\r\n");
        fwrite($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fwrite($fp, 'Content-length: ' . strlen($postdata) . "\r\n");
        fwrite($fp, "Connection: close\r\n\r\n");
        fwrite($fp, $postdata . "\r\n\r\n");

        //loop through the response from the server
        while (!feof($fp)) {
            $info[] = @fgets($fp, 1024);
        }

        //close fp - we are done with it
        fclose($fp);

        //break up results into a string
        $info = implode(',', $info);
    }

    return $info;
}

//Display Paypal Hidden Variables

function showVariables()
{
    global $paypal; ?>

    <!-- PayPal Configuration -->
    <input type="hidden" name="business" value="<?= $paypal['business'] ?>">
    <input type="hidden" name="cmd" value="<?= $paypal['cmd'] ?>">
    <input type="hidden" name="image_url" value="<?php echo "$paypal[site_url]$paypal[image_url]"; ?>">
    <input type="hidden" name="return" value="<?php echo "$paypal[site_url]$paypal[success_url]"; ?>">
    <input type="hidden" name="cancel_return" value="<?php echo "$paypal[site_url]$paypal[cancel_url]"; ?>">
    <input type="hidden" name="notify_url" value="<?php echo "$paypal[site_url]$paypal[notify_url]"; ?>">
    <input type="hidden" name="rm" value="<?= $paypal['return_method'] ?>">
    <input type="hidden" name="currency_code" value="<?= $paypal['currency_code'] ?>">
    <input type="hidden" name="lc" value="<?= $paypal['lc'] ?>">
    <input type="hidden" name="bn" value="<?= $paypal['bn'] ?>">
    <input type="hidden" name="cbt" value="<?= $paypal['continue_button_text'] ?>">

    <!-- Payment Page Information -->
    <input type="hidden" name="no_shipping" value="<?= $paypal['display_shipping_address'] ?>">
    <input type="hidden" name="no_note" value="<?= $paypal['display_comment'] ?>">
    <input type="hidden" name="cn" value="<?= $paypal['comment_header'] ?>">
    <input type="hidden" name="cs" value="<?= $paypal['background_color'] ?>">

    <!-- Product Information -->
    <input type="hidden" name="item_name" value="<?= $paypal['item_name'] ?>">
    <input type="hidden" name="amount" value="<?= $paypal['amount'] ?>">
    <input type="hidden" name="quantity" value="<?= $paypal['quantity'] ?>">
    <input type="hidden" name="item_number" value="<?= $paypal['item_number'] ?>">
    <input type="hidden" name="undefined_quantity" value="<?= $paypal['edit_quantity'] ?>">
    <input type="hidden" name="on0" value="<?= $paypal['on0'] ?>">
    <input type="hidden" name="os0" value="<?= $paypal['os0'] ?>">
    <input type="hidden" name="on1" value="<?= $paypal['on1'] ?>">
    <input type="hidden" name="os1" value="<?= $paypal['os1'] ?>">

    <!-- Shipping and Misc Information -->
    <input type="hidden" name="shipping" value="<?= $paypal['shipping_amount'] ?>">
    <input type="hidden" name="shipping2" value="<?= $paypal['shipping_amount_per_item'] ?>">
    <input type="hidden" name="handling" value="<?= $paypal['handling_amount'] ?>">
    <input type="hidden" name="tax" value="<?= $paypal['tax'] ?>">
    <input type="hidden" name="custom" value="<?= $paypal['custom'] ?>">
    <input type="hidden" name="invoice" value="<?= $paypal['invoice'] ?>">

    <!-- Customer Information -->
    <input type="hidden" name="first_name" value="<?= $paypal['firstname'] ?>">
    <input type="hidden" name="last_name" value="<?= $paypal['lastname'] ?>">
    <input type="hidden" name="address1" value="<?= $paypal['address1'] ?>">
    <input type="hidden" name="address2" value="<?= $paypal['address2'] ?>">
    <input type="hidden" name="city" value="<?= $paypal['city'] ?>">
    <input type="hidden" name="state" value="<?= $paypal['state'] ?>">
    <input type="hidden" name="zip" value="<?= $paypal['zip'] ?>">
    <input type="hidden" name="email" value="<?= $paypal['email'] ?>">
    <input type="hidden" name="night_phone_a" value="<?= $paypal['phone_1'] ?>">
    <input type="hidden" name="night_phone_b" value="<?= $paypal['phone_2'] ?>">
    <input type="hidden" name="night_phone_c" value="<?= $paypal['phone_3'] ?>">

    <?php
}
