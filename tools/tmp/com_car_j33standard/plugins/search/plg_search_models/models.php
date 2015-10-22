<?php
/**
 * @version 		$Id:$
 * @name			Car (Release 1.0.0)
 * @author			 ()
 * @package			com_car
 * @subpackage		com_car.search.car.models
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: compobjectplural.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.search.architectcomp.compobjectplural
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

require_once JPATH_SITE.'/components/com_car/router.php';

/**
 * Models Search plugin
 *
 */
class PlgSearchModels extends JPlugin
{
	/**
	 * @var    $layout	string	The sublayout to use when rendering the results.
	 */
	protected $layout = 'default';
	
	/**
	 * @var    $autoloadLanguage boolean	Load the language file on instantiation
	 */
	protected $autoloadLanguage = true;
		
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
	}
	/**
	 * @return array An array of search areas
	 */
	public function onContentSearchAreas()
	{
		static $areas = array(
			'models' => 'PLG_SEARCH_MODELS_MODELS'
			);
			return $areas;
	}

	/**
	 * Models Search method
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category(if used)
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */
	public function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$tag = JFactory::getLanguage()->getTag();

		require_once JPATH_SITE.'/components/com_car/helpers/route.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php';

		$search_text = $text;
		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		$sModel	= 1;
		$sModelArchived 	= 0;
		$limit = 50;		

		$sModel	= $this->params->get('search_models',1);
		$sModelArchived		= $this->params->get('search_archived_models',0);

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

		switch ($phrase)
		{
			case 'exact':
				$text		= $db->quote('%'.$db->escape($text, true).'%', false);
				$wheres_2	= array();
				$wheres_2[]	= $db->quoteName('a.name').' LIKE '.$text;
				$wheres_2[]	= $db->quoteName('a.description').' LIKE '.$text;
				$wheres_2[]	= $db->quoteName('a.metakey').' LIKE '.$text;
				$wheres_2[]	= $db->quoteName('a.metadesc').' LIKE '.$text;
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
					$wheres_2[]	= $db->quoteName('a.name').' LIKE '.$word;
					$wheres_2[]	= $db->quoteName('a.description').' LIKE '.$word;
					$wheres_2[]	= $db->quoteName('a.metakey').' LIKE '.$word;
					$wheres_2[]	= $db->quoteName('a.metadesc').' LIKE '.$word;
					$wheres[]	= implode(' OR ', $wheres_2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		$order = '';
		switch ($ordering)
		{
			case 'oldest':
				$order = $db->quoteName('a.created').' ASC';
				break;

			case 'popular':
				$order = $db->quoteName('a.hits').' DESC';
				break;
			case 'alpha':
				$order = $db->quoteName('a.name').' ASC';
				break;
			case 'newest':
				$order = $db->quoteName('a.created').' DESC';
				break;	

			default:
				$order = $db->quoteName('a.ordering').' DESC';
				
				break;
		}

		$rows = array();
		$query	= $db->getQuery(true);

		$search_section = JText::_('PLG_SEARCH_MODELS_MODELS');
		// search models
		if ($sModel AND $limit > 0)
		{
			$query->clear();
					
			//sqlsrv changes
			$slug_select = ' CASE WHEN ';
			$slug_select .= $query->charLength('a.alias', '!=', '0');
			$slug_select .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$slug_select .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$slug_select .= ' ELSE ';
			$slug_select .= $a_id.' END as slug, ';

								
			$query->select(
						$db->quoteName('a.name').' AS title, '.
						$db->quoteName('a.metadesc').', '.$db->quoteName('a.metakey').', '.
						$db->quoteName('a.created').' AS created, '.
						$db->quoteName('a.description').' AS text, '.
						$db->quoteName('a.language').' AS language, '.						
						$db->quote($search_section).' AS section, '.
						$slug_select.
						'"2" AS browsernav');
			$query->from($db->quoteName('#__car_models').' AS a');
			$query->where('('. $where .')' 
						.'AND '.$db->quoteName('a.state').' = 1 '
						.'AND '.$db->quoteName('a.access').' IN ('.$groups.') '
						.'AND ('.$db->quoteName('a.publish_up').' = '.$db->quote($null_date).' OR '.$db->quoteName('a.publish_up').' <= '.$db->quote($now).') '
						.'AND ('.$db->quoteName('a.publish_down').' = '.$db->quote($null_date).' OR '.$db->quoteName('a.publish_down').' >= '.$db->quote($now).') '
						);

			// Filter by language
			if ($app->isSite() AND JLanguageMultilang::isEnabled())
			{
				$query->where($db->quoteName('a.language').' IN (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				$query->where($db->quoteName('c.language').' IN (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
			}
			
			$query->group($db->quoteName('a.id').', '.$db->quoteName('a.name'));
			$query->order($order);

			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);

			if (isset($list))
			{
				foreach($list as $key => $item)
				{
					$list[$key]->href = JRoute::_(CarHelperRoute::getModelRoute($item->slug, $item->language, $this->layout, $keep_item_id));
					//Add the selected item id to the link if there is one
					$list[$key]->href .= $item_id_str;										
										
				}
			}
			$rows[] = $list;
		}

		// search archived models
		if ($sModelArchived AND $limit > 0)
		{
			$query->clear();

			//sqlsrv changes
			$slug_select = ' CASE WHEN ';
			$slug_select .= $query->charLength('a.alias', '!=', '0');
			$slug_select .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$slug_select .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$slug_select .= ' ELSE ';
			$slug_select .= $a_id.' END AS slug, ';

			
			$query->select(
						$db->quoteName('a.name').' AS title, '.
						$db->quoteName('a.metadesc').', '.$db->quoteName('a.metakey').', '.
						$db->quoteName('a.created').' AS created, '.
						$db->quoteName('a.description').' AS text, '.
						$db->quoteName('a.language').' AS language, '.						
						$db->quote($search_section).' AS section, '.
						$slug_select.
						'"2" AS browsernav');
			$query->from($db->quoteName('#__car_models').' AS a');
			$query->where('('. $where .') '
				.'AND '.$db->quoteName('a.state').' = 2 '
				.'AND '.$db->quoteName('a.access').' IN ('.$groups.') '
				.'AND ('.$db->quoteName('a.publish_up').' = '.$db->quote($null_date).' OR '.$db->quoteName('a.publish_up').' <= '.$db->quote($now).') '
				.'AND ('.$db->quoteName('a.publish_down').' = '.$db->quote($null_date).' OR '.$db->quoteName('a.publish_down').' >= '.$db->quote($now).') '
			);	
				
			// Filter by language
			if ($app->isSite() AND JLanguageMultilang::isEnabled())
			{
				$query->where($db->quoteName('a.language').' IN (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				$query->where($db->quoteName('c.language').' IN (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
			}	
			$query->order($order);


			$db->setQuery($query, 0, $limit);
			$list3 = $db->loadObjectList();

			// find an itemid for archived to use if there isn't another one
			$item	= $app->getMenu()->getItems('link', 'index.php?option=com_car&view=modelarchive', true);
			$item_id = isset($item) ? '&Itemid='.$item->id : $item_id_str;

			if (isset($list3))
			{
				foreach($list3 as $key => $item)
				{
					$date = JFactory::getDate($item->created);

					$created_month	= $date->format("n");
					$created_year	= $date->format("Y");

					$list3[$key]->href	= JRoute::_('index.php?option=com_car&view=modelarchive'.
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
				foreach($row as $model)
				{
					if (SearchHelper::checkNoHTML($model, $search_text, array('title',
																						'description',
																						'metadesc', 
																						'metakey'
																						)))
					{
						$new_row[] = $model;
					}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}
}
