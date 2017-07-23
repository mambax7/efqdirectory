<?php
// $Id: menu.php 2 2008-01-27 18:16:55Z wtravel $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
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
$adminmenu[1]['title'] = _MI_EFQDIR_ADMENU2;
$adminmenu[1]['link'] = "admin/index.php";
$adminmenu[2]['title'] = _MI_EFQDIR_ADMENU3;
$adminmenu[2]['link'] = "admin/directories.php";
$adminmenu[3]['title'] = _MI_EFQDIR_ADMENU7;
$adminmenu[3]['link'] = "admin/categories.php";
$adminmenu[4]['title'] = _MI_EFQDIR_ADMENU4;
$adminmenu[4]['link'] = "admin/fieldtypes.php";
$adminmenu[5]['title'] = _MI_EFQDIR_ADMENU5;
$adminmenu[5]['link'] = "admin/index.php?op=listNewListings";
$adminmenu[6]['icon'] = "images/prefs.png";
$adminmenu[6]['small'] = "images/prefs_small.png";
//$adminmenu[5]['title'] = _MI_EFQDIR_ADMENU6;
//$adminmenu[5]['link'] = "admin/addresstypes.php";
?>