<?php

// The name of this module
define('_MI_EFQDIR_NAME', 'Listings directory');

// A brief description of this module
define('_MI_EFQDIR_DESC', 'Creates a listings section where directories of listings can be managed.');

// Names of blocks for this module (Not every module has blocks)
define('_MI_EFQDIR_BNAME1', 'Recent Listings');
define('_MI_EFQDIR_BNAME2', 'Top Listings');
define('_MI_EFQDIR_MENU', 'Directories');

// Sub menu titles
define('_MI_EFQDIR_SMNAME1', 'Submit');
define('_MI_EFQDIR_SMNAME2', 'Popular');
define('_MI_EFQDIR_SMNAME3', 'Top Rated');

// Names of admin menu items
define('_MI_EFQDIR_ADMENU1', 'Home');
define('_MI_EFQDIR_ADMENU2', 'Categories');
define('_MI_EFQDIR_ADMENU3', 'Directories');
define('_MI_EFQDIR_ADMENU4', 'Field Types');
define('_MI_EFQDIR_ADMENU5', 'Validations');
define('_MI_EFQDIR_ADMENU6', 'Address Types');
define('_MI_EFQDIR_ADMENU7', 'Subscriptions');
define('_MI_EFQDIR_ADMENU8', 'Duplicate Data types');
define('_AM_EFQDIR_HOME', 'Home');
define('_AM_EFQDIR_ABOUT', 'About');

// Title of config items
define('_MI_EFQDIR_POPULAR', 'Select the number of hits for listings to be marked as popular');
define('_MI_EFQDIR_NEWLISTINGS', 'Select the maximum number of new listings displayed on top page');
define('_MI_EFQDIR_PERPAGE', 'Select the maximum number of listings displayed in each page');
define('_MI_EFQDIR_USESHOTS', 'Select yes to display screenshot images for each listing');
define('_MI_EFQDIR_USEDESTMOD', 'Use this module in combination with the destinations module?');
define('_MI_EFQDIR_ANONPOST', 'Allow anonymous users to post listings?');
define('_MI_EFQDIR_AUTOAPPROVE', 'Auto approve new listings without admin intervention?');
define('_MI_EFQDIR_RESULTSPERPAGE', 'Select the number of search results per page.');
define('_MI_EFQDIR_ANONVOTESWAITDAYS', 'Select the number of days before an anonymous user can vote for a listing again.');
define('_MI_EFQDIR_AUTOSHOWONEDIR', 'Show directory automatically if there is only one active directory?');

define('_MI_EFQDIR_ALLOWRATING', 'Allow visitors to rate listings?');
define('_MI_EFQDIR_ALLOWTELLAFRIEND', 'Show the "tell a friend" link?');
define('_MI_EFQDIR_ALLOWCOMMENTS', 'Allow visitors to add comments to a listing?');
define('_MI_EFQDIR_ALLOWREVIEWS', 'Allow visitors to submit reviews for a listing?');
define('_MI_EFQDIR_ALLOWCOUPONS', 'Allow coupons to be added to listings?');
define('_MI_EFQDIR_REMOTE_ADDRESS', 'To prevent abuse of the built-in scheduler the module requires the IP address of the Host server.');
define('_MI_EFQDIR_WARNINGTIME', 'Set the number of days before a user is notified of an expiring subscription.');
define('_MI_EFQDIR_PAYPAL_TEST', 'Use the Paypal Sandbox test environment instead of real payments?');
define('_MI_EFQDIR_PAYPAL_SECURE_YN', 'Use the Paypal secure payments page (recommended: yes)?');
define('_MI_EFQDIR_PAYPAL_BUS_MAIL', 'Set the e-mail address that is connected to your paypal account');
define('_MI_EFQDIR_SHOW_DFIELDSINCAT', 'Show data fields in category view?');

define('_MI_EFQDIR_ALLOW_SUBSCR', 'Allow subscriptions?');
define('_MI_EFQDIR_SHOW_LINK_IMAGES', 'Show link icons in listings?');

// Description of each config items
define('_MI_EFQDIR_POPULARDSC', '');
define('_MI_EFQDIR_NEWLISTINGSDSC', '');
define('_MI_EFQDIR_PERPAGEDSC', '');
define('_MI_EFQDIR_USEDESTMODDSC', '');
define('_MI_EFQDIR_SHOTWIDTHDSC', '');
define('_MI_EFQDIR_AUTOAPPROVEDSC', '');
define('_MI_EFQDIR_RESULTSPERPAGE_DSC', '');
define('_MI_EFQDIR_ANONVOTESWAITDAYS_DSC', '');
define('_MI_EFQDIR_AUTOSHOWONEDIR_DSC', '');
define('_MI_EFQDIR_ALLOWRATING_DSC', '');
define('_MI_EFQDIR_ALLOWTELLAFRIEND_DSC', '');
define('_MI_EFQDIR_ALLOWCOMMENTS_DSC', '');
define('_MI_EFQDIR_ALLOWREVIEWS_DSC', '');
define('_MI_EFQDIR_ALLOWCOUPONS_DSC', '');
define('_MI_EFQDIR_REMOTE_ADDRESS_DSC', '');
define('_MI_EFQDIR_WARNINGTIME_DSC', '');
define('_MI_EFQDIR_PAYPAL_TEST_DSC', '');
define('_MI_EFQDIR_PAYPAL_SECURE_YN_DSC', '');
define('_MI_EFQDIR_PAYPAL_BUS_MAIL_DSC', '');
define('_MI_EFQDIR_SHOW_DFIELDSINCAT_DSC', '');

// Text for notifications
define('_MI_EFQDIR_GLOBAL_NOTIFY', 'Global');
define('_MI_EFQDIR_GLOBAL_NOTIFYDSC', 'Global links notification options.');

define('_MI_EFQDIR_CATEGORY_NOTIFY', 'Category');
define('_MI_EFQDIR_CATEGORY_NOTIFYDSC', 'Notification options that apply to the current link category.');

define('_MI_EFQDIR_LISTING_NOTIFY', 'Listing');
define('_MI_EFQDIR_LISTING_NOTIFYDSC', 'Notification options that aply to the current listing.');

define('_MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFY', 'New Category');
define('_MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFYCAP', 'Notify me when a new listing category is created.');
define('_MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFYDSC', 'Receive notification when a new listing category is created.');
define('_MI_EFQDIR_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New listing category');

define('_MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFY', 'Modify Listing Requested');
define('_MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFYCAP', 'Notify me of any listing modification request.');
define('_MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFYDSC', 'Receive notification when any listing modification request is submitted.');
define('_MI_EFQDIR_GLOBAL_LISTINGMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Listing Modification Requested');

define('_MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFY', 'Broken Listing Submitted');
define('_MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFYCAP', 'Notify me of any broken listing report.');
define('_MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFYDSC', 'Receive notification when any broken listing report is submitted.');
define('_MI_EFQDIR_GLOBAL_LISTINGBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Broken Listing Reported');

define('_MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFY', 'New Listing Submitted');
define('_MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFYCAP', 'Notify me when any new listing is submitted (awaiting approval).');
define('_MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFYDSC', 'Receive notification when any new listing is submitted (awaiting approval).');
define('_MI_EFQDIR_GLOBAL_LISTINGSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New listing submitted');

define('_MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFY', 'New Listing');
define('_MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFYCAP', 'Notify me when any new listing is posted.');
define('_MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFYDSC', 'Receive notification when any new listing is posted.');
define('_MI_EFQDIR_GLOBAL_NEWLISTING_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New listing');

define('_MI_EFQDIR_CATEGORY_LISTINGSUBMIT_NOTIFY', 'New Listing Submitted');
define('_MI_EFQDIR_CATEGORY_LISTINGSUBMIT_NOTIFYCAP', 'Notify me when a new listing is submitted (awaiting approval) to the current category.');
define('_MI_EFQDIR_CATEGORY_LISTINGSUBMIT_NOTIFYDSC', 'Receive notification when a new listing is submitted (awaiting approval) to the current category.');
define('_MI_EFQDIR_CATEGORY_LISTINGSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New listing submitted in category');

define('_MI_EFQDIR_CATEGORY_NEWLISTING_NOTIFY', 'New Listing');
define('_MI_EFQDIR_CATEGORY_NEWLISTING_NOTIFYCAP', 'Notify me when a new listing is posted to the current category.');
define('_MI_EFQDIR_CATEGORY_NEWLISTING_NOTIFYDSC', 'Receive notification when a new listing is posted to the current category.');
define('_MI_EFQDIR_CATEGORY_NEWLISTING_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New listing in category');

define('_MI_EFQDIR_LISTING_APPROVE_NOTIFY', 'Listing Approved');
define('_MI_EFQDIR_LISTING_APPROVE_NOTIFYCAP', 'Notify me when this listing is approved.');
define('_MI_EFQDIR_LISTING_APPROVE_NOTIFYDSC', 'Receive notification when this listing is approved.');
define('_MI_EFQDIR_LISTING_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Listing approved');

//Help
define('_MI_EFQDIR_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_EFQDIR_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_EFQDIR_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_EFQDIR_OVERVIEW', 'Overview');

//define('_MI_EFQDIR_HELP_DIR', __DIR__);

//help multi-page
define('_MI_EFQDIR_DISCLAIMER', 'Disclaimer');
define('_MI_EFQDIR_LICENSE', 'License');
define('_MI_EFQDIR_SUPPORT', 'Support');
