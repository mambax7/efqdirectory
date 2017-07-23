<?php
// $Id: addcoupon.php,v 0.18 2006/03/23 21:37:00 wtravel
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
//	Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
// ------------------------------------------------------------------------- //

/*
 * This file handles the addition of coupons to listings.
 * Accessible to listing owners and administrators only. 	
 */

include "header.php";
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";
include_once XOOPS_ROOT_PATH."/class/module.errorhandler.php";
include_once XOOPS_ROOT_PATH."/include/xoopscodes.php";
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include_once "./class/class.formimage.php";
include_once "./class/class.image.php";
include_once "./class/class.couponhandler.php";

$eh = new ErrorHandler; //ErrorHandler object

$moddir = $xoopsModule->getvar("dirname");
$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");

//$moddir = $xoopsModule->getvar("dirname");
$couponid = isset($_GET['couponid']) ? intval($_GET['couponid']) : 0;
if (isset($_POST['itemid'])) {
	$itemid = intval($_POST['itemid']);
} else if (isset($_GET['item'])) {
	$itemid = intval($_GET['item']);
} else {
	$itemid = 0;
}

if ((empty($xoopsUser)) || !$xoopsUser->isAdmin($xoopsModule->mid()) || ($itemid == 0 && empty($_POST['delete']))) {
	redirect_header('index.php', 3, _NOPERM);
    exit();
}



if (isset($_POST['lbr'])) {
	$lbr = intval($_POST['lbr']);
} else {
	$lbr = 0;
}
if ($couponid > 0) {
    $coupon = new efqCouponHandler();
    $coupon->get($couponid);
    //$couponid = $coupon->couponid;
    $myts =& MyTextSanitizer::getInstance();
    $lbr = $coupon->lbr;
    $description = $coupon->descr;
    $image = $coupon->image;
    $heading = $coupon->heading;
    $publish = $coupon->publish > 0 ? $coupon->publish : time();
    $expire = $coupon->expire;    
    $dohtml = 1;
    $dobr = $lbr;
    if ($expire > 0) {
        $setexpire = 1;
    }
    else {
        $setexpire = 0;
        $expire = time() + 3600 * 24 * 7;
    }
} else {
    $itemid = isset($_POST['itemid']) ? intval($_POST['itemid']) : (isset($_GET['item']) ? intval($_GET['item']) : 0);
    $couponid = isset($_POST['couponid']) ? intval($_POST['couponid']) : null;
    $description = isset($_POST['description']) ? $_POST['description'] : "";
    $publish = isset($_POST['publish']) ? $_POST['publish'] : 0;
    $image = isset($_POST['image']) ? $_POST['image'] : "";
	$expire = isset($_POST['expire']) ? $_POST['expire'] : 0;
    $heading = isset($_POST['heading']) ? $_POST['heading'] : "";
    if ($expire > 0) {
        $setexpire = 1;
    }
    else {
        $setexpire = 0;
        $expire = time() + 3600 * 24 * 7;
    }
    
}



if (!empty($_POST['submit'])) {
	$coupon = new efqCouponHandler();
	if (isset($_POST['couponid'])) {
        $couponid = intval($_POST['couponid']);
        $message = _MD_COUPONEDITED;
    } else {
        $coupon->_new = true;
        $message = _MD_COUPONADDED;
    }	  
    if (!$coupon->create()) {
    	$coupon->message = _MD_ERR_ADDCOUPON;
    }      
    redirect_header('listing.php?item='.$itemid, 2, $coupon->message);
    exit();

} elseif (!empty($_POST['delete'])) {
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['couponid'])) {
            redirect_header('index.php',2,_MD_ERR_COUPONIDMISSING);
            exit();
        }
        $coupon = new efqCouponHandler();
        $couponid = intval($_POST['couponid']);
        if ($coupon->delete($couponid)) {
            redirect_header("listing.php?item=".$itemid,2,_MD_COUPONDELETED);
            exit();
        }
    }
    else {
        include XOOPS_ROOT_PATH.'/header.php';
        xoops_confirm(array('delete' => 'yes', 'couponid' => $couponid, 'ok' => 1), 'addcoupon.php?item='.$itemid.'', _MD_COUPONRUSURE);
        include_once XOOPS_ROOT_PATH.'/footer.php';
        exit();
    }
}
include XOOPS_ROOT_PATH.'/header.php';
include 'include/couponform.php';
include_once XOOPS_ROOT_PATH.'/footer.php';
?>