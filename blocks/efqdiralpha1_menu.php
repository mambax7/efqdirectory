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

/******************************************************************************
 * Function: b_efqdiralpha1_menu_show
 * Input   : $options[0] = date for the most recent links
 *                    hits for the most popular links
 *           $block['content'] = The optional above content
 *           $options[1]   = How many reviews are displayes
 * Output  : Returns the desired most recent or most popular links
 *****************************************************************************
 * @param $options
 * @return array
 */

use  XoopsModules\Efqdirectory;

function b_efqdiralpha1_menu_show($options)
{
    global $xoopsDB, $xoopsModule, $eh;
    $moduleDirName       = basename(dirname(__DIR__));
    require_once __DIR__ .  '/../class/Helper.php';
    $helper = Efqdirectory\Helper::getInstance();
    //  $info = __DIR__;
    //  $split = preg_split("#[\\\]#", $info);
    //  $count = count($split) - 2;
    //  $moddir = $split[$count];
    //    $moddir = $xoopsModule->getvar("dirname");

    $block                 = [];
    $block['lang_dirmenu'] = _MB_EFQDIR_MENU;
    $block['moddir']       = $moduleDirName;
    $myts                  = \MyTextSanitizer::getInstance();
    $sql                   = 'SELECT dirid, name, descr FROM ' . $xoopsDB->prefix($helper->getDirname() . '_dir') . " WHERE open='1' ORDER BY name";
    $result                = $xoopsDB->query($sql) ; //|| $eh->show('0013');
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $directory              = [];
        $name                   = $myts->htmlSpecialChars($myrow['name']);
        $directory['dirid']     = $myrow['dirid'];
        $directory['name']      = $name;
        $directory['descr']     = $myrow['descr'];
        $block['directories'][] = $directory;
    }
    $sublink = [];

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_efqdiralpha1_menu_edit($options)
{
    $form = '' . _MB_EFQDIR_DISP . '&nbsp;';
    $form .= "<input type='hidden' name='options[]' value='";
    if ('date' === $options[0]) {
        $form .= "date'";
    } else {
        $form .= "hits'";
    }
    $form .= '>';
    $form .= "<input type='text' name='options[]' value='" . $options[1] . '\'>&nbsp;' . _MB_EFQDIR_LISTINGS . '';
    $form .= '&nbsp;<br>' . _MB_EFQDIR_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . '\'>&nbsp;' . _MB_EFQDIR_LENGTH . '';

    return $form;
}
