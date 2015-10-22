<?php
/**
 * @version 		$Id: componentobject.php 411 2014-10-19 18:39:07Z BrianWade $
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
 * ComponentObject model.
 *
 */
class ComponentArchitectModelComponentObject extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_COMPONENTARCHITECT_COMPONENTOBJECTS';
	/**
	 * @var      string	The type alias for this object (for example, 'com_componentarchitect.componentobject')
	 */
	public $typeAlias = 'com_componentarchitect.componentobject';	

	/**
	 * @var		string	The context for the app call.
	 */
	protected $context = 'com_componentarchitect.componentobjects';
	
	/**
	 * @var		string	The event to trigger after before the data.
	 */
	protected $event_before_save = 'onComponentObjectBeforeSave';
	/**
	 * @var		string	The event to trigger after saving the data.
	 */
	protected $event_after_save = 'onComponentObjectAfterSave';

	/**
	 * @var    string	The event to trigger before deleting the data.
	 */
	protected $event_before_delete = 'onComponentObjectBeforeDelete';	
	/**
	 * @var    string	The event to trigger after deleting the data.
	 */
	protected $event_after_delete = 'onComponentObjectAfterDelete';	


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * 
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'ComponentObjects', $prefix = 'ComponentArchitectTable', $config = array())
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
			
						
			$registry = new JRegistry;
			$registry->loadString($item->joomla_parts);
			$item->joomla_parts = $registry->toArray();
			$registry = null; //release memory	
			
			// Check and reformat entries in the json array
			$field_array = $item->joomla_parts;
			$item->joomla_parts = $field_array;
			$registry = new JRegistry;
			$registry->loadString($item->joomla_features);
			$item->joomla_features = $registry->toArray();
			$registry = null; //release memory	
			
			// Check and reformat entries in the json array
			$field_array = $item->joomla_features;
			$item->joomla_features = $field_array;
			
			//[%%START_CUSTOM_CODE%%]
			// Get the parent object for this record
			if ($item->component_id > 0)
			{
				$item->component = $this->getComponent($item->component_id);
			}
			//[%%END_CUSTOM_CODE%%]	
			

			
		
						
			if ($recursive)
			{
				// If parent child object relationship created then children object functions will be here
				// If no children object then the logic is left for use in recursively getting other linked data
			
			}				
		}
				
		return $item;
	}
	//[%%START_CUSTOM_CODE%%]
	/**
	 * Method to get the associated Component/Extension.
	 *
	 * @param	int		The foreign key for the component/extension
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getComponent($fk)
	{
		if ($fk > 0)
		{
			$componentmodel = JModelLegacy::getInstance('component', 'ComponentArchitectModel', array('ignore_request' => false));

			$component = $componentmodel->getItem($fk);
			return $component;
		}
		return false;
	}	
	//[%%END_CUSTOM_CODE%%]
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$load_data	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $load_data = true)
	{
		$form = $this->loadForm('com_componentarchitect.edit.componentobject', 'componentobject', array('control' => 'jform', 'load_data' => $load_data));
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
		$data = $app->getUserState('com_componentarchitect.edit.componentobject.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('componentobject.id') == 0)
			{
			}
		}
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$this->preprocessData('com_componentarchitect.componentobject', $data);
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
		require_once JPATH_COMPONENT.'/helpers/pluralise.php';
		
		$table->plural_name = htmlspecialchars_decode($table->plural_name, ENT_QUOTES);
		if (empty($table->plural_name))
		{
			$name_array = explode(' ', $table->name);
			$name_array[count($name_array)-1] = ComponentArchitectPluraliseHelper::pluralise($name_array[count($name_array)-1]);
			$table->plural_name = implode(' ', $name_array);
		}	

		$table->short_name = htmlspecialchars_decode($table->short_name, ENT_QUOTES);
		if (empty($table->short_name))
		{
			$name_array = explode(' ', $table->name);
			$table->short_name = $name_array[count($name_array)-1];
		}

		$table->short_plural_name = htmlspecialchars_decode($table->short_plural_name, ENT_QUOTES);
		if (empty($table->short_plural_name))
		{
			$name_array = explode(' ', $table->plural_name);
			$table->short_plural_name = $name_array[count($name_array)-1];
		}
		
		$table->code_name = str_replace('-', '_', JApplication::stringURLSafe($table->code_name));
		if (empty($table->code_name))
		{
			$table->code_name = $this->generateUniqueCodeName((array) $table);
		}
		
		$table->plural_code_name = str_replace('-', '_', JApplication::stringURLSafe($table->plural_code_name));
		if (empty($table->plural_code_name))
		{
			$table->plural_code_name = $this->generateUniqueCodeName((array) $table, 'plural_code_name');
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
			
			if ((int) $data['id'] == 0)
			{
				$data['id'] = $new_pk;
			}
			
			if ($data['source_table'] != '')
			{
				if (!$this->importFields($data))
				{
					return false;
				}
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
	 *
	 */
	public function delete(&$pks)
	{
		//[%%START_CUSTOM_CODE%%]
		$this->cascadeDelete($pks);
		//[%%END_CUSTOM_CODE%%]

		// Include the componentarchitect plugins for the delete events.
		JPluginHelper::importPlugin('componentarchitect');	
		
		return parent::delete($pks);	
	}		
	//[%%START_CUSTOM_CODE%%]
	/**
	 * Method to delete the children of this object/table
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param	integer  $value  The value of the published state.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 */
	protected function cascadeDelete(&$pks)
	{	
		// If parent child object relationship created then children object functions will be here
		// If no children object then the logic is left for use in recursively getting other linked data
		
		$fieldset_model = JModelLegacy::getInstance('Fieldset', 'ComponentArchitectModel', array('ignore_request' => true));
		if (method_exists($fieldset_model, 'delete'))
		{
			// Ensure we have an array
			if (!is_array($pks))
			{
				$pks = array($pks);
			}
			foreach ($pks as $pk)
			{
				// Get all children ids
				$fieldsets_model = JModelLegacy::getInstance('Fieldsets', 'ComponentArchitectModel', array('ignore_request' => true));
				$fieldsets_model->setState('filter.component_object_id', $pk);

				$fieldset_children = $fieldsets_model->getItems();
				$child_pks = array();
				foreach ($fieldset_children as $child)
				{
					$child_pks[] = $child->id;
				}
				// Continue cascading the delete
				$fieldset_model->delete($child_pks);
			}
		}		

		return true;
	}
	//[%%END_CUSTOM_CODE%%]			
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

			// if this is not from a cascade copy from a parent object i.e. check the view is componentobjects then set the name and then all other name variables to blank to regenerate
			$view = JRequest::getCmd('view');
			if ($view == 'componentobjects')
			{
				$table->name = $this->generateUniqueName((array) $table);
				// Set ordering to 0 so it is forced to be set later to last position
				$table->ordering = 0;
				//[%%START_CUSTOM_CODE%%]
				$table->plural_name = '';
				$table->short_name = '';
				$table->short_plural_name = '';
				$table->code_name = '';
				$table->plural_code_name = '';
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

			// Trigger the onComponentObjectBeforeSave event.
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

			// Trigger the onComponentObjectAfterSave event.
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
			$key_array = array($field => str_replace('-', '_', JApplication::stringURLSafe($data[str_replace('code_','',$field)])));
		}
		else
		{
			$key_array = array($field => str_replace('-', '_', JApplication::stringURLSafe($data[$field])));
		}		
		
		$key_array[$field] = ltrim($key_array[$field], ' _0123456789');

		$key_array['component_id'] = $data['component_id'];
		
		$table = $this->getTable();
		while ($table->load($key_array))
		{
			$key_array[$field] = str_replace('-', '_', JString::increment($key_array[$field], 'dash'));
		}

		return $key_array[$field];
	}
	/**
	 * Method to get a unique code_name.
	 *
	 * @param   array   $data			The data where the original name is stored
	 * @param   string  $field			Name of field which allows for handling both code_name and plural_code_name
	 *
	 * @return	string  $code_name		The modified code_name.
	 */	
	protected function importFields($data)
	{
		$db = JFactory::getDBO();
		
		$source_table = $data['source_table'];
		$component_id = $data['component_id'];
		$component_object_id = $data['id'];
		
		// Get the default fieldset
		$query = $db->getQuery(true);

		// Construct the query
		$query->select($db->quoteName('a.default_fieldset_id'));
		$query->from($db->quoteName('#__componentarchitect_componentobjects').' AS a ');
		$query->where($db->quoteName('a.id').' = '.$component_object_id);

		// Setup the query
		$db->setQuery($query->__toString());

		// Return the result
		$default_fieldset_id = $db->loadResult();
			

		$query->clear();		
		$db->setQuery('SHOW COLUMNS FROM ' . $source_table);
		if ($table_fields = $db->loadAssocList())
		{
			$query->clear();

			// Construct the query
			$query->select($db->quoteName('a.code_name').' AS code_name');
			$query->from($db->quoteName('#__componentarchitect_fields').' AS a ');
			$query->where($db->quoteName('a.component_object_id').' = '.$component_object_id);

			// Setup the query
			$db->setQuery($query->__toString());

			// Return the result
			$current_fields = $db->loadColumn();

			$field_type_lookup = array();
			
			// Get all field types to get the Field Type ID for the field
			$field_types_model = JModelLegacy::getInstance('fieldtypes', 'ComponentarchitectModel', array('ignore_request' => true));
			$field_types_model->setState('list.ordering', 'a.name');
			$field_types_model->setState('list.direction', 'ASC');
			$field_types_model->setState('list.select', 'a.*');	
			
			$field_types = $field_types_model->getItems();	
			foreach ($field_types as $field_type)
			{
				$field_type_lookup[] = $field_type->code_name;
			}	
					
			foreach ($table_fields as $table_field)
			{
				$field = array();
				$field['code_name'] = JString::strtolower($table_field['Field']);
				
				// Only process fields if they do not already exist
				if (!in_array($field['code_name'], $current_fields))
				{
					$field['id'] = 0;
					$field['name'] = ucWords(str_replace('-',' ',str_replace('_',' ',$table_field['Field'])));
					
					$type_array = explode('(', $table_field['Type']);
					if (preg_match('/unsigned/i', $table_field['Type']))
					{
						$unsigned = true;
					}
					else
					{
						$unsigned = false;
					}
													
					$field['mysql_datatype'] = strtoupper(trim(preg_replace('/\)|unsigned/i', '', $type_array[0])));
					
					if (!is_null($table_field['Default']) AND $table_field['Default'] != '')
					{
						$field['mysql_default'] = "'".$table_field['Default']."'";
					}
					else
					{
						if ($table_field['Null'] == 'YES' AND is_null($table_field['Default']))
						{
							$field['mysql_default'] = "'NULL'";
						}
						else
						{
							$field['mysql_default'] = '';
						}
					}
					
					// Set field maxlength
					if (isset($type_array[1]))
					{
						$field['mysql_size'] = trim(preg_replace('/\)|unsigned/i', '', $type_array[1]));
					}
					else
					{
						$field['mysql_size'] = '';
					}
					
					switch ($field['mysql_datatype'])
					{
						case 'VARCHAR':
						case 'CHAR' :
						case 'TINYTEXT':
							if ((int) $field['mysql_size'] <= 255)
							{
								$type = 'text';
							}
							else
							{
								$type = 'textarea';
							}
							$field['maxlength'] = $field['mysql_size'];
								$field['php_variable_type'] = 'string';
							
							if ((int) $field['mysql_size'] <= 100)
							{
								$field['size'] = $field['mysql_size'];
							}

							if ($field['mysql_default'] == '' AND $field['mysql_datatype'] != 'TINYTEXT')
							{
								$field['mysql_default'] = "''";
							}							
							
							break;
						case 'INT':
						case 'BIGINT':
						case 'SERIAL':
							// Most likely a foreign key so check if we want to make it modal
							if ((int) $field['mysql_size'] == 10 
								/* AND $unsigned */ 
								AND $table_field['Key'] != '')
							{
								// Get all the component object names if one matches the field code name (without '_id'_
								$query->clear();

								// Construct the query
								$query->select($db->quoteName('a.id').' AS id');
								$query->from($db->quoteName('#__componentarchitect_componentobjects').' AS a ');
								$query->where($db->quote($field['code_name']).' REGEXP '.$db->quoteName('a.code_name'));

								// Setup the query
								$db->setQuery($query->__toString());							


								// Return the result
								$foreign_object_id = $db->loadRow();
								if ($foreign_object_id[0] > 0)
								{								
									$type = 'modal';
									$field['foreign_object_id'] = $foreign_object_id[0];
								}
								else
								{
									$type = 'text';
									$field['validate'] = 1;
									$field['validation_type'] = 'numeric';								
								}
								
								if ($field['mysql_default'] == '')
								{
									$field['mysql_default'] = "'0'";
								}
								
								$field['php_variable_type'] = 'int';
								
								break;
							}						
						case 'TINYINT':
						case 'SMALLINT':
							if ((int) $field['mysql_size'] == 1)
							{
								$type = 'checkbox';
							}
							else
							{
								$type = 'text';
								$field['validate'] = 1;
								$field['validation_type'] = 'numeric';
							}	
							
							if ($field['mysql_default'] == '')
							{
								$field['mysql_default'] = "'0'";
							}
							
							$field['php_variable_type'] = 'int';

							break;
						case 'BOOL':
						case 'BOOLEAN':
							$type = 'checkbox';
							
							$field['php_variable_type'] = 'int';
							
							break;													
						case 'FLOAT':
						case 'DOUBLE':
						case 'REAL':
						case 'DECIMAL':
							$type = 'text';
							
							$field['validate'] = 1;
							$field['validation_type'] = 'numeric';								
							
							$field['php_variable_type'] = 'float';

							if ($field['mysql_default'] == '')
							{
								$field['mysql_default'] = "'0'";
							}							

							
							break;	
						case 'DATE':
						case 'DATETIME':
						case 'TIMESTAMP':
							$type = 'calendar';
							
							break;						
						case 'TIME':
							$type = 'text';
							
							$field['format'] = 'H:M:S';
							
							break;																		
						case 'YEAR':												
							$type = 'text';
							
							$field['format'] = 'Y';
							
							break;	
						case 'TEXT':
						case 'MEDIUMTEXT':
						case 'LONGTEXT':
							$type = 'editor';

							$field['mysql_default'] = '';
							
							break;	
						default:
							$type = 'text';

							if ($field['mysql_default'] == '')
							{
								$field['mysql_default'] = "''";
							}							
							
							break;
					}
					
					// Set values for any defaults for the field type
					$index = array_search($type,$field_type_lookup);
					$optional_attributes = array('class',
						'size',
						'maxlength',
						'width',
						'height',
						'cols',
						'rows',
						'format',
						'first',
						'last',
						'step',
						'hide_none',
						'hide_default',
						'buttons',
						'hide_buttons',
						'field_filter',
						'max_file_size',
						'exclude_files',
						'accept_file_types',
						'directory',
						'link',
						'translate',
						'client',
						'stripext',
						'preview',
						'autocomplete',
						'onclick',
						'onchange',
						'mysql_size',
						'mysql_datatype',
						'mysql_default');

					foreach ($optional_attributes as $attribute)
					{
						// Temporary overrider as the Joomla Calendar field seems to only work with the full format date as below.
						if ($type == 'calendar' AND $attribute == 'format' AND !isset($field['format']))
						{
							$field[$attribute] = 'Y-m-d H:M:S';
						}
						else
						{
							if ($attribute == 'mysql_size' OR $attribute == 'mysql_datatype' OR $attribute == 'mysql_default')
							{
								if ($attribute == 'mysql_size' AND $field['mysql_size'] == ''
									AND in_array($field['mysql_datatype'], array('FLOAT', 'DOUBLE', 'REAL', 'DECIMAL')))
								{
									$field['mysql_size'] = '';
								}
								else
								{							
									if (!isset($field[$attribute]) OR $field[$attribute] == '')
									{
										$attribute_default = $attribute.'_default';
										if (isset($field_types[$index]->$attribute_default) AND 
											$field_types[$index]->$attribute_default != '')
										{
											$field[$attribute] = $field_types[$index]->$attribute_default;
										}
									}
								}							
							}
							else
							{
								if ($field_types[$index]->$attribute)
								{
									if (!isset($field[$attribute]) OR $field[$attribute] == '')
									{
										$attribute_default = $attribute.'_default';
										if (isset($field_types[$index]->$attribute_default) AND 
											$field_types[$index]->$attribute_default != '')
										{
											$field[$attribute] = $field_types[$index]->$attribute_default;
										}
									}
								}
							}
						}
					}

					// Set fields for field
					$field['component_id'] = $component_id;
					$field['component_object_id'] = $component_object_id;
					$field['fieldset_id'] = $default_fieldset_id;
					$field['fieldtype_id'] = $field_types[$index]->id;
					$field['language'] = '*';				
					$field['state'] = 1;
					$field['access'] = 1;
					$field['predefined_field'] = 0;									

					$field_model = JModelLegacy::getInstance('field', 'ComponentarchitectModel');


					if(!$field_model->save($field))
					{
						$this->setError(JText::sprintf('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_ERROR_PROBLEM_IMPORTING_SOURCE_TABLE', $field['code_name'], $source_table));
						return false;
					}
				}			
			}
			return true;
		}
		else
		{
			$this->setError(JText::sprintf('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_ERROR_SOURCE_TABLE_NOT_ACCESSIBLE', $source_table));
			return false;
		}	
	}	 
	//[%%END_CUSTOM_CODE%%]	
}