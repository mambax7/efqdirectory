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
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package      efqdirectory
 * @since
 * @author       Martijn Hertog (aka wtravel)
 * @author       XOOPS Development Team,
 */

use XoopsModules\Efqdirectory;

if (!class_exists('Coupon')) {
    /**
     * Class Coupon
     */
    class Coupon extends \XoopsObject
    {
        //Constructor
        /**
         * @param mixed $coupid int for coupon id or array with name->value pairs of properties
         */
        public function __construct($coupid = false)
        {
            global $moddir;
            $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
            $this->initVar('couponid', XOBJ_DTYPE_INT, null, false);
            $this->initVar('itemid', XOBJ_DTYPE_INT, null, true);
            $this->initVar('description', XOBJ_DTYPE_TXTAREA);
            $this->initVar('image', XOBJ_DTYPE_TXTBOX);
            $this->initVar('publish', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('expire', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('heading', XOBJ_DTYPE_TXTBOX);
            $this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('lbr', XOBJ_DTYPE_INT, 0, false);
            if (false !== $coupid) {
                if (is_array($coupid)) {
                    $this->assignVars($coupid);
                } else {
                    $couponHandler = Efqdirectory\Helper::getInstance()->getHandler('Coupon');
                    $coupon        = $couponHandler->get($coupid);
                    foreach ($coupon->vars as $k => $v) {
                        $this->assignVar($k, $v['value']);
                    }
                    unset($coupon);
                }
            }
        }

        /**
         * @return array
         */
        public function toArray()
        {
            $ret = [];
            foreach ($this->vars as $k => $v) {
                $ret[$k] = $v['value'];
            }

            return $ret;
        }
    }
}
