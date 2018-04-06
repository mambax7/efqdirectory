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

require_once __DIR__ . '/header.php';
$myts = \MyTextSanitizer::getInstance();// MyTextSanitizer object
// require_once __DIR__ . '/class/xoopstree.php';
//require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$moddir = $xoopsModule->getVar('dirname');
$helper = Efqdirectory\Helper::getInstance();
if (\Xmf\Request::hasVar('dirid', 'POST')) {
 $dirid = \Xmf\Request::getInt('dirid', 0, 'POST');
} elseif (\Xmf\Request::hasVar('dirid', 'GET')) {
 $dirid = \Xmf\Request::getInt('dirid', 0, 'GET');
}

//$eh = new ErrorHandler; //ErrorHandler object

if (isset($_GET['op'])) {
    $op = $_GET['op'];
} elseif (isset($_POST['op'])) {
    $op = $_POST['op'];
}

if (empty($xoopsUser) and !$helper->getConfig('anonpost')) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);
}

if (!empty($_POST['submit'])) {
    //Get all selectable categories and put the prefix 'selectcat' in front of the catid.
    //With all results check if the result has a corresponding $_POST value.
    if ('' === $_POST['title']) {
//        $eh::show('1001');
        $logger = \XoopsLogger::getInstance();
        $logger->addExtra('Validation', 'Please enter value for Title.');
    }
    $title = $myts->addSlashes($_POST['title']);
    $date  = time();
    $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_items') . '_itemid_seq');
    if (1 == $helper->getConfig('autoapprove')) {
        $status = 1;
    } else {
        $status = 0;
    }
    //itemtype = bronze, silver, gold etc., start with 0 as default.
    $submitter = $xoopsUser->getVar('uid');
    $newid     = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_items') . '_itemid_seq');
    $sql       = sprintf("INSERT INTO %s (itemid, uid, STATUS, created, title, hits, rating, votes, typeid, dirid) VALUES (%u, %u, %u, '%s', '%s', %u, %u, %u, '%s', %u)", $xoopsDB->prefix($helper->getDirname() . '_items'), $newid, $submitter, $status, time(), $title, 0, 0, 0, 0, $dirid);
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    if (0 == $newid) {
        $itemid = $xoopsDB->getInsertId();
    }
    $sql           = 'SELECT cid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE dirid='" . $dirid . '\' AND active=\'1\'';
    $allcatsresult = $xoopsDB->query($sql);
    $numrows       = $xoopsDB->getRowsNum($allcatsresult);
    $count         = 0;
    if ($numrows > 0) {
        while (false !== (list($cid) = $xoopsDB->fetchRow($allcatsresult))) {
            if (isset($_POST['selected' . $cid . ''])) {
                $sql = sprintf("INSERT INTO %s (xid, cid, itemid, active, created) VALUES (%u, %u, %u, '%s', '%s')", $xoopsDB->prefix($helper->getDirname() . '_item_x_cat'), $newid, $cid, $itemid, 1, time());
                $result = $xoopsDB->query($sql);
                if (!$result) {
                    $logger = \XoopsLogger::getInstance();
                    $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
                }
                ++$count;
            }
        }
        if (0 == $count) {
            redirect_header(XOOPS_URL . "/modules/$moddir/submit.php?dirid=" . $post_dirid . '', 2, _MD_NOCATEGORYMATCH);
        }
    } else {
        redirect_header(XOOPS_URL . "/modules/$moddir/submit.php?dirid=" . $post_dirid . '', 2, _MD_NOCATEGORIESAVAILABLE);
    }

    /* // RMV-NEW
    // Notify of new listing (anywhere). To be completed.
    $notificationHandler = xoops_getHandler('notification');
    $tags = array();
    $tags['ITEM_NAME'] = $title;
    $tags['ITEM_URL'] = XOOPS_URL . '/modules/'. $xoopsModule->getVar('dirname') . '/listing.php?item=' . $itemid;
    if ($helper->getConfig('autoapprove') == 1) {
    $notificationHandler->triggerEvent('global', $itemid, 'new_listing', $tags);
    redirect_header(XOOPS_URL."/modules/$moddir/edit.php?item=".$itemid."",2,_MD_APPROVED);
    } else {
    $tags['WAITINGLINKS_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/index.php?op=listNewListings';
    $notificationHandler->triggerEvent('global', $itemid, 'new_listing', $tags);
    if ($notify) {
    require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
    $notificationHandler->subscribe('link', $newid, 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
    }
    redirect_header(XOOPS_URL."/modules/$moddir/edit.php?item=".$itemid."",2,_MD_SAVED);
    }*/

    redirect_header(XOOPS_URL . "/modules/$moddir/edit.php?item=" . $itemid . '', 2, _MD_SAVED);
} else {
    if (\Xmf\Request::hasVar('dirid', 'GET')) {
        $get_dirid = \Xmf\Request::getInt('dirid', 0, 'GET');
    } else {
        redirect_header(XOOPS_URL . "/modules/$moddir/index.php", 2, _MD_NODIRECTORYSELECTED);
    }
    $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_submit.tpl';
    include XOOPS_ROOT_PATH . '/header.php';
    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    //Query datatypes that match the categories selected. If not category selected.
    ob_start();
    $form = new \XoopsThemeForm(_MD_SUBMITLISTING_FORM, 'submitform', 'submit.php');
    $form->setExtra('enctype="multipart/form-data"');
    $editor = !empty($_REQUEST['editor']) ? $_REQUEST['editor'] : '';
    if (!empty($editor)) {
        setcookie('editor', $editor); // save to cookie
    } else {
        // Or use user pre-selected editor through profile
        if (is_object($xoopsUser)) {
            $editor = @ $xoopsUser->getVar('editor'); // Need set through user profile
        }
    }
    $editor           = 'koivi';
    $options['name']  = 'link_description';
    $options['value'] = \Xmf\Request::getString('message', '', 'REQUEST');
//    $options['value'] = '';
    //optional configs
    $options['rows']   = 25; // default value = 5
    $options['cols']   = 60; // default value = 50
    $options['width']  = '100%'; // default value = 100%
    $options['height'] = '400px'; // default value = 400px
    $options['small']  = true;
    $options['smiles'] = false;

    // "textarea": if the selected editor with name of $editor can not be created, the editor "textarea" will be used
    // if no $onFailure is set, then the first available editor will be used
    // If dohtml is disabled, set $noHtml to true
    $form->addElement(new \XoopsFormText(_MD_TITLE, 'title', 50, 250, ''), true);
    $category_tray = new \XoopsFormElementTray(_MD_CATEGORIES, '', 'cid');
    $catselarea    = getCatSelectArea2();
    $category_tray->addElement(new \XoopsFormLabel('', $catselarea));
    $form->addElement($category_tray, true);
    $form->addElement(new \XoopsFormButton('', 'submit', _MD_CONTINUE, 'submit'));
    $form->addElement(new \XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->addElement(new \XoopsFormHidden('op', 'selectcat'));
    $form->addElement(new \XoopsFormHidden('dirid', $get_dirid));
    $form->display();
    $xoopsTpl->assign('submit_form', ob_get_contents());
    ob_end_clean();

    $xoopsTpl->assign('notify_show', !empty($xoopsUser) && !$helper->getConfig('autoapprove') ? 1 : 0);
    $xoopsTpl->assign('lang_sitetitle', _MD_SITETITLE);
    $xoopsTpl->assign('lang_siteurl', _MD_SITEURL);
    $xoopsTpl->assign('lang_category', _MD_CATEGORYC);
    $xoopsTpl->assign('lang_options', _MD_OPTIONS);
    $xoopsTpl->assign('lang_notify', _MD_NOTIFYAPPROVE);
    $xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
    $xoopsTpl->assign('lang_cancel', _CANCEL);
    include XOOPS_ROOT_PATH . '/footer.php';
}
