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
 * @CAversion		Id: compobject.php 408 2014-10-19 18:31:00Z BrianWade $
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
 * Config controller class.
 * 
 */
class NicegalleryControllerConfig extends JControllerForm
{
	/**
	 * @var    string	$view_item	The URL view item variable.
	 */
	protected $view_item = 'configform';
	/**
	 * @var    string	$view_list	The URL view list variable.
	 */
	protected $view_list = 'configs';
	/**
	 * 
	 * @var    string	$url_var	The URL edit variable
	 */
	protected $url_var = 'a.id';
	/**
	 * Constructor
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('apply',		'save');
		$this->registerTask('save2new',		'save');
		$this->registerTask('save2copy',	'save');
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 *
	 * @return	boolean
	 * 
	 */
	protected function allowAdd($data = array())
	{
		$user		= JFactory::getUser();
		$allow		= null;
		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key; default is id
	 *
	 * @return	boolean
	 * 
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$record_id	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$asset		= 'com_nicegallery.config.'.$record_id;
		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset))
		{
			$owner_id = 0;
			// Now test the owner is the user.
			if (isset($data['created_by']))
			{ 
				$owner_id	= (int) $data['created_by'];
			}
			if (empty($owner_id) AND $record_id)
			{
				// Need to do a lookup from the model.
				$record		= $this->getModel('configform')->getItem($record_id);

				if (empty($record))
				{
					return false;
				}

				$owner_id = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($owner_id == $user->id)
			{
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
	/**
	 * Method override to check if you can delete an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key; default is id
	 *
	 * @return	boolean
	 *
	 */
	protected function allowDelete($data = array(), $key = 'id')
	{
		$record_id	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$asset		= 'com_nicegallery.config.'.$record_id;

		// Check general delete permission.
		if ($user->authorise('core.delete', $asset))
		{
			return true;
		}

		// Fallback on delete.own.
		// First test if the permission is available.
		if ($user->authorise('core.delete.own', $asset))
		{
			$owner_id = 0;
			// Now test the owner is the user.
			if (isset($data['created_by']))
			{ 
				$owner_id	= (int) $data['created_by'];
			}
			if (empty($owner_id) AND $record_id)
			{
				// Need to do a lookup from the model.
				$record		= $this->getModel('configform')->getItem($record_id);

				if (empty($record))
				{
					return false;
				}

				$owner_id = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($owner_id == $user_id)
			{
				return true;
			}
			// If the owner matches 'me' then do the test.
			if ($owner_id == $user->id)
			{
				return true;
			}
			else
			{
				return false;
			}
		}		
	}	
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	object	The model.
	 * 
	 */
	public function getModel($name = 'configform', $prefix = '',$config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	/**
	 * Method to get the return page saved in session data.
	 *
	 * @param	string	$context	The context string used to store the return data
	 *
	 * @return	string	The url string for the return page
	 * 
	 */
	protected function getReturnPage($context)
	{
		$app		= JFactory::getApplication();

		if (!($return = $app->getUserState($context.'.return'))) 
		{
			return JUri::base();
		}

		$return = base64_decode($return);

		if (!JUri::isInternal($return)) 
		{
			$return = JUri::base();
		}

		return $return;
	}
	/**
	 * Method to set the return page as a saved entry in session data.
	 *
	 * @param	string	$context	The context string used to store the return data
	 *
	 * @return	void
	 * 
	 */
	protected function setReturnPage($context)
	{
		$app		= JFactory::getApplication();

		$return = $this->input->get('return', null, 'base64');
		
		if (empty($return) OR !JUri::isInternal(base64_decode($return)))
		{
			$return = base64_encode(JUri::base());
		}
		
		$app->setUserState($context.'.return', $return);
	}
	/**
	 * Method to clear the return page in session data.
	 *
	 * @param	string	$context	The context string used to store the return data
	 *
	 * @return	void
	 * 
	 */	
	protected function clearReturnPage($context)
	{
		$app		= JFactory::getApplication();

		$app->setUserState($context.'.return', null);
	}
	/**
	 * Method to add a new record.
	 *
	 * @return  mixed  True if the Config can be added, a error object if not.
	 *
	 */
	public function add()
	{
		$app		= JFactory::getApplication();
		$context	= $this->option.'.edit.'.$this->context;

		// Access check
		if (!$this->allowAdd()) 
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		// Clear the record edit information from the session.
		$app->setUserState($context.'.data',	null);

		// Clear the return page.
		// TODO: We should be including an optional 'return' variable in the URL.
		$this->setReturnPage($context);

		$redirect = JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.'&layout=edit', false);

		$this->setRedirect($redirect);

		return true;
	}

	/**
	 * Method to edit a object
	 *
	 * Sets object ID in the session from the request, checks the item out, and then redirects to the edit page.
	 * 
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $url_var  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 */
	public function edit($key = 'id', $url_var = null)
	{
		
		$app		= JFactory::getApplication();
		$context	= $this->option.'.edit.'.$this->context;
		$ids		= $this->input->get('cid', array(), 'array');

		// Get the id of the group to edit.
		$record_id =  (int) (empty($ids) ? $this->input->getInt('id') : array_pop($ids));

		// Access check
		if (!$this->allowEdit(array('id' => $record_id))) 
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		// Get the menu item model.
		$model = $this->getModel('configform');
		// Set the return url
		$this->setReturnPage($context);

		// Check that this is not a new item.

		if ($record_id > 0) 
		{
			$item = $model->getItem($record_id);

			// If not already checked out, do so.
			if ($item->checked_out == 0) 
			{
				if (!$model->checkout($record_id)) 
				{
					// Check-out failed, go back to the list and display a notice.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');
					
					// Redirect to the list screen.
					$this->setRedirect($this->getReturnPage($context));
					
					// Make sure return url is cleared
					$this->clearReturnPage($context);
						
					return false;
				}
			}
		}

		// Check-out succeeded, register the ID for editing.
		$this->holdEditId($context, $record_id);
		$app->setUserState($context.'.data',	null);

		$redirect = JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item
							.$this->getRedirectToItemAppend($record_id, $key), false);

		$this->setRedirect($redirect);

		return true;
	}

	/**
	 * Method to cancel an edit
	 *
	 * Checks the item in, sets item ID in the session to null, and then redirects to the list page.
	 * 
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancel($key = 'id')
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		
		$app		= JFactory::getApplication();
		$model		= $this->getModel('configform');
		$context	= $this->option.'.edit.'.$this->context;
		$record_id	= $this->input->getInt('id');

		if ($record_id) 
		{
			// Check we are holding the id in the edit list.
			if (!$this->checkEditId($context, $record_id)) 
			{
				// Somehow the person just went to the form - we don't allow that.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $record_id), 'error');
				$this->setRedirect($this->getReturnPage($context));
				
				// Make sure return url is cleared
				$this->clearReturnPage($context);	
				return false;
			}

			// If rows ids do not match, checkin previous row.
			if ($model->checkin($record_id) === false) 
			{
				// Check-in failed, go back to the menu item and display a notice.
				$this->setMessage(JText::sprintf('JERROR_CHECKIN_FAILED', $model->getError()), 'error');
				$this->setRedirect(JRoute::_(
					'index.php?option='.$this->option.'&view='.$this->view_item
					. $this->getRedirectToItemAppend($record_id, $key), false
					)
				);				
				return false;
			}
		}

		// Clear the menu item edit information from the session.
		$this->releaseEditId($context, $record_id);
		$app->setUserState($context.'.data',	null);

		// Redirect to the list screen.
		$this->setRedirect($this->getReturnPage($context));
		
		// Make sure return url is cleared
		$this->clearReturnPage($context);			
		return true;
	}
	/**
	 * Method to save the record
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $url_var  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.* 
	 */
	public function save($key = 'id', $url_var = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		
		$app		= JFactory::getApplication();
		// Think I need to leave this as JRequest for now as the App input only returns a copy of the post data so any changes will be lost
		// Replace with direct POST variable after some checking
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		$model		= $this->getModel('configform');
		$task		= $this->getTask();
		$context	= $this->option.'.edit.'.$this->context;
		$record_id = $this->input->getInt('id',0);	
		if (!$this->checkEditId($context, $record_id)) 
		{
			// Somehow the person just went to the form and saved it - we don't allow that.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $record_id), 'error');
			$this->setRedirect($this->getReturnPage($context));
			
			// Make sure return url is cleared
			$this->clearReturnPage($context);	
			return false;
		}

		// Populate the row id from the session.
		$data['id'] = $record_id;
		// Split intro from description
		$pattern    = '#<hr\s+id=(["\'])system-readmore\1\s*/*>#i';
		$text		= $data['introdescription'];
		$tag_pos		= preg_match($pattern, $text);

		if ($tag_pos == 0)
		{
			$data['intro'] = $data['introdescription'];
			$data['description'] = '';		
		}
		else
		{
			list($data['intro'], $data['description']) = preg_split($pattern, $data['introdescription'], 2);
		}		
		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') 
		{
			// Check-in the original row.
			if ($model->checkin() === false) 
			{
				// Check-in failed, go back to the item and display a notice.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()),'error');
				$this->setRedirect(JRoute::_(
					'index.php?option='.$this->option.'&view='.$this->view_item
					. $this->getRedirectToItemAppend($record_id, $key), false
					)
				);
				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data['id']	= 0;
			$task		= 'apply';
		}

		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form) 
		{
			JError::raiseError(500, $model->getError());

			return false;
		}
		$datastore = $data;		
		$data	= $model->validate($form, $data);

		// Check for validation errors.
		if ($data === false) 
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n AND $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) 
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else 
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context.'.data', $datastore);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_(
				'index.php?option='.$this->option.'&view='.$this->view_item
				. $this->getRedirectToItemAppend($record_id, $key), false
				)
			);
			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data)) 
		{
			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_(
				'index.php?option='.$this->option.'&view='.$this->view_item
				. $this->getRedirectToItemAppend($record_id, $key), false
				)
			);
			return false;
		}

		// Save succeeded, check-in the row.
		if ($model->checkin() === false) 
		{
			// Check-in failed, go back to the row and display a notice.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');
			$this->setRedirect(JRoute::_(
				'index.php?option='.$this->option.'&view='.$this->view_item
				. $this->getRedirectToItemAppend($record_id, $key), false
				)
			);
			return false;
		}

		if ($record_id == 0) 
		{
			$this->setMessage(JText::_('COM_NICEGALLERY_CONFIGS_SUBMIT_SAVE_SUCCESS'));
		} 
		else 
		{
			$this->setMessage(JText::_('COM_NICEGALLERY_CONFIGS_SAVE_SUCCESS'));
		}

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the row data in the session.
				$record_id = $model->getState('config.id');
				$this->holdEditId($context, $record_id);
				$app->setUserState($context.'.data',	null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_(
					'index.php?option='.$this->option.'&view='.$this->view_item
					. $this->getRedirectToItemAppend($record_id, $key), false
					)
				);				
				break;

			case 'save2new':
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $record_id);
				$app->setUserState($context.'.data',	null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_(
					'index.php?option='.$this->option.'&view='.$this->view_item
					.'&layout=edit', false
					)
				);
			break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $record_id);
				$app->setUserState($context.'.data',	null);

				// Redirect to the list screen.
				$this->setRedirect($this->getReturnPage($context));
				
				// Make sure return url is cleared
				$this->clearReturnPage($context);					
				break;
		}
		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $data);

		return true;		
	}

	/**
	 * Method to delete a object
	 *
	 * Sets object ID in the session from the request and then deletes the object.
	 *
	 * @return	boolean	True if the record can be edited, false if not.
	 */
	public function delete()
	{
		// Check for request forgeries
		(JSession::checkToken('get') OR JSession::checkToken()) OR die(JText::_('JINVALID_TOKEN'));
		
		$app		= JFactory::getApplication();
		$context	= "$this->option.delete.$this->context";
		$ids		= $this->input->get('cid', array(), 'array');

		// Get the id of the group to edit.
		$id =  (int) (empty($ids) ? $this->input->getInt('id') : array_pop($ids));

		// Access check
		if (!$this->allowDelete(array('id' => $id))) 
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		// Get the menu item model.
		$model = $this->getModel('config');

		// Check that this is not a new item.

		if ($id > 0) 
		{
			
			$trash_state = -2;
			if($model->publish($id, $trash_state))
			{
				$this->setMessage(JText::_('COM_NICEGALLERY_CONFIGS_DELETE_SUCCESS'));
				
			}
			else
			{
				$this->setMessage(JText::_('COM_NICEGALLERY_CONFIGS_DELETE_FAILED'));
			}
		}

		$this->setReturnPage($context);

		$this->setRedirect($this->getReturnPage($context));
		
		// Make sure return url is cleared
		$this->clearReturnPage($context);	
		
		return true;
	}	
	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   array         $validData   The validated data.
	 *
	 * @return  void
	 *
	 */
	protected function postSaveHook(JModelLegacy $model, $valid_data = array())
	{
		return;
	}	
}
