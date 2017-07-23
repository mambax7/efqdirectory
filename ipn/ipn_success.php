<?php
/*
 * ipn_success.php
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
//include file - not accessible directly

if (isset($paypal['business'])) {
    //log successful transaction to file or database
    $now     = time();
    $values  = create_local_variables();
    $orderid = $values['item_number'];
    if (checkDuplicateTrx($values['txn_id'], $values['payment_status']) === true) {
        $newid = $xoopsDB->genId($xoopsDB->prefix($module->getVar('dirname', 'n') . '_subscr_payments') . '_id_seq');
        $sql   = 'INSERT INTO ' . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_subscr_payments') . "
            (id, txn_id, txn_type, orderid, payer_business_name, address_name, address_street, address_city, address_state, address_zip, address_country, address_status, payer_email, payer_id, payer_status, mc_currency, mc_gross, mc_fee, created, payment_date, ref, payment_status) VALUES
            ($newid, '$values[txn_id]', '$values[txn_type]', '$orderid', '$values[payer_business_name]', '$values[address_name]', '$values[address_street]', '$values[address_city]', '$values[address_state]', '$values[address_zip]', '$values[address_country]', '$values[address_status]', '$values[payer_email]', '$values[payer_id]', '$values[payer_status]', '$values[mc_currency]', '$values[mc_gross]', '$values[mc_fee]', $now, '$values[payment_date]', '$values[custom]', '$values[payment_status]')";
        $xoopsDB->queryF($sql) or $eh->show('0013');

        if ($newid == 0) {
            $paymentid = $xoopsDB->getInsertId();
        }

        $subscription = new efqSubscription();
        $ordervalues  = $subscription->getOrderVars($orderid);
        if ($ordervalues['billto'] === '') {
            $current_billto = $ordervalues['startdate'];
        } else {
            $current_billto = $ordervalues['billto'];
        }
        $offervalues = $subscription->getOfferVars($ordervalues['offerid']);
        $count       = $offervalues['count'];
        $duration    = $offervalues['duration'];
        $date        = getdate($current_billto);

        switch ($duration) {
            case '1':
                $billto = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'] + (1 * $count), $date['year']);
                break;
            case '2':
                $billto = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'] + (7 * $count), $date['year']);
                break;
            case '3':
                $billto = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'] + (1 * $count), $date['mday'], $date['year']);
                break;
            case '4':
                $billto = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'] + (3 * $count), $date['mday'], $date['year']);
                break;
            case '5':
                $billto = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year'] + (1 * $count));
                break;
            default:
                $billto = '';
        }

        if ($ordervalues['startdate'] < time() && $ordervalues['billto'] === '') {
            $subscription->changeItemType($ordervalues['itemid'], $ordervalues['typeid']);
            $subscription->updateOrder($orderid, '1', time(), $billto);
        } else {
            $subscription->updateOrder($orderid, '1', $ordervalues['startdate'], $billto);
        }
        redirect_header('subscriptions.php?item=' . $values['custom'] . '', 5, _MD_ORDER_PROCESSED);
        exit();
    } else {
        redirect_header('subscriptions.php?item=' . $values['custom'] . '', 10, _MD_ORDER_ALREADY_PROCESSED);
        exit();
    }
} else {
    die('This page is not directly accessible');
}

/**
 * @param string $txn_id
 * @param string $payment_status
 * @return bool
 */
function checkDuplicateTrx($txn_id = '0', $payment_status = '0')
{
    global $xoopsDB;
    $block       = array();
    $myts        = MyTextSanitizer::getInstance();
    $userid      = 0;
    $sql         = 'SELECT txn_id, payment_status FROM ' . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_subscr_payments') . " WHERE txn_id='$txn_id' AND payment_status='$payment_status'";
    $result      = $xoopsDB->query($sql);
    $num_results = $xoopsDB->getRowsNum($result);
    if (!$result) {
        return true;
    } elseif ($num_results == 0) {
        return true;
    } else {
        return false;
    }
}
