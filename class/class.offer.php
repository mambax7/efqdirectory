<?php
/*
// ID: class.offer.php 30-apr-2007 13:39:01 efqconsultancy
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
*/

/**
 * Class efqSubscriptionOffer
 * Manages operations for subscription offers
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2008
 * @version   1.1.0
 */
class efqSubscriptionOffer extends XoopsObject
{

    /**
     * efqSubscriptionOffer::efqSubscriptionOffer()
     *
     * @param bool $offer
     * @internal param bool $itemtype
     */
    public function __construct($offer = false)
    {
        global $moddir;
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('offerid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dirid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('typeid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('duration', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('count', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('price', XOBJ_DTYPE_CURRENCY, 0.00, true);
        $this->initVar('activeyn', XOBJ_DTYPE_INT, 0, true, 2);
        $this->initVar('currency', XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('descr', XOBJ_DTYPE_OTHER, 0);
        $this->initVar('typename', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('level', XOBJ_DTYPE_INT, null, false, 10);
    }
}

/**
 * Class efqFieldTypeHandler
 * Manages database operations for field types
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class efqSubscriptionOfferHandler extends xoopsObjectHandler
{
//    public $db; //Database reference
    public $objOffer; // reference to object

    /**
     * efqSubscriptionOfferHandler::efqItemTypeHandler()
     *
     * @param bool $offer
     */
    public function __construct($offer = false)
    {
        $this->db       = XoopsDatabaseFactory::getDatabaseConnection();
        $this->objOffer = $offer;
    }

    /**
     * Function insertOffer inserts Subscripion offer into DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param   efqSubscriptionOffer $obj object
     *
     * @param bool     $forceQuery
     * @return bool true if insertion is succesful, false if unsuccesful
     */
    public function insertOffer($obj, $forceQuery = false)
    {
        $tablename    = 'efqdiralpha1_subscr_offers';
        $keyName      = 'offerid';
        $excludedVars = ['level', 'typename'];
        if ($obj instanceof efqSubscriptionOffer) {
            // Variable part of this function ends. From this line you can copy
            // this function for similar object handling functions.
            $obj->cleanVars();
            $cleanvars = $obj->cleanVars;
        } else {
            return false;
        }
        $countVars = count($cleanvars);
        $i         = 1;
        $strFields = '';
        $strValues = '';
        foreach ($cleanvars as $k => $v) {
            if (!in_array($k, $excludedVars)) {
                $strFields .= $k;
                $strValues .= "'" . $v . "'";
                if ($i < $countVars) {
                    $strFields .= ', ';
                    $strValues .= ', ';
                }
                $i++;
            }
        }
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->db->prefix($tablename), $strFields, $strValues);
        if ($forceQuery) {
            if ($this->db->queryF($sql)) {
                $itemid = $this->db->getInsertId();
                $obj->setVar($keyName, $itemid);

                return true;
            }
        } else {
            if ($this->db->query($sql)) {
                $itemid = $this->db->getInsertId();
                $obj->setVar($keyName, $itemid);

                return true;
            }
        }

        return false;
    }

    /**
     * Function updateOffer updates subscription offer
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param      $obj
     * @param bool $forceQuery
     * @return bool true if update is succesful, false if unsuccesful
     * @internal  param object $objOffer object of type listing
     */
    public function updateOffer($obj, $forceQuery = false)
    {
        $tablename    = 'efqdiralpha1_subscr_offers';
        $keyName      = 'offerid';
        $excludedVars = ['level', 'typename'];
        if ($obj instanceof efqSubscriptionOffer) {
            // Variable part of this function ends. From this line you can copy
            // this function for similar object handling functions.
            $obj->cleanVars();
            $cleanvars = $obj->cleanVars;
            $keyValue  = $obj->getVar($keyName);
        } else {
            return false;
        }
        $countVars = count($cleanvars);
        $i         = 1;
        $strSet    = '';
        $strValues = '';
        foreach ($cleanvars as $k => $v) {
            if (!in_array($k, $excludedVars)) {
                if ($i < $countVars and $i > 1) {
                    $strSet .= ', ';
                }
                $strSet .= $k . '=' . "'" . $v . "'";
            }
            $i++;
        }
        $sql = sprintf('UPDATE %s SET %s WHERE %s = %u', $this->db->prefix($tablename), $strSet, $keyName, $keyValue);
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
     * efqSubscriptionOfferHandler::setOffer()
     *
     * Sets the object created by class efqSubscriptionOffer with data from query
     *
     * @param int $gpc_offerid
     * @return bool true or false
     */
    public function setOffer($gpc_offerid = 0)
    {
        $sql     = 'SELECT o.offerid, o.title, o.typeid, o.duration, o.count, '
                   . 'o.price, o.activeyn, o.currency, o.descr, o.dirid, t.typename, t.level FROM '
                   . $this->db->prefix('efqdiralpha1_itemtypes')
                   . ' t, '
                   . $this->db->prefix('efqdiralpha1_subscr_offers')
                   . ' o	WHERE o.typeid=t.typeid AND o.offerid='
                   . (int)$gpc_offerid
                   . '';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        if ($numrows > 0) {
            while (list($offerid, $offertitle, $typeid, $duration, $count, $price, $activeyn, $currency, $descr, $dirid, $typename, $level) = $this->db->fetchRow($result)) {
                if (!$this->objOffer) {
                    $this->objOffer = new efqSubscriptionOffer();
                }
                $this->objOffer->setVar('offerid', $offerid);
                $this->objOffer->setVar('title', $offertitle);
                $this->objOffer->setVar('typeid', $typeid);
                $this->objOffer->setVar('duration', $duration);
                $this->objOffer->setVar('count', $count);
                $this->objOffer->setVar('price', $price);
                $this->objOffer->setVar('activeyn', $activeyn);
                $this->objOffer->setVar('currency', $currency);
                $this->objOffer->setVar('descr', $descr);
                $this->objOffer->setVar('dirid', $dirid);
                $this->objOffer->setVar('typename', $typename);
                $this->objOffer->setVar('level', $level);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function getOffer()
    {
        return $this->objOffer;
    }
}
