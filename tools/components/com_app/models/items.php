<?php
/**
 * @version 		$Id:$
 * @name			App (Release 1.0.0)
 * @author			 ()
 * @package			com_app
 * @subpackage		com_app.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectplural.php 424 2014-10-23 14:08:27Z BrianWade $
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

jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of items.
 *
 */
class AppModelItems extends JModelList
{
	/**
	 * Model context string.  Used in setting the store id for the session
	 *
	 * @var		string
	 */
	protected $context = 'com_app.items';

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
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
				'catid', 'a.catid', 'category_title',
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
				'rating',
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
		
		$item_id = JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);

		// Check to see if a single item has been specified either as a parameter or in the url Request
		$pk = $params->get('item_id', '') == '' ? JRequest::getInt('id', '') : $params->get('item_id');
		$this->setState('filter.item_id', $pk);
		
		// List state information
		if (JRequest::getCmd('layout', 'default') == 'blog')
		{
			$limit = $params->def('item_num_leading', 1) + $params->def('item_num_intro', 4) + $params->def('item_num_links', 4);
		}
		else
		{		
			$limit = $app->getUserStateFromRequest($this->context.'.list.' . $item_id . '.limit', 'limit', $params->get('item_num_per_page'),'integer');
		}
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context.'.limitstart','limitstart',0,'integer');
		$this->setState('list.start', $value);

		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$category_id = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $category_id);		

		$order_col = $app->getUserStateFromRequest($this->context. '.filter_order', 'filter_order', $params->get('item_initial_sort','a.ordering'), 'string');
		if (!in_array($order_col, $this->filter_fields))
		{
			$order_col = $params->get('item_initial_sort','a.ordering');
		}

		$this->setState('list.ordering', $order_col);

		$list_order = $app->getUserStateFromRequest($this->context. '.filter_order_Dir', 'filter_order_Dir',  $params->get('item_initial_direction','ASC'), 'cmd');
		if (!in_array(JString::strtoupper($list_order), array('ASC', 'DESC', '')))
		{
			$list_order =  $params->get('item_initial_direction','ASC');
		}
		$this->setState('list.direction', $list_order);
		
				
		if ((!$user->authorise('core.edit.state', 'com_app')) AND  (!$user->authorise('core.edit', 'com_app')))
		{
			// filter on status of published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}		

		$this->setState('filter.language',$app->getLanguageFilter());
		
		// process show_item_noauth parameter
		if (!$params->get('show_item_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}
		// check for category selection
		if ($params->get('filter_item_categories') AND implode(',', $params->get('filter_item_categories'))  == true)
		{
			$selected_categories = $params->get('filter_item_categories');
			$this->setState('filter.category_id', $selected_categories);
		}			
		if ($params->get('filter_item_featured') <> "")
		{
			$this->setState('filter.featured', $params->get('filter_item_featured'));
			
		}
		if ($params->get('filter_item_archived'))
		{
			$this->setState('filter.archived', $params->get('filter_item_archived'));
			
		}
		$this->setState('layout', JRequest::getCmd('layout'));
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
		$id .= ':'.serialize($this->getState('filter.category_id'));
		$id .= ':'.serialize($this->getState('filter.category_id.include'));
		$id .= ':'.$this->getState('filter.created_by_id');
		$id .= ':'.$this->getState('filter.created_by_id.include');
		$id .= ':'.$this->getState('filter.created_by_name');
		$id .= ':'.$this->getState('filter.created_by_name.include');	
		$id .= ':'.$this->getState('filter.item_id');
		$id .= ':'.$this->getState('filter.item_id.include');				
		

		return parent::getStoreId($id);
	}

	/**
	 * Get the main query for retrieving a list of items subject to the model state.
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

		// Select the required fields from the table.
		$query->select(
			$this->getState(
					'list.select',
					'a.*'
					)
				);


		$query->from($db->quoteName('#__app_items').' AS a');
		// Join over the categories.
		$query->select($db->quoteName('c.title').' AS category_title, '.
						$db->quoteName('c.alias').' AS category_alias, '.	
						$db->quoteName('c.access').' AS category_access, '.
						$db->quoteName('c.path').' AS category_route'
		);
		$query->join('LEFT', $db->quoteName('#__categories').' AS c ON '.$db->quoteName('c.id').' = '.$db->quoteName('a.catid'));
		// Join over the categories to get parent category titles
		$query->select($db->quoteName('parent.title').' AS parent_title, '.
						$db->quoteName('parent.id').' AS parent_id, '.
						$db->quoteName('parent.alias').' AS parent_alias, '.
						$db->quoteName('parent.path').' AS parent_route'
		);
		$query->join('LEFT', $db->quoteName('#__categories').' as parent ON '.$db->quoteName('parent.id').' = '.$db->quoteName('c.parent_id'));


		$query->select($db->quoteName('ua.name').' AS created_by_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS ua on '.$db->quoteName('ua.id').' = '.$db->quoteName('a.created_by'));

		$query->select($db->quoteName('uam.name').' AS modified_by_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS uam on '.$db->quoteName('uam.id').' = '.$db->quoteName('a.modified_by'));
		
		// Get contact id
		$sub_query = $db->getQuery(true);
		$sub_query->select('MAX(contact.id) AS id');
		$sub_query->from('#__contact_details AS contact');
		$sub_query->where('contact.published = 1');
		$sub_query->where('contact.user_id = a.created_by');

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$sub_query->where('(contact.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ') OR contact.language IS NULL)');
		}

		$query->select('(' . $sub_query . ') as contactid');
		
		// Join on vote rating table
		$query->select('ROUND('.$db->quoteName('v.rating_sum').' / '.$db->quoteName('v.rating_count').', 0) AS rating, '.$db->quoteName('v.rating_count').' as rating_count');
		$query->join('LEFT', $db->quoteName('#__app_rating').' AS v ON '.$db->quoteName('a.id').' = '.$db->quoteName('v.content_id').' AND '.$db->quoteName('v.content_type').' = '.$db->quote('items'));
		
		if ((!$user->authorise('core.edit.state', 'com_app')) AND (!$user->authorise('core.edit', 'com_app')))
		{
			//  Do not show unless today's date is within the publish up and down dates (or they are empty)
			$null_date = $db->quote($db->getNullDate());
			$now_date = $db->quote(JFactory::getDate()->toSQL());	
			$query->where('('.$db->quoteName('a.publish_up').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_up').' <= ' . $now_date . ')');
			$query->where('('.$db->quoteName('a.publish_down').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_down').' >= ' . $now_date . ')');
		}
		
		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where($db->quoteName('a.access').' IN ('.$groups.')');
			$query->where($db->quoteName('c.access').' IN ('.$groups.')');
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
					// Use item state 
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
		
	

		// Filter by a single or group of items.
		$item_id = $this->getState('filter.item_id');
		if ($item_id != '')
		{
			if (is_numeric($item_id))
			{
				$type = $this->getState('filter.item_id.include', true) ? '= ' : '<> ';
				$query->where($db->quoteName('a.id').' '.$type.(int) $item_id);
			}
			else
			{
				if (is_array($item_id))
				{
					JArrayHelper::toInteger($item_id);
					$item_id = implode(',', $item_id);
					$type = $this->getState('filter.item_id.include', true) ? 'IN' : 'NOT IN';
					$query->where($db->quoteName('a.id').' '.$type.' ('.$item_id.')');
				}
			}
		}
		// Filter by a single or group of categories
		$category_id = $this->getState('filter.category_id');

		if (is_numeric($category_id))
		{
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$include_sub_categories = $this->getState('filter.subcategories', false);
			$category_equals = $db->quoteName('a.catid').' '.$type.(int) $category_id;

			if ($include_sub_categories)
			{
				$levels = (int) $this->getState('filter.max_category_levels', '1');
				// Create a subquery for the subcategory list
				$sub_query = $db->getQuery(true);
				$sub_query->select($db->quoteName('sub.id'));
				$sub_query->from($db->quoteName('#__categories').' as sub');
				$sub_query->join('INNER', $db->quoteName('#__categories').' as this ON '.$db->quoteName('sub.lft').' > '.$db->quoteName('this.lft').' AND '.$db->quoteName('sub.rgt').' < '.$db->quoteName('this.rgt'));
				$sub_query->where($db->quoteName('this.id').' = '.(int) $category_id);
				$sub_query->where($db->quoteName('sub.level').' <= '.$db->quoteName('this.level').' + '.$levels);

				// Add the subquery to the main query
				$query->where('('.$category_equals.' OR '.$db->quoteName('a.catid').' IN ('.$sub_query->__toString().'))');
			}
			else
			{
				$query->where($category_equals);
			}
		}
		else
		{
			if (is_array($category_id) AND (count($category_id) > 0))
			{
				JArrayHelper::toInteger($category_id);
				$category_id = implode(',', $category_id);
				if (!empty($category_id))
				{
					$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
					$query->where($db->quoteName('a.catid').' '.$type.' ('.$category_id.')');
				}
			}
		}
		
		// Filter by creator
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
		
		// Filter by creator_name
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

		if ((is_object($params)) AND ($params->get('item_filter_field') != 'hide') AND ($filter = $this->getState('filter.search')))
		{
			// clean filter variable
			$filter = JString::strtolower($filter);
			$hits_filter = intval($filter);
			$filter = $db->quote('%'.$db->escape($filter, true).'%', false);

			switch ($params->get('item_filter_field'))
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
			$query->where('('.$db->quoteName('contact.language').' IN ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR '.$db->quoteName('contact.language').' IS NULL)');
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

		
			if ($this->state->get('list.ordering') == 'category_title')
			{
				$order_col = $db->quoteName('c.title').' '.$order_dirn.', '.$db->quoteName('a.ordering');
			}		


			if ($this->state->get('list.ordering') == 'a.ordering' OR $this->state->get('list.ordering') == 'ordering')
			{
				$order_col	= '';
				$order_col	.= $db->quoteName('a.ordering').' '.$order_dirn;		
			}

			if ($order_col == '')
			{
				$order_col = is_string($this->getState('list.ordering')) ? $this->getState('list.ordering') : 'a.ordering';
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
	 * Method to get a list of items.
	 *
	 * Overriden to inject convert the params field into a JParameter object.
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
		$global_params = JComponentHelper::getParams('com_app', true);

		if ($items = parent::getItems())
		{
			// Convert the parameter fields into objects.
			foreach ($items as &$item)
			{
				$query->clear();
				$item_params = new JRegistry;
				$item_params->loadString($item->params);
				// Convert the urls field to an array.
				$registry = new JRegistry;
				$registry->loadString($item->urls);
				$item->urls = $registry->toArray();
				$registry = null; //release memory	
				
		
				$item->introdescription = trim($item->intro) != '' ? $item->intro . $item->description : $item->description;

				
				// Unpack readmore and layout params
				$item->item_alternative_readmore = $item_params->get('item_alternative_readmore');
				$item->layout = $item_params->get('layout');
							
				if (!is_object($this->getState('params')))
				{
					$item->params = $item_params;
				}
				else
				{
					$item->params = clone $this->getState('params');

					// Item params override menu item params only if menu param = 'use_item'
					// Otherwise, menu item params control the layout
					// If menu item is 'use_item' and there is no item param, use global

					// create an array of just the params set to 'use_item'
					$menu_params_array = $this->getState('params')->toArray();
					$item_array = array();

					foreach ($menu_params_array as $key => $value)
					{
						if ($value === 'use_item')
						{
							// if the item has a value, use it
							if ($item_params->get($key) != '')
							{
								// get the value from the item
								$item_array[$key] = $item_params->get($key);
							}
							else
							{
								// otherwise, use the global value
								$item_array[$key] = $global_params->get($key);
							}
						}
					}

					// merge the selected item params
					if (count($item_array) > 0)
					{
						$item_params = new JRegistry;
						$item_params->loadArray($item_array);
						$item->params->merge($item_params);
					}


					// get display date
					switch ($item->params->get('list_show_item_date'))
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
				// Technically guest could edit an item, but lets not check that to improve performance a little.
				if (!$guest) 
				{
					$asset		= 'com_app.item.'.$item->id;
					
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
					// If the access filter has been set, we already have only the items this user can view.
					$item->params->set('access-view', true);
				}
				else 
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					if ($item->catid != 0  AND $item->category_access !== null)
					{ 
						$item->params->set('access-view', in_array($item->access, $groups) AND in_array($item->category_access, $groups));
					}
					else 
					{
						$item->params->set('access-view', in_array($item->access, $groups));
					}							
					
				}
			}
		}

		return $items;
	}
}
