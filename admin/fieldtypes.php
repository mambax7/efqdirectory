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
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts    = MyTextSanitizer::getInstance();
$eh      = new ErrorHandler;
$mytree  = new XoopsTree($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat'), 'cid', 'pid');
$mytree2 = new XoopsTree($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_fieldtypes'), 'typeid', 0);

$moddir = $xoopsModule->getVar('dirname');

if (isset($_GET['dirid'])) {
    $get_dirid = (int)$_GET['dirid'];
}
if (isset($_GET['typeid'])) {
    $get_typeid = (int)$_GET['typeid'];
}
$fieldtypes = array(
    '0'        => '---',
    'textbox'  => _MD_FIELDNAMES_TEXTBOX,
    'textarea' => _MD_FIELDNAMES_TEXTAREA,
    'dhtml'    => _MD_FIELDNAMES_DHTMLTEXTAREA,
    'select'   => _MD_FIELDNAMES_SELECT,
    'checkbox' => _MD_FIELDNAMES_CHECKBOX,
    'radio'    => _MD_FIELDNAMES_RADIO,
    'yesno'    => _MD_FIELDNAMES_YESNO,
    'date'     => _MD_FIELDNAMES_DATE,
    'datetime' => _MD_FIELDNAMES_DATETIME, //'address' => _MD_FIELDNAMES_ADDRESS, //EDIT-RC10
    //'locationmap' => _MD_FIELDNAMES_LOCATIONMAP,
    'rating'   => _MD_FIELDNAMES_RATING,
    'url'      => _MD_FIELDNAMES_URL
    //'gallery' => _MD_FIELDNAMES_GALLERY
);

function fieldtypesConfig()
{
    global $xoopsDB, $xoopsModule, $xoopsUser, $moddir, $myts, $eh, $fieldtypes;
    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation(basename(__FILE__));
    //adminmenu(2, _MD_A_FTYPESADMIN);
    echo '<h4>' . _MD_FTYPECONF . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
    $result  = $xoopsDB->query('SELECT typeid, title, fieldtype, descr, ext, activeyn FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_fieldtypes') . ' ORDER BY fieldtype ASC') or $eh->show('0013');
    $numrows = $xoopsDB->getRowsNum($result);
    echo '<tr><th>' . _MD_TITLE . '</th><th>' . _MD_TYPE . '</th><th>' . _MD_EXT . '</th><th>' . _MD_ACTIVE . "</th></tr>\n";
    if ($numrows > 0) {
        while (list($typeid, $title, $type, $descr, $ext, $status) = $xoopsDB->fetchRow($result)) {
            if ($status != '0') {
                $statusyn = '' . _MD_YES . '';
            } else {
                $statusyn = '' . _MD_NO . '';
            }
            echo '<tr><td class="even" valign="top"><a href="'
                 . XOOPS_URL
                 . '/modules/'
                 . $moddir
                 . "/admin/fieldtypes.php?op=view&typeid=$typeid\">$title</a></td><td class=\"even\" valign=\"top\">$type</td><td class=\"even\" valign=\"top\">$ext</td><td class=\"even\" valign=\"top\">$statusyn</td>";
            echo "</td></tr>\n";
        }
    } else {
        echo '<tr><td>' . _MD_NORESULTS . '</td></tr>';
    }
    echo '</table>';
    echo '<br>';
    echo '<h4>' . _MD_CREATE_NEWFTYPE . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
    $form = new XoopsThemeForm(_MD_NEWFTYPEFORM, 'submitform', 'fieldtypes.php');

    $form->addElement(new XoopsFormText(_MD_TITLE, 'title', 100, 150, ''), true);
    //TO DO: change type field to drop down field, based on available types.
    $element_select = new XoopsFormSelect(_MD_FIELDTYPE, 'field_type');
    $element_select->addOptionArray($fieldtypes);
    //$form->addElement($type_select);
    $form->addElement($element_select);
    $ext_tray = new XoopsFormElementTray(_MD_EXT, '');
    $ext_text = new XoopsFormText('', 'ext', 80, 150, '');
    $ext_text->setExtra('disabled=true');
    $ext_text->setExtra('style=\'background-color:lightgrey\'');
    $ext_button = new XoopsFormLabel('', '<INPUT type="button" value="' . _MD_SET_EXT . "\", onClick=\"openExtManager('submitform','" . XOOPS_URL . '/modules/' . $moddir . "/admin/extensionmanager.php','field_type', '" . _MD_SELECT_FORMTYPE . '\')">');
    $ext_tray->addElement($ext_text);
    $ext_tray->addElement($ext_button);
    $form->addElement($ext_tray);
    $form->addElement(new XoopsFormTextArea(_MD_DESCRIPTION, 'descr', '', 8, 50, ''), true);
    $form_txtactive = new XoopsFormCheckBox(_MD_ACTIVE, 'status', 0);
    $form_txtactive->addOption(1, _MD_YESNO);
    $form->addElement($form_txtactive);
    $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
    $form->addElement(new XoopsFormHidden('op', 'addFieldtype'));
    $form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
    $form->display();

    //Javascript function to check if field type is selected. If not, then warn the user. Otherwise
    //open the extension manager window.
    $js = '';
    $js .= "\n<!-- Start Extension Manager JavaScript //-->\n<script type='text/javascript'>\n<!--//\n";
    $js .= "function openExtManager(formname,url,ele,warning) {\n";
    $js .= "myform = window.document.submitform;\n";
    $js .= "var typeid = myform.field_type.value;\n";
    $js .= "if (typeid == 0) {
        alert([warning]);
        } else {
        window.open([url],'ext_window','width=600,height=450');
        }\n";
    $js .= "}\n";
    $js .= "//--></script>\n<!-- End Extension Manager JavaScript //-->\n";
    echo $js;

    echo '</td></tr></table>';
    xoops_cp_footer();
}

function viewFieldtype()
{
    global $xoopsDB, $mytree, $mytree2, $xoopsUser, $get_typeid, $moddir, $eh, $fieldtypes;
    xoops_cp_header();
    //adminmenu(2, _MD_A_FTYPESADMIN);
    echo '<h4>' . _MD_VIEW_FIELDTYPE . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
    $result  = $xoopsDB->query('SELECT typeid, title, fieldtype, descr, ext, activeyn FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_fieldtypes') . " WHERE typeid='" . $get_typeid . '\'') or $eh->show('0013');
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        while (list($typeid, $title, $fieldtype, $descr, $ext, $activeyn) = $xoopsDB->fetchRow($result)) {
            $form = new XoopsThemeForm(_MD_EDITFTYPEFORM, 'submitform', 'fieldtypes.php');
            $form->addElement(new XoopsFormText(_MD_TITLE, 'title', 100, 150, "$title"), true);
            //TO DO: change type field to drop down field, based on available types.
            $element_select = new XoopsFormSelect(_MD_FIELDTYPE, 'field_type', $fieldtype);
            $element_select->addOptionArray($fieldtypes);

            //$form->addElement($type_select);
            $form->addElement($element_select);
            $ext_tray = new XoopsFormElementTray(_MD_EXT, '');
            $ext_text = new XoopsFormText('', 'ext', 80, 150, "$ext");
            $ext_text->setExtra('style=\'background-color:lightgrey\'');
            $ext_button = new XoopsFormLabel('', '<INPUT type="button" value="' . _MD_SET_EXT . "\", onClick=\"openExtManager('submitform','" . XOOPS_URL . '/modules/' . $moddir . "/admin/extensionmanager.php','field_type', '" . _MD_SELECT_FORMTYPE . '\')">');
            $ext_tray->addElement($ext_text);
            $ext_tray->addElement($ext_button);
            $form->addElement($ext_tray);
            $form->addElement(new XoopsFormTextArea(_MD_DESCRIPTION, 'descr', "$descr", 8, 50, ''), true);
            $form_txtactive = new XoopsFormCheckBox(_MD_ACTIVE, 'status', $activeyn);
            $form_txtactive->addOption(1, _MD_YESNO);
            $form->addElement($form_txtactive);
            $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
            $form->addElement(new XoopsFormHidden('op', 'editFieldtype'));
            $form->addElement(new XoopsFormHidden('typeid', $get_typeid));
            $form->addElement(new XoopsFormHidden('uid', $xoopsUser->getVar('uid')));
            $form->display();

            //Javascript function to check if field type is selected. If not, then warn the user. Otherwise
            //open the extension manager window.
            $js = '';
            $js .= "\n<!-- Start Extension Manager JavaScript //-->\n<script type='text/javascript'>\n<!--//\n";
            $js .= "function openExtManager(formname,url,ele,warning) {\n";
            $js .= "myform = window.document.submitform;\n";
            $js .= "var typeid = myform.field_type.value;\n";
            $js .= "if (typeid == 0) {
                alert([warning]);
                } else {
                window.open([url],'ext_window','width=600,height=450');
                }\n";
            $js .= "}\n";
            $js .= "//--></script>\n<!-- End Extension Manager JavaScript //-->\n";
            echo $js;
        }
    }
    echo '</td></tr></table>';
    //echo "<form name=\"deleteFieldTypeForm\" action=\"\"
    xoops_cp_footer();
}

function addFieldtype()
{
    global $xoopsDB, $_POST, $myts, $eh, $xoopsModule;
    $p_title     = $myts->makeTboxData4Save($_POST['title']);
    $p_fieldtype = $_POST['field_type'];
    $p_descr     = $myts->makeTareaData4Save($_POST['descr']);
    if (isset($_POST['ext'])) {
        $p_ext = $_POST['ext'];
    } else {
        $p_ext = '';
    }
    if (isset($_POST['status'])) {
        $p_status = (int)$_POST['status'];
    } else {
        $p_status = 0;
    }
    $newid = $xoopsDB->genId($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_fieldtypes') . '_typeid_seq');
    $sql   = sprintf("INSERT INTO %s (typeid, title, fieldtype, descr, ext, activeyn) VALUES (%u, '%s', '%s', '%s', '%s', '%s')", $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_fieldtypes'), $newid, $p_title, $p_fieldtype, $p_descr, $p_ext, $p_status);
    $xoopsDB->query($sql) or $eh->show('0013');
    redirect_header('fieldtypes.php', 2, _MD_SAVED);
    exit();
}

function editFieldtype()
{
    global $xoopsDB, $_POST, $myts, $eh;
    if (isset($_POST['typeid'])) {
        $p_typeid = (int)$_POST['typeid'];
    } else {
        exit();
    }
    $p_title     = $myts->makeTboxData4Save($_POST['title']);
    $p_fieldtype = $_POST['field_type'];
    $p_descr     = $myts->makeTareaData4Save($_POST['descr']);
    if (isset($_POST['ext'])) {
        $p_ext = $_POST['ext'];
    } else {
        $p_ext = '';
    }
    if (isset($_POST['status'])) {
        $p_status = (int)$_POST['status'];
    } else {
        $p_status = 0;
    }
    $sql = 'UPDATE ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_fieldtypes') . " SET title = '$p_title', fieldtype='$p_fieldtype', ext='$p_ext', activeyn='$p_status' WHERE typeid = $p_typeid";
    $xoopsDB->query($sql) or $eh->show('0013');
    redirect_header("fieldtypes.php?op=view&typeid=$p_typeid", 2, _MD_SAVED);
    exit();
}

function newCat()
{
    global $xoopsDB, $myts, $eh;
    if (isset($_POST['dirid'])) {
        $p_dirid = (int)$_POST['dirid'];
    } else {
        exit();
    }
    $p_title       = $myts->makeTboxData4Save($_POST['title']);
    $p_active      = (int)$_POST['active'];
    $p_pid         = (int)$_POST['pid'];
    $p_allowlist   = (int)$_POST['allowlist'];
    $p_showpopular = (int)$_POST['showpopular'];
    if (isset($_POST['descr'])) {
        $p_descr = $myts->makeTareaData4Save($_POST['descr']);
    } else {
        $p_descr = '';
    }
    $newid = $xoopsDB->genId($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat') . '_cid_seq');
    $sql   = sprintf("INSERT INTO %s (cid, dirid, title, active, pid) VALUES (%u, %u, '%s', %u, %u)", $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat'), $newid, $p_dirid, $p_title, $p_active, $p_pid);
    //echo $sql;
    $xoopsDB->query($sql) or $eh->show('0013');
    if ($newid == 0) {
        $cid = $xoopsDB->getInsertId();
    }
    $newid = $xoopsDB->genId($xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat_txt') . '_txtid_seq');
    $sql2  = sprintf("INSERT INTO %s (txtid, cid, TEXT, active, created) VALUES (%u, %u, '%s', %u, '%s')", $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat_txt'), $newid, $cid, $p_descr, '1', time());
    //echo $sql2;
    $xoopsDB->query($sql2) or $eh->show('0013');
    redirect_header("categories.php?op=edit&cid=$newid", 0, _MD_CAT_UPDATED);
    exit();
}

if (!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
    $op = $_POST['op'];
}
switch ($op) {
    case 'view':
        viewFieldtype();
        break;
    case 'editFieldtype':
        editFieldtype();
        break;
    case 'addFieldtype':
        addFieldtype();
        break;
    default:
        fieldtypesConfig();
        break;
}

function getCatOverview()
{
    global $xoopsDB, $myts, $eh, $mytree, $get_dirid, $moddir;
    $mainresult = $xoopsDB->query('SELECT cid, title, active, pid FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat') . " WHERE dirid='" . $get_dirid . '\' AND pid=\'0\'');
    $numrows    = $xoopsDB->getRowsNum($mainresult);
    $output     = '';
    if ($numrows > 0) {
        $output = '<th>' . _MD_CATTITLE . '</th><th>' . _MD_ACTIVEYN . '</th><th>' . _MD_PARENTCAT . "</th>\n";
        $brench = 0;
        $tab    = '';
        while (list($cid, $title, $activeyn, $pid) = $xoopsDB->fetchRow($mainresult)) {
            $output .= '<tr><td>' . $tab . '<a href="' . XOOPS_URL . "/modules/$moddir/admin/categories.php?op=edit&cid=$cid\">" . $title . '</a></td><td>' . $activeyn . "</td></tr>\n";
            $output .= getChildrenCategories($cid);
        }
    } else {
        $output = '' . _MD_NORESULTS . '';
    }

    return $output;
}

function getChildrenCategories($childid = '0', $level = '1')
{
    global $xoopsDB, $myts, $eh, $mytree;
    $firstchildcats = $mytree->getFirstChildId($childid);
    $tab            = '';
    $output         = '';
    $plus           = '<img src="' . XOOPS_URL . "\images\arrow.jpg\">";
    for ($i = 0; $i < $level; ++$i) {
        $tab .= '&nbsp;&nbsp;';
    }
    foreach ($firstchildcats as $childid) {
        $childresult = $xoopsDB->query('SELECT cid, title, active, pid FROM ' . $xoopsDB->prefix($xoopsModule->getVar('dirname', 'n') . '_cat') . " WHERE pid='" . $childid . '\'');
        //$childresult = $xoopsDB->query("SELECT cid, title, active, pid FROM ".$xoopsDB->prefix("efqdiralpha1_cat")." WHERE dirid='".$dirid."' AND pid='".$childid."'");
        $numrows = $xoopsDB->getRowsNum($childresult);
        if ($numrows > 0) {
            while (list($cid, $title, $activeyn, $pid) = $xoopsDB->fetchRow($childresult)) {
                $output   .= '<tr><td>' . $tab . '' . $plus . '</td><td>' . $title . '</td><td>' . $activeyn . "</td></tr>\n";
                $newlevel = ++$level;
                $output   .= getChildrenCategories($cid, $newlevel);
            }
        }
    }

    return $output;
}
