<?php namespace XoopsModules\Efqdirectory;

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
 * Class SubscriptionOffer
 * Manages operations for subscription offers
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2008
 * @version   1.1.0
 */
class SubscriptionOffer extends \XoopsObject
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
        $this->initVar('offerid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dirid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('typeid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('duration', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('count', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('price', XOBJ_DTYPE_CURRENCY, 0.00, true);
        $this->initVar('activeyn', XOBJ_DTYPE_INT, 0, true, 2);
        $this->initVar('currency', XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('descr', XOBJ_DTYPE_OTHER, 0);
        $this->initVar('typename', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('level', XOBJ_DTYPE_INT, null, false, 10);
    }
}
