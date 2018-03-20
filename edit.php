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
use XoopsModules\Efqdirectory;

include __DIR__ . '/header.php';

$myts = \MyTextSanitizer::getInstance(); // MyTextSanitizer object
// require_once __DIR__ . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
// require_once __DIR__ . '/class/class.datafieldmanager.php';
// require_once __DIR__ . '/class/class.formimage.php';
// require_once __DIR__ . '/class/class.formdate.php';
// require_once __DIR__ . '/class/class.image.php';
// require_once __DIR__ . '/class/class.efqtree.php';
// require_once __DIR__ . '/class/class.listing.php';

// Get module directory name;
$moddir = $xoopsModule->getVar('dirname');
// Prepare two tree classes;
$mytree            = new Efqdirectory\Tree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');
$efqtree           = new Efqdirectory\Tree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');
$efqListing        = new Efqdirectory\Listing();
$efqListingHandler = new Efqdirectory\ListingHandler();

$eh               = new ErrorHandler; //ErrorHandler object
$datafieldmanager = new Efqdirectory\DataFieldManager();

// If the user is not logged in and anonymous postings are
// not allowed, redirect and exit.
if (empty($xoopsUser) and !$xoopsModuleConfig['anonpost']) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);
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
        $post_itemid = (int)$_POST['itemid'];
    } else {
        redirect_header('index.php', 2, _MD_NOVALIDITEM_IDMISSING);
    }
    if (isset($_POST['op'])) {
        $op = $_POST['op'];
    } else {
        $op = '';
    }
    // If option is "submitforapproval" then submit and redirect;
    if ('submitforapproval' === $op) {
        if ($efqListingHandler->updateStatus($post_itemid, '1')) {
            redirect_header('index.php', 2, _MD_SUBMITTED_PUBLICATION);
        } else {
            redirect_header('index.php', 2, _MD_ERROR_NOT_SAVED);
        }
        exit();
    }
    if (!empty($_POST['dirid'])) {
        $post_dirid = (int)$_POST['dirid'];
    } else {
        $post_dirid = 0;
    }
    if (isset($_POST['itemtitle'])) {
        $p_title     = $myts->addSlashes($_POST['itemtitle']);
        $p_ini_title = $myts->addSlashes($_POST['ini_itemtitle']);
        // Start uploading up file;
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $uploader = new \XoopsMediaUploader(XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads', ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'], 300000, 250, 250);
        $uploader->setPrefix('logo');
        $err    = [];
        $ucount = count($_POST['xoops_upload_file']);
        for ($i = 0; $i < $ucount; ++$i) {
            if ('' !== $_POST['xoops_upload_file'][$i]) {
                $medianame = $_POST['xoops_upload_file'][$i];
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][$i])) {
                    if (!$uploader->upload()) {
                        $err[] = $uploader->getErrors();
                    } else {
                        $savedfilename = $uploader->getSavedFileName();
                        $ticket        = uniqid(mt_rand(), 1);
                        //Rename the uploaded file to the same name in a different location that does not have 777 rights or 755.
                        rename('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '', '' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/uploads/' . $savedfilename . '');
                        //Delete the uploaded file from the initial upload folder if it is still present in that folder.
                        if (file_exists('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '')) {
                            unlink('' . XOOPS_ROOT_PATH . '/modules/' . $moddir . '/init_uploads/' . $savedfilename . '');
                        }
                    }
                    if ($p_title != $p_ini_title) {
                        $sql = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_items') . " SET logourl = '" . $savedfilename . '\' WHERE itemid = \'' . $post_itemid . '\'';
                    } else {
                        $sql = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_items') . " SET title = '" . $p_title . '\', logourl = \'' . $savedfilename . '\' WHERE itemid = \'' . $post_itemid . '\'';
                    }
                    $xoopsDB->query($sql) ; //|| $eh->show('0013');
                }
            } else {
                if ($p_title != $p_ini_title) {
                    $sql = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_items') . " SET title = '" . $p_title . '\' WHERE itemid = \'' . $post_itemid . '\'';
                }
                $xoopsDB->query($sql) ; //|| $eh->show('0013');
            }
        }
    } else {
        redirect_header('index.php', 2, _MD_NOVALIDITEM_TITLEMISSING);
    }
    if (isset($_POST['ini_description'])) {
        $p_ini_description = $myts->addSlashes($_POST['ini_description']);
    } else {
        $p_ini_description = null;
    }
    if (isset($_POST['description'])) {
        $p_description = $myts->addSlashes($_POST['description']);
    } else {
        $p_description = null;
    }
    if (isset($_POST['description_set'])) {
        if ('1' == $_POST['description_set']) {
            if ($p_ini_description != $p_description) {
                $sql = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_item_text') . " SET description = '$p_description' WHERE itemid = $post_itemid";
                $xoopsDB->query($sql) ; //|| $eh->show('0013');
            }
        } elseif (null != $p_description or '' !== $p_description) {
            $sql = sprintf("INSERT INTO %s (itemid, description) VALUES (%u, '%s')", $xoopsDB->prefix($helper->getDirname() . '_item_text'), $post_itemid, $p_description);
            $xoopsDB->query($sql) ; //|| $eh->show('0013');
        }
    }

    $linkedcats    = $efqListingHandler->getLinkedCatsArray($post_itemid, $post_dirid);
    $sql           = 'SELECT cid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE dirid='" . $post_dirid . '\' AND active=\'1\'';
    $allcatsresult = $xoopsDB->query($sql);
    $numrows       = $xoopsDB->getRowsNum($allcatsresult);
    $count         = 0;
    if ($numrows > 0) {
        while (false !== (list($cid) = $xoopsDB->fetchRow($allcatsresult))) {
            if (isset($_POST['selected' . $cid . ''])) {
                if (!in_array($cid, $linkedcats)) {
                    $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_item_x_cat') . '_xid_seq');
                    $sql   = sprintf("INSERT INTO %s (xid, cid, itemid, active, created) VALUES (%u, %u, %u, '%s', '%s')", $xoopsDB->prefix($helper->getDirname() . '_item_x_cat'), $newid, $cid, $post_itemid, 1, time());
                    $xoopsDB->query($sql) ; //|| $eh->show('0013');
                }

                ++$count;
            } else {
                if (in_array($cid, $linkedcats)) {
                    $sql = sprintf('DELETE FROM %s WHERE cid=%u AND itemid=%u', $xoopsDB->prefix($helper->getDirname() . '_item_x_cat'), $cid, $post_itemid);
                    $xoopsDB->query($sql) ; //|| $eh->show('0013');
                }
            }
        }
        if (0 == $count) {
            redirect_header(XOOPS_URL . "/modules/$moddir/submit.php?dirid=" . $post_dirid . '', 2, _MD_NOCATEGORYMATCH);
        }
    } else {
        redirect_header(XOOPS_URL . "/modules/$moddir/submit.php?dirid=" . $post_dirid . '', 2, _MD_NOCATEGORIESAVAILABLE);
    }

    // Get all datatypes that can be associated with this listing.
    $sql         = 'SELECT DISTINCT t.dtypeid, t.title, t.section, f.typeid, f.fieldtype, f.ext, t.options, d.itemid, d.value, t.custom ';
    $sql         .= 'FROM '
                    . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                    . ' ic, '
                    . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
                    . ' xc, '
                    . $xoopsDB->prefix($helper->getDirname() . '_fieldtypes')
                    . ' f, '
                    . $xoopsDB->prefix($helper->getDirname()
                                       . '_dtypes')
                    . ' t ';
    $sql         .= 'LEFT JOIN ' . $xoopsDB->prefix($helper->getDirname() . '_data') . ' d ON (t.dtypeid=d.dtypeid AND d.itemid=' . $post_itemid . ') ';
    $sql         .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=" . $post_itemid . '';
    $data_result = $xoopsDB->query($sql) ; //|| $eh->show('0013');
    while (false !== (list($dtypeid, $title, $section, $ftypeid, $fieldtype, $ext, $options, $itemid, $value, $custom) = $xoopsDB->fetchRow($data_result))) {
        if (isset($_POST["$dtypeid"])) {
            if (is_array($_POST["$dtypeid"])) {
                $post_value_array       = $_POST["$dtypeid"];
                $post_value             = '';
                $options_arr            = explode('[|]', $options);
                $options_arr[]          = '-';
                $count_post_value_array = count($post_value_array);
                for ($i = 0; $i < $count_post_value_array; ++$i) {
                    // Check if posted value is in options.
                    if (in_array($post_value_array[$i], $options_arr)) {
                        if (0 == $i) {
                            $post_value = $post_value_array[$i];
                        } else {
                            $post_value .= '|' . $post_value_array[$i];
                        }
                    }
                }
            } else {
                $post_value = $myts->addSlashes($_POST["$dtypeid"]);
            }
        } else {
            $post_value = '';
        }
        if (isset($_POST['custom' . $dtypeid . ''])) {
            $post_customtitle = $myts->addSlashes($_POST['custom' . $dtypeid . '']);
        } else {
            $post_customtitle = '';
        }
        if (isset($_POST['url_title' . $dtypeid . ''])) {
            $post_urltitle = $myts->addSlashes($_POST['url_title' . $dtypeid . '']);
        } else {
            $post_urltitle = '';
        }
        if (isset($_POST['url_link' . $dtypeid . ''])) {
            $post_urllink = $myts->addSlashes($_POST['url_link' . $dtypeid . '']);
        } else {
            $post_urllink = '';
        }
        if ('' !== $post_urllink) {
            $post_value = $post_urllink . '|' . $post_urltitle;
        }
        if (null == $itemid) {
            //That means there was not any value, so a new record should be added to the data table.
            $newid = $xoopsDB->genId($xoopsDB->prefix($helper->getDirname() . '_data') . '_dataid_seq');
            $sql   = sprintf("INSERT INTO %s (dataid, itemid, dtypeid, VALUE, created, customtitle) VALUES (%u, %u, %u, '%s', '%s', '%s')", $xoopsDB->prefix($helper->getDirname() . '_data'), $newid, $post_itemid, $dtypeid, $post_value, time(), $post_customtitle);
            $xoopsDB->query($sql) ; //|| $eh->show('0013');
        } else {
            if ($value != $post_value) {
                $sql = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_data') . " SET value = '$post_value', customtitle = '$post_customtitle' WHERE dtypeid = '$dtypeid' AND itemid = '$post_itemid'";
                $xoopsDB->query($sql) ; //|| $eh->show('0013');
            }
        }
    }
    redirect_header("edit.php?item=$post_itemid", 1, _MD_ITEM_UPDATED);
} else {
    // Prepare page for showing listing edit form.
    if (!empty($_GET['item'])) {
        $get_itemid = (int)$_GET['item'];
        $get_dirid  = getDirIdFromItem($get_itemid);
    } else {
        redirect_header('index.php', 2, _MD_NOVALIDITEM_GET_IDMISSING);
    }

    $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_editlisting.tpl';
    include XOOPS_ROOT_PATH . '/header.php';
    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    $xoopsTpl->assign('lang_submit', _SUBMIT);
    $xoopsTpl->assign('lang_cancel', _CANCEL);

    $sql         = 'SELECT i.itemid, i.logourl, i.uid, i.status, i.created, i.title, i.typeid, t.description FROM '
                   . $xoopsDB->prefix($helper->getDirname() . '_items')
                   . ' i LEFT JOIN '
                   . $xoopsDB->prefix($helper->getDirname() . '_item_text')
                   . ' t ON (i.itemid=t.itemid) WHERE i.itemid='
                   . $get_itemid
                   . '';
    $item_result = $xoopsDB->query($sql);
    $numrows     = $xoopsDB->getRowsNum($item_result);

    while (false !== (list($itemid, $logourl, $submitter, $status, $created, $itemtitle, $typeid, $description) = $xoopsDB->fetchRow($item_result))) {
        $itemtitle = $myts->htmlSpecialChars($itemtitle);
        // Only the submitter or the admin are allowed edit a listing, so make sure
        // all other users are redirected elsewhere.
        if ($isadmin or $submitter == $userid) {
            if ('0' == $status and $submitter == $userid) {
                // Only the submitter can submit listing for approval when status = 0.
                $submit_for_approval_button = "<form action=\"edit.php\" method=\"post\"><input type=\"hidden\" name=\"op\" value=\"submitforapproval\"><input type=\"hidden\" name=\"user\" value=\"$userid\"><input type=\"hidden\" name=\"itemid\" value=\"$get_itemid\"><input type=\"submit\" name=\"submit\" class=\"formButton\" value=\""
                                              . _MD_PUBLISH_LISTING
                                              . '"></form><br>';
                $xoopsTpl->assign('submitview_button', $submit_for_approval_button);
            } elseif (1 == $xoopsModuleConfig['autoapprove']) {
                // If status is not 0 and autoapprove is on, the submitter or
                // admin can edit the listing and with the button "view listing"
                // Go to the listing page in 'view' mode.
                $view_button = '<form action="listing.php" method="get"><input type="hidden" name="item" value="' . $itemid . '"><input type="submit" value="' . _MD_VIEWITEM . '"></input></form><br>';
                $xoopsTpl->assign('submitview_button', $view_button);
            } elseif (!$isadmin) {
                // Only admin is allowed to edit a listing after approval (status = 2)
                // in case autoapprove is off.
                redirect_header('listing.php?item=' . $itemid, 2, _MD_ONLYADMIN_ALLOWED_TO_EDIT);
            }
            if ('' !== $logourl) {
                $picture = "uploads/$logourl";
            } else {
                $picture = 'assets/images/nopicture.gif';
            }
            $sql         = 'SELECT DISTINCT t.dtypeid, t.title, t.section, f.typeid, f.fieldtype, f.ext, t.options, d.itemid, d.value, d.customtitle, t.custom ';
            $sql         .= 'FROM '
                            . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                            . ' ic, '
                            . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
                            . ' xc, '
                            . $xoopsDB->prefix($helper->getDirname() . '_fieldtypes')
                            . ' f, '
                            . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
                            . ' t ';
            $sql         .= 'LEFT JOIN ' . $xoopsDB->prefix($helper->getDirname() . '_data') . ' d ON (t.dtypeid=d.dtypeid AND d.itemid=' . $get_itemid . ') ';
            $sql         .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=" . $get_itemid . '';
            $data_result = $xoopsDB->query($sql) ; //|| $eh->show('0013');
            $numrows     = $xoopsDB->getRowsNum($data_result);

            ob_start();
            $form = new \XoopsThemeForm(_MD_EDITITEM_FORM, 'editform', 'edit.php');
            $form->setExtra('enctype="multipart/form-data"');
            $form->addElement(new \XoopsFormText(_MD_TITLE, 'itemtitle', 50, 250, $itemtitle), true);
            //$categories = getCategoriesPaths($get_itemid);
            $categories = getCatSelectArea($get_itemid, $get_dirid);
            $form_cats  = new \XoopsFormLabel(_MD_ITEMCATEGORIES, "$categories");
            $form->addElement($form_cats);
            $form->addElement(new \XoopsFormDhtmlTextArea(_MD_DESCRIPTION, 'description', $description, 5, 50));
            $form->addElement(new \XoopsFormFile(_MD_SELECT_PIC, 'image', 30000));
            $form->addElement(new Efqdirectory\XoopsFormImage(_MD_CURRENT_PIC, 'current_image', null, "$picture", '', ''));

            while (false !== (list($dtypeid, $title, $section, $ftypeid, $fieldtype, $ext, $options, $itemid, $value, $customtitle, $custom) = $xoopsDB->fetchRow($data_result))) {
                $field = $datafieldmanager->createField($title, $dtypeid, $fieldtype, $ext, $options, $value, $custom, $customtitle);
            }
            $form->addElement(new \XoopsFormButton('', 'submit', _MD_SAVE, 'submit'));
            $form->addElement(new \XoopsFormHidden('op', 'edit'));
            $form->addElement(new \XoopsFormHidden('itemid', $get_itemid));
            $form->addElement(new \XoopsFormHidden('dirid', $get_dirid));
            $form->addElement(new \XoopsFormHidden('ini_itemtitle', $itemtitle));

            if (null != $description) {
                $form->addElement(new \XoopsFormHidden('ini_description', $description));
            }
            $form->addElement(new \XoopsFormHidden('uid', $userid));
            if (null != $description) {
                $form->addElement(new \XoopsFormHidden('description_set', '1'));
            } else {
                $form->addElement(new \XoopsFormHidden('description_set', '0'));
            }
            $form->display();
            $xoopsTpl->assign('dtypes_form', ob_get_contents());
            ob_end_clean();
        }
    }
}
include XOOPS_ROOT_PATH . '/footer.php';

/**
 * @param $locdestid
 * @return int
 */
function GetLevelid($locdestid)
{
    global $xoopsDB;
    $block        = [];
    $myts         = \MyTextSanitizer::getInstance();
    $sql          = 'SELECT locid, levelid FROM ' . $xoopsDB->prefix('dst_loc') . ' WHERE locid = ' . $locdestid . ' ';
    $result2      = $xoopsDB->query($sql);
    $num_results2 = $GLOBALS['xoopsDB']->getRowsNum($result2);
    if (!$result2) {
        return 0;
    }
    for ($i = 0; $i < $num_results2; ++$i) {
        $row2    = $GLOBALS['xoopsDB']->fetchBoth($result2);
        $levelid = $row2['levelid'];
    }

    return $levelid;
}

/**
 * @param $locdestid
 * @return int
 */
function GetLocName($locdestid)
{
    global $xoopsDB;
    $block       = [];
    $myts        = \MyTextSanitizer::getInstance();
    $sql         = 'SELECT locid, name FROM ' . $xoopsDB->prefix('dst_loc') . ' WHERE locid = ' . $locdestid . ' ';
    $result      = $xoopsDB->query($sql);
    $num_results = $GLOBALS['xoopsDB']->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row     = $GLOBALS['xoopsDB']->fetchBoth($result);
        $locname = $myts->htmlSpecialChars($row['name']);
    }

    return $locname;
}

include XOOPS_ROOT_PATH . '/footer.php';
