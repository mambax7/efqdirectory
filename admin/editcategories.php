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

include __DIR__ . '/../include/functions.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
$myts   = MyTextSanitizer::getInstance();
$eh     = new ErrorHandler;
$mytree = new XoopsTree($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat'), 'cid', 'pid');
require_once __DIR__ . '/../class/class.datafieldmanager.php';
$datafieldmanager = new efqDataFieldManager();
$moddir           = $xoopsModule->getVar('dirname');

if (isset($_GET['item'])) {
    $get_itemid = (int)$_GET['item'];
}
if (isset($_POST['item'])) {
    $post_itemid = (int)$_POST['item'];
}

if (isset($_POST['dirid'])) {
    $post_dirid = (int)$_POST['dirid'];
}

$eh = new ErrorHandler; //ErrorHandler object

if (isset($_GET['op'])) {
    $op = $_GET['op'];
} elseif (isset($_POST['op'])) {
    $op = $_POST['op'];
}

if (!empty($_POST['submit'])) {
    //Get all selectable categories and put the prefix 'selectcat' in front of the catid.
    //With all results check if the result has a corresponding $_POST value.
    $dirid             = getDirIdFromItem($post_itemid);
    $sql               = 'SELECT cid FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_item_x_cat') . " WHERE itemid='" . $post_itemid . '\'';
    $allitemcatsresult = $xoopsDB->query($sql);
    $numrows           = $xoopsDB->getRowsNum($allitemcatsresult);
    $count             = 0;
    $allitemcats       = array();
    if ($numrows > 0) {
        while (list($cid) = $xoopsDB->fetchRow($allitemcatsresult)) {
            $allitemcats[] = $cid;
        }
    }
    $activeitemcatsresult = $xoopsDB->query('SELECT cid FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_item_x_cat') . " WHERE itemid='" . $post_itemid . '\' AND active=\'1\'');
    $numrows              = $xoopsDB->getRowsNum($activeitemcatsresult);
    $count                = 0;
    $activeitemcats       = array();
    if ($numrows > 0) {
        while (list($cid) = $xoopsDB->fetchRow($activeitemcatsresult)) {
            $activeitemcats[] = $cid;
        }
    }
    $allcatsresult = $xoopsDB->query('SELECT cid FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat') . " WHERE active='1'");
    $numrows       = $xoopsDB->getRowsNum($allcatsresult);
    $allcats       = array();
    $postedcats    = array();
    if ($numrows > 0) {
        while (list($cid) = $xoopsDB->fetchRow($allcatsresult)) {
            $allcats[] = $cid;
            if (isset($_POST['selected' . $cid . ''])) {
                $postedcats[] = $cid;
            }
        }
    }
    //$inactivatecats is determined by the difference between posted cats and itemcats.
    //$nonpostedcats = array_diff($postedcats, $allitemcats);

    //Update these categories to inactive
    foreach ($postedcats as $cat) {
        if (!in_array($cat, $allitemcats)) {
            $newid = $xoopsDB->genId($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_item_x_cat') . '_xid_seq');
            $sql   = sprintf("INSERT INTO %s (xid, cid, itemid, active, created) VALUES (%u, %u, %u, '%s', '%s')", $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_item_x_cat'), $newid, $cat, $post_itemid, 1, time());
            $xoopsDB->query($sql) or $eh->show('0013');
        } elseif (!in_array($cat, $activeitemcats)) {
            $sql = 'UPDATE ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_item_x_cat') . " SET active = '1' WHERE itemid = '" . $post_itemid . '\' AND cid=\'' . $cat . '\'';
            $xoopsDB->query($sql) or $eh->show('0013');
        }
    }
    foreach ($allitemcats as $cat) {
        if (!in_array($cat, $postedcats)) {
            $sql = 'UPDATE ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_item_x_cat') . " SET active = '0' WHERE itemid = '" . $post_itemid . '\' AND cid=\'' . $cat . '\'';
            $xoopsDB->query($sql) or $eh->show('0013');
        }
    }
    redirect_header(XOOPS_URL . "/modules/$moddir/admin/editcategories.php?item=" . $post_itemid . '', 2, _MD_CATEGORIES_UPDATED);
    exit();
} else {
    xoops_cp_header();
    $dirid = getDirIdFromItem($get_itemid);
    //Query datatypes that match the cargories selected. If not category selected.
    $form = new XoopsThemeForm(_MD_SELECTCAT_FORM, 'submitform', 'editcategories.php');
    $form->setExtra('enctype="multipart/form-data"');
    $category_tray = new XoopsFormElementTray(_MD_CATEGORIES, '', 'cid');
    $catselarea    = getCatSelectArea($dirid);
    $category_tray->addElement(new XoopsFormLabel('', $catselarea));
    $form->addElement($category_tray, true);
    $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
    $form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->addElement(new XoopsFormHidden('item', $get_itemid));
    $form->addElement(new XoopsFormHidden('dirid', $dirid));
    $form->display();
    xoops_cp_footer();
}
