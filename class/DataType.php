<?php namespace XoopsModules\Efqdirectory;

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
 * Class SubscriptionOffer
 * Manages operations for subscription offers
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2008
 * @version   1.1.0
 */
class DataType extends \XoopsObject
{

    /**
     * SubscriptionOffer::SubscriptionOffer()
     *
     * @param bool $offer
     * @internal param bool $itemtype
     */
    public function __construct($offer = false)
    {
        global $moddir;
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
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
