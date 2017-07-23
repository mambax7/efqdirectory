<?php
/*
// ID: class.itemtypes.php 30-apr-2007 13:39:01 efqconsultancy
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
 * Class efqItemType
 * Manages operations for item types
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqItemType extends XoopsObject
{
    
  /**
   * efqItemType::efqItemType()
   * 
   * @param bool $itemtype
   * @return
   */
    public function __construct($obj=false)
    {
        global $moddir;
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('typeid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('typename', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('level', XOBJ_DTYPE_INT, 0, true, 4);
        $this->initVar('dirid', XOBJ_DTYPE_INT, 0, true, 5);
    }
}

/**
 * Class efqFieldTypeHandler
 * Manages database operations for field types
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqItemTypeHandler extends xoopsObject
{
    public $db; //Database reference
    public $objItemType;
        
  /**
   * efqItemTypeHandler::efqItemTypeHandler()
   * 
   * @return
   */
    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }
    
    /**
     * Function insert inserts new record into DB
     * @author EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version 1.0.0
     * 
     * @param   object   $obj object
     * 
     * @return	bool	true if insertion is succesful, false if unsuccesful
     */
    public function insert($obj, $forceQuery=false)
    {
        $tablename = "efqdiralpha1_itemtypes";
        $keyName = "typeid";
        $excludedVars = array();
        if ($obj instanceof efqItemType) {
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
        foreach ($cleanvars as $k => $v) {
            if (!in_array($k, $excludedVars)) {
                $strFields .= $k;
                $strValues .= "'".$v."'";
                if ($i < $countVars) {
                    $strFields .= ", ";
                    $strValues .= ", ";
                }
                $i++;
            }
        }
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->db->prefix($tablename), $strFields, $strValues);
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
     * Function update updates record in DB
     * @author EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version 1.0.0
     * 
     * @param   object   $objOffer object of type listing
     * 
     * @return	bool	true if update is succesful, false if unsuccesful
     */
    public function update($obj, $forceQuery=false)
    {
        $tablename = "efqdiralpha1_itemtypes";
        $keyName = "typeid";
        $excludedVars = array();
        if ($obj instanceof efqItemType) {
            // Variable part of this function ends. From this line you can copy
            // this function for similar object handling functions.
            $obj->cleanVars();
            $cleanvars = $obj->cleanVars;
            $keyValue = $obj->getVar($keyName);
        } else {
            return false;
        }
        $countVars = count($cleanvars);
        $i = 0;
        $strSet = "";
        $strValues = "";
        foreach ($cleanvars as $k => $v) {
            if (!in_array($k, $excludedVars)) {
                if ($i < $countVars and $i > 0) {
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
   * Function set()
   * 
   * Sets the object with data from query 
   * 
   * @return bool true or false
   */
    public function set($typeid=0)
    {
        $sql = "SELECT typeid,typename,level,dirid FROM ".$this->db->prefix("efqdiralpha1_itemtypes")." WHERE typeid=".intval($typeid)."";
        $result = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        if ($numrows > 0) {
            while (list($typeid, $typename, $level, $dirid) = $this->db->fetchRow($result)) {
                if (! $this->objItemType) {
                    $this->objItemType = new efqSubscriptionOffer();
                }
                $this->objItemType->setVar('typeid', $typeid);
                $this->objItemType->setVar('typename', $typename);
                $this->objItemType->setVar('level', $level);
                $this->objItemType->setVar('dirid', $dirid);
            }
        } else {
            return false;
        }
        return true;
    }
    
    public function getObjItemType()
    {
        return $this->objItemType;
    }
        
  /**
   * efqItemTypeHandler::getByDir()
   * 
   * @param integer $dirid
   * @return array $arr or boolean false
   */
    public function getByDir($dirid=0)
    {
        $arr = array();
        $sql = "SELECT typeid,typename,level FROM ".$this->db->prefix("efqdiralpha1_itemtypes")." WHERE dirid=".intval($dirid)."";
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        while (list($typeid, $typename, $level) = $this->db->fetchRow($result)) {
            $arr[$typeid] = array('typeid' => $typeid,'typename' => $typename,'level' => $level);
        }
        return $arr;
    }

    /**
     * Function delete: Delete record
     * 
     * @author EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version 1.0.0
     * 
     * @param   int   $orderid - Default: '0' - Order ID
     * @return	array	$arr
     */
    public function delete($obj)
    {
        $tablename = "efqdiralpha1_itemtypes";
        $keyName = "typeid";
        $id = $obj->getVar($keyName);
        if ($id != 0) {
            $sql = "DELETE FROM ".$this->db->prefix($tablename)." WHERE ".$keyName."=".intval($id)."";
            $this->db->queryF($sql);
            return true;
        } else {
            return false;
        }
    }
}
