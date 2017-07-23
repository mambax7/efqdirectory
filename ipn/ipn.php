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
include __DIR__ . '/../header.php';
$myts = MyTextSanitizer::getInstance();// MyTextSanitizer object

$eh = new ErrorHandler;
//get global configuration information
require_once __DIR__ . '/../paypal_includes/global_config.inc.php';

//get pay pal configuration file
require_once __DIR__ . '/../paypal_includes/config.inc.php';

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
    require_once __DIR__ . '/ipn_success.php';
} else {
    require_once __DIR__ . '/ipn_error.php';
}
