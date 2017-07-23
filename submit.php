<?php
// $Id: submit.php,v 0.18 2006/03/23 21:37:00 wtravel
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
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";
include_once XOOPS_ROOT_PATH."/class/module.errorhandler.php";
include_once XOOPS_ROOT_PATH."/include/xoopscodes.php";
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

$moddir = $xoopsModule->getVar('dirname');
if (isset($_POST["dirid"])) {
    $dirid = intval($_POST["dirid"]);
} else if (isset($_GET["dirid"])) {
    $dirid = intval($_GET["dirid"]);
}

$eh = new ErrorHandler; //ErrorHandler object

if (isset($_GET["op"])) {
    $op =  $_GET["op"];
} else if (isset($_POST["op"])) {
    $op =  $_POST["op"];
}

if (empty($xoopsUser) and !$xoopsModuleConfig['anonpost']) {
	redirect_header(XOOPS_URL."/user.php",2,_MD_MUSTREGFIRST);
	exit();
}

if (!empty($_POST['submit'])) {
	//Get all selectable categories and put the prefix 'selectcat' in front of the catid.
	//With all results check if the result has a corresponding $_POST value.
	if ($_POST["title"] == "") {
		$eh->show("1001");
	}
	$title = $myts->makeTboxData4Save($_POST["title"]);
	$date = time();
	$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_items")."_itemid_seq");
	if ( $xoopsModuleConfig['autoapprove'] == 1 or $xoopsModuleConfig['autoapproveadmin'] == 1 ) {
		$status = 1;
	} else {
		$status = 0;
	}
	//itemtype = bronze, silver, gold etc., start with 0 as default.
	$submitter = $xoopsUser->getVar('uid');
	$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_items")."_itemid_seq");
	$sql = sprintf("INSERT INTO %s (itemid, uid, status, created, title, hits, rating, votes, typeid, dirid) VALUES (%u, %u, %u, '%s', '%s', %u, %u, %u, '%s', %u)", $xoopsDB->prefix("efqdiralpha1_items"), $newid, $submitter, $status, time(), $title, 0, 0, 0, 0, $dirid);
	$xoopsDB->query($sql) or $eh->show("0013");
	if ($newid == 0) {
		$itemid = $xoopsDB->getInsertId();
	}
	$allcatsresult = $xoopsDB->query("SELECT cid FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE dirid='".$dirid."' AND active='1'");
	$numrows = $xoopsDB->getRowsNum($allcatsresult);
	$count = 0;
    if ( $numrows > 0 ) {
		while(list($cid) = $xoopsDB->fetchRow($allcatsresult)) {
			if (isset($_POST["selected".$cid.""])) {
				$sql = sprintf("INSERT INTO %s (xid, cid, itemid, active, created) VALUES (%u, %u, %u, '%s', '%s')", $xoopsDB->prefix("efqdiralpha1_item_x_cat"), $newid, $cid, $itemid, 1, time());
				$xoopsDB->query($sql) or $eh->show("0013");
				$count ++;
			}
		}
		if ($count == 0) {
			redirect_header(XOOPS_URL."/modules/$moddir/submit.php?dirid=".$post_dirid."",2,_MD_NOCATEGORYMATCH);
			exit();
		}
	} else {
		redirect_header(XOOPS_URL."/modules/$moddir/submit.php?dirid=".$post_dirid."",2,_MD_NOCATEGORIESAVAILABLE);
		exit();
	}
	
/*	// RMV-NEW
	// Notify of new listing (anywhere). To be completed.
	$notification_handler =& xoops_gethandler('notification');
	$tags = array();
	$tags['ITEM_NAME'] = $title;
	$tags['ITEM_URL'] = XOOPS_URL . '/modules/'. $xoopsModule->getVar('dirname') . '/listing.php?item=' . $itemid;
	if ( $xoopsModuleConfig['autoapprove'] == 1 ) {
		$notification_handler->triggerEvent('global', $itemid, 'new_listing', $tags);
		redirect_header(XOOPS_URL."/modules/$moddir/edit.php?item=".$itemid."",2,_MD_APPROVED);
	}else{
		$tags['WAITINGLINKS_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/index.php?op=listNewListings';
		$notification_handler->triggerEvent('global', $itemid, 'new_listing', $tags);
		if ($notify) {
			include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
			$notification_handler->subscribe('link', $newid, 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
		}
		redirect_header(XOOPS_URL."/modules/$moddir/edit.php?item=".$itemid."",2,_MD_SAVED);
	}*/
	
	redirect_header(XOOPS_URL."/modules/$moddir/edit.php?item=".$itemid."",2,_MD_SAVED);
	exit();
} else {
	if (isset($_GET['dirid'])) {
		$get_dirid = intval($_GET['dirid']);
	} else {
		redirect_header(XOOPS_URL."/modules/$moddir/index.php",2,_MD_NODIRECTORYSELECTED);
	}
	$xoopsOption['template_main'] = 'efqdiralpha1_submit.html';
	include XOOPS_ROOT_PATH."/header.php";
	$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	//Query datatypes that match the categories selected. If not category selected.
	ob_start();
	$form = new XoopsThemeForm(_MD_SUBMITLISTING_FORM, 'submitform', 'submit.php');
	$form->setExtra('enctype="multipart/form-data"');
	$editor = !empty($_REQUEST['editor'])?$_REQUEST['editor']:"";
	if(!empty($editor)){
		setcookie("editor",$editor); // save to cookie
	}else
	// Or use user pre-selected editor through profile
	if(is_object($xoopsUser)){
		$editor =@ $xoopsUser->getVar("editor"); // Need set through user profile
	}
	$editor = "koivi";
	$options['name'] ='link_description';
	$options['value'] = empty($_REQUEST['message'])?"":$_REQUEST['message'];
	$options['value'] = "";
	//optional configs
	$options['rows'] = 25; // default value = 5
	$options['cols'] = 60; // default value = 50
	$options['width'] = '100%'; // default value = 100%
	$options['height'] = '400px'; // default value = 400px
	$options['small'] = true;
	$options['smiles'] = false;
	
	// "textarea": if the selected editor with name of $editor can not be created, the editor "textarea" will be used
	// if no $onFailure is set, then the first available editor will be used
	// If dohtml is disabled, set $noHtml to true
	$form->addElement(new XoopsFormText(_MD_TITLE, "title", 50, 250, ""), true);
	$category_tray = new XoopsFormElementTray(_MD_CATEGORIES, "", "cid");
	$catselarea = getCatSelectArea2();
	$category_tray->addElement(new XoopsFormLabel("", $catselarea));
	$form->addElement($category_tray, true);
	$form->addElement(new XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
	$form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
	$form->addElement(new XoopsFormHidden('op', 'selectcat'));
	$form->addElement(new XoopsFormHidden('dirid', $get_dirid));
	$form->display();
	$xoopsTpl->assign('submit_form', ob_get_contents());
	ob_end_clean();

	$xoopsTpl->assign('notify_show', !empty($xoopsUser) && !$xoopsModuleConfig['autoapprove'] ? 1 : 0);
	$xoopsTpl->assign('lang_sitetitle', _MD_SITETITLE);
	$xoopsTpl->assign('lang_siteurl', _MD_SITEURL);
	$xoopsTpl->assign('lang_category', _MD_CATEGORYC);
	$xoopsTpl->assign('lang_options', _MD_OPTIONS);
	$xoopsTpl->assign('lang_notify', _MD_NOTIFYAPPROVE);
	$xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
	$xoopsTpl->assign('lang_cancel', _CANCEL);
	include XOOPS_ROOT_PATH.'/footer.php';
}
?>