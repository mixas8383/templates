<?php
/**
 * @version			$Id: componentwizard.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */
//[%%START_CUSTOM_CODE%%]
// No direct access.
defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.modeladmin');
}
/**
 * Methods supporting a a generate dialog.
 *
 */
class ComponentArchitectModelComponentWizard extends JModelAdmin
{

	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_COMPONENTARCHITECT_COMPONENT_WIZARD';
	/**
	 * @var		string	The conttext for the app call.
	 */
	protected $context = 'com_componentarchitect.componentwizard';
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		$session = JFactory::getSession();
		
		$data = $app->getUserState($this->context . '.data');

		$params = JComponentHelper::getParams('com_componentarchitect');
		$this->setState('params', $params);

		$component_name = $app->getUserStateFromRequest($this->context.'.component_name', 'component_name', '', 'string');
		$this->setState('componentwizard_component_name', $data['component_name']);

		$component_objects = $app->getUserStateFromRequest($this->context.'.component_objects', 'component_objects', array(), 'array');
		if (isset($data['component_objects']['name']))
		{
			$this->setState('componentwizard_component_objects', $data['component_objects']['name']);
		}
		else
		{
			$this->setState('componentwizard_component_objects', '');
		}

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
		$item = new JObject();
		
		$item->component_name = $this->getState('componentwizard_component_name');

		if ($this->getState('componentwizard_component_objects') == '')
		{
			$item->component_objects = array('');
		}
		else
		{
			$item->component_objects = $this->getState('componentwizard_component_objects');
		}
		
		
		return $item;
	}		
	/**
	 * Method to get the component wizard form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * 
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{

		$form = $this->loadForm('com_componentarchitect.componentwizard', 'componentwizard', array('control' => 'jform', 'load_data' => $loadData),true);
		if (empty($form))
		{
			return false;
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
		$data = JFactory::getApplication()->getUserState('com_componentarchitect.componentwizard.data', array());
		return $data;
	}
	/**
	 * Method to get create the component and any objects/tables entered.
	 * 
	 * @param	string	$token		A form session token used to validate we are on the same form
	 *
	 * @return	array	An array of the progress (at completion) for the component generation.
	 */	
	public function wizardsave($data)
	{
		
		$component_model = JModelAdmin::getInstance('Component', 'ComponentArchitectModel', array('ignore_request' => false));
		$form = $component_model->getForm();
		
		$component = array();
		
		$fieldsets = $form->getFieldsets();
		
		foreach ($fieldsets as $fieldset)
		{
			// Check if a group
			if (count($form->getGroup($fieldset->name)) <= 0)
			{
				$fields = $form->getFieldset($fieldset->name);
				
				foreach ($fields as $field)
				{
					$component[$field->fieldname] = $form->getFieldAttribute($field->fieldname,'default', '');
				}
			}
			else
			{
				$fields = $form->getGroup($fieldset->name);
				if (count($fields) > 0)
				{
					foreach ($fields as $field)
					{
						$component[$fieldset->name][$field->fieldname] = $form->getFieldAttribute($field->fieldname,'default', '0', $fieldset->name);
					}				
				}
			}
		}

		$component['id'] = 0;
		$component['name'] = $data['component_name'];
		// Set code name to blank so it is auto generated
		$component['code_name'] = '';
		
		$component_model->save($component);

		$default_component_object = 0;
		
		for ($i = 0; $i < count($data['component_objects']['name']); $i++)
		{
			if ($data['component_objects']['name'][$i] != '')
			{
				$component_object_model = JModelAdmin::getInstance('ComponentObject', 'ComponentArchitectModel', array('ignore_request' => false));
				$form = $component_object_model->getForm();
				
				$component_object = array();
				$fieldsets = $form->getFieldsets();
				
				foreach ($fieldsets as $fieldset)
				{
					// Check if a group
					if (count($form->getGroup($fieldset->name)) <= 0)
					{
						$fields = $form->getFieldset($fieldset->name);
						
						foreach ($fields as $field)
						{
							$component_object[$field->fieldname] = $form->getFieldAttribute($field->fieldname,'default', '');
						}
					}
					else
					{
						$fields = $form->getGroup($fieldset->name);
						if (count($fields) > 0)
						{
							foreach ($fields as $field)
							{
								$component_object[$fieldset->name][$field->fieldname] = $form->getFieldAttribute($field->fieldname,'default', '', $fieldset->name);
							}				
						}
					}
				}				
				
				$component_object['id'] = 0;
				$component_object['component_id'] = $component_model->getState('component.id');
				$component_object['name'] = $data['component_objects']['name'][$i];
				$component_object['source_table'] = $data['component_objects']['source_table'][$i];

				// Set component object plural, code and short names to blank so they are auto generated
				$component_object['plural_name'] = '';
				$component_object['short_name'] = '';
				$component_object['short_plural_name'] = '';

				// Set component code names to blank so they are auto generated
				$component_object['code_name'] = '';
				$component_object['plural_code_name'] = '';
				
				$component_object_model->save($component_object);
				// Save id for default component object as first object created
				if ($default_component_object == 0)
				{
					$default_component_object = $component_object_model->getState('componentobject.id');
				}
								
				unset($component_object_model);
			}
		}
		$pk = $component_model->getState('component.id');
		$component_temp = $component_model->getItem($pk);
		
		$component['id'] = $pk;
		$component['default_object_id'] = $default_component_object;
		$component['code_name'] = $component_temp->code_name;		
		$component['created'] = $component_temp->created;
		$component['created_by'] = $component_temp->created_by;

		$component_model->save($component);

		unset($component_model);
		
		return true;
	}
}
//[%%END_CUSTOM_CODE%%]