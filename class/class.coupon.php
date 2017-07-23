<?php
// $Id: coupon.php,v 0.18 2006/03/23 21:37:00 wtravel
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
// ------------------------------------------------------------------------- //
//	Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
// ------------------------------------------------------------------------- //
if( ! class_exists( 'Coupon' ) ) {

class Coupon extends XoopsObject {
    //Constructor
    /** 
    * @param mixed $coupid int for coupon id or array with name->value pairs of properties
    * @return object {@link Coupon}
    */
	function Coupon($coupid = false) {
		global $moddir;
		$this->db = Database::getInstance();
        $this->initVar('couponid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('itemid', XOBJ_DTYPE_INT, null, true);
		$this->initVar('description', XOBJ_DTYPE_TXTAREA);
		$this->initVar('image', XOBJ_DTYPE_TXTBOX);
		$this->initVar('publish', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('expire', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('heading', XOBJ_DTYPE_TXTBOX);
		$this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('lbr', XOBJ_DTYPE_INT, 0, false);
		if ($coupid != false) {
			if (is_array($coupid)) {
                $this->assignVars($coupid);
            } else {
				$coupon_handler = xoops_getmodulehandler('coupon', $moddir);
                $coupon =& $coupon_handler->get($coupid);
                foreach ($coupon->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                unset($coupon);
            }
        }
    }
    
    function toArray() {
        $ret = array();
        foreach ($this->vars as $k => $v) {
            $ret[$k] = $v['value'];
        }
        return $ret;
    }
}
}

// Change the class name below to enable custom directory (Capitolize first letter YourdirectoryCouponHandler)
class efqdirectoryCouponHandler extends XoopsObjectHandler {
    /**
     * create a new coupon object
     * 
     * @param bool $isNew flag the new objects as "new"?
     * @return object {@link Coupon}
     */
    //var $coupon;
    //var $db;
    
    function &create($isNew = true)
    {
        //$this->db =& Database::getInstance();
        $coupon = new Coupon();
        if ($isNew) {
            $coupon->setNew();
        }
        return $coupon;
    } 
    /**
     * retrieve a coupon
     * 
     * @param int $coupid ID of the coupon
     * @return mixed reference to the {@link Coupon} object, FALSE if failed
     */
    function &get($coupid = false) {
        if ($coupid == false) {
            return false;
        }
        $coupid = intval($coupid);
        if ($coupid > 0) {
            $sql = "SELECT * FROM ".$this->db->prefix("efqdiralpha1_coupon")." WHERE couponid=".$coupid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $coupon =& $this->create(false);
            $coupon->assignVars($this->db->fetchArray($result));
            return $coupon;
        }
        return false;
    }
    
    /**
    * Save coupon in database
    * @param object $coupon reference to the {@link Coupon} object
    * @param bool $force 
    * @return bool FALSE if failed, TRUE if already present and unchanged or successful
    */
    function insert(&$coupon) {
        global $eh, $xoopsDB, $description, $image, $heading, $couponid;
		if (get_class($coupon) != 'Coupon') {
            echo " class not coupon ";
			return false;
        }
        if (!$coupon->isDirty()) {
            echo " coupon not dirty ";
			return true;
        }
        if (!$coupon->cleanVars()) {
            echo " coupon not cleanvars ";
			return false;
        }
        foreach ($coupon->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($coupon->_isNew) {
            $sql = "INSERT INTO ".$this->db->prefix("efqdiralpha1_coupon")." 
                    (itemid, description, image, publish, expire, heading, lbr) VALUES 
                    ($itemid, ".$this->db->quoteString($description).", ".$this->db->quoteString($image).", $publish, $expire, ".$this->db->quoteString($heading).", $lbr)";
        } else {
            $sql = "UPDATE ".$this->db->prefix("efqdiralpha1_coupon")." SET
                    itemid = $itemid,
                    description = ".$this->db->quoteString($description).",
					image = ".$this->db->quoteString($image).",
					publish = $publish,
                    lbr = $lbr,
                    heading = ".$this->db->quoteString($heading).",
                    expire = $expire WHERE couponid = ".$couponid;
        }
        $xoopsDB->query($sql) or $eh->show("0013");
        if ($coupon->_isNew) {
            $coupon->setVar('couponid', $this->db->getInsertId());
            $coupon->_isNew = false;
        }
        return true;
    }
    
    /**
    * delete a coupon from the database
    *
    * @param object $coupon reference to the {@link Coupon} to delete
    * @param bool $force
    * @return bool FALSE if failed.
    */
    function delete(&$coupon) {
        $sql = "DELETE FROM ".$this->db->prefix("efqdiralpha1_coupon")." WHERE couponid = ".intval($coupon->getVar('couponid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * get {@link Coupon} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link Coupon} objects
    * @param bool $id_as_key if true, the returned array will have the coupon ids as key
    *
    * @return array array of {@link Coupon} objects
    */
    function &getObjects($criteria = null, $as_objects = true, $id_as_key = false) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT l.title AS listingTitle, coup.couponid, coup.heading, coup.counter, l.itemid, l.logourl, coup.description, coup.image, coup.lbr, coup.publish, coup.expire 
                FROM '.$this->db->prefix('efqdiralpha1_coupon').' coup, '.$this->db->prefix('efqdiralpha1_items').' l
                WHERE coup.itemid=l.itemid AND ';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            
			$sql .= ' '.$criteria->render();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            if ($as_objects) {            
                $coupon = new Coupon();
                $coupon->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $coupon;
                } else {
                    $ret[$myrow['couponid']] =& $coupon;
                }
                unset($coupon);
            }
            else {
                $ret[] = $myrow;
            }
        }
        return $ret;
    }
    
    /**
    * get {@link Coupon} objects by listing
    *
    * @param int $itemid listing id
    *
    * @return array array of {@link Coupon} objects
    */
    function &getByLink($itemid) {
        global $eh;
		$ret = array();
        $limit = $start = 0;
        $now = time();
		$sql = 'SELECT l.title AS listingTitle, coup.couponid, coup.heading, coup.counter, l.itemid, l.logourl, coup.description, coup.image, coup.lbr, coup.publish, coup.expire 
                FROM '.$this->db->prefix('efqdiralpha1_coupon').' coup, '.$this->db->prefix('efqdiralpha1_items').' l';
        $sql .= ' WHERE coup.itemid = l.itemid AND coup.itemid='.intval($itemid).' AND coup.publish < '.$now.' AND (coup.expire = 0 OR coup.expire > '.$now.')';
        $sql .= ' ORDER BY listingTitle ASC';
        $result = $this->db->query($sql, $limit, $start) or $eh->show("0013");
    
	    if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow;
        }
        return $ret;
    }
    
    /** 
    * Returns number of coupons for a listing
    *
    * @param int $itemid listing id
    *
    * @return
    */
    function getCountByLink($itemid) {
        $ret = 0;
        $now = time();
		$sql = "SELECT count(*) FROM ".$this->db->prefix("efqdiralpha1_coupon")." WHERE itemid=".intval($itemid).' AND publish < '.$now.' AND (expire = 0 OR expire > '.$now.')';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        list($ret) = $this->db->fetchRow($result);
        return $ret;
    }
    
    /**
    * get {@link Coupon} object with listing info
    *
    * @param int $coupid Coupon ID
    *
    * @return object {@link Coupon}
    */
    function &getLinkedCoupon($coupid) {
        $ret = array();
        $limit = $start = 0;
        $now = time();
        $sql = 'SELECT l.title AS listingTitle, coup.couponid, coup.heading, coup.counter, l.itemid, l.logourl, coup.description,  coup.image, coup.lbr, coup.publish, coup.expire 
                FROM '.$this->db->prefix('efqdiralpha1_coupon').' coup, '.$this->db->prefix('efqdiralpha1_items').' l';
        $sql .= ' WHERE coup.itemid = l.itemid AND coup.couponid='.intval($coupid);
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow;
        }
        return $ret;
    }
    
    /** 
    * Prepares rows from getByLink and getByCategory to be displayed
    *
    * @param array $array rows to be prepared
    *
    * @return array
    */
    function prepare2show($array) {
        $myts =& MyTextSanitizer::getInstance();
        $ret = array();
        foreach ($array as $key => $myrow) {
            $description = $myts->displayTarea($myrow['description'], 1, 1, 1, 1, $myrow['lbr']);
            if ($myrow['expire'] > 0) {
                $expire = formatTimestamp($myrow['expire'], 's');
            }
            else {
                $expire = 0;
            }
            $ret ['items']['coupons'][] = array(  'itemid' => $myrow['itemid'],
											'couponid' => $myrow['couponid'],
											'heading' => $myts->htmlSpecialChars($myrow['heading']),
											'description' => $description,
											'logourl' => $myts->displayTarea($myrow['logourl']),
											'publish' => formatTimestamp($myrow['publish'], 's'),
											'expire' => $expire,
											'counter' => intval($myrow['counter']));
            $ret ['items']['listingTitle'] = $myts->displayTarea($myrow['listingTitle']);
			$ret ['items']['itemid'] = $myrow['itemid'];
        }
        return $ret;
    }
    
    /**
    * Increment coupon counter
    *
    * @param int $couponid
    *
    * @return bool
    */
    function increment($couponid) {
        $sql = 'UPDATE '.$this->db->prefix('efqdiralpha1_coupon').' SET counter=counter+1 WHERE couponid='.intval($couponid);
        return $this->db->queryF($sql);
    }
}
?>