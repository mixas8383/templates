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
-- @CAversion		Id:uninstall.architectcomp_mysql.utf8.sql 19 2012-01-12 16:33:49Z BrianWade $
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
-- Uninstall of `#__app` tables
--
DROP TABLE IF EXISTS `#__app_items`;

DROP TABLE IF EXISTS `#__app_rating`;

DELETE FROM `#__assets` WHERE `name` LIKE '%com_app%';

DELETE FROM `#__extensions` WHERE `name`='com_app' AND `type`='component';

DELETE FROM `#__categories` WHERE `extension`='com_app';
