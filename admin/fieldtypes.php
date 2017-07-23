<?php
// $Id: fieldtypes.php,v 0.18 2006/03/23 21:37:00 wtravel
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
include_once XOOPS_ROOT_PATH.'/include/xoopscodes.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
include_once '../class/class.fieldtype.php';
include_once '../class/class.directory.php';
$myts =& MyTextSanitizer::getInstance();
$eh = new ErrorHandler;
$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");
$mytree2 = new XoopsTree($xoopsDB->prefix("efqdiralpha1_fieldtypes"),"typeid",0);

$moddir = $xoopsModule->getvar("dirname");

if (isset($_GET['dirid'])) {
    $get_dirid = intval($_GET['dirid']);
} else {
	$get_dirid = "0";
}
if (isset($_GET['typeid'])) {
    $get_typeid = intval($_GET['typeid']);
}
$datatypes = array('0' => '---',
					'textbox' => _MD_FIELDNAMES_TEXTBOX,
					'textarea' => _MD_FIELDNAMES_TEXTAREA,
					'dhtml' => _MD_FIELDNAMES_DHTMLTEXTAREA,
					'select' => _MD_FIELDNAMES_SELECT,
					'checkbox' => _MD_FIELDNAMES_CHECKBOX,
					'radio' => _MD_FIELDNAMES_RADIO,
					'yesno' => _MD_FIELDNAMES_YESNO,
					'date' => _MD_FIELDNAMES_DATE,
					'datetime' => _MD_FIELDNAMES_DATETIME,
					//'gmap' => _MD_FIELDNAMES_GMAP,
					//'address' => _MD_FIELDNAMES_ADDRESS, //EDIT-RC10
					//'locationmap' => _MD_FIELDNAMES_LOCATIONMAP,
					'rating' => _MD_FIELDNAMES_RATING,
					'url' => _MD_FIELDNAMES_URL
					//'gallery' => _MD_FIELDNAMES_GALLERY
					);


function fieldtypesConfig()
{
	global $xoopsDB, $xoopsModule, $xoopsUser, $get_dirid, $moddir, $myts, $eh, $datatypes,$mytree2;
	xoops_cp_header();
	adminmenu(3,_MD_A_FTYPESADMIN);
	
	if ($get_dirid == "0") {
		$countopendirs = countOpenDirectories();
		$get_dirid = $countopendirs > 0 ? $countopendirs : 0;
		if ($get_dirid == 0) {
			$directoryHandler = new efqDirectoryHandler();
			//xoops_cp_header();
			//adminmenu(3,_MD_MANAGE_CATS);
			echo "<h4>"._MD_SELECTDIRECTORY."</h4>";
			echo "<table width='100%' border='0' cellspacing='1' class='outer'>";	
			echo "<table width='100%' border='0' cellspacing='1'><tr><td>";
			$form = new XoopsThemeForm(_MD_SELECTDIRFORM, 'submitform', 'fieldtypes.php', "get");
			$directories_array = $directoryHandler->getAllDirectoryTitles(); 
			$ele_select = new XoopsFormSelect(_MD_FORWHICHDIRECTORY_MANAGE_FIELDTYPES, 'dirid');
			$ele_select->addOptionArray($directories_array);
			$form->addElement($ele_select);
			$form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
			$form->display();
			echo "</td></tr></table>";	
			xoops_cp_footer();
			exit();
		}
	}
	
	echo "<h4>"._MD_FTYPECONF."</h4>";
	echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
	$fieldtype_handler = new efqFieldTypeHandler();
	$fieldtypes = $fieldtype_handler->getByDir($get_dirid);
	//$result = $xoopsDB->query("SELECT typeid, title, fieldtype, descr, ext, activeyn FROM ".$xoopsDB->prefix("efqdiralpha1_fieldtypes")." ORDER BY fieldtype ASC") or $eh->show("0013");
	if ($fieldtypes) {
		$numrows = count($fieldtypes);
		echo "<tr><th>"._MD_TITLE."</th><th>"._MD_TYPE."</th><th>"._MD_EXT."</th><th>"._MD_ACTIVE."</th></tr>\n";
	    if ( $numrows > 0 ) {
			foreach($fieldtypes as $fieldtype) {
		    	if ($fieldtype['activeyn'] != '0') {
					$statusyn = ""._MD_YES."";
				} else {
					$statusyn = ""._MD_NO."";
				}
				echo "<tr><td class=\"even\" valign=\"top\"><a href=\"".XOOPS_URL."/modules/".$moddir."/admin/fieldtypes.php?op=view&typeid=".$fieldtype['typeid']."\">".$fieldtype['title']."</a></td><td class=\"even\" valign=\"top\">".$fieldtype['fieldtype']."</td><td class=\"even\" valign=\"top\">".$fieldtype['ext']."</td><td class=\"even\" valign=\"top\">".$statusyn."</td>";
				echo "</td></tr>\n";
		    }
		} else {
			echo "<tr><td>"._MD_NORESULTS."</td></tr>";
		}	
	} else {
		echo "<tr><td>"._MD_NORESULTS."</td></tr>";
	}
   	echo "</table>";
 	echo "<br />";
	echo "<h4>"._MD_CREATE_NEWFTYPE."</h4>";
	echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
	$form = new XoopsThemeForm(_MD_NEWFTYPEFORM, 'submitform', 'fieldtypes.php');
	
	$form->addElement(new XoopsFormText(_MD_TITLE, "title", 100, 150, ""), true);
	//TO DO: change type field to drop down field, based on available types.
	$element_select = new XoopsFormSelect(_MD_FIELDTYPE, 'field_type');
	$element_select->addOptionArray($datatypes);
	//$form->addElement($type_select);
	$form->addElement($element_select);
	$ext_tray = new XoopsFormElementTray(_MD_EXT, "");
	$ext_text = new XoopsFormText("", "ext", 80, 150, "");
	$ext_text->setExtra('disabled=true');
	$ext_text->setExtra('style=\'background-color:lightgrey\'');
	$ext_button = new XoopsFormLabel("","<INPUT type=\"button\" value=\""._MD_SET_EXT."\", onClick=\"openExtManager('submitform','".XOOPS_URL."/modules/".$moddir."/admin/extensionmanager.php','field_type', '"._MD_SELECT_FORMTYPE."')\">");
	$ext_tray->addElement($ext_text);
	$ext_tray->addElement($ext_button);
	$form->addElement($ext_tray);
	$form->addElement(new XoopsFormTextArea(_MD_DESCRIPTION, "descr", "", 8, 50, ""), true);
	$form_txtactive = new XoopsFormCheckBox(_MD_ACTIVE, "status", 0);
	$form_txtactive->addOption(1, _MD_YESNO);
	$form->addElement($form_txtactive);
	$form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
	$form->addElement(new XoopsFormHidden("op", "addFieldtype"));
	$form->addElement(new XoopsFormHidden("dirid", $get_dirid));
	$form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
	$form->display();
	
	//Javascript function to check if field type is selected. If not, then warn the user. Otherwise
	//open the extension manager window.
	$js = "";
	$js .= "\n<!-- Start Extension Manager JavaScript //-->\n<script type='text/javascript'>\n<!--//\n";
	$js .= "function openExtManager(formname,url,ele,warning) {\n";
	$js .= "myform = window.document.submitform;\n";
	$js .= "var typeid = myform.field_type.value;\n";
	$js .= "if (typeid == 0) {
		alert([warning]);
	} else {
		window.open([url],'ext_window','width=600,height=450');
	}\n";
	$js .="}\n";
	$js .= "//--></script>\n<!-- End Extension Manager JavaScript //-->\n";
	echo $js;
	
	echo "</td></tr></table>";	
	xoops_cp_footer();
}

function viewFieldtype()
{
	global $xoopsDB, $mytree, $mytree2, $xoopsUser, $get_typeid, $moddir, $eh, $datatypes;
	xoops_cp_header();
	adminmenu(3,_MD_A_FTYPESADMIN);
	echo "<h4>"._MD_VIEW_FIELDTYPE."</h4>";
	echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
	$result = $xoopsDB->query("SELECT typeid, dirid, title, fieldtype, descr, ext, activeyn FROM ".$xoopsDB->prefix("efqdiralpha1_fieldtypes")." WHERE typeid='".$get_typeid."'") or $eh->show("0013");
	$numrows = $xoopsDB->getRowsNum($result);
    if ( $numrows > 0 ) {
		while(list($typeid, $dirid, $title, $fieldtype, $descr, $ext, $activeyn) = $xoopsDB->fetchRow($result)) {
			$form = new XoopsThemeForm(_MD_EDITFTYPEFORM, 'submitform', 'fieldtypes.php');
			$form->addElement(new XoopsFormText(_MD_TITLE, "title", 100, 150, "$title"), true);
			//TO DO: change type field to drop down field, based on available types.
			$element_select = new XoopsFormSelect(_MD_FIELDTYPE, 'field_type', $fieldtype);
			$element_select->addOptionArray($datatypes);
		
			//$form->addElement($type_select);
			$form->addElement($element_select);
			$ext_tray = new XoopsFormElementTray(_MD_EXT, "");
			$ext_text = new XoopsFormText("", "ext", 80, 150, "$ext");
			$ext_text->setExtra('style=\'background-color:lightgrey\'');
			$ext_button = new XoopsFormLabel("","<INPUT type=\"button\" value=\""._MD_SET_EXT."\", onClick=\"openExtManager('submitform','".XOOPS_URL."/modules/".$moddir."/admin/extensionmanager.php','field_type', '"._MD_SELECT_FORMTYPE."')\">");
			$ext_tray->addElement($ext_text);
			$ext_tray->addElement($ext_button);
			$form->addElement($ext_tray);
			$form->addElement(new XoopsFormTextArea(_MD_DESCRIPTION, "descr", "$descr", 8, 50, ""), true);
			$form_txtactive = new XoopsFormCheckBox(_MD_ACTIVE, "status", $activeyn);
			$form_txtactive->addOption(1, _MD_YESNO);
			$form->addElement($form_txtactive);
			$form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
			$form->addElement(new XoopsFormHidden("op", "editFieldtype"));
			$form->addElement(new XoopsFormHidden("typeid", $get_typeid));
			$form->addElement(new XoopsFormHidden("dirid", $dirid));
			$form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
			$form->display();
			
			
			
			//Javascript function to check if field type is selected. If not, then warn the user. Otherwise
			//open the extension manager window.
			$js = "";
			$js .= "\n<!-- Start Extension Manager JavaScript //-->\n<script type='text/javascript'>\n<!--//\n";
			$js .= "function openExtManager(formname,url,ele,warning) {\n";
			$js .= "myform = window.document.submitform;\n";
			$js .= "var typeid = myform.field_type.value;\n";
			$js .= "if (typeid == 0) {
				alert([warning]);
			} else {
				window.open([url],'ext_window','width=600,height=450');
			}\n";
			$js .="}\n";
			$js .= "//--></script>\n<!-- End Extension Manager JavaScript //-->\n";
			echo $js;
		}
	}
	echo "</td></tr></table>";
	//echo "<form name=\"deleteFieldTypeForm\" action=\"\"
	xoops_cp_footer();
}

function addFieldtype()
{
	global $xoopsDB, $_POST, $myts, $eh;
    $p_title = $myts->makeTboxData4Save($_POST["title"]);
	$p_fieldtype = $_POST["field_type"];
	$p_descr = $myts->makeTareaData4Save($_POST["descr"]);
	if (isset($_POST["ext"])) {
		$p_ext = $_POST["ext"];
	} else {
		$p_ext = "";
	}
	if (isset($_POST["status"])) {
		$p_status = intval($_POST["status"]);
	} else {
		$p_status = 0;
	}
	if (isset($_POST["dirid"])) {
		$p_dirid = intval($_POST["dirid"]);
	} else {
		$p_dirid = 0;
	}
	$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_fieldtypes")."_typeid_seq");
	$sql = sprintf("INSERT INTO %s (typeid, dirid, title, fieldtype, descr, ext, activeyn) VALUES (%u, '%s', '%s', '%s', '%s', '%s', '%s')", $xoopsDB->prefix("efqdiralpha1_fieldtypes"), $newid, $p_dirid, $p_title, $p_fieldtype, $p_descr, $p_ext, $p_status);
	$xoopsDB->query($sql) or $eh->show("0013");
	redirect_header("fieldtypes.php?dirid=$p_dirid",2,_MD_SAVED);
	exit();
}

function editFieldtype()
{
	global $xoopsDB, $_POST, $myts, $eh;
	if (isset ($_POST["typeid"]) ) {
		$p_typeid = intval($_POST["typeid"]);
	} else {
		exit();
	}
	$p_title = $myts->makeTboxData4Save($_POST["title"]);
	$p_fieldtype = $_POST["field_type"];
	$p_descr = $myts->makeTareaData4Save($_POST["descr"]);
	if (isset($_POST["ext"])) {
		$p_ext = $_POST["ext"];
	} else {
		$p_ext = "";
	}
	if (isset($_POST["status"])) {
		$p_status = intval($_POST["status"]);
	} else {
		$p_status = 0;
	}
	$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_fieldtypes")." SET title = '$p_title', fieldtype='$p_fieldtype', ext='$p_ext', activeyn='$p_status' WHERE typeid = $p_typeid";
	$xoopsDB->query($sql) or $eh->show("0013");
	redirect_header("fieldtypes.php?op=view&typeid=$p_typeid",2,_MD_SAVED);
	exit();
}

function newCat()
{
	global $xoopsDB, $myts, $eh;
	if (isset ($_POST["dirid"]) ) {
		$p_dirid = intval($_POST["dirid"]);
	} else {
		exit();
	}
    $p_title = $myts->makeTboxData4Save($_POST["title"]);
	$p_active = intval($_POST["active"]);
	$p_pid = intval($_POST["pid"]);
	$p_allowlist = intval($_POST["allowlist"]);
	$p_showpopular = intval($_POST["showpopular"]);
	if (isset ($_POST["descr"]) ) {
		$p_descr = $myts->makeTareaData4Save($_POST["descr"]);
	} else {
		$p_descr = "";
	}
	$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_cat")."_cid_seq");
	$sql = sprintf("INSERT INTO %s (cid, dirid, title, active, pid) VALUES (%u, %u, '%s', %u, %u)", $xoopsDB->prefix("efqdiralpha1_cat")	, $newid, $p_dirid, $p_title, $p_active, $p_pid);
	//echo $sql;
	$xoopsDB->query($sql) or $eh->show("0013");
	if ($newid == 0) {
		$cid = $xoopsDB->getInsertId();
	}
	$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_cat_txt")."_txtid_seq");
	$sql2 = sprintf("INSERT INTO %s (txtid, cid, text, active, created) VALUES (%u, %u, '%s', %u, '%s')", $xoopsDB->prefix("efqdiralpha1_cat_txt"), $newid, $cid, $p_descr, '1', time());
	//echo $sql2;
	$xoopsDB->query($sql2) or $eh->show("0013");
	redirect_header("categories.php?op=edit&cid=$newid",0,_MD_CAT_UPDATED);
	exit();
}

if(!isset($_POST['op'])) {
	$op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
	$op = $_POST['op'];
}
switch ($op) {
case "view":
	viewFieldtype();
	break;
case "editFieldtype":
	editFieldtype();
	break;
case "addFieldtype":
	addFieldtype();
	break;
default:
	fieldtypesConfig();
	break;
}

function getCatOverview()
{
	global $xoopsDB, $myts, $eh, $mytree, $get_dirid, $moddir;
	$mainresult = $xoopsDB->query("SELECT cid, title, active, pid FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE dirid='".$get_dirid."' AND pid='0'");
	$numrows = $xoopsDB->getRowsNum($mainresult);
	$output= "";
    if ( $numrows > 0 ) {
		$output = "<th>"._MD_CATTITLE."</th><th>"._MD_ACTIVEYN."</th><th>"._MD_PARENTCAT."</th>\n";
		$brench = 0;
		$tab = "";
		while(list($cid, $title, $activeyn, $pid) = $xoopsDB->fetchRow($mainresult)) {
			$output .= "<tr><td>".$tab."<a href=\"".XOOPS_URL."/modules/$moddir/admin/categories.php?op=edit&cid=$cid\">".$title."</a></td><td>".$activeyn."</td></tr>\n";
			$output .= getChildrenCategories($cid);
		}
	} else {
		$output = ""._MD_NORESULTS."";
	}
	return $output;
}

function getChildrenCategories($childid="0", $level="1")
{
	global $xoopsDB, $myts, $eh, $mytree;
	$firstchildcats = $mytree->getFirstChildId($childid);
	$tab = "";
	$output = "";
	$plus = "<img src=\"".XOOPS_URL."\images\arrow.jpg\">";
	for ($i=0; $i <$level; $i++)
	{
		$tab .= "&nbsp;&nbsp;";
	}
	foreach ($firstchildcats as $childid) {
		$childresult = $xoopsDB->query("SELECT cid, title, active, pid FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE pid='".$childid."'");
		//$childresult = $xoopsDB->query("SELECT cid, title, active, pid FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE dirid='".$dirid."' AND pid='".$childid."'");
		$numrows = $xoopsDB->getRowsNum($childresult);
		if ( $numrows > 0 ) {
			while(list($cid, $title, $activeyn, $pid) = $xoopsDB->fetchRow($childresult)) {
				$output .= "<tr><td>".$tab."".$plus."</td><td>".$title."</td><td>".$activeyn."</td></tr>\n";
				$newlevel = $level++;
				$output .= getChildrenCategories($cid, $newlevel);
			}
		}
	}
	return $output;			
}
?>