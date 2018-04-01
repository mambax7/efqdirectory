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
/** @var Efqdirectory\Helper $helper */
$helper = Efqdirectory\Helper::getInstance();
$myts = \MyTextSanitizer::getInstance(); // MyTextSanitizer object
// require_once __DIR__ . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
//require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
// require_once __DIR__ . '/class/class.datafieldmanager.php';
// require_once __DIR__ . '/class/class.couponhandler.php';
// require_once __DIR__ . '/class/class.efqtree.php';

$datafieldmanager = new Efqdirectory\DataFieldManager();
$moddir           = $xoopsModule->getVar('dirname');


$mytree  = new Efqdirectory\MyXoopsTree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');
$efqtree = new Efqdirectory\Tree($xoopsDB->prefix($helper->getDirname() . '_cat'), 'cid', 'pid');

//Check if any option in URL
if (!empty($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = 0;
}

//Check if a category is selected.
if (!empty($_GET['catid'])) {
    $get_catid = (int)$_GET['catid'];
} else {
    $get_catid = '0';
}

//Check if a directory is selected.
if (!empty($_GET['dirid'])) {
    $get_dirid = (int)$_GET['dirid'];
} else {
    $get_dirid = '0';
}

if ('0' == $get_dirid && '0' == $get_catid) {
    //Show an overview of directories with directory title, image and description for each directory.
    $sql         = 'SELECT dirid, name, descr, img FROM ' . $xoopsDB->prefix($helper->getDirname() . '_dir') . " WHERE open = '1' ORDER BY name";
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $num_results = $xoopsDB->getRowsNum($result);
    if (1 == $num_results) {
        if (1 == $helper->getConfig('autoshowonedir')) {
            while (false !== (list($dirid, $name, $descr, $img) = $xoopsDB->fetchRow($result))) {
                $get_dirid = $dirid;
            }
        } else {
            $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_directories.tpl';
            include XOOPS_ROOT_PATH . '/header.php';
            $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
            $xoopsTpl->assign('lang_directories', _MD_DIRECTORIES_HEADER);
            $xoopsTpl->assign('moddir', $moddir);
            while (false !== (list($dirid, $name, $descr, $img) = $xoopsDB->fetchRow($result))) {
                if ('' != $img) {
                    $img = XOOPS_URL . "/modules/$moddir/uploads/" . $myts->htmlSpecialChars($img);
                } else {
                    $img = XOOPS_URL . "/modules/$moddir/assets/images/nopicture.gif";
                }
                $xoopsTpl->append('directories', ['image' => $img, 'id' => $dirid, 'title' => $name, 'descr' => $descr]);
            }
        }
    } elseif ($num_results >= 2) {
        $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_directories.tpl';
        include XOOPS_ROOT_PATH . '/header.php';
        $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
        $xoopsTpl->assign('lang_directories', _MD_DIRECTORIES_HEADER);
        $xoopsTpl->assign('moddir', $moddir);
        while (false !== (list($dirid, $name, $descr, $img) = $xoopsDB->fetchRow($result))) {
            if ('' != $img) {
                $img = XOOPS_URL . "/modules/$moddir/uploads/" . $myts->htmlSpecialChars($img);
            } else {
                $img = XOOPS_URL . "/modules/$moddir/assets/images/nopicture.gif";
            }
            $xoopsTpl->append('directories', ['image' => $img, 'id' => $dirid, 'title' => $name, 'descr' => $descr]);
        }
    } else {
        redirect_header(XOOPS_URL, 2, _MD_NOACTIVEDIRECTORIES);
    }
}
if (0 != $get_dirid || 0 != $get_catid) {
    //Get all categories and child categories for selected category
    $GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_index.tpl';
    include XOOPS_ROOT_PATH . '/header.php';
    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    $xoopsTpl->assign('moddir', $moddir);
    if ('0' == $get_dirid) {
        $dirid = getDirId($get_catid);
    } else {
        $dirid = $get_dirid;
    }
    ob_start();
    printf(_MD_SEARCH_ADV, $dirid);
    $lang_adv_search = ob_get_contents();
    ob_end_clean();
    $searchform = '<form action="search.php" name="search" id="search" method="get">';
    $searchform .= '<input type="hidden" name="dirid" value="' . $dirid . '"><input type="text" name="q" size="40" maxsize="150" value=""><input type="submit" class="formButton" name="submit" value="' . _MD_SEARCH . '">' . $lang_adv_search . '</form>';
    $xoopsTpl->assign('searchform', $searchform);
    $pathstring = "<a href='index.php?dirid=" . $dirid . '\'>' . _MD_MAIN . '</a>&nbsp;:&nbsp;';
    $pathstring .= $efqtree->getNicePathFromId($get_catid, 'title', 'index.php?dirid=' . $dirid . '&op=');
    $xoopsTpl->assign('category_path', $pathstring);

    if (isset($xoopsUser) && null !== $xoopsUser) {
        $submitlink = '<a href="submit.php?dirid=' . $dirid . '"><img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/' . $xoopsConfig['language'] . '/listing-new.gif" alt="' . _MD_SUBMITLISTING . '" title="' . _MD_SUBMITLISTING . '"></a>';
        $xoopsTpl->assign('submit_link', $submitlink);
    }

    if (0 == $get_catid) {
        $sql = 'SELECT cid, title, img FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE pid = '0' AND dirid = '" . $get_dirid . '\' ORDER BY title' ;
    } else {
        $sql = 'SELECT cid, title, img FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE pid = '" . $get_catid . '\' ORDER BY title' ;
    }
    $result = $xoopsDB->query($sql);
    if (!$result) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $num_results = $GLOBALS['xoopsDB']->getRowsNum($result);
    if (0 == $num_results && isset($_GET['dirid'])) {
        $xoopsTpl->assign('lang_noresults', _MD_NORESULTS);
    } else {
        $count = 1;
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $totallisting = getTotalItems($myrow['cid'], 2);
            $img          = '';
            if ($myrow['img'] && '' != $myrow['img']) {
                $img = XOOPS_URL . "/modules/$moddir/uploads/" . $myts->htmlSpecialChars($myrow['img']);
            }
            $arr           = [];
            $arr           = $efqtree->getFirstChild($myrow['cid'], 'title');
            $space         = 0;
            $chcount       = 0;
            $subcategories = '';
            foreach ($arr as $ele) {
                $chtitle = $myts->htmlSpecialChars($ele['title']);
                if ($chcount > 5) {
                    $subcategories .= '...';
                    break;
                }
                if ($space > 0) {
                    $subcategories .= ', ';
                }
                $subcategories .= '<a class="subcategory" href="' . XOOPS_URL . "/modules/$moddir/index.php?catid=" . $ele['cid'] . '">' . $chtitle . '</a>';
                ++$space;
                ++$chcount;
            }
            $cattitle = '<a href="' . XOOPS_URL . "/modules/$moddir/index.php?catid=" . $myrow['cid'] . '">' . $myrow['title'] . '</a>';
            $xoopsTpl->append('categories', ['image' => $img, 'id' => $myrow['cid'], 'title' => $cattitle, 'subcategories' => $subcategories, 'totallisting' => $totallisting, 'count' => $count]);
            ++$count;
        }

        if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
            $isadmin = true;
        } else {
            $isadmin = false;
        }
        /*if ($helper->getConfig('allowcomments') == 1) {
        $xoopsTpl->assign('allowcomments', true);
        $xoopsTpl->assign('lang_comments' , _COMMENTS);
        }
        if ($helper->getConfig('allowreviews') == 1) {
        $xoopsTpl->assign('allowreviews', true);
        }*/
        if (1 == $helper->getConfig('allowtellafriend')) {
            $xoopsTpl->assign('allowtellafriend', true);
            $xoopsTpl->assign('lang_tellafriend', _MD_TELLAFRIEND);
        }
        if (1 == $helper->getConfig('allowrating')) {
            $xoopsTpl->assign('allowrating', true);
            $xoopsTpl->assign('lang_rating', _MD_RATINGC);
            $xoopsTpl->assign('lang_ratethissite', _MD_RATETHISSITE);
        }
        $xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
        $xoopsTpl->assign('lang_lastupdate', _MD_LASTUPDATEC);
        $xoopsTpl->assign('lang_hits', _MD_HITSC);
        $xoopsTpl->assign('lang_modify', _MD_MODIFY);
        $xoopsTpl->assign('lang_listings', _MD_LATESTLIST);
        $xoopsTpl->assign('lang_category', _MD_CATEGORYC);
        $xoopsTpl->assign('lang_visit', _MD_VISIT);
        $sections = [];
        if (0 == $get_catid) {
            $sql    = 'SELECT l.itemid, l.logourl, l.uid, l.status, l.created, l.title, l.hits, l.rating, l.votes, l.typeid, l.dirid, t.description FROM '
                      . $xoopsDB->prefix($helper->getDirname() . '_cat')
                      . ' c, '
                      . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                      . ' x, '
                      . $xoopsDB->prefix($helper->getDirname() . '_items')
                      . ' l LEFT JOIN '
                      . $xoopsDB->prefix($helper->getDirname() . '_item_text')
                      . " t ON (l.itemid=t.itemid) WHERE x.cid=c.cid AND l.itemid=x.itemid AND c.showpopular=1 AND l.status='2' AND l.dirid = '"
                      . $get_dirid
                      . '\' ORDER BY l.created DESC';
            $result = $xoopsDB->query($sql);
            if (!$result) {
                $logger = \XoopsLogger::getInstance();
                $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
            }

            while (false !== (list($itemid, $logourl, $uid, $status, $created, $ltitle, $hits, $rating, $votes, $type, $dirid, $description) = $xoopsDB->fetchRow($result))) {
                if ($isadmin) {
                    $adminlink = '<a href="' . XOOPS_URL . '/modules/' . $moddir . '/admin/index.php?op=edit&amp;item=' . $itemid . '"><img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/editicon.gif" border="0" alt="' . _MD_EDITTHISLINK . '"></a>';
                } else {
                    $adminlink = '';
                }
                if (1 == $votes) {
                    $votestring = _MD_ONEVOTE;
                } else {
                    $votestring = sprintf(_MD_NUMVOTES, $votes);
                }

                if ('1' == $helper->getConfig('showdatafieldsincat')) {
                    $xoopsTpl->assign('showdatafieldsincat', true);
                    $sql         = 'SELECT DISTINCT t.dtypeid, t.title, t.section, t.icon, f.typeid, f.fieldtype, f.ext, t.options, t.custom, d.itemid, d.value, d.customtitle ';
                    $sql         .= 'FROM '
                                    . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                                    . ' ic, '
                                    . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
                                    . ' xc, '
                                    . $xoopsDB->prefix($helper->getDirname() . '_fieldtypes')
                                    . ' f, '
                                    . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
                                    . ' t ';
                    $sql         .= 'LEFT JOIN ' . $xoopsDB->prefix($helper->getDirname() . '_data') . ' d ON (t.dtypeid=d.dtypeid AND d.itemid=' . $itemid . ') ';
                    $sql         .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=" . $itemid . ' ORDER BY t.seq ASC';
                    $data_result = $xoopsDB->query($sql) ;
                    if (!$data_result) {
                        $logger = \XoopsLogger::getInstance();
                        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
                    }
                    $numrows     = $xoopsDB->getRowsNum($data_result);
                    if ($numrows > 0) {
                        $xoopsTpl->assign('datatypes', true);
                    }
                    $sections = [];
                    while (false !== (list($dtypeid, $title, $section, $icon, $ftypeid, $fieldtype, $ext, $options, $custom, $ditemid, $value, $customtitle) = $xoopsDB->fetchRow($data_result))) {
                        $fieldvalue = $datafieldmanager->getFieldValue($fieldtype, $options, $value);
                        if ('' != $icon) {
                            $iconurl = "<img src=\"uploads/$icon\">";
                        } else {
                            $iconurl = '';
                        }
                        if ('0' != $custom && '' != $customtitle) {
                            $title = $customtitle;
                        }
                        if ('0' == $section or '1') {
                            $sections[] = ['icon' => $iconurl, 'label' => $title, 'value' => $fieldvalue, 'fieldtype' => $fieldtype];
                        }
                    }
                }

                $couponHandler = new Efqdirectory\CouponHandler();
                $coupons       = $couponHandler->getCountByLink($itemid);
                $path          = $efqtree->getPathFromId($get_catid, 'title');
                $path          = substr($path, 1);
                $path          = str_replace('/', " <img src='" . XOOPS_URL . '/modules/' . $moddir . "/assets/images/arrow.gif' board='0' alt=''> ", $path);
                $new           = newlinkgraphic($created, $status);
                $pop           = popgraphic($hits);
                $xoopsTpl->append('listings', [
                    'fields'       => $sections,
                    'coupons'      => $coupons,
                    'catid'        => $get_catid,
                    'id'           => $itemid,
                    'rating'       => number_format($rating, 2),
                    'title'        => $myts->htmlSpecialChars($ltitle) . $pop,
                    'type'         => $type,
                    'logourl'      => $myts->htmlSpecialChars($logourl),
                    'description'  => $myts->displayTarea($description, 0),
                    'adminlink'    => $adminlink,
                    'hits'         => $hits,
                    'votes'        => $votestring,
                    'mail_subject' => rawurlencode(sprintf(_MD_INTERESTING_LISTING, $xoopsConfig['sitename'])),
                    'mail_body'    => rawurlencode(sprintf(_MD_INTERESTING_LISTING_FOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/' . $moddir . '/singleitem.php?cid=' . $get_catid . '&amp;item=' . $itemid)
                ]);
            }
        } else {
            if (isset($_GET['show'])) {
                $show = (int)$_GET['show'];
            } else {
                $show = $helper->getConfig('perpage');
            }
            $min = \Xmf\Request::getInt('min', 0, 'GET');
            if (!isset($max)) {
                $max = $min + $show;
            }
            if (isset($_GET['orderby'])) {
                $orderby = convertOrderByIn($_GET['orderby']);
            } else {
                $orderby = 'typelevel DESC';
            }
            $fullcountresult = $xoopsDB->query('SELECT count(*) FROM ' . $xoopsDB->prefix($helper->getDirname() . '_items') . ' i, ' . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat') . " x WHERE i.itemid=x.itemid AND x.cid=$get_catid AND i.status='2'");
            list($numrows) = $xoopsDB->fetchRow($fullcountresult);
            $totalcount = $numrows;
            $page_nav   = '';
            if ($numrows > 0) {
                /*if ($helper->getConfig('allowcomments') == 1) {
                $xoopsTpl->assign('allowcomments', true);
                $xoopsTpl->assign('lang_comments' , _COMMENTS);
                }*/
                /*if ($helper->getConfig('allowreviews') == 1) {
                $xoopsTpl->assign('allowreviews', true);
                }*/
                if (1 == $helper->getConfig('allowtellafriend')) {
                    $xoopsTpl->assign('allowtellafriend', true);
                    $xoopsTpl->assign('lang_tellafriend', _MD_TELLAFRIEND);
                }
                if (1 == $helper->getConfig('allowrating')) {
                    $xoopsTpl->assign('allowrating', true);
                    $xoopsTpl->assign('lang_rating', _MD_RATINGC);
                    $xoopsTpl->assign('lang_ratethissite', _MD_RATETHISSITE);
                }
                $xoopsTpl->assign('lang_listings', _MD_LISTINGS);
                $xoopsTpl->assign('category_id', $get_catid);
                $xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
                $xoopsTpl->assign('lang_lastupdate', _MD_LASTUPDATEC);
                $xoopsTpl->assign('lang_hits', _MD_HITSC);
                $xoopsTpl->assign('lang_modify', _MD_MODIFY);
                $xoopsTpl->assign('lang_category', _MD_CATEGORYC);
                $xoopsTpl->assign('lang_visit', _MD_VISIT);
                $xoopsTpl->assign('show_listings', true);
                $sql     = 'SELECT i.itemid, i.logourl, i.uid, i.status, i.created, i.title, i.hits, i.rating, i.votes, i.typeid, i.dirid, t.typelevel, txt.description, x.cid FROM '
                           . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                           . ' x, '
                           . $xoopsDB->prefix($helper->getDirname() . '_items')
                           . ' i LEFT JOIN '
                           . $xoopsDB->prefix($helper->getDirname() . '_itemtypes')
                           . ' t ON (t.typeid=i.typeid) LEFT JOIN '
                           . $xoopsDB->prefix($helper->getDirname() . '_item_text')
                           . " txt ON (txt.itemid=i.itemid) WHERE i.itemid=x.itemid AND x.cid=$get_catid AND x.active='1' AND i.status='2' ORDER BY $orderby";
                $result  = $xoopsDB->query($sql, $show, $min) ;
                if (!$result) {
                    $logger = \XoopsLogger::getInstance();
                    $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
                }
                $numrows = $xoopsDB->getRowsNum($result);

                //if 2 or more items in result, show the sort menu
                if ($numrows > 1) {
                    $xoopsTpl->assign('show_nav', true);
                    $orderbyTrans = convertorderbytrans($orderby);
                    $xoopsTpl->assign('lang_sortby', _MD_SORTBY);
                    $xoopsTpl->assign('lang_title', _MD_TITLE);
                    $xoopsTpl->assign('lang_date', _MD_DATE);
                    $xoopsTpl->assign('lang_rating', _MD_RATING);
                    $xoopsTpl->assign('lang_popularity', _MD_POPULARITY);
                    if ('typelevel DESC' !== $orderby) {
                        $xoopsTpl->assign('lang_cursortedby', sprintf(_MD_CURSORTEDBY, convertorderbytrans($orderby)));
                    }
                }
                if (1 == $helper->getConfig('showlinkimages')) {
                    $xoopsTpl->assign('showlinkimages', 1);
                }
                while (false !== (list($itemid, $logourl, $uid, $status, $created, $itemtitle, $hits, $rating, $votes, $typeid, $dirid, $level, $description, $cid) = $xoopsDB->fetchRow($result))) {
                    if ($isadmin) {
                        if (1 == $helper->getConfig('showlinkimages')) {
                            $adminlink = '<a href="' . XOOPS_URL . '/modules/' . $moddir . '/admin/index.php?op=edit&amp;item=' . $itemid . '"><img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/editicon.gif" border="0" alt="' . _MD_EDITTHISLISTING . '"></a>';
                        } else {
                            $adminlink = '';
                        }
                    } else {
                        $adminlink = '';
                    }
                    if (1 == $votes) {
                        $votestring = _MD_ONEVOTE;
                    } else {
                        $votestring = sprintf(_MD_NUMVOTES, $votes);
                    }

                    if ('1' == $helper->getConfig('showdatafieldsincat')) {
                        $xoopsTpl->assign('showdatafieldsincat', true);
                        $sql         = 'SELECT DISTINCT t.dtypeid, t.title, t.section, t.icon, f.typeid, f.fieldtype, f.ext, t.options, t.custom, d.itemid, d.value, d.customtitle ';
                        $sql         .= 'FROM '
                                        . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                                        . ' ic, '
                                        . $xoopsDB->prefix($helper->getDirname() . '_dtypes_x_cat')
                                        . ' xc, '
                                        . $xoopsDB->prefix($helper->getDirname() . '_fieldtypes')
                                        . ' f, '
                                        . $xoopsDB->prefix($helper->getDirname() . '_dtypes')
                                        . ' t ';
                        $sql         .= 'LEFT JOIN ' . $xoopsDB->prefix($helper->getDirname() . '_data') . ' d ON (t.dtypeid=d.dtypeid AND d.itemid=' . $itemid . ') ';
                        $sql         .= "WHERE ic.cid=xc.cid AND ic.active='1' AND xc.dtypeid=t.dtypeid AND t.fieldtypeid=f.typeid AND t.activeyn='1' AND ic.itemid=" . $itemid . ' ORDER BY t.seq ASC';
                        $data_result = $xoopsDB->query($sql) ;
                        if (!$data_result) {
                            $logger = \XoopsLogger::getInstance();
                            $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
                        }
                        $numrows     = $xoopsDB->getRowsNum($data_result);
                        if ($numrows > 0) {
                            $xoopsTpl->assign('datatypes', true);
                        }
                        $sections = [];
                        while (false !== (list($dtypeid, $title, $section, $icon, $ftypeid, $fieldtype, $ext, $options, $custom, $ditemid, $value, $customtitle) = $xoopsDB->fetchRow($data_result))) {
                            $fieldvalue = $datafieldmanager->getFieldValue($fieldtype, $options, $value);
                            if ('' != $icon) {
                                $iconurl = "<img src=\"uploads/$icon\">";
                            } else {
                                $iconurl = '';
                            }
                            if ('0' != $custom && '' != $customtitle) {
                                $title = $customtitle;
                            }
                            if ('1' == $section or '1') {
                                $sections[] = ['icon' => $iconurl, 'label' => $title, 'value' => $fieldvalue, 'fieldtype' => $fieldtype];
                            }
                        }
                    }

                    $path = $efqtree->getPathFromId($get_catid, 'title');
                    $path = substr($path, 1);
                    $path = str_replace('/', " <img src='" . XOOPS_URL . '/modules/' . $moddir . "/assets/images/arrow.gif' board='0' alt=''> ", $path);
                    $new  = newlinkgraphic($created, $status);
                    $pop  = popgraphic($hits);
                    if (null === $level) {
                        $level = '0';
                    }
                    switch ($level) {
                        case '0':
                            $class = 'itemTableLevel0';
                            break;
                        case '1':
                            $class = 'itemTableLevel1';
                            break;
                        case '2':
                            $class = 'itemTableLevel2';
                            break;
                        case '3':
                            $class = 'itemTableLevel3';
                            break;
                    }
                    $xoopsTpl->append('listings', [
                        'fields'       => $sections,
                        'id'           => $itemid,
                        'catid'        => $get_catid,
                        'logourl'      => $myts->htmlSpecialChars($logourl),
                        'title'        => $myts->htmlSpecialChars($itemtitle) . $new . $pop,
                        'status'       => $status,
                        'created'      => formatTimestamp($created, 'm'),
                        'rating'       => number_format($rating, 2),
                        'category'     => $path,
                        'description'  => $myts->displayTarea($description, 0),
                        'adminlink'    => $adminlink,
                        'hits'         => $hits,
//                        'rating'       => $rating,
                        'votes'        => $votestring,
                        'class'        => $class,
                        'mail_subject' => rawurlencode(sprintf(_MD_INTERESTING_LISTING, $xoopsConfig['sitename'])),
                        'mail_body'    => rawurlencode(sprintf(_MD_INTERESTING_LISTING_FOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/' . $moddir . '/listing.php?catid=' . $get_catid . '&amp;item=' . $itemid)
                    ]);
                }
                $orderby = convertorderbyout($orderby);
                //Calculates how many pages exist.  Which page one should be on, etc...
                $listingpages = ceil($totalcount / $show);

                //Page Numbering
                if (1 != $listingpages && 0 != $listingpages) {
                    $get_catid = (int)$_GET['catid'];
                    $prev      = $min - $show;
                    if ($prev >= 0) {
                        $page_nav .= "<a href='index.php?catid=" . $get_catid . "&amp;min=$prev&amp;orderby=$orderby&amp;show=$show'><b><u>&laquo;</u></b></a>&nbsp;";
                    }
                    $counter     = 1;
                    $currentpage = ($max / $show);
                    while ($counter <= $listingpages) {
                        $mintemp = ($show * $counter) - $show;
                        if ($counter == $currentpage) {
                            $page_nav .= '<strong>(' . $counter . ')</strong>&nbsp;';
                        } else {
                            $page_nav .= "<a href='index.php?catid=" . $get_catid . '&amp;min=' . $mintemp . '&amp;orderby=' . $orderby . '&amp;show=' . $show . '\'>' . $counter . '</a>&nbsp;';
                        }
                        ++$counter;
                    }
                    if ($numrows > $max) {
                        $page_nav .= "<a href='index.php?catid=" . $get_catid . '&amp;min=' . $max . '&amp;orderby=' . $orderby . '&amp;show=' . $show . '\'>';
                        $page_nav .= '<strong><u>&raquo;</u></strong></a>';
                    }
                    $xoopsTpl->assign('page_nav', $page_nav);
                }
            }
        }
    }
}
include XOOPS_ROOT_PATH . '/footer.php';
