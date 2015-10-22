-- @tempversion
-- @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
-- @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
-- @package			[%%com_architectcomp%%]
-- @subpackage		[%%com_architectcomp%%].admin
-- @copyright		[%%COMPONENTCOPYRIGHT%%]
-- @license			GNU General Public License version 3 or later - See http://www.gnu.org/copyleft/gpl.html 
--
-- The following Component Architect header section must remain in any distribution of this file
--
-- @version			$Id:install.architectcomp_mysql.utf8.sql 19 2012-01-12 16:33:49Z BrianWade $
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
[%%FOREACH COMPONENT_OBJECT%%]
--
-- Table structure for table `#__[%%architectcomp%%]_[%%compobjectplural%%]`
--

DROP TABLE IF EXISTS `#__[%%architectcomp%%]_[%%compobjectplural%%]`;
CREATE TABLE IF NOT EXISTS `#__[%%architectcomp%%]_[%%compobjectplural%%]` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
[%%IF INCLUDE_NAME%%]  
  `name` VARCHAR(255) NOT NULL DEFAULT '',
    [%%IF INCLUDE_ALIAS%%]  
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
    [%%ENDIF INCLUDE_ALIAS%%]
[%%ENDIF INCLUDE_NAME%%]   
[%%IF INCLUDE_DESCRIPTION%%]
  `description` MEDIUMTEXT NOT NULL,
[%%ENDIF INCLUDE_DESCRIPTION%%]  
[%%IF INCLUDE_INTRO%%]  
  `intro` MEDIUMTEXT NOT NULL,
[%%ENDIF INCLUDE_INTRO%%]  
[%%FOREACH OBJECT_FIELD%%]
  `[%%FIELD_CODE_NAME%%]` [%%FIELD_DBTYPEANDSIZE%%] [%%FIELD_DBDEFAULT%%],
[%%ENDFOR OBJECT_FIELD%%]
[%%FOREACH REGISTRY_FIELD%%]
  `[%%FIELD_CODE_NAME%%]` [%%FIELD_DBTYPEANDSIZE%%] [%%FIELD_DBDEFAULT%%],
[%%ENDFOR REGISTRY_FIELD%%]
[%%IF INCLUDE_IMAGE%%]
  `image_url` VARCHAR(200) NOT NULL DEFAULT '',
  `image_alt_text` VARCHAR(255) NOT NULL DEFAULT '', 
[%%ENDIF INCLUDE_IMAGE%%]
[%%IF INCLUDE_URLS%%]
  `urls` TEXT NOT NULL DEFAULT '',
[%%ENDIF INCLUDE_URLS%%]  
[%%IF GENERATE_CATEGORIES%%]        
  `catid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
[%%ENDIF GENERATE_CATEGORIES%%]  
[%%IF INCLUDE_STATUS%%]
  `state` TINYINT(1) NOT NULL DEFAULT '0',
[%%ENDIF INCLUDE_STATUS%%]
[%%IF INCLUDE_PUBLISHED_DATES%%]  
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',  
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', 
[%%ENDIF INCLUDE_PUBLISHED_DATES%%] 
[%%IF INCLUDE_CREATED%%]  
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
[%%ENDIF INCLUDE_CREATED%%] 
[%%IF INCLUDE_MODIFIED%%] 
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
[%%ENDIF INCLUDE_MODIFIED%%]   
[%%IF INCLUDE_CHECKOUT%%]  
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
[%%ENDIF INCLUDE_CHECKOUT%%]
[%%IF INCLUDE_ORDERING%%]  
  `ordering` INT(11) NOT NULL DEFAULT '0',
[%%ENDIF INCLUDE_ORDERING%%]  
[%%IF INCLUDE_PARAMS_RECORD%%]
  `params` VARCHAR(5120) NOT NULL,
[%%ENDIF INCLUDE_PARAMS_RECORD%%]  
[%%IF INCLUDE_HITS%%]
  `hits` INT(10) UNSIGNED NOT NULL DEFAULT '0',
[%%ENDIF INCLUDE_HITS%%]  
[%%IF INCLUDE_FEATURED%%]
  `featured` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
[%%ENDIF INCLUDE_FEATURED%%] 
[%%IF INCLUDE_LANGUAGE%%]
  `language` CHAR(7) NOT NULL DEFAULT '*',
[%%ENDIF INCLUDE_LANGUAGE%%] 
[%%IF INCLUDE_ASSETACL%%]  
    [%%IF INCLUDE_ASSETACL_RECORD%%]  
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
    [%%ENDIF INCLUDE_ASSETACL_RECORD%%]
[%%ENDIF INCLUDE_ASSETACL%%]  
[%%IF INCLUDE_ACCESS%%] 
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '0',
[%%ENDIF INCLUDE_ACCESS%%] 
[%%IF INCLUDE_METADATA%%]  
  `metakey` TEXT NOT NULL,
  `metadesc` TEXT NOT NULL,
  `robots` VARCHAR(50) NOT NULL DEFAULT '',
  `author` VARCHAR(20) NOT NULL DEFAULT '',
  `xreference` VARCHAR(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
[%%ENDIF INCLUDE_METADATA%%] 
[%%IF INCLUDE_ACCESS%%]   
  KEY `idx_access` (`access`),
[%%ENDIF INCLUDE_ACCESS%%] 
[%%IF INCLUDE_CHECKOUT%%]  
  KEY `idx_checkout` (`checked_out`),
[%%ENDIF INCLUDE_CHECKOUT%%]
[%%IF INCLUDE_STATUS%%]   
  KEY `idx_state` (`state`),
[%%ENDIF INCLUDE_STATUS%%]  
[%%IF INCLUDE_CREATED%%]  
  KEY `idx_createdby` (`created_by`),
[%%ENDIF INCLUDE_CREATED%%]  
[%%IF GENERATE_CATEGORIES%%] 
  KEY `idx_catid` (`catid`),
[%%IF INCLUDE_FEATURED%%]       
  KEY `idx_featured_catid` (`featured`,`catid`),
[%%ENDIF INCLUDE_FEATURED%%]     
[%%ENDIF GENERATE_CATEGORIES%%]
[%%FOREACH FILTER_FIELD%%]
  KEY `idx_[%%FIELD_CODE_NAME%%]` (`[%%FIELD_CODE_NAME%%]`),
[%%ENDFOR FILTER_FIELD%%]
[%%IF INCLUDE_ORDERING%%]  
  KEY `idx_ordering` (`ordering`),
[%%ENDIF INCLUDE_ORDERING%%]
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
[%%ENDFOR COMPONENT_OBJECT%%]

[%%IF GENERATE_PLUGINS_VOTE%%]  
--
-- Table structure for table `#__[%%architectcomp%%]_rating`
--
DROP TABLE IF EXISTS `#__[%%architectcomp%%]_rating`;
CREATE TABLE IF NOT EXISTS `#__[%%architectcomp%%]_rating` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_type` VARCHAR(50) NOT NULL DEFAULT '',
  `content_id` INT(11) NOT NULL DEFAULT '0',
  `rating_sum` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `rating_count` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastip` VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `content_type_id` (`content_type`,`content_id`)
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
[%%ENDIF GENERATE_PLUGINS_VOTE%%]  
