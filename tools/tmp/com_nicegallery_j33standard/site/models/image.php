<?php
/**
 * @version 		$Id:$
 * @name			Nicegallery (Release 1.0.0)
 * @author			 ()
 * @package			com_nicegallery
 * @subpackage		com_nicegallery.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobject.php 423 2014-10-23 14:08:16Z BrianWade $
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
 * Nicegallery Component Image Model
 *
 */
class NicegalleryModelImage extends JModelItem
{
	/**
	 * Model context string.  Used in setting the store id for the session
	 *
	 * @var		string
	 */
	protected $context = 'com_nicegallery.image';

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * 
	 */
	public function __construct($config = array())
	{
		if (empty($config['image_filter_fields']))
		{
			$config['image_filter_fields'] = array(
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
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'ordering', 'a.ordering',
				);
		}

		parent::__construct($config);
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
			return $user->authorise('core.delete', 'com_nicegallery.image.'.(int) $record->id)
					  OR ($user->authorise('core.delete', 'com_nicegallery.image.'.(int) $record->id)
					  AND $record->created_by = $user->id);
		}
		return ;
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
			return $user->authorise('core.edit.state', 'com_nicegallery.image.'.(int) $record->id);
		}
		else
		{
			// New image, so check against the category.		
			if (!empty($record->catid))
			{
				return $user->authorise('core.edit.state', 'com_nicegallery.category.'.(int) $record->catid);
			}
			else 
			{
			// Default to component settings.			
				return $user->authorise('core.edit.state', 'com_nicegallery');
			}
		}
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('image.id', $pk);

		$offset = $app->input->getInt('limitstart');
		$this->setState('list.offset', $offset);

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

		// TODO: Tune these values based on other permissions.
		$user		= JFactory::getUser();
			
		if ((!$user->authorise('core.edit.state', 'com_nicegallery')) AND  (!$user->authorise('core.edit', 'com_nicegallery')))
		{
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}		

		if ($params->get('filter_image_archived'))
		{
			$this->setState('filter.archived', $params->get('filter_image_archived'));
			
		}
		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Images', $prefix = 'NicegalleryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get Image data.
	 *
	 * @param	integer	$pk	The id of the image.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Get current user for authorisation checks
		$user	= JFactory::getUser();
		
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('image.id');
		// Get the global params
		$global_params = JComponentHelper::getParams('com_nicegallery', true);

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select($this->getState(
					'item.select',
					'a.*'

					)
				);
				$query->from($db->quoteName('#__nicegallery_images').' AS a');
				
				// Join on category table.
				$query->select($db->quoteName('c.title').' AS category_title, '.
								$db->quoteName('c.alias').' AS category_alias, '.
								$db->quoteName('c.access').' AS category_access'
								);
				$query->join('LEFT', $db->quoteName('#__categories').' AS c on '.$db->quoteName('c.id').' = '.$db->quoteName('a.catid'));
				
				// Join over the categories to get parent category titles
				$query->select($db->quoteName('parent.title').' AS parent_title, '.
								$db->quoteName('parent.id').' AS parent_id, '.
								$db->quoteName('parent.alias').' AS parent_alias, '.
								$db->quoteName('parent.path').' AS parent_route'
				);
				$query->join('LEFT', $db->quoteName('#__categories').' AS parent ON '.$db->quoteName('parent.id').' = '.$db->quoteName('c.parent_id'));				
				// Join on user table.
				$query->select($db->quoteName('ua.name').' AS created_by_name');
				$query->join('LEFT', $db->quoteName('#__users').' AS ua on '.$db->quoteName('ua.id').' = '.$db->quoteName('a.created_by'));
				$query->select($db->quoteName('uam.name').' AS modified_by_name');
				$query->join('LEFT', $db->quoteName('#__users').' AS uam on '.$db->quoteName('uam.id').' = '.$db->quoteName('a.modified_by'));
				
				
				// Join over the language
				$query->select($db->quoteName('l.title').' AS language_title');
				$query->join('LEFT', $db->quoteName('#__languages').' AS l ON '.$db->quoteName('l.lang_code').' = '.$db->quoteName('a.language'));
				
				// Filter by language
				if ($this->getState('filter.language'))
				{
					$query->where($db->quoteName('a.language').' IN (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}
				
				$query->where($db->quoteName('a.id').' = ' . (int) $pk);
				
				// Join to check for category published status in parent categories up the tree
				// If all categories are published, badcats.id will be null, and we just use the image state
				$sub_query = ' (SELECT '.$db->quoteName('cat.id').' as id FROM '.$db->quoteName('#__categories').' AS cat JOIN '.$db->quoteName('#__categories').' AS parent ';
				$sub_query .= 'ON '.$db->quoteName('cat.lft').' BETWEEN '.$db->quoteName('parent.lft').' AND '.$db->quoteName('parent.rgt').' ';
				$sub_query .= 'WHERE '.$db->quoteName('parent.extension').' = ' . $db->quote('com_nicegallery');
				$sub_query .= ' AND '.$db->quoteName('parent.published').' <= 0 GROUP BY '.$db->quoteName('cat.id').')';
				$query->join('LEFT OUTER', $sub_query . ' AS badcats ON '.$db->quoteName('badcats.id').' = '.$db->quoteName('c.id'));
					

				$can_publish = $user->authorise('core.edit.state', 'com_nicegallery.image.'.$pk);
				//  Do not show unless today's date is within the publish up and down dates (or they are empty)
				if (!$can_publish)
				{
					$null_date = $db->quote($db->getNullDate());
					$now_date = $db->quote(JFactory::getDate()->toSQL());
					$query->where('('.$db->quoteName('a.publish_up').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_up').' <= ' . $now_date . ')');
					$query->where('('.$db->quoteName('a.publish_down').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_down').' >= ' . $now_date . ')');
				}
				// Filter by published status.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');
				if (is_numeric($published) 
						AND !$can_publish
					)
				{
					$query->where('('.$db->quoteName('a.state').' = ' . (int) $published . ' OR '.$db->quoteName('a.state').' = ' . (int) $archived . ')');
				
				}
				
				$query->select($db->quoteName('vl.title').' AS access_title');
				$query->join('LEFT', $db->quoteName('#__viewlevels').' AS vl on '.$db->quoteName('vl.id').' = '.$db->quoteName('a.access'));
					
																				
				$db->setQuery($query);

				$item = $db->loadObject();

				if (empty($item))
				{
					return JError::raiseError(404, JText::_('COM_NICEGALLERY_IMAGES_ERROR_ITEM_NOT_FOUND'));
				}
				// Include any manipulation of the data on the record e.g. expand out Registry fields
				// NB The params registry field - if used - is done automatcially in the JAdminModel parent class
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

		

				$item->tags = new JHelperTags;
				$item->tags->getItemTags('com_nicegallery.image', $item->id);
							
				// Check for published state if filter set.
				if (((is_numeric($published)) OR (is_numeric($archived))) AND (($item->state != $published) AND ($item->state != $archived)))
				{
					return JError::raiseError(404, JText::_('COM_NICEGALLERY_IMAGES_ERROR_ITEM_NOT_FOUND'));
				}
				$item->introdescription = trim($item->intro) != '' ? $item->intro.$item->description : $item->description;

				// Convert parameter fields to objects.
				$image_params = new JRegistry;
				$image_params->loadString($item->params);

				// Unpack readmore and layout params
				$item->image_alternative_readmore = $image_params->get('image_alternative_readmore');
				$item->layout = $image_params->get('layout');
				
				$item->params = clone $this->getState('params');				
								
				// Image params override menu item params only if menu param = 'use_image'
				// Otherwise, menu item params control the layout
				// If menu item is 'use_image' and there is no image param, use global

				// create an array of just the params set to 'use_image'
				$menu_params_array = $this->getState('params')->toArray();
				$image_array = array();

				foreach ($menu_params_array as $key => $value)
				{
					if ($value === 'use_image')
					{
						// if the image has a value, use it
						if ($image_params->get($key) != '')
						{
							// get the value from the image
							$image_array[$key] = $image_params->get($key);
						}
						else
						{
							// otherwise, use the global value
							$image_array[$key] = $global_params->get($key);
						}
					}
				}

				// merge the selected image params
				if (count($image_array) > 0)
				{
					$image_params = new JRegistry;
					$image_params->loadArray($image_array);
					$item->params->merge($image_params);
				}


				// Compute selected asset permissions.

				// Technically guest could edit an image, but lets not check that to improve performance a little.
				if (!$user->get('guest')) 
				{
					$user_id	= $user->get('id');
					$asset	= 'com_nicegallery.image.'.$item->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset)) 
					{
						$item->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					else
					{ 
						if (!empty($user_id) AND $user->authorise('core.edit.own', $asset)) 
						{
						// Check for a valid user and that they are the owner.
							if ($user_id == $item->created_by) 
							{
								$item->params->set('access-edit', true);
								// If owner allow them to edit state in front end
								$item->params->set('access-change', true);
								
							}
						}
					}

					if ($user->authorise('core.create', $asset))
					{
						$item->params->set('access-create', true);
					}	
					
					if ($user->authorise('core.delete', $asset)) 
					{
						$item->params->set('access-delete', true);
					}
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
				// Check edit state permission.
					if ($user->authorise('core.edit.state', $asset)) 
					{				
						$item->params->set('access-change', true);
					}											
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$item->params->set('access-view', true);
				}
				else
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$groups = $user->getAuthorisedViewLevels();
					if ($item->catid != 0 AND $item->category_access !== null)
					{ 
						$item->params->set('access-view', in_array($item->access, $groups) AND in_array($item->category_access, $groups));
					}
					else 
					{
						$item->params->set('access-view', in_array($item->access, $groups));
					}
				}

				$this->_item[$pk] = $item;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}			
			}
		}

		return $this->_item[$pk];
	}
	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param	integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function publish(&$pks, $value = 1)
	{
		
		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();
		$user	= JFactory::getUser();
	
		$pks = (array) $pks;

		// Include the nicegallery plugins for the change of state event.
		JPluginHelper::importPlugin('nicegallery');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());
			return false;
		}

		// Trigger the ChangeState event.
		$result = $dispatcher->trigger('onImageChangeState', array('com_nicegallery.image', $pks, $value));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
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
	 * Method to adjust the ordering of a row.
	 *
	 * Returns NULL if the user did not have edit
	 * privileges for any of the selected primary keys.
	 *
	 * @param	integer  $pks    The ID of the primary key to move.
	 * @param	integer  $delta  Increment, usually +1 or -1
	 *
	 * @return  mixed  False on failure or error, true on success, null if the $pk is empty (no items selected).
	 *
	 */
	public function reorder($pks, $delta = 0)
	{
		
		$table = $this->getTable();
		$pks = (array) $pks;
		$result = true;

		$allowed = true;

		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				// Access checks.
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					$allowed = false;
					continue;
				}
				
				$where = array();
				$where = $this->getReorderConditions($table);

				if (!$table->move($delta, $where))
				{
					$this->setError($table->getError());
					unset($pks[$i]);
					$result = false;
				}

			}
			else
			{
				$this->setError($table->getError());
				unset($pks[$i]);
				$result = false;
			}
		}

		if ($allowed === false AND empty($pks))
		{
			$result = null;
		}

		// Clear the component's cache
		if ($result == true)
		{
			$this->cleanCache();
		}

		return $result;
	}
	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array    $pks    An array of primary key ids.
	 * @param   integer  $order  +1 or -1
	 *
	 * @return  mixed
	 *
	 */
	public function saveorder($pks = null, $order = null)
	{
		
		$table = $this->getTable();
		$conditions = array();

		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}

		// update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			// Access checks.
			if (!$this->canEditState($table))
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
			elseif ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}

				// Remember to reorder within order fields
				$condition = $this->getReorderConditions($table);
				$found = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$key = $table->getKeyName();
					$conditions[] = array($table->$key, $condition);
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}	   	
		
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array    $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 * 
	 */
	public function delete(&$pks)
	{
		
		$dispatcher	= JEventDispatcher::getInstance();
		$pks		= (array) $pks;
		$table		= $this->getTable();

		// Include the nicegallery plugins for the on delete events.
		JPluginHelper::importPlugin('nicegallery');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{

			if ($table->load($pk))
			{
				if ($this->canDelete($table))
				{
					// Trigger the BeforeDelete event.
					$result = $dispatcher->trigger('onImageBeforeDelete', array('com_nicegallery.image', &$table));
					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					}
					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}

					// Trigger the AfterDelete event.
					$dispatcher->trigger('onImageAfterDelete', array('com_nicegallery.image', &$table));
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						JError::raiseWarning(500, $error);
						return false;
					}
					else
					{
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
						return false;
					}
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
	/**
	 * Increment the hit counter for the image.
	 *
	 * @pk		int		Optional primary key of the image to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$app = JFactory::getApplication('site');
	
		$hit_count = $app->input->getInt('hitcount', 1);

		if ($hit_count)
		{
			
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('image.id');
			$table =  $this->getTable();
			$table->load($pk);
			$table->hit($pk);			
		}

		return true;
	}
}
