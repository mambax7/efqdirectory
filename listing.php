<?php
// $Id: listing.php,v 1.1.0 2007-11-04 17:38:00 efqconsultancy
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
include "header.php";
$myts =& MyTextSanitizer::getInstance();// MyTextSanitizer object
include_once XOOPS_ROOT_PATH.'/class/xoopstree.php';
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';

include_once "class/class.listing.php";
include_once "class/class.datafieldmanager.php";
include_once "class/class.gmap.php";
include_once "class/class.couponhandler.php";

$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");
$datafieldmanager = new efqDataFieldManager();
$eh = new ErrorHandler;

$xoopsOption['template_main'] = 'efqdiralpha1_listing.html';
include XOOPS_ROOT_PATH."/header.php";

if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
	$isadmin = true;
} else {
	$isadmin = false;
}

if (isset($_GET['catid'])) {
	$get_catid = intval($_GET['catid']);
} else {
	$get_catid = 0;
}
if (!empty($_GET['item'])) {
	$get_itemid = intval($_GET['item']);
} else {
	redirect_header("index.php",2,_MD_NOVALIDITEM);
	exit();
}

$moddir = $xoopsModule->getvar("dirname");
$dirid = getDirIdFromItem($get_itemid);
$islistingowner = false;

$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
if ($isadmin) {
	$adminlink = '<a href="'.XOOPS_URL.'/modules/'.$moddir.'/admin/index.php?op=edit&amp;item='.$get_itemid.'"><img src="'.XOOPS_URL.'/modules/'.$moddir.'/images/editicon2.gif" border="0" alt="'._MD_EDITTHISLISTING.'" /></a>';
} else {
	$adminlink = '';
}
$xoopsTpl->assign('adminlink', $adminlink);

$coupon = new efqCouponHandler();
$listing = new efqListing();
$listinghandler = new efqListingHandler();
$listing->setVars($listinghandler->getListing($get_itemid));

$pathstring = "<a href='index.php?dirid=".$dirid."'>"._MD_MAIN."</a>&nbsp;:&nbsp;";
$pathstring .= $mytree->getNicePathFromId($get_catid, "title", "index.php?dirid=".$dirid."");

$editlink = "<a href=\"edit.php?item=".$get_itemid."\"><img src=\"".XOOPS_URL."/modules/".$moddir."/images/".$xoopsConfig['language']."/listing-edit.gif\" alt=\""._MD_EDIT_LISTING."\" title=\""._MD_EDIT_LISTING."\" /></a>";

if (isset($xoopsUser) && $xoopsUser != null) {
	if ($xoopsUser->getVar('uid') == $listing->getVar('uid')) {
		$islistingowner = true;
		$xoopsTpl->assign('listingowner', '1');
		if ( $listing->getVar('status') == '2' and $xoopsModuleConfig['autoapprove'] == 1 ) {
			$editrights = '1';
		} else {
			$editrights = '0';
		}
	} else {
		$editrights = '0';
	}
}
if (!$islistingowner and !$isadmin) {
	$listinghandler->incrementHits($get_itemid);
}

$type = getTypeFromId($listing->getVar('typeid'));
$template = getTemplateFromCatid($get_catid);
if ($listing->getVar('logourl') != "") {
	$logourl = "<img src=\"".XOOPS_URL."/modules/".$moddir."/uploads/".$listing->getVar('logourl')."\">";
} else {
	$logourl = "";
}

$xoopsTpl->assign('category_path', $pathstring);
$xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
$xoopsTpl->assign('edit_link', $editlink);
$xoopsTpl->assign('type', $type);
$xoopsTpl->assign('creates', $listing->getVar('created'));
$xoopsTpl->assign('editrights', $listing->_editrights);
$xoopsTpl->assign('item_id', $listing->getVar('itemid'));
$xoopsTpl->assign('item_title', $listing->getVar('title'));
$xoopsTpl->assign('item_description', $listing->getVar('description'));
$xoopsTpl->assign('item_logo', $logourl);
$xoopsTpl->assign('lang_item_title', _MD_LANG_ITEMTITLE);
$xoopsTpl->assign('lang_item_description', _MD_LANG_ITEMDESCRIPTION);
$xoopsTpl->assign('lang_edit_item', _MD_LANG_EDIT_ITEM);
$xoopsTpl->assign('template', $template);

$listing->setDataTypes($listinghandler->getDataTypes($get_itemid));

if (count($listing->_datatypes) > 0) {
	$xoopsTpl->assign('datatypes', true);
	foreach ($listing->_datatypes as $datatype) {
		$xoopsTpl->append('section'.$datatype['section'].'', array('icon' => $datatype['icon'], 'label' => $datatype['title'], 'value' => $datatype['value'], 'fieldtype' => $datatype['fieldtype']));		
	}
}

if ($xoopsModuleConfig['allowcoupons'] == '0') {
	$xoopsTpl->assign('couponsallowed', "0");
} else {
	$xoopsTpl->assign('couponsallowed', "1");
	$xoopsTpl->assign('lang_addcoupon', _MD_ADDCOUPON);
	$xoopsTpl->assign('coupons', $coupon->getCountByLink($get_itemid));
}
if ($xoopsModuleConfig['allowsubscr'] == '0') {
	$xoopsTpl->assign('subscrallowed', "0");
} else {
	$xoopsTpl->assign('subscrallowed', "1");
	$xoopsTpl->assign('lang_viewsubscription', _MD_VIEWSUBSCRIPTIONS);
}

include XOOPS_ROOT_PATH.'/footer.php';
?>