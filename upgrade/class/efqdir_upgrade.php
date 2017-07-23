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
class EfqDirUpgrade
{
    /**
     * @param string $filename
     * @return string
     */
    public function prepare2upgrade($filename = '')
    {
        global $moddir, $xoopsConfig;
        require_once XOOPS_ROOT_PATH . '/modules/' . $moddir . '/upgrade/class/dbmanager.php';
        require_once XOOPS_ROOT_PATH . '/modules/' . $moddir . '/upgrade/language/' . $xoopsConfig['language'] . '/install.php';
        $dbm = new db_manager;
        $dbm->queryFromFile(XOOPS_ROOT_PATH . '/modules/' . $moddir . '/sql/' . $filename);

        return $dbm->report();
    }
}
