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
// no direct access
defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.controller');
}


class ComponentArchitectControllerGenerateProgress extends JControllerLegacy
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * 
	 */
	protected $text_prefix = 'COM_COMPONENTARCHITECT_GENERATE_PROGRESS';
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
	 * Method to show progress of generate code
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */	
	public function progress()
	{
		$ajax = JRequest::getCmd('ajax', '');

		$model = $this->getModel('GenerateProgress', 'ComponentArchitectModel');
		$model->setState('ajax', $ajax);
		$token = JRequest::getString('token','','default', null);		
		
		$progress_array = $model->checkGenerateProgress($token);

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($progress_array). '###';
		flush();
		JFactory::getApplication()->close();
	}	
	//[%%END_CUSTOM_CODE%%]	
}
