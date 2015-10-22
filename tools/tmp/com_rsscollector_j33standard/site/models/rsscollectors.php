<?php
/**
 * @version 		$Id:$
 * @name			Rss_collector (Release 1.0.0)
 * @author			 ()
 * @package			com_rsscollector
 * @subpackage		com_rsscollector.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectplural.php 423 2014-10-23 14:08:16Z BrianWade $
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
 * This models supports retrieving lists of rss_collectors.
 *
 */
class RssCollectorModelRssCollectors extends JModelList
{
	/**
	 * @var    string	$context	Context string for the model type.  This is used to handle uniqueness within sessions data.
	 */
	protected $context = 'com_rsscollector.rsscollectors';

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * 
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'created_by_name', 'ua.name',
				'modified', 'a.modified',
				'modified_by', 'a.modified_by',
				'modified_by_name', 'uam.name',	
				'publish_up', 'a.publish_up',				
				'publish_down', 'a.publish_down',	
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'ordering', 'a.ordering',
				);
		}

		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * 
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();
		$menu_params = new JRegistry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menu_params->loadString($menu->params);
		}

		$merged_params = clone $menu_params;
		$merged_params->merge($params);

		$this->setState('params', $merged_params);

		$params = $this->state->params;	
		
		$user		= JFactory::getUser();
		
		$item_id = $app->input->getInt('id', 0) . ':' .$app->input->getInt('Itemid', 0);

		// Check to see if a single rss_collector has been specified either as a parameter or in the url Request
		$pk = $params->get('rss_collector_id', '') == '' ? $app->input->getInt('id', '') : $params->get('rss_collector_id');
		$this->setState('filter.rss_collector_id', $pk);
		
		// List state information
			$limit = $app->getUserStateFromRequest($this->context.'.list.' . $item_id . '.limit', 'limit', $params->get('rsscollector_num_per_page'),'integer');
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context.'.limitstart','limitstart',0,'integer');
		$this->setState('list.start', $value);

		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		

		$order_col = $app->getUserStateFromRequest($this->context. '.filter_order', 'filter_order', $params->get('rsscollector_initial_sort','a.ordering'), 'string');
		if (!in_array($order_col, $this->filter_fields))
		{
			$order_col = $params->get('rsscollector_initial_sort','a.ordering');
		}

		$this->setState('list.ordering', $order_col);

		$list_order = $app->getUserStateFromRequest($this->context. '.filter_order_Dir', 'filter_order_Dir',  $params->get('rsscollector_initial_direction','ASC'), 'cmd');
		if (!in_array(JString::strtoupper($list_order), array('ASC', 'DESC', '')))
		{
			$list_order =  $params->get('rsscollector_initial_direction','ASC');
		}
		$this->setState('list.direction', $list_order);
		
				
		if ((!$user->authorise('core.edit.state', 'com_rsscollector')) AND  (!$user->authorise('core.edit', 'com_rsscollector')))
		{
			// filter on status of published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}		

		$this->setState('filter.language',JLanguageMultilang::isEnabled());
		
		// process show_rsscollector_noauth parameter
		if (!$params->get('show_rsscollector_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}
		if ($params->get('filter_rsscollector_featured') <> "")
		{
			$this->setState('filter.featured', $params->get('filter_rsscollector_featured'));
			
		}
		if ($params->get('filter_rsscollector_archived'))
		{
			$this->setState('filter.archived', $params->get('filter_rsscollector_archived'));
			
		}
		$this->setState('layout', $app->input->getString('layout'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * 
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.search');				
		$id .= ':'.serialize($this->getState('filter.published'));
		$id .= ':'.$this->getState('filter.archived');			
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.featured');
		$id .= ':'.serialize($this->getState('filter.created_by_id'));
		$id .= ':'.$this->getState('filter.created_by_id.include');
		$id .= ':'.$this->getState('filter.created_by_name');
		$id .= ':'.$this->getState('filter.created_by_name.include');	
		$id .= ':'.serialize($this->getState('filter.rss_collector_id'));
		$id .= ':'.$this->getState('filter.rss_collector_id.include');				
		

		return parent::getStoreId($id);
	}

	/**
	 * Get the main query for retrieving a list of rsscollectors subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 * 
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$user	= JFactory::getUser();
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		// Set date values
		$null_date = $db->quote($db->getNullDate());
		$now_date = $db->quote(JFactory::getDate()->toSQL());
		
		// Select the required fields from the table.
		$query->select(
			$this->getState(
					'list.select',
					'a.*'
					)
				);


		$query->from($db->quoteName('#__rsscollector_rsscollectors').' AS a');


		$query->select($db->quoteName('ua.name').' AS created_by_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS ua on '.$db->quoteName('ua.id').' = '.$db->quoteName('a.created_by'));

		// use created if modified is 0
		$query->select('CASE WHEN '.$db->quoteName('a.modified').' = ' . $null_date . ' THEN '.$db->quoteName('a.created').' ELSE '.$db->quoteName('a.modified').' END AS modified');
		$query->select($db->quoteName('uam.name').' AS modified_by_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS uam on '.$db->quoteName('uam.id').' = '.$db->quoteName('a.modified_by'));
		
		
			// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where($db->quoteName('a.language').' IN (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}
		
		//  Ignore this check publish date if the user can edit state
		$can_publish = $user->authorise('core.edit.state', 'com_rsscollector');
		//  Do not show unless today's date is wihtin the publish up and down dates (or they are empty)		
		if (!$can_publish)
		{
			// use created if publish_up is 0
			$query->select('CASE WHEN '.$db->quoteName('a.publish_up').' = ' . $null_date . ' THEN '.$db->quoteName('a.created').' ELSE '.$db->quoteName('a.publish_up').' END as publish_up');
						
			$query->where('('.$db->quoteName('a.publish_up').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_up').' <= ' . $now_date . ')');
			$query->where('('.$db->quoteName('a.publish_down').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_down').' >= ' . $now_date . ')');
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where($db->quoteName('a.access').' IN ('.$groups.')');
		}


		// Filter by published status
		$published = $this->getState('filter.published');
		$archived = $this->getState('filter.archived');		
		if (is_numeric($archived))
		{
			$query->where($db->quoteName('a.state').' = '. (int) $archived);
			
		}
		else
		{
			if (is_numeric($published))
			{
				$query->where($db->quoteName('a.state').' = '. (int) $published);
				
			}
			else 
			{
				if (is_array($published))
				{
					JArrayHelper::toInteger($published);
					$published = implode(',', $published);
					// Use rsscollector state 
					$query->where($db->quoteName('a.state').' IN ('.$published.')');
				}
			}
		}

		// Filter by featured state
		$featured = $this->getState('filter.featured');
		switch ($featured)
		{
			case '0':
				$query->where($db->quoteName('a.featured').' = 0');
				break;

			case '1':
				$query->where($db->quoteName('a.featured').' = 1');
				break;

			default:
				// Normally we do not discriminate
				// between featured/unfeatured items.
				break;
		}
		
					

		// Filter by a single or group of rsscollectors.
		$rss_collector_id = $this->getState('filter.rss_collector_id');
		if ($rss_collector_id != '')
		{
			if (is_numeric($rss_collector_id))
			{
				$type = $this->getState('filter.rss_collector_id.include', true) ? '= ' : '<> ';
				$query->where($db->quoteName('a.id').' '.$type.(int) $rss_collector_id);
			}
			else
			{
				if (is_array($rss_collector_id))
				{
					JArrayHelper::toInteger($rss_collector_id);
					$rss_collector_id = implode(',', $rss_collector_id);
					$type = $this->getState('filter.rss_collector_id.include', true) ? 'IN' : 'NOT IN';
					$query->where($db->quoteName('a.id').' '.$type.' ('.$rss_collector_id.')');
				}
			}
		}
		
		// Filter by created_by
		$created_by_id = $this->getState('filter.created_by_id');
		$created_by_where = '';

		if (is_numeric($created_by_id))
		{
			$type = $this->getState('filter.created_by_id.include', true) ? '= ' : '<> ';
			$created_by_where = $db->quoteName('a.created_by').' '.$type.(int) $created_by_id;
		}
		else 
		{
			if (is_array($created_by_id))
			{
				JArrayHelper::toInteger($created_by_id);
				$created_by_id = implode(',', $created_by_id);

				if ($created_by_id)
				{
					$type = $this->getState('filter.created_by_id.include', true) ? 'IN' : 'NOT IN';
					$created_by_where = $db->quoteName('a.created_by').' '.$type.' ('.$created_by_id.')';
				}
			}
		}


		if (!empty($created_by_where) )
		{
			$query->where('('.$created_by_where.')');
		}
		
		// Filter by created_by_name
		$created_by_name = $this->getState('filter.created_by_name');
		$created_by_name_where = '';

		if (is_string($created_by_name))
		{
			$type = $this->getState('filter.created_by_name.include', true) ? '= ' : '<> ';
			$created_by_name_where = $db->quoteName('ua.name').' '.$type.$db->quote($created_by_name);
		}
		else
		{
			if (is_array($created_by_name))
			{
				$first = current($created_by_name);

				if (!empty($first))
				{
					JArrayHelper::toString($created_by_name);

					foreach ($created_by_name as $key => $alias)
					{
						$created_by_name[$key] = $db->quote($alias);
					}

					$created_by_name = implode(',', $created_by_name);

					if ($created_by_name)
					{
						$type = $this->getState('filter.created_by_name.include', true) ? 'IN' : 'NOT IN';
						$created_by_name_where = $db->quoteName('ua.name').' '.$type.' ('.$created_by_name .
							')';
					}
				}
			}
		}

		if (!empty($created_by_name_where) )
		{
			$query->where('('.$created_by_name_where.')');
		}

		// process the filter for list views with user-entered filters
		$params = $this->getState('params');

		if ((is_object($params)) AND ($params->get('show_rsscollector_filter_field') != 'hide') AND ($filter = $this->getState('filter.search')))
		{
			// clean filter variable
			$filter = JString::strtolower($filter);
			$hits_filter = (int) $filter;
			$filter = $db->quote('%'.$db->escape($filter, true).'%', false);

			switch ($params->get('show_rsscollector_filter_field'))
			{
				case 'hits':
					$query->where($db->quoteName('a.hits').' >= '.(int) $hits_filter.' ');
					break;
				case 'created_by':
					$query->where('LOWER('.$db->quoteName('ua.name').') LIKE '.$filter.' ');
					break;	
				case 'name':
				default: // default to 'name' if parameter is not valid
					$query->where('LOWER('.$db->quoteName('a.name').') LIKE '.$filter);
					break;
				
			}
		}
		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where($db->quoteName('a.language').' IN ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}

		// Add the list ordering clause.
		if (is_object($params))
		{
			$initial_sort = $params->get('field_initial_sort');
		}
		else
		{
			$initial_sort = '';
		}
		// Fall back to old style if the parameter hasn't been set yet.
		if (empty($initial_sort) OR $this->getState('list.ordering') != '')
		{
			$order_col	= '';
			$order_dirn	= $this->state->get('list.direction');

		


			if ($this->state->get('list.ordering') == 'a.ordering' OR $this->state->get('list.ordering') == 'ordering')
			{
				$order_col	= '';
				$order_col	.= $db->quoteName('a.ordering').' '.$order_dirn;		
			}

			if ($order_col == '')
			{
				$order_col = is_string($this->getState('list.ordering')) ? $db->quoteName($this->getState('list.ordering')) : $db->quoteName('a.ordering');
				$order_col .= ' '.$order_dirn;
			}
			$query->order($db->escape($order_col));			
					
		}
		else
		{
			$query->order($db->quoteName('a.'.$initial_sort).' '.$db->escape($this->getState('list.direction', 'ASC')));
			
		}	
		return $query;
	}

	/**
	 * Method to get a list of rss_collectors.
	 *
	 * Overriden to inject convert the params fields into an object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * 
	 */
	public function getItems()
	{
		$db = $this->getDbo();
  		$query = $db->getQuery(true);
		
		$user	= JFactory::getUser();
		$user_id	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

		// Get the global params
		$global_params = JComponentHelper::getParams('com_rsscollector', true);
		
		if ($items = parent::getItems())
		{
			// Convert the parameter fields into objects.
			foreach ($items as &$item)
			{
				$query->clear();

				$rss_collector_params = new JRegistry;
				$rss_collector_params->loadString($item->params);

				// Convert the images field to an array.
				$registry = new JRegistry;
				$registry->loadString($item->images);
				$item->images = $registry->toArray();
				$registry = null; //release memory	

				// Convert the urls field to an array.
				$registry = new JRegistry;
				$registry->loadString($item->urls);
				$item->urls = $registry->toArray();
				$registry = null; //release memory	
				
				
				
				
				
				
				
				
				
				
		
							
				$item->introdescription = trim($item->intro) != '' ? $item->intro . $item->description : $item->description;

				
				// Unpack readmore and layout params
				$item->rsscollector_alternative_readmore = $rss_collector_params->get('rsscollector_alternative_readmore');
				$item->layout = $rss_collector_params->get('layout');
							
				if (!is_object($this->getState('params')))
				{
					$item->params = $rss_collector_params;
				}
				else
				{
					$item->params = clone $this->getState('params');

					// RssCollector params override menu item params only if menu param = 'use_rsscollector'
					// Otherwise, menu item params control the layout
					// If menu item is 'use_rsscollector' and there is no rsscollector param, use global

					// create an array of just the params set to 'use_rsscollector'
					$menu_params_array = $this->getState('params')->toArray();
					$rss_collector_array = array();

					foreach ($menu_params_array as $key => $value)
					{
						if ($value === 'use_rsscollector')
						{
							// if the rsscollector has a value, use it
							if ($rss_collector_params->get($key) != '')
							{
								// get the value from the rsscollector
								$rss_collector_array[$key] = $rss_collector_params->get($key);
							}
							else
							{
								// otherwise, use the global value
								$rss_collector_array[$key] = $global_params->get($key);
							}
						}
					}

					// merge the selected rsscollector params
					if (count($rss_collector_array) > 0)
					{
						$rss_collector_params = new JRegistry;
						$rss_collector_params->loadArray($rss_collector_array);
						$item->params->merge($rss_collector_params);
					}


					// get display date
					switch ($item->params->get('list_show_rsscollector_date'))
					{
						case 'modified':
							$item->display_date = $item->modified;
							break;
						case 'publish_up':
							$item->display_date = $item->publish_up;
							if ($item->publish_up == 0)
							{
								$item->display_date = $item->created;
							}
							break;
						case 'created':
							$item->display_date = $item->created;
							break;
						default:
							$item->display_date = 0;
							break;
					}
				}
				// Compute the asset access permissions.
				// Technically guest could edit an rsscollector, but lets not check that to improve performance a little.
				if (!$guest) 
				{
					$asset	= 'com_rsscollector.rsscollector.'.$item->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset))
					{
						$item->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					else
					{ 
						if (!empty($user_id) AND $user->authorise('core.edit.own', $asset)) 
						{
							// Check for a valid user and that they are the owner.
							if ($user_id == $item->created_by) 
							{
								$item->params->set('access-edit', true);
							}
						}
					}
					if ($user->authorise('core.create', $asset))
					{
						$item->params->set('access-create', true);
					}	
						
					
					if ($user->authorise('core.delete', $asset)) 
					{
						$item->params->set('access-delete', true);
					}
					// Now check if delete.own is available.
					else
					{ 
						if (!empty($user_id) AND $user->authorise('core.delete.own', $asset)) 
						{
							// Check for a valid user and that they are the owner.
							if ($user_id == $item->created_by) 
							{
								$item->params->set('access-delete', true);
							}
						}
					}
				}

				$access = $this->getState('filter.access');

				if ($access) 
				{
					// If the access filter has been set, we already have only the rsscollectors this user can view.
					$item->params->set('access-view', true);
				}
				else 
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$item->params->set('access-view', in_array($item->access, $groups));
					
				}

				// Get the tags
				$item->tags = new JHelperTags;
				$item->tags->getItemTags('com_rsscollector.rsscollector', $item->id);
			}
		}
		return $items;
	}
}