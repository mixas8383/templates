-- @version 		$Id: 1.1.2 .sql 411 2014-10-19 18:39:07Z BrianWade $
-- @name			Component Architect (Release 1.1.0)
-- @author			Component Architect (www.componentarchitect.com)
-- @package			com_componentarchitect
-- @subpackage		com_componentarchitect.admin
-- @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
-- @license			GNU General Public License version 3 or later - See http://www.gnu.org/copyleft/gpl.html 
--
-- The following Component Architect header section must remain in any distribution of this file
--
-- @CAversion		Id:1.0.0.sql 19 2012-01-12 16:33:49Z BrianWade $
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
-- Updates to table `#__componentarchitect_components`
--
--
-- Updates to table `#__componentarchitect_componentobjects`
--
--
-- Updates to table `#__componentarchitect_fieldsets`
--
--
-- Updates to table `#__componentarchitect_fields`
--
ALTER TABLE `#__componentarchitect_fields` CHANGE `accept` `max_file_size` VARCHAR(10);
--
-- Updates to table `#__componentarchitect_fieldtypes`
--
UPDATE `#__componentarchitect_fieldtypes`
SET `accept_default` = '', `accept` = 0
WHERE `code_name` = 'media';

ALTER TABLE `#__componentarchitect_fieldtypes` CHANGE `accept` `max_file_size` TINYINT(1);

ALTER TABLE `#__componentarchitect_fieldtypes` CHANGE `accept_default` `max_file_size_default` VARCHAR(10);

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a standard Joomla! File field.</p><p>Code to upload a file specified in a File field type is NOT included in any code templates provided with the free version of Ccomponent Architect. Any uploaded function must be manually coded in the generated compononent.</p>'
WHERE `code_name` = 'file';

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a standard Joomla! File List field.</p>'
WHERE `code_name` = 'filelist';

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a standard Joomla! Folder List field.</p>'
WHERE `code_name` = 'folderlist';

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a standard Joomla! Grouped List field.</p>'
WHERE `code_name` = 'groupedlist';

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a standard Joomla! Image List field.</p>'
WHERE `code_name` = 'imagelist';

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a standard Joomla! Checkbox field.</p>'
WHERE `code_name` = 'checkboxes';

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a standard Joomla! Integer field.</p>'
WHERE `code_name` = 'integer';

UPDATE `#__componentarchitect_fieldtypes`
SET `description` = '<p>This is the field type for a Component Architect Tag field. This utilises a standard joomla SQL field to access the tags table.</p>'
WHERE `code_name` = 'tag';
--
-- Updates to table `#__componentarchitect_codetemplates`
--
