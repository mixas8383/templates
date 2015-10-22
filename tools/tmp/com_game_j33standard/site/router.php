<?php
/**
 * @version 		$Id:$
 * @name			Game (Release 1.0.0)
 * @author			 ()
 * @package			com_game
 * @subpackage		com_game.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: router.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
 * @CAtemplate		joomla_3_3_standard (Release 1.0.3)
 * @CAcopyright		Copyright (c)2013 - 2014  Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
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
 * Routing class for the Game component
 *
 */
class GameRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the Game component
	 *
	 * @param	array	$query		An array of URL arguments
	 *
	 * @return	array	$segments	The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$segments = array();

		// Get a menu item based on Itemid or currently active
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$params	= JComponentHelper::getParams('com_game');
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
		$menu_cat_id	= (empty($menu_item->query['catid'])) ? null : $menu_item->query['catid'];
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

		// Are we dealing with a game that is attached to a menu item?
		if (isset($view) AND ($menu_view == $view) AND (isset($query['id'])) AND ($menu_id == intval($query['id'])))
		{
			unset($query['view']);
			if (isset($query['catid']))
			{
				unset($query['catid']);
			}

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
				case 'category':
					if (!isset($query['id']) OR $menu_id != (int) $query['id'] OR $menu_view != $view)
					{
						if (isset($query['catid']))
						{
							$cat_id = $query['catid'];
						}
						else
						{
							if(isset($query['id']))
							{
								$cat_id = $query['id'];
							}
							else
							{
								$cat_id = null;
							}
						}	
									
						$menu_cat_id = $menu_id;
						$options['countItems'] = false;
						$options['table'] = '';				
						$categories = JCategories::getInstance('Game',$options);
						$category = $categories->get($cat_id);
						//TODO Throw error that the category either not exists or is unpublished
						$path = array_reverse($category->getPath());

						$array = array();
						foreach($path as $id)
						{
							if((int) $id == (int)$menu_cat_id)
							{
								break;
							}
							if($advanced)
							{
								list($tmp, $id) = explode(':', $id, 2);
							}
							$array[] = $id;
						}
						array_splice($array,1, 0, 'category');
						$segments = array_reverse($array);
					}
					unset($query['view']);
					unset($query['id']);
					unset($query['catid']);
													
					break;			
				case 'item':
					if (!isset($query['id']) OR $menu_id != (int) $query['id'] OR $menu_view != $view)
					{
						if (isset($query['catid']))
						{
							$cat_id = $query['catid'];
						}
						else
						{
							if(isset($query['id']))
							{
								$cat_id = $query['id'];
							}
							else
							{
								$cat_id = null;
							}
						}	

						$menu_cat_id = $menu_id;
						$options['countItems'] = false;
						$options['table'] = '';				
						$categories = JCategories::getInstance('Game',$options);
						$category = $categories->get($cat_id);
						if($category)
						{
							//TODO Throw error that the category either not exists or is unpublished
							$path = array_reverse($category->getPath());

							$array = array();
							foreach($path as $id)
							{
								if((int) $id == (int)$menu_cat_id)
								{
									break;
								}
								if($advanced)
								{
									list($tmp, $id) = explode(':', $id, 2);
								}
								$array[] = $id;
							}
							$segments = array_merge($segments, array_reverse($array));
						}
						
						// Make sure we have the id and the alias
						if (strpos($query['id'], ':') === false)
						{
							$db = JFactory::getDbo();
							$dbQuery = $db->getQuery(true)
								->select('alias')
								->from('#__game_items')
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
					unset($query['catid']);
					break;
				default:
					break;	
			}
		}

		if (!isset($query['layout']) OR $query['layout'] == 'default')
		{
			if (isset($menu_item->query['layout']) AND $menu_item->query['layout'] == 'blog' AND isset($query['id']))
			{
				$query['layout'] = 'article';
			}
		};

		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}
		
		return $segments;
	}
	/**
	 * Parse the segments of a URL.
	 *
	 * @param	array	$segments	The segments of the URL to parse.
	 *
	 * @return	array	$vars		The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$app	= JFactory::getApplication();
		$total = count($segments);
		$vars = array();

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}
		
		// Get the request view as this is need for multi view parsing
		$view = $app->input->getString('view', '');

		//Get the active menu item.
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getActive();
		$params = JComponentHelper::getParams('com_game');
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
		// From the categories view, we can only jump to a category.	
		$options['countItems'] = false;
		$options['table'] = '';	
		if ($item->query['view'] == 'categories' OR $item->query['view'] == 'category')
		{
			$categories = JCategories::getInstance('Game',$options)->get($id)->getChildren();
		}
		else
		{
			$categories = JCategories::getInstance('Game',$options);	
		}
		$vars['catid'] = $id;
		$vars['id'] = $id;
		$found = 0;
		foreach($segments as $segment)
		{
			$segment = $advanced ? str_replace(':', '-',$segment) : $segment;
			foreach($categories as $category)
			{
				if ($category->slug == $segment 
					OR $category->alias == $segment
					)
				{
					$vars['id'] = $category->id;
					$vars['catid'] = $category->id;
					$vars['view'] = 'category';
					$categories = $category->getChildren();
					$found = 1;
					break;
				}
			}

			if ($found == 0)
			{
				if ($item->query['view'] == 'items' OR $item->query['view'] == 'item'
					OR $view == 'items' OR $view == 'item')
				{
					if($advanced)
					{
						$db = JFactory::getDbo();
							
						$query = $db->getQuery(true);
						$query->select($db->quoteName('id'));
						$query->from($db->quoteName('#__game_items'));
						$query->where($db->quoteName('catid') . ' = ' . (int) $vars['catid']);
						$query->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
								
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
}