<?php
// $Id: directories.php,v 0.18 2006/03/23 21:37:00 wtravel
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
include_once "../class/class.formimage.php";
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
$myts =& MyTextSanitizer::getInstance();
$eh = new ErrorHandler;
$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");

$moddir = $xoopsModule->getvar("dirname");

if (isset($_GET['dirid'])) {
    $get_dir = intval($_GET['dirid']);
}

function dirConfig()
{
	global $xoopsDB, $xoopsModule, $xoopsUser, $myts, $moddir;
	xoops_cp_header();
	adminmenu(1,_MD_A_DIRADMIN);
	echo "<h4>"._MD_DIRCONF."</h4>";
	//Get a list of directories and their properties (included number of categories and items?)
	$result = $xoopsDB->query("SELECT dirid, postfix, open, name FROM ".$xoopsDB->prefix("efqdiralpha1_dir")."");
	$numrows = $xoopsDB->getRowsNum($result);
    if ( $numrows > 0 ) {
		echo '<form action="directories.php?&op=changestatus" method="post" name="select_directories_form">';
		echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
		echo "<tr><th>&nbsp;</th><th>"._MD_DIRNAME."</th><th>"._MD_STATUS."</th><th>"._MD_TOTALCATS."</th><th>"._MD_ACTION."</th></tr>\n";
		while(list($dirid, $postfix, $open, $name) = $xoopsDB->fetchRow($result)) {
			$sql = "SELECT COUNT(*) FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE dirid='".$dirid."'";
			$result_countcats = $xoopsDB->query($sql);
			$numrows = $xoopsDB->getRowsNum($result_countcats);
			list($totalcats) = $xoopsDB->fetchRow($result_countcats);

			if ($open != '0') {
				$openyn = ""._MD_OPEN."";
			} else {
				$openyn = ""._MD_CLOSED."";
			}
					
			echo "<tr><td class=\"even\"><input type=\"checkbox\" name=\"select[]\" value=\"".$dirid."\" /></td><td class=\"even\">$name<a href=\"".XOOPS_URL."/modules/".$moddir."/admin/directories.php?op=moddir&dirid=$dirid\">"._MD_EDIT_BRACKETS."</a></td><td class=\"even\">$openyn</td><td class=\"even\">$totalcats</td><td class=\"even\">";
			echo "<a href=\"".XOOPS_URL."/modules/".$moddir."/admin/categories.php?dirid=$dirid\"><img src=\"".XOOPS_URL."/modules/".$moddir."/images/accessories-text-editor.png\" title=\""._MD_MANAGE_CATS."\" alt=\""._MD_MANAGE_CATS."\" /></a>";
			echo "</td></tr>\n";
		}
		echo '<tr><td colspan="5">'._MD_WITH_SELECTED.':&nbsp;';
		echo '<select name="fct" onChange="form.submit()">';
		echo '<option value="nothing">---</option>';
		echo '<option value="activate">'._MD_OPEN.'</option>';
		echo '<option value="inactivate">'._MD_CLOSE.'</option></select>';
		echo '</td></tr>';
		echo '</table>';
		echo '</form>';
	} else {
		echo "<p><span style=\"background-color: #E6E6E6; padding: 5px; border: 1px solid #000000;\">"._MD_NORESULTS_PLEASE_CREATE_DIRECTORY."</span></p>";
	}	
	echo "<br />";
	echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
	$form = new XoopsThemeForm(_MD_CREATE_NEWDIR, 'submitform', 'directories.php');
	$form->addElement(new XoopsFormText(_MD_DIRNAME, "dirname", 100, 150, ""), true);
	$form_diropen = new XoopsFormCheckBox(_MD_OPENYN, "open", 0);
	$form_diropen->addOption(1, _MD_YESNO);
	$form->addElement($form_diropen);
	$form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
	$form->addElement(new XoopsFormHidden("op", "newdir"));
	$form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
	$form->display();
	echo "</td></tr></table>";	
	xoops_cp_footer();
}

function modDir($dirid = '0')
{
	global $xoopsDB, $myts, $xoopsUser, $moddir, $xoopsModuleConfig;
	xoops_cp_header();
	adminmenu(1,_MD_A_DIRADMIN);
	echo "<h4>"._MD_EDITDIR."</h4>";
	echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
	$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("efqdiralpha1_dir")." WHERE dirid='".$dirid."'");
	$numrows = $xoopsDB->getRowsNum($result);
    if ( $numrows > 0 ) {
		while(list($dirid, $postfix, $open, $dirname, $descr, $pic) = $xoopsDB->fetchRow($result)) {
			if ($pic != "") {
				$picture = XOOPS_URL."/modules/$moddir/uploads/$pic";
			} else {
				$picture = "/images/dummy.png";
			}
			$form = new XoopsThemeForm(_MD_EDITDIRFORM, 'editform', 'directories.php');
			$form->setExtra('enctype="multipart/form-data"');
			$form->addElement(new XoopsFormText(_MD_DIRNAME, "dirname", 100, 150, $myts->makeTboxData4Show($dirname)));
			$form_diropen = new XoopsFormCheckBox(_MD_OPENYN, "open", $open);
			$form_diropen->addOption(1, _MD_DIROPENYN);
			$form->addElement($form_diropen);
			$form->addElement(new XoopsFormTextArea(_MD_DESCRIPTION, "descr", "$descr", 12, 50, ""));
			$form->addElement(new XoopsFormFile(_MD_SELECT_PIC, 'img', $xoopsModuleConfig['dirimagemaxsize']));
			$form->addElement(new XoopsFormImage(_MD_CURRENT_PIC, "current_image", null, "$picture", "", ""));
			$form->addElement(new XoopsFormButton('', 'submit', _MD_UPDATE, 'submit'));
			$form->addElement(new XoopsFormHidden("op", "update"));
			$form->addElement(new XoopsFormHidden("dirid", $dirid));
			$form->addElement(new XoopsFormHidden("open_current", $open));
			$form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
			$form->display();
		}
	}
	echo myTextForm("".XOOPS_URL."/modules/$moddir/admin/directories.php",_MD_CANCEL);
	echo "</td></tr></table>";
	xoops_cp_footer();
}

function updateDir()
{
	global $xoopsDB, $_POST, $myts, $eh, $moddir, $xoopsModuleConfig;
	if (isset ($_POST["dirid"]) ) {
		$p_dirid = intval($_POST['dirid']);
	} else {
		echo "no dirid";
		exit();
	}
    $p_dirname = $myts->makeTBoxData4Save($_POST["dirname"]);
	if (isset($_POST["open"])) {
		$p_open = $_POST["open"];
	} else {
		$p_open = '0';
	}
	if (isset ($_POST["descr"]) ) {
		$p_descr = $myts->makeTareaData4Save($_POST["descr"]);
	} else {
		$p_descr = "";
	}
	if ( $_POST['xoops_upload_file'][0] != "" ) {
		include_once '../class/class.uploader.php';
		//include_once XOOPS_ROOT_PATH.'/class/class.uploader.php';
		$uploader = new XoopsMediaUploader(XOOPS_ROOT_PATH.'/modules/'.$moddir.'/init_uploads', array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'), $xoopsModuleConfig['dirimagemaxsize'], $xoopsModuleConfig['dirimagemaxwidth'], $xoopsModuleConfig['dirimagemaxheight']);
		if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
			$filename = $uploader->getMediaName();
		} else {
			$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_dir")." SET descr = '".$p_descr."', open='".$p_open."', name='".$p_dirname."' WHERE dirid = '".$p_dirid."'";
			$xoopsDB->query($sql) or $eh->show("0013");
			redirect_header("directories.php?dirid=$p_dirid",2,_MD_DIR_UPDATED);
			exit();
		}
		$uploader->setPrefix('efqdir');
		if ($uploader->upload()) {
			$savedfilename = $uploader->getSavedFileName();
			echo $uploader->getErrors();
			$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_dir")." SET img = '".$savedfilename."', descr = '".$p_descr."', open='".$p_open."', name='".$p_dirname."' WHERE dirid = '".$p_dirid."'";
			$xoopsDB->query($sql) or $eh->show("0013");
	
			//Rename the uploaded file to the same name in a different location that does not have 777 rights or 755. 			
			rename("".XOOPS_ROOT_PATH."/modules/$moddir/init_uploads/".$savedfilename."", "".XOOPS_ROOT_PATH."/modules/$moddir/uploads/".$savedfilename."");
			//Delete the uploaded file from the initial upload folder if it is still present in that folder.
			if(file_exists("".XOOPS_ROOT_PATH."/modules/$moddir/init_uploads/".$savedfilename."")) {
				unlink("".XOOPS_ROOT_PATH."/modules/$moddir/init_uploads/".$savedfilename."");
			}
			redirect_header("directories.php?op=moddir&dirid=$p_dirid",2,_MD_DIR_UPDATED);
			exit();
		}	else {
			echo $uploader->getErrors();
			$sql = "UPDATE ".$xoopsDB->prefix("efqdiralpha1_dir")." SET descr = '".$p_descr."', open='".$p_open."', name='".$p_dirname."' WHERE dirid = '".$p_dirid."'";
			$xoopsDB->query($sql) or $eh->show("0013");
			redirect_header("directories.php?dirid=$p_dirid",2,_MD_DIR_UPDATED);
			exit();
		}
	}
	redirect_header("directories.php?dirid=$p_dirid",2,_MD_DIR_NOT_UPDATED);
}

function changeStatus($status=0)
{
	global $xoopsDB, $eh, $moddir;
	$select = $_POST['select'];
	$users = '';
	$count = 0;
	$directories = '';
	$countselect = count($select);
	if ($countselect > 0) {
		foreach ($select as $directory) {
			if ($count > 0) {
				$directories .= ','.$directory;	
			} else {
				$directories .= $directory;
			}
			$count++;
		}
		$sql = sprintf("UPDATE %s SET open=".$status." WHERE dirid IN (%s)", $xoopsDB->prefix("efqdiralpha1_dir"), $directories);
		$xoopsDB->query($sql) or $eh->show("0013");
		redirect_header("directories.php",2,_MD_DIR_UPDATED);
		exit();
	} else {
		redirect_header("directories.php",2,_MD_DIR_NOT_UPDATED);
	}
}

function newDir()
{
	global $xoopsDB, $_POST, $myts, $eh;
    if (isset($_POST["postfix"])) {
		$p_postfix = $_POST["postfix"];
	} else {
		$p_postfix = "";
	}
	$p_dirname = $_POST["dirname"];
	if (isset($_POST["open"])) {
		$p_open = $_POST["open"];
	} else {
		$p_open = 0;
	}
	$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_dir")."_dirid_seq");
	$sql = sprintf("INSERT INTO %s (dirid, postfix, open, name) VALUES (%u, '%s', '%s', '%s')", $xoopsDB->prefix("efqdiralpha1_dir"), $newid, $p_postfix, $p_open, $p_dirname);
	$xoopsDB->query($sql) or $eh->show("0013");
	$db_dirid = $xoopsDB->getInsertId();
	redirect_header("directories.php?op=moddir&dirid=$db_dirid",2,_MD_DIR_SAVED);
	exit();
}

if(!isset($_POST['op'])) {
	$op = isset($_GET['op']) ? $_GET['op'] : 'dirConfig';
} else {
	$op = $_POST['op'];
}
switch ($op) {
case "edit":
	editDir();
	break;
case "update":
	updateDir();
	break;
case "changestatus":
	if (isset($_POST['fct'])) {
		$fct = $_POST['fct'];
		if ($fct == 'activate') {
			$newstatus = 1;
		} else if ($fct == 'inactivate') {
			$newstatus = 0;
		}
	}
	changeStatus($newstatus);
	break;
case "newdir":
	newDir();
	break;
case "moddir":
	modDir($get_dir);
	break;
default:
	dirConfig();
	break;
}
?>