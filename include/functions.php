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
 * @param $orderby
 * @return string
 */

use XoopsModules\Efqdirectory;

/**
 * @param $orderby
 * @return string
 */
function convertOrderByIn($orderby)
{
    switch (trim($orderby)) {
        case 'titleA':
            $orderby = 'title ASC';
            break;
        case 'dateA':
            $orderby = 'created ASC';
            break;
        case 'hitsA':
            $orderby = 'hits ASC';
            break;
        case 'ratingA':
            $orderby = 'rating ASC';
            break;
        case 'titleD':
            $orderby = 'title DESC';
            break;
        case 'hitsD':
            $orderby = 'hits DESC';
            break;
        case 'ratingD':
            $orderby = 'rating DESC';
            break;
        case'dateD':
        default:
            $orderby = 'created DESC';
            break;
    }

    return $orderby;
}

/**
 * @param $orderby
 * @return string
 */
function convertorderbytrans($orderby)
{
    $orderbyTrans = '';
    if ('hits ASC' === $orderby) {
        $orderbyTrans = '' . _MD_POPULARITYLTOM . '';
    }
    if ('hits DESC' === $orderby) {
        $orderbyTrans = '' . _MD_POPULARITYMTOL . '';
    }
    if ('title ASC' === $orderby) {
        $orderbyTrans = '' . _MD_TITLEATOZ . '';
    }
    if ('title DESC' === $orderby) {
        $orderbyTrans = '' . _MD_TITLEZTOA . '';
    }
    if ('date ASC' === $orderby) {
        $orderbyTrans = '' . _MD_DATEOLD . '';
    }
    if ('date DESC' === $orderby) {
        $orderbyTrans = '' . _MD_DATENEW . '';
    }
    if ('rating ASC' === $orderby) {
        $orderbyTrans = '' . _MD_RATINGLTOH . '';
    }
    if ('rating DESC' === $orderby) {
        $orderbyTrans = '' . _MD_RATINGHTOL . '';
    }

    return $orderbyTrans;
}

/**
 * @param $orderby
 * @return string
 */
function convertorderbyout($orderby)
{
    if ('title ASC' === $orderby) {
        $orderby = 'titleA';
    }
    if ('date ASC' === $orderby) {
        $orderby = 'dateA';
    }
    if ('hits ASC' === $orderby) {
        $orderby = 'hitsA';
    }
    if ('rating ASC' === $orderby) {
        $orderby = 'ratingA';
    }
    if ('title DESC' === $orderby) {
        $orderby = 'titleD';
    }
    if ('date DESC' === $orderby) {
        $orderby = 'dateD';
    }
    if ('hits DESC' === $orderby) {
        $orderby = 'hitsD';
    }
    if ('rating DESC' === $orderby) {
        $orderby = 'ratingD';
    }

    return $orderby;
}

/**
 * @param $time
 * @param $status
 * @return string
 */
function newlinkgraphic($time, $status)
{
    global $moddir;
    $count     = 7;
    $new       = '';
    $startdate = (time() - (86400 * $count));
    if ($startdate < $time) {
        if (2 == $status) {
            $new = '&nbsp;<img src="' . XOOPS_URL . "/modules/$moddir/assets/images/newred.gif\" alt=\"" . _MD_NEWTHISWEEK . '">';
        } elseif (3 == $status) {
            $new = '&nbsp;<img src="' . XOOPS_URL . "/modules/$moddir/assets/images/update.gif\" alt=\"" . _MD_UPTHISWEEK . '">';
        }
    }

    return $new;
}

/**
 * @param $hits
 * @return string
 */
function popgraphic($hits)
{
    global $xoopsModuleConfig, $moddir;
    if ($hits >= $xoopsModuleConfig['popular']) {
        return '&nbsp;<img src="' . XOOPS_URL . "/modules/$moddir/assets/images/pop.gif\" alt=\"" . _MD_POPULAR . '">';
    }

    return '';
}

/**
 * @param     $sel_id
 * @param int $status
 * @return int
 */
function getTotalItems($sel_id, $status = 0)
{
    global $xoopsDB, $mytree;
    $helper = Efqdirectory\Helper::getInstance();
    $count = 0;
    $arr   = [];
    $query = 'SELECT DISTINCT l.itemid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_items') . ' l, ' . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat') . ' x WHERE x.itemid=l.itemid AND x.cid=' . $sel_id . '';
    if ('' !== $status) {
        $query .= " AND l.status>='$status'";
    } else {
        $query .= '';
    }
    $query  .= " AND x.active >= '1'";
    $result = $xoopsDB->query($query);
    //print_r($xoopsDB->fetchArray($result));
    if (!$result) {
        $count = 0;
    } else {
        $num_results = $GLOBALS['xoopsDB']->getRowsNum($result);
        $count       = $num_results;
    }
    $arr  = $mytree->getAllChildId($sel_id);
    $size = count($arr);
    for ($i = 0; $i < $size; ++$i) {
        $query2 = 'SELECT DISTINCT l.itemid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_items') . ' l, ' . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat') . ' x WHERE l.itemid=x.itemid AND x.cid=' . $arr[$i] . '';
        if ('' !== $status) {
            $query2 .= " AND l.status>='$status'";
        } else {
            $query2 .= '';
        }
        $query2       .= " AND x.active >= '1'";
        $result2      = $xoopsDB->query($query2);
        $num_results2 = $GLOBALS['xoopsDB']->getRowsNum($result2);
        $count        += $num_results2;
    }

    return $count;
}

/**
 * @param        $sel_id
 * @param string $status
 * @param        $locdestid
 * @return int
 */
function getTotalItems2($sel_id, $status = '', $locdestid)
{
    global $xoopsDB, $mytree;
    $count = 0;
    $arr   = [];
    $query = 'SELECT count(*) FROM ' . $xoopsDB->prefix('links_links') . ' t, ' . $xoopsDB->prefix('links_x_loc_dest') . ' x  WHERE x.ldestid=t.lid AND x.locdestid=' . $locdestid . ' AND t.cid=' . $sel_id . '';
    //  $query = "select DISTINCT count(lid) from ".$xoopsDB->prefix("links_links")." t, ".$xoopsDB->prefix("links_x_loc_dest")." x  where x.ldestid=t.lid AND x.locdestid=".$locdestid." AND t.cid=".$sel_id."";
    //  $query = "select count(*) from ".$xoopsDB->prefix("links_links")." where cid=".$sel_id."";
    if ('' !== $status) {
        $query .= " and status>=$status";
    }
    $result = $xoopsDB->query($query);
    list($thing) = $xoopsDB->fetchRow($result);
    $count = $thing;
    $arr   = $mytree->getAllChildId($sel_id);
    $size  = count($arr);
    for ($i = 0; $i < $size; ++$i) {
        $query2 = 'select count(*) ' . $xoopsDB->prefix('links_links') . ' t, ' . $xoopsDB->prefix('links_x_loc_dest') . ' x  where x.ldestid=t.lid AND x.locdestid=' . $locdestid . ' AND t.cid=' . $arr[$i] . '';
        if ('' !== $status) {
            $query2 .= " and status>=$status";
        }
        $result2 = $xoopsDB->query($query2);
        list($thing) = $xoopsDB->fetchRow($result2);
        $count += $thing;
    }

    return $count;
}

/**
 * @param int $dirid
 * @return int
 */
function getDirNameFromId($dirid = 0)
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $myts        = \MyTextSanitizer::getInstance();
    $sql         = 'SELECT name FROM ' . $xoopsDB->prefix($helper->getDirname() . '_dir') . " WHERE dirid = '" . $dirid . '\'';
    $result      = $xoopsDB->query($sql);
    $num_results = $GLOBALS['xoopsDB']->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row     = $GLOBALS['xoopsDB']->fetchBoth($result);
        $dirname = $row['name'];
    }

    return $dirname;
}

/**
 * @param int $cid
 * @return int
 */
function getCatTitleFromId($cid = 0)
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    //$block = array();
    $myts        = \MyTextSanitizer::getInstance();
    $sql         = 'SELECT title FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE cid = '" . $cid . '\'';
    $result      = $xoopsDB->query($sql);
    $num_results = $GLOBALS['xoopsDB']->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row      = $GLOBALS['xoopsDB']->fetchBoth($result);
        $cattitle = $row['title'];
    }

    return $cattitle;
}

/**
 * @param $get_itemid
 * @return int|string
 */
function getCategoriesPaths($get_itemid)
{
    global $efqtree, $xoopsDB, $get_itemid, $get_dirid, $xoopsUser, $xoopsModule;
    $helper = Efqdirectory\Helper::getInstance();
    if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
        $isadmin = true;
    } else {
        $isadmin = false;
    }
    $sql         = 'SELECT xid, cid, itemid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat') . ' WHERE itemid = ' . $get_itemid . " AND active='1'";
    $result      = $xoopsDB->query($sql);
    $num_results = $GLOBALS['xoopsDB']->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    $output = '';
    for ($i = 0; $i < $num_results; ++$i) {
        $row    = $GLOBALS['xoopsDB']->fetchBoth($result);
        $cid    = $row['cid'];
        $path   = '';
        $path   .= $efqtree->getUnformattedPathFromId($cid, 'title', $path);
        $path   .= '<br>';
        $output .= $path;
    }
    if ($isadmin) {
        $output .= '<br><a href="admin/categories.php?dirid=' . $get_dirid . '">' . _MD_EDITCATEGORIES . '</a>';
    }

    //$output .= "<br><a href=\"editcategories.php?item=".$get_itemid."\">["._MD_EDIT_CATEGORIES."]</a>";
    return $output;
}

/**
 * @param int $currentoption
 * @param     $breadcrumb
 */
function adminmenu($currentoption = 0, $breadcrumb)
{
    global $xoopsModule, $xoopsConfig;
    $tblColors                 = [];
    $tblColors[0]              = $tblColors[1] = $tblColors[2] = $tblColors[3] = $tblColors[4] = $tblColors[5] = $tblColors[6] = $tblColors[7] = '#DDE';
    $tblColors[$currentoption] = 'white';
    echo "<table width=100% class='outer'><tr><td align=right>
        <font size=2>" . $xoopsModule->name() . ':' . $breadcrumb . '</font>
        </td></tr></table><br>';
    echo '<div id="navcontainer"><ul style="padding: 3px 0; margin-left:
        0;font: bold 12px Verdana, sans-serif; ">';
    echo '<li style="list-style: none; margin: 0; display: inline; ">
        <a href="index.php" style="padding: 3px 0.5em;
        margin-left: 3px;
        border: 1px solid #778; background: ' . $tblColors[0] . ';
        text-decoration: none; ">' . _MD_A_MODADMIN_HOME . '</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; ">
        <a href="directories.php" style="padding: 3px 0.5em;
        margin-left: 3px;
        border: 1px solid #778; background: ' . $tblColors[1] . ';
        text-decoration: none; ">' . _MD_A_DIRADMIN . '</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; ">
        <a href="fieldtypes.php" style="padding: 3px 0.5em;
        margin-left: 3px;
        border: 1px solid #778; background: ' . $tblColors[2] . ';
        text-decoration: none; ">' . _MD_A_FTYPESADMIN . '</a></li>';
    // echo "<li style=\"list-style: none; margin: 0; display: inline; \">
    //         <a href=\"addresstypes.php\" style=\"padding: 3px 0.5em;
    //         margin-left: 3px;
    //         border: 1px solid #778; background: ".$tblColors[3].";
    //         text-decoration: none; \">"._MD_A_ATYPESADMIN."</a></li>";
    echo '<li style="list-style: none; margin: 0; display: inline; ">
        <a href="subscriptions.php" style="padding: 3px 0.5em;
        margin-left: 3px;
        border: 1px solid #778; background: ' . $tblColors[4] . ';
        text-decoration: none; ">' . _MD_A_ASUBSCRIPTIONSADMIN . '</a></li>';
    echo '</div></ul>';
}

/**
 * @param string $typeid
 * @return int|string
 */
function getTypeFromId($typeid = '0')
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $myts        = \MyTextSanitizer::getInstance();
    $sql         = 'SELECT typename  FROM ' . $xoopsDB->prefix($helper->getDirname() . '_itemtypes') . " WHERE typeid = '" . $typeid . '\'';
    $result      = $xoopsDB->query($sql);
    $num_results = $xoopsDB->getRowsNum($result);
    $typename    = '';
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row      = $GLOBALS['xoopsDB']->fetchBoth($result);
        $typename = $row['typename'];
    }

    return $typename;
}

/**
 * @param string $catid
 * @return int
 */
function getDirId($catid = '0')
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $myts        = \MyTextSanitizer::getInstance();
    $sql         = 'SELECT dirid  FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE cid = '" . $catid . '\'';
    $result      = $xoopsDB->query($sql);
    $num_results = $xoopsDB->getRowsNum($result);
    $dirid       = 0;
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row   = $GLOBALS['xoopsDB']->fetchBoth($result);
        $dirid = $row['dirid'];
    }

    return $dirid;
}

/**
 * @param string $catid
 * @return bool
 */
function checkDescription($catid = '0')
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $myts        = \MyTextSanitizer::getInstance();
    $sql         = 'SELECT txtid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat_txt') . " WHERE cid = '" . $catid . '\'';
    $result      = $xoopsDB->query($sql);
    $num_results = $xoopsDB->getRowsNum($result);
    $txtid       = false;
    if (!$result) {
        return false;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row   = $GLOBALS['xoopsDB']->fetchBoth($result);
        $txtid = $row['txtid'];
    }

    return $txtid;
}

/**
 * @param string $get_catid
 * @return int|string
 */
function getTemplateFromCatid($get_catid = '0')
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $myts        = \MyTextSanitizer::getInstance();
    $result      = $xoopsDB->query('SELECT c.tplid, t.name  FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat_tpl') . ' c, ' . $xoopsDB->prefix($helper->getDirname() . '_tpl') . " t WHERE c.tplid=t.tplid AND c.catid = '" . $get_catid . '\'');
    $num_results = $xoopsDB->getRowsNum($result);
    $tplname     = '';
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row     = $GLOBALS['xoopsDB']->fetchBoth($result);
        $tplname = $row['name'];
    }

    return $tplname;
}

/**
 * @param string $item
 * @param string $dirid
 * @return string
 */
function getCatSelectArea($item = '0', $dirid = '0')
{
    global $xoopsDB, $myts, $mytree, $moddir, $get_itemid;
    $helper = Efqdirectory\Helper::getInstance();
    $sql        = 'SELECT c.cid, c.title, c.pid, c.allowlist, x.active FROM '
                  . $xoopsDB->prefix($helper->getDirname() . '_cat')
                  . ' c LEFT JOIN '
                  . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                  . ' x ON (c.cid=x.cid AND x.itemid='
                  . $item
                  . ")  WHERE c.dirid='"
                  . $dirid
                  . '\' AND c.pid=\'0\' AND c.active=\'1\'';
    $mainresult = $xoopsDB->query($sql);
    $numrows    = $xoopsDB->getRowsNum($mainresult);
    $output     = '<table>';
    if ($numrows > 0) {
        $cats   = '';
        $output .= '<tr><td class="categoryHeader" colspan="2"><strong>' . _MD_CATTITLE . '</strong></td><td class="categoryHeader"><strong>' . _MD_SELECT . "</strong></td></tr>\n";
        $brench = 0;
        $tab    = '';
        while (false !== (list($cid, $title, $pid, $allowlist, $active) = $xoopsDB->fetchRow($mainresult))) {
            //For each cid, get all 'first children' using getFirstChildId() function
            if ('0' != $allowlist) {
                if ('1' == $active) {
                    $checked = ' checked';
                } else {
                    $checked = '';
                }
                $checkbox = '<input type="checkbox" name="selected' . $cid . "\"$checked";
            } else {
                $checkbox = '&nbsp;';
            }
            $output .= '<tr><td><strong>' . $tab . '' . $title . "</strong></td><td>&nbsp;</td><td>$checkbox</td></tr>\n";
            $output .= getCatSelectAreaChildren($cid, 0, $dirid);
        }
    } else {
        $output .= '<tr><td>' . _MD_NORESULTS . '</td></tr>';
    }
    $output .= '</table>';

    return $output;
}

/**
 * @param string $childid
 * @param string $level
 * @return string
 */
function getCatSelectAreaChildren($childid = '0', $level = '0')
{
    global $xoopsDB, $myts, $mytree, $get_dirid, $moddir, $get_itemid;
    $helper = Efqdirectory\Helper::getInstance();
    $tab    = '&nbsp;';
    $level  = $level;
    $output = '';
    $plus   = '<img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/arrow.gif">';
    for ($i = 0; $i < $level; ++$i) {
        $tab .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $sql         = 'SELECT DISTINCT c.cid, c.title, c.pid, c.allowlist, x.active FROM '
                   . $xoopsDB->prefix($helper->getDirname() . '_cat')
                   . ' c LEFT JOIN '
                   . $xoopsDB->prefix($helper->getDirname() . '_item_x_cat')
                   . " x ON (c.cid=x.cid AND x.itemid='"
                   . $get_itemid
                   . '\')  WHERE c.dirid=\''
                   . $get_dirid
                   . '\' AND c.pid=\''
                   . $childid
                   . '\' AND c.active=\'1\'';
    $childresult = $xoopsDB->query($sql);
    if (!$childresult) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $numrows     = $xoopsDB->getRowsNum($childresult);
    if ($numrows > 0) {
        while (false !== (list($cid, $title, $pid, $allowlist, $active) = $xoopsDB->fetchRow($childresult))) {
            if ('0' != $allowlist) {
                if ('1' == $active) {
                    $checked = ' checked';
                } else {
                    $checked = '';
                }
                $checkbox = '<input type="checkbox" name="selected' . $cid . "\"$checked";
            } else {
                $checkbox = '&nbsp;';
            }
            $output   .= '<tr><td><strong>' . $tab . '' . $plus . '&nbsp;' . $title . "</td><td>&nbsp;</strong></td><td align=\"center\">$checkbox</td></tr>\n";
            $newlevel = $level + 1;
            $output   .= getCatSelectAreaChildren($cid, $newlevel);
        }
    }

    return $output;
}

/**
 * @param int $item
 * @return int
 */
function getDirIdFromItem($item = 0)
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $block       = [];
    $myts        = \MyTextSanitizer::getInstance();
    $dirid       = 0;
    $sql         = 'SELECT dirid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_items') . ' WHERE itemid = ' . $item . '';
    $result      = $xoopsDB->query($sql);
    $num_results = $xoopsDB->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row   = $GLOBALS['xoopsDB']->fetchBoth($result);
        $dirid = $row['dirid'];
    }

    return $dirid;
}

/**
 * @param int $item
 * @return int
 */
function getUserIdFromItem($item = 0)
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $block       = [];
    $myts        = \MyTextSanitizer::getInstance();
    $userid      = 0;
    $sql         = 'SELECT uid FROM ' . $xoopsDB->prefix($helper->getDirname() . '_items') . ' WHERE itemid = ' . $item . '';
    $result      = $xoopsDB->query($sql);
    $num_results = $xoopsDB->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    for ($i = 0; $i < $num_results; ++$i) {
        $row    = $GLOBALS['xoopsDB']->fetchBoth($result);
        $userid = $row['uid'];
    }

    return $userid;
}

//updates rating data in itemtable for a given item
/**
 * @param $sel_id
 */
function updaterating($sel_id)
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $query       = 'SELECT rating FROM ' . $xoopsDB->prefix($helper->getDirname() . '_votedata') . ' WHERE itemid = ' . $sel_id . '';
    $voteresult  = $xoopsDB->query($query);
    $votesDB     = $xoopsDB->getRowsNum($voteresult);
    $totalrating = 0;
    while (false !== (list($rating) = $xoopsDB->fetchRow($voteresult))) {
        $totalrating += $rating;
    }
    $finalrating = $totalrating / $votesDB;
    $finalrating = number_format($finalrating, 4);
    $query       = 'UPDATE ' . $xoopsDB->prefix($helper->getDirname() . '_items') . " SET rating=$finalrating, votes=$votesDB WHERE itemid = $sel_id";
    $xoopsDB->query($query) || exit();
}

/**
 * @param string $typeid
 * @return array|int
 */
function getAddressFields($typeid = '0')
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $block        = [];
    $myts         = \MyTextSanitizer::getInstance();
    $dirid        = 0;
    $addressarray = [];
    if ('0' == $typeid) {
        $result = $xoopsDB->query('SELECT typeid, address, address2, zip, postcode, lat, lon, phone, fax, mobile, city, country, typename, uselocyn FROM ' . $xoopsDB->prefix($helper->getDirname() . '_address_types') . " WHERE defaultyn = '1'");
    } else {
        $result = $xoopsDB->query('SELECT typeid, address, address2, zip, postcode, lat, lon, phone, fax, mobile, city, country, typename, uselocyn FROM ' . $xoopsDB->prefix($helper->getDirname() . '_address_types') . " WHERE typeid = '$typeid'");
    }
    $num_results = $xoopsDB->getRowsNum($result);
    if (!$result) {
        return 0;
    }
    while (false !== (list($typeid, $address, $address2, $zip, $postcode, $lat, $lon, $phone, $fax, $mobile, $city, $country, $typename, $uselocyn) = $xoopsDB->fetchRow($result))) {
        $addressarray = [
            'typeid'        => $typeid,
            'typename'      => $typename,
            'uselocyn'      => $uselocyn,
            'addressfields' => [
                'address'  => $address,
                'address2' => $address2,
                'zip'      => $zip,
                'postcode' => $postcode,
                'lat'      => $lat,
                'lon'      => $lon,
                'phone'    => $phone,
                'fax'      => $fax,
                'mobile'   => $mobile,
                'city'     => $city,
                'country'  => $country
            ]
        ];
    }

    return $addressarray;
}

/**
 * @param string $addrid
 * @return array
 */
function getAddressValues($addrid = '0')
{
    global $xoopsDB;
    $helper = Efqdirectory\Helper::getInstance();
    $myts         = \MyTextSanitizer::getInstance();
    $addressarray = [];
    $result       = $xoopsDB->query('SELECT address, address2, zip, postcode, lat, lon, phone, fax, mobile, city, country FROM ' . $xoopsDB->prefix($helper->getDirname() . '_addresses') . " WHERE addrid = '$addrid'");
    $num_results  = $xoopsDB->getRowsNum($result);
    if (0 == $num_results) {
        $addressarray = ['address' => '', 'address2' => '', 'zip' => '', 'postcode' => '', 'lat' => '', 'lon' => '', 'phone' => '', 'fax' => '', 'mobile' => '', 'city' => '', 'country' => ''];
    }
    while (false !== (list($address, $address2, $zip, $postcode, $lat, $lon, $phone, $fax, $mobile, $city, $country) = $xoopsDB->fetchRow($result))) {
        $addressarray = [
            'address'  => $address,
            'address2' => $address2,
            'zip'      => $zip,
            'postcode' => $postcode,
            'lat'      => $lat,
            'lon'      => $lon,
            'phone'    => $phone,
            'fax'      => $fax,
            'mobile'   => $mobile,
            'city'     => $city,
            'country'  => $country
        ];
    }

    return $addressarray;
}

/**
 * @return string
 */
function getCatSelectArea2()
{
    global $xoopsDB, $myts, $mytree, $get_dirid, $moddir, $xoopsUser, $xoopsModule;
    $helper = Efqdirectory\Helper::getInstance();
    if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
        $isadmin = true;
    } else {
        $isadmin = false;
    }
    $sql        = 'SELECT cid, title, pid, allowlist FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE dirid='" . $get_dirid . '\' AND pid=\'0\' AND active=\'1\'';
    $mainresult = $xoopsDB->query($sql);
    $numrows    = $xoopsDB->getRowsNum($mainresult);
    $output     = '';
    if ($numrows > 0) {
        $cats          = '';
        $output        = '<table class="categoryTable"><tr><td class="categoryHeader" colspan="2"><strong>' . _MD_CATTITLE . '</strong></td><td class="categoryHeader"><strong>' . _MD_SELECT . "</strong></td></tr>\n";
        $brench        = 0;
        $tab           = '';
        $selectablecat = false;
        while (false !== (list($cid, $title, $pid, $allowlist) = $xoopsDB->fetchRow($mainresult))) {
            //For each cid, get all 'first children' using getFirstChildId() function
            if ('0' != $allowlist) {
                $checkbox      = '<input type="checkbox" name="selected' . $cid . '"';
                $selectablecat = true;
            //$checked = "";
            } else {
                //$checked = " checked=checked";
                $checkbox = '&nbsp;';
            }
            //$cats .= $cid."|";
            $output .= '<tr><td><strong>' . $tab . '' . $title . "</strong></td><td>&nbsp;</td><td align=\"center\">$checkbox</td></tr>\n";
            $output .= getCatSelectAreaChildren2($cid);
            if ('' !== $output) {
                $selectablecat = true;
            }
        }
    } else {
        redirect_header(XOOPS_URL . "/modules/$moddir/index.php?dirid=$get_dirid", 2, _MD_NOACTIVECATEGORIES);
        //$output = ""._MD_NORESULTS."";
    }
    if ($isadmin) {
        $output .= '<tr><td><br><a href="admin/categories.php?dirid=' . $get_dirid . '">' . _MD_EDITCATEGORIES . '</a></td></tr>';
    }
    $output .= '</table>';
    if (false === $selectablecat) {
        redirect_header(XOOPS_URL . "/modules/$moddir/index.php?dirid=$get_dirid", 2, _MD_NOACTIVECATEGORIES);
    }

    return $output;
}

/**
 * @param string $childid
 * @param string $level
 * @return string
 */
function getCatSelectAreaChildren2($childid = '0', $level = '0')
{
    global $xoopsDB, $myts, $mytree, $get_dirid, $moddir;
    $helper = Efqdirectory\Helper::getInstance();
    $tab    = '&nbsp;';
    $level  = $level;
    $output = '';
    $plus   = '<img src="' . XOOPS_URL . '/modules/' . $moddir . '/assets/images/arrow.gif">';
    for ($i = 0; $i < $level; ++$i) {
        $tab .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    $sql         = 'SELECT cid, title, pid, allowlist FROM ' . $xoopsDB->prefix($helper->getDirname() . '_cat') . " WHERE dirid='" . (int)$get_dirid . '\' AND pid=\'' . (int)$childid . '\' AND active=\'1\'';
    $childresult = $xoopsDB->query($sql);
    $numrows     = $xoopsDB->getRowsNum($childresult);
    if ($numrows > 0) {
        while (false !== (list($cid, $title, $pid, $allowlist) = $xoopsDB->fetchRow($childresult))) {
            if ('0' != $allowlist) {
                $checkbox = '<input type="checkbox" name="selected' . $cid . '"';
            } else {
                $checkbox = '&nbsp;';
            }
            $output   .= '<tr><td><strong>' . $tab . '' . $plus . '&nbsp;' . $title . "</td><td>&nbsp;</strong></td><td align=\"center\">$checkbox</td></tr>\n";
            $newlevel = $level + 1;
            $output   .= getCatSelectAreaChildren($cid, $newlevel, $get_dirid);
        }
    }

    return $output;
}
