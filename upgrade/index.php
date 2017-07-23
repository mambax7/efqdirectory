<?php 
include '../../../include/cp_header.php';
include '../include/functions.php';

$filename = 'upgrade_018-019.sql';
include_once "class/efqdir_upgrade.php";
$moddir = $xoopsModule->getvar("dirname");
include_once XOOPS_ROOT_PATH.'/modules/'.$moddir.'/upgrade/language/'.$xoopsConfig['language'].'/install.php';
xoops_cp_header();
set_magic_quotes_runtime(1);
if (isset($_POST['submit'])) {
    switch($_POST['submit']) {
        case "Update":
        include_once XOOPS_ROOT_PATH.'/modules/'.$moddir.'/upgrade/class/dbmanager.php';
	    $dbm = new db_manager;
	    $dbm->queryFromFile(XOOPS_ROOT_PATH.'/modules/'.$moddir.'/sql/'.$filename);
	    $feedback = $dbm->report();
	    echo $feedback;
	    echo "<br /><br /><a href='".XOOPS_URL."/modules/system/admin.php?fct=modulesadmin'>Proceed</a>";
	    xoops_cp_footer();
	    exit();
    }
}
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
$upgrade_form = new XoopsThemeForm('Upgrade', 'upgradeform', 'index.php');
$upgrade_form->addElement(new XoopsFormButton(_MU_UPDATE, 'submit', 'Update', 'submit'));
$upgrade_form->display();


xoops_cp_footer();
?>