<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.itemnavigation
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: itemnavigation.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.itemnavigation
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
 * Example navigation plugin class.
 *
 */
class plgExampleItemnavigation extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	public function onItemBeforeDisplay ($context, &$row, &$params, $page=0)
	{
		$html = '';
		$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		$print = JRequest::getBool('print');

		if ($print) 
		{
			return false;
		}

		if ($params->get('show_item_navigation') AND 
			($context == 'com_example.item') AND 
			($view == 'item')) 
		{
			
			$html = '';
			$db		= JFactory::getDbo();
			$user	= JFactory::getUser();
			$app	= JFactory::getApplication();
			$lang	= JFactory::getLanguage();
			$null_date = $db->getNullDate();

			$date	= JFactory::getDate();
			$config	= JFactory::getConfig();
			$now	= $date->toSQL();

			$uid	= $row->id;
			$option	= 'com_example';
			$can_publish = $user->authorise('core.edit.state', $option.'.item.'.$row->id);
			$query	= $db->getQuery(true);
			

			
	        $slug_select = ' CASE WHEN ';
	        $slug_select .= $query->charLength('a.alias');
	        $slug_select .= ' THEN ';
	        $a_id = $query->castAsChar('a.id');
	        $slug_select .= $query->concatenate(array($a_id, 'a.alias'), ':');
	        $slug_select .= ' ELSE ';
	        $slug_select .= $a_id.' END as slug, ';			
	        $slug_select .= ' CASE WHEN ';
	        $slug_select .= $query->charLength('cc.alias');
	        $slug_select .= ' THEN ';
	        $c_id = $query->castAsChar('cc.id');
	        $slug_select .= $query->concatenate(array($c_id, 'cc.alias'), ':');
	        $slug_select .= ' ELSE ';
			$slug_select .= $c_id.' END as catslug, ';			
			
			$query->select($slug_select.
				'a.params,'.
				'a.language, '.
				'a.id');
			$query->from('#__example_items AS a');
			$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
			// Join over users for created by
			$query->select('ua.name AS created_by_name');
			$query->join('LEFT', '#__users AS ua on ua.id = a.created_by');
			
			if ($app->isSite() AND $app->getLanguageFilter())
			{
				$query->where('a.language in ('.$db->quote($lang->getTag()).','.$db->quote('*').')');
			}
						
			// Filter by a same category as the selected row
			if ($params->get('limit_category_fieldtype_navigation',false) == true) 
			{
				$query->where('a.catid = '. (int)$row->catid);
			} 
			

			if (!$can_publish) 
			{
				$query->where( '(a.state = 1 OR a.state = -1)'
					.' AND (a.publish_up = '.$db->quote($null_date)
					.' OR a.publish_up <= '.$db->quote($now).')'
					.' AND (a.publish_down = '.$db->quote($null_date)
					.' OR a.publish_down >= '.$db->quote($now).')'
					);
			}
			else
			{
				$query->where('a.state = '. (int)$row->state);			
			}
						
			if ($params->get('show_item_noauth') <> 1 AND $params->get('show_item_noauth') <> 'use_item')
			{
				$query->where('a.access = ' .(int)$row->access);
			}
			// Add the list ordering clause.
			$initial_sort = $params->get('item_initial_sort');
			// Falll back to old style if the parameter hasn't been set yet.
			if (empty($initial_sort))
			{
				$query->order($db->escape($params->get('list.ordering', 'a.ordering')).' '.$db->escape($params->get('list.direction', 'ASC')));
			}
			else 
			{
				$query->order('a.'.$initial_sort.' '.$db->escape($params->get('list.direction', 'ASC')));
			}	
			$db->setQuery($query);
			
			$list = $db->loadObjectList('id');

			// This check needed if incorrect Itemid is given resulting in an incorrect result.
			if (!is_array($list)) 
			{
				$list = array();
			}

			reset($list);

			// Location of current item item in array list.
			$location = array_search($uid, array_keys($list));

			$rows = array_values($list);

			$row->prev = null;
			$row->next = null;
			
			// Get the global params
			$global_params = JComponentHelper::getParams('com_example', true);

			if ($location -1 >= 0)	
			{
				$row->prev = $location -1 ; 
				// The previous item item cannot be in the array position -1.
				for ($i = $location-1; $i >= 0; $i--)
				{

					$row->prev = $rows[$i];
					break;

				}

			}

			if (($location +1) < count($rows)) 
			{
				$row->next = $location +1;
				// The next item item cannot be in an array position greater than the number of array postions.
				for ($i = $location+1; $i <= count($rows)-1; $i++)
				{
					$row->next = $rows[$i];
					break;
				}	

			}

			$pn_space = "";
			if (JText::_('JGLOBAL_LT') OR JText::_('JGLOBAL_GT')) 
			{
				$pn_space = " ";
			}


			$keep_item_id = (int) $params->get('keep_item_itemid', 0);		
					
			if ($row->prev) 
			{
				$row->prev = JRoute::_(ExampleHelperRoute::getItemRoute($row->prev->slug,$row->prev->catslug, $row->prev->language, $layout, $keep_item_id));
			} 
			else 
			{
				$row->prev = '';
			}

			if ($row->next) 
			{
				$row->next = JRoute::_(ExampleHelperRoute::getItemRoute($row->next->slug,$row->next->catslug, $row->next->language, $layout, $keep_item_id));
							
			} 
			else 
			{
				$row->next = '';
			}

			// Output.
			if ($row->prev OR $row->next) 
			{
				$html = '
				<ul class="pagenav">'
				;
				if ($row->prev) 
				{
					$html .= '
					<li class="pagenav-prev">
						<a href="'. $row->prev .'" rel="prev">'
						. JText::_('JGLOBAL_LT') . $pn_space . JText::_('JPREV') . '</a>
					</li>';
				}



				if ($row->next) 
				{
					$html .= '
					<li class="pagenav-next">
						<a href="'. $row->next .'" rel="next">'
						. JText::_('JNEXT') . $pn_space . JText::_('JGLOBAL_GT') .'</a>
					</li>';
				}
				$html .= '
				</ul>';
				$row->pagination = $html;
				$row->paginationposition = $this->params->get('item_position', 1);
				// This will default to the 1.5 and 1.6-1.7 behavior.
				$row->paginationrelative = $this->params->get('item_relative',0);				
			}
		}

		return ;
	}
}
