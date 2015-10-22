<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].itemnavigation
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: itemnavigation.php 418 2014-10-22 14:42:36Z BrianWade $
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
 * [%%ArchitectComp%%] navigation plugin class.
 *
 */
class plg[%%ArchitectComp%%]Itemnavigation extends JPlugin
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
	[%%FOREACH COMPONENT_OBJECT%%]
		[%%IF GENERATE_PLUGINS%%]
			[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	public function on[%%CompObject%%]BeforeDisplay ($context, &$row, &$params, $page=0)
	{
		$html = '';
		$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		$print = JRequest::getBool('print');

		if ($print) 
		{
			return false;
		}

		if ($params->get('show_[%%compobject%%]_navigation') AND 
			($context == '[%%com_architectcomp%%].[%%compobject%%]') AND 
			($view == '[%%compobject%%]')) 
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
			$option	= '[%%com_architectcomp%%]';
			[%%IF INCLUDE_ASSETACL%%]
				[%%IF INCLUDE_ASSETACL_RECORD%%]
			$can_publish = $user->authorise('core.edit.state', $option.'.[%%compobject%%].'.$row->id);
				[%%ELSE INCLUDE_ASSETACL_RECORD%%]
			$can_publish = $user->authorise('core.edit.state', $option);
				[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
			[%%ENDIF INCLUDE_ASSETACL%%]
			$query	= $db->getQuery(true);
			

			
			[%%IF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
	        $slug_select = ' CASE WHEN ';
	        $slug_select .= $query->charLength('a.alias');
	        $slug_select .= ' THEN ';
	        $a_id = $query->castAsChar('a.id');
	        $slug_select .= $query->concatenate(array($a_id, 'a.alias'), ':');
	        $slug_select .= ' ELSE ';
	        $slug_select .= $a_id.' END as slug, ';			
					[%%IF GENERATE_CATEGORIES%%]
	        $slug_select .= ' CASE WHEN ';
	        $slug_select .= $query->charLength('cc.alias');
	        $slug_select .= ' THEN ';
	        $c_id = $query->castAsChar('cc.id');
	        $slug_select .= $query->concatenate(array($c_id, 'cc.alias'), ':');
	        $slug_select .= ' ELSE ';
			$slug_select .= $c_id.' END as catslug, ';			
					[%%ENDIF GENERATE_CATEGORIES%%]
				[%%ELSE INCLUDE_ALIAS%%]
			$a_id = $query->castAsChar('a.id');			
			$slug_select = $a_id.' as slug, ';
					[%%IF GENERATE_CATEGORIES%%]
	        $c_id = $query->castAsChar('cc.id');				
			$slug_select .=	$c_id.' as catslug, ';
					[%%ENDIF GENERATE_CATEGORIES%%]			
				[%%ENDIF INCLUDE_ALIAS%%]
			[%%ELSE INCLUDE_NAME%%]
			$a_id = $query->castAsChar('a.id');			
			$slug_select = $a_id.' as slug, ';
					[%%IF GENERATE_CATEGORIES%%]
	        $c_id = $query->castAsChar('cc.id');				
			$slug_select .=	$c_id.' as catslug, ';
					[%%ENDIF GENERATE_CATEGORIES%%]			
			[%%ENDIF INCLUDE_NAME%%]
			
			$query->select($slug_select.
				[%%IF INCLUDE_PARAMS_RECORD%%]				
				'a.params,'.
				[%%ENDIF INCLUDE_PARAMS_RECORD%%]
				[%%IF INCLUDE_LANGUAGE%%]
				'a.language, '.
				[%%ENDIF INCLUDE_LANGUAGE%%]
				'a.id');
			$query->from('#__[%%architectcomp%%]_[%%compobjectplural%%] AS a');
			[%%IF GENERATE_CATEGORIES%%]
			$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
			[%%ENDIF GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_CREATED%%]
			// Join over users for created by
			$query->select('ua.name AS created_by_name');
			$query->join('LEFT', '#__users AS ua on ua.id = a.created_by');
			[%%ENDIF INCLUDE_CREATED%%]
			
			[%%IF INCLUDE_LANGUAGE%%]
			if ($app->isSite() AND $app->getLanguageFilter())
			{
				$query->where('a.language in ('.$db->quote($lang->getTag()).','.$db->quote('*').')');
			}
			[%%ENDIF INCLUDE_LANGUAGE%%]
						
			[%%IF GENERATE_CATEGORIES%%]
			// Filter by a same category as the selected row
			if ($params->get('limit_category_fieldtype_navigation',false) == true) 
			{
				$query->where('a.catid = '. (int)$row->catid);
			} 
			[%%ENDIF GENERATE_CATEGORIES%%]
			

			[%%IF INCLUDE_STATUS%%]
				[%%IF INCLUDE_ASSETACL%%]
			if (!$can_publish) 
			{
				[%%ENDIF INCLUDE_ASSETACL%%]
				$query->where( '(a.state = 1 OR a.state = -1)'
					[%%IF INCLUDE_PUBLISHED_DATES%%]
					.' AND (a.publish_up = '.$db->quote($null_date)
					.' OR a.publish_up <= '.$db->quote($now).')'
					.' AND (a.publish_down = '.$db->quote($null_date)
					.' OR a.publish_down >= '.$db->quote($now).')'
						[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
					);
				[%%IF INCLUDE_ASSETACL%%]
			}
			else
			{
				$query->where('a.state = '. (int)$row->state);			
			}
					[%%ENDIF INCLUDE_ASSETACL%%]
			[%%ELSE INCLUDE_STATUS%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
					[%%IF INCLUDE_ASSETACL%%]
			if (!$can_publish) 
			{
					[%%ENDIF INCLUDE_ASSETACL%%]
				$query->where('(a.publish_up = '.$db->quote($null_date)
					.' OR a.publish_up <= '.$db->quote($now).')'
					.' AND (a.publish_down = '.$db->quote($null_date)
					.' OR a.publish_down >= '.$db->quote($now).')'
					);
					[%%IF INCLUDE_ASSETACL%%]
			}
					[%%ENDIF INCLUDE_ASSETACL%%]
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			[%%ENDIF INCLUDE_STATUS%%]
						
			[%%IF INCLUDE_ACCESS%%]
			if ($params->get('show_[%%compobject%%]_noauth') <> 1 AND $params->get('show_[%%compobject%%]_noauth') <> 'use_[%%compobject%%]')
			{
				$query->where('a.access = ' .(int)$row->access);
			}
			[%%ENDIF INCLUDE_ACCESS%%]			
			[%%IF INCLUDE_PARAMS_RECORD%%]
			// Add the list ordering clause.
			$initial_sort = $params->get('[%%compobject%%]_initial_sort');
			// Falll back to old style if the parameter hasn't been set yet.
			if (empty($initial_sort))
			{
				[%%IF INCLUDE_ORDERING%%]
				$query->order($db->escape($params->get('list.ordering', 'a.ordering')).' '.$db->escape($params->get('list.direction', 'ASC')));
				[%%ELSE INCLUDE_ORDERING%%]
					[%%IF INCLUDE_NAME%%]
				$query->order($db->escape($params->get('list.ordering', 'a.name')).' '.$db->escape($params->get('list.direction', 'ASC')));
					[%%ELSE INCLUDE_NAME%%]				
				$query->order($db->escape($params->get('list.ordering', 'a.id')).' '.$db->escape($params->get('list.direction', 'ASC')));
					[%%ENDIF INCLUDE_NAME%%]				
				[%%ENDIF INCLUDE_ORDERING%%]			
			}
			else 
			{
				$query->order('a.'.$initial_sort.' '.$db->escape($params->get('list.direction', 'ASC')));
			}	
			[%%ENDIF INCLUDE_PARAMS_RECORD%%]			
			$db->setQuery($query);
			
			$list = $db->loadObjectList('id');

			// This check needed if incorrect Itemid is given resulting in an incorrect result.
			if (!is_array($list)) 
			{
				$list = array();
			}

			reset($list);

			// Location of current [%%compobject_name%%] item in array list.
			$location = array_search($uid, array_keys($list));

			$rows = array_values($list);

			$row->prev = null;
			$row->next = null;
			
			// Get the global params
			$global_params = JComponentHelper::getParams('[%%com_architectcomp%%]', true);

			if ($location -1 >= 0)	
			{
				$row->prev = $location -1 ; 
				// The previous [%%compobject_name%%] item cannot be in the array position -1.
				for ($i = $location-1; $i >= 0; $i--)
				{

					$row->prev = $rows[$i];
					break;

				}

			}

			if (($location +1) < count($rows)) 
			{
				$row->next = $location +1;
				// The next [%%compobject_name%%] item cannot be in an array position greater than the number of array postions.
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


			$keep_item_id = (int) $params->get('keep_[%%compobject%%]_itemid', 0);		
					
			if ($row->prev) 
			{
				[%%IF GENERATE_CATEGORIES%%]		 
					[%%IF INCLUDE_LANGUAGE%%]
				$row->prev = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug,$row->prev->catslug, $row->prev->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->prev = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug, $row->prev->catslug, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%ELSE GENERATE_CATEGORIES%%]
					[%%IF INCLUDE_LANGUAGE%%]
				$row->prev = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug, $row->prev->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->prev =  JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]	
				[%%ENDIF GENERATE_CATEGORIES%%]				
			} 
			else 
			{
				$row->prev = '';
			}

			if ($row->next) 
			{
				[%%IF GENERATE_CATEGORIES%%]		 
					[%%IF INCLUDE_LANGUAGE%%]
				$row->next = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug,$row->next->catslug, $row->next->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->next = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug, $row->next->catslug, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%ELSE GENERATE_CATEGORIES%%]
					[%%IF INCLUDE_LANGUAGE%%]
				$row->next = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug, $row->next->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->next =  JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]	
				[%%ENDIF GENERATE_CATEGORIES%%]				
							
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
				$row->paginationposition = $this->params->get('[%%compobject%%]_position', 1);
				// This will default to the 1.5 and 1.6-1.7 behavior.
				$row->paginationrelative = $this->params->get('[%%compobject%%]_relative',0);				
			}
		}

		return ;
	}
			[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
		[%%ENDIF GENERATE_PLUGINS%%]
	[%%ENDFOR COMPONENT_OBJECT%%]		
}
