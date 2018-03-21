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
 * Class SubscriptionHandler
 */
class SubscriptionHandler extends \XoopsObjectHandler
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        //Instantiate class
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
    }

    /**
     * Function delete: Delete subscription order
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param int|string|\XoopsObject $orderid - Default: '0' - Order ID
     * @return array|bool
     */
    public function delete(\XoopsObject $orderid = null)
    {
        if (null !== $orderid) {
            $sql = 'DELETE FROM ' . $this->db->prefix($helper->getDirname() . '_subscr_orders') . ' WHERE orderid=' . (int)$orderid . '';
            $this->db->query($sql);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Function createOrder: Create subscription order
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param   int $itemid - Default: '0' - Item ID
     * @return int $orderid - Newly created order id
     */
    public function createOrder($itemid = 0)
    {
        /** @var Efqdirectory\Helper $helper */
        $helper = Efqdirectory\Helper::getInstance();
        $orderid = 0;
        if (0 != $itemid) {
            if (isset($_POST['typeofferid'])) {
                $typeofferid = explode('_', $_POST['typeofferid']);
                $typeid      = $typeofferid[0];
                $offerid     = $typeofferid[1];
            } else {
                return false;
            }
            $submitter = (int)$_POST['uid'];
            $startdate = strtotime($_POST['startdate']);
            //TO DO: Add Auto renew functionality.
            //$autorenew = $_POST['autorenew'];
            $newid = $this->db->genId($this->db->prefix($helper->getDirname() . '_subscr_orders') . '_orderid_seq');
            $sql   = 'INSERT INTO ' . $this->db->prefix($helper->getDirname() . '_subscr_orders') . "
                (orderid, uid, offerid, typeid, startdate, status, itemid, autorenew) VALUES
                ($newid, $submitter, $offerid, $typeid, '$startdate', '0' , $itemid, '0')";
            $this->db->query($sql);
            if (0 == $newid) {
                $orderid = $this->db->getInsertId();
            }
        }

        return $orderid;
    }

    /*function renewOrder($itemid = '0', $orderid='0') {
    //Renew order
    global $xoopsDB, $eh;
    //$orderid = '0';
    if ($itemid != '0') {
    //Billto date needs to be updated, will be done on succesful payment;
    //Payment form needs to be created

    if ( isset( $_POST['typeofferid'] ) ) {
    $typeofferid =explode("_",$_POST['typeofferid']);
    $typeid = $typeofferid[0];
    $offerid = $typeofferid[1];
    } else {
    return false;
    }
    $submitter = $_POST['uid'];
    $startdate = strtotime($_POST['startdate']);
    //TO DO: Add Auto renew functionality.
    //$autorenew = $_POST['autorenew'];
    $newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_subscr_orders")."_orderid_seq");
    $sql = "INSERT INTO ".$xoopsDB->prefix("efqdiralpha1_subscr_orders")."
    (orderid, uid, offerid, typeid, startdate, status, itemid, autorenew) VALUES
    ($newid, $submitter, $offerid, $typeid, '$startdate', '0' , $itemid, '0')";
    $xoopsDB->query($sql) || $eh->show("0013");
    if ($newid == 0) {
    $orderid = $xoopsDB->getInsertId();
    }
    }

    return $orderid;
    }*/


    /**
     * Function durationArray: creates array of options for duration selbox:
     * months, weeks, year, days etc.
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @return array $arr
     */
    public function durationArray()
    {
        $arr = ['0' => '---', '1' => _MD_DAYS, '2' => _MD_WEEKS, '3' => _MD_MONTHS, '4' => _MD_QUARTERS, '5' => _MD_YEARS];

        return $arr;
    }

    /**
     * Function durationArray: creates array of options for duration selbox:
     * single items like: month, week, year, day etc.
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @return array $arr
     */
    public function durationSingleArray()
    {
        $arr = ['0' => '---', '1' => _MD_DAY, '2' => _MD_WEEK, '3' => _MD_MONTH, '4' => _MD_QUARTER, '5' => _MD_YEAR];

        return $arr;
    }


    /**
     * @param int $offerid
     * @return string
     */
    public function getOrderItemName($offerid = 0)
    {
        $sql     = 'SELECT o.offerid, o.duration, o.count, o.price, o.currency, o.descr, t.typeid, t.typename FROM '
                   . $this->db->prefix($helper->getDirname() . '_subscr_offers')
                   . ' o, '
                   . $this->db->prefix($helper->getDirname() . '_itemtypes')
                   . " t WHERE o.typeid=t.typeid AND o.offerid='$offerid' ORDER BY t.typename ASC";
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $result  = $this->db->query($sql);
        while (false !== (list($offerid, $duration, $count, $price, $currency, $descr, $typeid, $typename) = $this->db->fetchRow($result))) {
            if ('1' == $count) {
                $duration_arr = $this->durationSingleArray();
            } else {
                $duration_arr = $this->durationArray();
            }
            $durationname = $duration_arr['' . $duration . ''];
            $itemname     = $typename . ' - ' . $count . ' ' . $durationname . ' - ' . $price . ' ' . $currency;
        }

        return $itemname;
    }

    /**
     * @param string $orderid
     * @param string $status
     * @param string $startdate
     * @param string $billto
     */
    public function updateOrder($orderid = '0', $status = '1', $startdate = '0', $billto = '0')
    {
        $ordervars = $this->getOrderVars($orderid);
        $typeid    = $ordervars['typeid'];
        $itemid    = $ordervars['itemid'];
        $sql       = 'UPDATE ' . $this->db->prefix($helper->getDirname() . '_subscr_orders') . " SET status='$status'";
        if ('0' != $startdate) {
            $sql .= ", startdate='$startdate'";
        }
        if ('0' != $billto) {
            $sql .= ", billto='$billto'";
        }
        $sql .= ' WHERE orderid=' . (int)$orderid . '';
        $this->db->queryF($sql);
        if ($startdate > time()) {
            $this->updateScheduler('add', $itemid, $typeid, $startdate);
        } else {
            $this->updateItem($itemid, $typeid);
        }
    }

    /**
     * @param string $orderid
     * @return mixed
     */
    public function getOrderVars($orderid = '0')
    {
        $sql     = 'SELECT ord.uid, ord.billto, ord.startdate, ord.typeid, ord.status, ord.itemid, ord.offerid FROM ' . $this->db->prefix($helper->getDirname() . '_subscr_orders') . ' ord WHERE ord.orderid=' . (int)$orderid . '';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $arr     = $this->db->fetchArray($result);
        while (false !== (list($uid, $billto, $startdate, $typeid, $status, $itemid, $offerid) = $this->db->fetchRow($result))) {
            $arr['uid']       = $uid;
            $arr['billto']    = $billto;
            $arr['startdate'] = $startdate;
            $arr['typeid']    = $typeid;
            $arr['status']    = $status;
            $arr['itemid']    = $itemid;
            $arr['offerid']   = $offerid;
        }

        return $arr;
    }

    /**
     * @param string $offerid
     * @param string $showactive
     * @return array
     */
    public function getOfferVars($offerid = '0', $showactive = '1')
    {
        $sql = 'SELECT count, duration FROM ' . $this->db->prefix($helper->getDirname() . '_subscr_offers') . ' WHERE offerid=' . (int)$offerid . '';
        if ('1' == $showactive) {
            $sql .= " AND activeyn='1'";
        }
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $arr     = [];
        $arr     = $this->db->fetchArray($result);
        while (false !== (list($count, $duration) = $this->db->fetchRow($result))) {
            $arr['count']    = $count;
            $arr['duration'] = $duration;
        }

        return $arr;
    }

    /* function updateScheduler( $func='add', $itemid='0', $typeid='0', $startdate='0' ) {
    global $xoopsDB, $eh;
    if ($func='add') {
    $newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_subscr_scheduler")."_id_seq");
    $sql = "INSERT INTO ".$xoopsDB->prefix("efqdiralpha1_subscr_scheduler")."
    (id, startdate, itemid, newtypeid, status) VALUES
    ($newid, $startdate, $itemid, $typeid, '0')";
    $xoopsDB->queryF($sql) || $eh->show("0013");
    }
    }*/

    /**
     * @param string $itemid
     * @param string $typeid
     * @return bool
     */
    public function updateItem($itemid = '0', $typeid = '0')
    {
        if ('0' != $itemid && '0' != $typeid) {
            $sql = 'UPDATE ' . $this->db->prefix($helper->getDirname() . '_items') . " SET typeid='" . (int)$typeid . '\' WHERE itemid=' . (int)$itemid . '';
            $this->db->queryF($sql);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $offerid
     */
    public function getNewBillto($offerid = '0')
    {
    }

    /**
     * @param int $itemid
     * @param int $itemtype
     * @return bool
     */
    public function changeItemType($itemid = 0, $itemtype = 0)
    {
        global $xoopsDB;
        $sql = 'UPDATE ' . $this->db->prefix($helper->getDirname() . '_items') . " SET typeid=$itemtype WHERE itemid=(int)($itemid)";
        $this->db->queryF($sql);

        return true;
    }

    /**
     * @param string $dashes
     * @param string $showactive
     * @return array
     */
    public function durationPriceArray($dashes = '0', $showactive = '1')
    {
        $helper = Efqdirectory\Helper::getInstance();
        $sql = 'SELECT o.offerid, o.duration, o.count, o.price, o.currency, o.descr, t.typeid, t.typename FROM ' . $this->db->prefix($helper->getDirname() . '_subscr_offers') . ' o, ' . $this->db->prefix($helper->getDirname() . '_itemtypes') . ' t WHERE o.typeid=t.typeid';
        if ('1' == $showactive) {
            $sql .= " AND activeyn='1'";
        }
        $sql     .= ' ORDER BY t.typelevel ASC, t.typename ASC';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        if ('0' == $dashes) {
            $arr = ['0' => '---'];
        }
        while (false !== (list($offerid, $duration, $count, $price, $currency, $descr, $typeid, $typename) = $this->db->fetchRow($result))) {
            if ('1' == $count) {
                $duration_arr = $this->durationSingleArray();
            } else {
                $duration_arr = $this->durationArray();
            }
            $durationname                  = $duration_arr['' . $duration . ''];
            $arr[$typeid . '_' . $offerid] = $typename . '&nbsp;-&nbsp;' . $count . '&nbsp;' . $durationname . '&nbsp;-&nbsp;' . $price . '&nbsp;' . $currency;
        }

        return $arr;
    }

    /**
     * @param string $selname
     * @param bool   $none
     * @param int    $preselected
     */
    public function itemsSelBox($selname = '', $none = false, $preselected = 0)
    {
        $sql     = 'SELECT typeid, typename FROM ' . $this->db->prefix($helper->getDirname() . '_itemtypes') . '';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        echo "<select name='" . $selname . '\'';
        echo ">\n";
        $result = $this->db->query($sql);
        if ($none) {
            echo "<option value='0'>----</option>\n";
        }
        while (false !== (list($typeid, $typename) = $this->db->fetchRow($result))) {
            if ($preselected == $typeid) {
                $sel = '&nbsp;selected';
            } else {
                $sel = '';
            }
            echo "<option value='$typeid'>$typename</option>\n";
        }
        echo "</select>\n";
    }

    /**
     * @param string $dashes
     * @return array
     */
    public function itemTypesArray($dashes = '0')
    {
        global $xoopsModule;
        $helper = Efqdirectory\Helper::getInstance();
        $sql     = 'SELECT typeid, typename FROM ' . $this->db->prefix($helper->getDirname() . '_itemtypes') . ' ORDER BY typelevel ASC';
        $result  = $this->db->query($sql) ; //|| $eh->show('0013');
        if (!$result) {
            $logger = \XoopsLogger::getInstance();
            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
        }
        $numrows = $this->db->getRowsNum($result);
        $result  = $this->db->query($sql);
        if ('0' == $dashes) {
            $arr = ['0' => '---'];
        }
        while (false !== (list($typeid, $typename) = $this->db->fetchRow($result))) {
            $arr[$typeid] = $typename;
        }

        return $arr;
    }

    /**
     * @param int $typeid
     * @return bool
     */
    public function countSubscriptionsForType($typeid = 0)
    {
        $sql = 'SELECT COUNT(itemid) FROM ' . $this->db->prefix($helper->getDirname() . '_items') . ' WHERE typeid=' . (int)$typeid . '';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        list($ret) = $this->db->fetchRow($result);

        return $ret;
    }
}
