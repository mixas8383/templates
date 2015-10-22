<?php
/**
 * @version			$Id: generatedialog.php 411 2014-10-19 18:39:07Z BrianWade $
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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class ComponentArchitectControllerGenerateDialog extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * 
	 */
	protected $text_prefix = 'COM_COMPONENTARCHITECT_GENERATE_DIALOG';
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
		$app = JFactory::getApplication();
		// Initialise variables. cid is value from list view and id is value from record view
		$ids = JRequest::getVar('cid', array(), '', 'array');
		
		if (count($ids) <= 0)
		{
			$ids = JRequest::getVar('id', array(), '', 'array');
		}
		
		if (count($ids) > 1)
		{
			$app->enqueueMessage(JText::_('COM_COMPONENTARCHITECT_GENERATE_ERROR_ONLY_ONE_COMPONENT'), 'error');
			$this->setRedirect( 'index.php?option=com_componentarchitect&view=components');			
			return false;
		}
		
		
		$session = JFactory::getSession();
		$session->set('generate_component_id', $ids[0]);
		
		$this->setRedirect( 'index.php?option=com_componentarchitect&view=generatedialog');

	}			
	/**
	 * Method to generate component code
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	public function generate()
	{
		$ajax = JRequest::getCmd('ajax', '');
		$token = JRequest::getString('token','','default', null);		
		$component_id = JRequest::getInt('component_id', 0);
		$code_template_id = JRequest::getInt('code_template_id', 0);
		$output_path = str_replace(' ', '_',JRequest::getString('output_path','','default', null));
		$zip_format = JRequest::getString('zip_format','','default', null);		
		$logging = JRequest::getInt('logging', 0);
		$description = JRequest::getString('description','','default', null);
		
		if ($component_id == 0)
		{
			$msg = JText::_( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0004_NO_COMPONENT_SELECTED' );
			$this->setRedirect( 'index.php?option=com_componentarchitect', $msg );
			return false;			
		} 
		if ($code_template_id == 0)
		{
			$msg = JText::_( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0002_NO_CODE_TEMPLATE_SELECTED' );
			$this->setRedirect( 'index.php?option=com_componentarchitect', $msg );
			return false;			
		}

		// Make sure any previous session data is cleared
		$session = JFactory::getSession();		
		
		$model = $this->getModel('GenerateDialog','ComponentArchitectModel');

		$model->setState('component_id', $component_id);
		$model->setState('code_template_id', $code_template_id);
		$model->setState('output_path', $output_path);
		$model->setState('zip_format', $zip_format);		
		$model->setState('logging', $logging);			
		$model->setState('description',	$description);		
		
		$progress_array = $model->generate($token);

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($progress_array). '###';
		flush();
		JFactory::getApplication()->close();		
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
		$context = 'com_componentarchitect.generatedialog';
		
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->setMessage(JText::_('COM_COMPONENTARCHITECT_GENERATE_CANCEL'));
		
		// Clear the data in the session.
		$app->setUserState($context . '.data', '');

		// Redirect to the list screen.
		$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=components' . $this->getRedirectToListAppend(), false));

		return true;		
	}
	/**
	 * Install components
	 * 
	 *
	 * @return  boolean True or False
	 */
	public function install()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=components'. $this->getRedirectToListAppend(), false));
		
		$app = JFactory::getApplication();
		$path = $app->input->get('install_url', '', 'string');
		
		
		if ($path != '')
		{
			$path = JPATH_SITE. "/". str_replace("../", '', $path);
			
			$installer = JInstaller::getInstance();
			$result = $installer->install($path);	
		}
		else
		{
			$this->setMessage(JText::_('COM_COMPONENTARCHITECT_GENERATE_ERROR_NO_INSTALL_PATH'), 'warning');
			return false;
		}
		
		if ($result)
		{
			$this->setMessage(JText::_('COM_COMPONENTARCHITECT_GENERATE_INSTALL_SUCCESS'));
			
			return true;
		}
		else
		{
			$this->setMessage(JText::_('COM_COMPONENTARCHITECT_GENERATE_ERROR_INSTALL_FAILED'), 'warning');		
			return false;
		}
	}							
	//[%%END_CUSTOM_CODE%%]	
}
