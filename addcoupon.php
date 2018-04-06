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

/*
* This file handles the addition of coupons to listings.
* Accessible to listing owners and administrators only.
*/

include __DIR__ . '/header.php';
$myts = \MyTextSanitizer::getInstance(); // MyTextSanitizer object
// require_once __DIR__ . '/class/xoopstree.php';
//require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
// require_once __DIR__ . '/class/class.formimage.php';
// require_once __DIR__ . '/class/class.image.php';
// require_once __DIR__ . '/class/class.couponhandler.php';

//$eh = new ErrorHandler; //ErrorHandler object

$moddir = $xoopsModule->getVar('dirname');
$mytree = new Efqdirectory\MyXoopsTree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');

//$moddir = $xoopsModule->getvar("dirname");
$couponid = \Xmf\Request::getInt('couponid', 0, 'GET');

if (\Xmf\Request::hasVar('itemid', 'POST')) {
    $itemid = \Xmf\Request::getInt('itemid', 0, 'POST');
} else {
    $itemid = \Xmf\Request::getInt('item', 0, 'GET');
}

if (empty($xoopsUser) || !$xoopsUser->isAdmin($xoopsModule->mid()) || (0 == $itemid && empty($_POST['delete']))) {
    redirect_header('index.php', 3, _NOPERM);
}

$lbr = \Xmf\Request::getInt('lbr', 0, 'POST');

if ($couponid > 0) {
    $coupon = new Efqdirectory\CouponHandler();
    $coupon->get($couponid);
    //$couponid = $coupon->couponid;
    $myts        = \MyTextSanitizer::getInstance();
    $lbr         = $coupon->lbr;
    $description = $coupon->descr;
    $image       = $coupon->image;
    $heading     = $coupon->heading;
    $publish     = $coupon->publish > 0 ? $coupon->publish : time();
    $expire      = $coupon->expire;
    $dohtml      = 1;
    $dobr        = $lbr;
    if ($expire > 0) {
        $setexpire = 1;
    } else {
        $setexpire = 0;
        $expire    = time() + 3600 * 24 * 7;
    }
} else {
    $itemid      = \Xmf\Request::getInt('itemid', \Xmf\Request::getInt('item', 0, 'GET'), 'POST');
    $couponid    = \Xmf\Request::getInt('couponid', null, 'POST');
    $description = \Xmf\Request::getString('description', '', 'POST');
    $publish     = \Xmf\Request::getInt('publish', 0, POST);
    $image       = \Xmf\Request::getString('image', '', 'POST');
    $expire      = \Xmf\Request::getInt('expire', 0, POST);
    $heading     = \Xmf\Request::getString('heading', '', 'POST');
    if ($expire > 0) {
        $setexpire = 1;
    } else {
        $setexpire = 0;
        $expire    = time() + 3600 * 24 * 7;
    }
}

if (!empty($_POST['submit'])) {
    $coupon = new Efqdirectory\CouponHandler();
    if (isset($_POST['couponid'])) {
        $couponid = \Xmf\Request::getInt('couponid', 0, 'POST');
        $message  = _MD_COUPONEDITED;
    } else {
        $coupon->_new = true;
        $message      = _MD_COUPONADDED;
    }
    if (!$coupon->create()) {
        $coupon->message = _MD_ERR_ADDCOUPON;
    }
    redirect_header('listing.php?item=' . $itemid, 2, $coupon->message);
} elseif (!empty($_POST['delete'])) {
    if (!empty($_POST['ok'])) {
        if (empty($_POST['couponid'])) {
            redirect_header('index.php', 2, _MD_ERR_COUPONIDMISSING);
        }
        $coupon   = new Efqdirectory\CouponHandler();
        $couponid = \Xmf\Request::getInt('couponid', 0, 'POST');
        if ($coupon->delete($couponid)) {
            redirect_header('listing.php?item=' . $itemid, 2, _MD_COUPONDELETED);
        }
    } else {
        include XOOPS_ROOT_PATH . '/header.php';
        xoops_confirm(['delete' => 'yes', 'couponid' => $couponid, 'ok' => 1], 'addcoupon.php?item=' . $itemid . '', _MD_COUPONRUSURE);
        require_once XOOPS_ROOT_PATH . '/footer.php';
        exit();
    }
}
include XOOPS_ROOT_PATH . '/header.php';
include __DIR__ . '/include/couponform.php';
require_once XOOPS_ROOT_PATH . '/footer.php';
