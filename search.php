<?php
// $Id: search.php,v 0.18 2006/03/23 21:37:00 wtravel
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
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";
include_once XOOPS_ROOT_PATH."/class/module.errorhandler.php";
$myts =& MyTextSanitizer::getInstance();
$mytree = new XoopsTree($xoopsDB->prefix("links_cat"),"cid","pid");
$eh = new ErrorHandler;

$moddir = $xoopsModule->getVar('dirname');
include XOOPS_ROOT_PATH."/header.php";

if (isset($_GET['catid'])) {
        $get_cid = intval($_GET['cid']);
} else {
        $get_cid = "1";
}
if (isset($_GET['dirid'])) {
        $get_dirid = intval($_GET['dirid']);
} else {
        $get_dirid = "1";
}
if(isset($_GET['orderby'])) {
        $orderby = convertorderbyin($_GET['orderby']);
} else {
        $orderby = "title ASC";
}
if(isset($_GET['page'])) {
        $get_page = intval($_GET['page']);
} else {
        $get_page = 1;
}
$xoopsOption['template_main'] = 'efqdiralpha1_search.html';
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
$lang_adv_search = sprintf(_MD_SEARCH_ADV,$get_dirid);

ob_start();
	$searchform = "<form action=\"search.php\" name=\"search\" id=\"search\" method=\"get\">";
	$searchform .= "<input type=\"hidden\" name=\"dirid\" value=\"".$get_dirid."\" /><input type=\"text\" name=\"q\" size=\"40\" maxsize=\"150\" value=\"\" /><input type=\"submit\" id=\"submit\" value=\""._MD_SEARCH."\" />".$lang_adv_search."</form>";
	echo $searchform;
	if (!empty($_GET['q'])) {
		//get search results from query
		if(isset($_GET['q'])) {
			$querystring = mysql_real_escape_string($myts->stripSlashesGPC($_GET['q']));
			//echo $querystring."<br />";
		} else {
			redirect_header(XOOPS_URL."/modules/$moddir/search.php",2,_MD_NO_SEARCH_STRING_SELECTED);
		}
		$poscount = substr_count($querystring, '"')/2;
		$specialarr = array();
		for ($i=0; $i<$poscount; $i++) {
			$start = strpos($querystring, '"',0);
			$end = strpos($querystring, '"',$start+1);
			if ($end != false) {
				$specialstring = ltrim(substr($querystring, $start, $end-$start),'"');
				$specialarr[] = $specialstring;
				$querystring = ltrim(substr_replace($querystring, "", $start, $end-$start+1));
			} else {
				$querystring = ltrim(substr_replace($querystring, "", $start, 1));
			}
		}
		$queryarr = split(' ', $querystring);
		$queryarr = array_merge($specialarr, $queryarr);
		$emptyarr[] = "";
		$querydiff = array_diff($queryarr, $emptyarr);
		
		$limit = $xoopsModuleConfig['searchresults_perpage'];
		$offset = ($get_page - 1) * $limit;
		
		$andor = "AND";
		$searchresults = mod_search($querydiff, $andor, $limit, $offset);
		$maxpages = 10;
		$maxcount = 30;
		
		$count_results = mod_search_count($querydiff, $andor, $maxcount, 0);
		$count_pages = 0;
		//Calculate the number of result pages.
		if ($count_results > $limit) {
			$count_pages = ceil($count_results/$limit);
		}
		$pages_text = "";
		$pages_text .= $count_results ." "._MD_LISTINGS_FOUND."<br />";

		if ($count_pages >= 2) {
			$pages_text .= "<a href=\"search.php?q=".$querystring."&page=1\">1</a>";
		}
		for ($i=1; $i < $count_pages; $i++) {
			$page = $i + 1;
			$pages_text .= " - <a href=\"search.php?q=".$querystring."&page=".$page."\">".$page."</a>";
		}
			
		echo "<div class=\"itemTitleLarge\">"._MD_SEARCHRESULTS_TITLE."</div><br />";
		if ($searchresults == 0) {
			echo "<div class=\"itemTitle\">"._MD_NORESULTS."</div>"; 
		} else {
			foreach ($searchresults as $result) {
				echo "<div class=\"itemTitle\"><a href=\"".$result['link']."\">".$result['title']."</a></div><div class=\"itemText\">".$result['description']."</div><hr />";
			}
		}
		echo "<br />";
		echo $pages_text; 
	}
	$xoopsTpl->assign('search_page', ob_get_contents());
ob_end_clean();


include XOOPS_ROOT_PATH.'/footer.php';

function mod_search($queryarray, $andor, $limit, $offset) {
	global $xoopsDB, $eh;
	$sql = "SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM ".$xoopsDB->prefix("efqdiralpha1_data")." d RIGHT JOIN ".$xoopsDB->prefix("efqdiralpha1_items")." i ON (d.itemid=i.itemid) LEFT JOIN ".$xoopsDB->prefix("efqdiralpha1_item_text")." t ON (i.itemid=t.itemid) WHERE i.status='2'";
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $queryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((d.value LIKE '%$queryarray[0]%' OR i.title LIKE '%$queryarray[0]%' OR t.description LIKE '%$queryarray[0]%')";
		for ($i=1;$i<$count;$i++) {
			$sql .= " $andor ";
			$sql .= "(d.value LIKE '%$queryarray[$i]%' OR i.title LIKE '%$queryarray[$i]%' OR t.description LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY i.created DESC";
	
	$result = $xoopsDB->query($sql,$limit,$offset) or $eh->show("0013");
	$num_results = $xoopsDB->getRowsNum($result);
    if (!$result) {
        return 0;
    } else if ($num_results == 0) {
		return 0;
	} else {
		$ret = array();
		$i = 0;
		while($myrow = $xoopsDB->fetchArray($result)){
			$ret[$i]['image'] = "images/home.gif";
			$ret[$i]['link'] = "listing.php?item=".$myrow['itemid']."";
			$ret[$i]['title'] = $myrow['title'];
			$ret[$i]['description'] = $myrow['description'];
			$ret[$i]['time'] = $myrow['created'];
			$ret[$i]['uid'] = $myrow['uid'];
			$i++;
		}
		return $ret;
	}
}

function mod_search_count($queryarray, $andor, $limit, $offset=0) {
	global $xoopsDB, $eh;
	$count = 0;
	$sql = "SELECT COUNT(DISTINCT i.itemid) FROM ".$xoopsDB->prefix("efqdiralpha1_data")." d, ".$xoopsDB->prefix("efqdiralpha1_items")." i LEFT JOIN ".$xoopsDB->prefix("efqdiralpha1_item_text")." t ON (i.itemid=t.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $queryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((d.value LIKE '%$queryarray[0]%' OR i.title LIKE '%$queryarray[0]%' OR t.description LIKE '%$queryarray[0]%')";
		for ($i=1;$i<$count;$i++) {
			$sql .= " $andor ";
			$sql .= "(d.value LIKE '%$queryarray[$i]%' OR i.title LIKE '%$queryarray[$i]%' OR t.description LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$result = $xoopsDB->query($sql) or $eh->show("0013");
	list($count) = $xoopsDB->fetchRow($result);
	return $count;
}
?>