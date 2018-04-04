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

require_once __DIR__ . '/admin_header.php';
//include __DIR__ . '/../../../include/cp_header.php';

require_once __DIR__ . '/../include/functions.php';
// require_once __DIR__ . '/../class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
// require_once __DIR__ . '/../class/class.formimage.php';
// require_once __DIR__ . '/../class/Tree.php';
$efqtree = new Efqdirectory\Tree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');
$myts    = \MyTextSanitizer::getInstance();

$mytree  = new Efqdirectory\MyXoopsTree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');
$mytree2 = new Efqdirectory\MyXoopsTree($xoopsDB->prefix($helper->getDirname() . '_fieldtypes'), 'typeid', 0);
$efqtree = new Efqdirectory\Tree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');
$helper  = Efqdirectory\Helper::getInstance();

$moddir = $xoopsModule->getVar('dirname');
if (isset($_GET['dirid'])) {
    $get_dirid = (int)$_GET['dirid'];
} else {
    $get_dirid = 0;
}
if (isset($_GET['dtypeid'])) {
    $get_dtypeid = (int)$_GET['dtypeid'];
}
if (isset($_GET['catid'])) {
    $get_catid = (int)$_GET['catid'];
}

/**
 * @param string $dirid
 */
function catConfig($dirid = '0')
{
    global $xoopsDB, $xoopsModule, $xoopsUser, $mytree, $efqtree;
    if ('0' == $dirid) {
        redirect_header('directories.php', 2, _MD_NOVALIDDIR);
    }
    xoops_cp_header();
    //adminmenu(-1, _MD_MANAGE_CATS);
    $dirname = getDirNameFromId($dirid);
    echo '<h4>' . _MD_CATCONF . '&nbsp;-&nbsp;' . $dirname . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
    //Get all main categories for this directory.
    $cats = getCatOverview();
    echo $cats;
    echo '</table>';
    echo '<br>';
    echo '<h4>' . _MD_CREATE_NEWCAT . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1'><tr><td>";
    $form = new \XoopsThemeForm(_MD_NEWCATFORM, 'submitform', 'categories.php');
    $form->setExtra('enctype="multipart/form-data"');
    $form->addElement(new \XoopsFormText(_MD_CATTITLE, 'title', 50, 100, ''), true);
    $form_active = new \XoopsFormCheckBox(_MD_ACTIVEYN, 'active', '');
    $form_active->addOption(1, _MD_YESNO);
    $form->addElement($form_active);
    $form_importdtypes = new \XoopsFormCheckBox(_MD_IMPORT_DTYPES_YN, 'importdtypes', '');
    $form_importdtypes->addOption(1, _MD_YESNO);
    $form->addElement($form_importdtypes);
    $form_allowlist = new \XoopsFormCheckBox(_MD_ALLOWLISTYN, 'allowlist', '');
    $form_allowlist->addOption(1, _MD_YESNO);
    $form->addElement($form_allowlist);
    $form_showpopular = new \XoopsFormCheckBox(_MD_SHOWPOPULARYN, 'showpopular', '');
    $form_showpopular->addOption(1, _MD_YESNO);
    $form->addElement($form_showpopular);
    $form->addElement(new \XoopsFormDhtmlTextArea(_MD_DESCRIPTION, 'descr', '', 5, 50));
    //Add parent category select box.
    ob_start();
    $efqtree->setDir($dirid);
    $pcat_selbox = $efqtree->makeMySelBox('title', 'title', '', 1, 'pid');
    $selbox      = ob_get_contents();
    ob_end_clean();
    $pcat_tray = new \XoopsFormElementTray(_MD_PARENTCAT, '<br>');
    $pcat_tray->addElement(new \XoopsFormLabel('', $selbox));
    $form->addElement($pcat_tray);
    $form->addElement(new \XoopsFormFile(_MD_SELECT_PIC, 'img', 30000));
    $form_txtactive = new \XoopsFormCheckBox(_MD_CATTEXT_ACTIVE_YN, 'open', 0);
    $form_txtactive->addOption(1, _MD_YESNO);
    $form->addElement($form_txtactive);
    $form->addElement(new \XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
    $form->addElement(new \XoopsFormHidden('op', 'newcat'));
    $form->addElement(new \XoopsFormHidden('dirid', $dirid));
    $form->addElement(new \XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->display();
    echo '</td></tr></table>';
    xoops_cp_footer();
}

/**
 * @param string $catid
 */
function editCat($catid = '0')
{
    global $xoopsDB, $efqtree, $mytree, $mytree2, $xoopsUser, $get_catid, $myts;
    $pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    xoops_cp_header();
    $diridfromcatid = getDirId($get_catid);
    //adminmenu(-1, _MD_EDITCAT);
    echo '<h4>' . _MD_EDITCAT . '</h4>';
    $pathstring = "<a href='categories.php?dirid=$diridfromcatid'>" . _MD_MAIN . '</a>&nbsp;:&nbsp;';
    $pathstring .= $efqtree->getNicePathFromId($get_catid, 'title', 'categories.php?op=edit');
    echo $pathstring . '<br><br>';

    $catchildren   = $mytree->getFirstChild($get_catid);
    $count_subcats = count($catchildren);
    if (0 != $count_subcats) {
        $subcategories = '';
        echo '<strong>' . _MD_SUBCATEGORIES . '</strong>:<br>';
        foreach ($catchildren as $i => $iValue) {
            $subcategories .= "<a href='categories.php?op=edit&catid=" . $catchildren[$i]['cid'] . '\'>' . $catchildren[$i]['title'] . '</a>';
            if ($i + 1 != $count_subcats) {
                $subcategories .= ', ';
            }
        }
        echo $subcategories . '<br><br>';
    }

    echo "<table width='100%' border='0' cellspacing='1'><tr><td>";
    $sql    = 'SELECT c.cid, c.dirid, c.title, c.active, c.pid, c.img, c.allowlist, c.showpopular, c.height, c.width, t.text FROM '
              . $xoopsDB->prefix($helper->getDirname() . '_cat')
              . ' c LEFT JOIN '
              . $xoopsDB->prefix($helper->getDirname() . '_cat_txt')
              . " t  ON (c.cid=t.cid) WHERE c.cid='"
              . $catid
              . '\'';
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    } else {
        $numrows = $xoopsDB->getRowsNum($result);
        if ($numrows > 0) {
            while (false !== (list($cid, $dirid, $title, $active, $pid, $img, $allowlist, $showpopular, $imgheight, $imgwidth, $descr) = $xoopsDB->fetchRow($result))) {
                $title     = $myts->htmlSpecialChars($title);
                $parentcat = $pid;
                if ('' != $img) {
                    $picture = "../uploads/$img";
                } else {
                    $picture = '../uploads/dummy.png';
                }
                $form = new \XoopsThemeForm(_MD_EDITCATFORM, 'editform', 'categories.php');
                $form->setExtra('enctype="multipart/form-data"');
                $form->addElement(new \XoopsFormText(_MD_CATTITLE, 'title', 50, 100, $title), true);
                $form_active = new \XoopsFormCheckBox(_MD_ACTIVEYN, 'active', $active);
                $form_active->addOption(1, _MD_YESNO);
                $form->addElement($form_active);
                $form_allowlist = new \XoopsFormCheckBox(_MD_ALLOWLISTYN, 'allowlist', $allowlist);
                $form_allowlist->addOption(1, _MD_YESNO);
                $form->addElement($form_allowlist);
                $form_showpopular = new \XoopsFormCheckBox(_MD_SHOWPOPULARYN, 'showpopular', $showpopular);
                $form_showpopular->addOption(1, _MD_YESNO);
                $form->addElement($form_showpopular);
                //Add parent category select box.
                ob_start();
                $pcat_selbox = $mytree->makeMySelBox('title', 'title', $pid, 1, 'pid');
                $selbox      = ob_get_contents();
                ob_end_clean();
                $pcat_tray = new \XoopsFormElementTray(_MD_PARENTCAT, '<br>');
                $pcat_tray->addElement(new \XoopsFormLabel('', $selbox));
                $form->addElement($pcat_tray);

                $form->addElement(new \XoopsFormDhtmlTextArea(_MD_DESCRIPTION, 'descr', (string)$descr, 5, 50));
                $form->addElement(new \XoopsFormFile(_MD_SELECT_PIC, 'img', 30000));
                $form->addElement(new Efqdirectory\XoopsFormImage(_MD_CURRENT_PIC, 'current_image', null, $picture, $imgheight, $imgwidth));
                $form->addElement(new \XoopsFormButton('', 'submit', _MD_UPDATE, 'submit'));
                $form->addElement(new \XoopsFormHidden('op', 'update'));
                $form->addElement(new \XoopsFormHidden('catid', $cid));
                $form->addElement(new \XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
                $form->display();
            }
        }
    }
    echo '</td></tr></table>';
    $dellink = "<align=\"right\"><a href=\"categories.php?op=deleteCatConfirm&catid=$get_catid\"><img src=\"" . $pathIcon16 . '/delete.png">' . _MD_DELETE_CAT . '</a></align>';



    echo $dellink;
    echo '<h4>' . _MD_EDITDTYPES . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1'><tr><td>";
    echo '<form name="editdtypes" id="editdtypes" action="categories.php" method="post">';
    echo '<table width="100%" class="outer" cellspacing="1">';
    $sql    = 'SELECT d.dtypeid, x.cid, d.title, d.section, d.seq, d.icon, f.title, f.typeid, d.defaultyn, d.activeyn, d.options, d.custom FROM '
              . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
              . ' d, '
              . $xoopsDB->prefix($helper->getDirname() . '_fieldtypes')
              . ' f, '
              . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
              . " x WHERE d.fieldtypeid=f.typeid AND d.dtypeid=x.dtypeid AND x.cid='"
              . $catid
              . '\' ORDER BY d.section ASC, d.seq ASC';
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $numrows = $xoopsDB->getRowsNum($result);
    echo '<tr><th>' . _MD_DTYPE_ICON . '</th><th>' . _MD_DTYPE_TITLE . '</th><th>' . _MD_FTYPE_NAME . '</th><th>' . _MD_OPTIONS . '</th><th>' . _MD_CUSTOM . '</th><th>' . _MD_ACTIVE . '</th><th>' . _MD_DEFAULT . '</th><th>' . _MD_DTYPE_SEQ . '</th><th>' . _MD_SECTION . '</th></tr>';
    if ($numrows > 0) {
        $dtypes = '';
        $count  = 1;
        while (false !== (list($dtypeid, $cid, $dtypetitle, $section, $dseq, $picture, $ftypename, $ftypeid, $defaultyn, $activeyn, $options, $dcustom) = $xoopsDB->fetchRow($result))) {
            if ('1' == $defaultyn) {
                $default = _MD_YES;
                $checked = ' checked';
            } else {
                $default = _MD_NO;
                $checked = '';
            }
            if ('1' == $activeyn) {
                $default       = _MD_YES;
                $activechecked = ' checked';
            } else {
                $default       = _MD_NO;
                $activechecked = '';
            }
            if ('' != $picture) {
                $iconurl = "../uploads/$picture";
            } else {
                $iconurl = '';
            }
            if ('1' == $dcustom) {
                $custom = _MD_YES;
            } else {
                $custom = _MD_NO;
            }
            //Get the existing fields for this category.
            ob_start();
            $mytree2->makeMySelBox('title', 'title', $ftypeid, 1, 'fieldtype' . $dtypeid . '');
            $ftypebox = ob_get_contents();
            ob_end_clean();

            if (1 != $count) {
                $dtypes .= '|' . $dtypeid;
            } else {
                $dtypes = $dtypeid;
            }
            ++$count;
            echo "<tr><td class=\"even\"><img src=\"$iconurl\"></td><td class=\"even\"><a href=\"categories.php?op=editdtype&dtypeid=$dtypeid&amp;catid=$get_catid\">$dtypetitle</a></td><td class=\"even\">"
                 . $ftypename
                 . "</td><td class=\"even\">$options</td><td class=\"even\">$custom</td><td class=\"even\"><input type=\"checkbox\" name=\"activeyn"
                 . $dtypeid
                 . "\"$activechecked></input></td><td class=\"even\"><input type=\"checkbox\" name=\"defaultyn"
                 . $dtypeid
                 . "\"$checked></input></td><td class=\"even\"><input type=\"text\" size=\"5\" maxsize=\"10\" name=\"seq"
                 . $dtypeid
                 . '" value="'
                 . $dseq
                 . '"></input></td><td class="even"><input type="text" size="5" maxsize="10" name="section'
                 . $dtypeid
                 . '" value="'
                 . $section
                 . '"></input></td></tr>';
        }
        echo '<tr><td class="even">&nbsp;</td><td class="even" colspan="8"><input type="hidden" name="op" value="editdtypes"><input type="hidden" name="catid" value="'
             . $get_catid
             . '"><input type="hidden" name="dtypes" value="'
             . $dtypes
             . '"><input type="submit" class="formButton" name="submit"  id="submit" value="'
             . _MD_UPDATE
             . '" ></td></tr>';
    } else {
        echo '<tr><td colspan="7">' . _MD_NORECORDS . '&nbsp;';
        if ('0' != $parentcat) {
            echo '<input type="hidden" name="op" value="importdtypes"><input type="hidden" name="catid" value="'
                 . $get_catid
                 . '"><input type="hidden" name="pid" value="'
                 . $parentcat
                 . '"><input type="submit" class="formButton" name="submit"  id="submit" value="'
                 . _MD_IMPORT_DTYPES_FROM_PARENTCAT
                 . '" >';
        }
        echo '</td></tr>';
    }
    echo '</table></form></td></tr></table>';
    echo '<h4>' . _MD_ADDDTYPE . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1'><tr><td>";

    $form = new \XoopsThemeForm(_MD_ADD_DTYPE_FORM, 'newdatatypeform', 'categories.php');
    $form->setExtra('enctype="multipart/form-data"');
    $form->addElement(new \XoopsFormText(_MD_DTYPE_TITLE, 'title', 50, 100, ''), true);
    $form->addElement(new \XoopsFormFile(_MD_SELECT_ICON, 'image', 30000));
    $fieldtype_tray = new \XoopsFormElementTray(_MD_FTYPE_NAME, '', 'typeid');
    ob_start();
    $mytree2->makeMySelBox('title', 'title', 0, 1);
    $selbox = ob_get_contents();
    ob_end_clean();
    $options_tray     = new \XoopsFormElementTray(_MD_OPTIONS, '');
    $options_text     = new \XoopsFormLabel('', '<br>' . _MD_OPTIONS_EXPL);
    $options_textarea = new \XoopsFormTextArea('', 'options', '', 6, 50);
    $options_tray->addElement($options_textarea);
    $options_tray->addElement($options_text);
    $fieldtype_tray->addElement(new \XoopsFormLabel('', $selbox . ' <a href="fieldtypes.php">' . _MD_EDIT . ''));
    $form->addElement($fieldtype_tray, true);
    $form->addElement($options_tray);
    $form_active = new \XoopsFormCheckBox(_MD_DTYPE_ACTIVEYN, 'activeyn', 0);
    $form_active->addOption(1, _MD_YESNO);
    $form->addElement($form_active);
    $form_default = new \XoopsFormCheckBox(_MD_DTYPE_DEFAULTYN, 'defaultyn', 0);
    $form_default->addOption(1, _MD_YESNO);
    $form->addElement($form_default);
    $form->addElement(new \XoopsFormText(_MD_DTYPE_SEQ, 'seq', 5, 10, 0), true);
    $form->addElement(new \XoopsFormText(_MD_SECTION, 'section', 5, 10, 0), true);
    //$form_custom = new \XoopsFormCheckBox(_MD_DTYPE_CUSTOM, "custom", "");
    //$form_custom->addOption(1, _MD_YESNO);
    //$form->addElement($form_custom);
    $form->addElement(new \XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
    $form->addElement(new \XoopsFormHidden('op', 'newdatatype'));
    $form->addElement(new \XoopsFormHidden('catid', $get_catid));
    $form->addElement(new \XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->display();
    echo '</td></tr></table>';
    echo '<br>';
    echo '<h4>' . _MD_CREATE_NEWSUBCAT . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1'><tr><td>";
    $form = new \XoopsThemeForm(_MD_NEWCATFORM, 'submitform', 'categories.php');
    $form->setExtra('enctype="multipart/form-data"');
    $form->addElement(new \XoopsFormText(_MD_CATTITLE, 'title', 50, 100, (string)$title), true);
    $form_active = new \XoopsFormCheckBox(_MD_ACTIVEYN, 'active', '');
    $form_active->addOption(1, _MD_YESNO);
    $form->addElement($form_active);
    $form_importdtypes = new \XoopsFormCheckBox(_MD_IMPORT_DTYPES_YN, 'importdtypes', 1);
    $form_importdtypes->addOption(1, _MD_YESNO);
    $form->addElement($form_importdtypes);
    $form_allowlist = new \XoopsFormCheckBox(_MD_ALLOWLISTYN, 'allowlist', '');
    $form_allowlist->addOption(1, _MD_YESNO);
    $form->addElement($form_allowlist);
    $form_showpopular = new \XoopsFormCheckBox(_MD_SHOWPOPULARYN, 'showpopular', '');
    $form_showpopular->addOption(1, _MD_YESNO);
    $form->addElement($form_showpopular);
    $form->addElement(new \XoopsFormDhtmlTextArea(_MD_DESCRIPTION, 'descr', '', 5, 50));
    ob_start();
    $pcat_selbox = $mytree->makeMySelBox('title', 'title', (string)$get_catid, 0, 'pid');
    $selbox      = ob_get_contents();
    ob_end_clean();
    $pcat_tray = new \XoopsFormElementTray(_MD_PARENTCAT, '<br>');
    $pcat_tray->addElement(new \XoopsFormLabel('', $selbox));
    $form->addElement($pcat_tray);
    $form->addElement(new \XoopsFormFile(_MD_SELECT_PIC, 'img', 30000));
    $form_txtactive = new \XoopsFormCheckBox(_MD_CATTEXT_ACTIVE_YN, 'open', 0);
    $form_txtactive->addOption(1, _MD_YESNO);
    $form->addElement($form_txtactive);
    $form->addElement(new \XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
    $form->addElement(new \XoopsFormHidden('op', 'newcat'));
    $form->addElement(new \XoopsFormHidden('dirid', $diridfromcatid));
    $form->addElement(new \XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->display();
    echo '</td></tr></table>';
    xoops_cp_footer();
}

/**
 * @param string $dtypeid
 */
function editDatatype($dtypeid = '0')
{
    global $xoopsDB, $mytree, $mytree2, $xoopsUser, $get_catid;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    xoops_cp_header();
    //adminmenu(-1, _MD_EDITCAT);
    echo '<h4>' . _MD_EDITDTYPE . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
    $sql    = 'SELECT d.dtypeid, d.title, d.section, d.seq, d.icon, f.title, f.typeid, d.defaultyn, d.activeyn, d.options, d.custom FROM '
              . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
              . ' d, '
              . $xoopsDB->prefix($helper->getDirname() . '_fieldtypes')
              . " f WHERE d.fieldtypeid=f.typeid AND d.dtypeid='"
              . $dtypeid
              . '\' ORDER BY d.seq ASC';
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        while (false !== (list($dtypeid, $dtypetitle, $section, $dseq, $picture, $ftypename, $ftypeid, $defaultyn, $activeyn, $options, $dcustom) = $xoopsDB->fetchRow($result))) {
            if ('1' == $defaultyn) {
                $default = _MD_YES;
                $checked = ' checked';
            } else {
                $default = _MD_NO;
                $checked = '';
            }
            if ('1' == $activeyn) {
                $default       = _MD_YES;
                $activechecked = ' checked';
            } else {
                $default       = _MD_NO;
                $activechecked = '';
            }
            if ('' != $picture) {
                $iconurl = "../uploads/$picture";
            } else {
                $iconurl = '';
            }
            if ('1' == $dcustom) {
                $custom = '1';
            } else {
                $custom = '0';
            }
            ob_start();
            $mytree2->makeMySelBox('title', 'title', $ftypeid, 1, 'fieldtype' . $dtypeid . '');
            $ftypebox = ob_get_contents();
            ob_end_clean();

            $form = new \XoopsThemeForm(_MD_EDIT_DTYPE_FORM, 'editdatatypeform', 'categories.php');
            $form->setExtra('enctype="multipart/form-data"');
            //$form_custom = new \XoopsFormCheckBox(_MD_DTYPE_CUSTOM, "custom", $custom);
            //$form_custom->addOption(1, _MD_YESNO);
            //$form->addElement($form_custom);
            $form->addElement(new \XoopsFormText(_MD_DTYPE_TITLE, 'title', 50, 100, $dtypetitle), true);
            $form->addElement(new \XoopsFormFile(_MD_SELECT_ICON, 'image', 30000));
            $form->addElement(new Efqdirectory\XoopsFormImage(_MD_CURRENT_ICON, 'current_image', null, $iconurl, '', ''));
            $fieldtype_tray = new \XoopsFormElementTray(_MD_FTYPE_NAME, '', 'typeid');
            ob_start();
            $mytree2->makeMySelBox('title', 'title', $ftypeid, 1);
            $selbox = ob_get_contents();
            ob_end_clean();
            $options_tray     = new \XoopsFormElementTray(_MD_OPTIONS, '');
            $options_text     = new \XoopsFormLabel('', '<br>' . _MD_OPTIONS_EXPL);
            $options_textarea = new \XoopsFormTextArea('', 'options', $options, 6, 5);
            $options_tray->addElement($options_textarea);
            $options_tray->addElement($options_text);
            $fieldtype_tray->addElement(new \XoopsFormLabel('', $selbox . ' <a href="fieldtypes.php">' . _MD_EDIT . ''));
            $form->addElement($fieldtype_tray, true);
            $form->addElement($options_tray);
            $form_active = new \XoopsFormCheckBox(_MD_DTYPE_ACTIVEYN, 'activeyn', $activeyn);
            $form_active->addOption(1, _MD_YESNO);
            $form->addElement($form_active);
            $form_default = new \XoopsFormCheckBox(_MD_DTYPE_DEFAULTYN, 'defaultyn', $defaultyn);
            $form_default->addOption(1, _MD_YESNO);
            $form->addElement($form_default);
            $form->addElement(new \XoopsFormText(_MD_DTYPE_SEQ, 'seq', 5, 10, $dseq), true);
            $form->addElement(new \XoopsFormText(_MD_SECTION, 'section', 5, 10, $section), true);
            $form->addElement(new \XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
            $form->addElement(new \XoopsFormHidden('op', 'savedtype'));
            $form->addElement(new \XoopsFormHidden('catid', $get_catid));
            $form->addElement(new \XoopsFormHidden('dtypeid', $dtypeid));
            $form->addElement(new \XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
            $form->display();
        }
    } else {
        echo '<table><tr><td colspan="7">' . _MD_NORECORDS . '&nbsp;';
        //  if ($parentcat != '0') {
        //   echo "<input type=\"hidden\" name=\"op\" value=\"importdtypes\"><input type=\"hidden\" name=\"catid\" value=\"".$get_catid."\"><input type=\"hidden\" name=\"pid\" value=\"".$parentcat."\"><input type=\"submit\" class=\"formButton\" name=\"submit\"  id=\"submit\" value=\""._MD_IMPORT_DTYPES_FROM_PARENTCAT."\" >";
        //  }
        echo '</td></tr></table>';
    }
    echo '</td></tr></table>';
    xoops_cp_footer();
}

function saveDatatype()
{
    global $xoopsDB, $_POST, $myts, $moddir, $xoopsUser;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper    = Efqdirectory\Helper::getInstance();
    $count     = 1;
    $return    = '';
    $p_dtypeid = $_POST['dtypeid'];
    $p_catid   = $_POST['catid'];
    if (isset($_POST['title'])) {
        if (isset($_POST['custom'])) {
            $p_custom = $_POST['custom'];
        } else {
            $p_custom = '0';
        }
        $p_title     = $myts->addSlashes($_POST['title']);
        $p_fieldtype = $_POST['typeid'];
        $p_section   = $myts->addSlashes($_POST['section']);
        if (isset($_POST['defaultyn'])) {
            $p_default = $_POST['defaultyn'];
        } else {
            $p_default = '0';
        }
        if (isset($_POST['activeyn'])) {
            $p_active = $_POST['activeyn'];
        } else {
            $p_active = '0';
        }
        if (isset($_POST['options'])) {
            $p_options = $myts->addSlashes($_POST['options']);
        } else {
            $p_options = '';
        }
        $p_fieldtype = $_POST['typeid'];
        $p_seq       = $myts->addSlashes($_POST['seq']);

        //require_once XOOPS_ROOT_PATH . '/class/class.uploader.php';
        $uploader = new \XoopsMediaUploader(XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads", ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'], 30000, 50, 50);
        $uploader->setPrefix('icon');
        $err    = [];
        $ucount = count($_POST['xoops_upload_file']);
        if (0 != $ucount) {
            for ($i = 0; $i < $ucount; ++$i) {
                if ('' != $_POST['xoops_upload_file'][$i]) {
                    $medianame = $_POST['xoops_upload_file'][$i];
                    if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                        if (!$uploader->upload()) {
                            $err[] = $uploader->getErrors();
                        } else {
                            $savedfilename = $uploader->getSavedFileName();
                            $ticket        = uniqid(mt_rand(), 1);
                            //Rename the uploaded file to the same name in a different location that does not have 777 rights or 755.
                            rename('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '', '' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/uploads/' . $savedfilename . '');
                            //Delete the uploaded file from the initial upload folder if it is still present in that folder.
                            if (file_exists('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '')) {
                                unlink('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '');
                            }
                            $sql    = 'UPDATE '
                                      . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
                                      . " SET title = '$p_title', section = '$p_section', fieldtypeid = '$p_fieldtype', defaultyn = '$p_default', activeyn='$p_active', seq='$p_seq', options='$p_options', custom='$p_custom', icon='$savedfilename' WHERE dtypeid = $p_dtypeid";
                            $result = $xoopsDB->query($sql);
                            if (!$result) {
                                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
                            }
                        }
                    } else {
                        $sql    = 'UPDATE '
                                  . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
                                  . " SET title = '$p_title', section = '$p_section', fieldtypeid = '$p_fieldtype', defaultyn = '$p_default', activeyn='$p_active', seq='$p_seq', options='$p_options', custom='$p_custom' WHERE dtypeid = $p_dtypeid";
                        $result = $xoopsDB->query($sql);
                        if (!$result) {
                            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
                        }
                    }
                } else {
                    $sql    = 'UPDATE '
                              . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
                              . " SET title = '$p_title', section = '$p_section', fieldtypeid = '$p_fieldtype', defaultyn = '$p_default', activeyn='$p_active', seq='$p_seq', options='$p_options', custom='$p_custom' WHERE dtypeid = $p_dtypeid";
                    $result = $xoopsDB->query($sql);
                    if (!$result) {
                        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
                    }
                }
            }
        } else {
            $sql    = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_dtypes') . " SET title = '$p_title', section = '$p_section', fieldtypeid = '$p_fieldtype', defaultyn = '$p_default', activeyn='$p_active', seq='$p_seq', options='$p_options', custom='$p_custom' WHERE dtypeid = $p_dtypeid";
            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }
        }
    }
    redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_DTYPE_UPDATED);
}

function addDatatype()
{
    global $xoopsDB, $_POST, $myts, $xoopsUser, $moddir;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    if (isset($_POST['catid'])) {
        $p_catid = $_POST['catid'];
    } else {
        exit();
    }
    $p_title = $myts->addSlashes($_POST['title']);
    if (isset($_POST['defaultyn'])) {
        $p_default = $_POST['defaultyn'];
    } else {
        $p_default = '0';
    }
    if (isset($_POST['activeyn'])) {
        $p_active = $_POST['activeyn'];
    } else {
        $p_active = '0';
    }
    if (isset($_POST['custom'])) {
        $p_custom = $_POST['custom'];
    } else {
        $p_custom = '0';
    }
    if (isset($_POST['options'])) {
        $p_options = $myts->addSlashes($_POST['options']);
    } else {
        $p_options = '';
    }
    $p_fieldtype = (int)$_POST['typeid'];
    if ('' == $p_fieldtype) {
        redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_NOFIELDTYPE_SELECTED);
    }
    $p_section = $myts->addSlashes($_POST['section']);
    $uid       = $xoopsUser->getVar('uid');

    require_once XOOPS_ROOT_PATH . '/class/uploader.php';
    //$uploader = new \XoopsMediaUploader(XOOPS_ROOT_PATH.'/modules/'.$moddir.'/init_uploads', array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'), 30000, 50, 50);
    $uploader = new \XoopsMediaUploader(XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads", ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'], 30000, 50, 50);
    $uploader->setPrefix('icon');
    $err           = [];
    $ucount        = count($_POST['xoops_upload_file']);
    $savedfilename = '';
    for ($i = 0; $i < $ucount; ++$i) {
        if ('' != $_POST['xoops_upload_file'][$i]) {
            $medianame = $_POST['xoops_upload_file'][$i];
            if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                if (!$uploader->upload()) {
                    $err[] = $uploader->getErrors();
                } else {
                    $savedfilename = $uploader->getSavedFileName();
                    $ticket        = uniqid(mt_rand(), 1);
                    //Rename the uploaded file to the same name in a different location that does not have 777 rights or 755.
                    rename('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '', '' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/uploads/' . $savedfilename . '');
                    //Delete the uploaded file from the initial upload folder if it is still present in that folder.
                    if (file_exists('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '')) {
                        unlink('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '');
                    }
                }
            }
        }
    }
    $newid  = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_dtypes') . '_dtypeid_seq');
    $sql    = sprintf("INSERT INTO `%s`(dtypeid, title, section, fieldtypeid, uid, defaultyn, created, seq, activeyn, OPTIONS, custom, icon) VALUES (%u, '%s', %u, %u, %u, %u, '%s', '%s', %u, '%s', %u, '%s')", $xoopsDB->prefix($helper->getDirname() . '_dtypes'), $newid, $p_title, $p_section,
                      $p_fieldtype, $uid, $p_default, time(), 0, $p_active, $p_options, $p_custom, $savedfilename);
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $dtypeid = $xoopsDB->getInsertId();
    $newid   = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat') . '_xid_seq');
    $sql     = sprintf('INSERT INTO `%s`(xid, cid, dtypeid) VALUES (%u, %u, %u)', $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat'), $newid, $p_catid, $dtypeid);
    $result  = $xoopsDB->query($sql);
    if (!$result) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_CAT_UPDATED);
}

function editDatatypes()
{
    global $xoopsDB, $_POST, $myts, $moddir, $xoopsUser;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper     = Efqdirectory\Helper::getInstance();
    $count      = 1;
    $return     = '';
    $dtypes     = $_POST['dtypes'];
    $p_catid    = $_POST['catid'];
    $dtypes_arr = explode('[|]', $dtypes);
    foreach ($dtypes_arr as $dtype) {
        $p_section = $myts->addSlashes($_POST['section' . $dtype . '']);
        if (isset($_POST['defaultyn' . $dtype . ''])) {
            $p_defaultyn = '1';
        } else {
            $p_defaultyn = '0';
        }
        $p_seq = $myts->addSlashes($_POST['seq' . $dtype . '']);
        if (isset($_POST['activeyn' . $dtype . ''])) {
            $p_activeyn = '1';
        } else {
            $p_activeyn = '0';
        }
        $sql    = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_dtypes') . " SET section = '$p_section', defaultyn = '$p_defaultyn', activeyn='$p_activeyn', seq='$p_seq' WHERE dtypeid = $dtype";
        $result = $xoopsDB->query($sql);
        if (!$result) {
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }
    }
    redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_CAT_UPDATED);
}

function importDatatypes()
{
    global $xoopsDB, $_POST, $myts, $moddir, $xoopsUser;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    if (isset($_POST['pid'])) {
        $p_pid = $_POST['pid'];
    } else {
        exit();
    }
    if (isset($_POST['catid'])) {
        $p_catid = $_POST['catid'];
    } else {
        exit();
    }
    if (0 != $p_pid) {
        $sql = 'INSERT INTO '
               . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
               . ' (cid, dtypeid) SELECT '
               . $p_catid
               . ', d.dtypeid FROM '
               . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
               . ' d, '
               . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
               . ' x  WHERE d.dtypeid=x.dtypeid AND x.cid = '
               . $p_pid
               . " AND d.defaultyn = '1'";
    }
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_CAT_UPDATED);
}

/**
 * @return string
 */
function updateCat()
{
    global $xoopsDB, $_POST, $myts, $moddir;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    if (isset($_POST['catid'])) {
        $p_catid = $_POST['catid'];
    } else {
        exit();
    }
    $descr_exists = checkDescription($p_catid);

    if (isset($_POST['pid'])) {
        $p_pid = $_POST['pid'];
    } else {
        $p_pid = '0';
    }
    $p_title = $myts->addSlashes($_POST['title']);
    if (isset($_POST['active'])) {
        $p_active = $_POST['active'];
    } else {
        $p_active = 0;
    }
    if (isset($_POST['allowlist'])) {
        $p_allowlist = $_POST['allowlist'];
    } else {
        $p_allowlist = 0;
    }
    if (isset($_POST['showpopular'])) {
        $p_showpopular = $_POST['showpopular'];
    } else {
        $p_showpopular = 0;
    }
    if (isset($_POST['descr'])) {
        $p_descr = $myts->addSlashes($_POST['descr']);
    } else {
        $p_descr = false;
    }
    if ('' != $_POST['xoops_upload_file'][0]) {
        //require_once XOOPS_ROOT_PATH . "/modules/$moddir/class/class.uploader.php";
        $uploader = new Efqdirectory\MediaUploader(XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads", ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'], 30000, 250, 250);
        if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
            $filename = $uploader->getMediaName();
        } else {
            $sql    = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " SET title = '$p_title', active='$p_active', pid='$p_pid', allowlist='$p_allowlist', showpopular='$p_showpopular' WHERE cid = $p_catid";
            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }

            if ($p_descr) {
                if (true === $descr_exists) {
                    $sql2 = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_cat_txt') . " SET text='$p_descr' WHERE cid = $p_catid";
                } else {
                    $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat_txt') . '_txtid_seq');
                    $sql2  = sprintf("INSERT INTO `%s`(txtid, cid, TEXT, active, created) VALUES (%u, %u, '%s', %u, '%s')", $xoopsDB->prefix($helper->getDirname() . '_cat_txt'), $newid, $p_catid, $p_descr, '1', time());
                }
                $xoopsDB->query($sql2);
                if (!$result) {
                    $logger->handleError(E_USER_WARNING, $sql2, __FILE__, __LINE__);
                    return false;
                }
            }
            redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_CAT_UPDATED);
        }
        $uploader->setPrefix('efqdir');
        if ($uploader->upload()) {
            $savedfilename = $uploader->getSavedFileName();
            $width         = $uploader->getWidth();
            $height        = $uploader->getHeight();
            $imagelocation = $uploader->uploadDir;
            echo $uploader->getErrors();

            $sql    = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " SET img = '$savedfilename', width = '$width', height = '$height' WHERE cid = $p_catid";
            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }
            rename('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '', '' . XOOPS_ROOT_PATH . "/modules/$moddir/uploads/" . $savedfilename . '');
            //Delete the uploaded file from the initial upload folder if it is still present in that folder.
            if (file_exists('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '')) {
                unlink('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '');
            }
            redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_CAT_UPDATED);
        } else {
            echo $uploader->getErrors();
            $sql    = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " SET title = '$p_title', active='$p_active', pid='$p_pid', allowlist='$p_allowlist', showpopular='$p_showpopular' WHERE cid = $p_catid";
            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }
            if ($p_descr) {
                if (true === $descr_exists) {
                    $sql2 = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_cat_txt') . " SET text='$p_descr' WHERE cid = $p_catid";
                } else {
                    $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat_txt') . '_txtid_seq');
                    $sql2  = sprintf("INSERT INTO `%s`(txtid, cid, TEXT, active, created) VALUES (%u, %u, '%s', %u, '%s')", $xoopsDB->prefix($helper->getDirname() . '_cat_txt'), $newid, $p_catid, $p_descr, '1', time());
                }
                $result = $xoopsDB->query($sql2);
                if (!$result) {
                    $logger->handleError(E_USER_WARNING, $sql2, __FILE__, __LINE__);
                }
            }
            redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_CAT_UPDATED);
        }
    }
    redirect_header("categories.php?op=edit&catid=$p_catid", 2, _MD_CAT_NOT_UPDATED);
    return '';
}

function newCat()
{
    global $xoopsDB, $_POST, $myts, $moddir;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    if (isset($_POST['dirid'])) {
        $p_dirid = $_POST['dirid'];
    } else {
        exit();
    }
    if (isset($_POST['pid'])) {
        $p_pid = $_POST['pid'];
    } else {
        $p_pid = '0';
    }
    $p_title = $myts->addSlashes($_POST['title']);
    if (isset($_POST['active'])) {
        $p_active = $_POST['active'];
    } else {
        $p_active = 0;
    }
    if (isset($_POST['importdtypes'])) {
        if (0 != $p_pid) {
            $p_import = $_POST['importdtypes'];
        } else {
            $p_import = 0;
        }
    } else {
        $p_import = 0;
    }
    if (isset($_POST['txtactive'])) {
        $p_txtactive = $_POST['txtactive'];
    } else {
        $p_txtactive = 0;
    }
    if (isset($_POST['allowlist'])) {
        $p_allowlist = $_POST['allowlist'];
    } else {
        $p_allowlist = 0;
    }
    if (isset($_POST['showpopular'])) {
        $p_showpopular = $_POST['showpopular'];
    } else {
        $p_showpopular = 0;
    }
    if (isset($_POST['descr'])) {
        $p_descr = $myts->addSlashes($_POST['descr']);
    } else {
        $p_descr = '';
    }

    if ('' != $_POST['xoops_upload_file'][0]) {
        //require_once XOOPS_ROOT_PATH . "/modules/$moddir/class/class.uploader.php";
        $uploader = new Efqdirectory\MediaUploader(XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads", ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'], 30000, 250, 250);
        if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
            $filename = $uploader->getMediaName();
        } else {
            $newid  = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat') . '_cid_seq');
            $sql    = sprintf("INSERT INTO `%s` (cid, dirid, title, active, pid, allowlist, showpopular) VALUES (%u, %u, '%s', %u, %u, %u, %u)", $xoopsDB->prefix($helper->getDirname() . '_cat'), $newid, $p_dirid, $p_title, $p_active, $p_pid, $p_allowlist, $p_showpopular);
            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }
            if (0 == $newid) {
                $cid = $xoopsDB->getInsertId();
            }
            if ('' != $p_descr) {
                $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat_txt') . '_txtid_seq');
                $sql2  = sprintf("INSERT INTO `%s` (txtid, cid, TEXT, active, created) VALUES (%u, %u, '%s', %u, '%s')", $xoopsDB->prefix($helper->getDirname() . '_cat_txt'), $newid, $cid, $p_descr, $p_txtactive, time());
                $xoopsDB->query($sql2);
                if (!$result) {
                    $logger->handleError(E_USER_WARNING, $sql2, __FILE__, __LINE__);
                }
            }
            if ('1' == $p_import) {
                importDtypes($p_pid, $cid);
            }
            redirect_header("categories.php?op=edit&catid=$cid", 2, _MD_CAT_SAVED);
        }
        $uploader->setPrefix('efqdir');
        if ($uploader->upload()) {
            $savedfilename = $uploader->getSavedFileName();
            //echo $savedfilename;
            $width         = $uploader->getWidth();
            $height        = $uploader->getHeight();
            $imagelocation = $uploader->uploadDir;
            echo $uploader->getErrors();
            $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat') . '_cid_seq');
            $sql   = sprintf("INSERT INTO `%s`(cid, dirid, title, active, pid, img, allowlist, showpopular, width, height) VALUES (%u, %u, '%s', %u, %u, '%s', %u, %u, %u, %u)", $xoopsDB->prefix($helper->getDirname() . '_cat'), $newid, $p_dirid, $p_title, $p_active, $p_pid, $savedfilename,
                             $p_allowlist, $p_showpopular, $width, $height);

            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }
            if (0 == $newid) {
                $cid = $xoopsDB->getInsertId();
            }
            if ('' != $p_descr) {
                $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat_txt') . '_txtid_seq');
                $sql2  = sprintf("INSERT INTO `%s` (txtid, cid, TEXT, active, created) VALUES (%u, %u, '%s', %u, '%s')", $xoopsDB->prefix($helper->getDirname() . '_cat_txt'), $newid, $cid, $p_descr, $p_txtactive, time());

                $result = $xoopsDB->query($sql2);
                if (!$result) {
                    $logger->handleError(E_USER_WARNING, $sql2, __FILE__, __LINE__);
                }
            }
            if ('1' == $p_import) {
                importDtypes($p_pid, $cid);
            }
            rename('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '', '' . XOOPS_ROOT_PATH . "/modules/$moddir/uploads/" . $savedfilename . '');
            //Delete the uploaded file from the initial upload folder if it is still present in that folder.
            if (file_exists('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '')) {
                unlink('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '');
            }
            redirect_header("categories.php?op=edit&catid=$cid", 2, _MD_CAT_SAVED);
        } else {
            echo $uploader->getErrors();
            $newid  = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat') . '_cid_seq');
            $sql    = sprintf("INSERT INTO `%s` (cid, dirid, title, active, pid, img, allowlist, showpopular, width, height) VALUES (%u, %u, '%s', %u, %u, %u, %u, %u, %u, %u)", $xoopsDB->prefix($helper->getDirname() . '_cat'), $newid, $p_dirid, $p_title, $p_active, $p_pid, '', $p_allowlist,
                              $p_showpopular, '', '');
            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }
            if (0 == $newid) {
                $cid = $xoopsDB->getInsertId();
            }
            if ('' != $p_descr) {
                $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_cat_txt') . '_txtid_seq');
                $sql2  = sprintf("INSERT INTO `%s` (txtid, cid, TEXT, active, created) VALUES (%u, %u, '%s', %u, '%s')", $xoopsDB->prefix($helper->getDirname() . '_cat_txt'), $newid, $cid, $p_descr, $p_txtactive, time());
                $xoopsDB->query($sql2);
                if (!$result) {
                    $logger->handleError(E_USER_WARNING, $sql2, __FILE__, __LINE__);
                }
            }
            if ('1' == $p_import) {
                importDtypes($p_pid, $cid);
            }
            redirect_header("categories.php?op=edit&catid=$cid", 2, _MD_CAT_SAVED);
        }
    }

    redirect_header("categories.php?op=edit&catid=$cid", 2, _MD_CAT_SAVED);
}

function deleteCatConfirm()
{
    global $xoopsDB, $_POST, $get_catid;
    xoops_cp_header();
    $form        = new \XoopsThemeForm(_MD_CONFIRM_DELETECAT_FORM, 'confirmform', 'categories.php');
    $submit_tray = new \XoopsFormElementTray(_MD_DELETE_CAT_YN, '', 'cid');
    $submit_tray->addElement(new \XoopsFormButton('', 'submit', _MD_DELETE, 'submit'));
    $submit_tray->addElement(new \XoopsFormLabel('', '<input type="button" class="formButton" value="' . _MD_CANCEL . "\" onclick=\"location='categories.php?op=edit&amp;catid=$get_catid'\""));
    $form->addElement($submit_tray, true);
    //'$form->addElement($form_submit);
    $form->addElement(new \XoopsFormHidden('op', 'deleteCat'));
    $form->addElement(new \XoopsFormHidden('catid', $get_catid));
    $form->display();
    xoops_cp_footer();
}

function deleteCat()
{
    global $xoopsDB, $_POST, $xoopsModule;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    if (isset($_POST['catid'])) {
        $p_catid = (int)$_POST['catid'];
    } else {
        redirect_header('directories.php', 2, _MD_INVALID_DIR);
    }
    $dirid = getDirId($p_catid);

    $sql    = sprintf('DELETE FROM `%s` WHERE cid = %u', $xoopsDB->prefix($helper->getDirname() . '_item_x_cat'), $p_catid);
    $result = $xoopsDB->queryF($sql);
    {
        if (!$result) {
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }
        $sql    = sprintf('DELETE FROM `%s` WHERE cid = %u', $xoopsDB->prefix($helper->getDirname() . '_cat'), $p_catid);
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }

        $sql    = sprintf('DELETE FROM `%s` WHERE cid = %u', $xoopsDB->prefix($helper->getDirname() . '_cat_txt'), $p_catid);
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }

        $sql    = sprintf('DELETE FROM `%s` WHERE cid = %u', $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat'), $p_catid);
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }

        redirect_header('categories.php?dirid=' . $dirid, 2, _MD_CAT_DELETED);
    }
}

if (!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
    $op = $_POST['op'];
}
switch ($op) {
    case 'newdatatype':
        addDatatype();
        break;
    case 'editdtypes':
        editDatatypes();
        break;
    case 'editdtype':
        editDatatype($get_dtypeid);
        break;
    case 'savedtype':
        saveDatatype();
        break;
    case 'importdtypes':
        importDatatypes();
        break;
    case 'edit':
        editCat($get_catid);
        break;
    case 'update':
        updateCat();
        break;
    case 'newcat':
        newCat();
        break;
    case 'deleteCat':
        deleteCat();
        break;
    case 'deleteCatConfirm':
        deleteCatConfirm();
        break;
    default:
        catConfig($get_dirid);
        break;
}

/**
 * @return string
 */
function getCatOverview()
{
    global $xoopsDB, $myts, $mytree, $get_dirid, $moddir;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper     = Efqdirectory\Helper::getInstance();
    $sql        = 'SELECT cid, title, active, pid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE dirid='" . $get_dirid . '\' AND pid=\'0\'';
    $mainresult = $xoopsDB->query($sql);
    if (!$mainresult) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $numrows = $xoopsDB->getRowsNum($mainresult);
    $output  = '';
    if ($numrows > 0) {
        $output = '<th>' . _MD_CATTITLE . '</th><th>' . _MD_ACTIVE . '</th><th>' . _MD_ACTION . "</th>\n";
        $brench = 0;
        $tab    = '';
        while (false !== (list($cid, $title, $active, $pid) = $xoopsDB->fetchRow($mainresult))) {
            //For each cid, get all 'first children' using getFirstChildId() function
            if ('0' != $active) {
                $activeyn = '' . _MD_YES . '';
            } else {
                $activeyn = '' . _MD_NO . '';
            }
            $output .= '<tr><td>'
                       . $tab
                       . ''
                       . $title
                       . '</td><td>'
                       . $activeyn
                       . '</td><td><a href="'
                       . XOOPS_URL
                       . "/modules/$moddir/admin/categories.php?op=edit&catid=$cid\"><img src=\""
                       . XOOPS_URL
                       . '/modules/'
                       . $moddir
                       . '/assets/images/accessories-text-editor.png" title="'
                       . _MD_MANAGE_CAT
                       . '" alt="'
                       . _MD_MANAGE_CAT
                       . "\"></a></td></tr>\n";
            $output .= getChildrenCategories($cid);
        }
    } else {
        $output = '<p><span style="background-color: #E6E6E6; padding: 5px; border: 1px solid #000000;">' . _MD_NORESULTS_PLEASE_CREATE_CATEGORY . '</span></p>';
    }

    return $output;
}

/**
 * @param string|int $childid
 * @param string|int $level
 * @return string
 */
function getChildrenCategories($childid = 0, $level = 0)
{
    global $xoopsDB, $myts, $mytree, $get_dirid, $moddir;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    $tab    = '&nbsp;';
    $level  = $level;
    $output = '';
    $plus   = '<img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/arrow.gif">';
    for ($i = 0; $i < $level; ++$i) {
        $tab .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $sql         = 'SELECT cid, title, active, pid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE dirid='" . $get_dirid . '\' AND pid=\'' . $childid . '\'';
    $childresult = $xoopsDB->query($sql);
    if (!$childresult) {
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $numrows = $xoopsDB->getRowsNum($childresult);
    if ($numrows > 0) {
        while (false !== (list($cid, $title, $active, $pid) = $xoopsDB->fetchRow($childresult))) {
            if ('0' != $active) {
                $activeyn = '' . _MD_YES . '';
            } else {
                $activeyn = '' . _MD_NO . '';
            }
            $output   .= '<tr><td>'
                         . $tab
                         . $plus
                         . $title
                         . '&nbsp;</td><td>'
                         . $activeyn
                         . '</td><td><a href="'
                         . XOOPS_URL
                         . "/modules/$moddir/admin/categories.php?op=edit&catid=$cid\"><img src=\""
                         . XOOPS_URL
                         . '/modules/'
                         . $moddir
                         . '/assets/images/accessories-text-editor.png" title="'
                         . _MD_MANAGE_CAT
                         . '" alt="'
                         . _MD_MANAGE_CAT
                         . "\"></a></td></tr>\n";
            $newlevel = $level + 1;
            $output   .= getChildrenCategories($cid, $newlevel);
        }
    }

    return $output;
}

/**
 * @param string $pid
 * @param string $catid
 */
function importDtypes($pid = '0', $catid = '0')
{
    global $xoopsDB, $_POST;
    $logger = \XoopsLogger::getInstance();
    /** @var Efqdirectory\Helper $helper */
    $helper = Efqdirectory\Helper::getInstance();
    if (0 != $pid) {
        $sql = 'INSERT INTO '
               . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
               . ' (cid, dtypeid) SELECT '
               . $catid
               . ', d.dtypeid FROM '
               . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
               . ' d, '
               . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
               . ' x  WHERE d.dtypeid=x.dtypeid AND x.cid = '
               . $pid
               . " AND d.defaultyn = '1'";

        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }
    }

    //    $result = $xoopsDB->queryF($sql);
    //    if (!$result) {
    //        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    //    }
}
