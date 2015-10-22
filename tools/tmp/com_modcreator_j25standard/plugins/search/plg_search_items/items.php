<?php
/**
 * @version 		$Id:$
 * @name			ModCreator (Release 1.0.0)
 * @author			 ()
 * @package			com_modcreator
 * @subpackage		com_modcreator.search.modcreator.items
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: compobjectplural.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.search.architectcomp.compobjectplural
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

require_once JPATH_SITE.'/components/com_modcreator/router.php';

/**
 * Items Search plugin
 *
 */
class plgSearchItems extends JPlugin
{
	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 * 
	 */
	protected $layout = 'default';
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * 
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		if (isset($config['layout']))
		{		
			$this->layout = str_replace('_:','',$config['layout']);
		}		
		$this->loadLanguage();
	}
	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas()
	{
		static $areas = array(
			'items' => 'PLG_SEARCH_ITEMS_ITEMS'
			);
			return $areas;
	}

	/**
	 * Items Search method
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category(if used)
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$tag = JFactory::getLanguage()->getTag();

		require_once JPATH_SITE.'/components/com_modcreator/helpers/route.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php';

		$search_text = $text;
		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		$sItem	= 1;
		$sItemArchived 	= 0;
		$limit = 50;		

		$sItem	= $this->params->get('search_items',1);
		$sItemArchived		= $this->params->get('search_archived_items',0);

		$limit			= $this->params->def('search_limit',		50);
		if ($this->params->get('itemid') <> '')
		{
			$item_id_str = '&Itemid='.(string) $this->params->get('itemid');
			$keep_item_id = true;
		}
		else
		{
			$item_id_str = '';
			$keep_item_id = false;
		}

		$null_date		= $db->getNullDate();
		$date = JFactory::getDate();
		$now = $date->toSQL();

		$text = JString::trim($text);
		if ($text == '')
		{
			return array();
		}

		$wheres = array();
		switch ($phrase)
		{
			case 'exact':
				$text		= $db->quote('%'.$db->escape($text, true).'%', false);
				$wheres_2	= array();
				$wheres_2[]	= 'a.name LIKE '.$text;
				$wheres_2[]	= 'a.description LIKE '.$text;
				$wheres_2[]	= 'a.metakey LIKE '.$text;
				$wheres_2[]	= 'a.metadesc LIKE '.$text;
				$where		= '(' . implode(') OR (', $wheres_2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word)
				{
					$word		= $db->quote('%'.$db->escape($word, true).'%', false);
					$wheres_2	= array();
					$wheres_2[]	= 'a.name LIKE '.$word;
					$wheres_2[]	= 'a.description LIKE '.$word;
					$wheres_2[]	= 'a.metakey LIKE '.$word;
					$wheres_2[]	= 'a.metadesc LIKE '.$word;
					$wheres[]	= implode(' OR ', $wheres_2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		$order = '';
		switch ($ordering)
		{
			case 'oldest':
				$order = 'a.created ASC';
				break;

			case 'popular':
				$order = 'a.hits DESC';
				break;
			case 'alpha':
				$order = 'a.name ASC';
				break;
			case 'category':
				$order = 'c.title ASC, a.name ASC';
				$morder = 'a.name ASC';
				break;
			case 'newest':
				$order = 'a.created DESC';
				break;	

			default:
				$order = 'a.ordering DESC';
				
				break;
		}

		$rows = array();
		$query	= $db->getQuery(true);

		$search_section = JText::_('PLG_SEARCH_ITEMS_ITEMS');
		// search items
		if ($sItem AND $limit > 0)
		{
			$query->clear();
			
			$slug_select = 'a.id as slug, ';
			$slug_select .=	'c.id as catslug, ';
			
			$slug_select = 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, ';
			$slug_select .=	'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, ';
			
			$query->select(
						'a.name AS title, '.
						'a.metadesc, a.metakey, '.
						'a.created AS created, '.
						'a.description AS text, '.
						'a.language AS language, '.						
						'CONCAT_WS('.$db->quote($search_section).'"/", c.title) AS section, '.
						$slug_select.
						'"2" AS browsernav');
			$query->from('#__modcreator_items AS a');
			$query->innerJoin('#__categories AS c ON c.id=a.catid');
			$query->where('('. $where .')' 
						.'AND a.state=1 '
						.'AND c.published = 1 '
						.'AND c.access IN ('.$groups.') '
						.'AND a.access IN ('.$groups.') '
						.'AND (a.publish_up = '.$db->quote($null_date).' OR a.publish_up <= '.$db->quote($now).') '
						.'AND (a.publish_down = '.$db->quote($null_date).' OR a.publish_down >= '.$db->quote($now).') '
						);

			// Filter by language
			if ($app->isSite() AND $app->getLanguageFilter())
			{
				$query->where('a.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				$query->where('c.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
			}
			
			$query->group('a.id, a.name');
			$query->order($order);

			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);

			if (isset($list))
			{
				foreach($list as $key => $item)
				{
					$list[$key]->href = JRoute::_(ModcreatorHelperRoute::getItemRoute($item->slug, $item->catslug, $item->language, $this->layout, $keep_item_id));
					//Add the selected item id to the link if there is one
					$list[$key]->href .= $item_id_str;										
										
				}
			}
			$rows[] = $list;
		}

		// search archived items
		if ($sItemArchived AND $limit > 0)
		{
			$searchArchived = JText::_('JARCHIVED');

			$query->clear();

			$slug_select = 'a.id as slug, ';
			$slug_select .=	'c.id as catslug, ';
			
			$slug_select = 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, ';
			$slug_select .=	'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, ';
			
			$query->select(
						'a.name AS title, '.
						'a.metadesc, a.metakey, '.
						'a.created AS created, '.
						'a.description AS text, '.
						'a.language AS language, '.						
						$slug_select.
						'CONCAT_WS('.$db->quote($search_section).'"/", c.title) AS section, '.
						'"2" AS browsernav');
			$query->from('#__modcreator_items AS a');
			$join = '#__categories AS c ON c.id=a.catid';
			$join .= 'AND c.access IN ('. $groups .')';
			$query->innerJoin($join);
			$query->where('('. $where .') '
				.'AND a.state = 2 '
				.'AND c.published = 1 '
				.'AND c.access IN ('.$groups.') '
				.'AND a.access IN ('.$groups.') '
				.'AND (a.publish_up = '.$db->quote($null_date).' OR a.publish_up <= '.$db->quote($now).') '
				.'AND (a.publish_down = '.$db->quote($null_date).' OR a.publish_down >= '.$db->quote($now).') '
			);	
				
			// Filter by language
			if ($app->isSite() AND $app->getLanguageFilter())
			{
				$query->where('a.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				$query->where('c.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
			}	
			$query->order($order);


			$db->setQuery($query, 0, $limit);
			$list3 = $db->loadObjectList();

			// find an itemid for archived to use if there isn't another one
			$item	= $app->getMenu()->getItems('link', 'index.php?option=com_modcreator&view=itemarchive', true);
			$item_id = isset($item) ? '&Itemid='.$item->id : $item_id_str;

			if (isset($list3))
			{
				foreach($list3 as $key => $item)
				{
					$date = JFactory::getDate($item->created);

					$created_month	= $date->format("n");
					$created_year	= $date->format("Y");

					$list3[$key]->href	= JRoute::_('index.php?option=com_modcreator&view=itemarchive'.
													'&year='.$created_year.'&month='.$created_month.
													$item_id);
				}
			}

			$rows[] = $list3;
		}

		$results = array();
		if (count($rows))
		{
			foreach($rows as $row)
			{
				$new_row = array();
				foreach($row AS $key => $item)
				{
					if (searchHelper::checkNoHTML($item, $search_text, array('title',
																						'description',
																						'metadesc', 
																						'metakey'
																						)))
					{
						$new_row[] = $item;
					}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}
}
