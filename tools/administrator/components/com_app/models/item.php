<?php
/**
 * @version 		$Id:$
 * @name			App (Release 1.0.0)
 * @author			 ()
 * @package			com_app
 * @subpackage		com_app.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobject.php 418 2014-10-22 14:42:36Z BrianWade $
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

jimport('joomla.application.component.modeladmin');

/**
 * Item model.
 *
 */
class AppModelItem extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_APP_ITEMS';
	/**
	 * @var		string	The context for the app call.
	 */
	protected $context = 'com_app.items';	
	/**
	 * @var		string	The event to trigger after before the data.
	 */
	protected $event_before_save = 'onItemBeforeSave';
	/**
	 * @var		string	The event to trigger after saving the data.
	 */
	protected $event_after_save = 'onItemAfterSave';

	/**
	 * @var    string	The event to trigger before deleting the data.
	 */
	protected $event_before_delete = 'onItemBeforeDelete';	
	/**
	 * @var    string	The event to trigger after deleting the data.
	 */
	protected $event_after_delete = 'onItemAfterDelete';	
	/**
	 * @var    string	The event to trigger after changing the data's state field.
	 */
	protected $event_change_state = 'onItemChangeState';	

	/**	
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	record	A record object.
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
	
		if ($record->state != -2)
		{
			return ;
		}
		if (!empty($record->id))
		{
			return $user->authorise('core.delete', 'com_app.item.'.(int) $record->id);
		}
		else
		{
			return $user->authorise('core.delete', 'com_app');
		}							
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check against the id.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_app.item.'.(int) $record->id);
		}
		else
		{
			// New item, so check against the category.		
			if (!empty($record->catid))
			{
				return $user->authorise('core.edit.state', 'com_app.category.'.(int) $record->catid);
			}
			else 
			{
			// Default to component settings.			
				return parent::canEditState($record);
			}
		}
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Items', $prefix = 'AppTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Include any manipulation of the data on the record e.g. expand out Registry fields
			// NB The params registry field - if used - is done automatically in the JAdminModel parent class
			
						

			// Convert the urls field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->urls);
			$item->urls = $registry->toArray();
			
			$item->introdescription = trim($item->intro) != '' ? $item->intro . "<hr id=\"system-readmore\" />" . $item->description : $item->description;
		}

		return $item;
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$load_data	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $load_data = true)
	{
		$form = $this->loadForm('com_app.edit.item', 'item', array('control' => 'jform', 'load_data' => $load_data));
		if (empty($form))
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id =  $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id =  $jinput->get('id', 0);
		}		
		// Determine correct permissions to check.
		if ($this->getState('item.id'))
		{
			$id = $this->getState('item.id');		
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
			// Existing record. Can only edit own items in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}
		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.

			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');			

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('featured', 'filter', 'unset');	
			$form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('language', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');			
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_app.edit.item.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('item.id') == 0)
			{
				$app = JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_app.items.filter.category_id')));
			}
		}

		return $data;
	}
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param	JTable	$table
	 *
	 * @return	void
	 */
	protected function prepareTable(&$table)
	{
		$db = $this->getDbo();		
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
		$table->alias = $this->generateUniqueAlias(JApplication::stringURLSafe($table->alias));

		if (empty($table->alias))
		{
			$table->alias = $this->generateUniqueAlias(JApplication::stringURLSafe($table->name));
		}
		// Set the publish date to now
		if($table->state == 1 )
		{
			if (intval($table->publish_up) == 0)
			{
				$table->publish_up = JFactory::getDate()->toSQL();
			} 
			if (intval($table->publish_down) == 0)
			{
				$table->publish_down = $db->getNullDate();
			}				
		}

		if (empty($table->id) OR $table->id == 0)
		{
			// Set ordering to the last item if not set
			if (empty($table->ordering) OR $table->ordering == 0)
			{
				$conditions_array = $this->getReorderConditions($table);
				
				$conditions = implode(' AND ', $conditions_array);				
				$table->reorder($conditions);
			}
		}
	}
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 */
	public function save($data)
	{
		// Include the app plugins for the onSave events.
		JPluginHelper::importPlugin('app');	
		
		return parent::save($data);
	}	
	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 */
	public function publish(&$pks, $value = 1)
	{	
		// Include the app plugins for the change of state event.
		JPluginHelper::importPlugin('app');	
		
		return parent::publish($pks, $value);
	}
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 */
	public function delete(&$pks)
	{
		// Include the app plugins for the delete events.
		JPluginHelper::importPlugin('app');	
		
		return parent::delete($pks);	
	}		

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to add to ordering queries.
	 */
	protected function getReorderConditions($table = null)
	{
		$db = JFactory::getDbo();
	
		$condition = array();
		$condition[] = $db->quoteName('catid').' = '.(int) $table->catid;	
		$condition[] = $db->quoteName('state').' >= 0';
		return $condition;
	}


	/**
	 * Method to toggle the featured setting of items.
	 *
	 * @param	array	$pks	The ids of the items to toggle.
	 * @param	int		$value	The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_APP_ITEMS_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();

			$db->setQuery(
				'UPDATE '.$db->quoteName('#__app_items') .
				' SET '.$db->quoteName('featured').' = '.(int) $value.
				' WHERE '.$db->quoteName('id').' IN ('.implode(',', $pks).')'
			);
			if (!$db->query())
			{
				throw new Exception($db->getErrorMsg());
			}

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$conditions_array = $this->getReorderConditions($table);
		
		$conditions = implode(' AND ', $conditions_array);				
		$table->reorder($conditions);

		// Clean component's cache
		$this->cleanCache();

		return true;
	}
	/**
	 * Custom clean the cache of com_app and app modules
	 *
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_app');
		parent::cleanCache('mod_app');
		parent::cleanCache('mod_app_items');

	}
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;
		
		//Check box selected to copy items and then apply changes
		if (!empty($commands['copy_items']) AND $commands['copy_items'] == '1')
		{
			$result = $this->batchCopy(0, $pks, $contexts);
			if (is_array($result))
			{
				$pks = $result;
				// Build a new array of item contexts for the copied items
				$contexts = array();
				foreach ($pks as $pk)
				{
					$contexts[$pk] = $this->context . '.' . $pk;
				}					
			}
			else
			{
				return false;
			}
			$done = true;			
		}

		if (!empty($commands['category_id']))
		{
			if (!$this->batchCategory($commands['category_id'], $pks, $contexts))
			{
				return false;
			}
			$done = true;
		}

		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}
	
		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}
		
	/**
	 * Batch copy items .
	 * 
	 * @param   integer  $value     Dummy to match the category in the JModelAdmin calls.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$table = $this->getTable();
		$i = 0;
		$new_ids = array();
		
		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}
			
			// Reset the ID because we are making a copy
			$table->id = 0;
			
			// Reset hits because we are making a copy
			$this->table->hits = 0;
			
			$table->alias = $this->generateUniqueAlias($table->alias);			
			$table->name = $this->generateUniqueName(htmlspecialchars_decode($table->name, ENT_QUOTES));
			// Set ordering to 0 so it is forced to be set later to last position
			$table->ordering = 0;
			$this->prepareTable($table);


			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Get the new item ID
			$new_id = $table->get('id');

			// Add the new ID to the array
			$new_ids[$i]	= $new_id;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $new_ids;
	}
	
	/**
	 * Batch Category changes for a group of rows.
	 *
	 * @param   string  $value     The new value matching a language.
	 * @param   array   $pks       An array of row IDs.
	 * @param   array   $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 */
	protected function batchCategory($value, $pks, $contexts)
	{
	
		// Set the variables
		$category_id = (int) $value;
		
		$user	= JFactory::getUser();
		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($user->authorise('core.edit', $contexts[$pk]))
			{
				$table->reset();
				$table->load($pk);
				$table->catid = $category_id;

				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}
			}
			else
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}	
	
	
	/**
	* Method to get a unique name.
	*
	* @param   string   $name	The name.
	*
	* @return	string  $name	The modified name.
	*
	*/
	protected function generateUniqueName($name)
	{
		// Alter the name & alias
		$table = $this->getTable();
		while ($table->load(array('name' => $name)))
		{
			$name = JString::increment($name);
		}
		
		return $name;
	}
	/**
	 * Method to get a unique alias.
	 *
	 * @param   string  $alias		The alias.
	 *
	 * @return	string  $alias		The modified alias.
	 *
	 */
	protected function generateUniqueAlias($alias)
	{
		// Alter the name & alias
		$table = $this->getTable();
		$alias = JApplication::stringURLSafe($alias);
		
		while ($table->load(array('alias' => $alias)))
		{
			$alias = JString::increment($alias, 'dash');
		}

		return $alias;
	}
}