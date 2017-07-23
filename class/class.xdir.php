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
 * Class efqXdirHandler
 * Manages database operations for migration from the xDirectory module
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class efqXdirHandler extends xoopsObjectHandler
{
    public $db; //Database reference
    public $objXdir;
    public $errors; //array();
    public $categories; //array();
    public $listings; //array();
    public $xdir_cats; //array();
    public $efq_dirid;
    public $fieldtypes; //array();
    public $datatypes; //array();

    /**
     * efqSubscriptionOfferHandler::efqItemTypeHandler()
     *
     */
    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }

    /**
     * @param $arr
     */
    public function set_xdir_cats($arr)
    {
        if (is_array($arr)) {
            $this->xdir_cats = $arr;
        }
    }

    /**
     * @return mixed
     */
    public function get_xdir_cats()
    {
        return $this->xdir_cats;
    }

    /**
     * @return mixed
     */
    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * getCategories_xdir()
     *
     * Get xdir categories from database and return
     *
     * @return \arr|bool
     */
    public function setCategories_xdir()
    {
        $arr    = array();
        $sql    = 'SELECT cid, pid, title, imgurl FROM ' . $this->db->prefix('efqdiralpha1_xdir_cat');
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if ($numrows > 0) {
            while (list($cid, $pid, $title, $imgurl) = $this->db->fetchRow($result)) {
                $arr[] = array('cid' => $cid, 'pid' => $pid, 'title' => $title, 'imgurl' => $imgurl);
            }
        } else {
            return false;
        }
        $this->set_xdir_cats($arr);

        return true;
    }

    /**
     * @param int $dirid
     * @return bool
     */
    public function saveCategories($dirid = 0)
    {
        $tablename = 'efqdiralpha1_cat';
        $xdir_cats = $this->get_xdir_cats();
        foreach ($xdir_cats as $xdir_cat) {
            $cid    = $xdir_cat['cid'];
            $pid    = $xdir_cat['pid'];
            $title  = $xdir_cat['title'];
            $imgurl = $xdir_cat['imgurl'];
            //Set fields and values
            $strFields = 'cid,dirid,title,active,pid,img,allowlist';
            $strValues = "$cid,$dirid,'$title',1,$pid,'$imgurl',1";
            $sql       = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->db->prefix($tablename), $strFields, $strValues);
            if ($this->db->queryF($sql)) {
                $itemid = $this->db->getInsertId();
                $obj->setVar($keyName, $itemid);

                return true;
            }
        }
    }

    /**
     * Function createDataTypes inserts datatypes into DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     * @return bool true if insertion is succesful, false if unsuccesful
     * @internal  param object $obj object
     *
     */
    public function createDataTypes()
    {
        $datatype_handler = new efqDataTypeHandler();
        $arr[]            = array('title' => _MD_XDIR_DTYPE_ADDRESS, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_ADDRESS2, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_CITY, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_STATE, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_ZIP, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_COUNTRY, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_PHONE, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_FAX, 'fieldtype' => _MD_XDIR_FIELDTYPE_TEXTBOX);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_EMAIL, 'fieldtype' => _MD_XDIR_FIELDTYPE_EMAIL);
        $arr[]            = array('title' => _MD_XDIR_DTYPE_URL, 'fieldtype' => _MD_XDIR_FIELDTYPE_URL);
        foreach ($arr as $datatype) {
            $objDataType = new efqFieldType;
            $objDataType->setVar('title', $datatype['title']);
            $objDataType->setVar('section', 0);
            $objDataType->setVar('uid', $xoopsUser->getVar('uid'));
            $objDataType->setVar('defaultyn', 1);
            $objDataType->setVar('created', time());
            $objDataType->setVar('activeyn', 1);
            $objDataType->setVar('fieldtypeid', $this->fieldtypes[$datatype['fieldtype']]);
            $datatype_handler->insertDataType($objDataType, true);
            $datatypes[$datatype['title']] = $objDataType->getVar('dtypeid');
        }
    }

    /**
     * Function createFieldTypes inserts field types into DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     * @return bool true if insertion is succesful, false if unsuccesful
     * @internal  param object $obj object
     *
     */
    public function createFieldTypes()
    {
        $fieldtype_handler = new efqFieldTypeHandler();
        $arr[]             = array('title' => _MD_XDIR_FIELDTYPE_TEXTBOX, 'fieldtype' => 'textbox');
        $arr[]             = array('title' => _MD_XDIR_FIELDTYPE_EMAIL, 'fieldtype' => 'email');
        $arr[]             = array('title' => _MD_XDIR_FIELDTYPE_URL, 'fieldtype' => 'url');
        foreach ($arr as $fieldtype) {
            $objFieldtype = new efqFieldType;
            $objFieldtype->setVar('title', $fieldtype['title']);
            $objFieldtype->setVar('fieldtype', $fieldtype['fieldtype']);
            $objFieldtype->setVar('dirid', $this->efq_dirid);
            $fieldtype_handler->insertFieldType($objFieldtype, true);
            $fieldtypes[$fieldtype['title']] = $objFieldtype->getVar('typeid');
        }
    }

    /**
     * Function doMigrate migrates xDirectory to EFQ Directory
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param int $dirid
     * @return bool true if insertion is succesful, false if unsuccesful
     * @internal  param object $obj object
     *
     */
    public function doMigrate($dirid = 0)
    {
        $this->setCategories_xdir();
        $this->saveCategories($this->efq_dirid);
        $this->createFieldTypes();
        $this->createDataTypes();
    }
}
