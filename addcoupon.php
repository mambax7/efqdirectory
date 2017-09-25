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

/*
* This file handles the addition of coupons to listings.
* Accessible to listing owners and administrators only.
*/

include __DIR__ . '/header.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
require_once __DIR__ . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once __DIR__ . '/class/class.formimage.php';
require_once __DIR__ . '/class/class.image.php';
require_once __DIR__ . '/class/class.couponhandler.php';

$eh = new ErrorHandler; //ErrorHandler object

$moddir = $xoopsModule->getVar('dirname');
$mytree = new MyXoopsTree($xoopsDB->prefix($efqdirectory->getDirname() . '_cat'), 'cid', 'pid');

//$moddir = $xoopsModule->getvar("dirname");
$couponid = isset($_GET['couponid']) ? (int)$_GET['couponid'] : 0;
if (isset($_POST['itemid'])) {
    $itemid = (int)$_POST['itemid'];
} elseif (isset($_GET['item'])) {
    $itemid = (int)$_GET['item'];
} else {
    $itemid = 0;
}

if (empty($xoopsUser) || !$xoopsUser->isAdmin($xoopsModule->mid()) || (0 == $itemid && empty($_POST['delete']))) {
    redirect_header('index.php', 3, _NOPERM);
}

if (isset($_POST['lbr'])) {
    $lbr = (int)$_POST['lbr'];
} else {
    $lbr = 0;
}
if ($couponid > 0) {
    $coupon = new efqCouponHandler();
    $coupon->get($couponid);
    //$couponid = $coupon->couponid;
    $myts        = MyTextSanitizer::getInstance();
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
    $itemid      = isset($_POST['itemid']) ? (int)$_POST['itemid'] : (isset($_GET['item']) ? (int)$_GET['item'] : 0);
    $couponid    = isset($_POST['couponid']) ? (int)$_POST['couponid'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $publish     = isset($_POST['publish']) ? $_POST['publish'] : 0;
    $image       = isset($_POST['image']) ? $_POST['image'] : '';
    $expire      = isset($_POST['expire']) ? $_POST['expire'] : 0;
    $heading     = isset($_POST['heading']) ? $_POST['heading'] : '';
    if ($expire > 0) {
        $setexpire = 1;
    } else {
        $setexpire = 0;
        $expire    = time() + 3600 * 24 * 7;
    }
}

if (!empty($_POST['submit'])) {
    $coupon = new efqCouponHandler();
    if (isset($_POST['couponid'])) {
        $couponid = (int)$_POST['couponid'];
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
        $coupon   = new efqCouponHandler();
        $couponid = (int)$_POST['couponid'];
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
