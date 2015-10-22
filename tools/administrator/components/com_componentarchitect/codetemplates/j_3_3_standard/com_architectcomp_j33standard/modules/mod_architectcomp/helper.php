<?php
/**
 * @tempversion$
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].mod_[%%architectcomp%%]
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: helper.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.mod_architectcomp
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

require_once JPATH_SITE.'/components/[%%com_architectcomp%%]/helpers/route.php';

abstract class Mod[%%ArchitectComp%%]Helper
{
	/**
	 * Helper for mod_[%%architectcomp%%]
	 *
	 * @param json/registry	$params	Module parameters
	 * 
	 * @return array		$items	Items to display
	 */
	public static function getList(&$params)
	{
		// Get the dbo
		$db = JFactory::getDbo();
		
		$component_object_names = explode(':',$params->get('componentobject'));
		
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/[%%com_architectcomp%%]/models', '[%%ArchitectComp%%]Model'.$component_object_names[1]);

		// Get an instance of the model
		$model = JModelLegacy::getInstance($component_object_names[1], '[%%ArchitectComp%%]Model', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$app_params = $app->getParams();
		$model->setState('params', $app_params);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		
		[%%IF INCLUDE_STATUS%%]
		$model->setState('filter.state', 1);
		[%%ENDIF INCLUDE_STATUS%%]
		[%%IF INCLUDE_ACCESS%%]
		// Access filter
		$access = !JComponentHelper::getParams('[%%com_architectcomp%%]')->get('show_'.JString::strtolower($component_object_names[0]).'_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);
		[%%ENDIF INCLUDE_ACCESS%%]
		[%%IF INCLUDE_CREATED%%]
		// User filter
		$user_id = JFactory::getUser()->get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$model->setState('filter.created_by_id', (int) $user_id);
				break;
			case 'not_me':
				$model->setState('filter.created_by_id', $user_id);
				$model->setState('filter.created_by_id.include', false);
				break;

			case '0':
				break;

			default:
				$model->setState('filter.created_by_id', (int) $params->get('user_id'));
				break;
		}
		[%%ENDIF INCLUDE_CREATED%%]

		[%%IF INCLUDE_LANGUAGE%%]
		// Filter by language
		$model->setState('filter.language',JLanguageMultilang::isEnabled());
		[%%ENDIF INCLUDE_LANGUAGE%%]

		// Set ordering but check that the component object for ordering matches the slected component object
		if (JString::strtolower($component_object_names[1]) == JString::substr($params->get('ordering'),0,JString::strlen($params->get('ordering'))-6))
		{
			$order_map = array(
				[%%FOREACH COMPONENT_OBJECT%%]
					[%%IF INCLUDE_MODIFIED%%]
						[%%IF INCLUDE_CREATED%%]
				'[%%compobjectplural%%] m_dsc' => 'a.modified DESC, a.created',
						[%%ELSE INCLUDE_CREATED%%]
				'[%%compobjectplural%%] m_dsc' => 'a.modified',
						[%%ENDIF INCLUDE_CREATED%%]
					[%%ENDIF INCLUDE_MODIFIED%%]
					[%%IF INCLUDE_CREATED%%]
				'[%%compobjectplural%%] c_dsc' => 'a.created',
					[%%ENDIF INCLUDE_CREATED%%]
					[%%IF INCLUDE_PUBLISHED_DATES%%]
				'[%%compobjectplural%%] p_dsc' => 'a.publish_up',
					[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
					[%%IF INCLUDE_HITS%%]
				'[%%compobjectplural%%] h_dsc' => 'a.hits',
					[%%ENDIF INCLUDE_HITS%%]
					[%%IF INCLUDE_NAME%%]
				'[%%compobjectplural%%] n_asc' => 'a.name',
					[%%ENDIF INCLUDE_NAME%%]
					[%%IF INCLUDE_NAME%%]
				'[%%compobjectplural%%] n_dsc' => 'a.name',
					[%%ENDIF INCLUDE_NAME%%]
					[%%IF INCLUDE_ORDERING%%]
				'[%%compobjectplural%%] o_asc' => 'a.ordering',
					[%%ENDIF INCLUDE_ORDERING%%]																
				[%%ENDFOR COMPONENT_OBJECT%%]
			);
			$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'));
		}
		else
		{
			$ordering = 'a.id';
		}
		
		if (JString::substr($params->get('ordering'),-3,3) == 'dsc')
		{
			$dir = 'DESC';
		}
		else
		{
			$dir = 'ASC';
		}

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $dir);

		$items = $model->getItems();
		
		if ($params->get('itemid') <> '')
		{
			$item_id_str = '&Itemid='.(string) $params->get('itemid');
			$keep_item_id = false;
		}
		else
		{
			$item_id_str = '';
			$keep_item_id = true;
		}
		
		foreach ($items as &$item)
		{
			[%%IF INCLUDE_ALIAS%%]
			if (isset($item->alias))
			{
				$item->slug = $item->id.':'.$item->alias;
			}
			else
			{
				$item->slug = $item->id;			
			}
			[%%ELSE INCLUDE_ALIAS%%]
			$item->slug = $item->id;			
			[%%ENDIF INCLUDE_ALIAS%%]			
			[%%IF GENERATE_CATEGORIES%%]
			if (isset($item->catid))
			{
				$item->catslug = $item->catid.':'.$item->category_alias;
			}
			[%%ENDIF GENERATE_CATEGORIES%%]

			[%%IF INCLUDE_ACCESS%%]
			if (!isset($item->access) OR $access OR in_array($item->access, $authorised))
			{
			[%%ENDIF INCLUDE_ACCESS%%]
				if ($item_id_str == '')
				{			
					// We know that user has the privilege to view the item
					$route_function = 'get'.$component_object_names[0].'Route';
					
					[%%IF GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_LANGUAGE%%]
					if (isset($item->catslug) AND isset($item->language))
					{
						$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug, $item->catslug, $item->language));
					}
					else
					{
						if (isset($item->catslug))
						{
							$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug, $item->catslug));
						}
						else
						{
							if (isset($item->language))
							{
								$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug, $item->language));
							}
							else
							{
								$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug));
							}
						}
					}
						[%%ELSE INCLUDE_LANGUAGE%%]
					if (isset($item->catslug))
					{
						$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug, $item->catslug));
					}
					else
					{
						$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug));
					}
						[%%ENDIF INCLUDE_LANGUAGE%%]						
					[%%ELSE GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_LANGUAGE%%]
					if (isset($item->language))
					{
						$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug, $item->language));
					}
					else
					{
						$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug));
					}
						[%%ELSE INCLUDE_LANGUAGE%%]
					if (isset($item->catslug))
					{
						$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug, $item->catslug));
					}
					else
					{
						$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::$route_function($item->slug));
					}
						[%%ENDIF INCLUDE_LANGUAGE%%]						
					[%%ENDIF GENERATE_CATEGORIES%%]					
				}
				[%%IF INCLUDE_ACCESS%%]
				else
				{
					$item->link = JRoute::_('index.php?option=[%%com_architectcomp%%]&view='.JString::strtolower($component_object_names[0]).$item_id_str.'&id='.$item->id);
				}
				[%%ENDIF INCLUDE_ACCESS%%]				
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}

		return $items;
	}
}
