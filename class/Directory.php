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

/**
 * Class efqDirectory
 * Manages operations for directories
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */

use XoopsModules\Efqdirectory;

/**
 * Class Directory
 * @package XoopsModules\Efqdirectory
 */
class Directory extends \XoopsObject
{
    /**
     * efqDirectory constructor.
     * @param bool $directory
     */
    public function __construct($directory = false)
    {
        global $moddir;
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('dirid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('postfix', XOBJ_DTYPE_TXTBOX);
        $this->initVar('open', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('img', XOBJ_DTYPE_TXTBOX);
        $this->initVar('allowreview', XOBJ_DTYPE_INT, 0, false);

        if (false !== $directory) {
            if (is_array($directory)) {
                $this->assignVars($directory);
            } else {
                $directoryHandler = Efqdirectory\Helper::getInstance()->getHandler('Directory');
                $objDirectory     = $directoryHandler->get($directory);
                foreach ($objDirectory->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
                unset($objDirectory);
            }
        }
    }
}
