<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package      efqdirectory
 * @since
 * @author       Martijn Hertog (aka wtravel)
 * @author       XOOPS Development Team,
 */

use XoopsModules\Efqdirectory;

include __DIR__ . '/header.php';
$myts = \MyTextSanitizer::getInstance();// MyTextSanitizer object
// require_once __DIR__ . '/class/xoopstree.php';
//require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
// require_once __DIR__ . '/class/class.subscription.php';
// require_once __DIR__ . '/class/class.formradio.php';

//$eh           = new ErrorHandler;
$subscription = new Efqdirectory\Subscription();

if (isset($_GET['op'])) {
    $op = $_GET['op'];
} elseif (isset($_POST['op'])) {
    $op = $_POST['op'];
} else {
    $op = '';
}

$moddir = $xoopsModule->getVar('dirname');

if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
    $isadmin = true;
} else {
    $isadmin = false;
}

if (!empty($_GET['item'])) {
    $get_itemid = \Xmf\Request::getInt('item', 0, 'GET');
} else {
    $get_itemid = '0';
}

$owner = getUserIdFromItem($get_itemid);

if ($xoopsUser->getVar('uid') == $owner) {
    $editrights = '1';
} else {
    $editrights = '0';
    redirect_header("listing.php?itemid=$get_itemid", 2, _MD_EDITRIGHTS);
}

function showsubscription()
{
    global $xoopsDB, $myts, $moddir, $get_itemid, $owner, $xoopsOption, $xoopsTpl, $subscription, $xoopsUser;

    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    /** @var Efqdirectory\SubscriptionHandler $subscriptionHandler */
    $subscriptionHandler = $helper->getHandler('Subscription');

    //Check if item selected.
    if ('0' == $get_itemid) {
        redirect_header('index.php', 2, _MD_NOVALIDITEM);
    }

    //Default function (if listing type is normal) would be to view the possible subscriptions.

    //Show current subscription order for listing
    $defaultstartdate = time();
    $sql              = 'SELECT i.title, i.typeid, o.orderid, o.offerid, o.startdate, o.enddate, o.billto, o.status, o.itemid, o.autorenew, t.typename, p.ref, p.payment_status FROM '
                        . $xoopsDB->prefix($helper->getDirname() . '_itemtypes')
                        . ' t,  '
                        . $xoopsDB->prefix($helper->getDirname() . '_items')
                        . ' i, '
                        . $xoopsDB->prefix($helper->getDirname() . '_subscr_orders')
                        . ' o LEFT JOIN '
                        . $xoopsDB->prefix($helper->getDirname() . '_subscr_payments')
                        . ' p ON (o.orderid=p.orderid) WHERE o.typeid = t.typeid AND o.itemid=p.ref AND o.itemid=i.itemid AND i.itemid='
                        . $get_itemid
                        . ' ORDER BY t.typelevel ASC';
    $item_result      = $xoopsDB->query($sql) ; //|| $eh->show('0013');
    if (!$item_result) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $numrows          = $xoopsDB->getRowsNum($item_result);
    $order_exists     = false;
    if ($numrows > 0) {
        $xoopsTpl->assign('order_table', true);
        while (false !== (list($title, $typeid, $orderid, $offerid, $startdate, $enddate, $billto, $orderstatus, $itemid, $autorenew, $typename, $ref, $paymentstatus) = $xoopsDB->fetchRow($item_result))) {
            //Assign the text of the label for subscription type.
            $ordername = $subscription->getOrderItemName($offerid);

            if ('' == $paymentstatus) {
                $paymentstatus = _MD_LANG_INCOMPLETE;
                $terminate_on  = '1';
            } else {
                $terminate_on = null;
                $order_exists = true;
            }
            if ('1' == $orderstatus) {
                $defaultstartdate = $billto;
            }
            if ('' != $billto) {
                $billto = date('d-M-Y', $billto);
            }
            if ('' != $enddate) {
                $enddate = date('d-M-Y', $enddate);
            }
            if ('' != $startdate) {
                $startdate = date('d-M-Y', $startdate);
            }
            $xoopsTpl->assign('lang_subscr_offers_header', _MD_LANG_SUBSCR_ACTIVE_ORDERS_HEADER);
            $xoopsTpl->append('active_orders', [
                'orderid'       => $orderid,
                'ordername'     => $ordername,
                'offerid'       => $offerid,
                'startdate'     => $startdate,
                'enddate'       => $enddate,
                'billto'        => $billto,
                'orderstatus'   => $orderstatus,
                'itemid'        => $itemid,
                'autorenew'     => $autorenew,
                'typename'      => $myts->htmlSpecialChars($typename),
                'ref'           => $ref,
                'paymentstatus' => $paymentstatus,
                'renewal_url'   => "subscriptions.php?op=renew&order=$orderid&item=$get_itemid",
                'terminate_url' => "subscriptions.php?op=terminate&order=$orderid&item=$get_itemid",
                'terminate_on'  => $terminate_on
            ]);
            $xoopsTpl->assign('lang_current_subscr', _MD_LANG_CURRENT_SUBSCR);
            $xoopsTpl->assign('current_subscr', $typename);
            $xoopsTpl->assign('lang_terminate_order', _MD_LANG_TERMINATE_ORDER);
            $xoopsTpl->assign('lang_terminate_order_alt', _MD_LANG_TERMINATE_ORDER_ALT);
            $xoopsTpl->assign('lang_renew_subscription', _MD_LANG_RENEW_SUBSCRIPTION);
            $xoopsTpl->assign('lang_renew_subscription_alt', _MD_LANG_RENEW_SUBSCRIPTION_ALT);
            //$xoopsTpl->assign('renewal_url', "subscriptions.php?op=renew");

            $xoopsTpl->assign('lang_ordername', _MD_LANG_ORDERNAME);
            $xoopsTpl->assign('lang_startdate', _MD_LANG_STARTDATE);
            $xoopsTpl->assign('lang_billtodate', _MD_LANG_BILLTO);
            $xoopsTpl->assign('lang_enddate', _MD_LANG_ENDDATE);
            $xoopsTpl->assign('lang_paymentstatus', _MD_LANG_PAYMENTSTATUS);
            $xoopsTpl->assign('lang_actions', _MD_LANG_ACTIONS);
            $xoopsTpl->assign('moddir', $moddir);
            $listingtitle = $myts->htmlSpecialChars($title);
        }
    } else {
        $xoopsTpl->assign('lang_no_subscr_moment', _MD_LANG_NO_SUBSCR_MOMENT);
    }
    ob_start();
    if ($order_exists) {
        $order_form_title = _MD_UPDATE_SUBSCR_FORM;
    } else {
        $order_form_title = _MD_SUBSCR_FORM;
    }
    $form            = new \XoopsThemeForm($order_form_title, 'subscribeform', 'subscriptions.php?item=' . $get_itemid . '');
    $duration_arr    = $subscriptionHandler->durationPriceArray('1');
    $itemtype_select = new Efqdirectory\FormRadio(_MD_SUBSCR_TYPE, 'typeofferid', null, '<br>');
    $itemtype_select->addOptionArray($duration_arr);
    $form->addElement($itemtype_select, true);
    //TO DO: Add Auto Renew functionality
    //$form->addElement(new \XoopsFormRadioYN(_MD_AUTORENEWYN, 'autorenewal', '1'),true);
    $form->addElement(new \XoopsFormTextDateSelect(_MD_SELECT_STARTDATE, 'startdate', 15, $defaultstartdate), true);
    $form->addElement(new \XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
    $form->addElement(new \XoopsFormHidden('op', 'orderselect'));
    $form->addElement(new \XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->display();
    $orderform = ob_get_contents();
    ob_end_clean();
    $xoopsTpl->assign('orderform', $orderform);
}

function orderselect()
{
    //function to update subscription by creating an order or updating an order.
    global $xoopsDB, $myts, $moddir, $get_itemid, $owner, $xoopsOption, $xoopsTpl, $subscription, $xoopsUser;

    $helper = Efqdirectory\Helper::getInstance();
    /** @var Efqdirectory\SubscriptionHandler $subscriptionHandler */
    $subscriptionHandler = $helper->getHandler('Subscription');

    if ('0' == $get_itemid) {
        redirect_header('index.php', 2, _MD_NOVALIDITEM);
    }
    $orderid = $subscriptionHandler->createOrder($get_itemid);
    if (false === $orderid) {
        redirect_header("subscriptions.php?item=$get_itemid", 2, _MD_SUBSCR_TYPE_NOTSELECTED);
    }
    if (0 != $orderid) {
        redirect_header("subscriptions.php?item=$get_itemid&op=orderpayment&orderid=$orderid", 2, _MD_SAVED);
    } else {
        redirect_header("subscriptions.php?item=$get_itemid", 2, _MD_ITEM_NOT_EXIST);
    }
}

function orderpayment()
{
    global $xoopsDB, $myts, $moddir, $get_itemid, $owner, $xoopsOption, $xoopsTpl, $subscription, $xoopsUser;
    //Default function (if listing type is normal) would be to view the possible subscriptions.

    $helper = Efqdirectory\Helper::getInstance();

    //Show current subscription for listing
    //If standard subscription: Show subcription offers plus link to upgrade
    if (!empty($_GET['orderid'])) {
        $get_orderid = \Xmf\Request::getInt('orderid', 0, 'GET');
    } else {
        redirect_header('index.php', 2, _MD_NOVALIDITEM);
    }
    $sql          = 'SELECT o.orderid, o.uid, o.offerid, o.typeid, o.startdate, o.billto, o.status, o.itemid, o.autorenew, f.price, f.currency FROM '
                    . $xoopsDB->prefix($helper->getDirname() . '_subscr_orders')
                    . ' o, '
                    . $xoopsDB->prefix($helper->getDirname() . '_subscr_offers')
                    . ' f WHERE o.offerid=f.offerid AND o.orderid='
                    . $get_orderid
                    . ' ';
    $order_result = $xoopsDB->query($sql) ; //|| $eh->show('0013');
    if (!$order_result) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $numrows      = $xoopsDB->getRowsNum($order_result);
    if ($numrows > 0) {
        while (false !== (list($orderid, $uid, $offerid, $typeid, $startdate, $billto, $status, $itemid, $autorenew, $price, $currency) = $xoopsDB->fetchRow($order_result))) {
            ob_start();
            $itemname = $subscription->getOrderItemName($offerid);
            $form     = new \XoopsThemeForm(_MD_ORDER_PAYMENT_FORM, 'orderpaymentform', 'process.php');
            $form->addElement(new \XoopsFormText(_MD_PAY_FIRSTNAME, 'firstname', 50, 150, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_LASTNAME, 'lastname', 50, 150, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_ADDRESS1, 'address1', 50, 150, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_ADDRESS2, 'address2', 50, 150, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_CITY, 'city', 50, 150, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_STATE, 'state', 50, 150, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_ZIP, 'zip', 15, 50, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_EMAIL, 'email', 30, 150, ''));
            $form->addElement(new \XoopsFormText(_MD_PAY_PHONE1, 'phone1', 30, 150, ''));
            $form->addElement(new \XoopsFormLabel(_MD_PAY_WITH, '<img src="images/visa_mastercard.gif">'));
            $form->addElement(new \XoopsFormHidden('phone2', ''));
            $form->addElement(new \XoopsFormHidden('on0', ''));
            $form->addElement(new \XoopsFormHidden('os0', ''));
            $form->addElement(new \XoopsFormHidden('on1', ''));
            $form->addElement(new \XoopsFormHidden('os1', ''));
            $form->addElement(new \XoopsFormHidden('custom', $itemid));

            $form->addElement(new \XoopsFormHidden('item_name', $itemname));
            $form->addElement(new \XoopsFormHidden('item_number', $orderid));
            $form->addElement(new \XoopsFormHidden('amount', $price));
            $form->addElement(new \XoopsFormHidden('quantity', 1));
            $form->addElement(new \XoopsFormHidden('shipping_amount', '0'));
            $form->addElement(new \XoopsFormHidden('tax', '0'));

            $form->addElement(new \XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
            $form->display();
            $paymentform = ob_get_contents();
            ob_end_clean();
            $xoopsTpl->assign('paymentform', $paymentform);
        }

        $xoopsTpl->assign('lang_subscribe', _MD_LANG_SUBSCRIBE);
        $xoopsTpl->assign('lang_subscr_payment', _MD_LANG_SUBSCR_PAYMENT);
        $xoopsTpl->assign('lang_subscribe', _MD_LANG_SUBSCRIBE);
        $xoopsTpl->assign('moddir', $moddir);
    } else {
        //Else this item cannot be found in the database.
        redirect_header("listing.php?itemid=$get_itemid", 2, _MD_ITEM_NOT_EXIST);
    }
}

function terminate()
{
    global $xoopsDB, $myts, $moddir, $get_itemid, $editrights;
    if (!empty($_GET['order'])) {
        $get_orderid = \Xmf\Request::getInt('order', 0, 'GET');
    } else {
        redirect_header("subscriptions.php?item=$get_itemid", 2, _MD_NOVALIDORDER);
    }
    if ('1' == $editrights) {
        $form = new \XoopsThemeForm(_MD_CONFIRM_TERMINATE_TITLE, 'terminateform', 'subscriptions.php?item=' . $get_itemid . '');
        $form->addElement(new \XoopsFormLabel(_MD_CONFIRMATION, _MD_CONFIRM_TERMINATION_TEXT));
        $form->addElement(new \XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
        $form->addElement(new \XoopsFormHidden('op', 'terminate_confirm'));
        $form->addElement(new \XoopsFormHidden('orderid', $get_orderid));
        $form->display();
    } else {
        redirect_header("subscriptions.php?itemid=$get_itemid", 2, _MD_NORIGHTS);
    }
}

function terminate_confirm()
{
    global $subscription, $get_itemid;
    if (isset($_POST['orderid'])) {
        $post_orderid = \Xmf\Request::getInt('orderid', 0, 'POST');
        if ($subscription->delete($post_orderid)) {
            redirect_header("subscriptions.php?item=$get_itemid", 2, _MD_ORDER_DELETED);
        }
    } else {
        redirect_header("subscriptions.php?item=$get_itemid", 2, _MD_NOVALIDORDER);
    }
}

function renew()
{
    global $subscription, $get_itemid, $editrights;
    if (!empty($_GET['order'])) {
        $get_orderid = \Xmf\Request::getInt('order', 0, 'GET');
    } else {
        redirect_header('index.php', 2, _MD_NOVALIDITEM);
    }
    if ('1' == $editrights) {
        redirect_header("subscriptions.php?item=$get_itemid&op=orderpayment&orderid=$get_orderid", 2, _MD_FORWARDED_PAYMENT_PAGE);
    }
}

switch ($op) {
    case 'upgrade':
        upgrade();
        break;
    case 'orderselect':
        orderselect();
        break;
    case 'orderpayment':
        $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_subscriptions.tpl';
        include XOOPS_ROOT_PATH . '/header.php';
        orderpayment();
        $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
        break;
    case 'terminate':
        $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_subscriptions.tpl';
        include XOOPS_ROOT_PATH . '/header.php';
        terminate();
        $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
        break;
    case 'terminate_confirm':
        include XOOPS_ROOT_PATH . '/header.php';
        terminate_confirm();
        break;
    case 'renew':
        include XOOPS_ROOT_PATH . '/header.php';
        renew();
        break;
    default:
        $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_subscriptions.tpl';
        include XOOPS_ROOT_PATH . '/header.php';
        showsubscription();
        $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
        break;
}

include XOOPS_ROOT_PATH . '/footer.php';
