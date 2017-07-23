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

include __DIR__ . '/header.php';
//Include XOOPS classes
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
//Include module classes
require_once __DIR__ . '/class/class.formimage.php';
require_once __DIR__ . '/class/class.image.php';
require_once __DIR__ . '/class/class.efqtree.php';
require_once __DIR__ . '/class/class.datafieldmanager.php';

$myts             = MyTextSanitizer::getInstance();
$eh               = new ErrorHandler;
$datafieldmanager = new efqDataFieldManager();

$moddir = $xoopsModule->getVar('dirname');

if (isset($_GET['catid'])) {
    $get_cid = (int)$_GET['cid'];
} else {
    $get_cid = '1';
}
if (isset($_GET['dirid'])) {
    $get_dirid = (int)$_GET['dirid'];
} elseif (isset($_POST['dirid'])) {
    $get_dirid = (int)$_POST['dirid'];
}
if (isset($_GET['orderby'])) {
    $orderby = convertOrderByIn($_GET['orderby']);
} else {
    $orderby = 'title ASC';
}
if (isset($_GET['page'])) {
    $get_page = (int)$_GET['page'];
} else {
    $get_page = 1;
}

$GLOBALS['xoopsOption']['template_main'] = 'efqdiralpha1_search.tpl';
include XOOPS_ROOT_PATH . '/header.php';
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
//The advanced search is within a directory
//First get an array of all datatypes within a directory that has listings linked to them
if (isset($_POST['submit'])) {
    $sql         = 'SELECT DISTINCT dt.dtypeid, dt.title, dt.options, ft.fieldtype FROM '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_fieldtypes')
                   . ' ft, '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_dtypes')
                   . ' dt, '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n')
                                      . '_dtypes_x_cat')
                   . ' dxc, '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_cat')
                   . " c WHERE c.cid=dxc.cid AND dt.dtypeid=dxc.dtypeid AND dt.fieldtypeid=ft.typeid AND c.dirid='$get_dirid' AND dt.activeyn='1'";
    $result      = $xoopsDB->query($sql) || $eh->show('0013');
    $num_results = $xoopsDB->getRowsNum($result);
    $filter_arr  = array();
    while (list($dtypeid, $dtypetitle, $dtypeoptions, $fieldtype) = $xoopsDB->fetchRow($result)) {
        switch ($fieldtype) {
            case 'textbox':
                $filter_arr[] = getPostedValue_text($dtypeid);
                break;
            case 'textarea':
                $filter_arr[] = getPostedValue_text($dtypeid);
                break;
            case 'yesno':
                $filter_arr[] = getPostedValue_text($dtypeid);
                break;
            case 'radio':
                $filter_arr[] = getPostedValue_text($dtypeid);
                break;
            case 'select':
                $selectarr = getPostedValue_array($dtypeid);
                $count_arr = count($selectarr);

                if ($count_arr >= 1) {
                    foreach ($selectarr as $arr) {
                        $selectarray[] = $arr['postvalue'];
                    }
                    $filter_arr[] = array('dtypeid' => $dtypeid, 'constr' => 'equal', 'postvalue' => $selectarr['postvalue'], 'selectfields' => $selectarray, 'addressfields' => 0);
                }
                break;
            case 'dhtml':
                $filter_arr[] = getPostedValue_text($dtypeid);
                break;
            case 'address':
                $addressarr   = getPostedValue_address($dtypeid);
                $addressarray = array();
                //$countarr = count($address
                if (is_array($addressarray) && count($addressarray) >= 1) {
                    foreach ($addressarr as $arr) {
                        $addressarray[] = array($arr['field'] => $arr['postvalue']);
                    }
                    $filter_arr[] = array('dtypeid' => $dtypeid, 'constr' => 'equal', 'postvalue' => $arr['postvalue'], 'selectfields' => 0, 'addressfields' => $addressarray);
                }
                break;
            case 'rating':
                $selectarr = getPostedValue_array($dtypeid);
                //$selectarr = getPostedValue_text($dtypeid);
                $count_arr = count($selectarr);

                if ($count_arr >= 1) {
                    foreach ($selectarr as $arr) {
                        $selectarray[] = $arr;
                    }
                    //echo $selectarr['postvalue'];
                    $filter_arr[] = array('dtypeid' => $dtypeid, 'constr' => $selectarr['constr'], 'postvalue' => $selectarr['postvalue'], 'selectfields' => '', 'addressfields' => 0);
                }
                break;
            case 'date':
                $filter_arr[] = getPostedValue_text($dtypeid);
                break;
            default:
                break;
        }
    }
    if (isset($_POST['q'])) {
        $querystring = $GLOBALS['xoopsDB']->escape($myts->stripSlashesGPC($_POST['q']));
    } else {
        $querystring = '';
    }
    $poscount   = substr_count($querystring, '"') / 2;
    $specialarr = array();
    for ($i = 0; $i < $poscount; ++$i) {
        $start = strpos($querystring, '"', 0);
        $end   = strpos($querystring, '"', $start + 1);
        if ($end !== false) {
            $specialstring = ltrim(substr($querystring, $start, $end - $start), '"');
            $specialarr[]  = $specialstring;
            $querystring   = ltrim(substr_replace($querystring, '', $start, $end - $start + 1));
        } else {
            $querystring = ltrim(substr_replace($querystring, '', $start, 1));
        }
    }
    $queryarr   = explode(' ', $querystring);
    $queryarr   = array_merge($specialarr, $queryarr);
    $emptyarr[] = '';
    $querydiff  = array_diff($queryarr, $emptyarr);

    $limit  = $xoopsModuleConfig['searchresults_perpage'];
    $offset = ($get_page - 1) * $limit;

    $andor         = 'AND';
    $searchresults = mod_search($querydiff, $andor, $limit, $offset, $filter_arr);

    $maxpages      = 10;
    $maxcount      = 30;
    $count_results = count($searchresults);
    $count_pages   = 0;

    $items_arr_text = '';
    //Calculate the number of result pages.
    if ($count_results > $limit) {
        $count_pages = ceil($count_results / $limit);
    } else {
        $count_pages = 1;
    }
    $pages_text = sprintf(_MD_LISTINGS_FOUND_ADV, $count_results);

    if ($count_pages >= 2) {
        $searchnum  = uniqid(mt_rand(), 1);
        $pages_text .= '' . _MD_PAGES . '<a href="advancedsearch.php?ref=' . $searchnum . '&page=1&dirid=' . $get_dirid . '">1</a>';
    }
    for ($i = 1; $i < $count_pages; ++$i) {
        $page       = $i + 1;
        $pages_text .= ' - <a href="advancedsearch.php?ref=' . $searchnum . '&page=' . $page . '&dirid=' . $get_dirid . '">' . $page . '</a>';
    }
    $pages_text     .= sprintf(_MD_NEW_SEARCH_ADV, $get_dirid);
    $pages_top_text = sprintf(_MD_PAGE_OF_PAGES, $get_page, $count_pages) . '<br><br>';

    ob_start();
    echo '<div class="itemTitleLarge">' . _MD_SEARCHRESULTS_TITLE . '</div><br>';
    echo $pages_top_text;
    if ($searchresults == 0) {
        echo '<div class="itemTitle">' . _MD_NORESULTS . '</div>';
    } else {
        $y    = 0;
        $page = 1;
        foreach ($searchresults as $result) {
            if ($y < $limit && $page == 1) {
                echo '<div class="itemTitle"><a href="' . $result['link'] . '">' . $result['title'] . '</a></div><div class="itemText">' . $result['description'] . '</div><hr>';
                if ($y < $limit && $y > 0) {
                    $items_arr_text .= ',' . $result['itemid'] . '';
                } else {
                    $items_arr_text = $result['itemid'];
                }
                ++$y;
            } elseif ($y < $limit && $y > 0) {
                $items_arr_text .= ',' . $result['itemid'] . '';
                ++$y;
            } elseif ($y < $limit && $y == 0) {
                $items_arr_text = $result['itemid'];
                ++$y;
            } elseif ($y == $limit) {
                //Save $items_arr_text and $page into DB
                $newid = $xoopsDB->genId($xoopsDB->prefix($module->getVar('dirname', 'n') . '_searchresults') . '_searchid_seq');
                $sql   = sprintf("INSERT INTO %s (searchid, searchnum, created, PAGE, items, dirid, catid) VALUES (%u, '%s', '%s', %u, '%s', %u, %u)", $xoopsDB->prefix($module->getVar('dirname', 'n') . '_searchresults'), $newid, $searchnum, time(), $page, $items_arr_text, $get_dirid, $get_cid);
                $xoopsDB->query($sql) || $eh->show('0013');
                if ($newid == 0) {
                    $itemid = $xoopsDB->getInsertId();
                }
                $items_arr_text = $result['itemid'];
                $y              = 1;
                ++$page;
            }
        }
        if ($y != 0 && $page > 1) {
            $newid = $xoopsDB->genId($xoopsDB->prefix($module->getVar('dirname', 'n') . '_searchresults') . '_searchid_seq');
            $sql   = sprintf("INSERT INTO %s (searchid, searchnum, created, PAGE, items, dirid, catid) VALUES (%u, '%s', '%s', %u, '%s', %u, %u)", $xoopsDB->prefix($module->getVar('dirname', 'n') . '_searchresults'), $newid, $searchnum, time(), $page, $items_arr_text, $get_dirid, $get_cid);
            $xoopsDB->query($sql) || $eh->show('0013');
            if ($newid == 0) {
                $itemid = $xoopsDB->getInsertId();
            }
        }
    }
    echo '<br>';
    echo $pages_text;

    $xoopsTpl->assign('search_page', ob_get_contents());
    ob_end_clean();
} elseif (isset($_GET['ref']) && isset($_GET['page'])) {
    $get_searchnum = $GLOBALS['xoopsDB']->escape($myts->stripSlashesGPC($_GET['ref']));
    $get_page      = (int)$_GET['page'];

    //Query the saved results from the DB.
    $sql = 'SELECT searchid, searchnum, created, page, items, dirid, catid FROM ' . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_searchresults') . " WHERE searchnum='$get_searchnum' AND page='$get_page'";

    $result      = $xoopsDB->query($sql) || $eh->show('0013');
    $num_results = $xoopsDB->getRowsNum($result);
    while (list($searchid, $searchnum, $created, $page, $items, $dirid, $catid) = $xoopsDB->fetchRow($result)) {
        //Split items and for each item, get item data.
        if ($items != '') {
            $searchresults = get_search_results($items);
        } else {
            $searchresults = 0;
        }
    }
    $maxpages      = 10;
    $maxcount      = 30;
    $limit         = $xoopsModuleConfig['searchresults_perpage'];
    $count_results = getNumberOfResults($get_searchnum, $limit);
    $count_pages   = 0;

    $items_arr_text = '';

    $offset = ($get_page - 1) * $limit;
    //Calculate the number of result pages.
    if ($count_results > $limit) {
        $count_pages = ceil($count_results / $limit);
    }

    ob_start();
    printf(_MD_LISTINGS_FOUND_ADV, $count_results);
    $pages_text = ob_get_contents();
    ob_end_clean();

    ob_start();
    printf(_MD_PAGE_OF_PAGES, $get_page, $count_pages) . '<br><br>';
    $pages_top_text = ob_get_contents();
    ob_end_clean();

    if ($count_pages >= 2) {
        $pages_text .= '' . _MD_PAGES . '<a href="advancedsearch.php?ref=' . $get_searchnum . '&page=1&dirid=' . $get_dirid . '">1</a>';
    }
    for ($i = 1; $i < $count_pages; ++$i) {
        $page       = $i + 1;
        $pages_text .= ' - <a href="advancedsearch.php?ref=' . $get_searchnum . '&page=' . $page . '&dirid=' . $get_dirid . '">' . $page . '</a><br>';
    }
    ob_start();
    printf(_MD_NEW_SEARCH_ADV, $get_dirid);
    $pages_text .= ob_get_contents();
    ob_end_clean();

    ob_start();
    echo '<div class="itemTitleLarge">' . _MD_SEARCHRESULTS_TITLE . '</div><br>';
    echo $pages_top_text;
    if ($searchresults == 0) {
        echo '<div class="itemTitle">' . _MD_NORESULTS . '</div>';
    } else {
        $y    = 0;
        $page = 1;
        foreach ($searchresults as $result) {
            echo '<div class="itemTitle"><a href="' . $result['link'] . '">' . $result['title'] . '</a></div><div class="itemText">' . $result['description'] . '</div><hr>';
        }
    }
    echo $pages_text;
    $xoopsTpl->assign('search_page', ob_get_contents());
    ob_end_clean();
} else {
    //No search was posted nor was a search page requested.
    //A search form is generated from the datatype fields in the chosen directory.
    $sql         = 'SELECT DISTINCT dt.dtypeid, dt.title, dt.options, ft.fieldtype FROM '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_fieldtypes')
                   . ' ft, '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_dtypes')
                   . ' dt, '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n')
                                      . '_dtypes_x_cat')
                   . ' dxc, '
                   . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_cat')
                   . " c WHERE c.cid=dxc.cid AND dt.dtypeid=dxc.dtypeid AND dt.fieldtypeid=ft.typeid AND c.dirid='$get_dirid' AND dt.activeyn='1'";
    $result      = $xoopsDB->query($sql) || $eh->show('0013');
    $num_results = $xoopsDB->getRowsNum($result);

    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
    ob_start();
    $form = new XoopsThemeForm(_MD_ADVSEARCH_FORM, 'advsearchform', 'advancedsearch.php');
    $form->setExtra('enctype="multipart/form-data"');
    while (list($dtypeid, $dtypetitle, $dtypeoptions, $fieldtype) = $xoopsDB->fetchRow($result)) {
        $field = $datafieldmanager->createSearchField($dtypetitle, $dtypeid, $fieldtype, '', $dtypeoptions);
    }
    $form->addElement(new XoopsFormText(_MD_SEARCHSTRING, 'q', 50, 250, ''));
    $form->addElement(new XoopsFormButton('', 'submit', _MD_SEARCH, 'submit'));
    $form->addElement(new XoopsFormHidden('op', 'search'));
    $form->addElement(new XoopsFormHidden('dirid', $get_dirid));
    $form->display();
    $xoopsTpl->assign('search_page', ob_get_contents());
    ob_end_clean();
}
include XOOPS_ROOT_PATH . '/footer.php';

/**
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @param $filter_arr
 * @return array
 */
function mod_search($queryarray, $andor, $limit, $offset, $filter_arr)
{
    global $xoopsDB, $eh;
    // because count() returns 1 even if a supplied variable
    // is not an array, we must check if $queryarray is really an array
    $n            = 0;
    ${'sql' . $n} = '';
    $items        = '';
    if (is_array($queryarray) && count($queryarray) >= 1) {
        $count        = count($queryarray);
        ${'sql' . $n} = 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                        . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                        . ' d RIGHT JOIN '
                        . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                        . ' i ON (d.itemid=i.itemid) LEFT JOIN '
                        . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                        . " t ON (t.itemid=i.itemid) WHERE i.status='2'";
        ${'sql' . $n} .= " AND ((d.value LIKE '%$queryarray[0]%' OR i.title LIKE '%$queryarray[0]%' OR t.description LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; ++$i) {
            ${'sql' . $n} .= " $andor ";
            ${'sql' . $n} .= "(d.value LIKE '%$queryarray[$i]%' OR i.title LIKE '%$queryarray[$i]%' OR t.description LIKE '%$queryarray[$i]%')";
        }
        ${'sql' . $n} .= ') ';
        ${'sql' . $n} .= ' ORDER BY i.created DESC';
        ++$n;
    }

    $andor = ' AND';
    foreach ($filter_arr as $filter) {
        ${'sql' . $n}  = '';
        $dtypeid       = $filter['dtypeid'];
        $constr        = $filter['constr'];
        $postvalue     = $filter['postvalue'];
        $selectfields  = $filter['selectfields'];
        $addressfields = $filter['addressfields'];
        if (is_array($selectfields)) {
            if (count($selectfields) >= 1) {
                ${'sql' . $n}       .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                       . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                       . ' d, '
                                       . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                       . ' i LEFT JOIN '
                                       . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                       . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                $select             = '';
                ${'sql' . $n}       .= ' ' . $andor . " (d.dtypeid = $dtypeid AND d.value IN (";
                $count_selectfields = count($selectfields);
                $i                  = 1;
                foreach ($selectfields as $selectfield) {
                    ${'sql' . $n} .= '\'' . $selectfield . '\'';
                    if ($i != $count_selectfields) {
                        ${'sql' . $n} .= ', ';
                    }
                    ++$i;
                }
                ${'sql' . $n} .= '))';
                ++$n;
                $postvalue = '';
            }
        } else {
            $select = '0';
        }

        if ($postvalue != '') {
            ${'sql' . $n} = '';
            //$zero_allowed = '1';
            switch ($constr) {
                case 'equal':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = " = '" . $postvalue . '\'';
                    break;
                case 'notequal':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = " <> '" . $postvalue . '\'';
                    break;
                case 'contains':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = " LIKE '%" . $postvalue . "%'";
                    break;
                case 'notcontain':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = " NOT LIKE '%" . $postvalue . "%'";
                    break;
                case 'begins':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = " LIKE '" . $postvalue . "%'";
                    break;
                case 'ends':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = " LIKE '%" . $postvalue . '\'';
                    break;
                case 'bigger':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = ' > ' . (int)$postvalue . '';
                    break;
                case 'smaller':
                    ${'sql' . $n} .= 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
                                     . ' d, '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
                                     . ' i LEFT JOIN '
                                     . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
                                     . " t ON (t.itemid=i.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
                    $constraint   = ' < ' . (int)$postvalue . '';
                    break;
                default:
                    break;
            }
            if ($postvalue != '0') {
                ${'sql' . $n} .= $andor . ' (d.dtypeid = ' . $dtypeid . ' AND d.value ' . $constraint . ')';
            }
            ${'sql' . $n} .= ' ORDER BY i.created DESC';
            ++$n;
        }
    }

    //Getting the results into an array.
    $z = 0;

    $ret          = array();
    $items        = array();
    $intersection = array();
    for ($i = 0; $i < $n; ++$i) {
        $result      = $xoopsDB->query(${'sql' . $i}) || $eh->show('0013');
        $num_results = $xoopsDB->getRowsNum($result);

        if (!$result) {
            $num_results = 0;
        } elseif ($num_results == 0) {
            $num_results = 0;
        } else {
            while ($myrow = $xoopsDB->fetchArray($result)) {
                $items[] = $myrow['itemid'];
                if ($i == 0) {
                    $ret[$z]['itemid']      = $myrow['itemid'];
                    $ret[$z]['image']       = 'images/home.gif';
                    $ret[$z]['link']        = 'listing.php?item=' . $myrow['itemid'] . '';
                    $ret[$z]['title']       = $myrow['title'];
                    $ret[$z]['description'] = $myrow['description'];
                    $ret[$z]['time']        = $myrow['created'];
                    $ret[$z]['uid']         = $myrow['uid'];
                    $intersection           = $items;
                }
                ++$z;
            }
            $intersection = array_intersect($intersection, $items);
        }
    }

    $i        = 0;
    $item_arr = array();
    foreach ($intersection as $value) {
        $item_arr[$i] = $ret['' . findKeyValuePair($ret, 'itemid', $value, false) . ''];
        ++$i;
    }

    return $item_arr;
}

/**
 * @param string $items
 * @return array|bool
 */
function get_search_results($items = '')
{
    global $xoopsDB, $eh;
    if ($items != '') {
        $z           = 0;
        $ret         = array();
        $split_items = explode(',', $items);
        foreach ($split_items as $item) {
            $sql         = 'SELECT i.itemid, i.title, i.uid, i.created, t.description FROM ' . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items') . ' i, ' . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text') . " t WHERE i.itemid=t.itemid AND i.itemid='$item'";
            $result      = $xoopsDB->query($sql) || $eh->show('0013');
            $num_results = $xoopsDB->getRowsNum($result);
            while ($myrow = $xoopsDB->fetchArray($result)) {
                $ret[$z]['itemid']      = $myrow['itemid'];
                $ret[$z]['image']       = 'images/home.gif';
                $ret[$z]['link']        = 'listing.php?item=' . $myrow['itemid'] . '';
                $ret[$z]['title']       = $myrow['title'];
                $ret[$z]['description'] = $myrow['description'];
                $ret[$z]['time']        = $myrow['created'];
                $ret[$z]['uid']         = $myrow['uid'];
                ++$z;
            }
        }

        return $ret;
    } else {
        return false;
    }
}

/**
 * @param int $searchnum
 * @param int $limit
 * @return int
 */
function getNumberOfResults($searchnum = 0, $limit = 10)
{
    global $xoopsDB, $eh;
    $block       = array();
    $sql         = 'SELECT MAX(page), items FROM ' . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_searchresults') . " WHERE searchnum = '" . $searchnum . '\' GROUP BY page';
    $result      = $xoopsDB->query($sql) || $eh->show('0013');
    $num_results = $xoopsDB->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row   = $GLOBALS['xoopsDB']->fetchBoth($result);
        $page  = $row['MAX(page)'];
        $items = $row['items'];
    }
    $split_items    = explode(',', $items);
    $count_lastpage = count($split_items);
    $count          = ($page * $limit) - ($limit - $count_lastpage);

    return $count;
}

/**
 * @param     $queryarray
 * @param     $andor
 * @param     $limit
 * @param int $offset
 * @param     $filter_arr
 * @return int|void
 */
function mod_search_count($queryarray, $andor, $limit, $offset = 0, $filter_arr)
{
    global $xoopsDB, $eh;
    $count = 0;

    $sql = 'SELECT COUNT(DISTINCT i.itemid) FROM '
           . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_data')
           . ' d, '
           . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_items')
           . ' i LEFT JOIN '
           . $xoopsDB->prefix($module->getVar('dirname', 'n') . '_item_text')
           . " t ON (i.itemid=t.itemid) WHERE d.itemid=i.itemid AND i.status='2'";
    // because count() returns 1 even if a supplied variable
    // is not an array, we must check if $queryarray is really an array
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((d.value LIKE '%$queryarray[0]%' OR i.title LIKE '%$queryarray[0]%' OR t.description LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; ++$i) {
            $sql .= " $andor ";
            $sql .= "(d.value LIKE '%$queryarray[$i]%' OR i.title LIKE '%$queryarray[$i]%' OR t.description LIKE '%$queryarray[$i]%')";
        }
        $sql .= ') ';
    }
    $result = $xoopsDB->query($sql) || $eh->show('0013');
    list($count) = $xoopsDB->fetchRow($result);

    return $count;
}

/**
 * @param int $dtypeid
 * @return array
 */
function getPostedValue_text($dtypeid = 0)
{
    global $_POST;
    $postedvalues = array();
    if (isset($_POST["$dtypeid"])) {
        if (isset($_POST['' . $dtypeid . 'constr'])) {
            $constrvalue = $_POST['' . $dtypeid . 'constr'];
        } else {
            $constrvalue = '';
        }
        $postvalue    = $_POST["$dtypeid"];
        $postedvalues = array('dtypeid' => $dtypeid, 'constr' => $constrvalue, 'postvalue' => $postvalue, 'selectfields' => 0, 'addressfields' => 0);
    }

    return $postedvalues;
}

/**
 * @param int $dtypeid
 * @return array
 */
function getPostedValue_address($dtypeid = 0)
{
    global $_POST;
    $addressfields = getAddressFields('0');
    $postedvalues  = array();
    foreach ($addressfields['addressfields'] as $field => $fieldvalue) {
        if (isset($_POST["$dtypeid$field"])) {
            $addressfield = $_POST["$dtypeid$field"];
            if ($addressfield != '') {
                $postedvalues[] = array('field' => $field, 'postvalue' => $addressfield);
            }
        }
    }

    return $postedvalues;
}

/**
 * @param int $dtypeid
 * @return array
 */
function getPostedValue_array($dtypeid = 0)
{
    global $_POST;
    $postvalues_arr = array();
    if (isset($_POST["$dtypeid"])) {
        if (isset($_POST['' . $dtypeid . 'constr'])) {
            $constrvalue = $_POST['' . $dtypeid . 'constr'];
        } else {
            $constrvalue = '';
        }
        $postvalue    = $_POST["$dtypeid"];
        $postedvalues = array('dtypeid' => $dtypeid, 'constr' => $constrvalue, 'postvalue' => $postvalue, 'selectfields' => 0, 'addressfields' => 0);
    }

    //print_r($postedvalues);
    return $postedvalues;

    /* if ( isset($_POST["$dtypeid"]) ) {
    $postvalues_arr[] = $_POST["$dtypeid"];
    } else {
    $postvalues_arr[] = "";
    }

    return $postvalues_arr; */
}

/**
 * Search for a key and value pair in the second typelevel of a multi-dimensional array.
 *
 * @param array   multi-dimensional array to search
 * @param string  key name for which to search
 * @param mixed   value for which to search
 * @param boolean preform strict comparison
 * @return boolean found
 * @access public
 */
function findKeyValuePair($multiArray, $keyName, $value, $strict = false)
{
    /* Doing this test here makes for a bit of redundant code, but
    * improves the speed greatly, as it is not being preformed on every
    * iteration of the loop.
    */
    if (!$strict) {
        foreach ($multiArray as $multiArrayKey => $childArray) {
            if (array_key_exists($keyName, $childArray) && $childArray[$keyName] == $value) {
                return $multiArrayKey;
            }
        }
    } else {
        foreach ($multiArray as $multiArrayKey => $childArray) {
            if (array_key_exists($keyName, $childArray) && $childArray[$keyName] === $value) {
                return $multiArrayKey;
            }
        }
    }

    return false;
}
