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
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts = MyTextSanitizer::getInstance();
$eh   = new ErrorHandler;
if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = 'showExtFields';
}
if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = 'text';
}
if (isset($_GET['ext'])) {
    $ext = $_GET['ext'];
} else {
    $ext = '';
}

/**
 * @param string $type
 * @param string $ext
 */
function showExtFields($type = 'text', $ext = '')
{
    global $xoopsConfig, $xoopsDB, $_POST, $myts, $eh, $xoopsUser;

    //MHE - This is added code for the listings module, handling the extension manager.
    if (!headers_sent()) {
        header('Content-Type:text/html; charset=' . _CHARSET);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN"\n';
    echo '"http://www.w3.org/TR/html4/strict.dtd">';
    echo "<html lang=\"en\" dir=\"ltr\">\n";
    echo "<head>\n";
    echo '<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '">';
    echo '<meta http-equiv="content-language" content="' . _LANGCODE . '">';
    echo '<title>Extension Manager</title>';
    echo '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/xoops.css">';
    echo '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/modules/system/style.css">';
    echo '<script type="text/javascript"><!--';
    echo '
        function ext_initial_adv(formName, obj, type)
        {
        typeid = opener.document.forms[formName].elements[type].value;
        if (typeid == 0) {
        alert("' . _MD_SELECT_FORMTYPE . "\");
        self.close();
        }
        var arr_fields = new makeArray(9);
        arr_fields[0] = 'width';
        arr_fields[1] = 'height';
        arr_fields[2] = 'rows';
        arr_fields[3] = 'cols';
        arr_fields[4] = 'size';
        arr_fields[5] = 'maxsize';
        arr_fields[6] = 'value';
        arr_fields[7] = 'multiple';
        arr_fields[8] = 'checked';
        length_fields = arr_fields.length;
        bgc_type_checkbox_grp = /checked|multiple/gi;
        bgc_type_date_grp = /value/gi;
        bgc_type_datetime_grp = /value/gi;
        bgc_type_radio_grp = /checked/gi;
        bgc_type_select_grp = /value|multiple/gi;
        bgc_type_textarea_grp = /rows|cols|value/gi;
        bgc_type_dhtml_grp = /rows|cols|value/gi;
        bgc_type_textbox_grp = /size|maxsize|value/gi;
        bgc_type_yesno_grp = /value/gi;
        x = 0;
        reg_checkboxes = /multiple|checked/gi;
        while (x<length_fields) {
        if (typeid == 'checkbox') {
        if (arr_fields[x].match(bgc_type_checkbox_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'date') {
        if (arr_fields[x].match(bgc_type_date_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'datetime') {
        if (arr_fields[x].match(bgc_type_datetime_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'radio') {
        if (arr_fields[x].match(bgc_type_radio_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'select') {
        if (arr_fields[x].match(bgc_type_select_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'textarea') {
        if (arr_fields[x].match(bgc_type_textarea_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'dhtml') {
        if (arr_fields[x].match(bgc_type_dhtml_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'textbox') {
        if (arr_fields[x].match(bgc_type_textbox_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        if (typeid == 'yesno') {
        if (arr_fields[x].match(bgc_type_yesno_grp)) {
        var fieldname = arr_fields[x];
        } else {
        var fieldname = arr_fields[x];
        ext_manager.elements[fieldname].disabled = true;
        ext_manager.elements[fieldname].style.backgroundColor = 'lightgrey';
        }
        }
        x ++;
        }
        var ext = opener.document.forms[formName].elements[obj].value;
        regx = \"|\";
        ext_array = ext.split(regx);
        length = ext_array.length;
        i = 0;
        while (i<length) {
        item = ext_array[i];
        regy = \"=\";
        item_array = item.split(regy);
        item_length = item_array.length;
        reg_textboxes = /width|height|rows|cols|size|maxsize|value/gi;
        reg_checkboxes = /multiple|checked/gi;
        if (item_array[0].match(reg_textboxes)) {
        var fieldname = item_array[0];
        var fieldvalue = item_array[1];
        ext_manager.elements[fieldname].value = fieldvalue;
        }
        if (item_array[0].match(reg_checkboxes)) {
        var fieldname = item_array[0];
        var fieldvalue = item_array[1];
        if (fieldvalue == 1) {
        self_check(ext_manager, fieldname, 1);
        } else {
        self_check(ext_manager, fieldname, 0);
        }
        }
        i ++;
        }
        }\n";
    echo "
        function makeArray(n)
        {
        this.length = n;
        for (var i=1; i <=n; i++) {
        this[i] = null;
        }

        return this;
        }\n";
    echo "
        function updatefield(formname, fieldname, fieldvalue)
        {
        myform.elements[fieldname].value = [fieldvalue];
        }\n";
    echo "
        function ext_input(formName, obj)
        {
        reg_checkboxes = /multiple|checked/gi;
        var arr_fields = new makeArray(9);
        arr_fields[0] = 'width';
        arr_fields[1] = 'height';
        arr_fields[2] = 'rows';
        arr_fields[3] = 'cols';
        arr_fields[4] = 'size';
        arr_fields[5] = 'maxsize';
        arr_fields[6] = 'value';
        arr_fields[7] = 'multiple';
        arr_fields[8] = 'checked';
        length_fields = arr_fields.length;
        i = 0;
        y = 0;
        var output = '';
        while (i<length_fields) {
        var fieldname = arr_fields[i];
        if (fieldname.match(reg_checkboxes)) {
        if (ext_manager.elements[fieldname].checked === true) {
        var fieldvalue = 1;
        } else {
        var fieldvalue = 0;
        }
        } else {
        var fieldvalue = ext_manager.elements[fieldname].value;
        }
        if (fieldvalue != '') {
        if (y == 0) {
        output += fieldname+'='+fieldvalue;
        } else {
        output +='|'+fieldname+'='+fieldvalue;
        }
        y ++;
        }
        i ++;
        }
        opener.document.forms[formName].elements[obj].value = output;
        opener.document.forms[formName].elements[obj].disabled = false;
        self.close();
        }\n";
    echo "
        function ext_select(formName, obj, idx)
        {
        opener.document.forms[formName].elements[obj].selectedIndex = idx;
        self.close();
        }\n";
    echo "
        function self_checkRadio(formName, obj, choice)
        {
        ext_manager.elements[obj][choice].checked = true;
        }\n";
    echo "
        function ext_checkRadio(formName, obj, choice)
        {
        opener.document.forms[formName].elements[obj][choice].checked = true;
        self.close();
        }\n";
    echo "
        function self_check(formName, obj, choice)
        {
        ext_manager.elements[obj].checked = choice;
        }\n";
    echo "//--></script>\n";
    echo "</head>\n";
    echo '<body onload="ext_initial_adv(\'submitform\', \'ext\', \'field_type\')">';
    echo "<table border='0' width='100%' cellspacing='0' cellpadding='0'>
        <tr>
        <td bgcolor='#2F5376'><a href='https://xoops.org/' target='_blank'><img src='" . XOOPS_URL . "/images/logo.gif' alt='" . $GLOBALS['xoopsConfig']['sitename'] . "'></a></td>
        <td align='right' bgcolor='#2F5376'><img src='" . XOOPS_URL . "/images/logo.gif' alt=''></td>
        </tr><tr><td colspan='2'><div class='content'><br>";
    $form = new XoopsThemeForm(_MD_EXTMANAGER, 'ext_manager', 'fieldtypes.php');

    //TO DO: change type field to drop down field, based on available types.
    $fieldtypes = array(
        '0'            => '---',
        'checkbox'     => _MD_FIELDNAMES_CHECKBOX,
        'date'         => _MD_FIELDNAMES_DATE,
        'datetime'     => _MD_FIELDNAMES_DATETIME,
        'radio'        => _MD_FIELDNAMES_RADIO,
        'select'       => _MD_FIELDNAMES_SELECT,
        'select_multi' => _MD_FIELDNAMES_SELECTMULTI,
        'textarea'     => _MD_FIELDNAMES_TEXTAREA,
        'dhtml'        => _MD_FIELDNAMES_DHTMLTEXTAREA,
        'textbox'      => _MD_FIELDNAMES_TEXTBOX,
        'yesno'        => _MD_FIELDNAMES_YESNO
    );

    $form->addElement(new XoopsFormText(_MD_WIDTH, 'width', 10, 20, ''));
    $form->addElement(new XoopsFormText(_MD_HEIGHT, 'height', 10, 20, ''));
    $form->addElement(new XoopsFormText(_MD_ROWS, 'rows', 10, 20, ''));
    $form->addElement(new XoopsFormText(_MD_COLS, 'cols', 10, 20, ''));
    $form->addElement(new XoopsFormText(_MD_SIZE, 'size', 10, 20, ''));
    $form->addElement(new XoopsFormText(_MD_MAXSIZE, 'maxsize', 10, 20, ''));
    $form->addElement(new XoopsFormText(_MD_DEFAULTVALUE, 'value', 50, 100, ''));
    $form_multiple = new XoopsFormCheckBox(_MD_MULTIPLE, 'multiple', 0);
    $form_multiple->addOption(1, _MD_YESNO);
    $form_checked = new XoopsFormCheckBox(_MD_CHECKED, 'checked', 0);
    $form_checked->addOption(1, _MD_YESNO);
    $form->addElement($form_multiple);
    $form->addElement($form_checked);
    $ext_update = new XoopsFormLabel('', '<INPUT type="button" value="' . _MD_UPDATE . "\" onClick=\"ext_input('submitform','ext')\" value=\"Update\">");
    $ext_cancel = new XoopsFormLabel('', '<INPUT type="button" value="' . _MD_CANCEL . '" onClick="self.close()" value="Cancel">');
    $ext_tray   = new XoopsFormElementTray('', '');
    $ext_tray->addElement($ext_update);
    $ext_tray->addElement($ext_cancel);
    $form->addElement($ext_tray);
    $form->display();
    echo '</div>';
    echo '</td></tr></table>';
    echo '</body>';
}

switch ($op) {
    /*case "listNewLinks":
    listNewLinks();
    break;
    case "delLocs":
    delLocs();
    break;
    case "delLocs2":
    delLocationsToLink();
    break;*/
    default:
        showExtFields($type, $ext);
        break;
}
