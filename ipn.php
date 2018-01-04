<?php
/*
* ipn.php
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
include __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts = \MyTextSanitizer::getInstance();// MyTextSanitizer object

require_once __DIR__ . '/class/class.subscription.php';

$eh = new ErrorHandler;
//get global configuration information
require_once __DIR__ . '/paypal_includes/global_config.inc.php';

//get pay pal configuration file
require_once __DIR__ . '/paypal_includes/config.inc.php';

//decide which post method to use
switch ($paypal['post_method']) {

    case 'libCurl': //php compiled with libCurl support

        $result = libCurlPost($paypal['url'], $_POST);

        break;

    case 'curl': //cURL via command line

        $result = curlPost($paypal['url'], $_POST);
        //print_r($result);
        break;

    case 'fso': //php fsockopen();

        $result = fsockPost($paypal['url'], $_POST);
        //print_r($result);
        break;

    default: //use the fsockopen method as default post method

        $result = fsockPost($paypal['url'], $_POST);
        //print_r($result);
        break;

}

//check the ipn result received back from paypal

if (stripos('VERIFIED', $result)) {
    // Automatic update of the subscription is not yet included in the success page.
    // This will need to be done manually.
    require_once __DIR__ . '/ipn/ipn_success.php';
} else {
    require_once __DIR__ . '/ipn/ipn_error.php';
}
