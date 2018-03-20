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
 * Class Listing
 * Manages operations for listings
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class ListingData extends \XoopsObject
{
    public $_updated  = false;
    public $_inserted = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        // class constructor;
        //$this->setCurrentUser();
        $this->initVar('dataid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('itemid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('dtypeid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('itemid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('value', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('created', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('customtitle', XOBJ_DTYPE_TXTBOX, null, false, 255);
    }

    //Set variable $_updated to true of false (default)

    /**
     * @param bool $set
     */
    public function setUpdated($set = false)
    {
        $this->_updated = $set;
    }

    //Set variable $_inserted to true of false (default)

    /**
     * @param bool $set
     */
    public function setInserted($set = false)
    {
        $this->_inserted = $set;
    }

    /**
     * @param $arr
     * @return bool
     */
    public function setListingData($arr)
    {
        if (is_array($arr)) {
            $vars = $this->getVars();
            foreach ($vars as $k => $v) {
                $this->setVar($k, $arr[$k]);
            }
        } else {
            return false;
        }

        return true;
    }
}
