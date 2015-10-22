<?php
/**
 * @version 		$Id:$
 * @name			ModCreator (Release 1.0.0)
 * @author			 ()
 * @package			com_modcreator
 * @subpackage		com_modcreator.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: router.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
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
 * Build the route for the ModCreator component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function ModcreatorBuildRoute(&$query)
{
	$segments = array();

	// get a menu item based on Itemid or currently active
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$params	= JComponentHelper::getParams('com_modcreator');
	$advanced = $params->get('sef_advanced_link', 0);

	if (empty($query['Itemid']))
	{
		$menu_item = $menu->getActive();
	}
	else
	{
		$menu_item = $menu->getItem($query['Itemid']);
	}
	$menu_view	= (empty($menu_item->query['view'])) ? null : $menu_item->query['view'];
	$menu_id	= (empty($menu_item->query['id'])) ? null : $menu_item->query['id'];

	if (isset($query['view']))
	{
		$view = $query['view'];
		// For a modal layout force the route to the view in the uri not that of the menu item
		if (isset($query['layout']) AND $query['layout'] == 'modal')
		{
			$segments[] = $query['view'];
			unset($query['Itemid']);			
		}
		else
		{
			if (empty($query['Itemid']))
			{
				$segments[] = $query['view'];
			}
		}
	}

	// are we dealing with a modcreator that is attached to a menu item?
	if (isset($view) AND ($menu_view == $view) AND (isset($query['id'])) AND ($menu_id == intval($query['id'])))
	{
		unset($query['view']);

		if (isset($query['layout']) AND $query['layout'] != 'edit')
		{
			unset($query['layout']);
		}			
		unset($query['id']);
		return $segments;
	}
	
	// This is just some code

	if (isset($view) )
	{
		switch ($view)
		{
			case 'item':
				if (!isset($query['id']) OR $menu_id != (int) $query['id'] OR $menu_view != $view)
				{
					
					// Make sure we have the id and the alias
					if (strpos($query['id'], ':') === false)
					{
						$db = JFactory::getDbo();
						$dbQuery = $db->getQuery(true)
							->select('alias')
							->from('#__modcreator_items')
							->where('id=' . (int) $query['id']);
						$db->setQuery($dbQuery);
						$alias = $db->loadResult();
						$query['id'] = $query['id'] . ':' . $alias;
					}
					
					if($advanced)
					{
						list($tmp, $id) = explode(':', $query['id'], 2);
					}
					else
					{
						$id = isset($query['id']) ? $query['id'] : null;
					}
					$segments[] = 'item';
					$segments[] = $id;
				}
				unset($query['view']);
				unset($query['id']);
				break;
			default:
				break;	
		}
	}


	return $segments;
}
/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function ModcreatorParseRoute($segments)
{
	$vars = array();
	// Get the request view as this is need for multi view parsing
	$view = JRequest::getCmd('view', '');

	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('com_modcreator');
	$advanced = $params->get('sef_advanced_link', 0);

	// Count route segments
	$count = count($segments);

	// Standard routing for newsfeeds.
	if (!isset($item))
	{
		$vars['view']	= $segments[0];
		$vars['id']		= $segments[$count - 1];
		return $vars;
	}
	
	if (!$advanced)
	{
		$vars['id'] = (int) $segments[$count - 1];		
		$vars['view'] = $segments[$count - 2];	
		return $vars;	
	}

	$id = (isset($item->query['id']) AND $item->query['id'] > 1) ? $item->query['id'] : 'root';
	$vars['id'] = $id;
	$found = 0;
	foreach($segments as $segment)
	{
		$segment = $advanced ? str_replace(':', '-',$segment) : $segment;

		if ($found == 0)
		{
			if ($item->query['view'] == 'items' OR $item->query['view'] == 'item'
				OR $view == 'items' OR $view == 'item')
			{
				if($advanced)
				{
					$db = JFactory::getDBO();
					
					$where = ' WHERE ';
					$where .= 'alias = '.$db->quote($segment);
					$query = 'SELECT id FROM #__modcreator_items'.$where;					
						
							
					$db->setQuery($query);
					$nid = $db->loadResult();
				}
				else
				{
					$nid = $segment;
				}
				$vars['id'] = $nid;
				$vars['view'] = 'item';
			}
		}
	
		$found = 0;
	}
	return $vars;
}

