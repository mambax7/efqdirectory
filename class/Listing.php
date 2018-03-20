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

/**
 * Class Listing
 * Manages operations for listings
 *
 * @package   efqDirectory
 * @author    EFQ Consultancy <info@efqconsultancy.com>
 * @copyright EFQ Consultancy (c) 2007
 * @version   1.1.0
 */

use XoopsModules\Efqdirectory;

class Listing extends \XoopsObject
{
    public $_editrights = false;
    public $_currentuser;
    public $_value      = [];
    public $_postdata   = [];
    public $_datatypes  = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // class constructor;
        $this->setCurrentUser();

        $this->initVar('itemid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('logourl', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('status', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('created', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('hits', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('rating', XOBJ_DTYPE_OTHER, 0.0, true);
        $this->initVar('votes', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('typeid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('dirid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false);
    }

    /**
     * Function setListingVars sets listing variables
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param   array $listing Array with listing details
     */
    public function setListingVars($listing = [])
    {
        if (is_array($listing)) {
            $this->setVar('itemid', $listing['itemid']);
            $this->setVar('logourl', $listing['logourl']);
            $this->setVar('uid', $listing['uid']);
            $this->setVar('status', $listing['status']);
            $this->setVar('created', $listing['created']);
            $this->setVar('title', $listing['title']);
            $this->setVar('hits', $listing['hits']);
            $this->setVar('rating', $listing['rating']);
            $this->setVar('votes', $listing['votes']);
            $this->setVar('typeid', $listing['typeid']);
            $this->setVar('dirid', $listing['dirid']);
            $this->setVar('description', $listing['description']);
        }
    }

    /**
     * @param array $arr
     */
    public function setDataTypes($arr = [])
    {
        $this->_datatypes = $arr;
    }

    /**
     * @return array
     */
    public function getDataTypes()
    {
        return $this->_datatypes;
    }

    public function setCurrentUser()
    {
        global $xoopsUser;
        $this->_currentuser = !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
    }

    /**
     * @param bool $value
     */
    public function setEditRights($value = false)
    {
        $this->_editrights = $value;
    }

    /**
     * @param $arr
     */
    public function addPostDataArray($arr)
    {
        $this->_postdata[] = $arr;
    }

    public function updateListing()
    {
        global $xoopsDB;
        // Save array _postdata into database as update;
    }

    public function insertListing()
    {
        global $xoopsDB;
        // Save array postdata into database as new record;
    }
}
