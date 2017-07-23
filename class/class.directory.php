<?php
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
/**
 * Class efqDirectory
 * Manages operations for directories
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqDirectory extends XoopsObject
{
    public function __construct($directory = false)
    {
        global $moddir;
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('dirid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('postfix', XOBJ_DTYPE_TXTBOX);
        $this->initVar('open', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('img', XOBJ_DTYPE_TXTBOX);
        $this->initVar('allowreview', XOBJ_DTYPE_INT, 0, false);
        
        if ($directory != false) {
            if (is_array($directory)) {
                $this->assignVars($directory);
            } else {
                $directory_handler = xoops_getModuleHandler('directory', $moddir);
                $objDirectory = $directory_handler->get($directory);
                foreach ($objDirectory->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                unset($objDirectory);
            }
        }
    }
}

/**
 * Class efqDirectoryHandler
 * Manages database operations for directories
 * 
 * @package efqDirectory
 * @author EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version 1.1.0
 */
class efqDirectoryHandler extends XoopsObjectHandler
{
    public function __construct()
    {
        $this->db =XoopsDatabaseFactory::getDatabaseConnection();
    }
    
    /**
     * create instance of directory class or reset the existing instance.
     * 
     * @return object $directory
     */
    public function &create($isNew = true)
    {
        $directory = new efqDirectory();
        if ($isNew) {
            $directory->setNew();
        }
        return $directory;
    }
     
    /**
     * retrieve a directory
     * 
     * @param int $dirid ID of the directory
     * @return mixed reference to the {@link efqDirectory} object, FALSE if failed
     */
    public function &get($dirid = false)
    {
        if ($dirid == false) {
            return false;
        }
        $dirid = intval($dirid);
        if ($dirid > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('efqdiralpha1_dir') . ' WHERE dirid=' . $dirid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $directory =& $this->create(false);
            $directory->assignVars($this->db->fetchArray($result));
            return $directory;
        }
        return false;
    }
     
     /**
     * retrieve all directories
     * 
     * @return mixed reference to the {@link efqDirectory} object, FALSE if failed
     */
    public function &getAll()
    {
        $sql = 'SELECT dirid,postfix,open,name,descr,img FROM ' . $this->db->prefix('efqdiralpha1_dir') . '';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        while (list($dirid, $postfix, $open, $name, $descr, $img) = $this->db->fetchRow($result)) {
            $arr[] = array('dirid' => $dirid,
                'postfix' => $postfix,
                'open' => $open,
                'name' => $name,
                'descr' => $descr,
                'img' => $img );
        }
        return $arr;
    }
    
         
    /**
     * retrieve all directory ID's
     * 
     * @return array $idarray
     */
    public function &getAllDirectoryIds($idarray = array())
    {
        $sql = 'SELECT dirid FROM ' . $this->db->prefix('efqdiralpha1_dir') . '';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        while (list($r_id) = $this->db->fetchRow($result)) {
            array_push($idarray, $r_id);
        }
        return $idarray;
    }
    
    /**
     * retrieve all directory ID's and titles as array
     * 
     * @return array $idarray
     */
    public function &getAllDirectoryTitles($arr = array())
    {
        $sql = 'SELECT dirid, name FROM ' . $this->db->prefix('efqdiralpha1_dir') . '';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        while (list($r_id, $r_title) = $this->db->fetchRow($result)) {
            $result_arr[$r_id] = $r_title;
            //array_push($arr, $result_arr);
        }
        return $result_arr;
    }
    
    
    
    /**
     * count number of directories and if count == 1, set directory.
     * 
     * @return mixed $result, FALSE if failed, 0 if count is 0.
     */
    public function countAll()
    {
        global $xoopsDB;
        $block = array();
        $myts =& MyTextSanitizer::getInstance();
        $dirid = 0;
        $result = $xoopsDB->query('SELECT dirid FROM ' . $xoopsDB->prefix('efqdiralpha1_dir') . '');
        $num_results = $xoopsDB->getRowsNum($result);
        if (!$result) {
            return false;
        } elseif ($num_results == 0) {
            return 0;
        } elseif ($num_results == 1) {
            $row = mysql_fetch_array($result);
            $dirid = $row['dirid'];
            return $dirid;
        } else {
            return false;
        }
    }
    
    /**
     * retrieve all directory ID's and names
     * 
     * @return array $array
     */
    public function directoryArray($dashes = false)
    {
        $sql = 'SELECT dirid, name FROM ' . $this->db->prefix('efqdiralpha1_dir') . ' ORDER BY name ASC';
        $result = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $result = $this->db->query($sql);
        if ($dashes != false) {
            $arr = array('0' => '---');
        }
        while (list($dirid, $dirname) = $this->db->fetchRow($result)) {
            $arr[$dirid] = $dirname;
        }
        return $arr;
    }

    /**
     * Function insertDirectory inserts new record into DB
     * @author EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2008
     * @version 1.0.0
     * 
     * @param   object   $obj object
     * 
     * @return	bool	true if insertion is succesful, false if unsuccesful
     */
    public function insertDirectory($obj, $forceQuery=false)
    {
        $tablename = 'efqdiralpha1_dir';
        $keyName = 'dirid';
        $excludedVars = array();
        if ($obj instanceof efqDirectory) {
            // Variable part of this function ends. From this line you can copy
            // this function for similar object handling functions.
            $obj->cleanVars();
            $cleanvars = $obj->cleanVars;
        } else {
            return false;
        }
        $countVars = count($cleanvars);
        $i = 1;
        $strFields = '';
        $strValues = '';
        foreach ($cleanvars as $k => $v) {
            if (!in_array($k, $excludedVars)) {
                $strFields .= $k;
                $strValues .= "'".$v."'";
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
}
