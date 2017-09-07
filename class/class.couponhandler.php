<?php

/*
// ID: couponhandler.php 5-jul-2007 18:46:54 Demo efqconsultancy
//  ----------------------------------------------------------------------- //
//                    EFQ Web                          //
//                    Copyright (c) 2006 EFQ Consultancy               //
//                       <http://www.efqweb.com>                      //
//  ----------------------------------------------------------------------- //
// You may distribute this software under the terms of EFQ Consultany's  //
// modified Artistic Licence, as specified in the accompanying    //
// LICENCE.txt file.              //
--------------------------------------------------------------------------- //
*/

class efqCouponHandler
{
    public $db;
    public $coupon = [];
    public $couponid;
    public $descr;
    public $itemid;
    public $publish;
    public $expire;
    public $heading;
    public $message;
    public $lbr;
    public $_new   = false;

    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }

    public function create()
    {
        global $myts;
        $this->descr   = $myts->makeTareaData4Save($_POST['description']);
        $this->image   = $myts->makeTboxData4Save($_POST['image']);
        $this->itemid  = (int)$_POST['itemid'];
        $this->publish = strtotime($_POST['publish']['date']) + $_POST['publish']['time'];
        if (isset($_POST['expire_enable']) && ($_POST['expire_enable'] == 1)) {
            $this->expire = strtotime($_POST['expire']['date']) + $_POST['expire']['time'];
        } else {
            $this->expire = 0;
        }
        $this->lbr     = $_POST['lbr'];
        $this->heading = $myts->makeTboxData4Save($_POST['heading']);
        if (!isset($_POST['couponid'])) {
            $this->_new    = true;
            $this->message = _MD_COUPONADDED;
            if ($this->insert()) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->message  = _MD_COUPONUPDATED;
            $this->couponid = (int)$_POST['couponid'];
            if ($this->update()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function insert()
    {
        $sql = 'INSERT INTO ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . '
            (itemid, description, image, publish, expire, heading, lbr) VALUES
            (' . $this->itemid . ', ' . $this->db->quoteString($this->descr) . ', ' . $this->db->quoteString($this->image) . ', ' . $this->publish . ', ' . $this->expire . ', ' . $this->db->quoteString($this->heading) . ', ' . $this->lbr . ')';
        if ($this->db->query($sql)) {
            $this->couponid = $this->db->getInsertId();

            return true;
        }

        return false;
    }

    public function update()
    {
        $sql = 'UPDATE ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' SET
            description = ' . $this->db->quoteString($this->descr) . ',
            image = ' . $this->db->quoteString($this->image) . ',
            publish = ' . $this->publish . ',
            lbr = ' . $this->lbr . ',
            heading = ' . $this->db->quoteString($this->heading) . ",
            expire = $this->expire WHERE couponid = $this->couponid";
        $this->db->query($sql);

        return true;
    }

    public function get($couponid = false)
    {
        if ($couponid === false) {
            //echo 'couponid is false';
            return false;
        }
        //$couponid = (int)($couponid);
        if ($couponid > 0) {
            $sql = 'SELECT itemid, description, image, publish, expire, heading, lbr FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' WHERE couponid=' . $couponid;
            //echo $sql;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            while (list($itemid, $descr, $image, $publish, $expire, $heading, $lbr) = $this->db->fetchRow($result)) {
                $this->itemid  = $itemid;
                $this->descr   = $descr;
                $this->image   = $image;
                $this->publish = $publish;
                $this->expire  = $expire;
                $this->heading = $heading;
                $this->lbr     = $lbr;
            }

            return true;
        }

        return false;
    }

    public function delete($couponid)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' WHERE couponid=' . (int)$couponid;
        $this->db->query($sql);

        return true;
    }

    /* Returns number of coupons for a listing
    *
    * @param int $itemid listing id
    *
    * @return
    */
    public function getCountByLink($itemid = 0)
    {
        $ret = 0;
        $now = time();
        $sql = 'SELECT count(*) FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' WHERE itemid=' . $itemid . ' AND publish < ' . $now . ' AND (expire = 0 OR expire > ' . $now . ')';
        //echo $sql;
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        list($ret) = $this->db->fetchRow($result);

        return $ret;
    }

    public function getByItem($itemid = 0)
    {
        if ($itemid === false) {
            //echo 'couponid is false';
            return false;
        }
        //$couponid = (int)($couponid);
        if ($itemid > 0) {
            $sql = 'SELECT couponid, itemid, description, image, publish, expire, heading, lbr FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_coupon') . ' WHERE itemid=' . $itemid;
            //echo $sql;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            while (list($couponid, $itemid, $descr, $image, $publish, $expire, $heading, $lbr) = $this->db->fetchRow($result)) {
                if ($publish == 0) {
                    $publish = time();
                }
                if ($expire > 0) {
                    $expire = formatTimestamp($expire, 's');
                }
                $publish = formatTimestamp($publish, 's');
                $ret[]   = ['couponid' => $couponid, 'itemid' => $itemid, 'descr' => $descr, 'image' => $image, 'publish' => $publish, 'expire' => $expire, 'heading' => $heading, 'lbr' => $lbr];
            }

            return $ret;
        }

        return false;
    }
}
