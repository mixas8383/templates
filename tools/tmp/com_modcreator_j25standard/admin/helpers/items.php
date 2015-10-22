<?php
/**
 * @version 		$Id:$
 * @name			ModCreator (Release 1.0.0)
 * @author			 ()
 * @package			com_modcreator
 * @subpackage		com_modcreator.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectplural.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_2_5_standard (Release 1.0.4)
 * @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @Joomlacopyright Copyright (c)2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @CAlicense		GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

defined('_JEXEC') or die;
/**
 * Items component helper.
 *
 * 
 */
class ModcreatorItemsHelper
{
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The item ID.
	 *
	 * @return	JObject
	 * 
	 */
	public static function getActions($item_id = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
		$level = 'component';		
		if (!empty($item_id) AND $item_id !== 0) 
		{
			$asset_name = 'com_modcreator.item.'.(int) $item_id;
		}
		else
		{
			$asset_name = 'com_modcreator';
		}
		$actions = JAccess::getActions('com_modcreator', $level);

		foreach ($actions as $action) 
		{
			$result->set($action->name,	$user->authorise($action->name, $asset_name));
		}

		return $result;
	}


}