<?php
/*
// ID: fieldtype.php 4-nov-2007 10:51:01 efqconsultancy
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
 * Class efqFieldType
 * Manages operations for field types
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqFieldType extends XoopsObject
{
	
	function efqFieldType($fieldtype=false)
	{
		global $moddir;
		$this->db = Database::getInstance();
	    $this->initVar('fieldtypeid', XOBJ_DTYPE_INT, null, false);
	    $this->initVar('dirid', XOBJ_DTYPE_INT, null, false);
	    $this->initVar('title', XOBJ_DTYPE_TXTBOX);
	    $this->initVar('fieldtype', XOBJ_DTYPE_TXTBOX);
	    $this->initVar('descr', XOBJ_DTYPE_TXTAREA);
	    $this->initVar('ext', XOBJ_DTYPE_TXTBOX);
		$this->initVar('activeyn', XOBJ_DTYPE_INT, 0, false);
		
		if ($fieldtype != false) {
			if (is_array($fieldtype)) {
                $this->assignVars($fieldtype);
            } else {
				$fieldtype_handler = new efqFieldTypeHandler($this->db);
                $objFieldtype =& $fieldtype_handler->get($fieldtype);
                foreach ($objFieldtype->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                unset($objFieldtype);
            }
        }
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
class efqFieldTypeHandler extends XoopsObjectHandler
{
	
	var $db; //Database reference
	
	function efqFieldTypeHandler()
	{
		$this->db = Database::getInstance();
	}
	
	/**
	 * Function insertFieldType inserts record into DB
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2008
	 * @version 1.0.0
	 * 
	 * @param   object   $obj object
	 * 
	 * @return	bool	true if insertion is succesful, false if unsuccesful
	 */
	function insertFieldType($obj, $forceQuery=false) {
		$tablename = "efqdiralpha1_fieldtypes";
		$keyName = "typeid";
		$excludedVars = array();
		if ($obj instanceof efqFieldType) {
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
			if ( !in_array($k, $excludedVars) ) {
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
	 * Function updateFieldType updates record
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2008
	 * @version 1.0.0
	 * 
	 * @param   object
	 * 
	 * @return	bool	true if update is succesful, false if unsuccesful
	 */
	function updateFieldType($obj, $forceQuery=false) {
		$tablename = "efqdiralpha1_fieldtypes";
		$keyName = "typeid";
		$excludedVars = array();
		if ($obj instanceof efqFieldType) {
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
			if ( !in_array($k, $excludedVars) ) {
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
	
	function delete($obj_fieldtype,$force=false)
	{
		if (!is_object($obj_fieldtype)) {
			return false;
		}
		$sql = "DELETE * FROM ".$this->db->prefix("efqdiralpha1_fieldtypes")." WHERE typeid='".intval($this->getVar("typeid"))."'";
		if ($force) {
			if (!$result = $this->db->queryF($sql)) {
            	return false;
        	}
		} else {
			if (!$result = $this->db->query($sql)) {
            	return false;
        	}
		}
        return true;
	}
	
	/**
     * retrieve all field types for a directory
     * 
     * @param int $dirid ID of the directory
     * @return array $arr or boolean false
     */
	function getByDir($dirid=0)
	{
		//Get all fieldtypes for the selected directory
		$sql = "SELECT typeid,title,fieldtype,descr,ext,activeyn FROM ".$this->db->prefix("efqdiralpha1_fieldtypes")." WHERE dirid=".intval($dirid)."";
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
		$result = $this->db->query($sql);
		$arr = array();
        while ( list($typeid,$title,$fieldtype,$descr,$ext,$activeyn) = $this->db->fetchRow($result) ) {
			$arr[$typeid] = array('typeid' => $typeid,'title' => $title,'fieldtype' => $fieldtype,'descr' => $descr, 'ext' => $ext, 'activeyn' => $activeyn);
		}
        return $arr;
	}
	
	
}
?>