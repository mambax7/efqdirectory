<?php
// $Id: subscriptions.php,v 0.18 2006/04/22 08:26:00 wtravel
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
include '../../../include/cp_header.php';
if (file_exists("../language/" . $xoopsConfig['language'] . "/main.php")) {
    include "../language/" . $xoopsConfig['language'] . "/main.php";
} else {
    include "../language/english/main.php";
}
include '../include/functions.php';
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
include_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
include_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
include_once '../class/class.subscription.php';
include_once '../class/class.directory.php';
include_once '../class/class.itemtype.php';
include_once '../class/class.offer.php';

$myts = &MyTextSanitizer::getInstance();
$eh = new ErrorHandler;
//$itemtypes = new XoopsTree($xoopsDB->prefix("efqdiralpha1_itemtypes"),"typeid","");

$moddir = $xoopsModule->getvar("dirname");
define('EFQ_MODDIR', $moddir);
if (isset($_GET["typeid"])) {
    $gpc_typeid = intval($_GET["typeid"]);
} else
    if (isset($_POST["typeid"])) {
        $gpc_typeid = intval($_POST["typeid"]);
    } else {
        $gpc_typeid = '0';
    }
    if (isset($_GET["offerid"])) {
        $gpc_offerid = intval($_GET["offerid"]);
    } else {
        $gpc_offerid = 0;
    }

    if (isset($_GET["dirid"])) {
        $gpc_dirid = intval($_GET["dirid"]);
    } else if (isset($_POST["dirid"])) {
        $gpc_dirid = intval($_POST["dirid"]);
    } else {
        $gpc_dirid = 0;
    }
    $eh = new ErrorHandler; //ErrorHandler object

	if (isset($_GET["op"])) {
	    $op = $_GET["op"];
	} else if (isset($_POST["op"])) {
        $op = $_POST["op"];
    } else {
        $op = '';
    }

    //function to list subscription types
    /**
     * listoffers()
     * 
     * @return
     */
    function listoffers()
    {
        include_once '../class/class.formradio.php';
        global $xoopsDB, $gpc_dirid, $eh, $xoopsUser, $moddir, $itemtypes, $myts;

        $subscription = new efqSubscription();
        $subscriptionhandler = new efqSubscriptionHandler();
        xoops_cp_header();
        adminmenu(4, _MD_MANAGE_SUBSCRIPTION_OFFERS);
        echo "<br />";

        // Select directories from DB
        $directoryhandler = new efqDirectoryHandler();
        
        if ($gpc_dirid == 0) {
        	$directories = $directoryhandler->getAll();
			if (count($directories) == 0) {
	            redirect_header(XOOPS_URL . '/modules/' . $moddir . '/admin/index.php', 1,
	                _MD_NOACTIVEDIRECTORIES);
	            exit();
	        }
			
			echo '<h4>' . _MD_SELECT_DIRECTORY. '</h4>';
        	echo '<table width="100%" border="0" cellspacing="1" class="outer">';
        	echo '<tr><th>' . _MD_OFFER_DIRECTORY . '</th></tr>';
        	foreach ($directories as $directory) {
                echo '<td class="even" valign="top"><a href="'.XOOPS_URL.'/modules/' . $moddir . '/admin/subscriptions.php?dirid='.$directory['dirid'].'">'. $myts->makeTboxData4Show($directory['name']) . '</a></td>';
                echo '</tr>';
            }
        } else {
        	$itemtypehandler = new efqItemTypeHandler();
            $arr_itemtypes = $itemtypehandler->getByDir($gpc_dirid);
			$obj_directory = $directoryhandler->get($gpc_dirid);
			echo '<h4>' . sprintf(_MD_SUBSCR_OFFERS, $obj_directory->getVar('name')) . '</h4>';
	        echo '<table width="100%" border="0" cellspacing="1" class="outer">';
	        echo '<tr><th>' . _MD_OFFER_TITLE .
	            '</th><th>' . _MD_OFFER_DURATION . '</th><th>' . _MD_OFFER_COUNT . "</th><th>" .
	            _MD_OFFER_PRICE . "</th><th>" . _MD_OFFER_CURRENCY . "</th><th>" .
	            _MD_OFFER_ACTIVE . "</th></tr>";
			// For directory, show list of offers
            $offers = $subscriptionhandler->getOffers($gpc_dirid);
            $countoffers = count($offers);
            if ($countoffers > 0) {
                $i = 0;
                foreach ($offers as $offer) {
                    $offertitle = $myts->makeTboxData4Show($offer['title']);
                    if ($offer['activeyn'] == '1') {
                        $activeyn = _MD_YES;
                    } else {
                        $activeyn = _MD_NO;
                    }
                    $subscription = new efqSubscription();
                    $durationarray = $subscription->durationArray();
                    //Show offers
                    echo "<tr>";
                    echo '<td class="even"><a href="subscriptions.php?op=editoffer&amp;offerid=' . $offer['offerid'] .
                        '">' . $offertitle . '</a></td>';
                    echo '<td class="odd">' . $durationarray[$offer['duration']] . '</td>';
                    echo '<td class="even">' . $offer['count'] . '</td>';
                    echo '<td class="odd">' . $offer['price'] . '</td>';
                    echo '<td class="even">' . $offer['currency'] . '</td>';
                    echo '<td class="odd">' . $activeyn . '</td>';
                    echo '</tr>';
                    $i++;
                }
            } else {
                echo '<tr><td colspan="6" class="odd">' . _MD_NORESULTS . '</td></tr>';
            }
	        echo '</table>';

            echo "<h4>" . _MD_ADD_SUBSCR_OFFER . "</h4>";
            echo '<table width="100%" border="0" cellspacing="1" class="outer"><tr><td>';
            $form = new XoopsThemeForm(_MD_ADD_OFFER_FORM, 'newofferform',
                'subscriptions.php');

            $form->addElement(new XoopsFormText(_MD_OFFER_TITLE, "title", 50, 100, ""), true);
            
			//$itemtypes_arr = $subscriptionhandler->itemTypesArray($gpc_dirid);
			foreach($arr_itemtypes as $itemtype) {
				$itemtypes[] = $itemtype['typename']; 
			}
            $itemtype_select = new XoopsFormSelect(_MD_SUBSCR_ITEMTYPE, 'typeid');
            $itemtype_select->addOptionArray($itemtypes);
            $form->addElement($itemtype_select);

            $duration_arr = $subscription->durationArray();
            $duration_select = new XoopsFormSelect(_MD_OFFER_DURATION, 'duration');
            $duration_select->addOptionArray($duration_arr);
            $form->addElement($duration_select);

            $form->addElement(new XoopsFormText(_MD_OFFER_COUNT, "count", 10, 50, ""), true);
            $form->addElement(new XoopsFormText(_MD_OFFER_PRICE, "price", 20, 50, ""), true);

            $currency_arr = $subscription->currencyArray();
            $currency_select = new XoopsFormSelect(_MD_OFFER_CURRENCY, 'currency');
            $currency_select->addOptionArray($currency_arr);
            $form->addElement($currency_select);

            $form_active = new XoopsFormCheckBox(_MD_OFFER_ACTIVEYN, "activeyn", 0);
            $form_active->addOption(1, _MD_YESNO);
            $form->addElement($form_active, true);
            $form->addElement(new XoopsFormDhtmlTextArea(_MD_OFFER_DESCR, "descr", "", 5, 50,
                ""));
            $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
            $form->addElement(new XoopsFormHidden("op", "addoffer"));
            $form->addElement(new XoopsFormHidden("dirid", $gpc_dirid));
            $form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
            $form->display();
            echo '</td></tr></table>';
	
            //$itemtypehandler = new efqItemTypeHandler();
            //$arr_itemtypes = $itemtypehandler->getByDir($gpc_dirid);
            $numrows = count($arr_itemtypes);
            if ($numrows > 0) {
                echo '<h4>' . _MD_ITEMTYPES . '</h4>';
                echo '<table width="100%" border="0" cellspacing="1" class="outer">';
                echo '<tr><th>' . _MD_ITEMTYPE_NAME . '</th><th>' . _MD_ITEMTYPE_LEVEL .
                    '</th><th>' . _MD_ACTION . '</th></tr>';
                $duration_arr = $subscription->durationArray();
                foreach ($arr_itemtypes as $itemtype) {
                    $typename = $myts->makeTboxData4Show($itemtype['typename']);
                    $level = $myts->makeTboxData4Show($itemtype['level']);
                    //Show types
                    echo '<tr>';
                    echo '<td class="even"><a href="subscriptions.php?op=edittype&typeid=' . $itemtype['typeid'] .
                        '">' . $itemtype['typename'] . '</strong></td>';
                    echo '<td class="odd">' . $itemtype['level'] . '</td>';
                    echo '<td class="odd"><a href="subscriptions.php?op=deltype&typeid=' . $itemtype['typeid'] .
                        '">' . _MD_DELETE . '</strong></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<h4>' . _MD_ITEMTYPES . '</h4>';
                echo '<table width="100%" border="0" cellspacing="1" class="outer">';
                echo '<tr><th>' . _MD_ITEMTYPE_NAME . '</th><th>' . _MD_ITEMTYPE_LEVEL .
                    '</th><th>' . _MD_ACTION . '</th></tr>';
                echo '<tr>';
                echo '<td colspan="3" class="even">'._MD_NORESULTS.'</td>';
                echo '</tr></table>';
            }
            echo '<h4>' . _MD_ADD_ITEMTYPE . '</h4>';

            //Add item type form
            echo '<table width="100%" border="0" cellspacing="1" class="outer"><tr><td>';
            $form = new XoopsThemeForm(_MD_ADD_ITEMTYPE_FORM, 'newitemtypeform',
                'subscriptions.php');
            $form->addElement(new XoopsFormText(_MD_ITEMTYPE_NAME, "typename", 50, 100, ""), true);
            $form->addElement(new XoopsFormText(_MD_ITEMTYPE_LEVEL, "level", 10, 50, ""), true);
            $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
            $form->addElement(new XoopsFormHidden("op", "addtype"));
            $form->addElement(new XoopsFormHidden("dirid", $gpc_dirid));
            $form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
            $form->display();
        }
        
        
        echo '</td></tr></table>';
        xoops_cp_footer();
    }

    /**
     * edittype()
     * 
     * @return
     */
    function edittype($gpc_typeid = 0)
    {
        global $xoopsDB, $eh, $myts, $xoopsUser;
        // Select directories from DB
        //$directoryhandler = new efqDirectoryHandler();
        //$directories = $directoryhandler->getAll();
        if ($gpc_typeid == 0) {
            redirect_header(XOOPS_URL . '/modules/' . $moddir . '/admin/subscriptions.php',
                2, _MD_INVALID_TYPEID);
            exit();
        }
        xoops_cp_header();
        adminmenu(4, _MD_MANAGE_SUBSCRIPTION_OFFERS);
        echo "<br />";

        $sql = 'SELECT typeid, typename, level, dirid FROM ' . $xoopsDB->prefix("efqdiralpha1_itemtypes") .
            ' WHERE typeid=' . intval($gpc_typeid);
        $result = $xoopsDB->query($sql) or $eh->show("0013");
        $numrows = $xoopsDB->getRowsNum($result);
        if ($numrows > 0) {
            //$duration_arr = $subscription->durationArray();
            while (list($typeid, $typename, $level, $dirid) = $xoopsDB->fetchRow($result)) {
                echo '<table width="100%" border="0" cellspacing="1" class="outer"><tr><td>';
                $form = new XoopsThemeForm(_MD_EDIT_ITEMTYPE_FORM, 'edititemtypeform',
                    'subscriptions.php');
                $form->addElement(new XoopsFormText(_MD_ITEMTYPE_NAME, "typename", 50, 100, $typename), true);
                $form->addElement(new XoopsFormText(_MD_ITEMTYPE_LEVEL, "level", 10, 50, $level), true);
                $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
                $form->addElement(new XoopsFormHidden("op", "savetype"));
                $form->addElement(new XoopsFormHidden("typeid", $gpc_typeid));
                $form->addElement(new XoopsFormHidden("dirid", $dirid));
                $form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
                $form->display();
                echo "</td></tr></table>";
            }
        } else {
            redirect_header(XOOPS_URL . '/modules/$moddir/admin/subscriptions.php', 2,
                _MD_INVALID_TYPEID);
            exit();
        }
        xoops_cp_footer();
    }

    /**
     * editoffer()
     * 
     * @return
     */
    function editoffer($gpc_offerid = 0)
    {
        global $xoopsDB, $eh, $xoopsUser, $itemtypes;
        if ($gpc_offerid == 0) {
            redirect_header(XOOPS_URL . '/modules/' . EFQ_MODDIR .
                '/admin/subscriptions.php', 2, _MD_INVALID_OFFERID);
        }
        $obj_subscr = new efqSubscription();
        $subscr_offer_handler = new efqSubscriptionOfferHandler();
        $subscr_handler = new efqSubscriptionHandler();
		if($subscr_offer_handler->setOffer($gpc_offerid)) {
			$subcr_offer_set = true;
		} else {
			$subcr_offer_set = false;
		}
		$obj_subscr_offer = $subscr_offer_handler->getOffer();
        xoops_cp_header();
        adminmenu(4, _MD_MANAGE_SUBSCRIPTION_OFFERS);
        echo "<br />";
        if ($subcr_offer_set) {
            $duration_arr = $obj_subscr->durationArray();
            echo '<table width="100%" border="0" cellspacing="1" class="outer"><tr><td>';
            $form = new XoopsThemeForm(_MD_EDIT_OFFER_FORM, 'newofferform',
                'subscriptions.php');
            
            $form->addElement(new XoopsFormText(_MD_OFFER_TITLE, "title", 50, 100, $obj_subscr_offer->getVar('title')), true);

            $itemtypes_arr = $subscr_handler->itemtypesArray();
            $itemtype_select = new XoopsFormSelect(_MD_SUBSCR_ITEMTYPE, 'typeid', $obj_subscr_offer->getVar('typeid'));
            $itemtype_select->addOptionArray($itemtypes_arr);
            $form->addElement($itemtype_select);

            //$duration_arr = $subscription->durationArray();
            $duration_select = new XoopsFormSelect(_MD_OFFER_DURATION, 'duration', $obj_subscr_offer->getVar('duration'));
            $duration_select->addOptionArray($duration_arr);
            $form->addElement($duration_select);

            $form->addElement(new XoopsFormText(_MD_OFFER_COUNT, "count", 10, 50, $obj_subscr_offer->getVar('count')), true);
            $form->addElement(new XoopsFormText(_MD_OFFER_PRICE, "price", 20, 50, $obj_subscr_offer->getVar('price')), true);

            $currency_arr = $obj_subscr->currencyArray();
            $currency_select = new XoopsFormSelect(_MD_OFFER_CURRENCY, 'currency', $obj_subscr_offer->getVar('currency'));
            $currency_select->addOptionArray($currency_arr);
            $form->addElement($currency_select);

            $form_active = new XoopsFormCheckBox(_MD_OFFER_ACTIVEYN, "activeyn", $obj_subscr_offer->getVar('activeyn'));
            $form_active->addOption(1, _MD_YESNO);
            $form->addElement($form_active, true);
            $form->addElement(new XoopsFormDhtmlTextArea(_MD_OFFER_DESCR, "descr", $obj_subscr_offer->getVar('descr'), 5,
                50, ""));
            $form->addElement(new XoopsFormButton('', 'submit', _MD_SUBMIT, 'submit'));
            $form->addElement(new XoopsFormHidden("op", "saveoffer"));
            $form->addElement(new XoopsFormHidden("offerid", $gpc_offerid));
            $form->addElement(new XoopsFormHidden("uid", $xoopsUser->getVar('uid')));
            $form->display();
            echo '</td></tr></table>';
        } else {
            redirect_header(XOOPS_URL . '/modules/' . EFQ_MODDIR .
                '/admin/subscriptions.php', 2, _MD_INVALID_OFFERID);
        }
        xoops_cp_footer();
    }

    //function to view one subscription type
    /**
     * viewtype()
     * 
     * @return
     */
    function viewtype()
    {
        global $xoopsDB, $eh, $get_typeid;
        if (isset($get_itemid)) {
            //view type
        }
    }

    /**
     * saveoffer()
     * 
     * @param bool $new
     * @return nothing
     */
    function saveoffer($new = false)
    {
        global $myts, $moddir, $xoopsDB;
        $obj_offer = new efqSubscriptionOffer();
        if ($new == true) {
            $obj_offer->setNew();
        } else {
            $obj_offer->setVar('offerid', intval($_POST['offerid']));
        }
        $obj_offer->setVar('typeid', intval($_POST['typeid']));
        $obj_offer->setVar('title', $myts->makeTboxData4Save($_POST['title']));
        $obj_offer->setVar('duration', intval($_POST['duration']));
        $obj_offer->setVar('count', intval($_POST['count']));
        $obj_offer->setVar('price', $myts->makeTboxData4Save($_POST['price']));
        if (isset($_POST['activeyn'])) {
            $post_activeyn = intval($_POST['activeyn']);
        } else {
            $post_activeyn = 0;
        }
        $obj_offer->setVar('activeyn', $post_activeyn);
        $obj_offer->setVar('currency', $myts->makeTboxData4Save($_POST['currency']));
        $obj_offer->setVar('descr', $myts->makeTareaData4Save($_POST['descr']));
        $obj_offer->setVar('dirid', intval($_POST['dirid']));
        $offerhandler = new efqSubscriptionOfferHandler($obj_offer);
		if ($obj_offer->isNew()) {
			$offerhandler->insertOffer($obj_offer);	
		} else {
			$offerhandler->updateOffer($obj_offer,true);
		}
		
        redirect_header(XOOPS_URL . '/modules/' . $moddir .
            '/admin/subscriptions.php?offerid=' . $obj_offer->getVar('offerid'), 2,
            _MD_SAVED);
        exit();
    }

    /**
     * deltype()
     * 
     * @return nothing
     */
    function deltype() //function to delete an item type

    {
        global $xoopsDB, $eh, $moddir;
        $subscription = new efqSubscription();
        $subscriptionhandler = new efqSubscriptionHandler();
        if (isset($_GET['typeid'])) {
            $g_typeid = intval($_GET['typeid']);
        } else {
            redirect_header(XOOPS_URL . '/modules/' . $moddir . '/admin/subscriptions.php',
                2, _MD_ERR_ITEMTYPE_DELETE);
            exit();
        }

        if ($subscriptionhandler->countSubscriptionsForType($g_typeid) > 0) {
            redirect_header(XOOPS_URL . '/modules/' . EFQ_MODDIR .
                '/admin/subscriptions.php', 3, _MD_ERR_ITEMTYPE_LINKED_TO_LISTINGS);
            exit();
        }
        $itemtypehandler = new efqItemTypeHandler();
        $itemtypehandler->set($g_typeid);
        $obj_itemtype = $itemtypehandler->getObjItemType();
        //$obj_itemtype->setVar('typeid', $g_typeid);
        $itemtypehandler->delete($obj_itemtype, true);
        redirect_header(XOOPS_URL . '/modules/' . EFQ_MODDIR .
            '/admin/subscriptions.php?dirid='.$obj_itemtype->getVar('dirid'), 1, _MD_ITEMTYPE_DELETED);
        exit();
    }

    //function to save an existing subscription type
    /**
     * savetype()
     * 
     * @param bool $new
     * @return
     */
    function savetype($new = false)
    {
        global $post_typeid, $moddir, $myts;
        $p_typeid = $_POST['typeid'];
        $p_dirid = $_POST['dirid'];
		$p_typename = $myts->makeTboxData4Save($_POST['typename']);
        $p_level = $myts->makeTboxData4Save($_POST['level']);
		$itemtypehandler = new efqItemTypeHandler();
        $obj_itemtype = new efqItemType();
        if ($new) {
            $obj_itemtype->setNew();
        }
        $obj_itemtype->setVar('typeid', $p_typeid);
        $obj_itemtype->setVar('dirid', $p_dirid);
        $obj_itemtype->setVar('typename', $p_typename);
        $obj_itemtype->setVar('level', $p_level);
        if ($new) {
            if ($itemtypehandler->insert($obj_itemtype)) {
	            redirect_header(XOOPS_URL . '/modules/' . $moddir .
	                '/admin/subscriptions.php?dirid=' . $obj_itemtype->getVar('dirid'), 2, _MD_SAVED);
	        } else {
	            echo $obj_itemtype->getErrors();
	        }
        } else {
        	if ($itemtypehandler->update($obj_itemtype)) {
	            redirect_header(XOOPS_URL . '/modules/' . $moddir .
	                '/admin/subscriptions.php?dirid=' . $obj_itemtype->getVar('dirid'), 2, _MD_SAVED);
	        } else {
	            echo $obj_itemtype->getErrors();
	        }
        }		
        exit();
    }

    switch ($op) {
        case 'delete':
            deltype();
            break;
        case 'addtype':
            savetype(true);
            break;
        case 'edittype':
            edittype($gpc_typeid);
            break;
        case 'deltype':
            deltype($gpc_dirid);
            break;
        case 'savetype':
            savetype();
            break;
        case 'viewtype':
            viewtype();
            break;
        case 'addoffer':
            saveoffer(true);
            break;
        case 'editoffer':
            editoffer($gpc_offerid);
            break;
        case 'saveoffer':
            saveoffer();
            break;
        default:
            listoffers();
            break;
    }

?>