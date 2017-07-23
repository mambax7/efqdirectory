<?php
// $Id: efqdiralpha1_menu.php 2 2008-01-27 18:16:55Z wtravel $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
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
/******************************************************************************
 * Function: b_efqdiralpha1_menu_show
 * Input   : $options[0] = date for the most recent links
 *                    hits for the most popular links
 *           $block['content'] = The optional above content
 *           $options[1]   = How many reviews are displayes
 * Output  : Returns the desired most recent or most popular links
 ******************************************************************************/
 
function b_efqdiralpha1_menu_show($options) {
	global $xoopsDB, $xoopsModule, $eh;
	$info =  dirname(__FILE__);
	$split = split("[\]",$info);
	$count = count($split) - 2;
	$moddir = $split[$count];
	$block = array();
	$block['lang_dirmenu'] = _MB_EFQDIR_MENU;
	$block['moddir'] = $moddir;
	$myts =& MyTextSanitizer::getInstance();
	$result = $xoopsDB->query("SELECT dirid, name, descr FROM ".$xoopsDB->prefix("efqdiralpha1_dir")." WHERE open='1' ORDER BY name") or $eh->show("0013");
	while($myrow = $xoopsDB->fetchArray($result)){
		$directory = array();
		$name = $myts->makeTboxData4Show($myrow["name"]);
		$directory['dirid'] = $myrow['dirid'];
		$directory['name'] = $name;
		$directory['descr'] = $myrow['descr'];
		$block['directories'][] = $directory;
	}
	$sublink = array();
	return $block;
}

function b_efqdiralpha1_menu_edit($options) {
	$form = ""._MB_EFQDIR_DISP."&nbsp;";
	$form .= "<input type='hidden' name='options[]' value='";
	if($options[0] == "date"){
		$form .= "date'";
	}else {
		$form .= "hits'";
	}
	$form .= " />";
	$form .= "<input type='text' name='options[]' value='".$options[1]."' />&nbsp;"._MB_EFQDIR_LISTINGS."";
	$form .= "&nbsp;<br>"._MB_EFQDIR_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[2]."' />&nbsp;"._MB_EFQDIR_LENGTH."";
	return $form;
}
?>