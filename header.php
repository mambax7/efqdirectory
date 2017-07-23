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

include __DIR__ . '/../../mainfile.php';
$info = __DIR__;
if (preg_match("#[\/]#", $info)) {
    $split = preg_split("[\]", $info);
} else {
    $split = preg_split('[/]', $info);
}
$count  = count($split) - 1;
$moddir = $split[$count];
include __DIR__ . '/include/functions.php';
$xoops_module_header = '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/' . $moddir . '/assets/css/efqdirectory.css">';
