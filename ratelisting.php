<?php
// $Id: ratelisting.php,v 0.18 2006/03/23 21:37:00 wtravel
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
include_once XOOPS_ROOT_PATH."/class/module.errorhandler.php";
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
$moddir = $xoopsModule->getvar("dirname");

if (!empty($_POST['submit'])) {
	$eh = new ErrorHandler; //ErrorHandler object
	if(empty($xoopsUser)){
		$ratinguser = 0;
	}else{
		$ratinguser = $xoopsUser->getVar('uid');
	}

   	//Make sure only 1 anonymous from an IP in a single day.
   	$anonwaitdays = $xoopsModuleConfig['anonvotes_waitdays'];
   	$ip = getenv("REMOTE_ADDR");
	$p_itemid = intval($_POST['item']);
	$p_catid = intval($_POST['catid']);
	$p_rating = intval($_POST['rating']);

   	// Check if Rating is Null
   	if ($p_rating=="--") {
		redirect_header("ratelisting.php?catid=".$p_catid."&amp;item=".$itemid."",2,_MD_NORATING);
		exit();
   	}

   	// Check if Link POSTER is voting (UNLESS Anonymous users allowed to post)
   	if ($ratinguser != 0) {
       	$result=$xoopsDB->query("select submitter from ".$xoopsDB->prefix("efqdiralpha1_items")." where itemid=$p_itemid");
       	while(list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
       		if ($ratinguserDB == $ratinguser) {
				redirect_header("index.php",4,_MD_CANTVOTEOWN);
				exit();
          	}
       	}

    	// Check if REG user is trying to vote twice.
   		$result=$xoopsDB->query("select ratinguser from ".$xoopsDB->prefix("efqdiralpha1_votedata")." where itemid=$p_itemid");
       	while(list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
       		if ($ratinguserDB == $ratinguser) {
				redirect_header("index.php",4,_MD_VOTEONCE2);
				exit();
           	}
      	}

   	} else {

   		// Check if ANONYMOUS user is trying to vote more than once per day.
		$yesterday = (time()-(86400 * $anonwaitdays));
       	$result=$xoopsDB->query("select count(*) FROM ".$xoopsDB->prefix("efqdiralpha1_votedata")." WHERE itemid=$p_itemid AND ratinguser=0 AND ratinghostname = '$ip' AND ratingtimestamp > $yesterday");
   		list($anonvotecount) = $xoopsDB->fetchRow($result);
   		if ($anonvotecount > 0) {
			redirect_header("index.php",4,_MD_VOTEONCE2);
			exit();
       	}
   	}
	if($rating > 10){
		$rating = 10;
	}

    //Add to Line Item Rate to DB.
	$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_votedata")."_ratingid_seq");
	$datetime = time();
	$sql = sprintf("INSERT INTO %s (ratingid, itemid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES (%u, %u, %u, %u, '%s', %u)", $xoopsDB->prefix("efqdiralpha1_votedata"), $newid, $p_itemid, $ratinguser, $p_rating, $ip, $datetime);
	$xoopsDB->query($sql) or $eh->show("0013");

	//Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.
    updaterating($p_itemid);
	if (!empty($_POST['catid'])) {
		$p_catid = intval($_POST['catid']);
	} else {
		$p_catid = 0;
	}
	if (!empty($_POST['dirid'])) {
		$p_dirid = intval($_POST['dirid']);
	} else {
		$p_dirid = 0;
	}
	if ($p_dirid == 0) {
		$dirid = getDirIdFromItem($p_itemid);
	} else {
		$dirid = $p_dirid;
	}
	$ratemessage = _MD_VOTEAPPRE."<br />".sprintf(_MD_THANKURATE,$xoopsConfig['sitename']);
	redirect_header("index.php?dirid=".$dirid."",2,$ratemessage);
	exit();

} else {

	$xoopsOption['template_main'] = 'efqdiralpha1_ratelisting.html';
	include XOOPS_ROOT_PATH."/header.php";
	if (isset($_GET['item'])) {
		$get_itemid = intval($_GET['item']);
	} else {
		$get_itemid = '0';
	}
	if (isset($_GET['catid'])) {
		$get_catid = intval($_GET['catid']);
	} else {
		$get_catid = '0';
	}
	
	$result=$xoopsDB->query("select title from ".$xoopsDB->prefix("efqdiralpha1_listings")." where itemid=$get_itemid");
	list($title) = $xoopsDB->fetchRow($result);
	$xoopsTpl->assign('listing', array('itemid' => $get_itemid, 'catid' => $get_catid, 'title' => $myts->htmlSpecialChars($title)));
	$xoopsTpl->assign('moddir', $moddir);
	$xoopsTpl->assign('lang_voteonce', _MD_VOTEONCE);
	$xoopsTpl->assign('lang_ratingscale', _MD_RATINGSCALE);
	$xoopsTpl->assign('lang_beobjective', _MD_BEOBJECTIVE);
	$xoopsTpl->assign('lang_donotvote', _MD_DONOTVOTE);
	$xoopsTpl->assign('lang_rateit', _MD_RATEIT);
	$xoopsTpl->assign('lang_cancel', _CANCEL);
	include XOOPS_ROOT_PATH.'/footer.php';
}
?>