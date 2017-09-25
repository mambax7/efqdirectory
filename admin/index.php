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
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

require_once __DIR__ . '/admin_header.php';
//include __DIR__ . '/../../../include/cp_header.php';

xoops_cp_header();

$adminObject = \Xmf\Module\Admin::getInstance();

/** @var EfqDirectoryUtility $utilityClass */
$utilityClass = ucfirst($moduleDirName) . 'Utility';
if (!class_exists($utilityClass)) {
    xoops_load('utility', $moduleDirName);
}

$configurator = include __DIR__ . '/../include/config.php';
foreach (array_keys($configurator->uploadFolders) as $i) {
    $utilityClass::createFolder($configurator->uploadFolders[$i]);
    $adminObject->addConfigBoxLine($configurator->uploadFolders[$i], 'folder');
}

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayIndex();

echo $utilityClass::getServerStats();

include __DIR__ . '/admin_footer.php';
