<?php
/**
 * @version 		$Id: events.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.events
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: events.php 806 2013-12-24 13:24:16Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.events
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

/**
 * ComponentArchitect  plugin class.
 *
 */
class plgComponentArchitectEvents extends JPlugin
{
	/**
	 * @var    boolean	Load the language file on instantiation.
	 */
	protected $autoloadLanguage = true;
	
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		if (version_compare(JVERSION, '3.1', 'lt'))
		{		
			$this->loadLanguage();
		}
	}
	/**
	 * Before save component/extension method - dummy entry
	 * Component/Extension is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the component/extension passed to the plugin (added in 1.6)
	 * @component			object		The data relating to the component/extension that was saved
	 * @isNew				bool		If the component/extension is just about to be created
	 * @return				boolean	
	 */
	public function onComponentBeforeSave($context, $component, $is_new)
	{	
		return true;
	}
	
	/**
	 * After save component/extension method - dummy entry
	 * Component/Extension is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the component/extension passed to the plugin (added in 1.6)
	 * @component			object		The data relating to the component/extension that was saved
	 * @isNew				boolean		If the component/extension is just about to be created
	 * @return				boolean	
	 */
	public function onComponentAfterSave($context, $component, $is_new)
	{	
		return true;
	}
	
	/**
	 * Before delete component/extension method - dummy entry
	 *
	 * @context				string		The context for the component/extension passed to the plugin.
	 * @component			object		The data relating to the component/extension that is to be deleted.
	 * @isNew				boolean		If the component/extension is just created
	 * @return				boolean	
	 */
	public function onComponentBeforeDelete($context, $component)
	{
		return true;
	}
	
	/**
	 * After delete component/extension method - dummy entry
	 *
	 * @context				string	The context for the component/extension passed to the plugin.
	 * @component			object	The data relating to the component/extension that was deleted.
	 * @return				boolean
	 */
	public function onComponentAfterDelete($context, $component)
	{
		//[%%START_CUSTOM_CODE%%]
		// Remove fieldsets when component deleted
		$componentobjects_model = JModelLegacy::getInstance('componentobjects', 'ComponentArchitectModel', array('ignore_request' => true));
		$componentobjects_model->setState('filter.component_id', $component->id);
		$componentobjects_model->setState('list.ordering', 'a.ordering');
		$componentobjects_model->setState('list.direction', 'ASC');
		$componentobjects_model->setState('list.select', 'a.*');
		
		
		$componentobjects = $componentobjects_model->getItems();

		$componentobject_model = JModelLegacy::getInstance('componentobject', 'ComponentArchitectModel');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		foreach ($componentobjects as $componentobject)
		{
			try
			{
				// Deleting the child component objects for this component
				$query->clear();
				$query->delete('#__componentarchitect_componentobjects');
				$query->where('id = '.$componentobject->id);
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
			
			if (!$this->onComponentObjectAfterDelete($context, $componentobject))
			{
				return false;
			}

		}
		//[%%END_CUSTOM_CODE%%]		
		return true;
	}
	/**
	 * Prepare component/extension method - dummy entry
	 *
	 * @context				string	The context for the component/extension passed to the plugin.
	 * @component			object	The data relating to the component/extension.
	 * @params				array	Array holding the params for the current view and component/extension.
	 * @offset				integer	Offset of this component/extension in a list.
	 * @return				string
	 */		
	public function onComponentPrepare($context, $row, &$params, $offset=0)
	{
		// Display during .

		return '';
		
	}
	
	/**
	 * After name component/extension method - dummy entry
	 *
	 * @context				string	The context for the component/extension passed to the plugin.
	 * @component			object	The data relating to the component/extension.
	 * @params				array	Array holding the params for the current view and component/extension.
	 * @offset				integer	Offset of this component/extension in a list.
	 * @return				string
	 */		
	public function onComponentAfterName($context, $row, &$params, $offset=0)
	{
		// Display after name is output.

		return '';
		
	}
	
	/**
	 * Before display component/extension method - dummy entry
	 *
	 * @context				string	The context for the component/extension passed to the plugin.
	 * @component			object	The data relating to the component/extension.
	 * @params				array	Array holding the params for the current view and component/extension.
	 * @offset				integer	Offset of this component/extension in a list.
	 * @return				string
	 */		
	public function onComponentBeforeDisplay($context, $row, &$params, $offset=0)
	{
		
		// Display before component.

		return '';
	}
	
	/**
	 * After display component/extension method - dummy entry
	 *
	 * @context				string	The context for the component/extension passed to the plugin.
	 * @component			object	The data relating to the component/extension.
	 * @params				array	Array holding the params for the current view and component/extension.
	 * @offset				integer	Offset of this component/extension in a list.
	 * @return				string
	 */		
	public function onComponentAfterDisplay($context, $row, &$params, $offset=0)
	{
		// Display after component.

		return '';
		
	}
	/**
	 * Before save object/table method - dummy entry
	 * Object/Table is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the object/table passed to the plugin (added in 1.6)
	 * @componentobject			object		The data relating to the object/table that was saved
	 * @isNew				bool		If the object/table is just about to be created
	 * @return				boolean	
	 */
	public function onComponentObjectBeforeSave($context, $component_object, $is_new)
	{	
		return true;
	}
	
	/**
	 * After save object/table method - dummy entry
	 * Object/Table is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the object/table passed to the plugin (added in 1.6)
	 * @componentobject			object		The data relating to the object/table that was saved
	 * @isNew				boolean		If the object/table is just about to be created
	 * @return				boolean	
	 */
	public function onComponentObjectAfterSave($context, $component_object, $is_new)
	{	
		//[%%START_CUSTOM_CODE%%]
		// Avoid duplicating pre-defined Fieldsets and Fields when doing a Save As Copy for the Object/Table
		$app = JFactory::getApplication('administrator');
		if ($app->input->get('task') == 'save2copy')
		{			
			return true;
		}	
		// Need to check through the Include settings for this object to see which fields and fieldsets need to be added or removed.
		// First need to get the Component so can check Global settings if Use Global set in Object
		
		$component_model = JModelLegacy::getInstance('component', 'ComponentarchitectModel', array('ignore_request' => false));
		//$state = $component_model->get('State');		
		
		$component = $component_model->getItem($component_object->component_id);
		$component_features = $component->joomla_features;
		$component_parts = $component->joomla_parts;
		$component_features['include_categories'] = $component_parts['generate_categories'];
		
		
		// Convert features back to array
		$registry = new JRegistry;
		$registry->loadString($component_object->joomla_features);
		$object_features = $registry->toArray();
		$registry = null; //release memory
		
		// Whether a catid field is required is based on the generate_categories value in the joomla_parts field
		// add this setting to the $object_features array
		// Convert features back to array
		$registry = new JRegistry;
		$registry->loadString($component_object->joomla_parts);
		$object_parts = $registry->toArray();
		$registry = null; //release memory	
		
		if ($component_parts['generate_categories'] == '0')
		{
			$component_features['include_categories'] = '0';
			$object_features['include_categories'] = '0';
			
		}
		else
		{
			$object_features['include_categories'] = $object_parts['generate_categories'];
		}
		
		

		$include_field_array = array();
		$include_fieldset_array = array();	
		
		
		//Always create an id field and a basic_details fieldset - even if only field is id.
		$include_field_array[] = 'id';
		
		$include_fieldset_array[] = 'basic_details';			
		
		$remove_field_array = array();
		$remove_fieldset_array = array();	
		
		$fieldset_lookup_array = array();
		$field_lookup_array = array();
		
		
		foreach ($object_features as $key => $include)
		{
			if ($include == '')
			{
				$include = (int) $component_features[$key];
			}
			else
			{
				$include = (int) $include;
			}

			// Work out which fields and fieldsets are required for this include
			$field_array = array();
			$fieldset_name = '';
			$include_condition = JString::substr($key, 8);
			switch ($include_condition)
			{

				case 'alias':
				case 'description':
				case 'intro':	
				case 'name':	
				case 'ordering':
				case 'access':
				case 'featured':
				case 'hits':
				case 'language':				
					$field_array[] = $include_condition;
					$fieldset_name = 'basic_details';
					break;					
				case 'versions':	
					$field_array[] = 'version';
					$fieldset_name = 'basic_details';
					break;					
				case 'categories':
					$field_array[] = 'catid';
					$fieldset_name = 'basic_details';
					break;								
				case 'checkout':
					$field_array[] = 'checked_out';
					$field_array[] = 'checked_out_time';
					$fieldset_name = 'publishing';
					break;
				case 'created':
					$field_array[] = 'created';
					$field_array[] = 'created_by';
					$field_array[] = 'created_by_alias';
					$fieldset_name = 'publishing';
					break;	
				case 'image':
					$field_array[] = 'images';
					$field_array[] = 'image_url';
					$field_array[] = 'image_alt_text';
					$field_array[] = 'image_caption';
					if ((int) $component_features['include_intro'])
					{
						$field_array[] = 'intro_image_url';
						$field_array[] = 'intro_image_alt_text';
						$field_array[] = 'intro_image_caption';					
					}
					$fieldset_name = 'image';
					break;
				case 'urls':
					$field_array[] = 'urls';
					$field_array[] = 'urla';
					$field_array[] = 'urla_text';
					$field_array[] = 'urla_target';
					$field_array[] = 'urlb';
					$field_array[] = 'urlb_text';
					$field_array[] = 'urlb_target';
					$field_array[] = 'urlc';
					$field_array[] = 'urlc_text';
					$field_array[] = 'urlc_target';
					$field_array[] = 'urls';
					$fieldset_name = 'urls';					
					break;													
				case 'modified':
					$field_array[] = 'modified';
					$field_array[] = 'modified_by';
					$fieldset_name = 'publishing';
					break;	
				case 'metadata':
					$field_array[] = 'metakey';
					$field_array[] = 'metadesc';
					$field_array[] = 'robots';
					$field_array[] = 'author';
					$field_array[] = 'xreference';
					$fieldset_name = 'metadata';
					break;	
				case 'published_dates':
					$field_array[] = 'publish_up';
					$field_array[] = 'publish_down';
					$fieldset_name = 'publishing';
					break;															
				case 'params_record':
					$field_array[] = 'params';
					$fieldset_name = 'admin';
					break;																				
				case 'assetacl_record':
					$field_array[] = 'asset_id';
					$fieldset_name =  'admin';
					break;
				case 'status':
					$field_array[] = 'state';
					$fieldset_name = 'publishing';
					break;
				default:
					break;
			}

			if ($include == 1)
			{
				if (count($field_array) > 0)
				{
					$include_field_array = array_merge($include_field_array, $field_array);
				}
				if ($fieldset_name != '' AND !in_array($fieldset_name,$include_fieldset_array))
				{
					$include_fieldset_array[] = $fieldset_name;
				}
			}
			else
			{
				// Check if false i.e. zero - ignore if not as something wrong with the setting.
				if ($include == 0)
				{
					if (count($field_array) > 0)
					{				
						$remove_field_array = array_merge($remove_field_array, $field_array);
					}
					if ($fieldset_name != '' AND !in_array($fieldset_name,$remove_fieldset_array))
					{
						$remove_fieldset_array[] = $fieldset_name;
					}
				}
			}
		}
		
		// Get db and setup query
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
				
		// We have arrays for what is to be included and removed so process these
		$final_remove_fieldset_array = array();
		foreach ($remove_fieldset_array as $fieldset)
		{
			// Decide if fieldset is needed or not
			
			// get a count of the number of non predefined fields in the fieldset
			$query->select(COUNT($db->quoteName('f.id')));
			$query->from($db->quoteName('#__componentarchitect_fields').' AS f');
			$query->join('LEFT', $db->quoteName('#__componentarchitect_fieldsets').' AS fs ON '.$db->quoteName('fs.id').' = '.$db->quoteName('f.fieldset_id'));
			$query->where($db->quoteName('fs.component_object_id').' = '.$component_object->id);
			$query->where($db->quoteName('fs.code_name').' = '.$db->quote($fieldset));
			$query->where($db->quoteName('f.predefined_field').' = 0');
			
			try
			{
				$db->setQuery($query);
				$count = $db->loadResult();	
			}			
			catch (RuntimeException $e)
			{
				$result = false;
			}	
			$query->clear();	
			
			if (!in_array($fieldset, $include_fieldset_array) AND $count <= 0)
			{
				$final_remove_fieldset_array[] = $fieldset;
			}
		}
		
		// Remove fields not required
		$fields_model = JModelLegacy::getInstance('fields', 'ComponentarchitectModel', array('ignore_request' => true));
		$fields_model->setState('filter.component_object_id', $component_object->id);
		$fields_model->setState('filter.predefined_field', '1');		
		$fields_model->setState('list.ordering', 'a.ordering');
		$fields_model->setState('list.direction', 'ASC');
		$fields_model->setState('list.select', 'a.*');
		
		
		$fields = $fields_model->getItems();

		$field_model = JModelLegacy::getInstance('field', 'ComponentarchitectModel');

		
		$result = true;
		$pks =  array();
		
		for ($i = 0; $i < count($fields); $i++)
		{			
			if (in_array($fields[$i]->code_name, $remove_field_array))
			{
				// Directly delete the fields in the database as the use of the model would pick up the onDelete events which prevent predefined fields from being deleted
				// Create a new query object.

				$query->delete();
				$query->from($db->quoteName('#__componentarchitect_fields'));
				$query->where($db->quoteName('id').' = '.$fields[$i]->id);

				try
				{
					$db->setQuery($query);
					$db->execute();	
				}			
				catch (RuntimeException $e)
				{
					$result = false;
				}	

				$query->clear();
			}
		}
		
		
		if ($result)
		{
			// Remove fieldsets not required
			$fieldsets_model = JModelLegacy::getInstance('fieldsets', 'ComponentarchitectModel', array('ignore_request' => true));
			$fieldsets_model->setState('filter.component_object_id', $component_object->id);
			$fieldsets_model->setState('list.ordering', 'a.ordering');
			$fieldsets_model->setState('list.direction', 'ASC');
			$fieldsets_model->setState('list.select', 'a.*');
			
			$fieldsets = $fieldsets_model->getItems();		

			$fieldset_model = JModelLegacy::getInstance('fieldset', 'ComponentarchitectModel');

			$pks =  array();
			
			for ($i = 0; $i < count($fieldsets); $i++)
			{			
				if (in_array($fieldsets[$i]->code_name, $final_remove_fieldset_array))
				{
					// Directly delete the fieldset in the database as the use of the model would pick up the onDelete events which prevent predefined fieldsets from being deleted
					// Create a new query object.

					$query->delete();
					$query->from($db->quoteName('#__componentarchitect_fieldsets'));
					$query->where($db->quoteName('id').' = '.$fieldsets[$i]->id);
					
					try
					{
						$db->setQuery($query);
						$db->execute();	
					}			
					catch (RuntimeException $e)
					{
						$result = false;
					}	
					$query->clear();				
				}
				else
				{
					$fieldset_lookup_array[$fieldsets[$i]->code_name] = $fieldsets[$i]->id;
				}
			}
		}
		
		$default_fieldset_id = 0;
		
		if ($result)
		{		
			// Check if fieldsets required exist if not add them
			foreach ($include_fieldset_array as $fieldset_code_name)
			{
				$match_found = false;
				for ($i	= 0;  $i < count($fieldsets); $i++)
				{
					if($fieldsets[$i]->code_name == $fieldset_code_name)
					{
						$fieldset_lookup_array[$fieldsets[$i]->code_name] = $fieldsets[$i]->id;
						if ($fieldset_code_name == 'basic_details')
						{
							$default_fieldset_id = $fieldsets[$i]->id;
						}
						
						$match_found = true;
						continue;
					}
				}
				// No matching fieldset so add it
				if(!$match_found)
				{
					// Initialise a new fieldset
					$fieldset = array();
					switch ($fieldset_code_name)
					{
						case 'basic_details':
							$fieldset['name'] = $component_object->name.' '.JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_BASIC_DETAILS_LABEL');
							$fieldset['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_BASIC_DETAILS_DESC');
							$fieldset['ordering'] = 1;
							
							break;
						case 'image':
							$fieldset['name'] = $component_object->name.' '.JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_IMAGE_LABEL');
							$fieldset['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_IMAGE_DESC');
							$fieldset['ordering'] = 2;
							
							break;
						case 'urls':
							$fieldset['name'] = $component_object->name.' '.JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_URLS_LABEL');
							$fieldset['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_URLS_DESC');
							$fieldset['ordering'] = 3;
							
							break;
						case 'publishing':
							$fieldset['name'] = $component_object->name.' '.JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_PUBLISHING_LABEL');
							$fieldset['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_PUBLISHING_DESC');
							$fieldset['ordering'] = 4;
							
							break;																						
						case 'metadata':
							$fieldset['name'] = $component_object->name.' '.JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_METADATA_LABEL');
							$fieldset['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_METADATA_DESC');
							$fieldset['ordering'] = 5;
							
							break;
						case 'admin':
							$fieldset['name'] = $component_object->name.' '.JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_ADMIN_LABEL');
							$fieldset['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELDSET_ADMIN_DESC');
							$fieldset['ordering'] = 6;
							
							break;																					
					}
					$fieldset['id'] = 0;
					$fieldset['code_name'] = $fieldset_code_name;
					$fieldset['component_id'] = $component_object->component_id;
					$fieldset['component_object_id'] = $component_object->id;
					$fieldset['state'] = 1;
					$fieldset['access'] = 1;
					$fieldset['language'] = '*';
					$fieldset['predefined_fieldset'] = 1;
					

					// Slight fudge because in Admin save in FieldModel but in the Site in FieldFormModel
					if (JFactory::getApplication()->isSite())
					{
						$fieldset_model = JModelLegacy::getInstance('fieldsetform', 'ComponentarchitectModel', array('ignore_request' => false));
					}
					else
					{
						$fieldset_model = JModelLegacy::getInstance('fieldset', 'ComponentarchitectModel', array('ignore_request' => false));
					}
					
					//$fieldset_model->setState('fieldset.id',0);
					
					if(!$fieldset_model->save($fieldset))
					{
						$result = false;
					}
					else
					{
						$fieldset_lookup_array[$fieldset['code_name']] = $fieldset_model->getState('fieldset.id');
						if ($fieldset_code_name == 'basic_details')
						{
							$default_fieldset_id = $fieldset_model->getState('fieldset.id');
						}							
					}															
				}
			}	
		}

		if ($result AND $default_fieldset_id > 0)
		{
			// Directly update the default_fieldset_id in the Component Object - can't use model as this would cause a loop
			// Create a new query object.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Construct the query
			$query->select('*');
			$query->from($db->quoteName('#__componentarchitect_componentobjects'));
			$query->where($db->quoteName('id').' = '.$component_object->id);

			// Setup the query
			$db->setQuery($query->__toString());

			// Return the result
			$row = $db->loadObjectList();
			
			// If default field id not set yet then set it to value
			
			if (!isset($row[0]->default_fieldset_id) OR $row[0]->default_fieldset_id == 0)
			{
				$query->clear();
				$query->update($db->quoteName('#__componentarchitect_componentobjects'));
				$query->set($db->quoteName('default_fieldset_id').' = '.$default_fieldset_id);
				$query->where($db->quoteName('id').' = '.$component_object->id);
				
				$db->setQuery($query->__toString());
				$db->execute(); 				
			}
		}

		if ($result)
		{
			$field_type_lookup = array();
			// Get all field types to get the Field Type ID for the field
			$field_types_model = JModelLegacy::getInstance('fieldtypes', 'ComponentarchitectModel', array('ignore_request' => true));
			$field_types_model->setState('list.ordering', 'a.ordering');
			$field_types_model->setState('list.direction', 'ASC');
			
			$field_types = $field_types_model->getItems();	
			foreach ($field_types as $field_type)
			{
				$field_type_lookup[$field_type->code_name] = (int) $field_type->id;
			}		
			
			// Check if fields required exist if not add them
			
			foreach ($include_field_array as $field_code_name)
			{
				$match_found = false;		
				
				for ($i	= 0;  $i < count($fields); $i++)
				{
					if($fields[$i]->code_name == $field_code_name)
					{
						$match_found = true;
						continue;
					}
				}
				// No matching field so add it
				if(!$match_found)
				{
					// Initialise a new field
					$field = array();
					
					switch ($field_code_name)
					{
						case 'id':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_ID_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_ID_DESC');
							$field['class'] = 'readonly';
							$field['default'] = '0';
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";	
							$field['readonly'] = 1;
							$field['ordering'] = 1;
							$field['php_variable_type'] = 'int';
							
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['hidden'];
							break;
						case 'name':	
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_NAME_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_NAME_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['required'] = 1;	
							$field['search'] = 1;
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';						
							$field['ordering'] = 2;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;
						case 'alias':
							$field['name'] = JText::_('JFIELD_ALIAS_LABEL');
							$field['description'] = JText::_('JFIELD_ALIAS_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['filter'] = 'unset';	
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';							
							$field['search'] = 1;
							$field['ordering'] = 3;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;							
						case 'catid':
							$field['name'] = JText::_('JCATEGORY');
							$field['description'] = JText::_('JFIELD_CATEGORY_DESC');
							$field['class'] = 'inputbox';
							$field['extension'] = '#__com_'.JString::strtolower(str_replace('_','',$component->code_name));
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';							
							$field['mysql_default'] = "'0'";								
							$field['ordering'] = 4;
							$field['php_variable_type'] = 'int';

							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['category'];
							
							break;
						case 'state':
							$field['name'] = JText::_('JSTATUS');
							$field['description'] = JText::_('JFIELD_PUBLISHED_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '1';
							$field['default'] = '1';
							$field['option_values'] = "1:JPUBLISHED\n";
							$field['option_values'] .= "0:JUNPUBLISHED\n";
							$field['option_values'] .= "2:JARCHIVED\n";
							$field['option_values'] .= "-2:JTRASHED";
							
							$field['php_variable_type'] = 'int';
							$field['mysql_datatype'] = 'TINYINT';
							$field['mysql_size'] = '1';
							$field['mysql_default'] = "'0'";							
							$field['ordering'] = 5;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['list'];
							
							break;
						case 'access':
							$field['name'] = JText::_('JFIELD_ACCESS_LABEL');
							$field['description'] = JText::_('JFIELD_ACCESS_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '1';
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";								
							$field['ordering'] = 6;
							$field['php_variable_type'] = 'int';
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['accesslevel'];
							
							break;							
						case 'ordering':	
							$field['name'] = JText::_('JFIELD_ORDERING_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_ORDERING_DESC');
							$field['class'] = 'inputbox';
							$field['table'] = '#__com_'.JString::strtolower(str_replace('_','',$component->code_name)).'_'.str_replace('_','',$component_object->plural_code_name);
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '11';
							$field['mysql_default'] = "'0'";								
							$field['ordering'] = 7;
							$field['php_variable_type'] = 'int';
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;	
						case 'images':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGES_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGES_DESC');
							$field['mysql_datatype'] = 'TEXT';
							$field['ordering'] = 8;
							$field['fieldset_id'] = (int) $fieldset_lookup_array['image'];
							$field['fieldtype_id'] = $field_type_lookup['registry'];
							
							break;																						
						case 'image_url':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGEURL_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGEURL_DESC');
							$field['start_directory'] = JString::strtolower(str_replace('_','',$component->code_name));
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] ='200';	
							$field['hide_none'] = 1;							
							$field['ordering'] = 9;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['image'];
							$field['fieldtype_id'] = $field_type_lookup['media'];
							$field['registry_field_id'] = $field_lookup_array['images'];
							
							break;
						case 'image_alt_text':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGEALTTEXT_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGEALTTEXT_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';								
							$field['ordering'] = 10;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['image'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['images'];
							
							break;
						case 'image_caption':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGECAPTION_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_IMAGECAPTION_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';								
							$field['ordering'] = 11;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['image'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['images'];
							
							break;
						case 'intro_image_url':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_INTRO_IMAGEURL_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_INTRO_IMAGEURL_DESC');
							$field['start_directory'] = JString::strtolower(str_replace('_','',$component->code_name));
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] ='200';	
							$field['hide_none'] = 1;							
							$field['ordering'] = 12;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['image'];
							$field['fieldtype_id'] = $field_type_lookup['media'];
							$field['registry_field_id'] = $field_lookup_array['images'];
							
							break;
						case 'intro_image_alt_text':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_INTRO_IMAGEALTTEXT_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_INTRO_IMAGEALTTEXT_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';								
							$field['ordering'] = 13;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['image'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['images'];
							
							break;
						case 'intro_image_caption':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_INTRO_IMAGECAPTION_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_INTRO_IMAGECAPTION_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';								
							$field['ordering'] = 14;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['image'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['images'];
							
							break;															
						case 'urls':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLS_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLS_DESC');
							$field['mysql_datatype'] = 'TEXT';
							$field['ordering'] = 15;
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['registry'];
							
							break;	
						case 'urla':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLA_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';
							$field['registry_field_id'] = $field_lookup_array['urls'];
							$field['ordering'] = 16;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urla_text':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLA_LINK_TEXT_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_LINK_TEXT_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';
							$field['ordering'] = 17;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urla_target':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLA_TARGET_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_TARGET_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '1';
							$field['default'] = '1';
							$field['option_values'] = ":JGLOBAL_USE_GLOBAL\n";
							$field['option_values'] .= "0:JBROWSERTARGET_PARENT\n";
							$field['option_values'] .= "1:JBROWSERTARGET_NEW\n";
							$field['option_values'] .= "2:JBROWSERTARGET_POPUP\n";
							$field['option_values'] .= "3:JBROWSERTARGET_MODAL";
							
							$field['php_variable_type'] = 'int';
							$field['mysql_datatype'] = 'TINYINT';
							$field['mysql_size'] = '1';
							$field['mysql_default'] = "'0'";	
							$field['ordering'] = 18;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['list'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urlb':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLB_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';
							$field['ordering'] = 19;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urlb_text':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLB_LINK_TEXT_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_LINK_TEXT_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';
							$field['ordering'] = 20;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urlb_target':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLB_TARGET_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_TARGET_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '1';
							$field['default'] = '1';
							$field['option_values'] = ":JGLOBAL_USE_GLOBAL\n";
							$field['option_values'] .= "0:JBROWSERTARGET_PARENT\n";
							$field['option_values'] .= "1:JBROWSERTARGET_NEW\n";
							$field['option_values'] .= "2:JBROWSERTARGET_POPUP\n";
							$field['option_values'] .= "3:JBROWSERTARGET_MODAL";
							
							$field['php_variable_type'] = 'int';
							$field['mysql_datatype'] = 'TINYINT';
							$field['mysql_size'] = '1';
							$field['mysql_default'] = "'0'";	
							$field['ordering'] = 21;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['list'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urlc':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLC_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';
							$field['ordering'] = 22;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urlc_text':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLC_LINK_TEXT_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_LINK_TEXT_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';
							$field['ordering'] = 23;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;	
						case 'urlc_target':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URLC_TARGET_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_URL_TARGET_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '1';
							$field['default'] = '1';
							$field['option_values'] = ":JGLOBAL_USE_GLOBAL\n";
							$field['option_values'] .= "0:JBROWSERTARGET_PARENT\n";
							$field['option_values'] .= "1:JBROWSERTARGET_NEW\n";
							$field['option_values'] .= "2:JBROWSERTARGET_POPUP\n";
							$field['option_values'] .= "3:JBROWSERTARGET_MODAL";
							
							$field['php_variable_type'] = 'int';
							$field['mysql_datatype'] = 'TINYINT';
							$field['mysql_size'] = '1';
							$field['mysql_default'] = "'0'";	
							$field['ordering'] = 24;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['urls'];
							$field['fieldtype_id'] = $field_type_lookup['list'];
							$field['registry_field_id'] = $field_lookup_array['urls'];
							
							break;
						case 'featured':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_FEATURED_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_FEATURED_DESC');
							$field['class'] = 'inputbox';
							$field['default'] = '0';
							$field['mysql_datatype'] = 'TINYINT';
							$field['mysql_size'] = '3';
							$field['mysql_default'] = "'0'";								
							$field['ordering'] = 25;
							$field['php_variable_type'] = 'int';
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['checkbox'];
							
							break;
						case 'hits':
							$field['name'] = JText::_('JGLOBAL_HITS');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_HITS_DESC');
							$field['class'] = 'inputbox readonly';
							$field['size'] = '6';
							$field['filter'] = 'unset';	
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";	
							$field['readonly'] = 1;
							$field['ordering'] = 26;
							$field['php_variable_type'] = 'int';
							
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;
						case 'language':						
							$field['name'] = JText::_('JFIELD_LANGUAGE_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_LANGUAGE_DESC');
							$field['class'] = 'inputbox';
							$field['default'] = '*';
							$field['option_values'] = '*:JALL';
							$field['mysql_datatype'] = 'CHAR';
							$field['mysql_size'] = '7';
							$field['mysql_default'] = "'*'";								
							$field['ordering'] = 27;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['contentlanguage'];
							
							break;
						case 'version':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_VERSION_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_VERSION_DESC');
							$field['class'] = 'inputbox readonly';
							$field['size'] = '6';
							$field['filter'] = 'unset';	
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";	
							$field['readonly'] = 1;
							$field['ordering'] = 28;
							$field['php_variable_type'] = 'int';
							
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;	
						case 'description':
							$field['name'] = JText::_('JGLOBAL_DESCRIPTION');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_DESCRIPTION_DESC');
							$field['class'] = 'inputbox';
							$field['columns'] = '50';
							$field['rows'] = '5';
							$field['filter'] = 'safe_editor';	
							$field['buttons'] = '*';
							$field['hide_buttons'] = 'article,pagebreak';
							$field['search'] = 1;
							$field['ordering'] = 29;
							
							$field['mysql_datatype'] = 'MEDIUMTEXT';
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['editor'];
							
							break;							
						case 'intro':	
							$field['name'] = JText::_('JGLOBAL_INTRO_TEXT');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_INTRO_DESC');
							$field['class'] = 'inputbox hidden';
							$field['columns'] = '50';
							$field['rows'] = '5';
							$field['filter'] = 'safe_editor';	
							$field['buttons'] = '*';
							$field['hide_buttons'] = 'article,pagebreak';
							$field['search'] = 1;
							$field['readonly'] = 1;
							$field['hidden'] = 1;
							$field['ordering'] = 30;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['basic_details'];
							$field['fieldtype_id'] = $field_type_lookup['editor'];
							
							break;													
						case 'created':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CREATED_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CREATED_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '22';
							$field['filter'] = 'user_utc';
							$field['format'] = '%Y-%m-%d %H:%M:%S';
							$field['mysql_datatype'] = 'DATETIME';
							$field['mysql_default'] = "'0000-00-00 00:00:00'";								
							$field['ordering'] = 31;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['calendar'];
							
							break;
						case 'created_by':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CREATED_BY_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CREATED_BY_DESC');
							$field['filter'] = 'unset';
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";							
							$field['ordering'] = 32;
							$field['php_variable_type'] = 'int';
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['user'];
							
							break;	
						case 'created_by_alias':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CREATED_BY_ALIAS_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CREATED_BY_ALIAS_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '255';
							$field['ordering'] = 33;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;																																												
						case 'modified':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_MODIFIED_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_MODIFIED_DESC');
							$field['class'] = 'readonly';
							$field['size'] = '22';
							$field['filter'] = 'user_utc';
							$field['format'] = '%Y-%m-%d %H:%M:%S';
							$field['mysql_datatype'] = 'DATETIME';
							$field['mysql_default'] = "'0000-00-00 00:00:00'";
							$field['readonly'] = 1;							
							$field['ordering'] = 34;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['calendar'];
							
							break;
						case 'modified_by':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_MODIFIED_BY_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_MODIFIED_BY_DESC');
							$field['class'] = 'readonly';
							$field['filter'] = 'unset';
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";							
							$field['readonly'] = 1;							
							$field['ordering'] = 35;
							$field['php_variable_type'] = 'int';
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['user'];
							
							break;	
						case 'publish_up':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_PUBLISH_UP_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_PUBLISH_UP_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '22';
							$field['filter'] = 'user_utc';
							$field['format'] = '%Y-%m-%d %H:%M:%S';
							$field['mysql_datatype'] = 'DATETIME';
							$field['mysql_default'] = "'0000-00-00 00:00:00'";							
							$field['ordering'] = 36;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['calendar'];
							
							break;																																			
						case 'publish_down':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_PUBLISH_DOWN_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_PUBLISH_DOWN_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '22';
							$field['filter'] = 'user_utc';
							$field['format'] = '%Y-%m-%d %H:%M:%S';
							$field['mysql_datatype'] = 'DATETIME';
							$field['mysql_default'] = "'0000-00-00 00:00:00'";							
							$field['ordering'] = 37;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['calendar'];
							
							break;																																										
						case 'metakey':
							$field['name'] = JText::_('JFIELD_META_KEYWORDS_LABEL');
							$field['description'] = JText::_('JFIELD_META_KEYWORDS_DESC');
							$field['class'] = 'inputbox';
							$field['cols'] = '35';
							$field['rows'] = '3';
							$field['mysql_datatype'] = 'TEXT';
							$field['ordering'] = 38;
							
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['metadata'];
							$field['fieldtype_id'] = $field_type_lookup['textarea'];
							
							break;	
						case 'metadesc':
							$field['name'] = JText::_('JFIELD_META_DESCRIPTION_LABEL');
							$field['description'] = JText::_('JFIELD_META_DESCRIPTION_DESC');
							$field['class'] = 'inputbox';
							$field['cols'] = '35';
							$field['rows'] = '3';
							$field['mysql_datatype'] = 'TEXT';							
							$field['ordering'] = 39;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['metadata'];
							$field['fieldtype_id'] = $field_type_lookup['textarea'];
							
							break;
						case 'robots':						
							$field['name'] = JText::_('JFIELD_METADATA_ROBOTS_LABEL');
							$field['description'] = JText::_('JFIELD_METADATA_ROBOTS_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '50';							
							$field['ordering'] = 40;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['metadata'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;	
						case 'author':						
							$field['name'] = JText::_('JAUTHOR');
							$field['description'] = JText::_('JFIELD_METADATA_AUTHOR_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '20';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '20';							
							$field['ordering'] = 41;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['metadata'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;														
						case 'xreference':						
							$field['name'] = JText::_('JFIELD_KEY_REFERENCE_LABEL');
							$field['description'] = JText::_('JFIELD_KEY_REFERENCE_DESC');
							$field['class'] = 'inputbox';
							$field['size'] = '50';
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '50';							
							$field['ordering'] = 42;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['metadata'];
							$field['fieldtype_id'] = $field_type_lookup['text'];
							
							break;
						case 'checked_out':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CHECKED_OUT_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CHECKED_OUT_DESC');
							$field['filter'] = 'unset';
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";								
							$field['ordering'] = 43;
							$field['hidden'] = 1;

							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['hidden'];
							
							break;
						case 'checked_out_time':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CHECKED_OUT_TIME_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_CHECKED_OUT_TIME_DESC');
							$field['filter'] = 'unset';
							$field['mysql_datatype'] = 'DATETIME';
							$field['mysql_default'] = "'0000-00-00 00:00:00'";	
							$field['ordering'] = 44;
							$field['hidden'] = 1;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['publishing'];
							$field['fieldtype_id'] = $field_type_lookup['hidden'];
							
							break;								
						case 'asset_id':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_ASSET_ID_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_ASSET_ID_DESC');
							$field['filter'] = 'unset';
							$field['mysql_datatype'] = 'INT';
							$field['mysql_size'] = '10';
							$field['mysql_default'] = "'0'";
							$field['ordering'] = 45;
							$field['hidden'] = 1;
							
							$field['php_variable_type'] = 'int';
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['admin'];
							$field['fieldtype_id'] = $field_type_lookup['hidden'];
							
							break;																																																		
						case 'params':
							$field['name'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_PARAMS_LABEL');
							$field['description'] = JText::_('PLG_COMPONENTARCHITECT_EVENTS_FIELD_PARAMS_DESC');
							$field['mysql_datatype'] = 'VARCHAR';
							$field['mysql_size'] = '5120';								
							$field['ordering'] = 46;
							
							$field['fieldset_id'] = (int) $fieldset_lookup_array['admin'];
							$field['fieldtype_id'] = $field_type_lookup['registry'];
							
							break;																				
					}
					$field['id'] = 0;
					$field['code_name'] = $field_code_name;
					$field['component_id'] = $component_object->component_id;
					$field['component_object_id'] = $component_object->id;
					$field['language'] = '*';				
					$field['state'] = 1;
					$field['access'] = 1;
					$field['predefined_field'] = 1;
					
					// Slight fudge because in Admin save in FieldModel but in the Site in FieldFormModel
					// Also have to do a getState(field.id) twice because the first time can set the value to the Componentobject id (in JModelAdmin::populatestate)
					if (JFactory::getApplication()->isSite())
					{
						$field_model = JModelLegacy::getInstance('fieldform', 'ComponentarchitectModel');
					}
					else
					{
						$field_model = JModelLegacy::getInstance('field', 'ComponentarchitectModel');
					}

					if($field_model->save($field))
					{
						$result = true;
					}
					else
					{
						$result = false;
					}
					
					if ($field['code_name'] == 'urls' AND $result)
					{
						$field_lookup_array['urls'] = (int) $field_model->getState('field.id',0);
					}
					if ($field['code_name'] == 'images' AND $result)
					{
						$field_lookup_array['images'] = (int) $field_model->getState('field.id',0);
					}						
				}				
			}			
		}
		//[%%END_CUSTOM_CODE%%]		
		return true;
	}
	
	/**
	 * Before delete object/table method - dummy entry
	 *
	 * @context				string		The context for the object/table passed to the plugin.
	 * @componentobject			object		The data relating to the object/table that is to be deleted.
	 * @isNew				boolean		If the object/table is just created
	 * @return				boolean	
	 */
	public function onComponentObjectBeforeDelete($context, $component_object)
	{
		return true;
	}
	
	/**
	 * After delete object/table method - dummy entry
	 *
	 * @context				string	The context for the object/table passed to the plugin.
	 * @componentobject			object	The data relating to the object/table that was deleted.
	 * @return				boolean
	 */
	public function onComponentObjectAfterDelete($context, $component_object)
	{
		//[%%START_CUSTOM_CODE%%]
		// Remove fieldsets when component object deleted
		$fieldsets_model = JModelLegacy::getInstance('fieldsets', 'ComponentArchitectModel', array('ignore_request' => true));
		$fieldsets_model->setState('filter.component_object_id', $component_object->id);
		$fieldsets_model->setState('list.ordering', 'a.ordering');
		$fieldsets_model->setState('list.direction', 'ASC');
		$fieldsets_model->setState('list.select', 'a.*');
		
		
		$fieldsets = $fieldsets_model->getItems();

		$fieldset_model = JModelLegacy::getInstance('fieldset', 'ComponentArchitectModel');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		foreach ($fieldsets as $fieldset)
		{			
			try
			{
				// Deleting the child fieldsets for this component object
				$query->clear();
				$query->delete('#__componentarchitect_fieldsets');
				$query->where('id = '.$fieldset->id);
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}		
			if (!$this->onFieldsetAfterDelete($context, $fieldset))
			{
				return false;
			}
		}
		//[%%END_CUSTOM_CODE%%]	
		return true;
	}
	/**
	 * Prepare object/table method - dummy entry
	 *
	 * @context				string	The context for the object/table passed to the plugin.
	 * @componentobject			object	The data relating to the object/table.
	 * @params				array	Array holding the params for the current view and object/table.
	 * @offset				integer	Offset of this object/table in a list.
	 * @return				string
	 */		
	public function onComponentObjectPrepare($context, $row, &$params, $offset=0)
	{
		// Display during .

		return '';
		
	}
	
	/**
	 * After name object/table method - dummy entry
	 *
	 * @context				string	The context for the object/table passed to the plugin.
	 * @componentobject			object	The data relating to the object/table.
	 * @params				array	Array holding the params for the current view and object/table.
	 * @offset				integer	Offset of this object/table in a list.
	 * @return				string
	 */		
	public function onComponentObjectAfterName($context, $row, &$params, $offset=0)
	{
		// Display after name is output.

		return '';
		
	}
	
	/**
	 * Before display object/table method - dummy entry
	 *
	 * @context				string	The context for the object/table passed to the plugin.
	 * @componentobject			object	The data relating to the object/table.
	 * @params				array	Array holding the params for the current view and object/table.
	 * @offset				integer	Offset of this object/table in a list.
	 * @return				string
	 */		
	public function onComponentObjectBeforeDisplay($context, $row, &$params, $offset=0)
	{
		
		// Display before component.

		return '';
	}
	
	/**
	 * After display object/table method - dummy entry
	 *
	 * @context				string	The context for the object/table passed to the plugin.
	 * @componentobject			object	The data relating to the object/table.
	 * @params				array	Array holding the params for the current view and object/table.
	 * @offset				integer	Offset of this object/table in a list.
	 * @return				string
	 */		
	public function onComponentObjectAfterDisplay($context, $row, &$params, $offset=0)
	{
		// Display after component.

		return '';
		
	}
	/**
	 * Before save fieldset method - dummy entry
	 * Fieldset is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the fieldset passed to the plugin (added in 1.6)
	 * @fieldset			object		The data relating to the fieldset that was saved
	 * @isNew				bool		If the fieldset is just about to be created
	 * @return				boolean	
	 */
	public function onFieldsetBeforeSave($context, $fieldset, $is_new)
	{	
		//[%%START_CUSTOM_CODE%%]
		$view = JRequest::getCmd('view');

		// Cannot change the fieldset if it is predefined.  When new record it is not checked.
		if ($fieldset->predefined_fieldset AND $fieldset->id != 0 AND ($view == 'fieldset' OR $view == 'fieldsets'))
		{
			$fieldset->setError(JText::_('COM_COMPONENTARCHITECT_FIELDSETS_ERROR_PREDEFINED_FIELDSET_CANNOT_CHANGE'));
			return false;				
		}
		else
		{	
			return true;
		}
		//[%%END_CUSTOM_CODE%%]	
	}
	
	/**
	 * After save fieldset method - dummy entry
	 * Fieldset is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the fieldset passed to the plugin (added in 1.6)
	 * @fieldset			object		The data relating to the fieldset that was saved
	 * @isNew				boolean		If the fieldset is just about to be created
	 * @return				boolean	
	 */
	public function onFieldsetAfterSave($context, $fieldset, $is_new)
	{	
		return true;
	}
	
	/**
	 * Before delete fieldset method - dummy entry
	 *
	 * @context				string		The context for the fieldset passed to the plugin.
	 * @fieldset			object		The data relating to the fieldset that is to be deleted.
	 * @isNew				boolean		If the fieldset is just created
	 * @return				boolean	
	 */
	public function onFieldsetBeforeDelete($context, $fieldset)
	{
		return true;
	}
	
	/**
	 * After delete fieldset method - dummy entry
	 *
	 * @context				string	The context for the fieldset passed to the plugin.
	 * @fieldset			object	The data relating to the fieldset that was deleted.
	 * @return				boolean
	 */
	public function onFieldsetAfterDelete($context, $fieldset)
	{
		//[%%START_CUSTOM_CODE%%]
		// Remove fields when fieldset deleted
		$fields_model = JModelLegacy::getInstance('fields', 'ComponentArchitectModel', array('ignore_request' => true));
		$fields_model->setState('filter.fieldset_id', $fieldset->id);
		$fields_model->setState('list.ordering', 'a.ordering');
		$fields_model->setState('list.direction', 'ASC');
		$fields_model->setState('list.select', 'a.*');
		
		
		$fields = $fields_model->getItems();

		$field_model = JModelLegacy::getInstance('field', 'ComponentArchitectModel');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		foreach ($fields as $field)
		{			
			try
			{
				// Deleting the child fields for this fieldset
				$query->clear();
				$query->delete('#__componentarchitect_fields');
				$query->where('id = '.$field->id);
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
		}	
		//[%%END_CUSTOM_CODE%%]	
		return true;
	}
	/**
	 * Prepare fieldset method - dummy entry
	 *
	 * @context				string	The context for the fieldset passed to the plugin.
	 * @fieldset			object	The data relating to the fieldset.
	 * @params				array	Array holding the params for the current view and fieldset.
	 * @offset				integer	Offset of this fieldset in a list.
	 * @return				string
	 */		
	public function onFieldsetPrepare($context, $row, &$params, $offset=0)
	{
		// Display during .

		return '';
		
	}
	
	/**
	 * After name fieldset method - dummy entry
	 *
	 * @context				string	The context for the fieldset passed to the plugin.
	 * @fieldset			object	The data relating to the fieldset.
	 * @params				array	Array holding the params for the current view and fieldset.
	 * @offset				integer	Offset of this fieldset in a list.
	 * @return				string
	 */		
	public function onFieldsetAfterName($context, $row, &$params, $offset=0)
	{
		// Display after name is output.

		return '';
		
	}
	
	/**
	 * Before display fieldset method - dummy entry
	 *
	 * @context				string	The context for the fieldset passed to the plugin.
	 * @fieldset			object	The data relating to the fieldset.
	 * @params				array	Array holding the params for the current view and fieldset.
	 * @offset				integer	Offset of this fieldset in a list.
	 * @return				string
	 */		
	public function onFieldsetBeforeDisplay($context, $row, &$params, $offset=0)
	{
		
		// Display before component.

		return '';
	}
	
	/**
	 * After display fieldset method - dummy entry
	 *
	 * @context				string	The context for the fieldset passed to the plugin.
	 * @fieldset			object	The data relating to the fieldset.
	 * @params				array	Array holding the params for the current view and fieldset.
	 * @offset				integer	Offset of this fieldset in a list.
	 * @return				string
	 */		
	public function onFieldsetAfterDisplay($context, $row, &$params, $offset=0)
	{
		// Display after component.

		return '';
		
	}
	/**
	 * Before save field method - dummy entry
	 * Field is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the field passed to the plugin (added in 1.6)
	 * @field			object		The data relating to the field that was saved
	 * @isNew				bool		If the field is just about to be created
	 * @return				boolean	
	 */
	public function onFieldBeforeSave($context, $field, $is_new)
	{	
		//[%%START_CUSTOM_CODE%%]
		$view = JRequest::getCmd('view');

		// Cannot change the field if it is predefined.  When new record it is not checked.
		if ($field->predefined_field AND $field->id != 0 AND ($view == 'field' OR $view == 'fields'))
		{
			$field->setError(JText::_('COM_COMPONENTARCHITECT_FIELDS_ERROR_PREDEFINED_FIELD_CANNOT_CHANGE'));
			return false;				
		}
		else
		{	
			return true;
		}
		//[%%END_CUSTOM_CODE%%]
	}
	
	/**
	 * After save field method - dummy entry
	 * Field is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the field passed to the plugin (added in 1.6)
	 * @field			object		The data relating to the field that was saved
	 * @isNew				boolean		If the field is just about to be created
	 * @return				boolean	
	 */
	public function onFieldAfterSave($context, $field, $is_new)
	{	
		return true;
	}
	
	/**
	 * Before delete field method - dummy entry
	 *
	 * @context				string		The context for the field passed to the plugin.
	 * @field			object		The data relating to the field that is to be deleted.
	 * @isNew				boolean		If the field is just created
	 * @return				boolean	
	 */
	public function onFieldBeforeDelete($context, $field)
	{
		return true;
	}
	
	/**
	 * After delete field method - dummy entry
	 *
	 * @context				string	The context for the field passed to the plugin.
	 * @field			object	The data relating to the field that was deleted.
	 * @return				boolean
	 */
	public function onFieldAfterDelete($context, $field)
	{
		return true;
	}
	/**
	 * Prepare field method - dummy entry
	 *
	 * @context				string	The context for the field passed to the plugin.
	 * @field			object	The data relating to the field.
	 * @params				array	Array holding the params for the current view and field.
	 * @offset				integer	Offset of this field in a list.
	 * @return				string
	 */		
	public function onFieldPrepare($context, $row, &$params, $offset=0)
	{
		// Display during .

		return '';
		
	}
	
	/**
	 * After name field method - dummy entry
	 *
	 * @context				string	The context for the field passed to the plugin.
	 * @field			object	The data relating to the field.
	 * @params				array	Array holding the params for the current view and field.
	 * @offset				integer	Offset of this field in a list.
	 * @return				string
	 */		
	public function onFieldAfterName($context, $row, &$params, $offset=0)
	{
		// Display after name is output.

		return '';
		
	}
	
	/**
	 * Before display field method - dummy entry
	 *
	 * @context				string	The context for the field passed to the plugin.
	 * @field			object	The data relating to the field.
	 * @params				array	Array holding the params for the current view and field.
	 * @offset				integer	Offset of this field in a list.
	 * @return				string
	 */		
	public function onFieldBeforeDisplay($context, $row, &$params, $offset=0)
	{
		
		// Display before component.

		return '';
	}
	
	/**
	 * After display field method - dummy entry
	 *
	 * @context				string	The context for the field passed to the plugin.
	 * @field			object	The data relating to the field.
	 * @params				array	Array holding the params for the current view and field.
	 * @offset				integer	Offset of this field in a list.
	 * @return				string
	 */		
	public function onFieldAfterDisplay($context, $row, &$params, $offset=0)
	{
		// Display after component.

		return '';
		
	}
	/**
	 * Before save field type method - dummy entry
	 * Field Type is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the field type passed to the plugin (added in 1.6)
	 * @fieldtype			object		The data relating to the field type that was saved
	 * @isNew				bool		If the field type is just about to be created
	 * @return				boolean	
	 */
	public function onFieldTypeBeforeSave($context, $field_type, $is_new)
	{	
		return true;
	}
	
	/**
	 * After save field type method - dummy entry
	 * Field Type is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the field type passed to the plugin (added in 1.6)
	 * @fieldtype			object		The data relating to the field type that was saved
	 * @isNew				boolean		If the field type is just about to be created
	 * @return				boolean	
	 */
	public function onFieldTypeAfterSave($context, $field_type, $is_new)
	{	
		return true;
	}
	
	/**
	 * Before delete field type method - dummy entry
	 *
	 * @context				string		The context for the field type passed to the plugin.
	 * @fieldtype			object		The data relating to the field type that is to be deleted.
	 * @isNew				boolean		If the field type is just created
	 * @return				boolean	
	 */
	public function onFieldTypeBeforeDelete($context, $field_type)
	{
		return true;
	}
	
	/**
	 * After delete field type method - dummy entry
	 *
	 * @context				string	The context for the field type passed to the plugin.
	 * @fieldtype			object	The data relating to the field type that was deleted.
	 * @return				boolean
	 */
	public function onFieldTypeAfterDelete($context, $field_type)
	{
		return true;
	}
	/**
	 * Prepare field type method - dummy entry
	 *
	 * @context				string	The context for the field type passed to the plugin.
	 * @fieldtype			object	The data relating to the field type.
	 * @params				array	Array holding the params for the current view and field type.
	 * @offset				integer	Offset of this field type in a list.
	 * @return				string
	 */		
	public function onFieldTypePrepare($context, $row, &$params, $offset=0)
	{
		// Display during .

		return '';
		
	}
	
	/**
	 * After name field type method - dummy entry
	 *
	 * @context				string	The context for the field type passed to the plugin.
	 * @fieldtype			object	The data relating to the field type.
	 * @params				array	Array holding the params for the current view and field type.
	 * @offset				integer	Offset of this field type in a list.
	 * @return				string
	 */		
	public function onFieldTypeAfterName($context, $row, &$params, $offset=0)
	{
		// Display after name is output.

		return '';
		
	}
	
	/**
	 * Before display field type method - dummy entry
	 *
	 * @context				string	The context for the field type passed to the plugin.
	 * @fieldtype			object	The data relating to the field type.
	 * @params				array	Array holding the params for the current view and field type.
	 * @offset				integer	Offset of this field type in a list.
	 * @return				string
	 */		
	public function onFieldTypeBeforeDisplay($context, $row, &$params, $offset=0)
	{
		
		// Display before component.

		return '';
	}
	
	/**
	 * After display field type method - dummy entry
	 *
	 * @context				string	The context for the field type passed to the plugin.
	 * @fieldtype			object	The data relating to the field type.
	 * @params				array	Array holding the params for the current view and field type.
	 * @offset				integer	Offset of this field type in a list.
	 * @return				string
	 */		
	public function onFieldTypeAfterDisplay($context, $row, &$params, $offset=0)
	{
		// Display after component.

		return '';
		
	}
	/**
	 * Before save code template method - dummy entry
	 * Code Template is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the code template passed to the plugin (added in 1.6)
	 * @codetemplate			object		The data relating to the code template that was saved
	 * @isNew				bool		If the code template is just about to be created
	 * @return				boolean	
	 */
	public function onCodeTemplateBeforeSave($context, $code_template, $is_new)
	{	
		return true;
	}
	
	/**
	 * After save code template method - dummy entry
	 * Code Template is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @context				string		The context of the code template passed to the plugin (added in 1.6)
	 * @codetemplate			object		The data relating to the code template that was saved
	 * @isNew				boolean		If the code template is just about to be created
	 * @return				boolean	
	 */
	public function onCodeTemplateAfterSave($context, $code_template, $is_new)
	{	
		return true;
	}
	
	/**
	 * Before delete code template method - dummy entry
	 *
	 * @context				string		The context for the code template passed to the plugin.
	 * @codetemplate			object		The data relating to the code template that is to be deleted.
	 * @isNew				boolean		If the code template is just created
	 * @return				boolean	
	 */
	public function onCodeTemplateBeforeDelete($context, $code_template)
	{
		return true;
	}
	
	/**
	 * After delete code template method - dummy entry
	 *
	 * @context				string	The context for the code template passed to the plugin.
	 * @codetemplate			object	The data relating to the code template that was deleted.
	 * @return				boolean
	 */
	public function onCodeTemplateAfterDelete($context, $code_template)
	{
		return true;
	}
	/**
	 * Prepare code template method - dummy entry
	 *
	 * @context				string	The context for the code template passed to the plugin.
	 * @codetemplate			object	The data relating to the code template.
	 * @params				array	Array holding the params for the current view and code template.
	 * @offset				integer	Offset of this code template in a list.
	 * @return				string
	 */		
	public function onCodeTemplatePrepare($context, $row, &$params, $offset=0)
	{
		// Display during .

		return '';
		
	}
	
	/**
	 * After name code template method - dummy entry
	 *
	 * @context				string	The context for the code template passed to the plugin.
	 * @codetemplate			object	The data relating to the code template.
	 * @params				array	Array holding the params for the current view and code template.
	 * @offset				integer	Offset of this code template in a list.
	 * @return				string
	 */		
	public function onCodeTemplateAfterName($context, $row, &$params, $offset=0)
	{
		// Display after name is output.

		return '';
		
	}
	
	/**
	 * Before display code template method - dummy entry
	 *
	 * @context				string	The context for the code template passed to the plugin.
	 * @codetemplate			object	The data relating to the code template.
	 * @params				array	Array holding the params for the current view and code template.
	 * @offset				integer	Offset of this code template in a list.
	 * @return				string
	 */		
	public function onCodeTemplateBeforeDisplay($context, $row, &$params, $offset=0)
	{
		
		// Display before component.

		return '';
	}
	
	/**
	 * After display code template method - dummy entry
	 *
	 * @context				string	The context for the code template passed to the plugin.
	 * @codetemplate			object	The data relating to the code template.
	 * @params				array	Array holding the params for the current view and code template.
	 * @offset				integer	Offset of this code template in a list.
	 * @return				string
	 */		
	public function onCodeTemplateAfterDisplay($context, $row, &$params, $offset=0)
	{
		// Display after component.

		return '';
		
	}
}

