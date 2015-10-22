-- @version 		$Id: install.componentarchitect_mysql.utf8.sql 411 2014-10-19 18:39:07Z BrianWade $
-- @name			Component Architect (Release 1.1.3)
-- @author			Component Architect (http://www.componentarchitect.com)
-- @package			com_componentarchitect
-- @subpackage		com_componentarchitect.admin
-- @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
-- @license			GNU General Public License version 3 or later - See http://www.gnu.org/copyleft/gpl.html 
--
-- The following Component Architect header section must remain in any distribution of this file
--
-- @CAversion		Id:install.architectcomp_mysql.utf8.sql 19 2012-01-12 16:33:49Z BrianWade $
-- @CAauthor		Component Architect (www.componentarchitect.com)
-- @CApackage		architectcomp
-- @CAsubpackage	architectcomp.admin
-- @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
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
-- Table structure for table `#__componentarchitect_components`
--

DROP TABLE IF EXISTS `#__componentarchitect_components`;
CREATE TABLE IF NOT EXISTS `#__componentarchitect_components` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `author` VARCHAR(100) NOT NULL DEFAULT '',
  `start_version` VARCHAR(100) NOT NULL DEFAULT '',
  `web_address` VARCHAR(100) NOT NULL DEFAULT '',
  `email` VARCHAR(100) NOT NULL DEFAULT '',
  `code_name` VARCHAR(50) NOT NULL DEFAULT '',
  `copyright` VARCHAR(255) NOT NULL DEFAULT '',
  `default_object_id` INT(10) NOT NULL DEFAULT '0',
  `icon_16px` VARCHAR(255) NOT NULL DEFAULT '',
  `icon_48px` VARCHAR(255) NOT NULL DEFAULT '',
  `categories_icon_16px` VARCHAR(255) NOT NULL DEFAULT '',
  `categories_icon_48px` VARCHAR(255) NOT NULL DEFAULT '',
  `joomla_parts` VARCHAR(1024) NOT NULL DEFAULT '',
  `joomla_features` VARCHAR(1024) NOT NULL DEFAULT '',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT '',  
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_ordering` (`ordering`),
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- Table structure for table `#__componentarchitect_componentobjects`
--

DROP TABLE IF EXISTS `#__componentarchitect_componentobjects`;
CREATE TABLE IF NOT EXISTS `#__componentarchitect_componentobjects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `component_id` INT(10) NOT NULL DEFAULT '0',
  `readonly` TINYINT(1) NOT NULL DEFAULT '0',
  `plural_name` VARCHAR(50) NOT NULL DEFAULT '',
  `code_name` VARCHAR(50) NOT NULL DEFAULT '',
  `plural_code_name` VARCHAR(50) NOT NULL DEFAULT '',
  `short_name` VARCHAR(50) NOT NULL DEFAULT '',
  `short_plural_name` VARCHAR(50) NOT NULL DEFAULT '',
  `default_fieldset_id` INT(10) NOT NULL DEFAULT '0',
  `icon_16px` VARCHAR(255) NOT NULL DEFAULT '',
  `icon_48px` VARCHAR(255) NOT NULL DEFAULT '',
  `joomla_parts` VARCHAR(1024) NOT NULL DEFAULT '',
  `joomla_features` VARCHAR(1024) NOT NULL DEFAULT '',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT '',  
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_component_id` (`component_id`),
  KEY `idx_ordering` (`ordering`),
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- Table structure for table `#__componentarchitect_fieldsets`
--

DROP TABLE IF EXISTS `#__componentarchitect_fieldsets`;
CREATE TABLE IF NOT EXISTS `#__componentarchitect_fieldsets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `code_name` VARCHAR(50) NOT NULL DEFAULT '',
  `component_id` INT(10) NOT NULL DEFAULT '0',
  `component_object_id` INT(10) NOT NULL DEFAULT '0',
  `predefined_fieldset` TINYINT(1) NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT '',  
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_component_id` (`component_id`),
  KEY `idx_component_object_id` (`component_object_id`),
  KEY `idx_predefined_fieldset` (`predefined_fieldset`),
  KEY `idx_ordering` (`ordering`),
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- Table structure for table `#__componentarchitect_fields`
--

DROP TABLE IF EXISTS `#__componentarchitect_fields`;
CREATE TABLE IF NOT EXISTS `#__componentarchitect_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `code_name` VARCHAR(50) NOT NULL DEFAULT '',
  `component_id` INT(10) NOT NULL DEFAULT '0',
  `component_object_id` INT(10) NOT NULL DEFAULT '0',
  `fieldset_id` INT(10) NOT NULL DEFAULT '0',
  `predefined_field` TINYINT(1) NOT NULL DEFAULT '0',
  `fieldtype_id` INT(10) NOT NULL DEFAULT '0',
  `required` TINYINT(1) NOT NULL DEFAULT '0',
  `filter` TINYINT(1) NOT NULL DEFAULT '0',
  `order` TINYINT(1) NOT NULL DEFAULT '0',
  `search` TINYINT(1) NOT NULL DEFAULT '0',
  `readonly` TINYINT(1) NOT NULL DEFAULT '0',
  `disabled` TINYINT(1) NOT NULL DEFAULT '0',
  `hidden` TINYINT(1) NOT NULL DEFAULT '0',
  `validate` TINYINT(1) NOT NULL DEFAULT '0',
  `validation_type` VARCHAR(15) NOT NULL DEFAULT '',
  `allowed_input` VARCHAR(100) NOT NULL DEFAULT '',
  `custom_error_message` VARCHAR(255) NOT NULL DEFAULT '',
  `registry_field_id` INT(10) NOT NULL DEFAULT '0',
  `php_variable_type` VARCHAR(10) NOT NULL DEFAULT 'string',
  `default` VARCHAR(255) NOT NULL DEFAULT '',
  `class` VARCHAR(50) NOT NULL DEFAULT '',
  `size` VARCHAR(5) NOT NULL DEFAULT '',
  `maxlength` VARCHAR(5) NOT NULL DEFAULT '',
  `width` VARCHAR(5) NOT NULL DEFAULT '',
  `height` VARCHAR(5) NOT NULL DEFAULT '',
  `cols` VARCHAR(5) NOT NULL DEFAULT '',
  `rows` VARCHAR(5) NOT NULL DEFAULT '',
  `value_source` VARCHAR(40) NOT NULL DEFAULT '',
  `option_values` MEDIUMTEXT NOT NULL ,
  `multiple` TINYINT(1) NOT NULL DEFAULT '0',
  `format` VARCHAR(25) NOT NULL DEFAULT '',
  `first` VARCHAR(5) NOT NULL DEFAULT '',
  `last` VARCHAR(5) NOT NULL DEFAULT '',
  `step` VARCHAR(5) NOT NULL DEFAULT '',
  `hide_none` TINYINT(1) NOT NULL DEFAULT '0',
  `hide_default` TINYINT(1) NOT NULL DEFAULT '0',
  `buttons` VARCHAR(100) NOT NULL DEFAULT '',
  `hide_buttons` VARCHAR(100) NOT NULL DEFAULT '',
  `foreign_object_id` INT(10) NOT NULL DEFAULT '0',
  `cascade_object` TINYINT(1) NOT NULL DEFAULT '0',
  `field_filter` VARCHAR(15) NOT NULL DEFAULT '',
  `max_file_size` VARCHAR(10) NOT NULL DEFAULT '',
  `exclude_files` VARCHAR(50) NOT NULL DEFAULT '',
  `accept_file_types` VARCHAR(50) NOT NULL DEFAULT '',
  `directory` VARCHAR(255) NOT NULL DEFAULT '',
  `link` VARCHAR(255) NOT NULL DEFAULT '',
  `sql_query` VARCHAR(1024) NOT NULL ,
  `sql_key_field` VARCHAR(50) NOT NULL DEFAULT '',
  `sql_value_field` VARCHAR(50) NOT NULL DEFAULT '',
  `translate` TINYINT(1) NOT NULL DEFAULT '0',
  `client` VARCHAR(15) NOT NULL DEFAULT '',
  `stripext` TINYINT(1) NOT NULL DEFAULT '0',
  `preview` VARCHAR(15) NOT NULL DEFAULT '',
  `autocomplete` VARCHAR(15) NOT NULL DEFAULT '',
  `onclick` VARCHAR(255) NOT NULL ,
  `onchange` VARCHAR(255) NOT NULL ,
  `mysql_datatype` VARCHAR(15) NOT NULL DEFAULT '',
  `mysql_size` VARCHAR(5) NOT NULL DEFAULT '',
  `mysql_default` VARCHAR(50) NOT NULL DEFAULT '',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT '',  
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_component_id` (`component_id`),
  KEY `idx_component_object_id` (`component_object_id`),
  KEY `idx_fieldset_id` (`fieldset_id`),
  KEY `idx_predefined_field` (`predefined_field`),
  KEY `idx_fieldtype_id` (`fieldtype_id`),
  KEY `idx_foreign_object_id` (`foreign_object_id`),
  KEY `idx_ordering` (`ordering`),
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- Table structure for table `#__componentarchitect_fieldtypes`
--

DROP TABLE IF EXISTS `#__componentarchitect_fieldtypes`;
CREATE TABLE IF NOT EXISTS `#__componentarchitect_fieldtypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `code_name` VARCHAR(50) NOT NULL DEFAULT '',
  `class` TINYINT(1) NOT NULL DEFAULT '0',
  `size` TINYINT(1) NOT NULL DEFAULT '0',
  `maxlength` TINYINT(1) NOT NULL DEFAULT '0',
  `width` TINYINT(1) NOT NULL DEFAULT '0',
  `height` TINYINT(1) NOT NULL DEFAULT '0',
  `cols` TINYINT(1) NOT NULL DEFAULT '0',
  `rows` TINYINT(1) NOT NULL DEFAULT '0',
  `value_source` TINYINT(1) NOT NULL DEFAULT '0',
  `option_values` TINYINT(1) NOT NULL DEFAULT '0',
  `multiple` TINYINT(1) NOT NULL DEFAULT '0',
  `format` TINYINT(1) NOT NULL DEFAULT '0',
  `first` TINYINT(1) NOT NULL DEFAULT '0',
  `last` TINYINT(1) NOT NULL DEFAULT '0',
  `step` TINYINT(1) NOT NULL DEFAULT '0',
  `hide_none` TINYINT(1) NOT NULL DEFAULT '0',
  `hide_default` TINYINT(1) NOT NULL DEFAULT '0',
  `buttons` TINYINT(1) NOT NULL DEFAULT '0',
  `hide_buttons` TINYINT(1) NOT NULL DEFAULT '0',
  `foreign_object_id` TINYINT(1) NOT NULL DEFAULT '0',
  `cascade_object` TINYINT(1) NOT NULL DEFAULT '0',
  `field_filter` TINYINT(1) NOT NULL DEFAULT '0',
  `max_file_size` TINYINT(1) NOT NULL DEFAULT '0',
  `exclude_files` TINYINT(1) NOT NULL DEFAULT '0',
  `accept_file_types` TINYINT(1) NOT NULL DEFAULT '0',
  `directory` TINYINT(1) NOT NULL DEFAULT '0',
  `link` TINYINT(1) NOT NULL DEFAULT '0',
  `sql_query` TINYINT(1) NOT NULL DEFAULT '0',
  `sql_key_field` TINYINT(1) NOT NULL DEFAULT '0',
  `sql_value_field` TINYINT(1) NOT NULL DEFAULT '0',
  `translate` TINYINT(1) NOT NULL DEFAULT '0',
  `client` TINYINT(1) NOT NULL DEFAULT '0',
  `stripext` TINYINT(1) NOT NULL DEFAULT '0',
  `preview` TINYINT(1) NOT NULL DEFAULT '0',
  `autocomplete` TINYINT(1) NOT NULL DEFAULT '0',
  `onclick` TINYINT(1) NOT NULL DEFAULT '0',
  `onchange` TINYINT(1) NOT NULL DEFAULT '0',
  `default_default` VARCHAR(50) NOT NULL DEFAULT '',
  `class_default` VARCHAR(50) NOT NULL DEFAULT '',
  `maxlength_default` VARCHAR(5) NOT NULL DEFAULT '',
  `size_default` VARCHAR(5) NOT NULL DEFAULT '',
  `allowed_input_default` VARCHAR(100) NOT NULL DEFAULT '',
  `format_default` VARCHAR(25) NOT NULL DEFAULT '',
  `php_variable_type` VARCHAR(10) NOT NULL DEFAULT 'string',
  `cols_default` VARCHAR(5) NOT NULL DEFAULT '',
  `rows_default` VARCHAR(5) NOT NULL DEFAULT '',
  `width_default` VARCHAR(5) NOT NULL DEFAULT '',
  `height_default` VARCHAR(5) NOT NULL DEFAULT '',
  `buttons_default` VARCHAR(50) NOT NULL DEFAULT '',
  `hide_buttons_default` VARCHAR(50) NOT NULL DEFAULT '',
  `validation_type_default` VARCHAR(15) NOT NULL DEFAULT '',
  `field_filter_default` VARCHAR(15) NOT NULL DEFAULT '',
  `max_file_size_default` VARCHAR(10) NOT NULL DEFAULT '',
  `exclude_files_default` VARCHAR(100) NOT NULL DEFAULT '',
  `directory_default` VARCHAR(255) NOT NULL DEFAULT '',
  `accept_file_types_default` VARCHAR(100) NOT NULL DEFAULT '',
  `mysql_datatype_default` VARCHAR(15) NOT NULL DEFAULT '',
  `mysql_size_default` VARCHAR(5) NOT NULL DEFAULT '',
  `mysql_default_default` VARCHAR(50) NOT NULL DEFAULT '',
  `catid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT '',  
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_catid` (`catid`),
  KEY `idx_ordering` (`ordering`),
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- Table structure for table `#__componentarchitect_codetemplates`
--

DROP TABLE IF EXISTS `#__componentarchitect_codetemplates`;
CREATE TABLE IF NOT EXISTS `#__componentarchitect_codetemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `version` VARCHAR(15) NOT NULL DEFAULT '',
  `source_path` VARCHAR(255) NOT NULL DEFAULT '',
  `predefined_code_template` TINYINT(1) NOT NULL DEFAULT '0',
  `generate_predefined_fields` TINYINT(1) NOT NULL DEFAULT '0',
  `multiple_category_objects` TINYINT(1) NOT NULL DEFAULT '0',
  `platform` VARCHAR(25) NOT NULL DEFAULT '',
  `platform_version` VARCHAR(50) NOT NULL DEFAULT '',
  `coding_language` VARCHAR(100) NOT NULL DEFAULT '',
  `template_component_name` VARCHAR(50) NOT NULL DEFAULT '',
  `template_object_name` VARCHAR(50) NOT NULL DEFAULT '',
  `template_markup_prefix` VARCHAR(50) NOT NULL DEFAULT '',
  `template_markup_suffix` VARCHAR(50) NOT NULL DEFAULT '',
  `catid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` VARCHAR(255) NOT NULL DEFAULT '',  
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` INT(11) NOT NULL DEFAULT '0',
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_catid` (`catid`),
  KEY `idx_predefined_code_template` (`predefined_code_template`),
  KEY `idx_ordering` (`ordering`),
  PRIMARY KEY (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- [%%START_CUSTOM_CODE%%]
--
-- Table structure for table `#__componentarchitect_sessiondata`
--
DROP TABLE IF EXISTS `#__componentarchitect_sessiondata`;
CREATE TABLE IF NOT EXISTS `#__componentarchitect_sessiondata` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `context` VARCHAR(50) NOT NULL DEFAULT '',
  `key` VARCHAR(50) NOT NULL DEFAULT '',
  `data` MEDIUMTEXT,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expires` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `context_key` (`context`,`key`)
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- Insert basic values into `#__componentarchitect_fieldtypes` table
--
INSERT INTO `#__componentarchitect_fieldtypes` (`name`, `description`, `code_name`, `class`, `size`, `width`, `maxlength`, `height`, `cols`, `rows`, `value_source`, `option_values`, `multiple`, `format`, `first`, `last`, `step`, `hide_none`, `hide_default`, `buttons`, `hide_buttons`, `foreign_object_id`, `cascade_object`, `field_filter`, `max_file_size`, `exclude_files`, `accept_file_types`, `directory`, `link`, `sql_query`, `sql_key_field`, `sql_value_field`, `translate`, `client`, `stripext`, `preview`, `autocomplete`, `onclick`, `onchange`, `default_default`, `class_default`, `maxlength_default`, `size_default`, `allowed_input_default`, `format_default`, `php_variable_type`, `cols_default`, `rows_default`, `width_default`, `height_default`, `buttons_default`, `hide_buttons_default`, `validation_type_default`, `field_filter_default`, `max_file_size_default`, `exclude_files_default`, `directory_default`, `accept_file_types_default`, `mysql_datatype_default`, `mysql_size_default`, `mysql_default_default`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `ordering`) VALUES
('Text', '<p>This is the field type for a standard Joomla! Text field.</p>', 'text', 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '255', '50', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Hidden', '<p>This is the field type for a standard Joomla! Hidden field. The attribute ''hidden'' and class of ''hidden'' is automatically used by Joomla! for a field of type ''hidden''</p>', 'hidden', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '', '', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Text Area', '<p>This is the field type for a standard Text Area field.</p>', 'textarea', 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '', '', '', '', 'string', '50', '5', '', '', '', '', '', 'safehtml', '', '', '', '', 'MEDIUMTEXT', '', '', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Editor', '<p>This is the field type for a standard Joomla! Editor field. The editor field will be whatever editor is specified for the user.</p>', 'editor', 0, 0, 1, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inputbox', '', '', '', '', 'string', '50', '5', '100%', '250', '', '', '', 'safe_editor', '', '', '', '', 'TEXT', '', '', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Calendar', '<p>This is the field type for a standard Joomla! Calendar field.</p>', 'calendar', 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '25', '22', '(\\d{4})-(\\d{2})-(\\d{2}) (\\d{2}):(\\d{2}):(\\d{2})', 'Y-m-d H:i:s', 'string', '', '', '', '', '', '', '', 'user_utc', '', '', '', '', 'DATETIME', '', '''CURRENT_TIMESTAMP''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Color Picker', '<p>This is the field type for a standard Joomla! Color picker field.</p>', 'color', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '', '15', '', '', 'string', '', '', '', '', '', '', 'color', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('List', '<p>This is the field type for a standard Joomla! List field.</p>', 'list', 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, '', 'inputbox', '', '1', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Radio', '<p>This is the field type for a standard Radio field.</p>', 'radio', 1, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, '', 'btn-group', '', '', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('File', '<p>This is the field type for a standard Joomla! File field.</p><p>Code to upload a file specified in a File field type is NOT included in any code templates provided with the free version of Ccomponent Architect. Any uploaded function must be manually coded in the generated compononent.</p>', 'file', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '', '1', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('File List', '<p>This is the field type for a standard Joomla! File List field.</p>', 'filelist', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, '', 'inputbox', '', '5', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Folder List', '<p>This is the field type for a standard Joomla! Folder List field.</p>', 'folderlist', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inputbox', '', '5', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Media', '<p>This is the field type for a standard Media field.</p>', 'media', 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, '', 'inputbox', '', '', '', '', 'string', '', '', '100', '50', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Modal', '<p>This is the field type for a standard Modal field. Requires the generation of a modal dialog for a field. The dialog must be pre-coded.</p>', 'modal', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', 'modal', '', '', '', '', 'int', '', '', '', '', '', '', '', '', '', '', '', '', 'INT', '10', '''0''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Image List', '<p>This is the field type for a standard Joomla! Image List field.</p>', 'imagelist', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, '', 'inputbox', '', '5', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Category', '<p>This is the field type for a standard Joomla! Category field.</p>', 'category', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', 'category', '', '1', '', '', 'int', '', '', '', '', '', '', '', '', '', '', '', '', 'INT', '10', '''0''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Checkbox', '<p>This is the field type for a standard Joomla! Checkbox field.</p>', 'checkbox', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, '', 'inputbox', '', '', '', '', 'int', '', '', '', '', '', '', '', '', '', '', '', '', 'TINYINT', '1', '''0''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Checkboxes', '<p>This is the field type for a standard Joomla! Checkboxes field.</p>', 'checkboxes', 1, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, '', 'inputbox', '', '', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '50', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('SQL', '<p>This is the field type for a standard SQL Query field.</p>', 'sql', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, '', 'inputbox', '', '1', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('User', '<p>This is the field type for a standard User field.</p>', 'user', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, '0', 'inputbox', '', '1', '', '', 'int', '', '', '', '', '', '', '', '', '', '', '', '', 'INT', '10', '''0''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Registry', '<p>This is JSON Registry field type in which other fields are contains</p>', 'registry', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '1024', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Grouped List', '<p>This is the field type for a standard Joomla! Grouped List field.</p>', 'groupedlist', 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, '', 'inputbox', '', '5', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Integer', '<p>This is the field type for a standard Joomla! Integer field.</p>', 'integer', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, '0', 'inputbox', '', '1', '', '', 'int', '', '', '', '', '', '', 'numeric', 'int', '', '', '', '', 'INT', '10', '''0''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('User Group', '<p>This is the field type for a standard Joomla! User Group field.</p>', 'usergroup', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, '0', 'inputbox', '', '1', '', '', 'int', '', '', '', '', '', '', '', '', '', '', '', '', 'INT', '10', '''0''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Web Address/URL', '<p>This is the field type for a Joomla! web address/url field.</p>', 'url', 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '', '50', '', '', 'string', '', '', '', '', '', '', 'url', 'url', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Access Level', '<p>This is the field type for a standard Joomla! Access field.</p>', 'accesslevel', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '', '1', '', '', 'int', '', '', '', '', '', '', '', '', '', '', '', '', 'INT', '10', '''0''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Language', '<p>This is the field type for a standard Joomla! Language field.</p>', 'contentlanguage', 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, '', 'inputbox', '', '1', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '7', '', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Time Zone', '<p>This is the field type for a standard Joomla! Time Zone field.</p>', 'timezone', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '', '1', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '50', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Password', '<p>This is the field type for a standard Joomla! Password field.</p>', 'password', 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, '', 'inputbox', '', '25', '', '', 'string', '', '', '', '', '', '', 'password', '', '', '', '', '', 'VARCHAR', '100', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Telephone', '<p>This is the field type for a Joomla! telephone number field.</p>', 'tel', 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '50', '25', '', '', 'string', '', '', '', '', '', '', 'tel', 'tel', '', '', '', '', 'VARCHAR', '50', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Combo', '<p>This is the field type for a standard Joomla! Combo field.</p>', 'combo', 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Email Address', '<p>This is the field type for a standard Joomla! email address field.</p>', 'email', 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', 'inputbox', '255', '50', '', '', 'string', '', '', '', '', '', '', 'email', '', '', '', '', '', 'VARCHAR', '255', '''''', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1),
('Tag', '<p>This is the field type for a Component Architect Tag field. This utilises a standard joomla SQL field to access the tags table.</p>', 'tag', 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', 'inputbox', '1', '', '', '', 'string', '', '', '', '', '', '', '', '', '', '', '', '', 'VARCHAR', '2048', '', 10102, '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 1);

UPDATE `#__componentarchitect_fieldtypes`
SET `validation_type_default` = 'numeric'
WHERE `code_name` = 'modal';
-- [%%END_CUSTOM_CODE%%]
