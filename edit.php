<?php
// $Id: edit.php,v 0.18 2006/03/23 21:37:00 wtravel
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
include 'header.php';
$myts = &MyTextSanitizer::getInstance(); // MyTextSanitizer object
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
include_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
include_once 'class/class.datafieldmanager.php';
include_once 'class/class.datafield.php';
include_once 'class/class.formimage.php';
include_once 'class/class.formdate.php';
include_once 'class/class.image.php';
include_once 'class/class.efqtree.php';
include_once 'class/class.listing.php';
include_once 'class/class.listingdata.php';
include_once 'class/class.gmap.php';

// Get module directory name;
$moddir = $xoopsModule->getvar("dirname");
// Prepare two tree classes;
$mytree = new XoopsTree($xoopsDB->prefix("efqdiralpha1_cat"), "cid", "pid");
$efqtree = new efqTree($xoopsDB->prefix("efqdiralpha1_cat"), "cid", "pid");
$efqListing = new efqListing();
$efqListingHandler = new efqListingHandler();

$eh = new ErrorHandler; //ErrorHandler object
$datafieldmanager = new efqDataFieldManager();

// If the user is not logged in and anonymous postings are
// not allowed, redirect and exit.
if (empty($xoopsUser) and !$xoopsModuleConfig['anonpost']) {
    redirect_header(XOOPS_URL . "/user.php", 2, _MD_MUSTREGFIRST);
    exit();
}

// Check if user has adminrights or not;
if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
    $isadmin = true;
} else {
    $isadmin = false;
}

// Get the user ID;
$userid = $xoopsUser->getVar('uid');

// If submit data was posted;
if (!empty($_POST['submit'])) {

    if (!empty($_POST['itemid'])) {
        $gpc_itemid = intval($_POST['itemid']);
    } else {
        redirect_header("index.php", 2, _MD_NOVALIDITEM_IDMISSING);
        exit();
    }
    if (isset($_POST['op'])) {
        $op = $_POST['op'];
    } else {
        $op = "";
    }
    // If option is "submitforapproval", update status to 1;
    if ($op == 'submitforapproval') {
        if ($efqListingHandler->updateStatus($gpc_itemid, '1')) {
            redirect_header("index.php", 2, _MD_SUBMITTED_PUBLICATION);
        } else {
            redirect_header("index.php", 2, _MD_ERROR_NOT_SAVED);
        }
        exit();
    } else
        if ($op == 'publish' and $isadmin and $xoopsModuleConfig['autoapproveadmin'] ==
            1) {
            // If option is "publish" and item is submitted by admin user while auto
            // approve listings submitted by admin user is turned on, update status to 2;
            if ($efqListingHandler->updateStatus($gpc_itemid, '2')) {
                redirect_header("index.php", 2, _MD_PUBLISHED);
            } else {
                redirect_header("index.php", 2, _MD_ERROR_NOT_SAVED);
            }
            exit();
        }
    if (!empty($_POST['dirid'])) {
        $post_dirid = intval($_POST['dirid']);
    } else {
        $post_dirid = 0;
    }
    if (isset($_POST["itemtitle"])) {
        $gpc_title = $myts->makeTboxData4Save($_POST["itemtitle"]);
        $gpc_ini_title = $myts->makeTboxData4Save($_POST["ini_itemtitle"]);
        // Count number of upload files.
        $upload_files_count = count($_POST['xoops_upload_file']);
        $obj_listing = new efqListing();
        $obj_listing_handler = new efqListingHandler();
        $obj_listing->setListingVars($obj_listing_handler->getListing($gpc_itemid),"efqListing");
        
		if ($upload_files_count > 0) {
            include_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $uploader = new XoopsMediaUploader(XOOPS_ROOT_PATH . '/modules/' . $moddir .
                '/init_uploads', array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'), 500000, 500, 500);
            $uploader->setPrefix('logo');
            $err = array();

            for ($i = 0; $i < $upload_files_count; $i++) {
                if ($_POST['xoops_upload_file'][$i] != "") {
                    $medianame = $_POST['xoops_upload_file'][$i];
                    if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                        if (!$uploader->upload()) {
							$err[] = $uploader->getErrors();
                        } else {
                            $savedfilename = $uploader->getSavedFileName();
                            echo 'upload succesful';
							echo $savedfilename;
                            //Rename the uploaded file to the same name in a different location that does not have 777 rights or 755.
                            rename("".XOOPS_ROOT_PATH."/modules/".$moddir."/init_uploads/".$savedfilename."", "".XOOPS_ROOT_PATH."/modules/".$moddir."/uploads/".$savedfilename."");
                            //Delete the uploaded file from the initial upload folder if it is still present in that folder.
                            if (file_exists("".XOOPS_ROOT_PATH."/modules/".$moddir."/init_uploads/".$savedfilename . "")) {
                                unlink("".XOOPS_ROOT_PATH."/modules/".$moddir."/init_uploads/".$savedfilename."");
                            }
                        }
                        $obj_listing->setVar('logourl', $savedfilename);
                    } else {
                    	echo 'fetch media failed.';
                    }
                    
                }                
            }
        }
        $err[] = $uploader->getErrors();
        print_r($err);
        
       	if ($gpc_title != $gpc_ini_title) {
            $obj_listing->setVar('title', $gpc_title);
        }
		$obj_listing->setVar('itemid', $gpc_itemid);
		if ($obj_listing_handler->updateListing($obj_listing)) {
            $obj_listing->setUpdated(true);
        }
        
        
    } else {
        redirect_header("index.php", 2, _MD_NOVALIDITEM_TITLEMISSING);
        exit();
    }

    /** Update description if any */
    if (isset($_POST['ini_description'])) {
        $p_ini_description = $myts->makeTareaData4Save($_POST["ini_description"]);
    } else {
        $p_ini_description = null;
    }
    if (isset($_POST['description'])) {
        $p_description = $myts->makeTareaData4Save($_POST["description"]);
    } else {
        $p_description = null;
    }
    if (isset($_POST["description_set"])) {
        if ($_POST["description_set"] == '1') {
            if ($p_ini_description != $p_description) {
                $obj_listing->setVar('description', $p_description);
                $obj_listing_handler->updateDescription($obj_listing->getVar('itemid'), $obj_listing->getVar('description'));
            }
        } else
            if ($p_description != null or $p_description != "") {
                $obj_listing->setVar('description', $p_description);
                $obj_listing_handler->insertDescription($obj_listing->getVar('itemid'), $obj_listing->getVar('description'));
            }
    }

	/** Update categories linked to the listing if any */
    //Get all categories currently linked to the listing
	$linkedcategories = $efqListingHandler->getLinkedCategories($gpc_itemid, $post_dirid);
	$allcategories =  $efqListingHandler->getAllCategories($post_dirid);
	$count = 0;
    foreach ($allcategories as $category) {
    	if (isset($_POST["selected" . $category . ""])) {
            if (!in_array($category, $linkedcategories)) {
				$newid = $xoopsDB->genId($xoopsDB->prefix("efqdiralpha1_item_x_cat") .
                    "_xid_seq");
                $sql = sprintf("INSERT INTO %s (xid, cid, itemid, active, created) VALUES (%u, %u, %u, '%s', '%s')",
                $xoopsDB->prefix("efqdiralpha1_item_x_cat"), $newid, $category, $gpc_itemid, 1, time());
                $xoopsDB->query($sql) or $eh->show("0013");
            }
            $count++;
        } else {
            if (in_array($category, $linkedcategories)) {
                $sql = sprintf("DELETE FROM %s WHERE cid=%u AND itemid=%u", $xoopsDB->prefix("efqdiralpha1_item_x_cat"), $category, $gpc_itemid);
                $xoopsDB->query($sql) or $eh->show("0013");
            }
        }
    }
    if ($count == 0) {
        redirect_header(XOOPS_URL."/modules/$moddir/submit.php?dirid=".$post_dirid."", 2, _MD_NOCATEGORYMATCH);
        exit();
    }

	/** Get all datatypes that can be associated with this listing */
	$obj_data_handler = new efqListingDataHandler();
	$listingdata = $obj_data_handler->getData($gpc_itemid);
	foreach ($listingdata as $data) {
		$obj_data = new efqListingData();
    	$obj_data->setListingData($data);
		if (isset($_POST[$data['dtypeid']])) {
            if (is_array($_POST[$data['dtypeid']])) {
                $post_value_array = $_POST[$data['dtypeid']];
                $post_value = "";
                $options_arr = split("[|]", $data['options']);
                $options_arr[] = '-';
                $count_post_value_array = count($post_value_array);
                for ($i = 0; $i < $count_post_value_array; $i++) {
                    // Check if posted value is in options.
                    if (in_array($post_value_array[$i], $options_arr)) {
                        if ($i == 0) {
                            $post_value = $post_value_array[$i];
                        } else {
                            $post_value .= "|".$post_value_array[$i];
                        }
                    }
                }
            } else {
                $post_value = $myts->makeTboxData4Save($_POST[$data['dtypeid']]);
            }
        } else {
            $post_value = "";
        }
        if (isset($_POST["custom".$data['dtypeid'].""])) {
            $post_customtitle = $myts->makeTboxData4Save($_POST['custom'.$data['dtypeid']]);
        } else {
            $post_customtitle = "";
        }
        if (isset($_POST["url_title".$data['dtypeid'].""])) {
            $post_urltitle = $myts->makeTboxData4Save($_POST['url_title'.$data['dtypeid']]);
        } else {
            $post_urltitle = "";
        }
        if (isset($_POST["url_link".$data['dtypeid'].""])) {
            $post_urllink = $myts->makeTboxData4Save($_POST['url_link'.$data['dtypeid']]);
        } else {
            $post_urllink = "";
        }
        if ($post_urllink != "") {
            $post_value = $post_urllink . '|' . $post_urltitle;
        }
        
		
		if ($data['fieldtype'] == "gmap") {
			// Retrieve POST values for google map.
	        if (isset($_POST["" . $data['dtypeid']."_lon"])) {
	            $post_gmap_lon = $myts->makeTboxData4Save($_POST["".$data['dtypeid']."_lon"]);
	        } else {
	            $post_gmap_lon = "";
	        }
	        if (isset($_POST["" . $data['dtypeid']."_lat"])) {
	            $post_gmap_lat = $myts->makeTboxData4Save($_POST["".$data['dtypeid']."_lat"]);
	        } else {
	            $post_gmap_lat = "";
	        }
	        if (isset($_POST["" . $data['dtypeid']."_descr"])) {
	            $post_gmap_descr = $myts->makeTboxData4Save($_POST["".$data['dtypeid']."_descr"]);
	        } else {
	            $post_gmap_descr = "";
	        }
	        $gmapHandler = new efqGmapHandler();
            $obj_gmap = new efqGmap();
            if ($data['value'] != null) {
            	$obj_gmap->setData($gmapHandler->getByDataId($data['value']));
            }
        	$obj_gmap->setVar('lon', $post_gmap_lon);
        	$obj_gmap->setVar('lat', $post_gmap_lat);
        	$obj_gmap->setVar('descr', $post_gmap_descr);
        	if ($data['value'] != null) {
				if (!$gmapHandler->updateGmap($obj_gmap)) {
	                echo 'insert new gmap failed';
	                exit();
	            } else {
	            	$post_value = $obj_gmap->getVar('id');	
	            }
	             
            } else {
				if (!$gmapHandler->insertGmap($obj_gmap)) {
	                echo 'update gmap failed';
	                exit();
	            } else {
	            	$post_value = $obj_gmap->getVar('id');
	            }
            }			
		}
     
        if ($obj_data->getVar('itemid') == null) {
            // That means there was not any value, so a new record
			// should be added to the data table.
			$obj_data->setVar('value', $post_value);
			$obj_data->setVar('customtitle', $post_customtitle);            
            $obj_data_handler->insertListingData($obj_data);
        } else {
			if ($data['value'] != $post_value) {
				$obj_data->setVar('value', $post_value);
				$obj_data->setVar('customtitle', $post_customtitle);            
            	$obj_data_handler->updateListingData($obj_data);
            }
        }
    }
    redirect_header("edit.php?item=$gpc_itemid", 1, _MD_ITEM_UPDATED);
    exit();
} else {
    // Prepare page for showing listing edit form.
    if (!empty($_GET['item'])) {
        $get_itemid = intval($_GET['item']);
        $get_dirid = getDirIdFromItem($get_itemid);
    } else {
        redirect_header("index.php", 2, _MD_NOVALIDITEM_GET_IDMISSING);
        exit();
    }

    $xoopsOption['template_main'] = 'efqdiralpha1_editlisting.html';
    include XOOPS_ROOT_PATH . "/header.php";
    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    $xoopsTpl->assign('lang_submit', _SUBMIT);
    $xoopsTpl->assign('lang_cancel', _CANCEL);

    $sql = "SELECT i.itemid, i.logourl, i.uid, i.status, i.created, i.title, i.typeid, i.dirid, t.description FROM ".$xoopsDB->prefix("efqdiralpha1_items")." i LEFT JOIN ".$xoopsDB->prefix("efqdiralpha1_item_text")." t ON (i.itemid=t.itemid) WHERE i.itemid=".$get_itemid."";
    $item_result = $xoopsDB->query($sql);
    $numrows = $xoopsDB->getRowsNum($item_result);

    while (list($itemid, $logourl, $submitter, $status, $created, $itemtitle, $typeid, $dirid, $description) = $xoopsDB->fetchRow($item_result)) {
        $itemtitle = $myts->makeTboxData4Show($itemtitle);
        // Only the submitter or the admin are allowed edit a listing, so make sure
        // all other users are redirected elsewhere.
        if ($isadmin or $submitter == $userid) {
            if ($status == 0 and $submitter == $userid) {
                if ($xoopsModuleConfig['autoapproveadmin'] == 1) {
                    // If status is not 0 and autoapprove is on, the submitter or
                    // admin can edit the listing and with the button "view listing"
                    // Go to the listing page in 'view' mode.
                    $publish_button = "<form action=\"edit.php\" method=\"post\"><input type=\"hidden\" name=\"op\" value=\"publish\"><input type=\"hidden\" name=\"user\" value=\"$userid\"><input type=\"hidden\" name=\"itemid\" value=\"$get_itemid\"><input type=\"submit\" name=\"submit\" class=\"formButton\" value=\"" .
                        _MD_PUBLISH_LISTING . "\"></form><br />";
                    $xoopsTpl->assign('submitview_button', $publish_button);
                } else {
					// Only the submitter can submit listing for approval when status = 0.
	                $submit_for_approval_button = "<form action=\"edit.php\" method=\"post\"><input type=\"hidden\" name=\"op\" value=\"submitforapproval\"><input type=\"hidden\" name=\"user\" value=\"$userid\"><input type=\"hidden\" name=\"itemid\" value=\"$get_itemid\"><input type=\"submit\" name=\"submit\" class=\"formButton\" value=\"" .
	                    _MD_PUBLISH_LISTING . "\"></form><br />";
	                $xoopsTpl->assign('submitview_button', $submit_for_approval_button);
	            }
            } else
                if ($xoopsModuleConfig['autoapprove'] == 1 and $status == 2) {
                    // If status is not 0 and autoapprove is on, the submitter or
                    // admin can edit the listing and with the button "view listing"
                    // Go to the listing page in 'view' mode.
                    $view_button = "<form action=\"listing.php\" method=\"get\"><input type=\"hidden\" name=\"item\" value=\"" .
                        $itemid . "\"><input type=\"submit\" value=\"" . _MD_VIEWITEM . "\"></input></form><br />";
                    $xoopsTpl->assign('submitview_button', $view_button);
                } else
                    if ($xoopsModuleConfig['autoapproveadmin'] == 1 and $status == 1) {
                        // If status is not 0 and autoapprove is on, the submitter or
                        // admin can edit the listing and with the button "view listing"
                        // Go to the listing page in 'view' mode.
                        $publish_button = "<form action=\"edit.php\" method=\"post\"><input type=\"hidden\" name=\"op\" value=\"publish\"><input type=\"hidden\" name=\"user\" value=\"$userid\"><input type=\"hidden\" name=\"itemid\" value=\"$get_itemid\"><input type=\"submit\" name=\"submit\" class=\"formButton\" value=\"" .
                            _MD_PUBLISH_LISTING . "\"></form><br />";
                        $xoopsTpl->assign('submitview_button', $publish_button);
                    } else
                        if (!$isadmin) {
                            // Only admin is allowed to edit a listing after approval (status = 2)
                            // in case autoapprove is off.
                            redirect_header("listing.php?item=".$itemid, 2, _MD_ONLYADMIN_ALLOWED_TO_EDIT);
                            exit();
                        }
            if ($logourl != "") {
                $picture = "uploads/$logourl";
            } else {
                $picture = "images/nopicture.gif";
            }
            ob_start();
            $form = new XoopsThemeForm(_MD_EDITITEM_FORM, 'editform', 'edit.php');
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement(new XoopsFormText(_MD_TITLE, "itemtitle", 50, 250, $itemtitle), true);
            $categories = getCatSelectArea($get_itemid, $dirid);
            $form_cats = new XoopsFormLabel(_MD_ITEMCATEGORIES, "$categories");
            $form->addElement($form_cats);
            $form->addElement(new XoopsFormDhtmlTextArea(_MD_DESCRIPTION, "description", $description, 5, 50));
            $form->addElement(new XoopsFormFile(_MD_SELECT_PIC, 'image', 30000));
            $form->addElement(new XoopsFormImage(_MD_CURRENT_PIC, "current_image", null, "$picture", "", ""));

            $obj_datafield_handler = new efqDataFieldHandler();
            $datafields = $obj_datafield_handler->getDataFields($get_itemid);
            foreach ($datafields as $datafield) {
                $field = $datafieldmanager->createFieldFromArray($datafield);
            }
            $form->addElement(new XoopsFormButton('', 'submit', _MD_SAVE, 'submit'));
            $form->addElement(new XoopsFormHidden("op", "edit"));
            $form->addElement(new XoopsFormHidden("itemid", $get_itemid));
            $form->addElement(new XoopsFormHidden("dirid", $get_dirid));
            $form->addElement(new XoopsFormHidden("ini_itemtitle", $itemtitle));

            if ($description != null) {
                $form->addElement(new XoopsFormHidden("ini_description", $description));
            }
            $form->addElement(new XoopsFormHidden("uid", $userid));
            if ($description != null) {
                $form->addElement(new XoopsFormHidden("description_set", '1'));
            } else {
                $form->addElement(new XoopsFormHidden("description_set", '0'));
            }
            $form->display();
            $xoopsTpl->assign('dtypes_form', ob_get_contents());
            ob_end_clean();
        }

    }

}
include_once XOOPS_ROOT_PATH . '/footer.php';
?>