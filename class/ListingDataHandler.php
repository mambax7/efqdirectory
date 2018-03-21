<?php namespace XoopsModules\Efqdirectory;

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
 * Class ListingHandler
 * Manages database operations for listings
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class ListingDataHandler extends \XoopsObjectHandler
{
//    public $errorhandler;

    /**
     * ListingDataHandler constructor.
     */
    public function __construct()
    {
        //Instantiate class
//        global $eh;
        $this->db           = \XoopsDatabaseFactory::getDatabaseConnection();
//        $this->errorhandler = $eh;
    }

    /**
     * @param int $itemid
     * @return array
     */
    public function getData($itemid = 0)
    {
        $arr         = [];
        $sql         = 'SELECT DISTINCT t.dtypeid, t.title, t.section, f.typeid, f.fieldtype, f.ext, t.options, d.dataid, d.itemid, d.value, d.created, t.custom ';
        $sql         .= 'FROM ' . $this->db->prefix('efqdiralpha1_item_x_cat') . ' ic, ' . $this->db->prefix('efqdiralpha1_dtypes_x_cat') . ' xc, ' . $this->db->prefix('efqdiralpha1_fieldtypes') . ' f, ' . $this->db->prefix('efqdiralpha1_dtypes') . ' t ';
        $sql         .= 'LEFT JOIN ' . $this->db->prefix('efqdiralpha1_data') . ' d ON (t.dtypeid=d.dtypeid AND d.itemid=' . $itemid . ') ';
        $sql         .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=" . $itemid . '';
        $data_result = $this->db->query($sql) ; //|| $eh->show('0013');
        if (!$data_result) {
            $logger = \XoopsLogger::getInstance();
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }
        while (false !== (list($dtypeid, $title, $section, $ftypeid, $fieldtype, $ext, $options, $dataid, $itemid, $value, $created, $custom) = $this->db->fetchRow($data_result))) {
            $arr[] = [
                'dtypeid'     => $dtypeid,
                'title'       => $title,
                'section'     => $section,
                'ftypeid'     => $ftypeid,
                'fieldtype'   => $fieldtype,
                'ext'         => $ext,
                'options'     => $options,
                'dataid'      => $dataid,
                'itemid'      => $itemid,
                'value'       => $value,
                'created'     => $created,
                'customtitle' => $custom
            ];
        }

        return $arr;
    }

    /**
     * Function updateListingData updates listing data
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param   object $obj object of type listing
     *
     * @param bool     $forceQuery
     * @return bool true if update is successful, false if unsuccessful
     */
    public function updateListingData($obj, $forceQuery = false)
    {
        $tablename = 'efqdiralpha1_data';
        $keyName   = 'dataid';
        if ($obj instanceof ListingData) {
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
            $strSet .= $k . '=' . "'" . $v . "'";
            if ($i < $countVars) {
                $strSet .= ', ';
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
     * Function insertListingData inserts listing data into DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param      $obj
     * @param bool $forceQuery
     * @return bool true if insertion is succesful, false if unsuccesful
     * @internal  param object $objListing object of type listing
     */
    public function insertListingData($obj, $forceQuery = false)
    {
        $tablename = 'efqdiralpha1_data';
        $keyName   = 'dataid';
        if ($obj instanceof ListingData) {
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
            $strFields .= $k;
            $strValues .= "'" . $v . "'";
            if ($i < $countVars) {
                $strFields .= ', ';
                $strValues .= ', ';
            }
            $i++;
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
}
