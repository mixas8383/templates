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
// no direct access
defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.controllerform');
}


class ComponentArchitectControllerComponentWizard extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * 
	 */
	protected $text_prefix = 'COM_COMPONENTARCHITECT_COMPONENT_WIZARD';
	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * @return	void

	 */
	function __construct($config = array())
	{
		parent::__construct($config);
	}
	/**
	 * Method to display a dialog to request parameters for a component generation
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$this->setRedirect( 'index.php?option=com_componentarchitect&view=componentwizard');
	}			
	/**
	 * Method to generate component code
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	public function wizardsave()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$model = $this->getModel();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$task = $this->getTask();
		$context = 'com_componentarchitect.componentwizard';
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'error');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the wizard screen.
			if (JRequest::getVar('close', '') == 'modal')
			{
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=componentwizard&tmpl=component', false));
			}
			else
			{
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=componentwizard', false));
			}
			return false;
		}

		// Attempt to save the data.
		if (!$model->wizardsave($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the wizard screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			if (JRequest::getVar('close', '') == 'modal')
			{
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=componentwizard&tmpl=component', false));
			}
			else
			{
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=componentwizard', false));
			}

			return false;
		}
		
		// Clear the data in the session.
		$app->setUserState($context . '.data', '');
		
		$this->setMessage(JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_SUCCESS'));
		if (JRequest::getVar('close', '') == 'modal')
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=componentwizard&layout=close&tmpl=component', false));
		}
		else
		{		
			// Redirect to the list screen.
			$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=components' . $this->getRedirectToListAppend(), false));
		}
		
		return true;					
	}	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 */
	public function cancel($key = null)
	{
		$app = JFactory::getApplication();
		$context = 'com_componentarchitect.componentwizard';
		
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->setMessage(JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_CANCEL'));
		
		// Clear the data in the session.
		$app->setUserState($context . '.data', '');
		if (JRequest::getVar('close', '') == 'modal')
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=componentwizard&tmpl=component&close=1', false));
		}
		else
		{		
			// Redirect to the list screen.
			$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=components' . $this->getRedirectToListAppend(), false));
		}
		return true;		
	}	
	//[%%END_CUSTOM_CODE%%]	
}
