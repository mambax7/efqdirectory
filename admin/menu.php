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
 * @package
 * @since
 * @author       XOOPS Development Team,
 */

// Part of the efqDirectory module provided by: wtravel      //
//  e-mail: info@efqdirectory.com            //
// Purpose: Create a business directory for xoops.          //
// Based upon the mylinks and the mxDirectory modules       //

$moduleDirName = basename(dirname(__DIR__));

if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}


$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
//$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

//$moduleHelper->loadLanguage('modinfo');

$adminmenu[] = [
    'title' => _AM_MODULEADMIN_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];
$adminmenu[] = [
    'title' => _MI_EFQDIR_ADMENU3,
    'link'  => 'admin/directories.php',
    'icon'  => $pathIcon32 . '/category.png',
];
$adminmenu[] = [
    'title' => _MI_EFQDIR_ADMENU4,
    'link'  => 'admin/fieldtypes.php',
    'icon'  => $pathIcon32 . '/manage.png'
];
$adminmenu[] = [
    'title' => _MI_EFQDIR_ADMENU5,
    'link'  => 'admin/main.php?op=listNewListings',
    'icon'  => 'images/prefs.png',
];
$adminmenu[] = [
    'title' => _MI_EFQDIR_ADMENU7,
    'link'  => 'admin/subscriptions.php',
    'icon'  => $pathIcon32 . '/manage.png'
];
$adminmenu[] = [
    'title' => _MI_EFQDIR_ADMENU8,
    'link'  => 'admin/main.php?op=duplicateDataTypes',
    'icon'  => $pathIcon32 . '/manage.png',
];
$adminmenu[] = [
    'title' => _AM_MODULEADMIN_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
//$adminmenu[5]['title'] = _MI_EFQDIR_ADMENU6;
//$adminmenu[5]['link'] = "admin/addresstypes.php";
