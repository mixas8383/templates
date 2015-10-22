<?php
/**
 * @version 		$Id:$
 * @name			Slider (Release 1.0.0)
 * @author			 ()
 * @package			com_slider
 * @subpackage		com_slider.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobject.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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
 * Item model.
 *
 */
class SliderModelItem extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_SLIDER_ITEMS';
	/**
	 * @var      string	The type alias for this object (for example, 'com_slider.item')
	 */
	public $typeAlias = 'com_slider.item';	
	/**
	 * @var		string	The context for the app call.
	 */
	protected $context = 'com_slider.items';	
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
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Items', $prefix = 'SliderTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}	
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
			return $user->authorise('core.delete', 'com_slider.item.'.(int) $record->id);
		}
		else
		{
			return $user->authorise('core.delete', 'com_slider');
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
			return $user->authorise('core.edit.state', 'com_slider.item.'.(int) $record->id);
		}
		else
		{
			// Default to component settings.			
			return parent::canEditState($record);
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
		if ($item = parent::getItem($pk))
		{
			// Include any manipulation of the data on the record e.g. expand out Registry fields
			// NB The params registry field - if used - is done automatically in the JAdminModel parent class
			

			
			// Convert the images field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->images);
			$item->images = $registry->toArray();
			$registry = null; //release memory	

			// Convert the urls field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->urls);
			$item->urls = $registry->toArray();
			$registry = null; //release memory	
			
			$item->introdescription = trim($item->intro) != '' ? $item->intro . "<hr id=\"system-readmore\" />" . $item->description : $item->description;
			
			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, 'com_slider.item');
			}	
		}
		
		// Load associated content items
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_slider', '#__slider_items', 'com_slider.item.item', $item->id, 'id', 'alias', null);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}

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
		$form = $this->loadForm('com_slider.edit.item', 'item', array('control' => 'jform', 'load_data' => $load_data));
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

		// Prevent messing with Item language and category when editing existing Item with associations
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($id AND $app->isSite() AND $assoc)
		{
			$form->setFieldAttribute('language', 'readonly', 'true');
			$form->setFieldAttribute('language', 'filter', 'unset');
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
		$data = $app->getUserState('com_slider.edit.item.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
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
		$table->alias = JApplication::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = $this->generateUniqueAlias((array) $table);
		}
		
		// Set the publish date to now
		if($table->state == 1 )
		{
			if ((int) $table->publish_up == 0)
			{
				$table->publish_up = JFactory::getDate()->toSQL();
			}
			 
			if (intval($table->publish_down) == 0)
			{
				$table->publish_down = $db->getNullDate();
			}				
		}
		
		// Increment the item version number.
		$table->version++;
		
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
		// Include the slider plugins for the onSave events.
		JPluginHelper::importPlugin('slider');	
		
		$app = JFactory::getApplication();
		


		if (isset($data['images']) AND is_array($data['images']))
		{
			$registry = new JRegistry;
			$registry->loadArray($data['images']);
			$data['images'] = (string) $registry;
			$registry = null; //release memory	
		}

		if (isset($data['urls']) AND is_array($data['urls']))
		{

			foreach ($data['urls'] as $i => $url)
			{
				if ($url != false AND ($i == 'urla' OR $i == 'urlb' OR $i == 'urlc'))
				{
					$data['urls'][$i] = JStringPunycode::urlToPunycode($url);
				}

			}
			$registry = new JRegistry;
			$registry->loadArray($data['urls']);
			$data['urls'] = (string) $registry;
			$registry = null; //release memory	
		}	

		// Alter the name and alias for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			$data['name'] = $this->generateUniqueName($data);
			$data['alias']	= $this->generateUniqueAlias($data);
			$data['state'] = 0;
		}

		if (parent::save($data))
		{
			if (isset($data['featured']))
			{
				$this->featured($this->getState($this->getName().'.id'), $data['featured']);
			}

			$assoc =  JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$id = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language AND !empty($associations))
				{
					JError::raiseNotice(403, JText::_('COM_SLIDER_ERROR_ALL_LANGUAGE_ASSOCIATED'));
				}

				$associations[$item->language] = $item->id;
				try
				{
					// Deleting old association for these items
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->delete($db->quoteName('#__associations'));
					$query->where('context='.$db->quote('com_slider.item.item'));
					$query->where($db->quoteName('id').' IN ('.implode(',', $associations).')');
					$db->setQuery($query);
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					$this->setError($e->getMessage());
					return false;
				}

				if (!$all_language AND count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear();
					$query->insert($db->quoteName('#__associations'));

					foreach ($associations as $id)
					{
						$query->values($id.','.$db->quote('com_slider.item.item') . ',' . $db->quote($key));
					}

					try
					{
						$db->setQuery($query);
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						return false;
					}					
				}
			}

			return true;
		}

		return false;
	}	
	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param	integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 */
	public function publish(&$pks, $value = 1)
	{	
		// Include the slider plugins for the change of state event.
		JPluginHelper::importPlugin('slider');	
		
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
		// Include the slider plugins for the delete events.
		JPluginHelper::importPlugin('slider');	
		
		return parent::delete($pks);	
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
		$condition[] = $db->quoteName('state').' >= 0';
		return $condition;
	}


	/**
	 * Method to toggle the featured setting of items.
	 *
	 * @param	array	$pks	The ids of the items to toggle.
	 * @param	integer		$value	The value to toggle to.
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
			$this->setError(JText::_('COM_SLIDER_ITEMS_NO_ITEM_SELECTED'));
			return false;
		}

		try
		{
			$db = $this->getDbo();

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__slider_items'));
			$query->set($db->quoteName('featured').' = ' . (int) $value);
			$query->where($db->quoteName('id').' IN (' . implode(',', $pks) . ')');
			
			$db->setQuery($query);
						
			$db->execute();

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$table = $this->getTable();		
		$conditions_array = $this->getReorderConditions($table);
		
		$conditions = implode(' AND ', $conditions_array);				
		$table->reorder($conditions);

		// Clean component's cache
		$this->cleanCache();

		return true;
	}
	/**
	 * Custom clean the cache of com_slider and slider modules
	 *
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_slider');

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

		// Set some needed variables.
		$this->user = JFactory::getUser();
		$this->table = $this->getTable();
		$this->tableClassName = get_class($this->table);
		$this->contentType = new JUcmType;
		$this->type = $this->contentType->getTypeByTable($this->tableClassName);
		$this->batchSet = true;

		if ($this->type === false)
		{
			$type = new JUcmType;
			$this->type = $type->getTypeByAlias($this->typeAlias);
			$type_alias = $this->type->type_alias;
		}
		else
		{
			$type_alias = $this->type->type_alias;
		}

		$this->tagsObserver = $this->table->getObserverOfClass('JTableObserverTags');

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
		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}
		if (!empty($commands['tag']))
        {
                if (!$this->batchTag($commands['tag'], $pks, $contexts))
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
	 * @param	integer  $value     Dummy to match the category in the JModelAdmin calls.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$i = 0;
		$new_ids = array();

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
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
			$this->table->id = 0;

			// Reset hits because we are making a copy
			$this->table->hits = 0;
			
			$this->table->alias = $this->generateUniqueAlias((array) $this->table);			
			$this->table->name = $this->generateUniqueName((array) $this->table);
			// Set ordering to 0 so it is forced to be set later to last position
			$this->table->ordering = 0;
			$this->prepareTable($this->table);


			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());
				return false;
			}

			parent::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			
			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());
				return false;
			}

			// Get the new item ID
			$new_id = $this->table->get('id');

			// Add the new ID to the array
			$new_ids[$i]	= $new_id;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $new_ids;
	}
	
	/**
	* Method to get a unique name.
	*
	* @param   array   $data	The data where the original name is stored
	*
	* @return	string  $name	The modified name.
	*
	*/
	protected function generateUniqueName($data)
	{
		$table = $this->getTable();		
		
		$key_array = array('name' => $data['name']);
		
		// Alter the name
		while ($table->load($key_array))
		{
			$key_array['name'] = JString::increment($key_array['name']);
		}
		
		return htmlspecialchars_decode($key_array['name'], ENT_QUOTES);
	}
	/**
	 * Method to get a unique alias.
	 *
	* @param   array   $data	The data where the original name is stored
	 *
	 * @return	string  $alias		The modified alias.
	 *
	 */
	protected function generateUniqueAlias($data)
	{
		$table = $this->getTable();		

		// Alter the alias
		$key_array = array('alias' => JApplication::stringURLSafe($data['alias']));
		
		while ($table->load($key_array))
		{
			$key_array['alias'] = JString::increment($key_array['alias'], 'dash');
		}

		return $key_array['alias'];
	}
	/**
	 * Pre process the form to pick up items associated by language
	 *
	 * @param   object  $form		A form object
	 * @param   array	$data		The record data
	 * @param   string  $group		The association context.
	 * 
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void

	 */
	protected function preprocessForm(JForm $form, $data, $group = 'item')
	{
		// Association content items
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');

			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_SLIDER_ITEMS_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) OR $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_items');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
					
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}
}