<?php
// $Id: subscriptions.php,v 0.18 2006/05/04 21:54:00 wtravel
//  ------------------------------------------------------------------------ //
//                				EFQ Directory			                     //
//                    Copyright (c) 2006 EFQ Consultancy                     //
//                       <http://www.efqdirectory.com/>                      //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
//  Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
// ------------------------------------------------------------------------- //
include 'header.php';
$myts =& MyTextSanitizer::getInstance();// MyTextSanitizer object
include_once XOOPS_ROOT_PATH.'/class/xoopstree.php';
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
include_once XOOPS_ROOT_PATH.'/include/xoopscodes.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include_once 'class/class.subscription.php';
include_once 'class/class.formradio.php';

$eh = new ErrorHandler;
$subscription = new efqSubscription();

if (isset($_GET['op'])) {
    $op =  $_GET['op'];
} else if (isset($_POST['op'])) {
    $op =  $_POST['op'];
} else {
	$op = '';
}

$moddir = $xoopsModule->getvar("dirname");

if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
	$isadmin = true;
} else {
	$isadmin = false;
}

if (!empty($_GET['item'])) {
	$get_itemid = intval($_GET['item']);
} else {
	$get_itemid = '0';
}

$owner = getUserIdFromItem($get_itemid);

if ($xoopsUser->getVar('uid') == $owner) {
	$editrights = '1';
} else {
	$editrights = '0';
	redirect_header("listing.php?itemid=$get_itemid",2,_MD_EDITRIGHTS);
	exit();
}
		
function showsubscription()
{
	global $xoopsDB, $eh, $myts, $moddir, $get_itemid, $owner, $xoopsOption, $xoopsTpl, $subscription, $xoopsUser;
	//Check if item selected.
	if ( $get_itemid == '0' ) {
		redirect_header("index.php",2,_MD_NOVALIDITEM);
		exit();
	}
	
	//Default function (if listing type is normal) would be to view the possible subscriptions.
	
	//Show current subscription order for listing
	$defaultstartdate = time();
	$sql = "SELECT i.title, i.typeid, o.orderid, o.offerid, o.startdate, o.enddate, o.billto, o.status, o.itemid, o.autorenew, t.typename, p.ref, p.payment_status FROM ".$xoopsDB->prefix("efqdiralpha1_itemtypes")." t,  ".$xoopsDB->prefix("efqdiralpha1_items")." i, ".$xoopsDB->prefix("efqdiralpha1_subscr_orders")." o LEFT JOIN ".$xoopsDB->prefix("efqdiralpha1_subscr_payments")." p ON (o.orderid=p.orderid) WHERE o.typeid = t.typeid AND o.itemid=p.ref AND o.itemid=i.itemid AND i.itemid=".$get_itemid." ORDER BY t.level ASC";
	$item_result = $xoopsDB->query($sql) or $eh->show("0013");
	$numrows = $xoopsDB->getRowsNum($item_result);
	$order_exists = false;
	if ($numrows > 0) {
		$xoopsTpl->assign('order_table', true);
		while(list($title, $typeid, $orderid, $offerid, $startdate, $enddate, $billto, $orderstatus, $itemid, $autorenew, $typename, $ref, $paymentstatus) = $xoopsDB->fetchRow($item_result)) {
			//Assign the text of the label for subscription type.
			$ordername = $subscription->getOrderItemName($offerid);
			
			if ( $paymentstatus == '' ) {
				$paymentstatus = _MD_LANG_INCOMPLETE;
				$terminate_on = '1';
			} else {
				$terminate_on = null;
				$order_exists = true;
			}
			if ( $orderstatus == '1' ) {
				$defaultstartdate = $billto;
			}
			if ( $billto != '' ) {
				$billto = date('d-M-Y', $billto);
			}
			if ( $enddate != '' ) {
				$enddate = date('d-M-Y', $enddate);
			} 
			if ( $startdate != '' ) {
				$startdate = date('d-M-Y', $startdate);
			}  
			$xoopsTpl->assign('lang_subscr_offers_header', _MD_LANG_SUBSCR_ACTIVE_ORDERS_HEADER);
			$xoopsTpl->append('active_orders', array('orderid' => $orderid, 'ordername' => $ordername, 'offerid' => $offerid, 'startdate' => $startdate, 'enddate' => $enddate, 'billto' => $billto, 'orderstatus' => $orderstatus, 'itemid' => $itemid, 'autorenew' => $autorenew, 'typename' => $myts->makeTboxData4Show($typename), 'ref' => $ref, 'paymentstatus' => $paymentstatus, 'renewal_url' => "subscriptions.php?op=renew&order=$orderid&item=$get_itemid", 'terminate_url' => "subscriptions.php?op=terminate&order=$orderid&item=$get_itemid", 'terminate_on' => $terminate_on));
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
			$listingtitle = $myts->makeTboxData4Show($title);
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
		$form = new XoopsThemeForm($order_form_title, 'subscribeform', 'subscriptions.php?item='.$get_itemid.'');
		$duration_arr = $subscription->durationPriceArray('1');
		$itemtype_select = new efqFormRadio(_MD_SUBSCR_TYPE, 'typeofferid', null, "<br />");
		$itemtype_select->addOptionArray($duration_arr);
		$form->addElement($itemtype_select, true);
		//TO DO: Add Auto Renew functionality
		//$form->addElement(new XoopsFormRadioYN(_MD_AUTORENEWYN, 'autorenewal', '1'),true);
		$form->addElement(new XoopsFormTextDateSelect(_MD_SELECT_STARTDATE, 'startdate', 15, $defaultstartdate),true);
		$form->addElement(new XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
		$form->addElement(new XoopsFormHidden("op", "orderselect"));
		$form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
		$form->display();
		$orderform = ob_get_contents();
	ob_end_clean();
	$xoopsTpl->assign('orderform', $orderform);

	
}

function orderselect() {
	//function to update subscription by creating an order or updating an order.
	global $xoopsDB, $eh, $myts, $moddir, $get_itemid, $owner, $xoopsOption, $xoopsTpl, $subscription, $xoopsUser;
	if ( $get_itemid == '0' ) {
		redirect_header("index.php",2,_MD_NOVALIDITEM);
		exit();
	}
	$orderid = $subscription->createOrder($get_itemid);
	if ( $orderid == false ) {
		redirect_header("subscriptions.php?item=$get_itemid",2,_MD_SUBSCR_TYPE_NOTSELECTED);
		exit();	
	}
	if ( $orderid != 0 ) {
		redirect_header("subscriptions.php?item=$get_itemid&op=orderpayment&orderid=$orderid",2,_MD_SAVED);
		exit();	
	} else {
		redirect_header("subscriptions.php?item=$get_itemid",2,_MD_ITEM_NOT_EXIST);
		exit();
	}
}

function orderpayment()
{
	global $xoopsDB, $eh, $myts, $moddir, $get_itemid, $owner, $xoopsOption, $xoopsTpl, $subscription, $xoopsUser;
	//Default function (if listing type is normal) would be to view the possible subscriptions.
	
	//Show current subscription for listing
	//If standard subscription: Show subcription offers plus link to upgrade
	if (!empty($_GET['orderid'])) {
		$get_orderid = intval($_GET['orderid']);
	} else {
		redirect_header("index.php",2,_MD_NOVALIDITEM);
		exit();
	}
	$sql = "SELECT o.orderid, o.uid, o.offerid, o.typeid, o.startdate, o.billto, o.status, o.itemid, o.autorenew, f.price, f.currency FROM ".$xoopsDB->prefix("efqdiralpha1_subscr_orders")." o, ".$xoopsDB->prefix("efqdiralpha1_subscr_offers")." f WHERE o.offerid=f.offerid AND o.orderid=".$get_orderid."";
	$order_result = $xoopsDB->query($sql) or $eh->show("0013");
	$numrows = $xoopsDB->getRowsNum($order_result);
	if ($numrows > 0) {
		while(list($orderid, $uid, $offerid, $typeid, $startdate, $billto, $status, $itemid, $autorenew, $price, $currency) = $xoopsDB->fetchRow($order_result)) {
			ob_start();
				$itemname = $subscription->getOrderItemName($offerid);
				$form = new XoopsThemeForm(_MD_ORDER_PAYMENT_FORM, 'orderpaymentform', 'process.php');
				$form->addElement(new XoopsFormText(_MD_PAY_FIRSTNAME, 'firstname', 50, 150, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_LASTNAME, 'lastname', 50, 150, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_ADDRESS1, 'address1', 50, 150, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_ADDRESS2, 'address2', 50, 150, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_CITY, 'city', 50, 150, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_STATE, 'state', 50, 150, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_ZIP, 'zip', 15, 50, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_EMAIL, 'email', 30, 150, ""));
				$form->addElement(new XoopsFormText(_MD_PAY_PHONE1, 'phone1', 30, 150, ""));
				$form->addElement(new XoopsFormLabel(_MD_PAY_WITH, '<img src="images/visa_mastercard.gif">'));
				$form->addElement(new XoopsFormHidden('phone2', ""));
				$form->addElement(new XoopsFormHidden('on0', ""));
				$form->addElement(new XoopsFormHidden('os0', ""));
				$form->addElement(new XoopsFormHidden('on1', ""));
				$form->addElement(new XoopsFormHidden('os1', ""));
				$form->addElement(new XoopsFormHidden('custom', $itemid));
				
				$form->addElement(new XoopsFormHidden('item_name', $itemname));
				$form->addElement(new XoopsFormHidden('item_number', $orderid));
				$form->addElement(new XoopsFormHidden('amount', $price));
				$form->addElement(new XoopsFormHidden('quantity', 1));
				$form->addElement(new XoopsFormHidden('shipping_amount', '0'));
				$form->addElement(new XoopsFormHidden('tax', '0'));
				
				$form->addElement(new XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
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
		redirect_header("listing.php?itemid=$get_itemid",2,_MD_ITEM_NOT_EXIST);
		exit();		
	}
}

function terminate()
{
	global $xoopsDB, $eh, $myts, $moddir, $get_itemid, $editrights;
	if (!empty($_GET['order'])) {
		$get_orderid = intval($_GET['order']);
	} else {
		redirect_header("subscriptions.php?item=$get_itemid",2,_MD_NOVALIDORDER);
		exit();
	}
	if ($editrights == '1') {
		$form = new XoopsThemeForm(_MD_CONFIRM_TERMINATE_TITLE, 'terminateform', 'subscriptions.php?item='.$get_itemid.'');
		$form->addElement(new XoopsFormLabel(_MD_CONFIRMATION, _MD_CONFIRM_TERMINATION_TEXT));
		$form->addElement(new XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
		$form->addElement(new XoopsFormHidden("op", "terminate_confirm"));
		$form->addElement(new XoopsFormHidden("orderid", $get_orderid));
		$form->display();
	} else {
		redirect_header("subscriptions.php?itemid=$get_itemid",2,_MD_NORIGHTS);
		exit();
	}
	
}

function terminate_confirm()
{
	global $subscription, $get_itemid;
	if ( isset( $_POST['orderid'] )) {
		$post_orderid = intval($_POST['orderid']);
		if ($subscription->delete($post_orderid)) {
			redirect_header("subscriptions.php?item=$get_itemid",2,_MD_ORDER_DELETED);
			exit();
		}
	} else {
		redirect_header("subscriptions.php?item=$get_itemid",2,_MD_NOVALIDORDER);
		exit();
	}
}

function renew()
{
	global $subscription, $get_itemid, $editrights;
	if (!empty($_GET['order'])) {
		$get_orderid = intval($_GET['order']);
	} else {
		redirect_header("index.php",2,_MD_NOVALIDITEM);
		exit();
	}
	if ($editrights == '1') {
		redirect_header("subscriptions.php?item=$get_itemid&op=orderpayment&orderid=$get_orderid",2,_MD_FORWARDED_PAYMENT_PAGE);
		exit();	
	}
}

switch($op) {
case "upgrade":
	upgrade();
	break;
case "orderselect":
	orderselect();
	break;
case "orderpayment":
	$xoopsOption['template_main'] = 'efqdiralpha1_subscriptions.html';
	include XOOPS_ROOT_PATH."/header.php";
	orderpayment();
	$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	break;
case "terminate":
	$xoopsOption['template_main'] = 'efqdiralpha1_subscriptions.html';
	include XOOPS_ROOT_PATH."/header.php";
	terminate();
	$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	break;
case "terminate_confirm":
	include XOOPS_ROOT_PATH."/header.php";
	terminate_confirm();
	break;
case "renew":
	include XOOPS_ROOT_PATH."/header.php";
	renew();
	break;
default:
	$xoopsOption['template_main'] = 'efqdiralpha1_subscriptions.html';
	include XOOPS_ROOT_PATH."/header.php";
	showsubscription();
	$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	break;
}


include XOOPS_ROOT_PATH.'/footer.php';
?>