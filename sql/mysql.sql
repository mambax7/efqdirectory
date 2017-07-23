# phpMyAdmin SQL Dump
# version 2.6.4-pl3
# http://www.phpmyadmin.net
# 
# Host: localhost
# Generation Time: Jul 16, 2006 at 09:57 PM
# 
# 

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_address_types`
# 

CREATE TABLE `efqdirectory_address_types` (
  `typeid`    INT(4)       NOT NULL AUTO_INCREMENT,
  `defaultyn` TINYINT(2)   NOT NULL DEFAULT '0',
  `locid`     INT(11)      NOT NULL DEFAULT '0',
  `address`   TINYINT(2)   NOT NULL DEFAULT '0',
  `address2`  TINYINT(2)   NOT NULL DEFAULT '0',
  `zip`       TINYINT(2)   NOT NULL DEFAULT '0',
  `postcode`  TINYINT(2)   NOT NULL DEFAULT '0',
  `lat`       TINYINT(2)   NOT NULL DEFAULT '0',
  `lon`       TINYINT(2)   NOT NULL DEFAULT '0',
  `phone`     TINYINT(2)   NOT NULL,
  `fax`       TINYINT(2)   NOT NULL DEFAULT '0',
  `mobile`    TINYINT(2)   NOT NULL DEFAULT '0',
  `city`      TINYINT(2)   NOT NULL DEFAULT '0',
  `country`   TINYINT(2)   NOT NULL,
  `typename`  VARCHAR(255) NOT NULL,
  `uselocyn`  TINYINT(2)   NOT NULL DEFAULT '0',
  PRIMARY KEY (`typeid`)
)
  ENGINE = MyISAM
  COMMENT = 'address types'
  AUTO_INCREMENT = 2;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_cat`
# 

CREATE TABLE `efqdirectory_cat` (
  `cid`         INT(7) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `dirid`       INT(8) UNSIGNED     NOT NULL DEFAULT '0',
  `title`       VARCHAR(100)        NOT NULL DEFAULT '',
  `active`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `pid`         INT(7) UNSIGNED     NOT NULL DEFAULT '0',
  `img`         VARCHAR(60)         NOT NULL DEFAULT '',
  `allowlist`   TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `showpopular` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `width`       INT(8) UNSIGNED     NOT NULL DEFAULT '0',
  `height`      INT(8) UNSIGNED     NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`),
  KEY `title` (`title`),
  KEY `active` (`active`),
  KEY `pid` (`pid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 27;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_cat_tpl`
# 

CREATE TABLE `efqdirectory_cat_tpl` (
  `xid`   INT(11) NOT NULL AUTO_INCREMENT,
  `catid` INT(11) NOT NULL DEFAULT '0',
  `tplid` INT(5)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`xid`)
)
  ENGINE = MyISAM
  COMMENT = 'Category x templates'
  AUTO_INCREMENT = 1;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_cat_txt`
# 

CREATE TABLE `efqdirectory_cat_txt` (
  `txtid`   INT(7) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `cid`     INT(7) UNSIGNED     NOT NULL DEFAULT '0',
  `text`    TEXT               ,
  `active`  TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `created` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`txtid`),
  KEY `active` (`active`),
  KEY `cid` (`cid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 12;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_coupon`
# 

CREATE TABLE `efqdirectory_coupon` (
  `couponid`    INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `itemid`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `description` TEXT             ,
  `image`       TEXT             ,
  `publish`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `expire`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `heading`     TEXT             ,
  `lbr`         INT(1)           NOT NULL DEFAULT '0',
  `counter`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `addrid`      INT(10)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`couponid`),
  KEY `addrid` (`addrid`),
  KEY `itemid` (`itemid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 2;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_data`
# 

CREATE TABLE `efqdirectory_data` (
  `dataid`      INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `itemid`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `dtypeid`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `value`       TEXT             ,
  `created`     INT(10)          NOT NULL DEFAULT '0',
  `customtitle` VARCHAR(255)     NOT NULL,
  PRIMARY KEY (`dataid`),
  KEY `dtypeid` (`dtypeid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 142;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_dir`
# 

CREATE TABLE `efqdirectory_dir` (
  `dirid`       INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `postfix`     VARCHAR(20)     NOT NULL DEFAULT '',
  `open`        TINYINT(2)      NOT NULL DEFAULT '0',
  `name`        VARCHAR(100)    NOT NULL DEFAULT '',
  `descr`       LONGTEXT        ,
  `img`         VARCHAR(50)     NOT NULL DEFAULT '',
  `allowreview` TINYINT(2)      NOT NULL DEFAULT '0',
  PRIMARY KEY (`dirid`)
)
  ENGINE = MyISAM
  COMMENT = 'Directories'
  AUTO_INCREMENT = 5;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_dtypes`
# 

CREATE TABLE `efqdirectory_dtypes` (
  `dtypeid`     INT(6) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(100)         NOT NULL DEFAULT '',
  `section`     SMALLINT(3)          NOT NULL DEFAULT '0',
  `fieldtypeid` SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `uid`         INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `defaultyn`   TINYINT(1)           NOT NULL DEFAULT '0',
  `created`     INT(10)              NOT NULL DEFAULT '0',
  `seq`         INT(5)               NOT NULL DEFAULT '0',
  `activeyn`    TINYINT(2)           NOT NULL DEFAULT '0',
  `options`     TEXT                 ,
  `custom`      TINYINT(2)           NOT NULL DEFAULT '0',
  `icon`        VARCHAR(100)         NOT NULL,
  PRIMARY KEY (`dtypeid`),
  KEY `typeid` (`fieldtypeid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 51;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_dtypes_x_cat`
# 

CREATE TABLE `efqdirectory_dtypes_x_cat` (
  `xid`     INT(11) NOT NULL AUTO_INCREMENT,
  `cid`     INT(11) NOT NULL DEFAULT '0',
  `dtypeid` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xid`)
)
  ENGINE = MyISAM
  COMMENT = 'Cross tabel categories linked to dtypes'
  AUTO_INCREMENT = 106;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_fieldtypes`
# 

CREATE TABLE `efqdirectory_fieldtypes` (
  `typeid`    SMALLINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`     VARCHAR(100)         NOT NULL DEFAULT '',
  `fieldtype` VARCHAR(100)         NOT NULL DEFAULT '',
  `descr`     TEXT                 ,
  `ext`       VARCHAR(255)         NOT NULL DEFAULT '',
  `activeyn`  TINYINT(2)           NOT NULL DEFAULT '1',
  PRIMARY KEY (`typeid`)
)
  ENGINE = MyISAM
  COMMENT = 'Data field types'
  AUTO_INCREMENT = 18;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_form_options`
# 

CREATE TABLE `efqdirectory_form_options` (
  `id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `dtypeid`  INT(11)      NOT NULL DEFAULT '0',
  `option`   VARCHAR(255) NOT NULL,
  `activeyn` TINYINT(1)   NOT NULL DEFAULT '0',
  `seq`      TINYINT(4)   NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `dtypeid` (`dtypeid`)
)
  ENGINE = MyISAM
  COMMENT = 'Form options'
  AUTO_INCREMENT = 1;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_item_img`
# 

CREATE TABLE `efqdirectory_item_img` (
  `id`      INT(11)          NOT NULL AUTO_INCREMENT,
  `dirid`   INT(6) UNSIGNED  NOT NULL DEFAULT '0',
  `itemid`  INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `dtypeid` INT(6) UNSIGNED  NOT NULL DEFAULT '0',
  `created` VARCHAR(10)      NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_item_text`
# 

CREATE TABLE `efqdirectory_item_text` (
  `itemid`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `description` TEXT             ,
  KEY `itemid` (`itemid`)
)
  ENGINE = MyISAM;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_item_x_cat`
# 

CREATE TABLE `efqdirectory_item_x_cat` (
  `xid`     INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `cid`     INT(7) UNSIGNED     NOT NULL DEFAULT '0',
  `itemid`  INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `active`  TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `created` INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`xid`),
  KEY `cid` (`cid`),
  KEY `itemid` (`itemid`),
  KEY `active` (`active`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 43;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_item_x_loc`
# 

CREATE TABLE `efqdirectory_item_x_loc` (
  `locid`  INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `itemid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `seq`    TINYINT(2)       NOT NULL DEFAULT '0',
  KEY `locdestid` (`locid`),
  KEY `loctypeid` (`itemid`),
  KEY `loctypeid_2` (`itemid`)
)
  ENGINE = MyISAM
  COMMENT = 'Attach item to location';

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_items`
# 

CREATE TABLE `efqdirectory_items` (
  `itemid`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `logourl` VARCHAR(60)      NOT NULL DEFAULT '',
  `uid`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `status`  TINYINT(2)       NOT NULL DEFAULT '0',
  `created` INT(10)          NOT NULL DEFAULT '0',
  `title`   VARCHAR(100)     NOT NULL DEFAULT '',
  `hits`    INT(11)          NOT NULL DEFAULT '0',
  `rating`  DOUBLE(5, 4)     NOT NULL DEFAULT '0.0000',
  `votes`   INT(11)          NOT NULL DEFAULT '0',
  `typeid`  INT(8)           NOT NULL DEFAULT '0',
  `dirid`   INT(5) UNSIGNED  NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemid`),
  KEY `status` (`status`),
  KEY `title` (`title`(50)),
  KEY `typeid` (`typeid`),
  KEY `dirid` (`dirid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 38;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_itemtypes`
# 

CREATE TABLE `efqdirectory_itemtypes` (
  `typeid`    INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typename`  VARCHAR(50)     NOT NULL DEFAULT '',
  `typelevel` TINYINT(2)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`typeid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 5;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_loc`
# 

CREATE TABLE `efqdirectory_loc` (
  `locid`          INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(100)    NOT NULL DEFAULT '',
  `amazonname`     VARCHAR(100)             DEFAULT NULL,
  `fullname`       VARCHAR(150)             DEFAULT NULL,
  `searchname`     VARCHAR(150)    NOT NULL DEFAULT '',
  `levelid`        TINYINT(2)      NOT NULL DEFAULT '0',
  `plocid`         INT(7) UNSIGNED NOT NULL DEFAULT '0',
  `loctypeid`      INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `icaoid`         INT(6) UNSIGNED NOT NULL DEFAULT '0',
  `childloctypeid` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `featatrid`      INT(8) UNSIGNED          DEFAULT '0',
  `status`         TINYINT(1)      NOT NULL DEFAULT '0',
  `popular`        TINYINT(1)      NOT NULL DEFAULT '0',
  PRIMARY KEY (`locid`),
  KEY `name` (`name`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_loc_types`
# 

CREATE TABLE `efqdirectory_loc_types` (
  `loctypeid` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typename`  VARCHAR(50)     NOT NULL DEFAULT '0',
  `typelevel` TINYINT(2)      NOT NULL DEFAULT '0',
  PRIMARY KEY (`loctypeid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_loc_x_loctype`
# 

CREATE TABLE `efqdirectory_loc_x_loctype` (
  `locid`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `loctypeid` INT(3) UNSIGNED  NOT NULL DEFAULT '0',
  `seq`       TINYINT(2)       NOT NULL DEFAULT '0',
  KEY `locdestid` (`locid`),
  KEY `loctypeid` (`loctypeid`)
)
  ENGINE = MyISAM
  COMMENT = 'Attach levels to location';

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_searchresults`
# 

CREATE TABLE `efqdirectory_searchresults` (
  `searchid`  INT(11)     NOT NULL AUTO_INCREMENT,
  `searchnum` VARCHAR(50) NOT NULL,
  `created`   VARCHAR(10) NOT NULL,
  `page`      SMALLINT(4) NOT NULL,
  `items`     TEXT        ,
  `dirid`     INT(5)      NOT NULL,
  `catid`     INT(7)      NOT NULL,
  PRIMARY KEY (`searchid`),
  KEY `searchnum` (`searchnum`, `dirid`, `catid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 33;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_subscr_notify`
# 

CREATE TABLE `efqdirectory_subscr_notify` (
  `id`       INT(11)     NOT NULL AUTO_INCREMENT,
  `method`   TINYINT(2)  NOT NULL DEFAULT '0',
  `datetime` VARCHAR(10) NOT NULL,
  `msg`      INT(5)      NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_subscr_offers`
# 

CREATE TABLE `efqdirectory_subscr_offers` (
  `offerid`  INT(5)       NOT NULL AUTO_INCREMENT,
  `typeid`   INT(5)       NOT NULL DEFAULT '0',
  `title`    VARCHAR(255) NOT NULL,
  `duration` INT(5)       NOT NULL DEFAULT '0',
  `count`    INT(5)       NOT NULL DEFAULT '0',
  `price`    DOUBLE(5, 2) NOT NULL DEFAULT '0.00',
  `activeyn` TINYINT(2)   NOT NULL DEFAULT '0',
  `currency` VARCHAR(10)  NOT NULL,
  `descr`    LONGTEXT     ,
  PRIMARY KEY (`offerid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 3;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_subscr_orders`
# 

CREATE TABLE `efqdirectory_subscr_orders` (
  `orderid`   INT(10)     NOT NULL AUTO_INCREMENT,
  `uid`       INT(10)     NOT NULL,
  `offerid`   INT(10)     NOT NULL,
  `typeid`    INT(4)      NOT NULL,
  `startdate` VARCHAR(10) NOT NULL,
  `enddate`   VARCHAR(10) NOT NULL,
  `billto`    VARCHAR(10) NOT NULL,
  `status`    TINYINT(2)  NOT NULL,
  `itemid`    INT(10)     NOT NULL,
  `autorenew` TINYINT(2)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 33;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_subscr_payments`
# 

CREATE TABLE `efqdirectory_subscr_payments` (
  `id`                  INT(12)       NOT NULL AUTO_INCREMENT,
  `txn_id`              VARCHAR(150)  NOT NULL,
  `txn_type`            VARCHAR(50)   NOT NULL,
  `orderid`             INT(10)       NOT NULL,
  `payer_business_name` VARCHAR(200)  NOT NULL,
  `address_name`        VARCHAR(200)  NOT NULL,
  `address_street`      VARCHAR(200)  NOT NULL,
  `address_city`        VARCHAR(200)  NOT NULL,
  `address_state`       VARCHAR(200)  NOT NULL,
  `address_zip`         VARCHAR(200)  NOT NULL,
  `address_country`     VARCHAR(200)  NOT NULL,
  `address_status`      VARCHAR(200)  NOT NULL,
  `payer_email`         VARCHAR(200)  NOT NULL,
  `payer_id`            VARCHAR(200)  NOT NULL,
  `payer_status`        VARCHAR(200)  NOT NULL,
  `mc_currency`         VARCHAR(10)   NOT NULL,
  `mc_gross`            DOUBLE(10, 2) NOT NULL,
  `mc_fee`              DOUBLE(9, 2)  NOT NULL,
  `created`             VARCHAR(10)   NOT NULL,
  `payment_date`        VARCHAR(50)   NOT NULL,
  `ref`                 VARCHAR(255)  NOT NULL,
  `payment_status`      VARCHAR(50)   NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 25;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_subscr_scheduler`
# 

CREATE TABLE `efqdirectory_subscr_scheduler` (
  `id`        INT(11)    NOT NULL AUTO_INCREMENT,
  `startdate` INT(10)    NOT NULL,
  `itemid`    INT(11)    NOT NULL,
  `newtypeid` INT(6)     NOT NULL,
  `status`    TINYINT(2) NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 3;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_tpl`
# 

CREATE TABLE `efqdirectory_tpl` (
  `tplid`    INT(5)       NOT NULL AUTO_INCREMENT,
  `title`    VARCHAR(255) NOT NULL DEFAULT '',
  `name`     VARCHAR(50)  NOT NULL DEFAULT '',
  `activeyn` TINYINT(2)   NOT NULL DEFAULT '0',
  PRIMARY KEY (`tplid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

# --------------------------------------------------------

# 
# Table structure for table `efqdirectory_votedata`
# 

CREATE TABLE `efqdirectory_votedata` (
  `ratingid`        INT(11)             NOT NULL AUTO_INCREMENT,
  `itemid`          INT(11)             NOT NULL DEFAULT '0',
  `ratinguser`      INT(11) UNSIGNED    NOT NULL DEFAULT '0',
  `rating`          TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `ratinghostname`  VARCHAR(60)         NOT NULL DEFAULT '',
  `ratingtimestamp` INT(10)             NOT NULL DEFAULT '0',
  PRIMARY KEY (`ratingid`),
  KEY `ratinguser` (`ratinguser`),
  KEY `ratinghostname` (`ratinghostname`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

#
# Dumping data for table `efqdirectory_itemtypes`
# 

INSERT INTO `efqdirectory_itemtypes` (`typeid`, `typename`, `typelevel`) VALUES (1, 'Bronze', 1);
INSERT INTO `efqdirectory_itemtypes` (`typeid`, `typename`, `typelevel`) VALUES (2, 'Silver', 2);
INSERT INTO `efqdirectory_itemtypes` (`typeid`, `typename`, `typelevel`) VALUES (3, 'Gold', 3);

# 
# Dumping data for table `efqdirectory_fieldtypes`
# 

INSERT INTO `efqdirectory_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (9, 'Textbox default (50 - 100)', 'textbox', 'Default text box', 'size=50|maxsize=100', 1);
INSERT INTO `efqdirectory_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (10, 'Yes/No', 'yesno', 'Yes or no', '', 1);
INSERT INTO `efqdirectory_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (11, 'Text Area (10 - 50)', 'textarea', 'Normal text area', 'rows=10|cols=50', 1);
INSERT INTO `efqdirectory_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (12, 'Select box', 'select', 'Select a rating', '', 1);
INSERT INTO `efqdirectory_fieldtypes` (`typeid`, `title`, `fieldtype`, `descr`, `ext`, `activeyn`) VALUES (13, 'DHTML text area', 'dhtml', 'Default DHTML area', 'rows=10|cols=50', 1);
