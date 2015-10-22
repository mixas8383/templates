<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].site
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @version			$Id: compobjectform.php 418 2014-10-22 14:42:36Z BrianWade $
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

jimport('joomla.application.component.modelform');

class [%%ArchitectComp%%]Model[%%CompObject%%]Form extends JModelForm
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $context = '[%%com_architectcomp%%].edit.[%%compobject%%]';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('[%%compobject%%].id', $pk);

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', urldecode(base64_decode($return)));		
		[%%IF GENERATE_CATEGORIES%%]
		$this->setState('[%%compobject%%].catid', JRequest::getInt('catid'));
		[%%ENDIF GENERATE_CATEGORIES%%]
		
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

		$this->setState('layout', JRequest::getCmd('layout'));
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = '[%%CompObjectPlural%%]', $prefix = '[%%ArchitectComp%%]Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the login form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$load_data	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * 
	 */
	public function getForm($data = array(), $load_data = true)
	{
		// Get the form.
		$form = $this->loadForm('[%%com_architectcomp%%].edit.[%%compobject%%]', '[%%compobject%%]', array('control' => 'jform', 'load_data' => $load_data));
		if (empty($form))
		{
			return false;
		}
		[%%IF GENERATE_CATEGORIES%%]
		if ($id = (int) $this->getState('[%%compobject%%].id'))
		{
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
			// Existing record. Can only edit own [%%compobjectplural%%] in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}
		[%%ENDIF GENERATE_CATEGORIES%%]
		return $form;
	}

	/**
	 * Method to get [%%compobject_name%%] data.
	 *
	 * @param	integer	The id of the [%%compobject_name%%].
	 *
	 * @return	mixed	[%%CompObject_name%%] item data object on success, false on failure.
	 */
	public function getItem($item_id = null)
	{
		
		$item_id = (int) (!empty($item_id)) ? $item_id : $this->getState('[%%compobject%%].id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($item_id);

		// Check for a table object error.
		if ($return === false AND $table->getError())
		{
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');

		// Convert params field to Registry.
		$item->params = new JRegistry;
		$item->params->loadString($item->params);
		
		// Include any manipulation of the data on the record e.g. expand out Registry fields
		// NB The params registry field - if used - is done automatcially in the JAdminModel parent class
		[%%IF INCLUDE_URLS%%]
		// Convert the urls field to an array.
		$registry = new JRegistry;
		$registry->loadString($item->urls);
		$item->urls = $registry->toArray();
		$registry = null; //release memory	
		[%%ENDIF INCLUDE_URLS%%]
		[%%IF INCLUDE_DESCRIPTION%%]
			[%%IF INCLUDE_INTRO%%]
		$item->introdescription = trim($item->intro) != '' ? $item->intro . "<hr id=\"system-readmore\" />" . $item->description : $item->description;
			[%%ENDIF INCLUDE_INTRO%%]
		[%%ENDIF INCLUDE_DESCRIPTION%%]
		
		[%%FOREACH OBJECT_FIELD%%]
			[%%IF FIELD_CHECKBOXES%%]
		if ($item->[%%FIELD_CODE_NAME%%] !='')
		{
			$item->[%%FIELD_CODE_NAME%%] = explode(',',$item->[%%FIELD_CODE_NAME%%]);
		}	
			[%%ENDIF FIELD_CHECKBOXES%%]
			[%%IF FIELD_SQL%%]
		if (isset($item->[%%FIELD_CODE_NAME%%]) AND $item->[%%FIELD_CODE_NAME%%] !='')
		{
			$item->[%%FIELD_CODE_NAME%%] = explode(',',JString::trim($item->[%%FIELD_CODE_NAME%%], ','));
		}	
			[%%ENDIF FIELD_SQL%%]
			[%%IF FIELD_MULTIPLE%%]
				[%%IF FIELD_NOT_CHECKBOXES%%]
					[%%IF FIELD_NOT_SQL%%]
						[%%IF FIELD_NOT_TAG%%]
		if (isset($item->[%%FIELD_CODE_NAME%%]) AND $item->[%%FIELD_CODE_NAME%%] !='')
		{
			$item->[%%FIELD_CODE_NAME%%] = explode(',',$item->[%%FIELD_CODE_NAME%%]);
		}	
						[%%ENDIF FIELD_NOT_TAG%%]
					[%%ENDIF FIELD_NOT_SQL%%]
				[%%ENDIF FIELD_NOT_CHECKBOXES%%]
			[%%ENDIF FIELD_MULTIPLE%%]							
		[%%ENDFOR OBJECT_FIELD%%]		
		
		[%%FOREACH REGISTRY_FIELD%%]
		$registry = new JRegistry;
		$registry->loadString($item->[%%FIELD_CODE_NAME%%]);
		$item->[%%FIELD_CODE_NAME%%] = $registry->toArray();
		$registry = null; //release memory	
		
		// Check and reformat entries in the json array
		$field_array = $item->[%%FIELD_CODE_NAME%%];
		
			[%%FOREACH REGISTRY_ENTRY%%]
				[%%IF FIELD_CHECKBOXES%%]
		if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
		{
			$field_array['[%%FIELD_CODE_NAME%%]'] = explode(',',$field_array['[%%FIELD_CODE_NAME%%]']);
		}	
				[%%ENDIF FIELD_CHECKBOXES%%]
				[%%IF FIELD_SQL%%]
		if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
		{
			$field_array['[%%FIELD_CODE_NAME%%]'] = explode(',',JString::trim($field_array['[%%FIELD_CODE_NAME%%]'], ','));
		}	
				[%%ENDIF FIELD_SQL%%]
				[%%IF FIELD_MULTIPLE%%]
					[%%IF FIELD_NOT_CHECKBOXES%%]
						[%%IF FIELD_NOT_SQL%%]
							[%%IF FIELD_NOT_TAG%%]
		if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
		{
			$field_array['[%%FIELD_CODE_NAME%%]'] = explode(',',$field_array['[%%FIELD_CODE_NAME%%]']);
		}	
							[%%ENDIF FIELD_NOT_TAG%%]
						[%%ENDIF FIELD_NOT_SQL%%]
					[%%ENDIF FIELD_NOT_CHECKBOXES%%]
				[%%ENDIF FIELD_MULTIPLE%%]
			[%%ENDFOR REGISTRY_ENTRY%%]
				
		$item->[%%FIELD_CODE_NAME%%] = $field_array;			
		[%%ENDFOR REGISTRY_FIELD%%]
			
		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$user_id	= $user->get('id');

		// Get user name.
		[%%IF INCLUDE_CREATED%%]
		if ($item_id)
		{
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			// Retrieve user name(s)
			$query->select('id, name');
			$query->from('`#__users`');
			$query->where('id = '.$item->created_by);
					// Setup the query
			$db->setQuery($query->__toString());

			// Return the result
			$created_by_user = $db->loadObjectList();					
			$item->created_by_name = $created_by_user ? $created_by_user[0]->name : $item->created_by;
			
			[%%IF INCLUDE_MODIFIED%%]
			$query->clear();
			$query->select('id, name');
			$query->from('`#__users`');
			$query->where('id = '.$item->modified_by);
					// Setup the query
			$db->setQuery($query->__toString());

			// Return the result
			$modified_by_user = $db->loadObjectList();			
			$item->modified_by_name = $modified_by_user ? $modified_by_user[0]->name : $item->modified_by;
			[%%ENDIF INCLUDE_MODIFIED%%]							
		}
		else
		{
			// New item.
			$item->created_by_name = $user->name;
			[%%IF INCLUDE_MODIFIED%%]
			$item->modified_by_name = $user->name;
			[%%ENDIF INCLUDE_MODIFIED%%]					
		}
		[%%ELSE INCLUDE_CREATED%%]
			[%%IF INCLUDE_MODIFIED%%]
		if ($item_id)
		{
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);		
			// Retrieve user name(s)
			$query->clear();
			$query->select('id, name');
			$query->from('`#__users`');
			$query->where('id = '.$item->modified_by);
					// Setup the query
			$db->setQuery($query->__toString());

			// Return the result
			$modified_by_user = $db->loadObjectList();		
			$item->modified_by_name = $modified_by_user ? $modified_by_user[0]->name : $item->modified_by;
		}
		else
		{
			// New item.
			$item->modified_by_name = $user->name;
		}			
			[%%ENDIF INCLUDE_MODIFIED%%]
		[%%ENDIF INCLUDE_CREATED%%]
		
		[%%IF INCLUDE_ASSETACL%%]
			[%%IF INCLUDE_ASSETACL_RECORD%%]
		$asset		= '[%%com_architectcomp%%].[%%compobject%%].'.$item->id;
			[%%ELSE INCLUDE_ASSETACL_RECORD%%]
		$asset		= '[%%com_architectcomp%%]';
			[%%ENDIF INCLUDE_ASSETACL_RECORD%%]

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) 
		{
			$item->params->set('access-edit', true);
		}
			[%%IF INCLUDE_CREATED%%]
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
			[%%ENDIF INCLUDE_CREATED%%]
		if ($user->authorise('core.create', $asset))
		{
			$item->params->set('access-create', true);
		}	
		if ($user->authorise('core.delete', $asset)) 
		{
			$item->params->set('access-delete', true);
		}
			[%%IF INCLUDE_CREATED%%]
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
			[%%ENDIF INCLUDE_CREATED%%]			
		// Check edit state permission.
		if ($item_id)
		{
			// Existing item
			$item->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else
		{
			// New item.
			[%%IF GENERATE_CATEGORIES%%]			
			$cat_id = (int) $this->getState('[%%compobject%%].catid');
			if ($cat_id)
			{
				$item->params->set('access-change', $user->authorise('core.edit.state', '[%%com_architectcomp%%].category.'.$cat_id));
				$item->catid = $cat_id;
			}
			else 
			{
				$item->params->set('access-change', $user->authorise('core.edit.state', '[%%com_architectcomp%%]'));
			}
			[%%ELSE GENERATE_CATEGORIES%%]
			$item->params->set('access-change', $user->authorise('core.edit.state', '[%%com_architectcomp%%]'));
			[%%ENDIF GENERATE_CATEGORIES%%]			
		}
		[%%ENDIF INCLUDE_ASSETACL%%]
		return $item;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @access	public
	 * @param	object		$form		The form to validate against.
	 * @param	array		$data		The data to validate.
	 * @param   string		$group		The name of the field group to validate.
	 * 
	 * @return	mixed		Array of filtered data if valid, false otherwise.
	 */
	public function validate($form, $data, $group = null)
	{
		[%%IF INCLUDE_ASSETACL%%]
		$this->_setAccessFilters($form, $data);
		[%%ENDIF INCLUDE_ASSETACL%%]

		return parent::validate($form, $data, $group);
	}

	[%%IF INCLUDE_ASSETACL%%]
	protected function _setAccessFilters(&$form, $data)
	{
		$user = JFactory::getUser();

		if (!$user->authorise('core.edit.state', '[%%com_architectcomp%%]'))
		{
			$form->setFieldAttribute('state', 'filter', 'unset');
		}
	}
	[%%ENDIF INCLUDE_ASSETACL%%]

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * 
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('[%%com_architectcomp%%].edit.[%%compobject%%].data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 * 
	 */
	public function save($data)
	{
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$form		= $this->getForm($data, false);
		$pk			= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('[%%compobject%%].id');
		$is_new		= true;

		if (!$form)
		{
			JError::raiseError(500, $this->getError());
			return false;
		}

		// Validate the posted data.
		$data	= $this->validate($form, $data);
		if ($data === false)
		{
			return false;
		}

		// Load the row if saving an existing item.
		if ($pk > 0)
		{
			$table->load($pk);
			$is_new = false;
		}
		[%%IF INCLUDE_ASSETACL%%]
		else
		{
			// Save the default (empty) rules for the component
			$actions = JAccess::getActions('[%%com_architectcomp%%]');
			$action_array = array();
			foreach ($actions as $action)
			{
				$action_array[$action->name] = array(); 
			}
			$data['rules'] = $action_array;
		}
		[%%ENDIF INCLUDE_ASSETACL%%]
		
		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

		[%%IF INCLUDE_ORDERING%%]
		// Reorder the [%%compobject_plural_name%%] so the new [%%compobject_name%%] is first
		if (empty($table->id))
		{
			$conditions_array = $this->getReorderConditions($table);
			
			$conditions = implode(' AND ', $conditions_array);				
			$table->reorder($conditions);
		}
		[%%ENDIF INCLUDE_ORDERING%%]

		// Include the [%%architectcomp_name%%] plugins for the onSave events.
		JPluginHelper::importPlugin('[%%architectcomp%%]');

		$result = $dispatcher->trigger('on[%%CompObject%%]BeforeSave', array('[%%com_architectcomp%%].[%%compobject%%]', &$table, $is_new));

		if (in_array(false, $result, true))
		{
			JError::raiseError(500, $table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		// Clean the cache.
		$cache = JFactory::getCache('[%%com_architectcomp%%]');
		$cache->clean();

		$dispatcher->trigger('on[%%CompObject%%]AfterSave', array('[%%com_architectcomp%%].[%%compobject%%]', &$table, $is_new));

		$this->setState('[%%compobject%%].id', $table->id);

		return true;
	}

	[%%IF INCLUDE_CHECKOUT%%]
	/**
	 * Method to checkin a row.
	 *
	 * @param	integer	$pk The numeric id of a row
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	public function checkin($pk = null)
	{
		$pk	= (!empty($pk)) ? $pk : (int) $this->getState('[%%compobject%%].id');

		// Only attempt to check the row in if it exists.
		if ($pk)
		{
			$user	= JFactory::getUser();

			// Get an instance of the row to checkin.
			$table = $this->getTable();
			if (!$table->load($pk))
			{
				$this->setError($table->getError());
				return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 AND $table->checked_out != $user->get('id'))
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pk))
			{
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check-out a row for editing.
	 *
	 * @param	int		$pk	The numeric id of the row to check-out.
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	public function checkout($pk = null)
	{
		$pk		= (!empty($pk)) ? $pk : (int) $this->getState('[%%compobject%%].id');

		// Only attempt to check the row in if it exists.
		if ($pk)
		{
			// Get a row instance.
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $pk))
			{
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}
	[%%ENDIF INCLUDE_CHECKOUT%%]

	[%%IF INCLUDE_ORDERING%%]
	/**
	* A protected method to get a set of ordering conditions.
	*
	* @param	object	A record object.
	* @return	array	An array of conditions to add to add to ordering queries.
	*/
	protected function getReorderConditions($table)
	{
		$db = JFactory::getDbo();
		
		$condition = array();
		[%%IF GENERATE_CATEGORIES%%]
		$condition[] = $db->quoteName('catid').' = '.(int) $table->catid;	
		[%%ENDIF GENERATE_CATEGORIES%%]
		[%%FOREACH ORDER_FIELD%%]
			[%%IF FIELD_LINK%%]
		$condition[] = $db->quoteName('[%%FIELD_CODE_NAME%%]').' = '.(int) $table->[%%FIELD_CODE_NAME%%];	
			[%%ELSE FIELD_LINK%%]
		$condition[] = $db->quoteName('[%%FIELD_CODE_NAME%%]').' = '. $db->quote($table->[%%FIELD_CODE_NAME%%]);	
			[%%ENDIF FIELD_LINK%%]			
		[%%ENDFOR ORDER_FIELD%%]		
		[%%IF INCLUDE_STATUS%%]
		$condition[] = $db->quoteName('state').' >= 0';
		[%%ENDIF INCLUDE_STATUS%%]
		return $condition;
	}	
	[%%ENDIF INCLUDE_ORDERING%%]	
}