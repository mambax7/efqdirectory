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
/** @var Efqdirectory\Helper $helper */
$helper = Efqdirectory\Helper::getInstance();

include __DIR__ . '/header.php';
$myts = \MyTextSanitizer::getInstance();// MyTextSanitizer object
// require_once __DIR__ . '/class/xoopstree.php';
//require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
// require_once __DIR__ . '/class/class.listing.php';
// require_once __DIR__ . '/class/class.datafieldmanager.php';
// require_once __DIR__ . '/class/class.couponhandler.php';

$mytree           = new Efqdirectory\MyXoopsTree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');
$datafieldmanager = new Efqdirectory\DataFieldManager();
//$eh               = new ErrorHandler;

$GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_listing.tpl';
include XOOPS_ROOT_PATH . '/header.php';

if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
    $isadmin = true;
} else {
    $isadmin = false;
}

$get_catid = \Xmf\Request::getInt('catid', 0, 'GET');

if (!empty($_GET['item'])) {
    $get_itemid = \Xmf\Request::getInt('item', 0, 'GET');
} else {
    redirect_header('index.php', 2, _MD_NOVALIDITEM);
}

$moddir         = $xoopsModule->getVar('dirname');
$dirid          = getDirIdFromItem($get_itemid);
$islistingowner = false;

$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
if ($isadmin) {
    $adminlink = '<a href="' . XOOPS_URL . '/modules/' . $moddir . '/admin/index.php?op=edit&amp;item=' . $get_itemid . '"><img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/editicon2.gif" border="0" alt="' . _MD_EDITTHISLISTING . '"></a>';
} else {
    $adminlink = '';
}
$xoopsTpl->assign('adminlink', $adminlink);

$coupon         = new Efqdirectory\CouponHandler();
$listing        = new Efqdirectory\Listing();
$listinghandler = new Efqdirectory\ListingHandler();

$listing->setVars($listinghandler->getListing($get_itemid));

$pathstring = "<a href='index.php?dirid=" . $dirid . '\'>' . _MD_MAIN . '</a>&nbsp;:&nbsp;';
$pathstring .= $mytree->getNicePathFromId($get_catid, 'title', 'index.php?dirid=' . $dirid . '');

$editlink = '<a href="edit.php?item=' . $get_itemid . '"><img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/' . $xoopsConfig['language'] . '/listing-edit.gif" alt="' . _MD_EDIT_LISTING . '" title="' . _MD_EDIT_LISTING . '"></a>';

if (isset($xoopsUser) && null !== $xoopsUser) {
    if ($xoopsUser->getVar('uid') == $listing->getVar('uid')) {
        $islistingowner = true;
        $xoopsTpl->assign('listingowner', '1');
        if ('2' == $listing->getVar('status') and 1 == $helper->getConfig('autoapprove')) {
            $editrights = '1';
        } else {
            $editrights = '0';
        }
    } else {
        $editrights = '0';
    }
}
if (!$islistingowner and !$isadmin) {
    $listinghandler->incrementHits($get_itemid);
}

$type     = getTypeFromId($listing->getVar('typeid'));
$template = getTemplateFromCatid($get_catid);
if ('' !== $listing->getVar('logourl')) {
    $logourl = '<img src="' . XOOPS_URL . '/modules/' . $moddir . '/uploads/' . $listing->getVar('logourl') . '">';
} else {
    $logourl = '';
}

$myts   = \MyTextSanitizer::getInstance();
$html   = 1;
$smiley = 1;
$xcodes = 1;

$description = $myts->displayTarea($listing->getVar('description'), $html, $smiley, $xcodes);

$xoopsTpl->assign('category_path', $pathstring);
$xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
$xoopsTpl->assign('edit_link', $editlink);
$xoopsTpl->assign('type', $type);
$xoopsTpl->assign('creates', $listing->getVar('created'));
$xoopsTpl->assign('editrights', $listing->_editrights);
$xoopsTpl->assign('item_id', $listing->getVar('itemid'));
$xoopsTpl->assign('item_title', $listing->getVar('title'));
$xoopsTpl->assign('item_description', $description);
$xoopsTpl->assign('item_logo', $logourl);
$xoopsTpl->assign('lang_item_title', _MD_LANG_ITEMTITLE);
$xoopsTpl->assign('lang_item_description', _MD_LANG_ITEMDESCRIPTION);
$xoopsTpl->assign('lang_edit_item', _MD_LANG_EDIT_ITEM);
$xoopsTpl->assign('template', $template);

$listing->setDataTypes($listinghandler->getDataTypes($get_itemid));

if (count($listing->_datatypes) > 0) {
//    xoops_debug('o, o');
    $xoopsTpl->assign('datatypes', true);
    foreach ($listing->_datatypes as $datatype) {
        $xoopsTpl->append('section' . $datatype['section'] . '', ['icon' => $datatype['icon'], 'label' => $datatype['title'], 'value' => $datatype['value'], 'fieldtype' => $datatype['fieldtype']]);
    }
}

if ('0' == $helper->getConfig('allowcoupons')) {
    $xoopsTpl->assign('couponsallowed', '0');
} else {
    $xoopsTpl->assign('couponsallowed', '1');
    $xoopsTpl->assign('lang_addcoupon', _MD_ADDCOUPON);
    $xoopsTpl->assign('coupons', $coupon->getCountByLink($get_itemid));
}
if ('0' == $helper->getConfig('allowsubscr')) {
    $xoopsTpl->assign('subscrallowed', '0');
} else {
    $xoopsTpl->assign('subscrallowed', '1');
    $xoopsTpl->assign('lang_viewsubscription', _MD_VIEWSUBSCRIPTIONS);
}

include XOOPS_ROOT_PATH . '/footer.php';
