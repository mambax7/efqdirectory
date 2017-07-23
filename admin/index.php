<?php
// $Id: index.php,v 0.18 2006/03/23 21:37:00 wtravel
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
//include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
include_once XOOPS_ROOT_PATH.'/include/xoopscodes.php';
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
$myts =& MyTextSanitizer::getInstance();
$eh = new ErrorHandler;
$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");
include_once "../class/class.datafieldmanager.php";
include_once "../class/class.subscription.php";
include_once "../class/class.efqtree.php";
include_once "../class/class.listing.php";
include_once "../class/class.gmap.php";
$efqtree = new EfqTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");
$datafieldmanager = new efqDataFieldManager();

$listinghandler = new efqListingHandler;
$subscription = new efqSubscription;
$subscriptionhandler = new efqSubscriptionHandler;
$moddir = $xoopsModule->getvar("dirname");

if (isset($_GET['item'])) {
    $get_itemid = intval($_GET['item']);
}

function listings()
{
    global $xoopsDB, $xoopsModule;
	xoops_cp_header();
	adminmenu(0,_MD_A_MODADMIN_HOME);
	echo "<h4>"._MD_LISTINGSCONF."</h4>";
		echo"<table width='100%' border='0' cellspacing='1' class='outer'>"
		."<tr class=\"odd\"><td>";
		//$result = $xoopsDB->query("select count(*) from ".$xoopsDB->prefix("efqdiralpha1_broken")."");
		//list($totalbrokenlinks) = $xoopsDB->fetchRow($result);
	$result3 = $xoopsDB->query("select count(*) from ".$xoopsDB->prefix("efqdiralpha1_items")." where status='1'");
    	list($totalnewlistings) = $xoopsDB->fetchRow($result3);
	if($totalnewlistings>0){
		$totalnewlistings = "<span style='color: #ff0000; font-weight: bold'>$totalnewlistings</span>";
	}
	echo " - <a href='".XOOPS_URL."/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=".$xoopsModule->getVar('mid')."'>"._MD_GENERALSET."</a>";
	echo "<br /><br />";
	echo " - <a href=directories.php>"._MD_MANAGEDIRECTORIES."</a>";
	echo "<br /><br />";
	echo " - <a href=fieldtypes.php>"._MD_MANAGEFIELDTYPES."</a>";
	echo "<br /><br />";
	echo " - <a href=index.php?op=listNewListings>"._MD_LISTINGSWAITING." ($totalnewlistings)</a>";
	echo "<br /><br />";
	echo " - <a href=index.php?op=duplicateDataTypes>"._MD_DUPLICATE_DATATYPES."</a>";
	echo "<br /><br />";
	echo " - <a href=xdir_migrate.php>"._MD_MIGRATE_FROM_XDIR."</a>";
	echo "<br /><br />";
	$result=$xoopsDB->query("select count(*) from ".$xoopsDB->prefix("efqdiralpha1_items")." where status>0");
    list($numrows) = $xoopsDB->fetchRow($result);
	echo "<br /><br /><div>";
	printf(_MD_THEREARE,$numrows);	echo "</div>";
   	echo"</td></tr></table>";
	xoops_cp_footer();
}

function listNewListings()  //completed
{
	global $xoopsDB, $xoopsConfig, $myts, $eh, $mytree, $mytree2, $moddir;
	$sql = "SELECT i.itemid, i.logourl, i.uid, i.status, i.created, i.title, i.typeid FROM ".$xoopsDB->prefix("efqdiralpha1_items")." i WHERE i.status='1'";
	$result = $xoopsDB->query($sql, 10, 0);
    $numrows = $xoopsDB->getRowsNum($result);
    if ( $numrows > 0 ) {
		xoops_cp_header();
		echo "<h4>"._MD_LISTINGSCONF."</h4>";
		echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
		echo "<tr class=\"odd\"><td>";
		echo "<h4>"._MD_LISTINGSWAITING."&nbsp;($numrows)</h4><br />";
		echo "<table width=\"95%\">";
		echo "<tr><td align=\"left\"nowrap><b>"._MD_LISTINGTITLE."</b></td>";
		echo "<td align=\"left\" nowrap><b>"._MD_SUBMITTER."</b></td><td align=\"left\" nowrap><b>"._MD_CREATED."</b></td><td>&nbsp;</td></tr>";
		while(list($itemid, $logourl, $submitterid, $status, $date, $title, $type) = $xoopsDB->fetchRow($result)) {
			$title = $myts->makeTboxData4Edit($title);
			$submitter = XoopsUser::getUnameFromId($submitterid);
			$created = formatTimestamp($date);
			echo "<tr><td>$title</td><td><a href=\"".XOOPS_URL."/userinfo.php?uid=$submitterid\">$submitter</a></td><td>$created</td><td valign=\"bottom\">";
            echo "<a href=\"".XOOPS_URL."/modules/".$moddir."/admin/index.php?op=edit&amp;item=$itemid\">"._MD_EDIT."</a>";
			echo "</td></tr>\n";
		}
		echo"</table></td></tr></table>";
		xoops_cp_footer();
	} else {
		redirect_header("".XOOPS_URL."/modules/".$moddir."/admin/index.php?op=listings",1,_MD_NONEW_LISTINGS);
		exit();
	}
}

function delVote()
{
	global $xoopsDB, $_GET, $eh;
    $rid = $_GET['rid'];
    $get_itemid = intval($_GET['itemid']);
	$sql = sprintf("DELETE FROM %s WHERE ratingid = %u", $xoopsDB->prefix("listings_votedata"), $rid);
    $xoopsDB->query($sql) or $eh->show("0013");
    updaterating($get_itemid);
    redirect_header("index.php",1,_MD_VOTEDELETED);
    exit();
}

function delListingConfirm()
{
	global $xoopsDB, $eh, $xoopsModule, $get_itemid;
	xoops_cp_header();
	$form = new XoopsThemeForm(_MD_CONFIRM_DELETELISTING_FORM, 'confirmform', 'index.php');
	$submit_tray = new XoopsFormElementTray(_MD_DELETEYN, "", "cid");
	$submit_tray->addElement(new XoopsFormButton("", 'submit', _MD_DELETE, 'submit'));
	$submit_tray->addElement(new XoopsFormLabel("", "<input type=\"button\" class=\"formButton\" value=\""._MD_CANCEL."\" onclick=\"location='index.php?op=edit&amp;item=$get_itemid'\""));
	$form->addElement($submit_tray, true);
	//$form->addElement($form_submit);
	$form->addElement(new XoopsFormHidden("op", "deleteListing"));
	$form->addElement(new XoopsFormHidden("itemid", $get_itemid));
	$form->display();
	xoops_cp_footer();
}

function delListing()
{
	global $xoopsDB, $eh, $xoopsModule;
	$sql = sprintf("DELETE FROM %s WHERE itemid = %u", $xoopsDB->prefix("efqdiralpha1_items"), intval($_POST["itemid"]));//EDIT-RC10
   	$xoopsDB->queryF($sql) or $eh->show("0013");
	$sql = sprintf("DELETE FROM %s WHERE itemid = %u", $xoopsDB->prefix("efqdiralpha1_item_text"), intval($_POST["itemid"]));//EDIT-RC10
	$xoopsDB->queryF($sql) or $eh->show("0013");
	$sql = sprintf("DELETE FROM %s WHERE itemid = %u", $xoopsDB->prefix("efqdiralpha1_item_img"), intval($_POST["itemid"]));//EDIT-RC10
	$xoopsDB->queryF($sql) or $eh->show("0013");
	$sql = sprintf("DELETE FROM %s WHERE itemid = %u", $xoopsDB->prefix("efqdiralpha1_item_x_cat"), intval($_POST["itemid"]));//EDIT-RC10
	$xoopsDB->queryF($sql) or $eh->show("0013");
	$sql = sprintf("DELETE FROM %s WHERE itemid = %u", $xoopsDB->prefix("efqdiralpha1_item_x_loc"), intval($_POST["itemid"]));//EDIT-RC10
	$xoopsDB->queryF($sql) or $eh->show("0013");
	xoops_comment_delete($xoopsModule->getVar('mid'), intval($_POST["itemid"]));
	xoops_notification_deletebyitem ($xoopsModule->getVar('mid'), 'listing', intval($_POST["itemid"]));
    redirect_header("index.php",1,_MD_LISTINGDELETED);
	exit();
}

function approve()
{
	global $xoopsConfig, $xoopsDB, $get_itemid, $eh;
	$query = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_items")." set status='2' where itemid=".$get_itemid."";
	$xoopsDB->queryF($query) or $eh->show("0013");
    redirect_header("index.php?op=listNewListings",1,_MD_LISTINGAPPROVED);
}

function updateItemType()
{
	global $xoopsConfig, $xoopsDB, $eh;
	$post_itemid = intval($_POST['itemid']);
	$post_typeid = intval($_POST['typeid']);
	$query = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_items")." SET typeid='$post_typeid' WHERE itemid=".$post_itemid."";
	$xoopsDB->query($query) or $eh->show("0013");
    redirect_header("index.php?op=edit&amp;item=$post_itemid",2,_MD_ITEM_UPDATED);
}

function listDuplicateDataTypes()
{
	global $xoopsConfig, $xoopsDB, $eh, $myts, $moddir;
	
	xoops_cp_header();
	adminmenu(-1,_MD_DUPLICATE_DATATYPES);
	echo "<br />";
  	$sql = "SELECT dt1.dtypeid, dt1.title, dt1.fieldtypeid ";
	$sql .=	"FROM ".$xoopsDB->prefix('efqdiralpha1_dtypes')." dt1, ".$xoopsDB->prefix('efqdiralpha1_dtypes')." dt2 ";
	$sql .= "WHERE dt1.title=dt2.title AND dt1.fieldtypeid=dt2.fieldtypeid ORDER BY dt1.title ASC, dt1.fieldtypeid ASC"; 
  	
  	$result = $xoopsDB->query($sql) or $eh->show("0013");
    $numrows = $xoopsDB->getRowsNum($result);
    
    if ( $numrows > 0 ) {
		echo "<h4>"._MD_DUPLICATE_DATATYPES."</h4>";
		echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
		echo "<tr><th>"._MD_DTYPE."</th><th>"._MD_DTYPE_TITLE."</th><th>"._MD_FIELDTYPE."</th></tr>";
		while(list($dtypeid, $title, $fieldtypeid) = $xoopsDB->fetchRow($result)) {
			$dtypetitle = $myts->makeTboxData4Show($title);
			$result_arr[] = $dtypeid.$dtypetitle.$fieldtypeid;
			$result_array[] = array( $dtypeid, $dtypetitle, $fieldtypeid );
		}
		$checkKeysUniqueComparison = create_function('$value','if ($value > 1) return true;');
		$duplicate_results = array_keys (array_filter (array_count_values($result_arr), $checkKeysUniqueComparison));
		
		if ( count($duplicate_results) > 0 ) {
			$duplicates = '1';
		} else {
			$duplicates = '0';
		}
		
		$last_title = '';
		$last_fieldtypeid = '0';
		$merge = '';
		foreach($result_array as $arr) {
			$id = $arr[0];
			$match[$id] = $arr[0].$arr[1].$arr[2];
			$test = array_pop($match);
			$key_to_be_deleted = array_search($test, $duplicate_results);
			
			if (in_array($test,$duplicate_results)) {
				echo "<tr>";
				echo "<td class=\"even\">$arr[0]</td>";
				echo "<td class=\"odd\">$arr[1]</td>";
				echo "<td class=\"even\">$arr[2]</td>";
				echo "</tr>";
				if ( $last_title == '' && $last_fieldtypeid == '0') {
					$last_title = $arr[1];
					$last_fieldtypeid = $arr[2];
					$merge .= $arr[0];
				} else if ( $arr[1] != $last_title && $arr[2] != $last_fieldtypeid ) {
					$last_title = $arr[1];
					$last_fieldtypeid = $arr[2];
					$merge .= "|".$arr[0];
					echo "<tr><td colspan='3'>";
					echo "<form action='index.php' method='post'>";
					echo "<input type='button' class='formButton' action='submit' name='submit' id='submit' value='"._MD_MERGE."'";
					echo "<input type='hidden' name='merge' value='$merge'>";
					echo "<input type='hidden' name='op' value='mergeDuplicates'>";
					echo "</form>";
					echo "</td></tr>";
					$merge = '';
				} else {
					$merge .= "|".$arr[0]; 
				}
				unset ( $duplicate_results[$key_to_be_deleted] );
			}
			unset( $match );
			
		}
		if ($duplicates == '1' ) {
			echo "<tr><td colspan='3'>";
			echo "<form action='index.php' method='post'>";
			echo "<input type='submit' class='formButton' action='submit' name='submit' id='submit' value='"._MD_MERGE."'";
			echo "<input type='hidden' name='merge' value='$merge'>";
			echo "<input type='hidden' name='op' value='mergeDuplicates'>";
			echo "</form>";
			echo "</td></tr>";
		} else {
			echo "<tr><td colspan='3'>";
			echo _MD_NORESULTS;
			echo "</td></tr>";
		}
		echo "</table>";
    }
    xoops_cp_footer();
}

function mergeDuplicates()
{
	global $xoopsDB, $eh;
	if ( isset( $_POST['merge'] ) ) {
		$merge = $_POST['merge'];
		$merge_arr = split('[|]',$merge);
		$replacements_arr = array_slice($merge_arr, 1);
		$replacements = '';
		foreach ( $replacements_arr as $key => $value ) {
			$replacements .= "'".$value."',";
		}
		$length = strlen($replacements);
		$replacements = substr($replacements, 0 , $length-1);
		$sql = "UPDATE ".$xoopsDB->prefix('efqdiralpha1_dtypes_x_cat')." SET dtypeid=".$merge_arr[0]." WHERE dtypeid IN (".$replacements.")";
		$xoopsDB->queryF($sql) or $eh->show("0013");
		$sql = "UPDATE ".$xoopsDB->prefix('efqdiralpha1_data')." SET dtypeid=".$merge_arr[0]." WHERE dtypeid IN (".$replacements.")";
		$xoopsDB->queryF($sql) or $eh->show("0013");
		$sql = "DELETE FROM ".$xoopsDB->prefix('efqdiralpha1_dtypes')." WHERE dtypeid IN (".$replacements.")";
		$xoopsDB->queryF($sql) or $eh->show("0013");
	} else {
		$merge = '';
	}
	redirect_header("index.php?op=duplicateDataTypes",2,_MD_SAVED);
	
}

function migrateFromXdirectory() {
	
}

if(!isset($_POST['op'])) {
	$op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
	$op = $_POST['op'];
}
switch ($op) {
	case "approve":
	approve();
	break;
case "deleteListingConfirm":
	delListingConfirm();
	break;
case "deleteListing":
	delListing();
	break;
case "delVote":
	delVote();
	break;
case "listNewListings":
	listNewListings();
	break;
case "updateItemType":
	updateItemType();
	break;
case "duplicateDataTypes":
	listDuplicateDataTypes();
	break;
case "mergeDuplicates":
	mergeDuplicates();
	break;
case 'migrateXdir':
	migrateFromXdirectory();
	break;
case "edit":
	global $xoopsDB, $xoopsConfig, $myts, $eh, $efqtree, $moddir, $xoopsUser, $datafieldmanager, $subscription, $subscriptionhandler;
	$sql = "SELECT i.itemid, i.logourl, i.uid, i.status, i.created, i.title, i.typeid, t.description FROM ".$xoopsDB->prefix("efqdiralpha1_items")." i LEFT JOIN ".$xoopsDB->prefix("efqdiralpha1_item_text")." t ON (i.itemid=t.itemid) WHERE i.itemid=".$get_itemid."";
	$item_result = $xoopsDB->query($sql);
    $numrows = $xoopsDB->getRowsNum($item_result);
	xoops_cp_header();
	adminmenu(-1,_MD_A_MODADMIN_HOME);
	echo "<hr size='1'/><br />";
	while(list($itemid, $logourl, $submitter, $status, $created, $itemtitle, $typeid, $description) = $xoopsDB->fetchRow($item_result)) {
		if ($status == '1') {
			echo "<input type=\"button\" value=\""._MD_APPROVE."\" onclick=\"location='index.php?op=approve&amp;item=$get_itemid'\">&nbsp;";
		}	
		echo "&nbsp;<input type=\"button\" value=\""._MD_DELETE."\" onClick=\"location='index.php?op=deleteListingConfirm&amp;item=$get_itemid'\">&nbsp;<input type=\"button\" value=\""._MD_VIEWITEM."\" onclick=\"location='".XOOPS_URL."/modules/".$moddir."/listing.php?item=$get_itemid'\"><br /><br />";
		
		$sql = "SELECT DISTINCT t.dtypeid, t.title, t.section, f.typeid, f.fieldtype, f.ext, t.options, d.itemid, d.value, d.customtitle, t.custom ";
		$sql .= "FROM ".$xoopsDB->prefix("efqdiralpha1_item_x_cat")." ic, ".$xoopsDB->prefix("efqdiralpha1_dtypes_x_cat")." xc, ".$xoopsDB->prefix("efqdiralpha1_fieldtypes")." f, ".$xoopsDB->prefix("efqdiralpha1_dtypes")." t ";
		$sql .= "LEFT JOIN ".$xoopsDB->prefix("efqdiralpha1_data")." d ON (t.dtypeid=d.dtypeid AND d.itemid=".$get_itemid.") ";
		$sql .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=".$get_itemid."";
		$data_result = $xoopsDB->query($sql) or $eh->show("0013");
		$numrows = $xoopsDB->getRowsNum($data_result);

		$form = new XoopsThemeForm(_MD_EDITITEM_FORM, 'editform', 'index.php');
		$form->addElement(new XoopsFormText(_MD_TITLE, "itemtitle", 50, 250, $itemtitle), true);
		//$categories = getCategoriesPaths($get_itemid);
		$get_dirid = getDirIdFromItem($itemid);
		$categories = getCatSelectArea($itemid, $get_dirid);
		$form_cats = new XoopsFormLabel(_MD_ITEMCATEGORIES, "$categories");
		$form->addElement($form_cats);
		$form->addElement(new XoopsFormDhtmlTextArea(_MD_DESCRIPTION, "description", $description, 5, 50));
		while(list($dtypeid, $title, $section, $ftypeid, $fieldtype, $ext, $options, $itemid, $value, $customtitle, $custom) = $xoopsDB->fetchRow($data_result)) {
			$field = $datafieldmanager->createField($title, $dtypeid, $fieldtype, $ext, $options, $value, $custom, $customtitle);
		}
		$form->addElement(new XoopsFormButton('', 'submit', _MD_SAVE, 'submit'));
		$form->addElement(new XoopsFormHidden("op", "save"));
		$form->addElement(new XoopsFormHidden("itemid", $get_itemid));
		$form->addElement(new XoopsFormHidden("dirid", $get_dirid));
		$form->addElement(new XoopsFormHidden("ini_itemtitle", $itemtitle));
		$form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
		if ($description == false) {
			$form->addElement(new XoopsFormHidden("description_set", '0'));
		} else {
			$form->addElement(new XoopsFormHidden("description_set", '1'));
		}
		$form->display();
		echo "<br />";
		$itemtypes = $subscriptionhandler->itemTypesArray();
		$form = new XoopsThemeForm(_MD_EDITITEMTYPE_FORM, 'edititemtypeform', 'index.php');
		$itemtypes_select = new XoopsFormSelect(_MD_SELECT_ITEMTYPE, 'typeid', $typeid);
		$itemtypes_select->addOptionArray($itemtypes);
		$form->addElement($itemtypes_select);
		$form->addElement(new XoopsFormButton('', 'submit', _MD_SAVE, 'submit'));
		$form->addElement(new XoopsFormHidden("op", "updateItemType"));
		$form->addElement(new XoopsFormHidden("itemid", $get_itemid));
		$form->addElement(new XoopsFormHidden("dirid", $get_dirid));
		$form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
		$form->display();
	}
	xoops_cp_footer();
	break;
case "save":
	if (!empty($_POST["submit"])) {
		$submitter = !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
	
		if (!empty($_POST["itemid"])) {
			$post_itemid = intval($_POST["itemid"]);
		} else {
			redirect_header("index.php",2,_MD_NOVALIDITEM);
			exit();
		}
		if (isset($_POST["itemtitle"])) {
			$p_title = $myts->makeTboxData4Save($_POST["itemtitle"]);
			$p_ini_title = $_POST["ini_itemtitle"];
			if ($p_title != $p_ini_title) {
				//If the posted title is different from the initial title the record should be updated.
				$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_items")." SET title = '$p_title' WHERE itemid = $post_itemid";
				$xoopsDB->query($sql) or $eh->show("0013");
			}
		} else {
			redirect_header("index.php",2,_MD_NOVALIDITEM);
			exit();
		}
		if (!empty($_POST['dirid'])) {
			$post_dirid = intval($_POST['dirid']);
		} else {
			$post_dirid = 0;
		}
		if (isset($_POST['ini_description'])) {
			$p_ini_description = $myts->makeTareaData4Save($_POST["ini_description"]);
		} else {
			$p_ini_description = NULL;
		}
		if (isset($_POST["description"])) {
			$p_description = $myts->makeTareaData4Save($_POST["description"]);
		} else {
			$p_description = NULL;
		}
		if (isset($_POST["description_set"])) {
			if ($_POST["description_set"] == '1') {
				if ($p_ini_description != $p_description) {
					$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_item_text")." SET description = '$p_description' WHERE itemid = $post_itemid";
					$xoopsDB->query($sql) or $eh->show("0013");
				}
			} else if (isset($_POST["description"]) && $_POST["description"] != "") {
				if ($p_description != NULL) {
					$sql = sprintf("INSERT INTO %s (itemid, description) VALUES (%u, '%s')", $xoopsDB->prefix("efqdiralpha1_item_text"), $post_itemid, $p_description);
					$xoopsDB->query($sql) or $eh->show("0013");
				}
			}
		}
		$sql = "SELECT DISTINCT t.dtypeid, t.title, t.section, f.typeid, f.fieldtype, f.ext, t.options, d.itemid, d.value ";
		$sql .= "FROM ".$xoopsDB->prefix("efqdiralpha1_item_x_cat")." ic, ".$xoopsDB->prefix("efqdiralpha1_dtypes_x_cat")." xc, ".$xoopsDB->prefix("efqdiralpha1_fieldtypes")." f, ".$xoopsDB->prefix("efqdiralpha1_dtypes")." t ";
		$sql .= "LEFT JOIN ".$xoopsDB->prefix("efqdiralpha1_data")." d ON (t.dtypeid=d.dtypeid AND d.itemid=".$post_itemid.") ";
		$sql .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND ic.itemid=".$post_itemid."";
		$data_result = $xoopsDB->query($sql) or $eh->show("0013");
		$numrows = $xoopsDB->getRowsNum($data_result);
		while(list($dtypeid, $title, $section, $ftypeid, $fieldtype, $ext, $options, $itemid, $value) = $xoopsDB->fetchRow($data_result))
		{
			if (isset($_POST["$dtypeid"])) {
				if ($fieldtype == "textarea" || "dhtml") {
					$post_value = $myts->makeTareaData4Save($_POST["$dtypeid"]);
				} else {
					$post_value = $myts->makeTboxData4Save($_POST["$dtypeid"]);
				}
			} else {
				$post_value = "";
			}

			if (isset($_POST["custom".$dtypeid.""])) {
				$post_customtitle = $myts->makeTboxData4Save($_POST["custom".$dtypeid.""]);
			} else {
				$post_customtitle = "";
			}
			if ($fieldtype == 'address') {
				$addressfields = array('address', 'address2', 'zip', 'postcode','phone', 'lat', 'lon', 'phone', 'fax', 'mobile', 'city', 'country', 'uselocyn', 'main', 'active');
				foreach ($addressfields as $field) {
					if (isset($_POST["$dtypeid$field"])) {
						${"post_".$field} = $myts->makeTboxData4Save($_POST["$dtypeid$field"]);
					} else {
						${"post_".$field} = "";
					}
				}
				//INSERT OF UPDATE address
				//If INSERT, then get $newid and save it as the value in efqdiralpha1_data table
				if ( isset($_POST["submitaddress"]) ) {
					if ($itemid == NULL || $post_value == "") {
						//That means there was not any value, so a new record should be added to the data table.
						$newaddrid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_addresses")."_addrid_seq");
						$sql = sprintf("INSERT INTO %s (addrid, itemid, dtypeid, address, address2, zip, postcode, phone, lat, lon, main, active, fax, mobile, city, country) VALUES (%u, %u, %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %u, %u, '%s', '%s', '%s', '%s')", $xoopsDB->prefix("efqdiralpha1_addresses"), $newaddrid, $post_itemid, $dtypeid, $post_address, $post_address2, $post_zip, $post_postcode, $post_phone, $post_lat, $post_lon, $post_main, $post_active, $post_fax, $post_mobile, $post_city, $post_country);
						//echo $sql."<br /><br />";
						$xoopsDB->query($sql) or $eh->show("0013");
						$post_value = $xoopsDB->getInsertId();
					} else {
						$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_addresses")." SET address = '$post_address', address2 = '$post_address2', zip = '$post_zip', postcode = '$post_postcode', lat = '$post_lat', lon = '$post_lon', main = '$post_main', active = '$post_active', fax = '$post_fax', mobile = '$post_mobile', city = '$post_city', country = '$post_country' WHERE addrid = '$post_value' AND itemid = '$post_itemid'";
						//echo $sql."<br /><br />";
						$xoopsDB->query($sql) or $eh->show("0013");
					}
					
				}
			}
			if ($itemid == NULL) {
				//That means there was not any value, so a new record should be added to the data table.
				$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_data")."_dataid_seq");
				$sql = sprintf("INSERT INTO %s (dataid, itemid, dtypeid, value, created) VALUES (%u, %u, %u, '%s', '%s')", $xoopsDB->prefix("efqdiralpha1_data"), $newid, $post_itemid, $dtypeid, $post_value, time());
				$xoopsDB->query($sql) or $eh->show("0013");
			} else {
				if ($value != $post_value) {
					$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_data")." SET value = '$post_value' WHERE dtypeid = $dtypeid AND itemid = $itemid";
					$xoopsDB->query($sql) or $eh->show("0013");
				}
			}
		}
		
		$efqlisting = new efqListing();
		$efqlistinghandler = new efqListingHandler();
		$linkedcats = $efqlistinghandler->getLinkedCatsArray($post_itemid, $post_dirid);
		$sql = "SELECT cid FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE dirid='".$post_dirid."' AND active='1'";
		$allcatsresult = $xoopsDB->query($sql);	
		
		$numrows = $xoopsDB->getRowsNum($allcatsresult);
		$count = 0;
	    if ( $numrows > 0 ) {
			while(list($cid) = $xoopsDB->fetchRow($allcatsresult)) {
				if (isset($_POST["selected".$cid.""])) {
					if (!in_array($cid, $linkedcats)) {
						$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_item_x_cat")."_xid_seq");
						$sql = sprintf("INSERT INTO %s (xid, cid, itemid, active, created) VALUES (%u, %u, %u, '%s', '%s')", $xoopsDB->prefix("efqdiralpha1_item_x_cat"), $newid, $cid, $post_itemid, 1, time());
						$xoopsDB->query($sql) or $eh->show("0013");
					}
					
					$count ++;				
				} else {
					if (in_array($cid, $linkedcats)) {
						$sql = sprintf("DELETE FROM %s WHERE cid=%u AND itemid=%u", $xoopsDB->prefix("efqdiralpha1_item_x_cat"), $cid, $post_itemid);
						$xoopsDB->query($sql) or $eh->show("0013");
					}
				}
			}
			if ($count == 0) {
				redirect_header(XOOPS_URL."/modules/$moddir/admin/index.php?op=edit&item=".$post_itemid."",2,_MD_NOCATEGORYMATCH);
				exit();
			}
		} else {
			redirect_header(XOOPS_URL."/modules/$moddir/admin/index.php?op=edit&item=".$post_itemid."",2,_MD_NOCATEGORIESAVAILABLE);
			exit();
		}
		
		redirect_header("index.php?op=edit&amp;item=$post_itemid",2,_MD_ITEM_UPDATED);
		exit();
	}
	break;
default:
	listings();
	break;
}

function unique_events($array){
   //checks $array for duplicate values and returns an
   //array containing the keys of duplicates
   $count= array_intersect_assoc($array, array_flip( array_count_values($array)));
   $return = array();
   foreach($array as $key=>$value){
       if (in_array($value,$count)){
           $return[$value][]=$key;
       }
   }
   return $return;
} 
?>