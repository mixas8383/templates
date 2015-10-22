<?php
/**
 * @version 		$Id:$
 * @name			Simplepoll (Release 1.0.0)
 * @author			 ()
 * @package			com_simplepoll
 * @subpackage		com_simplepoll.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: categories.php 408 2014-10-19 18:31:00Z BrianWade $
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
 * This models supports retrieving lists of simplepoll categories.
 *
 */
class SimplepollModelCategories extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_simplepoll.categories';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_simplepoll';

	private $_parent = null;

	private $_children = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param	string		$ordering	Field used for order by clause
	 * @param	string		$direction	Direction of order
	 * 	
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parent_id = $app->input->getInt('id');
		$this->setState('filter.parentId', $parent_id);

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
		
		$this->setState('filter.published',	1);
		$this->setState('filter.language',	$app->getLanguageFilter());
	
		// process show_category_noauth parameter
		if (!$params->get('show_category_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}
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
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');
		$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Get the child sub-categories for a category
	 *
	 * @return mixed An array of data items on success, false on failure.
	 */
	public function getChildren()
	{
		if(!count($this->_children))
		{
			$params = $this->getState()->get('params');
			
			$options = array();
			
			$options['access'] = $this->getState('filter.access');
			$options['published'] = $this->getState('filter.published');
			$options['countItems'] = true;
			
			if ($params->get('items_to_display') AND $params->get('items_to_display') !='')
			{
				$options['table'] = '#__simplepoll_'.JString::strtolower(str_replace(' ','',$params->get('items_to_display')));
			}
			else
			{
				$options['table'] = '';
			}
							
			$categories = JCategories::getInstance('Simplepoll', $options);
			$this->_parent = $categories->get($this->getState('filter.parentId', 'root'));
			if(is_object($this->_parent))
			{
				$this->_children = $this->_parent->getChildren();
			}
			else
			{
				$this->_children = false;
			}
		}

		return $this->_children;
	}

	public function getParent()
	{
		if(!is_object($this->_parent))
		{
			$this->getChildren();
		}
		return $this->_parent;
	}
}
