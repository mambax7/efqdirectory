<?php
// $Id: xdir_migrate.php wtravel
//  ------------------------------------------------------------------------ //
//                				EFQ Directory			                     //
//                    Copyright (c) 2006 EFQ Consultancy                     //
//                       <http://www.efqdirectory.com/>                      //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
//	Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
// ------------------------------------------------------------------------- //
include '../../../include/cp_header.php';
if (file_exists('../language/' . $xoopsConfig['language'] . '/main.php')) {
    include '../language/' . $xoopsConfig['language'] . '/main.php';
} else {
    include '../language/english/main.php';
}
include '../include/functions.php';
include '../class/class.fieldtype.php';
include '../class/class.datatype.php';
include '../class/class.directory.php';
include '../class/class.xdir.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
$myts =& MyTextSanitizer::getInstance();
$eh = new ErrorHandler;

$moddir = $xoopsModule->getVar('dirname');

if (isset($_GET['dirid'])) {
    $get_dir = intval($_GET['dirid']);
}

function xdirConfig()
{
    global $xoopsDB, $xoopsModule, $xoopsUser, $myts, $moddir;
    xoops_cp_header();
    adminmenu(0, _MD_A_DIRADMIN);
    echo '<h4>' . _MD_MIGRATE_FROM_XDIR . '</h4>';
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td>";
    $form = new XoopsThemeForm(_MD_XIDR_MIGRATE_TO_NEWDIR, 'submitform', 'xdir_migrate.php');
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

function newDir()
{
    global $xoopsDB, $_POST, $myts, $eh;
    if (isset($_POST['dirname']) and $_POST['dirname'] != '') {
        $p_dirname = $_POST['dirname'];
    } else {
        redirect_header("directories.php?op=moddir&dirid=$db_dirid", 2, _MD_XDIR_CREATE_EMPTY_DIR);
    }
    if (isset($_POST['open'])) {
        $p_open = $_POST['open'];
    } else {
        $p_open = 0;
    }
    $directory = new efqDirectory;
    $directory->setVar('name', $p_dirname);
    $directory->setVar('open', $p_open);
    $directory_handler = new efqDirectoryHandler;
    $directory_handler->insertDirectory($directory);
    $db_dirid = $directory->getVar('dirid');
    
    if ($db_dirid > 0) {
        $xdirHandler = new efqXdirHandler();
        $xdirHandler->doMigrate($db_dirid);
        $migration_errors = $xdirHandler->get_errors();
        if (count($migration_errors) > 0) {
            redirect_header('xdir_migrate.php', 2, _MD_XDIR_MIGRATION_FAILED);
        } else {
            redirect_header('directories.php?op=moddir&dirid='.$db_dirid, 2, _MD_XDIR_MIGRATION_COMPLETED);
        }
    } else {
        redirect_header('xdir_migrate.php', 2, _MD_XDIR_MIGRATION_FAILED);
    }
}

if (!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'dirConfig';
} else {
    $op = $_POST['op'];
}
switch ($op) {

case 'newdir':
    newDir();
    break;
default:
    xdirConfig();
    break;
}
