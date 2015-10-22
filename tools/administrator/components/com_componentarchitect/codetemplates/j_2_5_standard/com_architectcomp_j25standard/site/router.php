<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].site
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @version			$Id: router.php 418 2014-10-22 14:42:36Z BrianWade $
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

[%%IF GENERATE_CATEGORIES%%]	
jimport('joomla.application.categories');
[%%ENDIF GENERATE_CATEGORIES%%]

/**
 * Build the route for the [%%ArchitectComp_name%%] component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function [%%ArchitectComp%%]BuildRoute(&$query)
{
	$segments = array();

	// get a menu item based on Itemid or currently active
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$params	= JComponentHelper::getParams('[%%com_architectcomp%%]');
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
	[%%IF GENERATE_CATEGORIES%%]	
	$menu_cat_id	= (empty($menu_item->query['catid'])) ? null : $menu_item->query['catid'];
	[%%ENDIF GENERATE_CATEGORIES%%]	
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

	// are we dealing with a [%%architectcomp%%] that is attached to a menu item?
	if (isset($view) AND ($menu_view == $view) AND (isset($query['id'])) AND ($menu_id == intval($query['id'])))
	{
		unset($query['view']);
		[%%IF GENERATE_CATEGORIES%%]		
		if (isset($query['catid']))
		{
			unset($query['catid']);
		}
		[%%ENDIF GENERATE_CATEGORIES%%]	

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
			[%%IF GENERATE_CATEGORIES%%]	
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
					$categories = JCategories::getInstance('[%%ArchitectComp%%]',$options);
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
			[%%ENDIF GENERATE_CATEGORIES%%]
			[%%FOREACH COMPONENT_OBJECT%%]
			case '[%%compobject%%]':
				if (!isset($query['id']) OR $menu_id != (int) $query['id'] OR $menu_view != $view)
				{
					[%%IF GENERATE_CATEGORIES%%]
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
					$categories = JCategories::getInstance('[%%ArchitectComp%%]',$options);
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
					[%%ENDIF GENERATE_CATEGORIES%%]
					[%%IF INCLUDE_NAME%%]
						[%%IF INCLUDE_ALIAS%%]
					
					// Make sure we have the id and the alias
					if (strpos($query['id'], ':') === false)
					{
						$db = JFactory::getDbo();
						$dbQuery = $db->getQuery(true)
							->select('alias')
							->from('#__[%%architectcomp%%]_[%%compobjectplural%%]')
							->where('id=' . (int) $query['id']);
						$db->setQuery($dbQuery);
						$alias = $db->loadResult();
						$query['id'] = $query['id'] . ':' . $alias;
					}
					
						[%%ENDIF INCLUDE_ALIAS%%]
					[%%ENDIF INCLUDE_NAME%%]					
					if($advanced)
					{
						list($tmp, $id) = explode(':', $query['id'], 2);
					}
					else
					{
						$id = isset($query['id']) ? $query['id'] : null;
					}
					$segments[] = '[%%compobject%%]';
					$segments[] = $id;
				}
				unset($query['view']);
				unset($query['id']);
				[%%IF GENERATE_CATEGORIES%%]		
				unset($query['catid']);
				[%%ENDIF GENERATE_CATEGORIES%%]
				break;
			[%%ENDFOR COMPONENT_OBJECT%%]					
			default:
				break;	
		}
	}

	[%%IF GENERATE_SITE_LAYOUT_BLOG%%]
		[%%IF GENERATE_SITE_LAYOUT_ARTICLE%%]
	if (!isset($query['layout']) OR $query['layout'] == 'default')
	{
		if (isset($menu_item->query['layout']) AND $menu_item->query['layout'] == 'blog' AND isset($query['id']))
		{
			$query['layout'] = 'article';
		}
	};
		[%%ENDIF GENERATE_SITE_LAYOUT_ARTICLE%%]
	[%%ENDIF GENERATE_SITE_LAYOUT_BLOG%%]

	return $segments;
}
/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function [%%ArchitectComp%%]ParseRoute($segments)
{
	$vars = array();
	// Get the request view as this is need for multi view parsing
	$view = JRequest::getCmd('view', '');

	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('[%%com_architectcomp%%]');
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
	[%%IF GENERATE_CATEGORIES%%]	
	// From the categories view, we can only jump to a category.	
	$options['countItems'] = false;
	$options['table'] = '';	
	if ($item->query['view'] == 'categories' OR $item->query['view'] == 'category')
	{
		$categories = JCategories::getInstance('[%%ArchitectComp%%]',$options)->get($id)->getChildren();
	}
	else
	{
		$categories = JCategories::getInstance('[%%ArchitectComp%%]',$options);	
	}
	$vars['catid'] = $id;
	[%%ENDIF GENERATE_CATEGORIES%%]	
	$vars['id'] = $id;
	$found = 0;
	foreach($segments as $segment)
	{
		$segment = $advanced ? str_replace(':', '-',$segment) : $segment;
		[%%IF GENERATE_CATEGORIES%%]		
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
		[%%ENDIF GENERATE_CATEGORIES%%]

		if ($found == 0)
		{
			[%%FOREACH COMPONENT_OBJECT%%]
			if ($item->query['view'] == '[%%compobjectplural%%]' OR $item->query['view'] == '[%%compobject%%]'
				OR $view == '[%%compobjectplural%%]' OR $view == '[%%compobject%%]')
			{
				if($advanced)
				{
					$db = JFactory::getDBO();
					
					[%%IF GENERATE_CATEGORIES%%]					
					$where = ' WHERE ';
					$where .= 'catid = '.(int) $vars['catid'];
						[%%IF INCLUDE_NAME%%]
							[%%IF INCLUDE_ALIAS%%]	
					$where .= ' AND ';	
					$where .= 'alias = '.$db->quote($segment);	
							[%%ENDIF INCLUDE_ALIAS%%]
						[%%ENDIF INCLUDE_NAME%%]
					$query = 'SELECT id FROM #__[%%architectcomp%%]_[%%compobjectplural%%]'.$where;																			
					[%%ELSE GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_NAME%%]
							[%%IF INCLUDE_ALIAS%%]
					$where = ' WHERE ';
					$where .= 'alias = '.$db->quote($segment);
					$query = 'SELECT id FROM #__[%%architectcomp%%]_[%%compobjectplural%%]'.$where;					
							[%%ELSE INCLUDE_ALIAS%%]
					$query = 'SELECT id FROM #__[%%architectcomp%%]_[%%compobjectplural%%]';
							[%%ENDIF INCLUDE_ALIAS%%]
						[%%ELSE INCLUDE_NAME%%]
					$query = 'SELECT id FROM #__[%%architectcomp%%]_[%%compobjectplural%%]';
						[%%ENDIF INCLUDE_NAME%%]
						
					[%%ENDIF GENERATE_CATEGORIES%%]
							
					$db->setQuery($query);
					$nid = $db->loadResult();
				}
				else
				{
					$nid = $segment;
				}
				$vars['id'] = $nid;
				$vars['view'] = '[%%compobject%%]';
			}
			[%%ENDFOR COMPONENT_OBJECT%%]	
		}
	
		$found = 0;
	}
	return $vars;
}

