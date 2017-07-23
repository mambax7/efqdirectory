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
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once __DIR__ . '/../class/class.formimage.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts   = MyTextSanitizer::getInstance();
$eh     = new ErrorHandler;
$mytree = new XoopsTree($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat'), 'cid', 'pid');

$moddir = $xoopsModule->getVar('dirname');

if (isset($_GET['dirid'])) {
    $get_dir = (int)$_GET['dirid'];
}

function dirConfig()
{
    global $xoopsDB, $xoopsModule, $xoopsUser, $myts, $moddir;
    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation(basename(__FILE__));
    //adminmenu(1, _MD_A_DIRADMIN);
    echo '<h4>' . _MD_DIRCONF . '</h4>';
    //Get a list of directories and their properties (included number of categories and items?)
    $result  = $xoopsDB->query('SELECT dirid, postfix, open, name FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir') . '');
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        echo '<form action="directories.php?&op=changestatus" method="post" name="select_directories_form">';
        echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
        echo '<tr><th>&nbsp;</th><th>' . _MD_DIRNAME . '</th><th>' . _MD_STATUS . '</th><th>' . _MD_TOTALCATS . '</th><th>' . _MD_ACTION . "</th></tr>\n";
        while (list($dirid, $postfix, $open, $name) = $xoopsDB->fetchRow($result)) {
            $sql              = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat') . " WHERE dirid='" . $dirid . '\'';
            $result_countcats = $xoopsDB->query($sql);
            $numrows          = $xoopsDB->getRowsNum($result_countcats);
            list($totalcats) = $xoopsDB->fetchRow($result_countcats);

            if ($open != '0') {
                $openyn = '' . _MD_OPEN . '';
            } else {
                $openyn = '' . _MD_CLOSED . '';
            }

            echo '<tr><td class="even"><input type="checkbox" name="select[]" value="'
                 . $dirid
                 . "\"></td><td class=\"even\">$name<a href=\""
                 . XOOPS_URL
                 . '/modules/'
                 . $moddir
                 . "/admin/directories.php?op=moddir&dirid=$dirid\">"
                 . _MD_EDIT_BRACKETS
                 . "</a></td><td class=\"even\">$openyn</td><td class=\"even\">$totalcats</td><td class=\"even\">";
            echo '<a href="' . XOOPS_URL . '/modules/' . $moddir . "/admin/categories.php?dirid=$dirid\"><img src=\"" . XOOPS_URL . '/modules/' . $moddir . '/assets/images/accessories-text-editor.png" title="' . _MD_MANAGE_CATS . '" alt="' . _MD_MANAGE_CATS . '"></a>';
            echo "</td></tr>\n";
        }
        echo '<tr><td colspan="5">' . _MD_WITH_SELECTED . ':&nbsp;';
        echo '<select name="fct" onChange="form.submit()">';
        echo '<option value="nothing">---</option>';
        echo '<option value="activate">' . _MD_OPEN . '</option>';
        echo '<option value="inactivate">' . _MD_CLOSE . '</option></select>';
        echo '</td></tr>';
        echo '</table>';
        echo '</form>';
    } else {
        echo '<p><span style="background-color: #E6E6E6; padding: 5px; border: 1px solid #000000;">' . _MD_NORESULTS_PLEASE_CREATE_DIRECTORY . '</span></p>';
    }
    echo '<br>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
    $form = new XoopsThemeForm(_MD_CREATE_NEWDIR, 'submitform', 'directories.php');
    $form->addElement(new XoopsFormText(_MD_DIRNAME, 'dirname', 100, 150, ''), true);
    $form_diropen = new XoopsFormCheckBox(_MD_OPENYN, 'open', 0);
    $form_diropen->addOption(1, _MD_YESNO);
    $form->addElement($form_diropen);
    $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
    $form->addElement(new XoopsFormHidden('op', 'newdir'));
    $form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->display();
    echo '</td></tr></table>';
    xoops_cp_footer();
}

function modDir($dirid = '0')
{
    global $xoopsDB, $xoopsModule, $myts, $xoopsUser, $moddir;
    xoops_cp_header();
    //adminmenu(1, _MD_A_DIRADMIN);
    echo '<h4>' . _MD_EDITDIR . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
    $result  = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir') . " WHERE dirid='" . $dirid . '\'');
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        while (list($dirid, $postfix, $open, $dirname, $descr, $pic) = $xoopsDB->fetchRow($result)) {
            if ($pic != '') {
                $picture = XOOPS_URL . "/modules/$moddir/uploads/$pic";
            } else {
                $picture = '/images/dummy.png';
            }
            $form = new XoopsThemeForm(_MD_EDITDIRFORM, 'editform', 'directories.php');
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement(new XoopsFormText(_MD_DIRNAME, 'dirname', 100, 150, $myts->htmlSpecialChars($dirname)));
            $form_diropen = new XoopsFormCheckBox(_MD_OPENYN, 'open', $open);
            $form_diropen->addOption(1, _MD_DIROPENYN);
            $form->addElement($form_diropen);
            $form->addElement(new XoopsFormTextArea(_MD_DESCRIPTION, 'descr', "$descr", 12, 50, ''));
            $form->addElement(new XoopsFormFile(_MD_SELECT_PIC, 'img', 30000));
            $form->addElement(new XoopsFormImage(_MD_CURRENT_PIC, 'current_image', null, "$picture", '', ''));
            $form->addElement(new XoopsFormButton('', 'submit', _MD_UPDATE, 'submit'));
            $form->addElement(new XoopsFormHidden('op', 'update'));
            $form->addElement(new XoopsFormHidden('dirid', $dirid));
            $form->addElement(new XoopsFormHidden('open_current', $open));
            $form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
            $form->display();
        }
    }
    echo myTextForm('' . XOOPS_URL . "/modules/$moddir/admin/directories.php", _MD_CANCEL);
    echo '</td></tr></table>';
    xoops_cp_footer();
}

function updateDir()
{
    global $xoopsDB, $_POST, $myts, $eh, $moddir;
    if (isset($_POST['dirid'])) {
        $p_dirid = (int)$_POST['dirid'];
    } else {
        echo 'no dirid';
        exit();
    }
    $p_dirname = $myts->makeTboxData4Save($_POST['dirname']);
    if (isset($_POST['open'])) {
        $p_open = $_POST['open'];
    } else {
        $p_open = '0';
    }
    if (isset($_POST['descr'])) {
        $p_descr = $myts->makeTareaData4Save($_POST['descr']);
    } else {
        $p_descr = '';
    }
    if ($_POST['xoops_upload_file'][0] != '') {
        require_once __DIR__ . '/../class/class.uploader.php';
        $uploader = new XoopsMediaUploader(XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads', array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'), 30000, 80, 80);
        if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
            $filename = $uploader->getMediaName();
        } else {
            $sql = 'UPDATE ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir') . " SET descr = '" . $p_descr . '\', open=\'' . $p_open . '\', name=\'' . $p_dirname . '\' WHERE dirid = \'' . $p_dirid . '\'';
            $xoopsDB->query($sql) || $eh->show('0013');
            redirect_header("directories.php?dirid=$p_dirid", 2, _MD_DIR_UPDATED);
            exit();
        }
        $uploader->setPrefix('efqdir');
        if ($uploader->upload()) {
            $savedfilename = $uploader->getSavedFileName();
            echo $uploader->getErrors();
            $sql = 'UPDATE ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir') . " SET img = '" . $savedfilename . '\', descr = \'' . $p_descr . '\', open=\'' . $p_open . '\', name=\'' . $p_dirname . '\' WHERE dirid = \'' . $p_dirid . '\'';
            $xoopsDB->query($sql) || $eh->show('0013');

            //Rename the uploaded file to the same name in a different location that does not have 777 rights or 755.
            rename('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '', '' . XOOPS_ROOT_PATH . "/modules/$moddir/uploads/" . $savedfilename . '');
            //Delete the uploaded file from the initial upload folder if it is still present in that folder.
            if (file_exists('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '')) {
                unlink('' . XOOPS_ROOT_PATH . "/modules/$moddir/init_uploads/" . $savedfilename . '');
            }
            redirect_header("directories.php?op=moddir&dirid=$p_dirid", 2, _MD_DIR_UPDATED);
            exit();
        } else {
            echo $uploader->getErrors();
            $sql = 'UPDATE ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir') . " SET descr = '" . $p_descr . '\', open=\'' . $p_open . '\', name=\'' . $p_dirname . '\' WHERE dirid = \'' . $p_dirid . '\'';
            $xoopsDB->query($sql) || $eh->show('0013');
            redirect_header("directories.php?dirid=$p_dirid", 2, _MD_DIR_UPDATED);
            exit();
        }
    }
    redirect_header("directories.php?dirid=$p_dirid", 2, _MD_DIR_NOT_UPDATED);
}

function changeStatus($status = 0)
{
    global $xoopsDB, $eh, $moddir;
    $select      = $_POST['select'];
    $users       = '';
    $count       = 0;
    $directories = '';
    $countselect = count($select);
    if ($countselect > 0) {
        foreach ($select as $directory) {
            if ($count > 0) {
                $directories .= ',' . $directory;
            } else {
                $directories .= $directory;
            }
            ++$count;
        }
        $sql = sprintf('UPDATE %s SET OPEN=' . $status . ' WHERE dirid IN (%s)', $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir'), $directories);
        $xoopsDB->query($sql) || $eh->show('0013');
        redirect_header('directories.php', 2, _MD_DIR_UPDATED);
        exit();
    } else {
        redirect_header('directories.php', 2, _MD_DIR_NOT_UPDATED);
    }
}

function newDir()
{
    global $xoopsDB, $xoopsModule, $_POST, $myts, $eh;
    if (isset($_POST['postfix'])) {
        $p_postfix = $_POST['postfix'];
    } else {
        $p_postfix = '';
    }
    $p_dirname = $_POST['dirname'];
    if (isset($_POST['open'])) {
        $p_open = $_POST['open'];
    } else {
        $p_open = 0;
    }
    $newid = $xoopsDB->genId($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir') . '_dirid_seq');
    $sql   = sprintf("INSERT INTO %s (dirid, postfix, OPEN, NAME) VALUES (%u, '%s', '%s', '%s')", $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_dir'), $newid, $p_postfix, $p_open, $p_dirname);
    $xoopsDB->query($sql) || $eh->show('0013');
    $db_dirid = $xoopsDB->getInsertId();
    redirect_header("directories.php?op=moddir&dirid=$db_dirid", 2, _MD_DIR_SAVED);
    exit();
}

if (!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'dirConfig';
} else {
    $op = $_POST['op'];
}
switch ($op) {
    case 'edit':
        editDir();
        break;
    case 'update':
        updateDir();
        break;
    case 'changestatus':
        if (isset($_POST['fct'])) {
            $fct = $_POST['fct'];
            if ($fct == 'activate') {
                $newstatus = 1;
            } elseif ($fct == 'inactivate') {
                $newstatus = 0;
            }
        }
        changeStatus($newstatus);
        break;
    case 'newdir':
        newDir();
        break;
    case 'moddir':
        modDir($get_dir);
        break;
    default:
        dirConfig();
        break;
}
