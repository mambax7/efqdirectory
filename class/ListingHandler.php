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

/**
 * Class ListingHandler
 * Manages database operations for listings
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */
class ListingHandler extends \XoopsObjectHandler
{
    public function __construct()
    {
        //Instantiate class
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
    }

    /**
     * Function updateStatus updates status for listing
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param   int      $itemid    - Default: 0 - Listing to be updated
     * @param int|string $newstatus - Default: '1' - New status for listing
     * @return bool true if update is succesful, false if unsuccesful
     */
    public function updateStatus($itemid = 0, $newstatus = '1')
    {
        $helper = Efqdirectory\Helper::getInstance();
        $sql = 'UPDATE ' . $this->db->prefix($helper->getDirname() . '_items') . ' SET status = ' . $newstatus . ' WHERE itemid = ' . (int)$itemid . '';
        if ($this->db->query($sql)) {
            return true;
        }

        return false;
    }

    /**
     * Function incrementHits increments hits for listing with 1
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param   int $itemid - Default: 0 - Listing to be updated
     *
     * @return bool true if update is succesful, false if unsuccesful
     */
    public function incrementHits($itemid = 0)
    {
        $helper = Efqdirectory\Helper::getInstance();
        $sql = sprintf('UPDATE %s SET hits = hits+1 WHERE itemid = %u AND STATUS = 2', $this->db->prefix($helper->getDirname() . '_items'), (int)$itemid);
        if ($this->db->queryF($sql)) {
            return true;
        }

        return false;
    }

    /**
     * Function getLinkedCatsArray gets categories linked to a listing.
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param int|string $itemid - Default: '0' - Listing ID
     * @param int|string $dirid  - Default: '0' - Directory ID
     * @return array $arr Array with category ID's
     */
    public function getLinkedCatsArray($itemid = '0', $dirid = '0')
    {
        $helper = Efqdirectory\Helper::getInstance();
        $sql     = 'SELECT c.cid, x.active FROM '
                   . $this->db->prefix($helper->getDirname() . '_cat')
                   . ' c, '
                   . $this->db->prefix($helper->getDirname() . '_item_x_cat')
                   . ' x WHERE c.cid=x.cid AND x.itemid='
                   . (int)$itemid
                   . " AND c.dirid='"
                   . (int)$dirid
                   . '\' AND c.active=\'1\'';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $arr     = [];
        if ($numrows > 0) {
            while (false !== (list($cid, $active) = $this->db->fetchRow($result))) {
                $arr[] = $cid;
            }
        }

        return $arr;
    }

    /**
     * Function getListing gets listing from DB as an array.
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param   int $itemid - Default: '0' - Listing ID
     *
     * @return array $arr Array with listing fields and values
     */
    public function getListing($itemid)
    {
        $helper = Efqdirectory\Helper::getInstance();
        $sql    = 'SELECT i.*, t.description FROM ' . $this->db->prefix($helper->getDirname() . '_items') . ' i LEFT JOIN ' . $this->db->prefix($helper->getDirname() . '_item_text') . ' t ON (i.itemid=t.itemid) WHERE i.itemid=' . (int)$itemid;
        $result = $this->db->query($sql);
        $arr    = [];
        if (!$result) {
            return $arr;
        } else {
            $numrows = $this->db->getRowsNum($result);
            if (0 == $numrows) {
                return $arr;
            } else {
                $arr = $this->db->fetchArray($result);
            }
        }

        return $arr;
    }

    /**
     * @param $itemid
     * @return array
     */
    public function getDataTypes($itemid)
    {
        global $datafieldmanager;
        $helper = Efqdirectory\Helper::getInstance();
        $sql     = 'SELECT DISTINCT t.dtypeid, t.title, t.section, t.icon, f.typeid, f.fieldtype, f.ext, t.options, t.custom, d.itemid, d.value, d.customtitle ';
        $sql     .= 'FROM '
                    . $this->db->prefix($helper->getDirname() . '_item_x_cat')
                    . ' ic, '
                    . $this->db->prefix($helper->getDirname() . '_dtypes_x_cat')
                    . ' xc, '
                    . $this->db->prefix($helper->getDirname() . '_fieldtypes')
                    . ' f, '
                    . $this->db->prefix($helper->getDirname() . '_dtypes')
                    . ' t ';
        $sql     .= 'LEFT JOIN ' . $this->db->prefix($helper->getDirname() . '_data') . ' d ON (t.dtypeid=d.dtypeid AND d.itemid=' . (int)$itemid . ') ';
        $sql     .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=" . (int)$itemid . ' ORDER BY t.seq ASC';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        //$arr = $this->db->fetchArray($result);
        $arr = [];
        while (false !== (list($dtypeid, $title, $section, $icon, $ftypeid, $fieldtype, $ext, $options, $custom, $itemid, $value, $customtitle) = $this->db->fetchRow($result))) {
            $fieldvalue = $datafieldmanager->getFieldValue($fieldtype, $options, $value);
            if ('' != $icon) {
                $iconurl = "<img src=\"uploads/$icon\">";
            } else {
                $iconurl = '';
            }
            if ('0' != $custom && '' != $customtitle) {
                $title = $customtitle;
            }
            $arr[] = [
                'dtypeid'     => $dtypeid,
                'title'       => $title,
                'section'     => $section,
                'icon'        => $iconurl,
                'ftypeid'     => $ftypeid,
                'fieldtype'   => $fieldtype,
                'ext'         => $ext,
                'options'     => $options,
                'custom'      => $custom,
                'itemid'      => $itemid,
                'value'       => $fieldvalue,
                'customtitle' => $customtitle
            ];
        }

        return $arr;
    }
}
