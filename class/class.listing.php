<?php
// $Id: listing.php,v 1.1.0 2007/11/03 17:46:00 wtravel
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

/**
 * Class efqListing
 * Manages operations for listings
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqListing extends XoopsObject {

	var $_editrights = false;
	var $_currentuser;
	var $_value = array();
	var $_postdata = array();
	var $_datatypes = array();
	var $_updated = false;
	var $_inserted = false;

	/**
	 * Constructor
	 */
	function efqListing() {
		// class constructor;
		$this->setCurrentUser();		
		$this->initVar('itemid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('logourl', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('status', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('created', XOBJ_DTYPE_INT, 0, true); 
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('hits', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('rating', XOBJ_DTYPE_OTHER, 0.0, true);
        $this->initVar('votes', XOBJ_DTYPE_INT, 0, true);     
        $this->initVar('typeid', XOBJ_DTYPE_INT, 0, true); 
        $this->initVar('dirid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false);
	}	
		
	/**
	 * Function setListingVars sets listing variables
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   array   $listing	Array with listing details
	 */
	function setListingVars($arr=array(),$handlername=false)
	{
		global $moddir;
		if ($arr != false and $handlername != false) {
			if (is_array($arr)) {
                $this->assignVars($arr);
            } else {
				$obj_handler = xoops_getmodulehandler($handlername, $moddir);
                $object =& $obj_handler->get($arr);
                foreach ($object->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                unset($object);
            }
        }
	}
	
	function setDataTypes($arr = array()) {
		$this->_datatypes = $arr;
	}
	
	function getDataTypes() {
		return $this->_datatypes;
	}
	
	function setCurrentUser() {
		global $xoopsUser;
		$this->_currentuser = !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
	}

	function setEditRights($value = false) {
		$this->_editrights = $value;
	}
	
	function addPostDataArray($arr) {
		$this->_postdata[] = $arr;
	}
	
	//Set variable $_updated to true of false (default)
	function setUpdated($set=false) {
		$this->_updated = $set;
	}
	
	//Set variable $_inserted to true of false (default)
	function setInserted($set=false) {
		$this->_inserted = $set;
	}
	
}

/**
 * Class efqListingHandler
 * Manages database operations for listings
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqListingHandler extends XoopsObjectHandler
{
	var $errorhandler;
	
	function efqListingHandler()
	{
		//Instantiate class
		global $eh;
		$this->db =& XoopsDatabaseFactory::getDatabaseConnection();
		$this->errorhandler = $eh;
	}
	
	/**
	 * Function updateStatus updates status for listing
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: 0 - Listing to be updated
	 * @param   int   $newstatus - Default: '1' - New status for listing
	 * 
	 * @return	bool	true if update is succesful, false if unsuccesful
	 */
	function updateStatus($itemid=0, $newstatus='1') {
		$sql = "UPDATE ".$this->db->prefix("efqdiralpha1_items")." SET status = ".$newstatus." WHERE itemid = ".intval($itemid)."";
		if ($this->db->query($sql)) {
			return true;	
		}
		return false;
	}
	
	/**
	 * Function incrementHits increments hits for listing with 1
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: 0 - Listing to be updated
	 * 
	 * @return	bool	true if update is succesful, false if unsuccesful
	 */
	function incrementHits($itemid=0) {
		$sql = sprintf("UPDATE %s SET hits = hits+1 WHERE itemid = %u AND status = 2", $this->db->prefix("efqdiralpha1_items"), intval($itemid));
		if ($this->db->queryF($sql)) {
			return true;	
		}
		return false;		
	}

	/**
	 * Function getLinkedCategories gets categories linked to a listing.
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: '0' - Listing ID
	 * @param   int   $dirid - Default: '0' - Directory ID
	 * @param   int   $activeonly - Default: true - Should category be active only?  
	 * 
	 * @return	array	$arr Array with category ID's
	 */
	function getLinkedCategories($itemid='0', $dirid='0', $activeonly=true) {
		$sql = "SELECT c.cid, x.active FROM ".$this->db->prefix("efqdiralpha1_cat")." c, ".$this->db->prefix("efqdiralpha1_item_x_cat")." x WHERE c.cid=x.cid AND x.itemid=".intval($itemid)." AND c.dirid='".intval($dirid)."'";
		if ($activeonly) {
			$sql .= " AND c.active='1'";	
		}		
		$result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
	    $arr = array();
		if ( $numrows > 0 ) {
			while(list($cid, $active) = $this->db->fetchRow($result)) {
				$arr[] = $cid;
			}
		}
		return $arr;
	}

	/**
	 * Function getAllCategories gets categories linked to a directory.
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: '0' - Listing ID
	 * @param   int   $dirid - Default: '0' - Directory ID
	 * @param   int   $activeonly - Default: true - Should category be active only?
	 * 
	 * @return	array	$arr Array with category ID's
	 */
	function getAllCategories($dirid='0', $activeonly=true) {
		$sql = "SELECT cid FROM ".$this->db->prefix("efqdiralpha1_cat")." WHERE dirid='".intval($dirid)."'";
		if ($activeonly) {
			$sql .= " AND active='1'";	
		}
		$result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
	    $arr = array();
		if ( $numrows > 0 ) {
			while(list($cid) = $this->db->fetchRow($result)) {
				$arr[] = $cid;
			}
		}
		return $arr;
	}
	
	/**
	 * Function getListing gets listing from DB as an array.
	 * 
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: '0' - Listing ID
	 * 
	 * @return	array	$arr Array with listing fields and values
	 */
	function getListing($itemid) {
		$sql = "SELECT i.*, t.description FROM ".$this->db->prefix('efqdiralpha1_items')." i LEFT JOIN ".$this->db->prefix('efqdiralpha1_item_text')." t ON (i.itemid=t.itemid) WHERE i.itemid=".intval($itemid);
		$result = $this->db->query($sql);
		$arr = array();
		if (!$result) {
            return $arr;
        } else {
        	$numrows = $this->db->getRowsNum($result);
        	if ($numrows == 0) {
        		return $arr;
        	} else {
        		$arr = $this->db->fetchArray($result);
        	}
        }
        return $arr;
	}
	
	function getListingsByDirectory( $dirid=0, $show=10, $min=0, $orderby=false ) {
		$sql = "SELECT l.itemid, l.logourl, l.uid, l.status, l.created, l.title, l.hits, l.rating, l.votes, l.typeid, l.dirid, t.description FROM ".$this->db->prefix("efqdiralpha1_cat")." c, ".$this->db->prefix("efqdiralpha1_item_x_cat")." x, ".$this->db->prefix("efqdiralpha1_items")." l LEFT JOIN ".$this->db->prefix("efqdiralpha1_item_text")." t ON (l.itemid=t.itemid) WHERE x.cid=c.cid AND l.itemid=x.itemid AND c.showpopular=1 AND l.status='2' AND l.dirid = '".$dirid."' ORDER BY l.created DESC";
		$result = $this->db->query($sql) or  $this->errorhandler->show("0013");
		return $result;
	}
	
	function getListingsByCategory( $catid=0, $show=10, $min=0, $orderby=false ) {
		$sql = "SELECT i.itemid, i.logourl, i.uid, i.status, i.created, i.title, i.hits, i.rating, i.votes, i.typeid, i.dirid, t.level, txt.description, x.cid FROM ".$this->db->prefix("efqdiralpha1_item_x_cat")." x, ".$this->db->prefix("efqdiralpha1_items")." i LEFT JOIN ".$this->db->prefix("efqdiralpha1_itemtypes")." t ON (t.typeid=i.typeid) LEFT JOIN ".$this->db->prefix("efqdiralpha1_item_text")." txt ON (txt.itemid=i.itemid) WHERE i.itemid=x.itemid AND x.cid=$catid AND x.active='1' AND i.status='2' ORDER BY $orderby";
		$result = $this->db->query($sql) or  $this->errorhandler->show("0013");
		return $result;
	}
	
	function getDataTypes( $itemid, $show=10, $min=0 ) {
		$sql = "SELECT DISTINCT t.dtypeid, t.title, t.section, t.icon, f.typeid, f.fieldtype, f.ext, t.options, t.custom, d.itemid, d.value, d.customtitle ";
		$sql .= "FROM ".$this->db->prefix("efqdiralpha1_item_x_cat")." ic, ".$this->db->prefix("efqdiralpha1_dtypes_x_cat")." xc, ".$this->db->prefix("efqdiralpha1_fieldtypes")." f, ".$this->db->prefix("efqdiralpha1_dtypes")." t ";
		$sql .= "LEFT JOIN ".$this->db->prefix("efqdiralpha1_data")." d ON (t.dtypeid=d.dtypeid AND d.itemid=".intval($itemid).") ";
		$sql .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=".intval($itemid)." ORDER BY t.seq ASC";
		$result=$this->db->query($sql,$show,$min) or $this->errorhandler->show("0013");
		$numrows = $this->db->getRowsNum($result);
		$arr = array();
		while(list($dtypeid, $title, $section, $icon, $ftypeid, $fieldtype, $ext, $options, $custom, $itemid, $value, $customtitle) = $this->db->fetchRow($result)) {
			$fieldvalue = $this->getFieldValue($fieldtype, $options, $value);
			if ($icon != '') {
				$iconurl = "<img src=\"uploads/$icon\" />";
			} else { 
				$iconurl = "";
			}
			if ($custom != '0' && $customtitle != "") {
				$title = $customtitle;
			}
			$arr[] = array('dtypeid' => $dtypeid,
				'title' => $title,
				'section' => $section,
				'icon' => $iconurl,
				'ftypeid' => $ftypeid,
				'fieldtype' => $fieldtype,
				'ext' => $ext,
				'options' => $options,
				'custom' => $custom,
				'itemid' => $itemid,
				'value' => $fieldvalue,
				'customtitle' => $customtitle );			
		}
		return $arr;
	}
	
	function getFieldValue($fieldtype="", $options="", $value=0) {
		global $myts, $moddir;
		switch ($fieldtype) {
		case "dhtml":
			return $myts->makeTareaData4Show($value);
			break;
		case "gmap":
			$gmapHandler = new efqGmapHandler();
			$gmap = new efqGmap();
			$gmap->setPointsJS($gmapHandler->getPointsJS($gmap));
			$gmap->generateMap();
			$ret = $gmap->showMap();
			unset($gmap);
			unset($gmapHandler);
			return $ret;
			break;
		case "radio":
			return $myts->makeTboxData4Show($value);
			break;
		case "rating":
			$xoops_url = XOOPS_URL;
			switch ($value) {
				case 1:
					$src = "$xoops_url/modules/$moddir/images/rating_1.gif";
					break;
				case 2:
					$src = "$xoops_url/modules/$moddir/images/rating_2.gif";
					break;
				case 3:
					$src = "$xoops_url/modules/$moddir/images/rating_3.gif";
					break;
				case 4:
					$src = "$xoops_url/modules/$moddir/images/rating_4.gif";
					break;
				case 5:
					$src = "$xoops_url/modules/$moddir/images/rating_5.gif";
					break;
				case 6:
					$src = "$xoops_url/modules/$moddir/images/rating_6.gif";
					break;
				case 7:
					$src = "$xoops_url/modules/$moddir/images/rating_7.gif";
					break;
				case 8:
					$src = "$xoops_url/modules/$moddir/images/rating_8.gif";
					break;
				case 9:
					$src = "$xoops_url/modules/$moddir/images/rating_9.gif";
					break;
				case 10:
					$src = "$xoops_url/modules/$moddir/images/rating_10.gif";
					break;
				default:
				$src = "";
			}			
			$rating = "<img src=\"$src\" />";
			return $rating;
			break;
		case "select":
			return $myts->makeTboxData4Show($value);
			break;
		case "textbox":
			return $myts->makeTboxData4Show($value);
			break;
		case "url":
			$link = explode('|',$value);
			return '<a href="'.$myts->makeTboxData4Show($link[0]).'" title="'.$myts->makeTboxData4Show($link[1]).'">'.$myts->makeTboxData4Show($link[0]).'</a>';
			break;
		case "yesno":
			if ($value == '1') {
				return _YES;
			} else {
				return _NO;
			}
			break;
		default:
			return $myts->makeTboxData4Show($value);
			break;
		}
	}
	
	/**
	 * Function updateDescription updates description of listing
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2008
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: 0 - Listing to be updated
	 * @param   textarea   $descr - Default: "" - Description
	 * 
	 * @return	bool	true if update is succesful, false if unsuccesful
	 */
	function updateDescription($itemid=0, $descr="") {
		$sql = sprintf("UPDATE %s SET description = %s WHERE itemid = %u", $this->db->prefix("efqdiralpha1_item_text"), $this->db->quoteString($descr), intval($itemid));
		if ($this->db->queryF($sql)) {
			return true;	
		}
		return false;		
	}
	
	/**
	 * Function insertDescription inserts description of listing into DB
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2008
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: 0 - Listing to be updated
	 * @param   textarea   $descr - Default: "" - Description
	 * 
	 * @return	bool	true if update is succesful, false if unsuccesful
	 */
	function insertDescription($itemid=0, $descr="") {
		$sql = sprintf("INSERT INTO %s (itemid, description) VALUES (%u, %s)", $this->db->prefix("efqdiralpha1_item_text"), intval($itemid), $this->db->quoteString($descr));
		if ($this->db->queryF($sql)) {
			return true;	
		}
		return false;		
	}
		
	/**
	 * Function updateListing updates listing
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2008
	 * @version 1.0.0
	 * 
	 * @param   object   $objListing object of type listing
	 * 
	 * @return	bool	true if update is succesful, false if unsuccesful
	 */
	function updateListing($obj, $forceQuery=false) {
		$tablename = "efqdiralpha1_items";
		$keyName = "itemid";
		if ($obj instanceof efqListing) {
			// Variable part of this function ends. From this line you can copy
			// this function for similar object handling functions. 			
			$obj->cleanVars();
			$cleanvars = $obj->cleanVars;
			$keyValue = $obj->getVar($keyName);
		} else {
			return false;
		}
		$countVars = count($cleanvars);
		$i = 1;
		$strSet = "";
		$strValues = "";
		foreach ($cleanvars as $k => $v) {
			if ($k != 'description') {
				if ($i < $countVars and $i > 1) {
					$strSet .= ", ";
				}
				$strSet .= $k."="."'".$v."'";
			}
			$i++;		
		}
		$sql = sprintf("UPDATE %s SET %s WHERE %s = %u", $this->db->prefix($tablename), $strSet, $keyName, $keyValue);
		if ($forceQuery) {
			if ($this->db->queryF($sql)) {
				return true;	
			}	
		} else {
			if ($this->db->query($sql)) {
				return true;	
			}
		}		
		return false;		
	}
	
	/**
	 * Function insertListing inserts listing into DB
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2008
	 * @version 1.0.0
	 * 
	 * @param   object   $objListing object of type listing
	 * 
	 * @return	bool	true if insertion is succesful, false if unsuccesful
	 */
	function insertListing($obj, $forceQuery=false) {
		$tablename = "efqdiralpha1_items";
		$keyName = "itemid";
		if ($obj instanceof efqListing) {
			// Variable part of this function ends. From this line you can copy
			// this function for similar object handling functions. 			
			$obj->cleanVars();
			$cleanvars = $obj->cleanVars;
		} else {
			return false;
		}
		$countVars = count($cleanvars);
		$i = 1;
		$strFields = "";
		$strValues = "";
		print_r($cleanvars);
		foreach ($cleanvars as $cleanvars) {
			$strFields .= $cleanvar['name'];
			$strValues .= $cleanvar['value'];
			if ($i < $countVars) {
				$strFields .= ", ";
				$strValues .= ", ";
			}
			$i++;
		}
		$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->db->prefix($tablename), $strFields, $strValues);
		if ($forceQuery) {
			if ($this->db->queryF($sql)) {
				$itemid = $this->db->getInsertId();
				$objListing->setVar('itemid', $itemid);
				return true;
			}	
		} else {
			if ($this->db->query($sql)) {
				$itemid = $this->db->getInsertId();
				$objListing->setVar('itemid', $itemid);
				return true;	
			}
		}		
		return false;		
	}
	

}
?>