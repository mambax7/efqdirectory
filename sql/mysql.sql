-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jul 16, 2006 at 09:57 PM
-- 
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_address_types`
-- 

CREATE TABLE `efqdiralpha1_address_types` (
  `typeid` int(4) NOT NULL auto_increment,
  `defaultyn` tinyint(2) NOT NULL default '0',
  `locid` int(11) NOT NULL default '0',
  `address` tinyint(2) NOT NULL default '0',
  `address2` tinyint(2) NOT NULL default '0',
  `zip` tinyint(2) NOT NULL default '0',
  `postcode` tinyint(2) NOT NULL default '0',
  `lat` tinyint(2) NOT NULL default '0',
  `lon` tinyint(2) NOT NULL default '0',
  `phone` tinyint(2) NOT NULL,
  `fax` tinyint(2) NOT NULL default '0',
  `mobile` tinyint(2) NOT NULL default '0',
  `city` tinyint(2) NOT NULL default '0',
  `country` tinyint(2) NOT NULL,
  `typename` varchar(255) NOT NULL,
  `uselocyn` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`typeid`)
) TYPE=MyISAM COMMENT='address types' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_cat`
-- 

CREATE TABLE `efqdiralpha1_cat` (
  `cid` int(7) unsigned NOT NULL auto_increment,
  `dirid` int(8) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `active` tinyint(1) unsigned NOT NULL default '0',
  `pid` int(7) unsigned NOT NULL default '0',
  `img` varchar(60) NOT NULL default '',
  `allowlist` tinyint(1) unsigned NOT NULL default '0',
  `showpopular` tinyint(1) unsigned NOT NULL default '0',
  `width` int(8) unsigned NOT NULL default '0',
  `height` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `title` (`title`),
  KEY `active` (`active`),
  KEY `pid` (`pid`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_cat_tpl`
-- 

CREATE TABLE `efqdiralpha1_cat_tpl` (
  `xid` int(11) NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `tplid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`xid`)
) TYPE=MyISAM COMMENT='Category x templates' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_cat_txt`
-- 

CREATE TABLE `efqdiralpha1_cat_txt` (
  `txtid` int(7) unsigned NOT NULL auto_increment,
  `cid` int(7) unsigned NOT NULL default '0',
  `text` text NOT NULL,
  `active` tinyint(1) unsigned NOT NULL default '0',
  `created` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`txtid`),
  KEY `active` (`active`),
  KEY `cid` (`cid`)
) TYPE=MyISAM AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_coupon`
-- 

CREATE TABLE `efqdiralpha1_coupon` (
  `couponid` int(12) unsigned NOT NULL auto_increment,
  `itemid` int(11) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `image` text NOT NULL,
  `publish` int(10) unsigned NOT NULL default '0',
  `expire` int(10) unsigned NOT NULL default '0',
  `heading` text NOT NULL,
  `lbr` int(1) NOT NULL default '0',
  `counter` int(10) unsigned NOT NULL default '0',
  `addrid` int(10) NOT NULL default '0',
  PRIMARY KEY  (`couponid`),
  KEY `addrid` (`addrid`),
  KEY `itemid` (`itemid`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_data`
-- 

CREATE TABLE `efqdiralpha1_data` (
  `dataid` int(12) unsigned NOT NULL auto_increment,
  `itemid` int(11) unsigned NOT NULL default '0',
  `dtypeid` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  `created` int(10) NOT NULL default '0',
  `customtitle` varchar(255) NOT NULL,
  PRIMARY KEY  (`dataid`),
  KEY `dtypeid` (`dtypeid`)
) TYPE=MyISAM AUTO_INCREMENT=142 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_dir`
-- 

CREATE TABLE `efqdiralpha1_dir` (
  `dirid` int(5) unsigned NOT NULL auto_increment,
  `postfix` varchar(20) NOT NULL default '',
  `open` tinyint(2) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `descr` longtext NOT NULL,
  `img` varchar(50) NOT NULL default '',
  `allowreview` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`dirid`)
) TYPE=MyISAM COMMENT='Directories' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_dtypes`
-- 

CREATE TABLE `efqdiralpha1_dtypes` (
  `dtypeid` int(6) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `section` smallint(3) NOT NULL default '0',
  `fieldtypeid` smallint(3) unsigned NOT NULL default '0',
  `uid` int(10) unsigned NOT NULL default '0',
  `defaultyn` tinyint(1) NOT NULL default '0',
  `created` int(10) NOT NULL default '0',
  `seq` int(5) NOT NULL default '0',
  `activeyn` tinyint(2) NOT NULL default '0',
  `options` text NOT NULL,
  `custom` tinyint(2) NOT NULL default '0',
  `icon` varchar(100) NOT NULL,
  PRIMARY KEY  (`dtypeid`),
  KEY `typeid` (`fieldtypeid`)
) TYPE=MyISAM AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_dtypes_x_cat`
-- 

CREATE TABLE `efqdiralpha1_dtypes_x_cat` (
  `xid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `dtypeid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`xid`)
) TYPE=MyISAM COMMENT='Cross tabel categories linked to dtypes' AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_fieldtypes`
-- 

CREATE TABLE `efqdiralpha1_fieldtypes` (
  `typeid` smallint(3) unsigned NOT NULL auto_increment,
  `dirid` int(5) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `fieldtype` varchar(100) NOT NULL default '',
  `descr` text NOT NULL,
  `ext` varchar(255) NOT NULL default '',
  `activeyn` tinyint(2) NOT NULL default '1',
  PRIMARY KEY  (`typeid`)
) TYPE=MyISAM COMMENT='Data field types' AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_form_options`
-- 

CREATE TABLE `efqdiralpha1_form_options` (
  `id` int(11) NOT NULL auto_increment,
  `dtypeid` int(11) NOT NULL default '0',
  `option` varchar(255) NOT NULL,
  `activeyn` tinyint(1) NOT NULL default '0',
  `seq` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `dtypeid` (`dtypeid`)
) TYPE=MyISAM COMMENT='Form options' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_item_img`
-- 

CREATE TABLE `efqdiralpha1_item_img` (
  `id` int(11) NOT NULL auto_increment,
  `dirid` int(6) unsigned NOT NULL default '0',
  `itemid` int(11) unsigned NOT NULL default '0',
  `dtypeid` int(6) unsigned NOT NULL default '0',
  `created` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_item_text`
-- 

CREATE TABLE `efqdiralpha1_item_text` (
  `itemid` int(11) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  KEY `itemid` (`itemid`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_item_x_cat`
-- 

CREATE TABLE `efqdiralpha1_item_x_cat` (
  `xid` int(11) unsigned NOT NULL auto_increment,
  `cid` int(7) unsigned NOT NULL default '0',
  `itemid` int(10) unsigned NOT NULL default '0',
  `active` tinyint(1) unsigned NOT NULL default '0',
  `created` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`xid`),
  KEY `cid` (`cid`),
  KEY `itemid` (`itemid`),
  KEY `active` (`active`)
) TYPE=MyISAM AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_item_x_loc`
-- 

CREATE TABLE `efqdiralpha1_item_x_loc` (
  `locid` int(11) unsigned NOT NULL default '0',
  `itemid` int(11) unsigned NOT NULL default '0',
  `seq` tinyint(2) NOT NULL default '0',
  KEY `locdestid` (`locid`),
  KEY `loctypeid` (`itemid`),
  KEY `loctypeid_2` (`itemid`)
) TYPE=MyISAM COMMENT='Attach item to location';

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_items`
-- 

CREATE TABLE `efqdiralpha1_items` (
  `itemid` int(11) unsigned NOT NULL auto_increment,
  `logourl` varchar(60) NOT NULL default '',
  `uid` int(11) unsigned NOT NULL default '0',
  `status` tinyint(2) NOT NULL default '0',
  `created` int(10) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `hits` int(11) NOT NULL default '0',
  `rating` double(5,4) NOT NULL default '0.0000',
  `votes` int(11) NOT NULL default '0',
  `typeid` int(8) NOT NULL default '0',
  `dirid` int(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`itemid`),
  KEY `status` (`status`),
  KEY `title` (`title`(50)),
  KEY `typeid` (`typeid`),
  KEY `dirid` (`dirid`)
) TYPE=MyISAM AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_itemtypes`
-- 

CREATE TABLE `efqdiralpha1_itemtypes` (
  `typeid` int(3) unsigned NOT NULL auto_increment,
  `typename` varchar(50) NOT NULL default '0',
  `level` int(4) NOT NULL default '0',
  `dirid` int(5) NOT NULL default '0',
  PRIMARY KEY  (`typeid`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_loc`
-- 

CREATE TABLE `efqdiralpha1_loc` (
  `locid` int(7) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `amazonname` varchar(100) default NULL,
  `fullname` varchar(150) default NULL,
  `searchname` varchar(150) NOT NULL default '',
  `levelid` tinyint(2) NOT NULL default '0',
  `plocid` int(7) unsigned NOT NULL default '0',
  `loctypeid` int(3) unsigned NOT NULL default '0',
  `icaoid` int(6) unsigned NOT NULL default '0',
  `childloctypeid` int(3) unsigned NOT NULL default '0',
  `featatrid` int(8) unsigned default '0',
  `status` tinyint(1) NOT NULL default '0',
  `popular` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`locid`),
  KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_loc_types`
-- 

CREATE TABLE `efqdiralpha1_loc_types` (
  `loctypeid` int(3) unsigned NOT NULL auto_increment,
  `typename` varchar(50) NOT NULL default '0',
  `level` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`loctypeid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_loc_x_loctype`
-- 

CREATE TABLE `efqdiralpha1_loc_x_loctype` (
  `locid` int(11) unsigned NOT NULL default '0',
  `loctypeid` int(3) unsigned NOT NULL default '0',
  `seq` tinyint(2) NOT NULL default '0',
  KEY `locdestid` (`locid`),
  KEY `loctypeid` (`loctypeid`)
) TYPE=MyISAM COMMENT='Attach levels to location';

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_searchresults`
-- 

CREATE TABLE `efqdiralpha1_searchresults` (
  `searchid` int(11) NOT NULL auto_increment,
  `searchnum` varchar(50) NOT NULL,
  `created` varchar(10) NOT NULL,
  `page` smallint(4) NOT NULL,
  `items` text NOT NULL,
  `dirid` int(5) NOT NULL,
  `catid` int(7) NOT NULL,
  PRIMARY KEY  (`searchid`),
  KEY `searchnum` (`searchnum`,`dirid`,`catid`)
) TYPE=MyISAM AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_subscr_notify`
-- 

CREATE TABLE `efqdiralpha1_subscr_notify` (
  `id` int(11) NOT NULL auto_increment,
  `method` tinyint(2) NOT NULL default '0',
  `datetime` varchar(10) NOT NULL,
  `msg` int(5) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_subscr_offers`
-- 

CREATE TABLE `efqdiralpha1_subscr_offers` (
  `offerid` int(5) NOT NULL auto_increment,
  `dirid` int(5) NOT NULL default '0',
  `typeid` int(5) NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `duration` int(5) NOT NULL default '0',
  `count` int(5) NOT NULL default '0',
  `price` double(5,2) NOT NULL default '0.00',
  `activeyn` tinyint(2) NOT NULL default '0',
  `currency` varchar(10) NOT NULL,
  `descr` longtext NOT NULL,
  PRIMARY KEY  (`offerid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_subscr_orders`
-- 

CREATE TABLE `efqdiralpha1_subscr_orders` (
  `orderid` int(10) NOT NULL auto_increment,
  `uid` int(10) NOT NULL,
  `offerid` int(10) NOT NULL,
  `typeid` int(4) NOT NULL,
  `startdate` varchar(10) NOT NULL,
  `enddate` varchar(10) NOT NULL,
  `billto` varchar(10) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `itemid` int(10) NOT NULL,
  `autorenew` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`orderid`)
) TYPE=MyISAM AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_subscr_payments`
-- 

CREATE TABLE `efqdiralpha1_subscr_payments` (
  `id` int(12) NOT NULL auto_increment,
  `txn_id` varchar(150) NOT NULL,
  `txn_type` varchar(50) NOT NULL,
  `orderid` int(10) NOT NULL,
  `payer_business_name` varchar(200) NOT NULL,
  `address_name` varchar(200) NOT NULL,
  `address_street` varchar(200) NOT NULL,
  `address_city` varchar(200) NOT NULL,
  `address_state` varchar(200) NOT NULL,
  `address_zip` varchar(200) NOT NULL,
  `address_country` varchar(200) NOT NULL,
  `address_status` varchar(200) NOT NULL,
  `payer_email` varchar(200) NOT NULL,
  `payer_id` varchar(200) NOT NULL,
  `payer_status` varchar(200) NOT NULL,
  `mc_currency` varchar(10) NOT NULL,
  `mc_gross` double(10,2) NOT NULL,
  `mc_fee` double(9,2) NOT NULL,
  `created` varchar(10) NOT NULL,
  `payment_date` varchar(50) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_subscr_scheduler`
-- 

CREATE TABLE `efqdiralpha1_subscr_scheduler` (
  `id` int(11) NOT NULL auto_increment,
  `startdate` int(10) NOT NULL,
  `itemid` int(11) NOT NULL,
  `newtypeid` int(6) NOT NULL,
  `status` tinyint(2) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_tpl`
-- 

CREATE TABLE `efqdiralpha1_tpl` (
  `tplid` int(5) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `activeyn` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`tplid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `efqdiralpha1_votedata`
-- 

CREATE TABLE `efqdiralpha1_votedata` (
  `ratingid` int(11) NOT NULL auto_increment,
  `itemid` int(11) NOT NULL default '0',
  `ratinguser` int(11) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `ratinghostname` varchar(60) NOT NULL default '',
  `ratingtimestamp` int(10) NOT NULL default '0',
  PRIMARY KEY  (`ratingid`),
  KEY `ratinguser` (`ratinguser`),
  KEY `ratinghostname` (`ratinghostname`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


-- 
-- Dumping data for table `efqdiralpha1_itemtypes`
-- 

INSERT INTO `efqdiralpha1_itemtypes` (`typeid`, `typename`, `level`) VALUES (1, 'Bronze', 1);
INSERT INTO `efqdiralpha1_itemtypes` (`typeid`, `typename`, `level`) VALUES (2, 'Silver', 2);
INSERT INTO `efqdiralpha1_itemtypes` (`typeid`, `typename`, `level`) VALUES (3, 'Gold', 3);

-- 
-- Dumping data for table `efqdiralpha1_fieldtypes`
-- 

INSERT INTO `efqdiralpha1_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (9, 'Textbox default (50 - 100)', 'textbox', 'Default text box', 'size=50|maxsize=100', 1);
INSERT INTO `efqdiralpha1_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (10, 'Yes/No', 'yesno', 'Yes or no', '', 1);
INSERT INTO `efqdiralpha1_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (11, 'Text Area (10 - 50)', 'textarea', 'Normal text area', 'rows=10|cols=50', 1);
INSERT INTO `efqdiralpha1_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (12, 'Select box', 'select', 'Select a rating', '', 1);
INSERT INTO `efqdiralpha1_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (13, 'DHTML text area', 'dhtml', 'Default DHTML area', 'rows=10|cols=50', 1);