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
// No direct access.
defined('_JEXEC') or die;
if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.modeladmin');
}
require_once JPATH_COMPONENT.'/'.'helpers'.'/'.'generateprogress.php';
/**
 * Methods supporting a a generate dialog.
 *
 */
class ComponentArchitectModelGenerateDialog extends JModelAdmin
{

	/**
	 * @var		string	The event to trigger after before the generate.
	 */
	protected $event_before_generate = 'onCscomponentBeforeGenerate';
	/**
	 * @var		string	The event to trigger after saving the generate.
	 */
	protected $event_after_generate = 'onCscomponentAfterGenerate';
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

		$params = JComponentHelper::getParams('com_componentarchitect');
		$this->setState('params', $params);
		
		$ids = $session->get('component_ids');
		
		$this->setState('component_ids', $ids);
	}	
	/**
	 * Method to get the generate dialog form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * 
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{

		$form = $this->loadForm('com_componentarchitect.generatedialog', 'generatedialog', array('control' => 'jform', 'load_data' => $loadData),true);
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
		$data = JFactory::getApplication()->getUserState('com_componentarchitect.generatedialog.data', array());
		
		// If selected on a components list then set the component id
		$session = JFactory::getSession();
		$component_id = $session->get('generate_component_id', 0);		
		$data['component_id'] = $component_id;
				
		return $data;
	}
	/**
	 * Method to get generate the component using helper functions.
	 * 
	 * @param	string	$token		A form session token used to validate we are on the same form
	 *
	 * @return	array	An array of the progress (at completion) for the component generation.
	 */	
	public function generate($token)
	{
		$component_id = (int)  $this->getState('component_id', 0);
		$code_template_id = (int) $this->getState('code_template_id', 0);
		$output_path = (string)$this->getState('output_path', '');
		$zip_format = (string) $this->getState('zip_format', '');		
		$logging = (int) $this->getState('logging', 0);		
		$description = (string) $this->getState('description', '');
		
		// Initialise session data
		$progress = new ComponentArchitectGenerateProgressHelper();
		$progress->initialiseProgress($token);		
		
		require_once JPATH_COMPONENT.'/'.'helpers'.'/'.'generate.php';

		$generate = new ComponentArchitectGenerateHelper($token);
		
		$generate->generateComponent($component_id, $code_template_id, $token, $output_path, $zip_format, (bool) $logging );
		
		$progress_array = $progress->getProgress($token);
		
		$progress->clearProgress($token);
		
		return $progress_array;		
	}
}
//[%%END_CUSTOM_CODE%%]