<?php
/**
 * @version			$Id: logs.php 411 2014-10-19 18:39:07Z BrianWade $
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

defined('_JEXEC') or die('Restricted Access');

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.controllerform');
}

/**
 * Log view controller class
 *
 */
class ComponentArchitectControllerLogs extends JControllerForm
{
	public function  __construct($config = array())
	{
	
		parent::__construct($config);
	}

	// Renders the contents of the log's iframe
	public function iframe()
	{
		parent::display();
		
		flush();
		JFactory::getApplication()->close();
	}

	public function download()
	{
		$tag = JRequest::getCmd('tag',null);
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
 
		// Load the parameters - this will only load those from config.xml
		$params = JComponentHelper::getParams('com_componentarchitect');		

		$filename = JPATH_ROOT.'/'.$params->get('default_logging_folder').'/'.$tag;

		@ob_end_clean(); // In case some braindead plugin spits its own HTML
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Content-Description: File Transfer");
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="com_componentarchitect_generate_log.txt"');
		echo "--- START OF RAW LOG --\r\n";
		@readfile($filename); // The at sign is necessary to skip showing PHP errors if the file doesn't exist or isn't readable for some reason
		echo "--- END OF RAW LOG ---\r\n";
		flush();
		JFactory::getApplication()->close();
	}
}
//[%%END_CUSTOM_CODE%%]