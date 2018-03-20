<?php namespace XoopsModules\Efqdirectory;

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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * An Image
 *
 * @package       kernel
 * @author        Kazumi Ono  <onokazu@xoops.org>
 * @copyright (c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsImage extends \XoopsObject
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
        $this->initVar('image_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('image_name', XOBJ_DTYPE_OTHER, null, false, 30);
        $this->initVar('image_nicename', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('image_mimetype', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('image_created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('image_display', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('image_weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('image_body', XOBJ_DTYPE_SOURCE, null, true);
        $this->initVar('imgcat_id', XOBJ_DTYPE_INT, 0, false);
    }
}
