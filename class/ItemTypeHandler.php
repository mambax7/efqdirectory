<?php namespace XoopsModules\Efqdirectory;

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

use XoopsModules\Efqdirectory;

/**
 * Class FieldTypeHandler
 * Manages database operations for field types
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class ItemTypeHandler extends \XoopsObject
{
    public $db; //Database reference
    public $objItemType;

    /**
     * ItemTypeHandler::ItemTypeHandler()
     *
     */
    public function __construct()
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
    }

    /**
     * Function insert inserts new record into DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param   ItemType $obj object
     *
     * @param bool          $forceQuery
     * @return bool true if insertion is succesful, false if unsuccesful
     */
    public function insert($obj, $forceQuery = false)
    {
        $tablename    = 'efqdiralpha1_itemtypes';
        $keyName      = 'typeid';
        $excludedVars = [];
        if ($obj instanceof ItemType) {
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
     * Function update updates record in DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param      $obj
     * @param bool $forceQuery
     * @return bool true if update is succesful, false if unsuccesful
     * @internal  param object $objOffer object of type listing
     */
    public function update($obj, $forceQuery = false)
    {
        $tablename    = 'efqdiralpha1_itemtypes';
        $keyName      = 'typeid';
        $excludedVars = [];
        if ($obj instanceof ItemType) {
            // Variable part of this function ends. From this line you can copy
            // this function for similar object handling functions.
            $obj->cleanVars();
            $cleanvars = $obj->cleanVars;
            $keyValue  = $obj->getVar($keyName);
        } else {
            return false;
        }
        $countVars = count($cleanvars);
        $i         = 0;
        $strSet    = '';
        $strValues = '';
        foreach ($cleanvars as $k => $v) {
            if (!in_array($k, $excludedVars)) {
                if ($i < $countVars and $i > 0) {
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
     * Function set()
     *
     * Sets the object with data from query
     *
     * @param int $typeid
     * @return bool true or false
     */
    public function set($typeid = 0)
    {
        $sql     = 'SELECT typeid,typename,level,dirid FROM ' . $this->db->prefix('efqdiralpha1_itemtypes') . ' WHERE typeid=' . (int)$typeid . '';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        if ($numrows > 0) {
            while (false !== (list($typeid, $typename, $level, $dirid) = $this->db->fetchRow($result))) {
                if (!$this->objItemType) {
                    $this->objItemType = new Efqdirectory\SubscriptionOffer();
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

    /**
     * @return mixed
     */
    public function getObjItemType()
    {
        return $this->objItemType;
    }

    /**
     * ItemTypeHandler::getByDir()
     *
     * @param integer $dirid
     * @return array|bool
     */
    public function getByDir($dirid = 0)
    {
        $arr = [];
        $sql = 'SELECT typeid,typename,level FROM ' . $this->db->prefix('efqdiralpha1_itemtypes') . ' WHERE dirid=' . (int)$dirid . '';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        while (false !== (list($typeid, $typename, $level) = $this->db->fetchRow($result))) {
            $arr[$typeid] = ['typeid' => $typeid, 'typename' => $typename, 'level' => $level];
        }

        return $arr;
    }

    /**
     * Function delete: Delete record
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param $obj
     * @return array|bool
     * @internal  param int $orderid - Default: '0' - Order ID
     */
    public function delete($obj)
    {
        $tablename = 'efqdiralpha1_itemtypes';
        $keyName   = 'typeid';
        $id        = $obj->getVar($keyName);
        if (0 != $id) {
            $sql = 'DELETE FROM ' . $this->db->prefix($tablename) . ' WHERE ' . $keyName . '=' . (int)$id . '';
            $this->db->queryF($sql);

            return true;
        } else {
            return false;
        }
    }
}
