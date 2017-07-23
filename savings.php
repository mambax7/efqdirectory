<?php
// $Id: savings.php,v 0.18 2006/03/23 21:37:00 wtravel
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
//	Hacks provided by: Adam Frick											 //
// 	e-mail: africk69@yahoo.com												 //
//	Purpose: Create a yellow-page like business directory for xoops using 	 //
//	the mylinks module as the foundation.									 //
//  ------------------------------------------------------------------------ //
//  Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
//  ------------------------------------------------------------------------ //

include "header.php";
$myts =& MyTextSanitizer::getInstance();// MyTextSanitizer object
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
include_once "class/class.couponhandler.php";

$eh = new ErrorHandler;
$moddir = $xoopsModule->getvar("dirname");

$itemid = isset($_GET['itemid']) ? intval($_GET['itemid']) : 0;
$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;

$xoopsOption['template_main'] = 'efqdiralpha1_savings.html';
include XOOPS_ROOT_PATH."/header.php";
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);

$coupon = new efqCouponHandler();
if ($itemid) {
    $coupons = $coupon->getByItem($itemid);
}
$sql = "SELECT itemid, title FROM ".$xoopsDB->prefix("efqdiralpha1_items")." WHERE itemid=".$itemid."";
$item_result = $xoopsDB->query($sql);
$numrows = $xoopsDB->getRowsNum($item_result);
//echo $numrows;
while(list($itemid, $itemtitle) = $xoopsDB->fetchRow($item_result)) {
	$title = $myts->makeTboxData4Show($itemtitle);
	$item = $itemid;
}
$xoopsTpl->assign('itemtitle', $title);
$xoopsTpl->assign('itemid', $item);

foreach ($coupons as $coup) {
	//echo $coup['descr'];
	$xoopsTpl->append('coupons', array('couponid' => $coup['couponid'], 'itemid' => $coup['itemid'], 'descr' => $myts->makeTareaData4Show($coup['descr']), 'image' => $coup['image'], 'publish' => $coup['publish'], 'expire' => $coup['expire'], 'heading' => $coup['heading'], 'lbr' => $coup['lbr']));
}
if ($xoopsUser) {
    $xoopsTpl->assign('admin', $xoopsUser->isAdmin($xoopsModule->mid()));
}
$xoopsTpl->assign('moddir', $moddir);

include XOOPS_ROOT_PATH.'/footer.php';
?>