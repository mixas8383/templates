-- @version 		$Id: uninstall.componentarchitect_mysql.utf8.sql 411 2014-10-19 18:39:07Z BrianWade $
-- @name			Component Architect (Release 1.1.3)
-- @author			Component Architect (www.componentarchitect.com)
-- @package			com_componentarchitect
-- @subpackage		com_componentarchitect.admin
-- @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
-- @license			GNU General Public License version 3 or later - See http://www.gnu.org/copyleft/gpl.html 
--
-- The following Component Architect header section must remain in any distribution of this file
--
-- @CAversion		Id:uninstall.architectcomp_mysql.utf8.sql 19 2012-01-12 16:33:49Z BrianWade $
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
-- ----------------------------------------------------------
-- Uninstall of `#__componentarchitect` tables
--
DROP TABLE IF EXISTS `#__componentarchitect_components`;
DROP TABLE IF EXISTS `#__componentarchitect_componentobjects`;
DROP TABLE IF EXISTS `#__componentarchitect_fieldsets`;
DROP TABLE IF EXISTS `#__componentarchitect_fields`;
DROP TABLE IF EXISTS `#__componentarchitect_fieldtypes`;
DROP TABLE IF EXISTS `#__componentarchitect_codetemplates`;
-- [%%START_CUSTOM_CODE%%]
DROP TABLE IF EXISTS `#__componentarchitect_sessiondata`;
-- [%%END_CUSTOM_CODE%%]

DELETE FROM `#__assets` WHERE `name` LIKE '%com_componentarchitect%';

DELETE FROM `#__extensions` WHERE `name`='com_componentarchitect' AND `type`='component';

DELETE FROM `#__categories` WHERE `extension`='com_componentarchitect';
