<?php namespace XoopsModules\Efqdirectory;

/*
// ID: category.php 3-nov-2007 18:18:06 efqconsultancy
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
 * Class GmapHandler
 * Manages database operations for google maps
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class GmapHandler extends \XoopsObjectHandler
{
    //    public $db;

    /**
     * GmapHandler constructor.
     */
    public function __construct()
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
    }

    /**
     * create instance of Gmap class or reset the existing instance.
     *
     * @param bool $isNew
     * @return Gmap $Gmap
     */
    public function &create($isNew = true)
    {
        $gmap = new Gmap();
        if ($isNew) {
            $gmap->setNew();
        }

        return $gmap;
    }

    /**
     * retrieve all points from the database
     *
     * @param $gmap
     * @return mixed reference to the <a href='psi_element://Gmap'>Gmap</a> object, FALSE if failed
     * object, FALSE if failed
     * @internal param int $dirid ID of the directory
     */
    public function getPointsJS($gmap)
    {
        if (!is_object($gmap)) {
            return false;
        }
        $sql = 'SELECT * FROM ' . $this->db->prefix('efqdiralpha1_gmaps');
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $gmap       =& $this->create(false);
        $javaScript = '';
        while (false !== ($row = $this->db->fetchArray($result))) {
            //$row{'descr'} = addslashes($row{'descr'});
            $row['descr'] = addslashes($row['descr']);
            //$row{'descr'} = str_replace(';',',',$row{'descr'});
            $row['descr'] = str_replace(';', ',', $row['descr']);
            //$javaScript .= "mArray.push('{$row{'lat'}};{$row{'lon'}};{$row{'descr'}}')\n";
            //echo $row['lat'];
            $javaScript .= "mArray.push('{" . $row['lat'] . '};{' . $row['lon'] . '};{' . $row['descr'] . "}')\n";
        }
        $gmap->setPointsJS($javaScript);

        return true;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getGmapById($id = 0)
    {
        $arr    = [];
        $sql    = sprintf('SELECT * FROM %s WHERE id=%u', $this->db->prefix('efqdiralpha1_gmaps'), (int)$id);
        $result = $this->db->query($sql) ; //|| $eh->show('0013');
        while (false !== (list($id, $lat, $lon, $descr, $dataid) = $this->db->fetchRow($result))) {
            $arr = ['id' => $id, 'lat' => $lat, 'lon' => $lon, 'descr' => $descr, 'dataid' => $dataid];
        }

        return $arr;
    }

    /**
     * @param int $id
     * @return bool|Gmap
     */
    public function getByDataId($id = 0)
    {
        if (false === $id) {
            return false;
        }
        $id = (int)$id;
        echo $id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('efqdiralpha1_gmaps') . ' WHERE dataid=' . (int)$id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $gmap =& $this->create(false);
            $gmap->assignVars($this->db->fetchArray($result));

            return $gmap;
        }

        return false;
    }

    /**
     * Function insertGmap inserts google map data into DB
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param   Gmap $obj object
     *
     * @param bool      $forceQuery
     * @return bool true if insertion is succesful, false if unsuccesful
     */
    public function insertGmap($obj, $forceQuery = false)
    {
        $tablename = 'efqdiralpha1_gmaps';
        $keyName   = 'id';
        if ($obj instanceof Gmap) {
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

    /**
     * Function updateGmap updates google map data
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version   1.0.0
     *
     * @param   Gmap $obj object
     *
     * @param bool      $forceQuery
     * @return bool true if update is succesful, false if unsuccesful
     */
    public function updateGmap($obj, $forceQuery = false)
    {
        $tablename = 'efqdiralpha1_gmaps';
        $keyName   = 'id';
        if ($obj instanceof Gmap) {
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
            if ('id' !== $k) {
                $strSet .= $k . '=' . "'" . $v . "'";
                if ($i < $countVars) {
                    $strSet .= ', ';
                }
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
}
