<?php
/**
 * @version 		$Id:$
 * @name			ModCreator (Release 1.0.0)
 * @author			 ()
 * @package			com_modcreator
 * @subpackage		com_modcreator.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: category.php 418 2014-10-22 14:42:36Z BrianWade $
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
require_once dirname(__FILE__) . '/items.php';

class ModcreatorModelCategory extends JModelList
{
	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $_item = null;

	protected $_siblings = null;

	protected $_children = null;

	protected $_parent = null;
		
	protected $_items = null;

	protected $_pagination = null;	

	/**
	 * Constructor.
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
				'modified', 'a.modified',
				'modified_by', 'a.modified_by',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'ordering', 'a.ordering'	
				);
		}
		parent::__construct($config);
	}
	/**
	 * The category that applies.
	 *
	 * @access	protected
	 * @var		object
	 */
	protected $_category = null;

	/**
	 * The list of other newfeed categories.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_categories = null;
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
		$item_id		= JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setState('category.id', $id);

		
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

		$params = $merged_params;
		
		$user = JFactory::getUser();

		// set the depth of the category query based on parameter

		if (!$params->get('show_categories_max_level'))
		{
			$show_sub_categories = $params->get('show_categories_max_level');

			if ($show_sub_categories)
			{
				$this->setState('filter.max_category_levels', $params->get('show_categories_max_level', '1'));
				$this->setState('filter.subcategories', true);
			}
		}
		
		// process show_category_noauth parameter
		if (!$params->get('show_category_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}
		
		$object_lower_case = JString::strtolower(str_replace(' ','',$params->get('items_to_display')));
		// Optional filter text

	
		if ((!$user->authorise('core.edit.state', 'com_modcreator')) AND  (!$user->authorise('core.edit', 'com_modcreator')))
		{
			// limit to published for people who can't edit or edit.state.
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}		
						
		$this->setState('list.item_filter', JRequest::getString('item-filter-search'));

		// List state information
		$order_col	= JRequest::getCmd('filter_order', 'ordering');
								

		$this->setState('list.ordering', $order_col);

		$format = JRequest::getWord('format');
		if ($format=='feed')
		{
			$limit = $app->getCfg('feed_limit');
		}
		else
		{
			$limit = $app->getUserStateFromRequest('com_modcreator.category.list.' . $item_id . '.limit', 'limit', $app->getCfg('list_limit'));
		}
		$this->setState('list.limit', $limit);
						
		$limitstart = JRequest::getUInt('limitstart', 0);
		$this->setState('list.start', $limitstart);
		
		$list_order	=  JString::strtoupper(JRequest::getCmd('filter_order_Dir', 'ASC'));
		if (!in_array($list_order, array('ASC', 'DESC', '')))
		{
			$list_order = 'ASC';
		}		
		$this->setState('list.direction', $list_order);
		
		$this->setState('filter.language', $app->getLanguageFilter());			

		$this->setState('layout', JRequest::getCmd('layout'));
		
		switch ($params->get('items_to_display','None'))
		{
			case 'Items' :
				$object_lower_case = 'item_';
				if ($params->get('filter_'.$object_lower_case.'featured') <> "")
				{
					$this->setState('filterfeatured', $params->get('filter_'.$object_lower_case.'featured'));
					
				}
				if ($params->get('filter_'.$object_lower_case.'archived'))
				{
					$this->setState('filter.archived', 2);
					
				}
				// process noauth parameter
				if (!$params->get('show_'.$object_lower_case.'noauth'))
				{
					$this->setState('filter.access', true);
				}
				else
				{
					$this->setState('filter.access', false);
				}
				break;
			default :
				$object_lower_case = '';
				$this->setState('filter.access', true);
				
				break;
		}

	}
	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
	public function getItems()
	{

		$params = $this->getState()->get('params');
		$limit = $this->getState('list.limit');
		if ($params->get('items_to_display') AND $params->get('items_to_display') !='None')
		{
			$object_upper_case = str_replace(' ','',JString::ucwords($params->get('items_to_display')));	
			
			if ($this->_items === null AND $category = $this->getCategory())
			{
				$model = JModel::getInstance($object_upper_case, 'ModcreatorModel', array('ignore_request' => true));
				$model->setState('params',  $params);
				$model->setState('filter.category_id', $category->id);
				$model->setState('filter.published', $this->getState('filter.published'));
				$model->setState('filter.archived', $this->getState('filter.archived'));			
				$model->setState('filter.featured', $this->getState('filter.featured'));
				$model->setState('filter.language', $this->getState('filter.language'));
				$model->setState('filter.access', $this->getState('filter.access'));
				$model->setState('list.ordering', $this->_buildOrderBy());
				$model->setState('list.start', $this->getState('list.start'));
				$model->setState('list.limit', $limit);
				$model->setState('list.direction', $this->getState('list.direction'));
				$model->setState('list.filter', $this->getState('list.filter'));
				$model->setState('filter.subcategories', $this->getState('filter.subcategories'));
				$model->setState('filter.max_category_levels', $this->setState('filter.max_category_levels'));
				$model->setState('list.links', $this->getState('list.links'));

				if ($limit >= 0)
				{
					$this->_items = $model->getItems();

					if ($this->_items === false)
					{
						$this->setError($model->getError());
					}
				}
				else
				{
					$this->_items=array();
				}

				$this->_pagination = $model->getPagination();
			}

			// Convert the params field into an object, saving original in _params
			for ($i = 0, $n = count($this->_items); $i < $n; $i++)
			{
				$item = &$this->_items[$i];
				if (!isset($item->params))
				{
					$params = new JRegistry();
					$params->loadString($item->params);
					$this->$item->params = $params;
				}
			}
		}
		return $this->_items;	
	}
	/**
	 * Build the orderby for the query
	 *
	 * @return	string	$order_by portion of query
	 * 
	 */
	protected function _buildOrderBy()
	{
		$app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$params		= $this->state->params;
		$item_id		= JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
		$order_col	= $app->getUserStateFromRequest('com_modcreator.category.list.' . $item_id . '.filter_order', 'filter_order', '', 'string');
		$order_dirn	= $app->getUserStateFromRequest('com_modcreator.category.list.' . $item_id . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$order_by	= ' ';

		if (!in_array($order_col, $this->filter_fields))
		{
			$order_col = null;
		}

		if (!in_array(JString::strtoupper($order_dirn), array('ASC', 'DESC', '')))
		{
			$order_dirn = 'ASC';
		}

		if ($order_col AND $order_dirn)
		{
			$order_by .= $db->escape($order_col) . ' ' . $db->escape($order_dirn) . ', ';
		}
		switch ($params->get('items_to_display','None'))
		{
			case 'Items' :
				$secondary_order_by	= $params->get('item_orderby_sec', 'none');
				$order_date			= $params->get('item_order_date');
				$category_order_by	= $params->def('item_orderby_pri', '');
				
				$primary			= ModcreatorHelperQuery::orderbyPrimary($category_order_by);	
				$secondary			= ModcreatorHelperQuery::orderbySecondary($secondary_order_by, $order_date, 'ordering') . ', ';
				
				$order_by			.= $db->escape($primary).' '. $db->escape($secondary).' a.created ';
				break;
			default :
				$category_order_by	= 'ordering';
				$primary			= ModcreatorHelperQuery::orderbyPrimary($category_order_by);
				$order_by			.= $db->escape($primary);
								
			break;
		}
		
		return $order_by;
	}

	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			return null;
		}
		return $this->_pagination;
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @param	int		An optional ID
	 *
	 * @return	object
	 * 
	 */
	public function getCategory()
	{
		if(!is_object($this->_item))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry();
			$params->loadString($active->params);
			$options = array();

			$options['access'] = $this->getState('filter.access');
			$options['published'] = $this->getState('filter.published');
			$options['countItems'] = true;
			
			if ($params->get('items_to_display') AND $params->get('items_to_display') !='None')
			{
				$options['table'] = '#__modcreator_'.JString::strtolower(str_replace(' ','',$params->get('items_to_display')));
			}
			else
			{
				$options['table'] = '';
			}
															
			$categories = JCategories::getInstance('Modcreator', $options);
			$this->_item = $categories->get($this->getState('category.id', 'root'));
			if(is_object($this->_item))
			{
				$user	= JFactory::getUser();
				$user_id	= $user->get('id');
				$asset	= 'com_modcreator.category.'.$this->_item->id;

				// Check general create permission.
				if ($user->authorise('core.create', $asset))
				{
					$this->_item->getParams()->set('access-create', true);
				}			
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;
				if($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}
				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else
			{
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
	}

	/**
	 * Get the parent category.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 */
	public function getParent()
	{
		if(!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_parent;
	}

	/**
	 * Get the sibling (adjacent) categories.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 */
	function &getLeftSibling()
	{
		if(!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_leftsibling;
	}

	function &getRightSibling()
	{
		if(!is_object($this->_item))
		{
			$this->getCategory();
		}
		return $this->_rightsibling;
	}

	/**
	 * Get the child categories.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 */
	function &getChildren()
	{
		if(!is_object($this->_item))
		{
			$this->getCategory();
		}
		
		// Order subcategories
		if (count($this->_children))
		{
			$params = $this->getState()->get('params');
			if ($params->get('orderby_pri') == 'alpha' OR $params->get('orderby_pri') == 'ralpha')
			{
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}		
		return $this->_children;
	}
}

