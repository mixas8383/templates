-- @version 		$Id:$
-- @name			App (Release 1.0.0)
-- @author			 ()
-- @package			com_app
-- @subpackage		com_app.admin
-- @copyright		
-- @license			GNU General Public License version 3 or later - See http://www.gnu.org/copyleft/gpl.html 
--
-- The following Component Architect header section must remain in any distribution of this file
--
-- @CAversion		Id:install.architectcomp_mysql.utf8.sql 19 2012-01-12 16:33:49Z BrianWade $
-- @CAauthor		Component Architect (www.componentarchitect.com)
-- @CApackage		architectcomp
-- @CAsubpackage	architectcomp.admin
-- @CAtemplate		joomla_2_5_standard (Release 1.0.4)
-- @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
-- @CAlicense		GNU General Public License version 3 or later - See http://www.gnu.org/copyleft/gpl.html
--
-- This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY, without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
-- --------------------------------------------------------
--
-- Table structure for table `#__app_items`
--

DROP TABLE IF EXISTS `#__app_items`;
CREATE TABLE IF NOT EXISTS `#__app_items` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `intro` MEDIUMTEXT NOT NULL,
  `image_url` VARCHAR(200) NOT NULL DEFAULT '',
  `image_alt_text` VARCHAR(255) NOT NULL DEFAULT '', 
  `urls` TEXT NOT NULL DEFAULT '',
  `catid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `state` TINYINT(1) NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',  
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  `params` VARCHAR(5120) NOT NULL,
  `hits` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `featured` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `language` CHAR(7) NOT NULL DEFAULT '*',
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `metakey` TEXT NOT NULL,
  `metadesc` TEXT NOT NULL,
  `robots` VARCHAR(50) NOT NULL DEFAULT '',
  `author` VARCHAR(20) NOT NULL DEFAULT '',
  `xreference` VARCHAR(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_catid` (`catid`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_ordering` (`ordering`),
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__app_rating`
--
DROP TABLE IF EXISTS `#__app_rating`;
CREATE TABLE IF NOT EXISTS `#__app_rating` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_type` VARCHAR(50) NOT NULL DEFAULT '',
  `content_id` INT(11) NOT NULL DEFAULT '0',
  `rating_sum` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `rating_count` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastip` VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `content_type_id` (`content_type`,`content_id`)
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
