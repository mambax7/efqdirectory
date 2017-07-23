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
//  Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
// ------------------------------------------------------------------------- //
include "header.php";
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
include_once XOOPS_ROOT_PATH."/class/xoopstree.php";
include_once XOOPS_ROOT_PATH."/include/xoopscodes.php";
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
include_once "class/class.datafieldmanager.php";
include_once "class/class.couponhandler.php";
include_once "class/class.efqtree.php";
include_once "class/class.listing.php";

$datafieldmanager = new efqDataFieldManager();
$moddir = $xoopsModule->getvar("dirname");
$eh = new ErrorHandler;
$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");
$efqtree = new efqTree($xoopsDB->prefix("efqdiralpha1_cat"),"cid","pid");

//Check if a category is selected.
if (!empty($_GET['catid'])) {
	$get_catid = intval($_GET['catid']);
} else {
	$get_catid = '0';
}

//Check if a directory is selected.
if (!empty($_GET['dirid'])) {
	$get_dirid = intval($_GET['dirid']);
} else {
	$get_dirid = '0';
}

if ($get_dirid == '0' && $get_catid == '0') {
	//Show an overview of directories with directory title, image and description for each directory.
	$result=$xoopsDB->query("SELECT dirid, name, descr, img FROM ".$xoopsDB->prefix("efqdiralpha1_dir")." WHERE open = '1' ORDER BY name") or  $eh->show("0013");
	$num_results = $xoopsDB->getRowsNum($result);
	if ($num_results == 1) {
		if ($xoopsModuleConfig['autoshowonedir'] == 1) {
			while(list($dirid, $name, $descr, $img) = $xoopsDB->fetchRow($result)) {
				$get_dirid = $dirid;
			}				
		} else {
			$xoopsOption['template_main'] = 'efqdiralpha1_directories.html';
			include XOOPS_ROOT_PATH."/header.php";
			$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
			$xoopsTpl->assign('lang_directories', _MD_DIRECTORIES_HEADER);
			$xoopsTpl->assign('moddir', $moddir);
			while(list($dirid, $name, $descr, $img) = $xoopsDB->fetchRow($result)) {
				if ($img != ""){
					$img = XOOPS_URL."/modules/$moddir/uploads/".$myts->makeTboxData4Show($img);
				} else {
					$img = XOOPS_URL."/modules/$moddir/images/nopicture.gif";
				}
				$xoopsTpl->append('directories', array('image' => $img, 'id' => $dirid, 'title' => $name, 'descr' => $descr));
			}
		}
	} else if ($num_results >= 2) {
		$xoopsOption['template_main'] = 'efqdiralpha1_directories.html';
		include XOOPS_ROOT_PATH."/header.php";
		$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
		$xoopsTpl->assign('lang_directories', _MD_DIRECTORIES_HEADER);
		$xoopsTpl->assign('moddir', $moddir);
		while(list($dirid, $name, $descr, $img) = $xoopsDB->fetchRow($result)) {
			if ($img != ""){
				$img = XOOPS_URL."/modules/$moddir/uploads/".$myts->makeTboxData4Show($img);
			} else {
				$img = XOOPS_URL."/modules/$moddir/images/nopicture.gif";
			}
			$xoopsTpl->append('directories', array('image' => $img, 'id' => $dirid, 'title' => $name, 'descr' => $descr));
		}
	} else {
		redirect_header(XOOPS_URL,2,_MD_NOACTIVEDIRECTORIES);
		exit();
	}
}
if ($get_dirid != 0 || $get_catid != 0) {
	//Get all categories and child categories for selected category
	$xoopsOption['template_main'] = 'efqdiralpha1_index.html';
	include XOOPS_ROOT_PATH."/header.php";
	$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	$xoopsTpl->assign('moddir', $moddir);
	if ($get_dirid == '0') {
		$dirid = getDirId($get_catid);
	} else {
		$dirid = $get_dirid;
	}
	ob_start();
	printf(_MD_SEARCH_ADV,$dirid);
	$lang_adv_search = ob_get_contents();
	ob_end_clean();
	$searchform = "<form action=\"search.php\" name=\"search\" id=\"search\" method=\"get\">";
	$searchform .= "<input type=\"hidden\" name=\"dirid\" value=\"".$dirid."\" /><input type=\"text\" name=\"q\" size=\"40\" maxsize=\"150\" value=\"\" /><input type=\"submit\" class=\"formButton\" name=\"submit\" value=\""._MD_SEARCH."\">".$lang_adv_search."</form>";
	$xoopsTpl->assign('searchform', $searchform);
	$pathstring = "<a href='index.php?dirid=".$dirid."'>"._MD_MAIN."</a>&nbsp;:&nbsp;";
	$pathstring .= $efqtree->getNicePathFromId($get_catid, "title", "index.php?dirid=".$dirid."&op=");
	$xoopsTpl->assign('category_path', $pathstring);
	
	if (isset($xoopsUser) && $xoopsUser != null ) {
		$submitlink = "<a href=\"submit.php?dirid=".$dirid."\"><img src=\"".XOOPS_URL."/modules/".$moddir."/images/".$xoopsConfig['language']."/listing-new.gif\" alt=\""._MD_SUBMITLISTING."\" title=\""._MD_SUBMITLISTING."\" /></a>";
		$xoopsTpl->assign('submit_link', $submitlink);
	}
	
	if ($get_catid == 0) {
		$result=$xoopsDB->query("SELECT cid, title, img FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE pid = '0' AND dirid = '".$get_dirid."' ORDER BY title") or  $eh->show("0013");
	} else {
		$result=$xoopsDB->query("SELECT cid, title, img FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE pid = '".$get_catid."' ORDER BY title") or  $eh->show("0013");
	}
	$num_results = mysql_num_rows($result);
	if ($num_results == 0 && isset($_GET['dirid'])) {
		$xoopsTpl->assign('lang_noresults', _MD_NORESULTS);
	} else {
		$count = 1;
		while($myrow = $xoopsDB->fetchArray($result)) {
			$totallisting = getTotalItems($myrow['cid'], 2);
			$img = '';
			if ($myrow['img'] && $myrow['img'] != ""){
				$img = XOOPS_URL."/modules/$moddir/uploads/".$myts->makeTboxData4Show($myrow['img']);
			}
			$arr = array();
			$arr = $efqtree->getFirstChild($myrow['cid'], "title");
			$space = 0;
			$chcount = 0;
			$subcategories = '';
			foreach($arr as $ele){
				$chtitle = $myts->makeTboxData4Show($ele['title']);
				if ($chcount > 5) {
					$subcategories .= "...";
					break;
				}
				if ($space>0) {
					$subcategories .= ", ";
				}
				$subcategories .= "<a class=\"subcategory\" href=\"".XOOPS_URL."/modules/$moddir/index.php?catid=".$ele['cid']."\">".$chtitle."</a>";
				$space++;
				$chcount++;
			}
			$cattitle = "<a href=\"".XOOPS_URL."/modules/$moddir/index.php?catid=".$myrow['cid']."\">".$myrow['title']."</a>";
			$xoopsTpl->append('categories', array('image' => $img, 'id' => $myrow['cid'], 'title' => $cattitle, 'subcategories' => $subcategories, 'totallisting' => $totallisting, 'count' => $count));
			$count++;
		}
		
		if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
			$isadmin = true;
		} else {
			$isadmin = false;
		}
		/*if ($xoopsModuleConfig['allowcomments'] == 1) {
			$xoopsTpl->assign('allowcomments', true);
			$xoopsTpl->assign('lang_comments' , _COMMENTS);
		}
		if ($xoopsModuleConfig['allowreviews'] == 1) {
			$xoopsTpl->assign('allowreviews', true);
		}*/
		if ($xoopsModuleConfig['allowtellafriend'] == 1) {
			$xoopsTpl->assign('allowtellafriend', true);
			$xoopsTpl->assign('lang_tellafriend', _MD_TELLAFRIEND);
		}
		if ($xoopsModuleConfig['allowrating'] == 1) {
			$xoopsTpl->assign('allowrating', true);
			$xoopsTpl->assign('lang_rating', _MD_RATINGC);
			$xoopsTpl->assign('lang_ratethissite', _MD_RATETHISSITE);
		}
		$xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
		$xoopsTpl->assign('lang_lastupdate', _MD_LASTUPDATEC);
		$xoopsTpl->assign('lang_hits', _MD_HITSC);
		$xoopsTpl->assign('lang_modify', _MD_MODIFY);
		$xoopsTpl->assign('lang_listings' , _MD_LATESTLIST);
		$xoopsTpl->assign('lang_category' , _MD_CATEGORYC);
		$xoopsTpl->assign('lang_visit' , _MD_VISIT);
		$sections = array();
		if ($get_catid == 0) {
			$efqListingHandler = new efqListingHandler();
			$directorylistings = $efqListingHandler->getListingsByDirectory($get_dirid);
			while(list($itemid, $logourl, $uid, $status, $created, $ltitle, $hits, $rating, $votes, $type, $dirid, $description) = $xoopsDB->fetchRow($directorylistings)) {
				if ($isadmin) {
					$adminlink = '<a href="'.XOOPS_URL.'/modules/'.$moddir.'/admin/index.php?op=edit&amp;item='.$itemid.'"><img src="'.XOOPS_URL.'/modules/'.$moddir.'/images/editicon.gif" border="0" alt="'._MD_EDITTHISLINK.'" /></a>';
				} else {
					$adminlink = '';
				}
				if ($votes == 1) {
					$votestring = _MD_ONEVOTE;
				} else {
					$votestring = sprintf(_MD_NUMVOTES,$votes);
				}
				
				if ($xoopsModuleConfig['showdatafieldsincat'] == '1') {
					$xoopsTpl->assign('showdatafieldsincat', true);
					$listingdata = $efqListingHandler->getDataTypes($itemid);
					if (count($listingdata) > 0) {
						$xoopsTpl->assign('datatypes', true);
					}
					$sections = array();
					foreach ($listingdata as $data) {
						$fieldvalue = $efqListingHandler->getFieldValue($data['fieldtype'], $data['options'], $data['value']);
						if ($data['icon'] != '') {
							$iconurl = '<img src="uploads/'.$data['icon'].'" />';
						} else { 
							$iconurl = "";
						}
						if ($data['custom'] != '0' && $data['customtitle'] != "") {
							$title = $data['customtitle'];
						} else {
							$title = $data['title'];
						}
						if ($data['section'] == "0" or "1") {
							$sections[] = array('icon' => $iconurl, 'label' => $title, 'value' => $data['value'], 'fieldtype' => $data['fieldtype']);
						}
					}
				}
				
				$coupon_handler = new efqCouponHandler();
				$coupons = $coupon_handler->getCountByLink($itemid);
				$path = $efqtree->getPathFromId($get_catid, "title");
				$path = substr($path, 1);
				$path = str_replace("/"," <img src='".XOOPS_URL."/modules/".$moddir."/images/arrow.gif' board='0' alt=''> ",$path);
				$new = newlinkgraphic($created, $status);
				$pop = popgraphic($hits);
				$xoopsTpl->append('listings', array('fields' => $sections, 'coupons' => $coupons, 'catid' => $get_catid, 'id' => $itemid, 'rating' => number_format($rating, 2), 'title' => $myts->makeTboxData4Show($ltitle).$pop, 'type' => $type, 'logourl' => $myts->makeTboxData4Show($logourl), 'description' => $myts->makeTareaData4Show($description,0), 'adminlink' => $adminlink, 'hits' => $hits, 'votes' => $votestring, 'mail_subject' => rawurlencode(sprintf(_MD_INTERESTING_LISTING,$xoopsConfig['sitename'])), 'mail_body' => rawurlencode(sprintf(_MD_INTERESTING_LISTING_FOUND,$xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/'.$moddir.'/singleitem.php?cid='.$get_catid.'&amp;item='.$itemid)));
			}
		} else {
			if (isset($_GET['show'])) {
				$show = intval($_GET['show']);
			} else {
					$show = $xoopsModuleConfig['perpage'];
			}
			$min = isset($_GET['min']) ? intval($_GET['min']) : 0;
			$max = $min + $show;
			if(isset($_GET['orderby'])) {
					$orderby = convertorderbyin($_GET['orderby']);
			} else {
					$orderby = "level DESC";
			}
			$fullcountresult=$xoopsDB->query("select count(*) from ".$xoopsDB->prefix("efqdiralpha1_items")." i, ".$xoopsDB->prefix("efqdiralpha1_item_x_cat")." x WHERE i.itemid=x.itemid AND x.cid=$get_catid AND i.status='2'");
			list($numrows) = $xoopsDB->fetchRow($fullcountresult);
			$totalcount = $numrows;
			$page_nav = '';
			if($numrows>0){
				/*if ($xoopsModuleConfig['allowcomments'] == 1) {
					$xoopsTpl->assign('allowcomments', true);
					$xoopsTpl->assign('lang_comments' , _COMMENTS);
				}*/
				/*if ($xoopsModuleConfig['allowreviews'] == 1) {
					$xoopsTpl->assign('allowreviews', true);
				}*/
				if ($xoopsModuleConfig['allowtellafriend'] == 1) {
					$xoopsTpl->assign('allowtellafriend', true);
					$xoopsTpl->assign('lang_tellafriend', _MD_TELLAFRIEND);
				}
				if ($xoopsModuleConfig['allowrating'] == 1) {
					$xoopsTpl->assign('allowrating', true);
					$xoopsTpl->assign('lang_rating', _MD_RATINGC);
					$xoopsTpl->assign('lang_ratethissite', _MD_RATETHISSITE);
				}
				$xoopsTpl->assign('lang_listings' , _MD_LISTINGS);
				$xoopsTpl->assign('category_id', $get_catid);
				$xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
				$xoopsTpl->assign('lang_lastupdate', _MD_LASTUPDATEC);
				$xoopsTpl->assign('lang_hits', _MD_HITSC);
				$xoopsTpl->assign('lang_modify', _MD_MODIFY);
				$xoopsTpl->assign('lang_category' , _MD_CATEGORYC);
				$xoopsTpl->assign('lang_visit' , _MD_VISIT);
				$xoopsTpl->assign('show_listings', true);
				
				$efqListingHandler = new efqListingHandler();
				$categorylistings = $efqListingHandler->getListingsByCategory($get_catid, $show, $min, $orderby);
				$numrows = $xoopsDB->getRowsNum($categorylistings);
			
				//if 2 or more items in result, show the sort menu
				if($numrows>1){
					
					$xoopsTpl->assign('show_nav', true);
					$orderbyTrans = convertorderbytrans($orderby);
					$xoopsTpl->assign('lang_sortby', _MD_SORTBY);
					$xoopsTpl->assign('lang_title', _MD_TITLE);
					$xoopsTpl->assign('lang_date', _MD_DATE);
					$xoopsTpl->assign('lang_rating', _MD_RATING);
					$xoopsTpl->assign('lang_popularity', _MD_POPULARITY);
					if ($orderby != "level DESC") {
						$xoopsTpl->assign('lang_cursortedby', sprintf(_MD_CURSORTEDBY, convertorderbytrans($orderby)));
					}
				}
				if ($xoopsModuleConfig['showlinkimages'] == 1) {
					$xoopsTpl->assign('showlinkimages', 1);
				}
				while(list($itemid, $logourl, $uid, $status, $created, $itemtitle, $hits, $rating, $votes, $typeid, $dirid, $level, $description, $cid) = $xoopsDB->fetchRow($categorylistings)) {
					if ($isadmin) {
						if ($xoopsModuleConfig['showlinkimages'] == 1) {
							$adminlink = '<a href="'.XOOPS_URL.'/modules/'.$moddir.'/admin/index.php?op=edit&amp;item='.$itemid.'"><img src="'.XOOPS_URL.'/modules/'.$moddir.'/images/editicon.gif" border="0" alt="'._MD_EDITTHISLISTING.'" /></a>';
						} else {
							$adminlink = '';
						}
					} else {
						$adminlink = '';
					}
					if ($votes == 1) {
						$votestring = _MD_ONEVOTE;
					} else {
						$votestring = sprintf(_MD_NUMVOTES,$votes);
					}
					
					if ($xoopsModuleConfig['showdatafieldsincat'] == '1') {
						$xoopsTpl->assign('showdatafieldsincat', true);
						$listingdata = $efqListingHandler->getDataTypes($itemid);
						if (count($listingdata) > 0) {
							$xoopsTpl->assign('datatypes', true);
						}
						$sections = array();
						foreach ($listingdata as $data) {
							$fieldvalue = $efqListingHandler->getFieldValue($data['fieldtype'], $data['options'], $data['value']);
							if ($data['icon'] != '') {
								$iconurl = '<img src="uploads/'.$data['icon'].'" />';
							} else { 
								$iconurl = "";
							}
							if ($data['custom'] != '0' && $data['customtitle'] != "") {
								$title = $data['customtitle'];
							} else {
								$title = $data['title'];
							}
							if ($data['section'] == "0" or "1") {
								$sections[] = array('icon' => $iconurl, 'label' => $title, 'value' => $data['value'], 'fieldtype' => $data['fieldtype']);
							}
						}
					}					
					$path = $efqtree->getPathFromId($get_catid, "title");
					$path = substr($path, 1);
					$path = str_replace("/"," <img src='".XOOPS_URL."/modules/".$moddir."/images/arrow.gif' board='0' alt=''> ",$path);
					$new = newlinkgraphic($created, $status);
					$pop = popgraphic($hits);
					if ($level == NULL) {
						$level = '0';
					}
					switch ($level) {
					case "0":
						$class = "itemTableLevel0";
						break;
					case "1";
						$class = "itemTableLevel1";
						break;
					case "2";
						$class = "itemTableLevel2";
						break;
					case "3";
						$class = "itemTableLevel3";
						break;
					}
					$xoopsTpl->append('listings', array('fields' => $sections, 'id' => $itemid, 'catid' => $get_catid, 'logourl' => $myts->makeTboxData4Show($logourl), 'title' => $myts->makeTboxData4Show($itemtitle).$new.$pop, 'status' => $status, 'created' => formatTimestamp($created,"m"), 'rating' => number_format($rating, 2), 'category' => $path, 'description' => $myts->makeTareaData4Show($description,0), 'adminlink' => $adminlink, 'hits' => $hits, 'rating' => $rating, 'votes' => $votestring, 'class' => $class, 'mail_subject' => rawurlencode(sprintf(_MD_INTERESTING_LISTING,$xoopsConfig['sitename'])), 'mail_body' => rawurlencode(sprintf(_MD_INTERESTING_LISTING_FOUND,$xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/'.$moddir.'/listing.php?catid='.$get_catid.'&amp;item='.$itemid)));
				}
				$orderby = convertorderbyout($orderby);
				//Calculates how many pages exist.  Which page one should be on, etc...
				$listingpages = ceil($totalcount / $show);
				
				//Page Numbering
				if ($listingpages!=1 && $listingpages!=0) {
					$get_catid = intval($_GET['catid']);
					$prev = $min - $show;
					if ($prev>=0) {
						$page_nav .= "<a href='index.php?catid=".$get_catid."&amp;min=$prev&amp;orderby=$orderby&amp;show=$show'><b><u>&laquo;</u></b></a>&nbsp;";
					}
					$counter = 1;
					$currentpage = ($max / $show);
					while ( $counter<=$listingpages ) {
						$mintemp = ($show * $counter) - $show;
						if ($counter == $currentpage) {
							$page_nav .= "<strong>(".$counter.")</strong>&nbsp;";
						} else {
							$page_nav .= "<a href='index.php?catid=".$get_catid."&amp;min=".$mintemp."&amp;orderby=".$orderby."&amp;show=".$show."'>".$counter."</a>&nbsp;";
						}
						$counter++;
					}
					if ( $numrows>$max ) {
						$page_nav .= "<a href='index.php?catid=".$get_catid."&amp;min=".$max."&amp;orderby=".$orderby."&amp;show=".$show."'>";
						$page_nav .= "<strong><u>&raquo;</u></strong></a>";
					}
					$xoopsTpl->assign('page_nav', $page_nav);
				}
			}
		}
	}
}
include XOOPS_ROOT_PATH.'/footer.php';
?>