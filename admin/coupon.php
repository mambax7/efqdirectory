<?php
// $Id: categories.php,v 0.18 2006/03/23 21:37:00 wtravel
//  ------------------------------------------------------------------------ //
//                				EFQ Directory			                     //
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
//	Hacks provided by: Adam Frick											 //
// 	e-mail: africk69@yahoo.com												 //
//	Purpose: Create a yellow-page like business directory for xoops using 	 //
//	the mylinks module as the foundation.									 //
// ------------------------------------------------------------------------- //

include '../../../include/cp_header.php';
//$mydirname = $xoopsModule->getVar('dirname');

xoops_cp_header();
include '../include/functions.php';

adminmenu(-1);
if ( file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
	include "../language/".$xoopsConfig['language']."/main.php";
} else {
	include "../language/english/main.php";
}

$coupon_handler =& xoops_getmodulehandler('coupon', $moddir);
//$coupon_handler = new ListingsCouponHandler;

if (!isset($_GET['op'])) {
    header('location', 'index.php');
}
$op = trim($_GET['op']);
$criteria = new CriteriaCompo();

switch ($op) {
    case 'expired':
		$criteria->add(new Criteria('expire', time(), '<'));
   		$criteria->add(new Criteria('expire', '0', '!='));
    break;
    
	case 'noexp':
		$criteria->add(new Criteria('expire', '0', '='));	
    break;
	
	case 'future':
        $criteria->add(new Criteria('publish', time(), '>'));
    break;
}
$coupons = $coupon_handler->getObjects($criteria, false);
$coupons = $coupon_handler->prepare2show($coupons);
$output = "<table>";
foreach ($coupons as $catid => $category) {
    $output .= '<tr>
            <th colspan="2">				
				'.$category['catTitle'].';
       </th>
        </tr>';
    foreach ($category['coupons'] as $key => $coupon) {
        if (!isset($class) || ($class != "odd")) {
            $class = "odd";
        }
        else {
            $class = "even";
        }
        $output .= "<tr class='".$class."'>
                <td>";
        $output .= '<a href="'.XOOPS_URL.'/modules/' .$moddir. '/addcoupon.php?couponid='.$coupon['couponid'].'"><img src="'.XOOPS_URL.'/modules/' .$mydirname. '/images/editicon.gif" alt="'._MD_EDITCOUPON.'" /></a>
                        <a href="'.XOOPS_URL.'/modules/' .$moddir. '/singlelink.php?lid='.$coupon['lid'].'">'.$coupon['linkTitle'].'</a><br />
                        <br />
                        '._MD_PUBLISHEDON.' '.$coupon['publish'];
        if ($coupon['expire'] > 0) {
            $output .= "<br />"._MD_EXPIRESON.$coupon['expire'];
        }
        $output .= "<br />"._MD_COUPONHITS." : ".$coupon['counter'];
        $output .= '</div>
                </td>
                <td valign="top">'.$coupon['heading'].'<br />'.$coupon['description'].'</td>
            </tr>
            <tr>
                <td colspan="2" class="foot">
                        <a href="'.XOOPS_URL.'/modules/' .$moddir. '/addcoupon.php?couponid='.$coupon['couponid'].'">'._MD_EDITCOUPON.'</a> 
                </tr>';
    }
}
$output .= "</table>";
if (count($coupons) < 1) {
    $output = _MD_NOSAVINGS;
}

echo $output;

xoops_cp_footer();
echo "<p class=\"mytext\">&nbsp;</p>";
?>