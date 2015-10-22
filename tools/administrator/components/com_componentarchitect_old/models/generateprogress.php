<?php
/**
 * @version			$Id: generateprogress.php 411 2014-10-19 18:39:07Z BrianWade $
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
	jimport('joomla.application.component.model');
}
require_once JPATH_COMPONENT.'/'.'helpers'.'/'.'generateprogress.php';
/**
 * Class of Methods supporting a a generate dialog.
 *
 */
class ComponentArchitectModelGenerateProgress extends JModelLegacy
{
	/**
	 * Method to check the progress of a component generation.
	 * 
	 * @param	string	$token		A form session token used to validate we are on the same form
	 *
	 * @return	array	An array of the progress (at completion) for the component generation.
	 */
	public function checkGenerateProgress($token)
	{
		$progress_array = array();
		
		$ajaxTask = $this->getState('ajax');
		
		$progress = new ComponentArchitectGenerateProgressHelper();

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		switch($ajaxTask)
		{
			case 'step':

				// get the progress session data
				$progress_array = $progress->getProgress($token);				

				break;
			// Handle completion when there has been an error
			case 'complete':
				
				// get the progress session data
				$progress_array = $progress->getProgress($token);
				
				// Make sure session data is cleared
				$progress->clearProgress($token);

				break;

			default:
				break;
		}
	
		return $progress_array;
	}
}
//[%%END_CUSTOM_CODE%%]