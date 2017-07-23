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

class efqSubscription extends XoopsObject
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        //Constructor
    }

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
    public static function durationArray()
    {
        $arr = array('0' => '---', '1' => _MD_DAYS, '2' => _MD_WEEKS, '3' => _MD_MONTHS, '4' => _MD_QUARTERS, '5' => _MD_YEARS);

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
        $arr = array('0' => '---', '1' => _MD_DAY, '2' => _MD_WEEK, '3' => _MD_MONTH, '4' => _MD_QUARTER, '5' => _MD_YEAR);

        return $arr;
    }

    /**
     * Function currencyArray: creates array of options for currency selbox
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @return array $arr
     */
    public function currencyArray()
    {
        //create array of options for duration selbox: months, weeks, year, days etc.
        $arr = array('0' => '---', 'USD' => _MD_CURR_USD, 'AUD' => _MD_CURR_AUD, 'EUR' => _MD_CURR_EUR, 'GBP' => _MD_CURR_GBP, 'YEN' => _MD_CURR_YEN);

        return $arr;
    }

    /**
     * Function notifyExpireWarning
     * Notify user of a subscription order that is about to expire.
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param int|string $orderid - Default: '0' - Order ID
     * @param int|string $userid  - Default: '0' - User ID
     */
    public function notifyExpireWarning($orderid = '0', $userid = '0')
    {
        global $xoopsConfig, $moddir;
        require_once XOOPS_ROOT_PATH . '/class/mail/xoopsmultimailer.php';

        $xoopsMailer = new XoopsMailer();
        $xoopsMailer->useMail();
        $template_dir = XOOPS_URL . '/modules/' . $moddir . '/language/' . $xoopsConfig['language'] . '/mail_template/';
        $template     = 'expirewarning.tpl';
        $subject      = _MD_LANG_EXPIREWARNING_SUBJECT;
        $xoopsMailer->setTemplateDir($template_dir);
        $xoopsMailer->setTemplate($template);
        $xoopsMailer->setToUsers($userid);
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject($subject);
        $success = $xoopsMailer->send();
    }
}

class efqSubscriptionHandler extends XoopsObjectHandler
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        //Instantiate class
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }

    /**
     * Function delete: Delete subscription order
     *
     * @author    EFQ Consultancy <info@efqconsultancy.com>
     * @copyright EFQ Consultancy (c) 2007
     * @version   1.0.0
     *
     * @param int|string|XoopsObject $orderid - Default: '0' - Order ID
     * @return array $arr
     */
    public function delete(XoopsObject $orderid = null)
    {
        if ($orderid !== null) {
            $sql = 'DELETE FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_subscr_orders') . ' WHERE orderid=' . (int)$orderid . '';
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
        $orderid = 0;
        if ($itemid != 0) {
            if (isset($_POST['typeofferid'])) {
                $typeofferid =explode('_', $_POST['typeofferid']);
                $typeid      = $typeofferid[0];
                $offerid     = $typeofferid[1];
            } else {
                return false;
            }
            $submitter = (int)$_POST['uid'];
            $startdate = strtotime($_POST['startdate']);
            //TO DO: Add Auto renew functionality.
            //$autorenew = $_POST['autorenew'];
            $newid = $this->db->genId($this->db->prefix($module->getVar('dirname', 'n') . '_subscr_orders') . '_orderid_seq');
            $sql   = 'INSERT INTO ' . $this->db->prefix($module->getVar('dirname', 'n') . '_subscr_orders') . "
                (orderid, uid, offerid, typeid, startdate, status, itemid, autorenew) VALUES
                ($newid, $submitter, $offerid, $typeid, '$startdate', '0' , $itemid, '0')";
            $this->db->query($sql);
            if ($newid == 0) {
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

    public function getOrderItemName($offerid = 0)
    {
        $sql     = 'SELECT o.offerid, o.duration, o.count, o.price, o.currency, o.descr, t.typeid, t.typename FROM '
                   . $this->db->prefix($module->getVar('dirname', 'n') . '_subscr_offers')
                   . ' o, '
                   . $this->db->prefix($module->getVar('dirname', 'n') . '_itemtypes')
                   . " t WHERE o.typeid=t.typeid AND o.offerid='$offerid' ORDER BY t.typename ASC";
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $result  = $this->db->query($sql);
        while (list($offerid, $duration, $count, $price, $currency, $descr, $typeid, $typename) = $this->db->fetchRow($result)) {
            if ($count == '1') {
                $duration_arr = $this->durationSingleArray();
            } else {
                $duration_arr = $this->durationArray();
            }
            $durationname = $duration_arr['' . $duration . ''];
            $itemname     = $typename . ' - ' . $count . ' ' . $durationname . ' - ' . $price . ' ' . $currency;
        }

        return $itemname;
    }

    public function updateOrder($orderid = '0', $status = '1', $startdate = '0', $billto = '0')
    {
        $ordervars = $this->getOrderVars($orderid);
        $typeid    = $ordervars['typeid'];
        $itemid    = $ordervars['itemid'];
        $sql       = 'UPDATE ' . $this->db->prefix($module->getVar('dirname', 'n') . '_subscr_orders') . " SET status='$status'";
        if ($startdate != '0') {
            $sql .= ", startdate='$startdate'";
        }
        if ($billto != '0') {
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

    public function getOrderVars($orderid = '0')
    {
        $sql     = 'SELECT ord.uid, ord.billto, ord.startdate, ord.typeid, ord.status, ord.itemid, ord.offerid FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_subscr_orders') . ' ord WHERE ord.orderid=' . (int)$orderid . '';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $arr     = $this->db->fetchArray($result);
        while (list($uid, $billto, $startdate, $typeid, $status, $itemid, $offerid) = $this->db->fetchRow($result)) {
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

    public function getOfferVars($offerid = '0', $showactive = '1')
    {
        $sql = 'SELECT count, duration FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_subscr_offers') . ' WHERE offerid=' . (int)$offerid . '';
        if ($showactive == '1') {
            $sql .= " AND activeyn='1'";
        }
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        $arr     = array();
        $arr     = $this->db->fetchArray($result);
        while (list($count, $duration) = $this->db->fetchRow($result)) {
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

    public function updateItem($itemid = '0', $typeid = '0')
    {
        if ($itemid != '0' && $typeid != '0') {
            $sql = 'UPDATE ' . $this->db->prefix($module->getVar('dirname', 'n') . '_items') . " SET typeid='" . (int)$typeid . '\' WHERE itemid=' . (int)$itemid . '';
            $this->db->queryF($sql);

            return true;
        } else {
            return false;
        }
    }

    public function getNewBillto($offerid = '0')
    {
    }

    public function changeItemType($itemid = 0, $itemtype = 0)
    {
        global $xoopsDB, $eh;
        $sql = 'UPDATE ' . $this->db->prefix($module->getVar('dirname', 'n') . '_items') . " SET typeid=$itemtype WHERE itemid=(int)($itemid)";
        $this->db->queryF($sql);

        return true;
    }

    public function durationPriceArray($dashes = '0', $showactive = '1')
    {
        $sql = 'SELECT o.offerid, o.duration, o.count, o.price, o.currency, o.descr, t.typeid, t.typename FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_subscr_offers') . ' o, ' . $this->db->prefix($module->getVar('dirname', 'n') . '_itemtypes') . ' t WHERE o.typeid=t.typeid';
        if ($showactive == '1') {
            $sql .= " AND activeyn='1'";
        }
        $sql     .= ' ORDER BY t.typelevel ASC, t.typename ASC';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        if ($dashes == '0') {
            $arr = array('0' => '---');
        }
        while (list($offerid, $duration, $count, $price, $currency, $descr, $typeid, $typename) = $this->db->fetchRow($result)) {
            if ($count == '1') {
                $duration_arr = $this->durationSingleArray();
            } else {
                $duration_arr = $this->durationArray();
            }
            $durationname                  = $duration_arr['' . $duration . ''];
            $arr[$typeid . '_' . $offerid] = $typename . '&nbsp;-&nbsp;' . $count . '&nbsp;' . $durationname . '&nbsp;-&nbsp;' . $price . '&nbsp;' . $currency;
        }

        return $arr;
    }

    public function itemsSelBox($selname = '', $none = false, $preselected = 0)
    {
        $sql     = 'SELECT typeid, typename FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_itemtypes') . '';
        $result  = $this->db->query($sql);
        $numrows = $this->db->getRowsNum($result);
        echo "<select name='" . $selname . '\'';
        echo ">\n";
        $result = $this->db->query($sql);
        if ($none) {
            echo "<option value='0'>----</option>\n";
        }
        while (list($typeid, $typename) = $this->db->fetchRow($result)) {
            if ($preselected == $typeid) {
                $sel = '&nbsp;selected';
            } else {
                $sel = '';
            }
            echo "<option value='$typeid'>$typename</option>\n";
        }
        echo "</select>\n";
    }

    public function itemTypesArray($dashes = '0')
    {
        $sql     = 'SELECT typeid, typename FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_itemtypes') . ' ORDER BY typelevel ASC';
        $result  = $this->db->query($sql) || $eh->show('0013');
        $numrows = $this->db->getRowsNum($result);
        $result  = $this->db->query($sql);
        if ($dashes == '0') {
            $arr = array('0' => '---');
        }
        while (list($typeid, $typename) = $this->db->fetchRow($result)) {
            $arr[$typeid] = $typename;
        }

        return $arr;
    }

    public function countSubscriptionsForType($typeid = 0)
    {
        $sql = 'SELECT COUNT(itemid) FROM ' . $this->db->prefix($module->getVar('dirname', 'n') . '_items') . ' WHERE typeid=' . (int)$typeid . '';
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        list($ret) = $this->db->fetchRow($result);

        return $ret;
    }
}
