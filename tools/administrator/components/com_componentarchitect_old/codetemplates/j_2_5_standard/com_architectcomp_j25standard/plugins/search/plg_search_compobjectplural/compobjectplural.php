<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].search.[%%architectcomp%%].[%%compobjectplural%%]
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: compobjectplural.php 418 2014-10-22 14:42:36Z BrianWade $
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

require_once JPATH_SITE.'/components/[%%com_architectcomp%%]/router.php';

/**
 * [%%CompObject_plural_name%%] Search plugin
 *
 */
class plgSearch[%%CompObjectPlural%%] extends JPlugin
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
			'[%%compobjectplural%%]' => 'PLG_SEARCH_[%%COMPOBJECTPLURAL%%]_[%%COMPOBJECTPLURAL%%]'
			);
			return $areas;
	}

	/**
	 * [%%CompObject_plural_name%%] Search method
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

		require_once JPATH_SITE.'/components/[%%com_architectcomp%%]/helpers/route.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php';

		$search_text = $text;
		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		$s[%%CompObject%%]	= 1;
		[%%IF INCLUDE_STATUS%%]
		$s[%%CompObject%%]Archived 	= 0;
		[%%ENDIF INCLUDE_STATUS%%]
		$limit = 50;		

		$s[%%CompObject%%]	= $this->params->get('search_[%%compobjectplural%%]',1);
		[%%IF INCLUDE_STATUS%%]
		$s[%%CompObject%%]Archived		= $this->params->get('search_archived_[%%compobjectplural%%]',0);
		[%%ENDIF INCLUDE_STATUS%%]

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
				[%%IF INCLUDE_NAME%%]
				$wheres_2[]	= 'a.name LIKE '.$text;
				[%%ENDIF INCLUDE_NAME%%]
				[%%IF INCLUDE_DESCRIPTION%%]
				$wheres_2[]	= 'a.description LIKE '.$text;
				[%%ENDIF INCLUDE_DESCRIPTION%%]
				[%%IF INCLUDE_METADATA%%]
				$wheres_2[]	= 'a.metakey LIKE '.$text;
				$wheres_2[]	= 'a.metadesc LIKE '.$text;
				[%%ENDIF INCLUDE_METADATA%%]
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
					[%%IF INCLUDE_NAME%%]
					$wheres_2[]	= 'a.name LIKE '.$word;
					[%%ENDIF INCLUDE_NAME%%]					
					[%%IF INCLUDE_DESCRIPTION%%]
					$wheres_2[]	= 'a.description LIKE '.$word;
					[%%ENDIF INCLUDE_DESCRIPTION%%]
					[%%IF INCLUDE_METADATA%%]
					$wheres_2[]	= 'a.metakey LIKE '.$word;
					$wheres_2[]	= 'a.metadesc LIKE '.$word;
					[%%ENDIF INCLUDE_METADATA%%]
					$wheres[]	= implode(' OR ', $wheres_2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		$order = '';
		switch ($ordering)
		{
			[%%IF INCLUDE_CREATED%%]
			case 'oldest':
				$order = 'a.created ASC';
				break;

			[%%ENDIF INCLUDE_CREATED%%]
			[%%IF INCLUDE_HITS%%]
			case 'popular':
				$order = 'a.hits DESC';
				break;
			[%%ENDIF INCLUDE_HITS%%]
			[%%IF INCLUDE_NAME%%]
			case 'alpha':
				$order = 'a.name ASC';
				break;
			[%%ENDIF INCLUDE_NAME%%]
			[%%IF GENERATE_CATEGORIES%%]
			case 'category':
				[%%IF INCLUDE_NAME%%]
				$order = 'c.title ASC, a.name ASC';
				$morder = 'a.name ASC';
				[%%ELSE INCLUDE_NAME%%]
				$order = 'c.title ASC';
				[%%ENDIF INCLUDE_NAME%%]
				break;
			[%%ENDIF GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_CREATED%%]
			case 'newest':
				$order = 'a.created DESC';
				break;	
			[%%ENDIF INCLUDE_CREATED%%]		

			default:
				[%%IF INCLUDE_ORDERING%%]
				$order = 'a.ordering DESC';
				[%%ELSE INCLUDE_ORDERING%%]
				$order = 'a.id DESC';
				[%%ENDIF INCLUDE_ORDERING%%]
				
				break;
		}

		$rows = array();
		$query	= $db->getQuery(true);

		$search_section = JText::_('PLG_SEARCH_[%%COMPOBJECTPLURAL%%]_[%%COMPOBJECTPLURAL%%]');
		// search [%%compobject_plural_name%%]
		if ($s[%%CompObject%%] AND $limit > 0)
		{
			$query->clear();
			
			$slug_select = 'a.id as slug, ';
			[%%IF GENERATE_CATEGORIES%%]
			$slug_select .=	'c.id as catslug, ';
			[%%ENDIF GENERATE_CATEGORIES%%]
			
			[%%IF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
			$slug_select = 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, ';
					[%%IF GENERATE_CATEGORIES%%]
			$slug_select .=	'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, ';
					[%%ENDIF GENERATE_CATEGORIES%%]	
				[%%ENDIF INCLUDE_ALIAS%%]
			[%%ENDIF INCLUDE_NAME%%]		
			
			$query->select(
						[%%IF INCLUDE_NAME%%]
						'a.name AS title, '.
						[%%ENDIF INCLUDE_NAME%%]
						[%%IF INCLUDE_METADATA%%]
						'a.metadesc, a.metakey, '.
						[%%ENDIF INCLUDE_METADATA%%]
						[%%IF INCLUDE_CREATED%%]
						'a.created AS created, '.
						[%%ENDIF INCLUDE_CREATED%%]
						[%%IF INCLUDE_DESCRIPTION%%]
						'a.description AS text, '.
						[%%ENDIF INCLUDE_DESCRIPTION%%]
						[%%IF INCLUDE_LANGUAGE%%]
						'a.language AS language, '.						
						[%%ENDIF INCLUDE_LANGUAGE%%]
						[%%IF GENERATE_CATEGORIES%%]
						'CONCAT_WS('.$db->quote($search_section).'"/", c.title) AS section, '.
						[%%ELSE GENERATE_CATEGORIES%%]
						$db->quote($search_section).' AS section, '.
						[%%ENDIF GENERATE_CATEGORIES%%]
						$slug_select.
						'"2" AS browsernav');
			$query->from('#__[%%architectcomp%%]_[%%compobjectplural%%] AS a');
			[%%IF GENERATE_CATEGORIES%%]
			$query->innerJoin('#__categories AS c ON c.id=a.catid');
			[%%ENDIF GENERATE_CATEGORIES%%]			
			$query->where('('. $where .')' 
						[%%IF INCLUDE_STATUS%%]
						.'AND a.state=1 '
						[%%ENDIF INCLUDE_STATUS%%]
						[%%IF GENERATE_CATEGORIES%%]			
						.'AND c.published = 1 '
						[%%IF INCLUDE_ACCESS%%]
						.'AND c.access IN ('.$groups.') '
						[%%ENDIF INCLUDE_ACCESS%%]
						[%%ENDIF GENERATE_CATEGORIES%%]						
						[%%IF INCLUDE_ACCESS%%]
						.'AND a.access IN ('.$groups.') '
						[%%ENDIF INCLUDE_ACCESS%%]
						[%%IF INCLUDE_PUBLISHED_DATES%%]
						.'AND (a.publish_up = '.$db->quote($null_date).' OR a.publish_up <= '.$db->quote($now).') '
						.'AND (a.publish_down = '.$db->quote($null_date).' OR a.publish_down >= '.$db->quote($now).') '
						[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
						);

			[%%IF INCLUDE_LANGUAGE%%]
			// Filter by language
			if ($app->isSite() AND $app->getLanguageFilter())
			{
				$query->where('a.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				$query->where('c.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
			}
			[%%ENDIF INCLUDE_LANGUAGE%%]
			
			[%%IF INCLUDE_NAME%%]
			$query->group('a.id, a.name');
			[%%ELSE INCLUDE_NAME%%]
			$query->group('a.id');
			[%%ENDIF INCLUDE_NAME%%]
			$query->order($order);

			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);

			if (isset($list))
			{
				foreach($list as $key => $item)
				{
					[%%IF GENERATE_CATEGORIES%%]		 
						[%%IF INCLUDE_LANGUAGE%%]
					$list[$key]->href = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->catslug, $item->language, $this->layout, $keep_item_id));
						[%%ELSE INCLUDE_LANGUAGE%%]
					$list[$key]->href = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->catslug, $this->layout, $keep_item_id));
						[%%ENDIF INCLUDE_LANGUAGE%%]
					[%%ELSE GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_LANGUAGE%%]
					$list[$key]->href = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->language, $this->layout, $keep_item_id));
						[%%ELSE INCLUDE_LANGUAGE%%]
					$list[$key]->href =  JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $this->layout, $keep_item_id));
						[%%ENDIF INCLUDE_LANGUAGE%%]	
					[%%ENDIF GENERATE_CATEGORIES%%]				
					//Add the selected item id to the link if there is one
					$list[$key]->href .= $item_id_str;										
										
				}
			}
			$rows[] = $list;
		}

		// search archived [%%compobject_plural_name%%]
		if ($s[%%CompObject%%]Archived AND $limit > 0)
		{
			$searchArchived = JText::_('JARCHIVED');

			$query->clear();

			$slug_select = 'a.id as slug, ';
			[%%IF GENERATE_CATEGORIES%%]
			$slug_select .=	'c.id as catslug, ';
			[%%ENDIF GENERATE_CATEGORIES%%]
			
			[%%IF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
			$slug_select = 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, ';
					[%%IF GENERATE_CATEGORIES%%]
			$slug_select .=	'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, ';
					[%%ENDIF GENERATE_CATEGORIES%%]	
				[%%ENDIF INCLUDE_ALIAS%%]	
			[%%ENDIF INCLUDE_NAME%%]	
			
			$query->select(
						[%%IF INCLUDE_NAME%%]
						'a.name AS title, '.
						[%%ENDIF INCLUDE_NAME%%]
						[%%IF INCLUDE_METADATA%%]
						'a.metadesc, a.metakey, '.
						[%%ENDIF INCLUDE_METADATA%%]
						[%%IF INCLUDE_CREATED%%]
						'a.created AS created, '.
						[%%ENDIF INCLUDE_CREATED%%]
						[%%IF INCLUDE_DESCRIPTION%%]
						'a.description AS text, '.
						[%%ENDIF INCLUDE_DESCRIPTION%%]
						[%%IF INCLUDE_LANGUAGE%%]
						'a.language AS language, '.						
						[%%ENDIF INCLUDE_LANGUAGE%%]
						$slug_select.
						[%%IF GENERATE_CATEGORIES%%]
						'CONCAT_WS('.$db->quote($search_section).'"/", c.title) AS section, '.
						[%%ELSE GENERATE_CATEGORIES%%]
						$db->quote($search_section).' AS section, '.
						[%%ENDIF GENERATE_CATEGORIES%%]
						'"2" AS browsernav');
			$query->from('#__[%%architectcomp%%]_[%%compobjectplural%%] AS a');
			[%%IF GENERATE_CATEGORIES%%]
			$join = '#__categories AS c ON c.id=a.catid';
			[%%IF INCLUDE_ACCESS%%]
			$join .= 'AND c.access IN ('. $groups .')';
			[%%ENDIF INCLUDE_ACCESS%%]
			$query->innerJoin($join);
			[%%ENDIF GENERATE_CATEGORIES%%]			
			$query->where('('. $where .') '
				[%%IF INCLUDE_STATUS%%]
				.'AND a.state = 2 '
				[%%ENDIF INCLUDE_STATUS%%]
				[%%IF GENERATE_CATEGORIES%%]			
				.'AND c.published = 1 '
				[%%IF INCLUDE_ACCESS%%]
				.'AND c.access IN ('.$groups.') '
				[%%ENDIF INCLUDE_ACCESS%%]
				[%%ENDIF GENERATE_CATEGORIES%%]						
				[%%IF INCLUDE_ACCESS%%]
				.'AND a.access IN ('.$groups.') '
				[%%ENDIF INCLUDE_ACCESS%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
				.'AND (a.publish_up = '.$db->quote($null_date).' OR a.publish_up <= '.$db->quote($now).') '
				.'AND (a.publish_down = '.$db->quote($null_date).' OR a.publish_down >= '.$db->quote($now).') '
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			);	
				
			[%%IF INCLUDE_LANGUAGE%%]
			// Filter by language
			if ($app->isSite() AND $app->getLanguageFilter())
			{
				$query->where('a.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				$query->where('c.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
			}	
			[%%ENDIF INCLUDE_LANGUAGE%%]								
			$query->order($order);


			$db->setQuery($query, 0, $limit);
			$list3 = $db->loadObjectList();

			// find an itemid for archived to use if there isn't another one
			$item	= $app->getMenu()->getItems('link', 'index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]archive', true);
			$item_id = isset($item) ? '&Itemid='.$item->id : $item_id_str;

			if (isset($list3))
			{
				foreach($list3 as $key => $item)
				{
					[%%IF INCLUDE_CREATED%%]
					$date = JFactory::getDate($item->created);

					$created_month	= $date->format("n");
					$created_year	= $date->format("Y");
					[%%ENDIF INCLUDE_CREATED%%]

					$list3[$key]->href	= JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]archive'.
													[%%IF INCLUDE_CREATED%%]
													'&year='.$created_year.'&month='.$created_month.
													[%%ENDIF INCLUDE_CREATED%%]
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
				foreach($row AS $key => $[%%compobject_code_name%%])
				{
					if (searchHelper::checkNoHTML($[%%compobject_code_name%%], $search_text, array('title',
																						[%%IF INCLUDE_DESCRIPTION%%]
																						'description',
																						[%%ENDIF INCLUDE_DESCRIPTION%%]
																						[%%IF INCLUDE_METADATA%%]
																						'metadesc', 
																						'metakey'
																						[%%ENDIF INCLUDE_METADATA%%]
																						)))
					{
						$new_row[] = $[%%compobject_code_name%%];
					}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}
}
