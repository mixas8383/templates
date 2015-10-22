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

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');
if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.model');
}
jimport('joomla.filesystem.file');

/**
 * The Control Panel model
 *
 */
class ComponentArchitectModelLogs extends JModelLegacy
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * 
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
 
		// Load the parameters - this will only load those from config.xml
		$params = JComponentHelper::getParams('com_componentarchitect');
		$this->setState('params', $params);
		parent::populateState();
	}
	function getLogFiles()
	{
		$ret = array();	
		$params = JComponentHelper::getParams('com_componentarchitect');
		$logdir = JPATH_ROOT.'/'.$params->get('default_logging_folder');
		if (!is_dir($logdir))
		{
			// Default path used for log.  Store this to display at end.
			$config = &JFactory::getConfig();
			$logdir = $config->getValue('config.log_path');
		}
		$dir = opendir($logdir);
		// retrieve all test data generation log files
		while(false !== ( $file = readdir($dir)) )
		{ 
			if (( $file != '.' ) AND ( $file != '..' ))
			{ 
				if( JString::strpos($file,'com_componentarchitect_generate_log_') !== False )
				{
					$ret[] = $file;
				}			
			}
			
		}
		closedir($dir);		

		return $ret;
	}

}
//[%%END_CUSTOM_CODE%%]