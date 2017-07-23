# phpMyAdmin SQL Dump
# version 2.6.4-pl3
# http://www.phpmyadmin.net
# 
# Upgrade DB structure for EFQ directory
# Version 1.0.2 to 1.1.0

ALTER TABLE `efqdiralpha1_fieldtypes`
  ADD `dirid` INT(5) DEFAULT '0' NOT NULL;
ALTER TABLE `efqdiralpha1_subscr_offers`
  ADD `dirid` INT(5) DEFAULT '0' NOT NULL
  AFTER `typeid`;
