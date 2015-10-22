<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.mod_example
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: helper.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.mod_architectcomp
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

require_once JPATH_SITE.'/components/com_example/helpers/route.php';

JModel::addIncludePath(JPATH_SITE.'/components/com_example/models', 'ExampleModelItems');

abstract class modItemsHelper
{
	public static function getList(&$params)
	{
		// Get the dbo
		$db = JFactory::getDbo();

		// Get an instance of the generic items model
		$model = JModel::getInstance('Items', 'ExampleModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$app_params = $app->getParams();
		$model->setState('params', $app_params);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);
		// Access filter
		$access = !JComponentHelper::getParams('com_example')->get('show_item_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);
		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));
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

		// Filter by language
		$model->setState('filter.language',$app->getLanguageFilter());


		//  Filter by featured
		$model->setState('filter.featured', $params->get('show_featured'));
		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());
		
		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created',
			'c_dsc' => 'a.created',
			'p_dsc' => 'a.publish_up',
			'h_dsc' => 'a.hits',
			'n_asc' => 'a.name',
			'n_dsc' => 'a.name',
			'o_asc' => 'a.ordering',
		);
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.name');

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

		if ($params->get('item_layout') <> '')
		{
			$layout = str_replace('_:','',(string) $params->get('item_layout'));
			$layout_str = '&layout='.$layout;
		}
		else
		{
			$layout = '';
			$layout_str = '';
		}
				
		foreach ($items as &$item)
		{
			$item->slug = $item->id.':'.$item->alias;
			
			$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access OR in_array($item->access, $authorised))
			{
				if ($item_id_str == '')
				{
					// We know that user has the privilege to view the item
					$item->link = JRoute::_(ExampleHelperRoute::getItemRoute($item->slug, $item->catslug, $item->language, $layout));
				}
				else
				{
					$item->link = JRoute::_('index.php?option=com_example&view=item'.$layout_str.$item_id_str.'&id='.$item->id);
				}					
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}

		return $items;
	}
}
