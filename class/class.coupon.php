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

if (!class_exists('Coupon')) {
    class Coupon extends XoopsObject
    {
        //Constructor
        /**
         * @param mixed $coupid int for coupon id or array with name->value pairs of properties
         */
        public function __construct($coupid = false)
        {
            global $moddir;
            $this->db = XoopsDatabaseFactory::getDatabaseConnection();
            $this->initVar('couponid', XOBJ_DTYPE_INT, null, false);
            $this->initVar('itemid', XOBJ_DTYPE_INT, null, true);
            $this->initVar('description', XOBJ_DTYPE_TXTAREA);
            $this->initVar('image', XOBJ_DTYPE_TXTBOX);
            $this->initVar('publish', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('expire', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('heading', XOBJ_DTYPE_TXTBOX);
            $this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('lbr', XOBJ_DTYPE_INT, 0, false);
            if ($coupid !== false) {
                if (is_array($coupid)) {
                    $this->assignVars($coupid);
                } else {
                    $couponHandler = xoops_getModuleHandler('coupon', $moddir);
                    $coupon        = $couponHandler->get($coupid);
                    foreach ($coupon->vars as $k => $v) {
                        $this->assignVar($k, $v['value']);
                    }
                    unset($coupon);
                }
            }
        }

        public function toArray()
        {
            $ret = array();
            foreach ($this->vars as $k => $v) {
                $ret[$k] = $v['value'];
            }

            return $ret;
        }
    }
}

// Change the class name below to enable custom directory (Capitolize first letter YourdirectoryCouponHandler)
class efqdirectoryCouponHandler extends XoopsObjectHandler
{
    /**
     * create a new coupon object
     *
     * @param bool $isNew flag the new objects as "new"?
     * @return object {@link Coupon}
     */
    //var $coupon;
    //var $db;

    public function &create($isNew = true)
    {
        //$this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $coupon = new Coupon();
        if ($isNew) {
            $coupon->setNew();
        }

        return $coupon;
    }

    /**
     * retrieve a coupon
     *
     * @param bool|int $coupid ID of the coupon
     * @return mixed reference to the <a href='psi_element://Coupon'>Coupon</a> object, FALSE if failed
     *                         object, FALSE if failed
     */
    public function &get($coupid = false)
    {
        if ($coupid === false) {
            return false;
        }
        $coupid = (int)$coupid;
        if ($coupid > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' WHERE couponid=' . $coupid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $coupon =& $this->create(false);
            $coupon->assignVars($this->db->fetchArray($result));

            return $coupon;
        }

        return false;
    }

    /**
     * Save coupon in database
     * @param object|XoopsObject $coupon reference to the {@link Coupon}
     *                                   object
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     * @internal param bool $force
     */
    public function insert(XoopsObject $coupon)
    {
        global $eh, $xoopsDB, $description, $image, $heading, $couponid;
        if (get_class($coupon) != 'Coupon') {
            echo ' class not coupon ';

            return false;
        }
        if (!$coupon->isDirty()) {
            echo ' coupon not dirty ';

            return true;
        }
        if (!$coupon->cleanVars()) {
            echo ' coupon not cleanvars ';

            return false;
        }
        foreach ($coupon->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($coupon->_isNew) {
            $sql = 'INSERT INTO ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . "
                (itemid, description, image, publish, expire, heading, lbr) VALUES
                ($itemid, " . $this->db->quoteString($description) . ', ' . $this->db->quoteString($image) . ", $publish, $expire, " . $this->db->quoteString($heading) . ", $lbr)";
        } else {
            $sql = 'UPDATE ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . " SET
                itemid = $itemid,
                description = " . $this->db->quoteString($description) . ',
                image = ' . $this->db->quoteString($image) . ",
                publish = $publish,
                lbr = $lbr,
                heading = " . $this->db->quoteString($heading) . ",
                expire = $expire WHERE couponid = " . $couponid;
        }
        $xoopsDB->query($sql) or $eh->show('0013');
        if ($coupon->_isNew) {
            $coupon->setVar('couponid', $this->db->getInsertId());
            $coupon->_isNew = false;
        }

        return true;
    }

    /**
     * delete a coupon from the database
     *
     * @param object|XoopsObject $coupon reference to the {@link Coupon}
     *                                   to delete
     * @return bool FALSE if failed.
     * @internal param bool $force
     */
    public function delete(XoopsObject $coupon)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' WHERE couponid = ' . (int)$coupon->getVar('couponid');
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * get {@link Coupon} objects from criteria
     *
     * @param object $criteria   reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool   $as_objects if true, the returned array will be {@link Coupon} objects
     * @param bool   $id_as_key  if true, the returned array will have the coupon ids as key
     *
     * @return array array of {@link Coupon} objects
     */
    public function &getObjects($criteria = null, $as_objects = true, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT l.title AS listingTitle, coup.couponid, coup.heading, coup.counter, l.itemid, l.logourl, coup.description, coup.image, coup.lbr, coup.publish, coup.expire
            FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' coup, ' . $this->db->prefix($module->getVar('dirname', 'n') . '_items') . ' l
            WHERE coup.itemid=l.itemid AND ';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->render();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            if ($as_objects) {
                $coupon = new Coupon();
                $coupon->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $coupon;
                } else {
                    $ret[$myrow['couponid']] =& $coupon;
                }
                unset($coupon);
            } else {
                $ret[] = $myrow;
            }
        }

        return $ret;
    }

    /**
     * get {@link Coupon} objects by listing
     *
     * @param int $itemid listing id
     *
     * @return array array of {@link Coupon} objects
     */
    public function &getByLink($itemid)
    {
        global $eh;
        $ret    = array();
        $limit  = $start = 0;
        $now    = time();
        $sql    = 'SELECT l.title AS listingTitle, coup.couponid, coup.heading, coup.counter, l.itemid, l.logourl, coup.description, coup.image, coup.lbr, coup.publish, coup.expire
            FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' coup, ' . $this->db->prefix($module->getVar('dirname', 'n') . '_items') . ' l';
        $sql    .= ' WHERE coup.itemid = l.itemid AND coup.itemid=' . (int)$itemid . ' AND coup.publish < ' . $now . ' AND (coup.expire = 0 OR coup.expire > ' . $now . ')';
        $sql    .= ' ORDER BY listingTitle ASC';
        $result = $this->db->query($sql, $limit, $start) or $eh->show('0013');

        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow;
        }

        return $ret;
    }

    /**
     * Returns number of coupons for a listing
     *
     * @param int $itemid listing id
     * @return bool|int
     */
    public function getCountByLink($itemid)
    {
        $ret = 0;
        $now = time();
        $sql = 'SELECT count(*) FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' WHERE itemid=' . (int)$itemid . ' AND publish < ' . $now . ' AND (expire = 0 OR expire > ' . $now . ')';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        list($ret) = $this->db->fetchRow($result);

        return $ret;
    }

    /**
     * get {@link Coupon} object with listing info
     *
     * @param int $coupid Coupon ID
     *
     * @return array|object
     */
    public function &getLinkedCoupon($coupid)
    {
        $ret    = array();
        $limit  = $start = 0;
        $now    = time();
        $sql    = 'SELECT l.title AS listingTitle, coup.couponid, coup.heading, coup.counter, l.itemid, l.logourl, coup.description,  coup.image, coup.lbr, coup.publish, coup.expire
            FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' coup, ' . $this->db->prefix($module->getVar('dirname', 'n') . '_items') . ' l';
        $sql    .= ' WHERE coup.itemid = l.itemid AND coup.couponid=' . (int)$coupid;
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow;
        }

        return $ret;
    }

    /**
     * Prepares rows from getByLink and getByCategory to be displayed
     *
     * @param array $array rows to be prepared
     *
     * @return array
     */
    public function prepare2show($array)
    {
        $myts = MyTextSanitizer::getInstance();
        $ret  = array();
        foreach ($array as $key => $myrow) {
            $description = $myts->displayTarea($myrow['description'], 1, 1, 1, 1, $myrow['lbr']);
            if ($myrow['expire'] > 0) {
                $expire = formatTimestamp($myrow['expire'], 's');
            } else {
                $expire = 0;
            }
            $ret ['items']['coupons'][]    = array(
                'itemid'      => $myrow['itemid'],
                'couponid'    => $myrow['couponid'],
                'heading'     => $myts->htmlSpecialChars($myrow['heading']),
                'description' => $description,
                'logourl'     => $myts->displayTarea($myrow['logourl']),
                'publish'     => formatTimestamp($myrow['publish'], 's'),
                'expire'      => $expire,
                'counter'     => (int)$myrow['counter']
            );
            $ret ['items']['listingTitle'] = $myts->displayTarea($myrow['listingTitle']);
            $ret ['items']['itemid']       = $myrow['itemid'];
        }

        return $ret;
    }

    /**
     * Increment coupon counter
     *
     * @param int $couponid
     *
     * @return bool
     */
    public function increment($couponid)
    {
        $sql = 'UPDATE ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' SET counter=counter+1 WHERE couponid=' . (int)$couponid;

        return $this->db->queryF($sql);
    }
}
