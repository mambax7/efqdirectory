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

use XoopsModules\Efqdirectory;

include __DIR__ . '/../../mainfile.php';
include XOOPS_ROOT_PATH . '/header.php';
$moduleDirName = basename(__DIR__);
$coupid        = \Xmf\Request::getInt('coupid', 0, 'GET');
if (!($coupid > 0)) {
    redirect_header('index.php');
}

/**
 * @param $coupid
 */
function PrintPage($coupid)
{
    global $xoopsModule, $xoopsTpl, $xoopsModuleConfig, $moduleDirName;
    $couponHandler = Efqdirectory\Helper::getInstance()->getHandler('Coupon');
    $couponHandler->increment($coupid);
    $coupon     = $couponHandler->getLinkedCoupon($coupid);
    $coupon_arr = $couponHandler->prepare2show($coupon);
    //$xoopsTpl->assign('coupon_footer', $helper->getConfig('coupon_footer'));
    $xoopsTpl->assign('coupon', $coupon_arr['items']['coupons'][0]);
    $xoopsTpl->template_dir = XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname();
    $xoopsTpl->display('db:efqdiralpha1_print_savings.tpl');
}

//Smarty directory autodetect
$smartydir = $moduleDirName;
$xoopsTpl->assign('smartydir', $smartydir);
PrintPage($coupid);
