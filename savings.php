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

include __DIR__ . '/header.php';
$myts = MyTextSanitizer::getInstance();// MyTextSanitizer object
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once __DIR__ . '/class/class.couponhandler.php';

$eh     = new ErrorHandler;
$moddir = $xoopsModule->getVar('dirname');

$itemid = isset($_GET['itemid']) ? (int)$_GET['itemid'] : 0;
$catid  = isset($_GET['catid']) ? (int)$_GET['catid'] : 0;

$GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_savings.tpl';
include XOOPS_ROOT_PATH . '/header.php';
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);

$coupon = new efqCouponHandler();
if ($itemid) {
    $coupons = $coupon->getByItem($itemid);
}
$sql         = 'SELECT itemid, title FROM ' . $xoopsDB->prefix($efqdirectory->getDirname() . '_items') . ' WHERE itemid=' . $itemid . '';
$item_result = $xoopsDB->query($sql);
$numrows     = $xoopsDB->getRowsNum($item_result);
//echo $numrows;
while (list($itemid, $itemtitle) = $xoopsDB->fetchRow($item_result)) {
    $title = $myts->htmlSpecialChars($itemtitle);
    $item  = $itemid;
}
$xoopsTpl->assign('itemtitle', $title);
$xoopsTpl->assign('itemid', $item);

foreach ($coupons as $coup) {
    //echo $coup['descr'];
    $xoopsTpl->append('coupons', [
        'couponid' => $coup['couponid'],
        'itemid'   => $coup['itemid'],
        'descr'    => $myts->displayTarea($coup['descr']),
        'image'    => $coup['image'],
        'publish'  => $coup['publish'],
        'expire'   => $coup['expire'],
        'heading'  => $coup['heading'],
        'lbr'      => $coup['lbr']
    ]);
}
if ($xoopsUser) {
    $xoopsTpl->assign('admin', $xoopsUser->isAdmin($xoopsModule->mid()));
}
$xoopsTpl->assign('moddir', $moddir);

include XOOPS_ROOT_PATH . '/footer.php';
