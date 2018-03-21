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
//require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts   = \MyTextSanitizer::getInstance();
$mytree = new Efqdirectory\MyXoopsTree($xoopsDB->prefix('links_cat'), 'cid', 'pid');
//$eh     = new ErrorHandler;

$moddir = $xoopsModule->getVar('dirname');
include XOOPS_ROOT_PATH . '/header.php';

if (isset($_GET['catid'])) {
    $get_cid = (int)$_GET['cid'];
} else {
    $get_cid = '1';
}
if (isset($_GET['dirid'])) {
    $get_dirid = (int)$_GET['dirid'];
} else {
    $get_dirid = '1';
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
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
$lang_adv_search = sprintf(_MD_SEARCH_ADV, $get_dirid);

ob_start();
$searchform = '<form action="search.php" name="search" id="search" method="get">';
$searchform .= '<input type="hidden" name="dirid" value="' . $get_dirid . '"><input type="text" name="q" size="40" maxsize="150" value=""><input type="submit" id="submit" value="' . _MD_SEARCH . '">' . $lang_adv_search . '</form>';
echo $searchform;
if (!empty($_GET['q'])) {
    //get search results from query
    if (isset($_GET['q'])) {
        $querystring = $GLOBALS['xoopsDB']->escape($myts->stripSlashesGPC($_GET['q']));
    //echo $querystring."<br>";
    } else {
        redirect_header(XOOPS_URL . "/modules/$moddir/search.php", 2, _MD_NO_SEARCH_STRING_SELECTED);
    }
    $poscount   = substr_count($querystring, '"') / 2;
    $specialarr = [];
    for ($i = 0; $i < $poscount; ++$i) {
        $start = strpos($querystring, '"', 0);
        $end   = strpos($querystring, '"', $start + 1);
        if (false !== $end) {
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
    $searchresults = mod_search($querydiff, $andor, $limit, $offset);
    $maxpages      = 10;
    $maxcount      = 30;

    $count_results = mod_search_count($querydiff, $andor, $maxcount, 0);
    $count_pages   = 0;
    //Calculate the number of result pages.
    if ($count_results > $limit) {
        $count_pages = ceil($count_results / $limit);
    }
    $pages_text = '';
    $pages_text .= $count_results . ' ' . _MD_LISTINGS_FOUND . '<br>';

    if ($count_pages >= 2) {
        $pages_text .= '<a href="search.php?q=' . $querystring . '&page=1">1</a>';
    }
    for ($i = 1; $i < $count_pages; ++$i) {
        $page       = $i + 1;
        $pages_text .= ' - <a href="search.php?q=' . $querystring . '&page=' . $page . '">' . $page . '</a>';
    }

    echo '<div class="itemTitleLarge">' . _MD_SEARCHRESULTS_TITLE . '</div><br>';
    if (0 == $searchresults) {
        echo '<div class="itemTitle">' . _MD_NORESULTS . '</div>';
    } else {
        foreach ($searchresults as $result) {
            echo '<div class="itemTitle"><a href="' . $result['link'] . '">' . $result['title'] . '</a></div><div class="itemText">' . $result['description'] . '</div><hr>';
        }
    }
    echo '<br>';
    echo $pages_text;
}
$xoopsTpl->assign('search_page', ob_get_contents());
ob_end_clean();

include XOOPS_ROOT_PATH . '/footer.php';

/**
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @return array|int
 */
function mod_search($queryarray, $andor, $limit, $offset)
{
    global $xoopsDB;
    $sql = 'SELECT DISTINCT i.itemid, i.title, i.uid, i.created, t.description FROM '
           . $xoopsDB->prefix($helper->getDirname() . '_data')
           . ' d RIGHT JOIN '
           . $xoopsDB->prefix($helper->getDirname() . '_items')
           . ' i ON (d.itemid=i.itemid) LEFT JOIN '
           . $xoopsDB->prefix($helper->getDirname() . '_item_text')
           . " t ON (i.itemid=t.itemid) WHERE i.status='2'";
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
    $sql .= 'ORDER BY i.created DESC';

    $result      = $xoopsDB->query($sql, $limit, $offset) ; //|| $eh->show('0013');
    if (!$result) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    $num_results = $xoopsDB->getRowsNum($result);
    if (!$result) {
        return 0;
    } elseif (0 == $num_results) {
        return 0;
    } else {
        $ret = [];
        $i   = 0;
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $ret[$i]['image']       = 'images/home.gif';
            $ret[$i]['link']        = 'listing.php?item=' . $myrow['itemid'] . '';
            $ret[$i]['title']       = $myrow['title'];
            $ret[$i]['description'] = $myrow['description'];
            $ret[$i]['time']        = $myrow['created'];
            $ret[$i]['uid']         = $myrow['uid'];
            ++$i;
        }

        return $ret;
    }
}

/**
 * @param     $queryarray
 * @param     $andor
 * @param     $limit
 * @param int $offset
 * @return int|void
 */
function mod_search_count($queryarray, $andor, $limit, $offset = 0)
{
    global $xoopsDB;
    $count = 0;
    $sql   = 'SELECT COUNT(DISTINCT i.itemid) FROM '
             . $xoopsDB->prefix($helper->getDirname() . '_data')
             . ' d, '
             . $xoopsDB->prefix($helper->getDirname() . '_items')
             . ' i LEFT JOIN '
             . $xoopsDB->prefix($helper->getDirname() . '_item_text')
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
    $result = $xoopsDB->query($sql) ; //|| $eh->show('0013');
    if (!$result) {
        $logger = \XoopsLogger::getInstance();
        $logger->handleError(E_USER_WARNING, $sql, __FILE__, __LINE__);
    }
    list($count) = $xoopsDB->fetchRow($result);

    return $count;
}
