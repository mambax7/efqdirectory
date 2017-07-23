<?php
/*
// ID: class.datatype.php 30-apr-2007 13:39:01 efqconsultancy
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
class efqDataType extends XoopsObject
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
        $this->initVar('dtypeid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('section', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('fieldtypeid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('defaultyn', XOBJ_DTYPE_INT, 0, true, 2);
        $this->initVar('created', XOBJ_DTYPE_INT, 0, true, 10);
        $this->initVar('seq', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('options', XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('activeyn', XOBJ_DTYPE_INT, 0, true, 2);
        $this->initVar('custom', XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('icons', XOBJ_DTYPE_TXTBOX, null, false, 50);
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
class efqDataTypeHandler extends xoopsObjectHandler
{
    public $db; //Database reference
    public $objDataType; // reference to object

    /**
     * efqSubscriptionOfferHandler::efqItemTypeHandler()
     *
     * @param bool $obj
     */
    public function __construct($obj = false)
    {
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->objDataType = $obj;
    }

    /**
     * Function insertOffer inserts record into DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param   efqDataType $obj object
     *
     * @param bool     $forceQuery
     * @return bool true if insertion is succesful, false if unsuccesful
     */
    public function insertDataType($obj, $forceQuery = false)
    {
        $tablename    = 'efqdiralpha1_dtypes';
        $keyName      = 'dtypeid';
        $excludedVars = array();
        if ($obj instanceof efqDataType) {
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
     * @internal  param $object
     *
     */
    public function updateDataType($obj, $forceQuery = false)
    {
        $tablename    = 'efqdiralpha1_dtypes';
        $keyName      = 'dtypeid';
        $excludedVars = array();
        if ($obj instanceof efqDataType) {
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
     * function setDataType()
     *
     * Sets the object created by class efqDataType with data from query
     *
     * @param int $gpc_dtypeid
     * @return bool true or false
     */
    public function setDataType($gpc_dtypeid = 0)
    {
        $sql     = 'SELECT dtypeid,title,section,fieldtypeid,uid,defaultyn,created,seq,activeyn,options,custom,icon	WHERE dtypeid=' . (int)$gpc_dtypeid;
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        if ($numrows > 0) {
            while (list($dtypeid, $title, $section, $fieldtypeid, $uid, $defaultyn, $activeyn, $options, $custom, $icon) = $this->db->fetchRow($result)) {
                if (!$this->objDataType) {
                    $this->objDataType = new efqDataType();
                }
                $this->objDataType->setVar('dtypeid', $dtypeid);
                $this->objDataType->setVar('title', $title);
                $this->objDataType->setVar('section', $section);
                $this->objDataType->setVar('fieldtypeid', $fieldtypeid);
                $this->objDataType->setVar('uid', $uid);
                $this->objDataType->setVar('defaultyn', $defaultyn);
                $this->objDataType->setVar('created', $created);
                $this->objDataType->setVar('seq', $seq);
                $this->objDataType->setVar('options', $options);
                $this->objDataType->setVar('activeyn', $activeyn);
                $this->objDataType->setVar('custom', $custom);
                $this->objDataType->setVar('icon', $icon);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function getDataType()
    {
        return $this->objDataType;
    }
}
