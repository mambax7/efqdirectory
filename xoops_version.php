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
 * @package
 * @since
 * @author       XOOPS Development Team,
 */

use XoopsModules\Efqdirectory;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

include __DIR__ . '/preloads/autoloader.php';

$moduleDirName = basename(__DIR__);

$modversion['version']       = 1.07;
$modversion['module_status'] = 'Beta 1';
$modversion['release_date']  = '2017/07/15';
$modversion['name']          = _MI_EFQDIR_NAME;
$modversion['description']   = _MI_EFQDIR_DESC;
$modversion['author']        = 'Martijn Hertog (EFQ Consultancy) aka wtravel';
$modversion['credits']       = 'XOOPS Module Development Team, Adam Frick';
$modversion['help']          = 'page=help';
$modversion['license']       = 'GNU GPL 2.0 or later';
$modversion['license_url']   = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']      = 0;
$modversion['image']         = 'assets/images/logoModule.png';
$modversion['dirname']       = $moduleDirName;

$modversion['modicons16'] = 'assets/images/icons/16';
$modversion['modicons32'] = 'assets/images/icons/32';

//about
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.9';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];

// ------------------- Mysql ------------------- //
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'] = [
    $moduleDirName . '_' . 'cat',
    $moduleDirName . '_' . 'cat_txt',
    $moduleDirName . '_' . 'data',
    $moduleDirName . '_' . 'dir',
    $moduleDirName . '_' . 'item_x_cat',
    $moduleDirName . '_' . 'item_x_loc',
    $moduleDirName . '_' . 'items',
    $moduleDirName . '_' . 'item_text',
    $moduleDirName . '_' . 'item_img',
    $moduleDirName . '_' . 'itemtypes',
    $moduleDirName . '_' . 'fieldtypes',
    $moduleDirName . '_' . 'dtypes',
    $moduleDirName . '_' . 'dtypes_x_cat',
    $moduleDirName . '_' . 'coupon',
    $moduleDirName . '_' . 'loc',//In case not used in combo with destinations mod.
    $moduleDirName . '_' . 'loc_types',//In case not used in combo with destinations mod.
    $moduleDirName . '_' . 'loc_x_loctype',//In case not used in combo with destinations mod.
    $moduleDirName . '_' . 'votedata',
    //$moduleDirName . '_' . = "loc_x_address_types",
    $moduleDirName . '_' . 'form_options',
    $moduleDirName . '_' . 'address_types',
    $moduleDirName . '_' . 'searchresults',
    $moduleDirName . '_' . 'subscr_offers',
    $moduleDirName . '_' . 'subscr_orders',
    $moduleDirName . '_' . 'subscr_payments',
    $moduleDirName . '_' . 'subscr_scheduler',
    $moduleDirName . '_' . 'subscr_notify',
    $moduleDirName . '_' . 'cat_tpl',
    $moduleDirName . '_' . 'tpl',
];

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_EFQDIR_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_EFQDIR_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_EFQDIR_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_EFQDIR_SUPPORT, 'link' => 'page=support'],
];

// Blocks
$modversion['blocks'][1]['file']        = 'efqdiralpha1_menu.php';
$modversion['blocks'][1]['name']        = _MI_EFQDIR_MENU;
$modversion['blocks'][1]['description'] = 'Shows directories menu';
$modversion['blocks'][1]['show_func']   = 'b_efqdiralpha1_menu_show';
$modversion['blocks'][1]['edit_func']   = 'b_efqdiralpha1_menu_edit';
$modversion['blocks'][1]['options']     = '';
$modversion['blocks'][1]['template']    = 'efqdiralpha1_block_directories.tpl';

// Menu
$modversion['hasMain'] = 1;

// Search
$modversion['hasSearch']      = 0;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'links_search';

// Comments
$modversion['hasComments']             = 0;
$modversion['comments']['itemName']    = 'itemid';
$modversion['comments']['pageName']    = 'listing.php';
$modversion['comments']['extraParams'] = ['cid'];
// Comment callback functions
$modversion['comments']['callbackFile']        = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'listings_com_approve';
$modversion['comments']['callback']['update']  = 'listings_com_update';

// Templates
$modversion['templates'][1]['file']         = 'efqdiralpha1_editcategories.tpl';
$modversion['templates'][1]['description']  = '';
$modversion['templates'][2]['file']         = 'efqdiralpha1_editlisting.tpl';
$modversion['templates'][2]['description']  = '';
$modversion['templates'][3]['file']         = 'efqdiralpha1_index.tpl';
$modversion['templates'][3]['description']  = '';
$modversion['templates'][4]['file']         = 'efqdiralpha1_listing.tpl';
$modversion['templates'][4]['description']  = '';
$modversion['templates'][5]['file']         = 'efqdiralpha1_ratelisting.tpl';
$modversion['templates'][5]['description']  = '';
$modversion['templates'][6]['file']         = 'efqdiralpha1_smalllisting.tpl';
$modversion['templates'][6]['description']  = '';
$modversion['templates'][7]['file']         = 'efqdiralpha1_submit.tpl';
$modversion['templates'][7]['description']  = '';
$modversion['templates'][8]['file']         = 'efqdiralpha1_directories.tpl';
$modversion['templates'][8]['description']  = '';
$modversion['templates'][9]['file']         = 'efqdiralpha1_smalldirectory.tpl';
$modversion['templates'][9]['description']  = '';
$modversion['templates'][10]['file']        = 'efqdiralpha1_savings.tpl';
$modversion['templates'][10]['description'] = '';
$modversion['templates'][11]['file']        = 'efqdiralpha1_search.tpl';
$modversion['templates'][11]['description'] = '';
$modversion['templates'][12]['file']        = 'efqdiralpha1_subscriptions.tpl';
$modversion['templates'][12]['description'] = '';
$modversion['templates'][13]['file']        = 'efqdiralpha1_smallsubscription.tpl';
$modversion['templates'][13]['description'] = '';
$modversion['templates'][14]['file']        = 'efqdiralpha1_print_savings.tpl';
$modversion['templates'][14]['description'] = '';

// Config Settings (only for modules that need config settings generated automatically)

// name of config option for accessing its specified value. i.e. $helper->getConfig('storyhome')
$modversion['config'][1]['name'] = 'popular';

// title of this config option displayed in config settings form
$modversion['config'][1]['title'] = '_MI_EFQDIR_POPULAR';

// description of this config option displayed under title
$modversion['config'][1]['description'] = '_MI_EFQDIR_POPULARDSC';

// form element type used in config form for this option. can be one of either textbox, textarea, select, select_multi, yesno, group, group_multi
$modversion['config'][1]['formtype'] = 'select';

// value type of this config option. can be one of either int, text, float, array, or other
// form type of 'group_multi', 'select_multi' must always be 'array'
// form type of 'yesno', 'group' must be always be 'int'
$modversion['config'][1]['valuetype'] = 'int';

// the default value for this option
// ignore it if no default
// 'yesno' formtype must be either 0(no) or 1(yes)
$modversion['config'][1]['default'] = 100;

// options to be displayed in selection box
// required and valid for 'select' or 'select_multi' formtype option only
// language constants can be used for both array keys and values
$modversion['config'][1]['options'] = ['5' => 5, '10' => 10, '50' => 50, '100' => 100, '200' => 200, '500' => 500, '1000' => 1000];

$modversion['config'][2]['name']        = 'newlistings';
$modversion['config'][2]['title']       = '_MI_EFQDIR_NEWLISTINGS';
$modversion['config'][2]['description'] = '_MI_EFQDIR_NEWLISTINGSDSC';
$modversion['config'][2]['formtype']    = 'select';
$modversion['config'][2]['valuetype']   = 'int';
$modversion['config'][2]['default']     = 10;
$modversion['config'][2]['options']     = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];

$modversion['config'][3]['name']        = 'perpage';
$modversion['config'][3]['title']       = '_MI_EFQDIR_PERPAGE';
$modversion['config'][3]['description'] = '_MI_EFQDIR_PERPAGEDSC';
$modversion['config'][3]['formtype']    = 'select';
$modversion['config'][3]['valuetype']   = 'int';
$modversion['config'][3]['default']     = 10;
$modversion['config'][3]['options']     = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];

/*$modversion['config'][4]['name'] = 'anonpost';
$modversion['config'][4]['title'] = '_MI_EFQDIR_ANONPOST';
$modversion['config'][4]['description'] = '';
$modversion['config'][4]['formtype'] = 'yesno';
$modversion['config'][4]['valuetype'] = 'int';
$modversion['config'][4]['default'] = 0;*/

$modversion['config'][5]['name']        = 'autoapprove';
$modversion['config'][5]['title']       = '_MI_EFQDIR_AUTOAPPROVE';
$modversion['config'][5]['description'] = '_MI_EFQDIR_AUTOAPPROVEDSC';
$modversion['config'][5]['formtype']    = 'yesno';
$modversion['config'][5]['valuetype']   = 'int';
$modversion['config'][5]['default']     = 0;

$modversion['config'][6]['name']        = 'autoapproveadmin';
$modversion['config'][6]['title']       = '_MI_EFQDIR_AUTOAPPROVEADMIN';
$modversion['config'][6]['description'] = '_MI_EFQDIR_AUTOAPPROVEADMINDSC';
$modversion['config'][6]['formtype']    = 'yesno';
$modversion['config'][6]['valuetype']   = 'int';
$modversion['config'][6]['default']     = 1;

/*$modversion['config'][6]['name'] = 'usedestmod';
$modversion['config'][6]['title'] = '_MI_EFQDIR_USEDESTMOD';
$modversion['config'][6]['description'] = '_MI_EFQDIR_USEDESTMOD_DSC';
$modversion['config'][6]['formtype'] = 'yesno';
$modversion['config'][6]['valuetype'] = 'int';
$modversion['config'][6]['default'] = 0;*/

$modversion['config'][7]['name']        = 'searchresults_perpage';
$modversion['config'][7]['title']       = '_MI_EFQDIR_RESULTSPERPAGE';
$modversion['config'][7]['description'] = '_MI_EFQDIR_RESULTSPERPAGE_DSC';
$modversion['config'][7]['formtype']    = 'select';
$modversion['config'][7]['valuetype']   = 'int';
$modversion['config'][7]['default']     = 10;
$modversion['config'][7]['options']     = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];

$modversion['config'][8]['name']        = 'anonvotes_waitdays';
$modversion['config'][8]['title']       = '_MI_EFQDIR_ANONVOTESWAITDAYS';
$modversion['config'][8]['description'] = '_MI_EFQDIR_ANONVOTESWAITDAYS_DSC';
$modversion['config'][8]['formtype']    = 'select';
$modversion['config'][8]['valuetype']   = 'int';
$modversion['config'][8]['default']     = 1;
$modversion['config'][8]['options']     = ['1' => 1, '2' => 2, '3' => 3, '7' => 7, '14' => 14, '30' => 30, '365' => 365];

$modversion['config'][9]['name']        = 'autoshowonedir';
$modversion['config'][9]['title']       = '_MI_EFQDIR_AUTOSHOWONEDIR';
$modversion['config'][9]['description'] = '_MI_EFQDIR_AUTOSHOWONEDIR_DSC';
$modversion['config'][9]['formtype']    = 'yesno';
$modversion['config'][9]['valuetype']   = 'int';
$modversion['config'][9]['default']     = 1;
//$modversion['config'][9]['options'] = array('1' => 1, '2' => 2, '3' => 3, '7' => 7, '14' => 14, '30' => 30, '365' => 365);

$modversion['config'][10]['name']        = 'allowrating';
$modversion['config'][10]['title']       = '_MI_EFQDIR_ALLOWRATING';
$modversion['config'][10]['description'] = '_MI_EFQDIR_ALLOWRATING_DSC';
$modversion['config'][10]['formtype']    = 'yesno';
$modversion['config'][10]['valuetype']   = 'int';
$modversion['config'][10]['default']     = 1; //By default allow listings to be rated

$modversion['config'][11]['name']        = 'allowtellafriend';
$modversion['config'][11]['title']       = '_MI_EFQDIR_ALLOWTELLAFRIEND';
$modversion['config'][11]['description'] = '_MI_EFQDIR_ALLOWTELLAFRIEND_DSC';
$modversion['config'][11]['formtype']    = 'yesno';
$modversion['config'][11]['valuetype']   = 'int';
$modversion['config'][11]['default']     = 0; //By default allow to show tell a friend link in listing

/*$modversion['config'][12]['name'] = 'allowcomments';
$modversion['config'][12]['title'] = '_MI_EFQDIR_ALLOWCOMMENTS';
$modversion['config'][12]['description'] = '_MI_EFQDIR_ALLOWCOMMENTS_DSC';
$modversion['config'][12]['formtype'] = 'yesno';
$modversion['config'][12]['valuetype'] = 'int';
$modversion['config'][12]['default'] = 1; //By default allow to add comments to listings

$modversion['config'][13]['name'] = 'allowreviews';
$modversion['config'][13]['title'] = '_MI_EFQDIR_ALLOWREVIEWS';
$modversion['config'][13]['description'] = '_MI_EFQDIR_ALLOWREVIEWS_DSC';
$modversion['config'][13]['formtype'] = 'yesno';
$modversion['config'][13]['valuetype'] = 'int';
$modversion['config'][13]['default'] = 1; //By default allow reviews of listings for each directory
*/
$modversion['config'][14]['name']        = 'allowcoupons';
$modversion['config'][14]['title']       = '_MI_EFQDIR_ALLOWCOUPONS';
$modversion['config'][14]['description'] = '_MI_EFQDIR_ALLOWCOUPONS_DSC';
$modversion['config'][14]['formtype']    = 'yesno';
$modversion['config'][14]['valuetype']   = 'int';
$modversion['config'][14]['default']     = 0; //By default allow coupons to be added to listings

/*$modversion['config'][15]['name'] = 'server_remote_address';
$modversion['config'][15]['title'] = '_MI_EFQDIR_REMOTE_ADDRESS';
$modversion['config'][15]['description'] = '_MI_EFQDIR_REMOTE_ADDRESS_DSC';
$modversion['config'][15]['formtype'] = 'textbox';
$modversion['config'][15]['valuetype'] = 'text';
$modversion['config'][15]['default'] = '127.0.0.1'; //By default use the server address of localhost
*/
$modversion['config'][16]['name']        = 'warningtime';
$modversion['config'][16]['title']       = '_MI_EFQDIR_WARNINGTIME';
$modversion['config'][16]['description'] = '_MI_EFQDIR_WARNINGTIME_DSC';
$modversion['config'][16]['formtype']    = 'textbox';
$modversion['config'][16]['valuetype']   = 'int';
$modversion['config'][16]['default']     = '3'; //By default 3 days of warning time

$modversion['config'][17]['name']        = 'paypal_test';
$modversion['config'][17]['title']       = '_MI_EFQDIR_PAYPAL_TEST';
$modversion['config'][17]['description'] = '_MI_EFQDIR_PAYPAL_TEST_DSC';
$modversion['config'][17]['formtype']    = 'yesno';
$modversion['config'][17]['valuetype']   = 'int';
$modversion['config'][17]['default']     = 1; //By default set Paypal url to sandbox;

$modversion['config'][18]['name']        = 'paypal_secure_yn';
$modversion['config'][18]['title']       = '_MI_EFQDIR_PAYPAL_SECURE_YN';
$modversion['config'][18]['description'] = '_MI_EFQDIR_PAYPAL_SECURE_YN_DSC';
$modversion['config'][18]['formtype']    = 'yesno';
$modversion['config'][18]['valuetype']   = 'int';
$modversion['config'][18]['default']     = 1; //By default use https protocol for more secure payments.

$modversion['config'][19]['name']        = 'paypal_business_mail';
$modversion['config'][19]['title']       = '_MI_EFQDIR_PAYPAL_BUS_MAIL';
$modversion['config'][19]['description'] = '_MI_EFQDIR_PAYPAL_BUS_MAIL_DSC';
$modversion['config'][19]['formtype']    = 'textbox';
$modversion['config'][19]['valuetype']   = 'text';
$modversion['config'][19]['default']     = '';

$modversion['config'][20]['name']        = 'allowsubscr';
$modversion['config'][20]['title']       = '_MI_EFQDIR_ALLOW_SUBSCR';
$modversion['config'][20]['description'] = '_MI_EFQDIR_ALLOW_SUBSCR_DSC';
$modversion['config'][20]['formtype']    = 'yesno';
$modversion['config'][20]['valuetype']   = 'int';
$modversion['config'][20]['default']     = 0;

$modversion['config'][21]['name']        = 'showlinkimages';
$modversion['config'][21]['title']       = '_MI_EFQDIR_SHOW_LINK_IMAGES';
$modversion['config'][21]['description'] = '_MI_EFQDIR_SHOW_LINK_IMAGES_DSC';
$modversion['config'][21]['formtype']    = 'yesno';
$modversion['config'][21]['valuetype']   = 'int';
$modversion['config'][21]['default']     = 0;

$modversion['config'][22]['name']        = 'showdatafieldsincat';
$modversion['config'][22]['title']       = '_MI_EFQDIR_SHOW_DFIELDSINCAT';
$modversion['config'][22]['description'] = '_MI_EFQDIR_SHOW_DATAFIELDSINCAT_DSC';
$modversion['config'][22]['formtype']    = 'yesno';
$modversion['config'][22]['valuetype']   = 'int';
$modversion['config'][22]['default']     = 0;

$modversion['config'][23]['name']        = 'catimagemaxsize';
$modversion['config'][23]['title']       = '_MI_EFQDIR_CAT_IMGMAXSIZE';
$modversion['config'][23]['description'] = '_MI_EFQDIR_CAT_IMGMAXSIZE_DSC';
$modversion['config'][23]['formtype']    = 'textbox';
$modversion['config'][23]['valuetype']   = 'int';
$modversion['config'][23]['default']     = 100000;

$modversion['config'][24]['name']        = 'catimagemaxwidth';
$modversion['config'][24]['title']       = '_MI_EFQDIR_CAT_IMGMAXWIDTH';
$modversion['config'][24]['description'] = '_MI_EFQDIR_CAT_IMGMAXWIDTH_DSC';
$modversion['config'][24]['formtype']    = 'textbox';
$modversion['config'][24]['valuetype']   = 'int';
$modversion['config'][24]['default']     = 50;

$modversion['config'][25]['name']        = 'catimagemaxheight';
$modversion['config'][25]['title']       = '_MI_EFQDIR_CAT_IMGMAXHEIGHT';
$modversion['config'][25]['description'] = '_MI_EFQDIR_CAT_IMGMAXHEIGHT_DSC';
$modversion['config'][25]['formtype']    = 'textbox';
$modversion['config'][25]['valuetype']   = 'int';
$modversion['config'][25]['default']     = 50;

$modversion['config'][26]['name']        = 'dirimagemaxsize';
$modversion['config'][26]['title']       = '_MI_EFQDIR_DIR_IMGMAXSIZE';
$modversion['config'][26]['description'] = '_MI_EFQDIR_DIR_IMGMAXSIZE_DSC';
$modversion['config'][26]['formtype']    = 'textbox';
$modversion['config'][26]['valuetype']   = 'int';
$modversion['config'][26]['default']     = 100000;

$modversion['config'][27]['name']        = 'dirimagemaxwidth';
$modversion['config'][27]['title']       = '_MI_EFQDIR_DIR_IMGMAXWIDTH';
$modversion['config'][27]['description'] = '_MI_EFQDIR_DIR_IMGMAXWIDTH_DSC';
$modversion['config'][27]['formtype']    = 'textbox';
$modversion['config'][27]['valuetype']   = 'int';
$modversion['config'][27]['default']     = 50;

$modversion['config'][28]['name']        = 'dirimagemaxheight';
$modversion['config'][28]['title']       = '_MI_EFQDIR_DIR_IMGMAXHEIGHT';
$modversion['config'][28]['description'] = '_MI_EFQDIR_DIR_IMGMAXHEIGHT_DSC';
$modversion['config'][28]['formtype']    = 'textbox';
$modversion['config'][28]['valuetype']   = 'int';
$modversion['config'][28]['default']     = 50;

$modversion['config'][29]['name']        = 'imagemaxsize';
$modversion['config'][29]['title']       = '_MI_EFQDIR_IMGMAXSIZE';
$modversion['config'][29]['description'] = '_MI_EFQDIR_IMGMAXSIZE_DSC';
$modversion['config'][29]['formtype']    = 'textbox';
$modversion['config'][29]['valuetype']   = 'int';
$modversion['config'][29]['default']     = 100000;

// Notification
$modversion['hasNotification'] = 0;
/*
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'listings_notify_iteminfo';

$modversion['notification']['category'][1]['name'] = 'global';
$modversion['notification']['category'][1]['title'] = _MI_EFQDIR_GLOBAL_NOTIFY;
$modversion['notification']['category'][1]['description'] = _MI_EFQDIR_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][1]['subscribe_from'] = array('index.php','listing.php');

$modversion['notification']['category'][2]['name'] = 'category';
$modversion['notification']['category'][2]['title'] = _MI_EFQDIR_CATEGORY_NOTIFY;
$modversion['notification']['category'][2]['description'] = _MI_EFQDIR_CATEGORY_NOTIFYDSC;
$modversion['notification']['category'][2]['subscribe_from'] = array('index.php', 'listing.php');
$modversion['notification']['category'][2]['item_name'] = 'cid';
$modversion['notification']['category'][2]['allow_bookmark'] = 1;

$modversion['notification']['category'][3]['name'] = 'link';
$modversion['notification']['category'][3]['title'] = _MI_EFQDIR_LISTING_NOTIFY;
$modversion['notification']['category'][3]['description'] = _MI_EFQDIR_LISTING_NOTIFYDSC;
$modversion['notification']['category'][3]['subscribe_from'] = 'listing.php';
$modversion['notification']['category'][3]['item_name'] = 'lid';
$modversion['notification']['category'][3]['allow_bookmark'] = 1;

$modversion['notification']['event'][1]['name'] = 'new_category';
$modversion['notification']['event'][1]['category'] = 'global';
$modversion['notification']['event'][1]['title'] = _MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFY;
$modversion['notification']['event'][1]['caption'] = _MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFYCAP;
$modversion['notification']['event'][1]['description'] = _MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFYDSC;
$modversion['notification']['event'][1]['mail_template'] = 'global_newcategory_notify';
$modversion['notification']['event'][1]['mail_subject'] = _MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFYSBJ;
*/
$modversion['notification']['event'][2]['name']          = 'listing_modify';
$modversion['notification']['event'][2]['category']      = 'global';
$modversion['notification']['event'][2]['admin_only']    = 1;
$modversion['notification']['event'][2]['title']         = _MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFY;
$modversion['notification']['event'][2]['caption']       = _MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFYCAP;
$modversion['notification']['event'][2]['description']   = _MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFYDSC;
$modversion['notification']['event'][2]['mail_template'] = 'global_listingmodify_notify';
$modversion['notification']['event'][2]['mail_subject']  = _MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFYSBJ;

/*$modversion['notification']['event'][3]['name'] = 'listing_broken';
$modversion['notification']['event'][3]['category'] = 'global';
$modversion['notification']['event'][3]['admin_only'] = 1;
$modversion['notification']['event'][3]['title'] = _MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFY;
$modversion['notification']['event'][3]['caption'] = _MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFYCAP;
$modversion['notification']['event'][3]['description'] = _MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFYDSC;
$modversion['notification']['event'][3]['mail_template'] = 'global_listingbroken_notify';
$modversion['notification']['event'][3]['mail_subject'] = _MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFYSBJ;*/

$modversion['notification']['event'][4]['name']          = 'listing_submit';
$modversion['notification']['event'][4]['category']      = 'global';
$modversion['notification']['event'][4]['admin_only']    = 1;
$modversion['notification']['event'][4]['title']         = _MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFY;
$modversion['notification']['event'][4]['caption']       = _MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFYCAP;
$modversion['notification']['event'][4]['description']   = _MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFYDSC;
$modversion['notification']['event'][4]['mail_template'] = 'global_listingsubmit_notify';
$modversion['notification']['event'][4]['mail_subject']  = _MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFYSBJ;

$modversion['notification']['event'][5]['name']          = 'new_listing';
$modversion['notification']['event'][5]['category']      = 'global';
$modversion['notification']['event'][5]['title']         = _MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFY;
$modversion['notification']['event'][5]['caption']       = _MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFYCAP;
$modversion['notification']['event'][5]['description']   = _MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFYDSC;
$modversion['notification']['event'][5]['mail_template'] = 'global_newlisting_notify';
$modversion['notification']['event'][5]['mail_subject']  = _MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFYSBJ;

$modversion['notification']['event'][8]['name']          = 'approve';
$modversion['notification']['event'][8]['category']      = 'listing';
$modversion['notification']['event'][8]['invisible']     = 1;
$modversion['notification']['event'][8]['title']         = _MI_EFQDIR_LISTING_APPROVE_NOTIFY;
$modversion['notification']['event'][8]['caption']       = _MI_EFQDIR_LISTING_APPROVE_NOTIFYCAP;
$modversion['notification']['event'][8]['description']   = _MI_EFQDIR_LISTING_APPROVE_NOTIFYDSC;
$modversion['notification']['event'][8]['mail_template'] = 'listing_approve_notify';
$modversion['notification']['event'][8]['mail_subject']  = _MI_EFQDIR_LISTING_APPROVE_NOTIFYSBJ;
