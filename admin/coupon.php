<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package      efqdirectory
 * @since
 * @author       Martijn Hertog (aka wtravel)
 * @author       XOOPS Development Team,
 */

require_once __DIR__ . '/admin_header.php';
//include __DIR__ . '/../../../include/cp_header.php';
//$mydirname = $xoopsModule->getVar('dirname');

xoops_cp_header();
include __DIR__ . '/../include/functions.php';

//adminmenu(-1);
if (file_exists('../language/' . $xoopsConfig['language'] . '/main.php')) {
    include __DIR__ . '/../language/' . $xoopsConfig['language'] . '/main.php';
} else {
    include __DIR__ . '/../language/english/main.php';
}

$couponHandler = xoops_getModuleHandler('coupon', $moddir);
//$couponHandler = new ListingsCouponHandler;

if (!isset($_GET['op'])) {
    header('location', 'index.php');
}
$op       = trim($_GET['op']);
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
$coupons = $couponHandler->getObjects($criteria, false);
$coupons = $couponHandler->prepare2show($coupons);
$output  = '<table>';
foreach ($coupons as $catid => $category) {
    $output .= '<tr>
        <th colspan="2">
        ' . $category['catTitle'] . ';
        </th>
        </tr>';
    foreach ($category['coupons'] as $key => $coupon) {
        if (!isset($class) || ($class != 'odd')) {
            $class = 'odd';
        } else {
            $class = 'even';
        }
        $output .= "<tr class='" . $class . '\'>
            <td>';
        $output .= '<a href="' . XOOPS_URL . '/modules/' . $moddir . '/addcoupon.php?couponid=' . $coupon['couponid'] . '"><img src="' . XOOPS_URL . '/modules/' . $mydirname . '/assets/images/editicon.gif" alt="' . _MD_EDITCOUPON . '"></a>
            <a href="' . XOOPS_URL . '/modules/' . $moddir . '/singlelink.php?lid=' . $coupon['lid'] . '">' . $coupon['linkTitle'] . '</a><br>
            <br>
            ' . _MD_PUBLISHEDON . ' ' . $coupon['publish'];
        if ($coupon['expire'] > 0) {
            $output .= '<br>' . _MD_EXPIRESON . $coupon['expire'];
        }
        $output .= '<br>' . _MD_COUPONHITS . ' : ' . $coupon['counter'];
        $output .= '</div>
            </td>
            <td valign="top">' . $coupon['heading'] . '<br>' . $coupon['description'] . '</td>
            </tr>
            <tr>
            <td colspan="2" class="foot">
            <a href="' . XOOPS_URL . '/modules/' . $moddir . '/addcoupon.php?couponid=' . $coupon['couponid'] . '">' . _MD_EDITCOUPON . '</a>
            </tr>';
    }
}
$output .= '</table>';
if (count($coupons) < 1) {
    $output = _MD_NOSAVINGS;
}

echo $output;

xoops_cp_footer();
echo '<p class="mytext">&nbsp;</p>';
