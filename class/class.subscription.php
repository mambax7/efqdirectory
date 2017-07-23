<?php
// $Id: subscriptionmanager.php,v 0.18 2006/02/28 19:46:00 wtravel
//  ------------------------------------------------------------------------ //
//                				EFQ Directory			                     //
//                    Copyright (c) 2006 EFQ Consultancy                     //
//                       <http://www.efqdirectory.com/>                      //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
//	Part of the efqDirectory module provided by: wtravel					 //
// 	e-mail: info@efqdirectory.com											 //
//	Purpose: Create a business directory for xoops.		 	 				 //
//	Based upon the mylinks and the mxDirectory modules						 //
// ------------------------------------------------------------------------- //

class efqSubscription extends XoopsObject {

	/**
	 * Constructor
	 * 
	 */
	function efqSubscription(){
		//Constructor
	}
	
	
	/**
	 * Function durationArray: creates array of options for duration selbox:
	 * months, weeks, year, days etc.
	 * 
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @return	array	$arr
	 */
	function durationArray($dashes=false) {
		if ($dashes) {
			$arr = array('0' => '---', '1' => _MD_DAYS, '2' => _MD_WEEKS, '3' => _MD_MONTHS, '4' => _MD_QUARTERS, '5' => _MD_YEARS);			
		} else {
			$arr = array('1' => _MD_DAYS, '2' => _MD_WEEKS, '3' => _MD_MONTHS, '4' => _MD_QUARTERS, '5' => _MD_YEARS);
		}
		return $arr;
	}
	
	/**
	 * Function durationArray: creates array of options for duration selbox:
	 * single items like: month, week, year, day etc.
	 * 
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @return	array	$arr
	 */
	function durationSingleArray() {
		$arr = array('0' => '---', '1' => _MD_DAY, '2' => _MD_WEEK, '3' => _MD_MONTH, '4' => _MD_QUARTER, '5' => _MD_YEAR);
		return $arr;
	}
	
	/**
	 * Function currencyArray: creates array of options for currency selbox
	 * 
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @return	array	$arr
	 */	
	function currencyArray() {
		//create array of options for duration selbox: months, weeks, year, days etc.
		$arr = array('0' => '---', 'USD' => _MD_CURR_USD, 'AUD' => _MD_CURR_AUD, 'EUR' => _MD_CURR_EUR, 'GBP' => _MD_CURR_GBP, 'YEN' => _MD_CURR_YEN);
		return $arr;
	}
	
	/**
	 * Function notifyExpireWarning
	 * Notify user of a subscription order that is about to expire.
	 *  
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $orderid - Default: '0' - Order ID
	 * @param   int   $userid - Default: '0' - User ID
	 */
	function notifyExpireWarning($orderid='0', $userid='0') {
		global $xoopsConfig, $moddir;
		include_once(XOOPS_ROOT_PATH."/class/mail/xoopsmultimailer.php");
		
		$xoopsMailer = new XoopsMailer();
		$xoopsMailer->useMail();
		$template_dir = XOOPS_URL."/modules/".$moddir."/language/".$xoopsConfig['language']."/mail_template/";
		$template = "expirewarning.tpl";
		$subject = _MD_LANG_EXPIREWARNING_SUBJECT;
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
	var $errorhandler;
	var $subscription;
	
	/**
	 * Constructor
	 * 
	 */
	function efqSubscriptionHandler()
	{
		//Instantiate class
		global $eh;
		$this->db =& XoopsDatabaseFactory::getDatabaseConnection();
		$this->errorhandler = $eh;	
	}
	
	/**
     * create instance of directory class or reset the existing instance.
     * 
     * @return object $directory
     */
	function &create($isNew = true)
    {
        $subscription = new efqSubscription();
        if ($isNew) {
            $subscription->setNew();
        }
        return $subscription;
    }
    
	/**
	 * Function delete: Delete subscription order
	 * 
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $orderid - Default: '0' - Order ID
	 * @return	array	$arr
	 */	
	function delete($orderid='0') {
		if ( $orderid != '0' ) {
			$sql = "DELETE FROM ".$this->db->prefix('efqdiralpha1_subscr_orders')." WHERE orderid=".intval($orderid)."";
			$this->db->query($sql);
			return true;
		} else {
			return false;
		}		
	}
	
	/**
	 * Function createOrder: Create subscription order
	 * 
	 * @author EFQ Consultancy <info@efqconsultancy.com>
	 * @copyright EFQ Consultancy (c) 2007
	 * @version 1.0.0
	 * 
	 * @param   int   $itemid - Default: '0' - Item ID
	 * @return	int	$orderid - Newly created order id
	 */	
	function createOrder($itemid = 0) {
		$orderid = 0;
		if ($itemid != 0) {
			if ( isset( $_POST['typeofferid'] ) ) {
				$typeofferid = split("_",$_POST['typeofferid']);
				$typeid = $typeofferid[0];
				$offerid = $typeofferid[1];
			} else {
				return false;
			}
			$submitter = intval($_POST['uid']);
			$startdate = strtotime($_POST['startdate']);
			//TO DO: Add Auto renew functionality.
			//$autorenew = $_POST['autorenew'];
			$newid = $this->db->genId($this->db->prefix("efqdiralpha1_subscr_orders")."_orderid_seq");
			$sql = "INSERT INTO ".$this->db->prefix("efqdiralpha1_subscr_orders")."
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
				$typeofferid = split("_",$_POST['typeofferid']);
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
				$xoopsDB->query($sql) or $eh->show("0013");
			if ($newid == 0) {
				$orderid = $xoopsDB->getInsertId();
			}
		}
		return $orderid;
	}*/
	
	function getOrderItemName($offerid=0) {
		$sql = "SELECT o.offerid, o.duration, o.count, o.price, o.currency, o.descr, t.typeid, t.typename FROM ".$this->db->prefix("efqdiralpha1_subscr_offers")." o, ".$this->db->prefix("efqdiralpha1_itemtypes")." t WHERE o.typeid=t.typeid AND o.offerid='$offerid' ORDER BY t.typename ASC";
		$result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
		$result = $this->db->query($sql);
		while ( list($offerid, $duration, $count, $price, $currency, $descr, $typeid, $typename) = $this->db->fetchRow($result) ) {
			if ($count == '1') {
				$duration_arr = $this->durationSingleArray();
			} else {
				$duration_arr = $this->durationArray();
			}			
			$durationname = $duration_arr[''.$duration.''];
			$itemname = $typename.' - '.$count.' '.$durationname.' - '.$price.' '.$currency;
		}
		return $itemname;
	}
	
	
	function updateOrder( $orderid='0', $status='1', $startdate='0', $billto='0' ) {
		$ordervars = $this->getOrderVars($orderid);
		$typeid = $ordervars['typeid'];
		$itemid = $ordervars['itemid'];
		$sql = "UPDATE ".$this->db->prefix("efqdiralpha1_subscr_orders")." SET status='$status'";
		if ( $startdate != '0' ) {
			$sql .= ", startdate='$startdate'";
		}
		if ( $billto != '0' ) {
			$sql .= ", billto='$billto'";
		}
		$sql .= " WHERE orderid=".intval($orderid)."";
		$this->db->queryF($sql);
		if ( $startdate > time() ) {
			$this->updateScheduler( 'add', $itemid, $typeid, $startdate );	
		} else {
			$this->updateItem( $itemid, $typeid );
		}
		
	}
	
	function getOrderVars( $orderid='0' ) {
		$sql = "SELECT ord.uid, ord.billto, ord.startdate, ord.typeid, ord.status, ord.itemid, ord.offerid FROM ".$this->db->prefix("efqdiralpha1_subscr_orders")." ord WHERE ord.orderid=".intval($orderid)."";
		$result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
		$arr = $this->db->fetchArray($result);
		while ( list($uid, $billto, $startdate, $typeid, $status, $itemid, $offerid ) = $this->db->fetchRow($result) ) {
			$arr['uid'] = $uid;
			$arr['billto'] = $billto;
			$arr['startdate'] = $startdate;
			$arr['typeid'] = $typeid;
			$arr['status'] = $status;
			$arr['itemid'] = $itemid;
			$arr['offerid'] = $offerid;
		}
		return $arr;
	}
	
	function getOfferVars( $offerid='0', $showactive='1' ) {
		$sql = "SELECT count, duration FROM ".$this->db->prefix("efqdiralpha1_subscr_offers")." WHERE offerid=".intval($offerid)."";
		if ($showactive == '1') {
			$sql .= " AND activeyn='1'";
		}
		$result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
		$arr = array();
		$arr = $this->db->fetchArray($result);
		while ( list( $count, $duration ) = $this->db->fetchRow($result) ) {
			$arr['count'] = $count;
			$arr['duration'] = $duration;
		}
		return $arr;
	}
	
/*	function updateScheduler( $func='add', $itemid='0', $typeid='0', $startdate='0' ) {
		global $xoopsDB, $eh;
		if ( $func='add' ) {
			$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_subscr_scheduler")."_id_seq");
			$sql = "INSERT INTO ".$xoopsDB->prefix("efqdiralpha1_subscr_scheduler")."
				(id, startdate, itemid, newtypeid, status) VALUES 
				($newid, $startdate, $itemid, $typeid, '0')";
			$xoopsDB->queryF($sql) or $eh->show("0013");
		}
	}*/
	
	function updateItem( $itemid='0', $typeid='0' ) {
		if ( $itemid != '0' && $typeid != '0' ) {
			$sql = "UPDATE ".$this->db->prefix("efqdiralpha1_items")." SET typeid='".intval($typeid)."' WHERE itemid=".intval($itemid)."";
			$this->db->queryF($sql);
			return true;
		} else {
			return false;
		}
	}
	
	
	
	function getNewBillto($offerid='0') {
				
	}
	
	function changeItemType($itemid=0, $itemtype=0) {
		global $xoopsDB, $eh;
		$sql = "UPDATE ".$this->db->prefix('efqdiralpha1_items')." SET typeid=$itemtype WHERE itemid=intval($itemid)";
		$this->db->queryF($sql);
		return true;
	}
	
	function durationPriceArray($dashes = '0', $showactive = '1') {
		$sql = "SELECT o.offerid, o.duration, o.count, o.price, o.currency, o.descr, t.typeid, t.typename FROM ".$this->db->prefix("efqdiralpha1_subscr_offers")." o, ".$this->db->prefix("efqdiralpha1_itemtypes")." t WHERE o.typeid=t.typeid";
		if ($showactive == '1') {
			$sql .= " AND activeyn='1'";
		}
		$sql .= " ORDER BY t.level ASC, t.typename ASC";
		$result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
		if ($dashes != '0') {
			$arr = array('0' => '---');
		}
		while ( list($offerid, $duration, $count, $price, $currency, $descr, $typeid, $typename) = $this->db->fetchRow($result) ) {
			if ($count == '1') {
				$duration_arr = $this->durationSingleArray();
			} else {
				$duration_arr = $this->durationArray();
			}			
			$durationname = $duration_arr[''.$duration.''];
			$arr[$typeid."_".$offerid] = $typename."&nbsp;-&nbsp;".$count."&nbsp;".$durationname."&nbsp;-&nbsp;".$price."&nbsp;".$currency;
		}
		return $arr;
	}
	
	function itemsSelBox($selname="", $none=false, $preselected=0) {
		$sql = "SELECT typeid, typename FROM ".$this->db->prefix("efqdiralpha1_itemtypes")."";
		$result = $this->db->query($sql);
		$numrows = $this->db->getRowsNum($result);
		echo "<select name='".$selname."'";
		echo ">\n";
		if ( $none ) {
			echo "<option value='0'>----</option>\n";
		}
		while ( list($typeid, $typename) = $this->db->fetchRow($result) ) {
			if ( $preselected == $typeid ) {
				$sel = "&nbsp;selected";
			} else {
				$sel = "";
			}
			echo "<option value='$typeid'>$typename</option>\n";
		}
		echo "</select>\n";
	}
	
	function itemTypesArray($dirid = 0, $dashes = 0) {
		$sql = "SELECT typeid, typename FROM ".$this->db->prefix("efqdiralpha1_itemtypes")." WHERE dirid=".intval($dirid)." ORDER BY level ASC";
		$arr = array();
		$result = $this->db->query($sql) or $eh->show("0013");
		$numrows = $this->db->getRowsNum($result);
		if ($dashes != 0) {
			$arr = array('0' => '---');
		}
		while ( list($typeid, $typename) = $this->db->fetchRow($result) ) {
			$arr[$typeid] = $typename;
		}
		return $arr;
	}
	
	function countSubscriptionsForType($typeid=0) {
		$sql = "SELECT COUNT(itemid) FROM ".$this->db->prefix("efqdiralpha1_items")." WHERE typeid=".intval($typeid)."";
		if (!$result = $this->db->query($sql)) {
            return false;
        }
        list($ret) = $this->db->fetchRow($result);
        return $ret;
	}
	
	function getOffers($dirid=0) {
		$extendedwhere = '';
		if ($dirid != 0) {
			$extendedwhere = ' AND o.dirid='.intval($dirid);
		}
		$sql = "SELECT o.dirid, o.offerid, o.typeid, o.title, o.duration, o.count, o.price, o.activeyn, o.currency, o.descr, t.typename, t.level FROM ".$this->db->prefix("efqdiralpha1_itemtypes")." t, ".$this->db->prefix("efqdiralpha1_subscr_offers")." o WHERE o.typeid=t.typeid".$extendedwhere;
		$result = $this->db->query($sql) or $this->errorhandler->show("0013");
		$arr = array();
		while(list($dirid, $offerid, $typeid, $title, $duration, $count, $price, $activeyn, $currency, $descr, $typename, $level) = $this->db->fetchRow($result)) {
			$arr[] = array('dirid' => $dirid,
				'offerid' => $offerid,
				'typeid' => $typeid,
				'title' => $title,
				'duration' => $duration,
				'count' => $count,
				'price' => $price,
				'activeyn' => $activeyn,
				'currency' => $currency,
				'descr' => $descr,
				'typename' => $typename,
				'level' => $level );			
		}
		return $arr;
	}
}
?>