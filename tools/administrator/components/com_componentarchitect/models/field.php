<?php
/**
 * @version 		$Id: field.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobject.php 806 2013-12-24 13:24:16Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
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
if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.modeladmin');
}	

/**
 * Field model.
 *
 */
class ComponentArchitectModelField extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_COMPONENTARCHITECT_FIELDS';
	/**
	 * @var      string	The type alias for this object (for example, 'com_componentarchitect.field')
	 */
	public $typeAlias = 'com_componentarchitect.field';	

	/**
	 * @var		string	The context for the app call.
	 */
	protected $context = 'com_componentarchitect.fields';
	
	/**
	 * @var		string	The event to trigger after before the data.
	 */
	protected $event_before_save = 'onFieldBeforeSave';
	/**
	 * @var		string	The event to trigger after saving the data.
	 */
	protected $event_after_save = 'onFieldAfterSave';

	/**
	 * @var    string	The event to trigger before deleting the data.
	 */
	protected $event_before_delete = 'onFieldBeforeDelete';	
	/**
	 * @var    string	The event to trigger after deleting the data.
	 */
	protected $event_after_delete = 'onFieldAfterDelete';	


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * 
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Fields', $prefix = 'ComponentArchitectTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Extension to the core method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		
		parent::populateState();
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams($this->option);	
	
	}
	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 * @param	boolean		Get recursively item children - true or false
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null, $recursive = false)
	{
		if ($item = parent::getItem($pk))
		{
			// Include any manipulation of the data on the record e.g. expand out Registry fields, convert checkboxes
			// NB The params registry field - if used - is done automatically in the JAdminModel parent class

			//[%%START_CUSTOM_CODE%%]
			$option_values = array('values' => array(), 'labels' => array());
			if ($item->option_values != '')
			{			
				// Make sure Unix eol is in field
				$item->option_values = str_replace("\r\n", "\n", $item->option_values);
				$options_array = explode("\n",$item->option_values);

				for ($i = 0; $i <count($options_array); $i++)
				{
					$option = explode(":",$options_array[$i]);
					$option_values['values'][] = $option[0];
					$option_values['labels'][] = $option[1];
				}
			}
			$item->option_values =  $option_values;			
			//[%%END_CUSTOM_CODE%%]			
 
 			if ($recursive)
			{
				// If parent child object relationship created then children object functions will be here
				// If no children object then the logic is left for use in recursively getting other linked data
			
			}				
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
		$form = $this->loadForm('com_componentarchitect.edit.field', 'field', array('control' => 'jform', 'load_data' => $load_data));
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

		
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		$app = JFactory::getApplication();
		// Check the session for previously entered form data.
		$data = $app->getUserState('com_componentarchitect.edit.field.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('field.id') == 0)
			{
			}
		}
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$this->preprocessData('com_componentarchitect.field', $data);
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
	protected function prepareTable($table)
	{
		$db = $this->getDbo();		
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

		//[%%START_CUSTOM_CODE%%]
		$table->code_name = str_replace('-', '_', JApplication::stringURLSafe($table->code_name));
		if (empty($table->code_name))
		{
			$table->code_name = $this->generateUniqueCodeName((array) $table);
		}
		//[%%END_CUSTOM_CODE%%]					

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
	 */
	public function save($data)
	{
		// Include the component architect plugins for the onSave events.
		JPluginHelper::importPlugin('componentarchitect');	
		
		$app = JFactory::getApplication();
		$table = $this->getTable();

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : JRequest::getInt('id');



		// Alter values for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			$data['name'] = $this->generateUniqueName($data);
		}

		if (parent::save($data))
		{
			$new_pk = (int) $this->getState($this->getName() . '.id');

			if ($app->input->get('task') == 'save2copy')
			{
				// Reorder table so that new record has a unique ordering value
				$table->load($new_pk);
				$conditions_array = $this->getReorderConditions($table);
				$conditions = implode(' AND ', $conditions_array);				
				$table->reorder($conditions);
			}
			
			return true;
		}

		return false;
	}	
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	public function delete(&$pks)
	{
		// Include the componentarchitect plugins for the delete events.
		JPluginHelper::importPlugin('componentarchitect');	
		
		//[%%START_CUSTOM_CODE%%]
		// Check to prevent delete of pre-defined fields
		$ok_pks = $this->check_predefined($pks);
		if (count($pks) > count($ok_pks))
		{
			if (count($ok_pks) == 0)
			{
				$this->setError(JText::_($this->text_prefix.'_ERROR_PREDEFINED_FIELD_CANNOT_DELETE'));
			}
			else
			{
				$result = parent::delete($ok_pks);
				
				if ($result)
				{
					$this->setError(JText::plural($this->text_prefix.'_ERROR_PREDEFINED_FIELD_CANNOT_DELETE_N_ITEMS_DELETED', count($ok_pks)));				
				}				
			}	
			return false;
		}
		else
		{
			return parent::delete($pks);	
		}
		//[%%END_CUSTOM_CODE%%]		
	}		

	/**
	 * Copy items .
	 * @param   array    $pks       An array of row IDs.
	 * @param   array	 $parents	An array of parent ids.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 */
	public function copy(&$pks, $parents = array())
	{
		$user	= JFactory::getUser();
		$new_pks = array();
		$dispatcher = JEventDispatcher::getInstance();
		// Include the component architect plugins for the onSave events.
		JPluginHelper::importPlugin('componentarchitect');	
		
		if (!$user->authorise('core.create', $this->context))
		{
			return false;
		}	
		$table = $this->getTable();
		$i = 0;
		
		// Process the ids
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

			// if this is not from a cascade copy from a parent object i.e. check the view is fields then set the name and then all other name variables to blank to regenerate
			$view = JRequest::getCmd('view');
			if ($view == 'fields')
			{
				$table->name = $this->generateUniqueName((array) $table);
				// Set ordering to 0 so it is forced to be set later to last position
				$table->ordering = 0;
				//[%%START_CUSTOM_CODE%%]
				$table->code_name = '';
				$table->predefined_field = 0;
				//[%%END_CUSTOM_CODE%%]
			}					
			
			if (count($parents) > 0)
			{		
				// Modify each parent key in the heirarchy
				foreach ($parents as $parent)
				{
					if (isset($table->$parent['fk_field']))
					{
						$table->$parent['fk_field'] = $parent['value'];
					}
				}
			}	

			$this->prepareTable($table);

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onFieldBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, true));

			if (in_array(false, $result, true))
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

			// Trigger the onFieldAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, true));

			// Get the new item ID
			$new_pk = $table->get('id');

			// Add the new ID to the array
			$new_pks[$i]	= $new_pk;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $new_pks;
	}	
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
		$condition[] = $db->quoteName('component_id').' = '.(int) $table->component_id;	
		$condition[] = $db->quoteName('component_object_id').' = '.(int) $table->component_object_id;	
		$condition[] = $db->quoteName('fieldset_id').' = '.(int) $table->fieldset_id;	
		$condition[] = $db->quoteName('foreign_object_id').' = '.(int) $table->foreign_object_id;	
		return $condition;
	}


	/**
	 * Custom clean the cache of com_componentarchitect and componentarchitect modules
	 *
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_componentarchitect');

	}
	/**
	* Method to get a unique name.
	*
	* @param   array   $data	The data where the original name is stored
	*
	* @return	string  $name	The modified name.
	*/
	protected function generateUniqueName($data)
	{
		
		$key_array = array('name' => $data['name']);

		//[%%START_CUSTOM_CODE%%]
		$key_array['component_id'] = $data['component_id'];
		$key_array['component_object_id'] = $data['component_object_id'];
		//[%%END_CUSTOM_CODE%%]
		
		// Alter the name
		$table = $this->getTable();
		while ($table->load($key_array))
		{
			$key_array['name'] = JString::increment($key_array['name']);
			//[%%START_CUSTOM_CODE%%]
			$key_array['name'] = str_replace('(', '', $key_array['name']);
			$key_array['name'] = str_replace(')', '', $key_array['name']);
			//[%%END_CUSTOM_CODE%%]				
		}
		
		return htmlspecialchars_decode($key_array['name'], ENT_QUOTES);
	}
	//[%%START_CUSTOM_CODE%%]
	/**
	 * Method to get a unique code_name.
	 *
	 * @param   array   $data			The data where the original name is stored
	 * @param   string  $field			Name of field which allows for handling both code_name and plural_code_name
	 *
	 * @return	string  $code_name		The modified code_name.
	 */
	protected function generateUniqueCodeName($data, $field = 'code_name')
	{
		if ($data[$field] == '')
		{
			$key_array = array($field => str_replace('-', '_', JApplication::stringURLSafe($data['name'])));
		}
		else
		{
			$key_array = array($field => str_replace('-', '_', JApplication::stringURLSafe($data[$field])));
		}		
		
		$key_array[$field] = ltrim($key_array[$field], ' _0123456789');

		$key_array['component_id'] = $data['component_id'];
		$key_array['component_object_id'] = $data['component_object_id'];
		
		$table = $this->getTable();
		while ($table->load($key_array))
		{
			$key_array[$field] = str_replace('-', '_', JString::increment($key_array[$field], 'dash'));
		}

		return $key_array[$field];
	}	
	/**
	 * Method to check one or more records to see if they are predefined and thus cannot be changed.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	private function check_predefined($pks)
	{
		$view = JRequest::getCmd('view');

		$table = $this->getTable();
		$pks = (array) $pks;
		$ok_pks = array();

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				// Cannot delete predefined fieldsets unless from a components or component objects view. 
				if (!$table->predefined_field OR $table->id == 0 OR ($view != 'fields' AND $view != 'fieldsets'))
				{
					$ok_pks[] = $pk;				
				}
			}
		}

		return $ok_pks;	
	}	
	//[%%END_CUSTOM_CODE%%]	
}