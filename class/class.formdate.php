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

defined('XOOPS_ROOT_PATH') || exit('XOOPS Root Path not defined');

/**
 * Class XoopsFormDate extends form classes for date selection
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.0.0
 *
 * @param   array $listing Array with listing details
 */
class XoopsFormDate extends XoopsFormElementTray
{
    /**
     * Function XoopsFormDate adds form element for selecting Date
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param string $caption
     * @param string $name
     * @param int    $size
     * @param string $value
     * @internal  param array $listing Array with listing details
     */
    public function __construct($caption, $name, $size = 15, $value = '')
    {
        parent::__construct($caption, '&nbsp;');
        $datetime = getdate($value);
        $this->addElement(new XoopsFormTextDateSelect('', $name, $size, $value));
    }
}
