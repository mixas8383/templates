<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].mod_[%%architectcomp%%]
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: helper.php 417 2014-10-22 14:42:10Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.mod_architectcomp
 * @CAtemplate		joomla_3_2_standard (Release 1.0.4)
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

JModelLegacy::addIncludePath(JPATH_SITE.'/components/[%%com_architectcomp%%]/models', '[%%ArchitectComp%%]Model[%%CompObjectPlural%%]');

abstract class Mod[%%CompObjectPlural%%]Helper
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

		// Get an instance of the generic [%%compobject_plural_name%%] model
		$model = JModelLegacy::getInstance('[%%CompObjectPlural%%]', '[%%ArchitectComp%%]Model', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$app_params = $app->getParams();
		$model->setState('params', $app_params);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		[%%IF INCLUDE_STATUS%%]
		$model->setState('filter.published', 1);
		[%%ENDIF INCLUDE_STATUS%%]
		[%%IF INCLUDE_ACCESS%%]
		// Access filter
		$access = !JComponentHelper::getParams('[%%com_architectcomp%%]')->get('show_[%%compobject%%]_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);
		[%%ENDIF INCLUDE_ACCESS%%]
		[%%IF GENERATE_CATEGORIES%%]
		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));
		[%%ENDIF GENERATE_CATEGORIES%%]
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

		// Filter by language
		$model->setState('filter.language',JLanguageMultilang::isEnabled());


		[%%IF INCLUDE_FEATURED%%]
		//  Filter by featured
		$model->setState('filter.featured', $params->get('show_featured'));
		[%%ENDIF INCLUDE_FEATURED%%]
		[%%IF INCLUDE_LANGUAGE%%]
		// Filter by language
		$model->setState('filter.language', JLanguageMultilang::isEnabled());
		[%%ENDIF INCLUDE_LANGUAGE%%]
		
		$order_map = array(
			[%%IF INCLUDE_MODIFIED%%]
				[%%IF INCLUDE_CREATED%%]
			'm_dsc' => 'a.modified DESC, a.created',
				[%%ELSE INCLUDE_CREATED%%]
			'm_dsc' => 'a.modified',
				[%%ENDIF INCLUDE_CREATED%%]
			[%%ENDIF INCLUDE_MODIFIED%%]
			[%%IF INCLUDE_CREATED%%]
			'c_dsc' => 'a.created',
			[%%ENDIF INCLUDE_CREATED%%]
			[%%IF INCLUDE_PUBLISHED_DATES%%]
			'p_dsc' => 'a.publish_up',
			[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			[%%IF INCLUDE_HITS%%]
			'h_dsc' => 'a.hits',
			[%%ENDIF INCLUDE_HITS%%]
			[%%IF INCLUDE_NAME%%]
			'n_asc' => 'a.name',
			[%%ENDIF INCLUDE_NAME%%]
			[%%IF INCLUDE_NAME%%]
			'n_dsc' => 'a.name',
			[%%ENDIF INCLUDE_NAME%%]
			[%%IF INCLUDE_ORDERING%%]
			'o_asc' => 'a.ordering',
			[%%ENDIF INCLUDE_ORDERING%%]			
		);
		[%%IF INCLUDE_NAME%%]
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.name');
		[%%ELSE INCLUDE_NAME%%]
			[%%IF INCLUDE_ORDERING%%]
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.ordering');
			[%%ELSE INCLUDE_ORDERING%%]
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.id');
			[%%ENDIF INCLUDE_ORDERING%%]			
		[%%ENDIF INCLUDE_NAME%%]			

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

		if ($params->get('[%%compobject%%]_layout') <> '')
		{
			$layout = str_replace('_:','',(string) $params->get('[%%compobject%%]_layout'));
			$layout_str = '&layout='.$layout;
		}
		else
		{
			$layout = '';
			$layout_str = '';
		}
				
		foreach ($items as &$item)
		{
			[%%IF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
			$item->slug = $item->id.':'.$item->alias;
				[%%ELSE INCLUDE_ALIAS%%]	
			$item->slug = $item->id;
				[%%ENDIF INCLUDE_ALIAS%%]
			[%%ELSE INCLUDE_NAME%%]
			$item->slug = $item->id;
			[%%ENDIF INCLUDE_NAME%%]
			
			[%%IF GENERATE_CATEGORIES%%]
			$item->catslug = $item->catid.':'.$item->category_alias;
			[%%ENDIF GENERATE_CATEGORIES%%]

			[%%IF INCLUDE_ACCESS%%]
			if ($access OR in_array($item->access, $authorised))
			{
			[%%ENDIF INCLUDE_ACCESS%%]
				if ($item_id_str == '')
				{
					// We know that user has the privilege to view the [%%compobject_name%%]
					[%%IF GENERATE_CATEGORIES%%]		 
						[%%IF INCLUDE_LANGUAGE%%]
					$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->catslug, $item->language, $layout));
						[%%ELSE INCLUDE_LANGUAGE%%]
					$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->catslug, $layout));
						[%%ENDIF INCLUDE_LANGUAGE%%]
					[%%ELSE GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_LANGUAGE%%]
					$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->language, $layout));
						[%%ELSE INCLUDE_LANGUAGE%%]
					$item->link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $layout));
						[%%ENDIF INCLUDE_LANGUAGE%%]	
					[%%ENDIF GENERATE_CATEGORIES%%]
				}
				else
				{
					$item->link = JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]'.$layout_str.$item_id_str.'&id='.$item->id);
				}					
			[%%IF INCLUDE_ACCESS%%]
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
			[%%ENDIF INCLUDE_ACCESS%%]
		}

		return $items;
	}
}
