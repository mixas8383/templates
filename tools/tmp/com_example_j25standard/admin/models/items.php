<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectplural.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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
 * Methods supporting a list of item records.
 *
 */
class ExampleModelItems extends JModelList
{
	/**
	 * Model context string.  Used in setting the store id for the session
	 *
	 * @var		string
	 */
	protected $context = 'com_example.items';
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
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'publish_up', 'a.publish_up',				
				'publish_down', 'a.publish_down',	
				'featured', 'a.featured',
				'language', 'a.language', 'l.title',
				'hits', 'a.hits',
				'ordering', 'a.ordering',				
			);
		}

		parent::__construct($config);
	}
	/**
	 * Returns a reference to a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * 
	 */
	public function getTable($type = 'Items', $prefix = 'ExampleTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}	
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$category_id = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $category_id);		
		
		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);
	
		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
		
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);	
			
		$value = $this->getUserStateFromRequest($this->context . '.limit', 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $this->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
		
		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $this->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', 'a.name');
		if (!in_array($value, $this->filter_fields))
		{
			$value = 'a.name';
			$app->setUserState($this->context . '.ordercol', $value);
		}
		$this->setState('list.ordering', $value);	

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $this->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', 'asc');
		if (!in_array(strtoupper($value), array('ASC', 'DESC')))
		{
			$value = 'asc';
			$app->setUserState($this->context . '.orderdirn', $value);
		}
		$this->setState('list.direction', $value);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_example');
		$this->setState('params', $params);
	}
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * 
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.created_by');	
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.language');
		return parent::getStoreId($id);
	}	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 *
	 */
	protected function getListQuery()
	{
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required items from the table.
		$query->select(
			$this->getState(
					'list.select',
					'a.*'
			)
		);
		$query->from($db->quoteName('#__example_items').' AS a');

		// Join over the language
		$query->select($db->quoteName('l.title').' AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON '.$db->quoteName('l.lang_code').' = '.$db->quoteName('a.language'));
		
		// Join over the users for the checked out user.
		$query->select($db->quoteName('uc.name').' AS editor');
		$query->join('LEFT', $db->quoteName('#__users').' AS uc ON '.$db->quoteName('uc.id').' = '.$db->quoteName('a.checked_out'));
		
		// Join over the access levels.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', $db->quoteName('#__viewlevels').' AS ag ON '.$db->quoteName('ag.id').' = '.$db->quoteName('a.access'));
		// Join over the categories.
		$query->select($db->quoteName('c.title').' AS category_title');
		$query->join('LEFT', $db->quoteName('#__categories').' AS c ON '.$db->quoteName('c.id').' = '.$db->quoteName('a.catid'));			
		// Filter by search in name
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where($db->quoteName('a.id').' = '.(int) JString::substr($search, 3));
			}
			else
			{
				$search = $db->quote('%'.$db->escape(JString::trim($search), true).'%', false);
				$where = $db->quoteName('a.name').' LIKE '.$search;
				$where .= ' OR '.$db->quoteName('a.alias').' LIKE '.$search;
				$query->where('('.$where.')');
			}
		}
		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where($db->quoteName('a.language').' = '.$db->quote($language));
		}	

		// Filter by state e.g. published
		$state = $this->getState('filter.state');
		if (is_numeric($state))
		{
			$query->where($db->quoteName('a.state').' = '.(int) $state);
		}
		else
		{
			if ($state === '')
			{
				$query->where('('.$db->quoteName('a.state').' IN (0, 1))');
			}
		}
			
		
		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where($db->quoteName('a.access').' = ' . (int) $access);
		}
		
		
		// Filter by a single or group of categories.
		$baselevel = 1;
		$category_id = $this->getState('filter.category_id');
		if (is_numeric($category_id))
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($category_id);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where($db->quoteName('c.lft').' >= '.(int) $lft);
			$query->where($db->quoteName('c.rgt').' <= '.(int) $rgt);
		}
		else 
		{
			if (is_array($category_id))
			{
				JArrayHelper::toInteger($category_id);
				$category_id = implode(',', $category_id);
				$query->where($db->quoteName('a.catid').' IN ('.$category_id.')');
			}
		}
		// Add the list ordering clause.
		$order_col	= '';
		$order_dirn	= $this->getState('list.direction');

		
		if ($this->getState('list.ordering') == 'category_title')
		{
			$order_col = $db->quoteName('c.title').' '.$order_dirn.', '.$db->quoteName('a.ordering');
			
		}		


		if ($this->getState('list.ordering') == 'a.ordering' OR $this->getState('list.ordering') == 'ordering')
		{
			$order_col	= '';
			$order_col	.= $db->quoteName('a.ordering').' '.$order_dirn;		
			
		}
		
		if ($order_col == '' AND $this->getState('list.ordering') != '')
		{
			$order_col	=  $db->quoteName($this->getState('list.ordering')).' '.$order_dirn;
		}
		else
		{
			//sqlsrv change
			if ($order_col == 'language' OR $order_col == 'a.language')
			{
				$order_col = $db->quoteName('l.title').' '.$order_dirn;	
			}
			else
			{
				if ($order_col == 'access_level' OR $order_col =='access' OR $order_col =='a.access')
				{
					$order_col = $db->quoteName('ag.title').' '.$order_dirn;
				}
			}		
		}
		// If order column still blank then set it to default order
		if ($order_col == '')
		{
			$order_col =  $db->quoteName('a.ordering').' '.$order_dirn;
		}
			
		$query->order($db->escape($order_col));

		return $query;
	}
	/**
	 * Method to get a set of records.
	 *
	 *
	 * @return	mixed	Objects on success, false on failure.
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{
			// Include any manipulation of the data on the record e.g. expand out Registry fields
			// NB The params registry field - if used - is done automatcially in the JAdminModel parent class
			foreach ($items as $item)
			{
				// Convert the urls field to an array.
				$registry = new JRegistry;
				$registry->loadString($item->urls);
				$item->urls = $registry->toArray();
				$registry = null; //release memory	
			}
		}

		return $items;
	}	
}