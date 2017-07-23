<?php
// $Id: editcategories.php 2 2008-01-27 18:16:55Z wtravel $
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
include '../../../include/cp_header.php';
if ( file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
	include "../language/".$xoopsConfig['language']."/main.php";
} else {
	include "../language/english/main.php";
}
include '../include/functions.php';
include_once XOOPS_ROOT_PATH.'/class/xoopstree.php';
include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
include_once XOOPS_ROOT_PATH.'/include/xoopscodes.php';
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
$myts =& MyTextSanitizer::getInstance();
$eh = new ErrorHandler;
$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");
include_once "../class/class.datafieldmanager.php";
$datafieldmanager = new efqDataFieldManager();
$moddir = $xoopsModule->getvar("dirname");

if (isset($_GET["item"])) {
    $get_itemid = intval($_GET["item"]);
}
if (isset($_POST["item"])) {
    $post_itemid = intval($_POST["item"]);
}

if (isset($_POST["dirid"])) {
    $post_dirid = intval($_POST["dirid"]);
}

$eh = new ErrorHandler; //ErrorHandler object

if (isset($_GET["op"])) {
    $op =  $_GET["op"];
} else if (isset($_POST["op"])) {
    $op =  $_POST["op"];
}

if (!empty($_POST['submit'])) {
	//Get all selectable categories and put the prefix 'selectcat' in front of the catid.
	//With all results check if the result has a corresponding $_POST value.
	$dirid = getDirIdFromItem($post_itemid);
	$sql = "SELECT cid FROM ".$xoopsDB->prefix("efqdiralpha1_item_x_cat")." WHERE itemid='".$post_itemid."'";
	$allitemcatsresult = $xoopsDB->query($sql); 
	$numrows = $xoopsDB->getRowsNum($allitemcatsresult);
	$count = 0;
	$allitemcats = array();
    if ( $numrows > 0 ) {
		while(list($cid) = $xoopsDB->fetchRow($allitemcatsresult)) {
			$allitemcats[] = $cid;
		}
	}
	$activeitemcatsresult = $xoopsDB->query("SELECT cid FROM ".$xoopsDB->prefix("efqdiralpha1_item_x_cat")." WHERE itemid='".$post_itemid."' AND active='1'"); 
	$numrows = $xoopsDB->getRowsNum($activeitemcatsresult);
	$count = 0;
	$activeitemcats = array();
    if ( $numrows > 0 ) {
		while(list($cid) = $xoopsDB->fetchRow($activeitemcatsresult)) {
			$activeitemcats[] = $cid;
		}
	}
	$allcatsresult = $xoopsDB->query("SELECT cid FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE active='1'");
	$numrows = $xoopsDB->getRowsNum($allcatsresult);
	$allcats = array();
	$postedcats = array();
    if ( $numrows > 0 ) {
		while(list($cid) = $xoopsDB->fetchRow($allcatsresult)) {
			$allcats[] = $cid;
			if (isset($_POST["selected".$cid.""])) {
				$postedcats[] = $cid;
			}
		}
	}
	//$inactivatecats is determined by the difference between posted cats and itemcats.
	//$nonpostedcats = array_diff($postedcats, $allitemcats);
	
	//Update these categories to inactive
	foreach ($postedcats as $cat) {
		if (! in_array($cat, $allitemcats)) {
			$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_item_x_cat")."_xid_seq");
			$sql = sprintf("INSERT INTO %s (xid, cid, itemid, active, created) VALUES (%u, %u, %u, '%s', '%s')", $xoopsDB->prefix("efqdiralpha1_item_x_cat"), $newid, $cat, $post_itemid, 1, time());
			$xoopsDB->query($sql) or $eh->show("0013");
		} else if (! in_array($cat, $activeitemcats)) {
			$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_item_x_cat")." SET active = '1' WHERE itemid = '".$post_itemid."' AND cid='".$cat."'";
			$xoopsDB->query($sql) or $eh->show("0013");
		}
	}
	foreach ($allitemcats as $cat) {
		if (! in_array($cat, $postedcats)) {
			$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_item_x_cat")." SET active = '0' WHERE itemid = '".$post_itemid."' AND cid='".$cat."'";
			$xoopsDB->query($sql) or $eh->show("0013");
		}
	}
	redirect_header(XOOPS_URL."/modules/$moddir/admin/editcategories.php?item=".$post_itemid."",2,_MD_CATEGORIES_UPDATED);
	exit();
} else {
	xoops_cp_header();
	$dirid = getDirIdFromItem($get_itemid);
	//Query datatypes that match the cargories selected. If not category selected.
	$form = new XoopsThemeForm(_MD_SELECTCAT_FORM, 'submitform', 'editcategories.php');
	$form->setExtra('enctype="multipart/form-data"');
	$category_tray = new XoopsFormElementTray(_MD_CATEGORIES, "", "cid");
	$catselarea = getCatSelectArea($dirid);
	$category_tray->addElement(new XoopsFormLabel("", $catselarea));
	$form->addElement($category_tray, true);
	$form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
	$form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
	$form->addElement(new XoopsFormHidden('item', $get_itemid));
	$form->addElement(new XoopsFormHidden('dirid', $dirid));
	$form->display();
	xoops_cp_footer();
}
?>