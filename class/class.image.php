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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * An Image
 *
 * @package       kernel
 * @author        Kazumi Ono  <onokazu@xoops.org>
 * @copyright (c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsImage extends XoopsObject
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

/**
 * XOOPS image handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS image class objects.
 *
 * @package       kernel
 *
 * @author        Kazumi Ono  <onokazu@xoops.org>
 * @copyright (c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsImageHandler extends XoopsObjectHandler
{
    /**
     * Create a new {@link XoopsImage}
     *
     * @param   boolean $isNew Flag the object as "new"
     * @return  XoopsImage
     **/
    public function &create($isNew = true)
    {
        $image = new XoopsImage();
        if ($isNew) {
            $image->setNew();
        }

        return $image;
    }

    /**
     * Write a {@link XoopsImage} object to the database
     *
     * @param XoopsObject $image
     * @param string      $itemid
     * @return bool
     */
    public function insert(XoopsObject $image, $itemid = '0')
    {
        global $image_name;
        if ('0' == $itemid) {
            return false;
        }
        //if (strtolower(get_class($image)) != 'xoopsimage') {
        //    return false;
        //}
        if (!$image->isDirty()) {
            return true;
        }
        if (!$image->cleanVars()) {
            return false;
        }
        foreach ($image->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($image->isNew()) {
            $image_id = $this->db->genId('image_image_id_seq');
            $sql      = sprintf('INSERT INTO %s WHERE itemid=' . $itemid . ' (logourl) VALUES (%s)', $this->db->prefix($efqdirectory->getDirname() . '_items'), $this->db->quoteString($image_name));
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            if (empty($image_id)) {
                $image_id = $this->db->getInsertId();
            }
            /*             if (isset($image_body) && $image_body != '') {
            $sql = sprintf("INSERT INTO %s (image_id, image_body) VALUES (%u, %s)", $this->db->prefix('imagebody'), $image_id, $this->db->quoteString($image_body));
            if (!$result = $this->db->query($sql)) {
            $sql = sprintf("DELETE FROM %s WHERE image_id = %u", $this->db->prefix('image'), $image_id);
            $this->db->query($sql);

            return false;
            }
            } */
            /* $image->assignVar('image_id', $image_id); */
        } else {
            $sql = sprintf('UPDATE %s SET image_name = %s WHERE itemid = %u', $this->db->prefix($efqdirectory->getDirname() . '_items'), $this->db->quoteString($image_name));
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            /*             if (isset($image_body) && $image_body != '') {
            $sql = sprintf("UPDATE %s SET image_body = %s WHERE image_id = %u", $this->db->prefix('imagebody'), $this->db->quoteString($image_body), $image_id);
            if (!$result = $this->db->query($sql)) {
            $this->db->query(sprintf("DELETE FROM %s WHERE image_id = %u", $this->db->prefix('image'), $image_id));

            return false;
            }
            } */
        }

        return true;
    }

    /**
     * Delete an image from the database
     *
     * @param XoopsObject $image
     * @return bool
     */
    public function delete(XoopsObject $image)
    {
        if ('xoopsimage' !== strtolower(get_class($image))) {
            return false;
        }
        $id  = $image->getVar('image_id');
        $sql = sprintf('DELETE FROM %s WHERE image_id = %u', $this->db->prefix('image'), $id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE image_id = %u', $this->db->prefix('imagebody'), $id);
        $this->db->query($sql);

        return true;
    }

    /**
     * Load {@link XoopsImage}s from the database
     *
     * @param           $criteria  {@link CriteriaElement}
     * @param   boolean $id_as_key Use the ID as key into the array
     * @param   boolean $getbinary
     * @return  array   Array of {@link XoopsImage} objects
     **/
    public function &getObjects(CriteriaElement $criteria = null, $id_as_key = false, $getbinary = false)
    {
        $ret   = [];
        $limit = $start = 0;
        if ($getbinary) {
            $sql = 'SELECT i.*, b.image_body FROM ' . $this->db->prefix('image') . ' i LEFT JOIN ' . $this->db->prefix('imagebody') . ' b ON b.image_id=i.image_id';
        } else {
            $sql = 'SELECT * FROM ' . $this->db->prefix('image');
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql   .= ' ' . $criteria->renderWhere();
            $sort  = !in_array($criteria->getSort(), ['image_id', 'image_created', 'image_mimetype', 'image_display', 'image_weight']) ? 'image_weight' : $criteria->getSort();
            $sql   .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $image = new XoopsImage();
            $image->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $image;
            } else {
                $ret[$myrow['image_id']] =& $image;
            }
            unset($image);
        }

        return $ret;
    }

    /**
     * Count some images
     *
     * @param   $criteria {@link CriteriaElement}
     * @return  int
     **/
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('image');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Get a list of images
     *
     * @param   int  $imgcat_id
     * @param   bool $image_display
     * @return  array   Array of {@link XoopsImage} objects
     **/
    public function &getList($imgcat_id, $image_display = null)
    {
        $criteria = new CriteriaCompo(new Criteria('imgcat_id', (int)$imgcat_id));
        if (isset($image_display)) {
            $criteria->add(new Criteria('image_display', (int)$image_display));
        }
        $images = $this->getObjects($criteria, false, true);
        $ret    = [];
        foreach (array_keys($images) as $i) {
            $ret[$images[$i]->getVar('image_name')] = $images[$i]->getVar('image_nicename');
        }

        return $ret;
    }
}
