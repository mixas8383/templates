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

class ComponentArchitectGenerateProgressHelper
{ 

	protected $_logging = false; 

	/**
	* Initialise progress - creates a session array for the progress data.
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* 
	* @return		true
	*/ 	
	public function initialiseProgress($token)
	{
		$this->deleteSessionData('com_componentarchitect_generate', $token);	
		
		$stages = array();
		
		$stages['initialise']	= array('logging' => 0, 'code_template_name' => '', 'component_name' => '');

		// Progress of generate is divided into 3 stages: analyse component, create files and search/replace markup
		
		// First stage - Analayse Component: metric used is the number of fields in all component objects
		
		$stages['stage_1']	= array('total_count' => 0, 'current_count' => 0, 'current_step' => '', 'start_time' => 0, 'current_time' => 0);

		// Second stage -Create files: metric used is the number of files in the source Code Template
		
		$stages['stage_2']	= array('total_count' => 0, 'current_count' => 0, 'current_step' => '', 'start_time' => 0, 'current_time' => 0);

		// Third stage - Search/Replace markup: metric used is the number of files in the generated files for the component
		
		$stages['stage_3']	= array('total_count' => 0, 'current_count' => 0, 'current_step' => '', 'start_time' => 0, 'current_time' => 0);
		
		$stages['completion']	= array('is_complete' => 0, 'component_path' => '', 'zip_path' => '', 'zip_file' => '', 'error' => '', 'warnings' => array());

		return $this->setSessionData('com_componentarchitect_generate', $token, $stages);
	}
	/**
	* Initialise progress - creates a session array for the progress data.
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* @param		string	Generated component path
	* @param		string	Zip file path
	* @param		string	Zip file
	* 
	* @return		true
	*/ 	
	public function completeProgress($token, $component_path = '', $zip_path = '', $zip_file = '')
	{
		$stages = $this->getSessionData('com_componentarchitect_generate', $token);
		
		$stages['completion']['is_complete'] = 1;
		$stages['completion']['component_path'] = $component_path;
		$stages['completion']['zip_path'] = $zip_path;
		$stages['completion']['zip_file'] = $zip_file;

		return $this->setSessionData('com_componentarchitect_generate', $token, $stages);
	}
	/**
	* set initialise parameters
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* @param		integer	0 or 1 whether logging is requested or not
	* @param		string	Name of the code template 
	* @param		string	Name of the component
	* 
	* @return		true
	*/ 	
	public function setInitialiseStage($token, $logging = 0, $code_template_name = '', $component_name = '')
	{
		$stages = $this->getSessionData('com_componentarchitect_generate', $token);
		
		$stages['initialise']	= array('logging' => $logging, 'code_template_name' => $code_template_name, 'component_name' => $component_name);
		
		return $this->setSessionData('com_componentarchitect_generate', $token, $stages);
	}		
	/**
	* set progress stage - update the session progress at the state of a new progress stage.
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* @param		integer	Stage to set progress for
	* @param		integer	Total count for the specified stage
	* 
	* @return		true
	*/ 	
	public function setProgressStage($token, $stage, $total_count = 0)
	{
		$stages = $this->getSessionData('com_componentarchitect_generate', $token);
		
		$stages[$stage]['total_count'] = $total_count;
		$stages[$stage]['current_count'] = 0;		
		$stages[$stage]['start_time'] = microtime(true);  // time in milli seconds

		if ($this->_logging) $this->writeToLog(JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_START_'.JString::strtoupper($stage),$total_count));

		return $this->setSessionData('com_componentarchitect_generate', $token, $stages);
	}	
	/**
	* set progress - update the session progress with the current data.
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* @param		integer	Stage to set progress for
	* @param		string	A description of the current step for the specified stage
	* @param		boolean	A flag to indicate whether the stage has completed or not
	* 
	* @return		true
	*/ 	
	public function setProgress($token, $stage, $current_step = '', $stage_complete = false)
	{
		$stages = $this->getSessionData('com_componentarchitect_generate', $token);
		
		// First time set for a stage will have current count  = 0 so set the start time for the stage
		if ($stages[$stage]['current_count'] == 0)
		{
			$last_step_time = $stages[$stage]['start_time'];
			$stages[$stage]['current_time'] = $stages[$stage]['start_time'];

			$stages[$stage]['current_count']++;			
		}
		else
		{
			if ($stages[$stage]['current_count'] < $stages[$stage]['total_count'] AND !$stage_complete)
			{
				$last_step_time = (int) $stages[$stage]['current_time'];

				$stages[$stage]['current_count']++;				
			}
			else
			{
				// Stage procressing complete so calculate total stage time
				$last_step_time = (int) $stages[$stage]['start_time'];
				$stages[$stage]['current_count'] = $stages[$stage]['total_count'];			
			}
		}
		
		$current_count = $stages[$stage]['current_count'];
		$total_count = $stages[$stage]['total_count'];
		
		$stages[$stage]['current_step'] = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_PROGRESS_'.JString::strtoupper($stage), $current_count, $total_count, $current_step);
		$stages[$stage]['current_time'] = microtime(true);  // time in milli seconds
		
		// The progress steps are a good way of recording log steps if logging required
		if ($this->_logging) 
		{
			$step_time = $stages[$stage]['current_time'] - $last_step_time;
			
			$this->writeToLog(JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_LOG_'.JString::strtoupper($stage), $current_count, $total_count, $current_step), $step_time);
		}
		return $this->setSessionData('com_componentarchitect_generate', $token, $stages);
	}
	/**
	* get progress - return the array of progress data.
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* 
	* @return		array	The current progress data for all stages
	*/ 	
	public function getProgress($token, $stage = '')
	{
		$stages = $this->getSessionData('com_componentarchitect_generate', $token);
		
		// get all stages
		if ($stage != '')
		{
			$stages =  $stages[$stage];
		}

		return $stages;
	}
	/**
	* clear progress - at the end of the component generation clear the stages progress session data
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* 
	* @return		true
	*/
	public function clearProgress($token)
	{
		return $this->deleteSessionData('com_componentarchitect_generate', $token);
	}
	/**
	* Output error - add an error to the error array and, if requested, ouput a log record
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* @param		array	Error details in an array
	* @param		string	File name of current file where error found
	* @param		integer	Line number in source file where error occurred 
	* 
	* @return		true
	*/ 	
	public function outputError($token, $error, $file_name = '', $line_no = '', $step_time = 0)
	{
		$file_name = str_replace(JPATH_ROOT, '', $file_name);
		$this->_error = $error;
		$stages = $this->getSessionData('com_componentarchitect_generate', $token);
		
		if ($file_name != '')
		{
			if ($line_no != '')
			{
				$stages['completion']['error'] = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_ERROR_MESSAGE_FILE_LINE', $error['message'], $file_name, $line_no);
			}
			else
			{
				$stages['completion']['error'] = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_ERROR_MESSAGE_FILE', $error['message'], $file_name);
			}			
		}
		else
		{
			$stages['completion']['error'] = $error['message'];
		}
		if ($this->_logging) 
		{
			$this->writeToLog
			(
				array
				(
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE') =>$error['message'], 
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE') => $error['errorcode'], 
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_FILE') => $file_name, 
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_LINE_NUMBER') => (string) $line_no,
					'priority' => 'JLog::ERROR'
				),
				$step_time
			);
		}
		
		return $this->setSessionData('com_componentarchitect_generate', $token, $stages);
	}
	/**
	* Output warning - add warning to the warning array and, if requested, ouput a log record
	* 
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* @param		array	Warning details in an array
	* @param		string	File name of current file where warning found
	* @param		integer	Line number in source file where warning occurred 
	* 
	* @return		true
	*/ 	
	public function outputWarning($token, $warning, $file_name = '', $line_no = '', $step_time = 0)
	{
		$stages = $this->getSessionData('com_componentarchitect_generate', $token);
		
		$file_name = str_replace(JPATH_ROOT, '', $file_name);
		if ($file_name != '')
		{
			if ($line_no != '')
			{		
				$stages['completion']['warnings'][] = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_MESSAGE_FILE_LINE', $warning['message'], $line_no + 1, $file_name);
			}
			else
			{		
				$stages['completion']['warnings'][] = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_MESSAGE_FILE', $warning['message'], $file_name);
			}			
		}
		else
		{
			$stages['completion']['warnings'][] = $warning['message'];
		}
		
		if ($this->_logging) 
		{
			$this->writeToLog
			(
				array
				(
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE') =>$warning['message'], 
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE') => $warning['errorcode'], 
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_FILE') => $file_name, 
					JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_LINE_NUMBER') => (string) $line_no,
					'priority' => 'JLog::WARNING'
				),
				$step_time
			);
		}
		
		
		return $this->setSessionData('com_componentarchitect_generate', $token, $stages);
	}			
	/**
	* get session data - return data stored for a componentarchitect session.
	* 
	* @param		string	Context for the session data
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* 
	* @return		array	The current session data for the context and key specified
	*/ 	
	public function getSessionData($context, $key)
	{
		$session_data = array();
		
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('*');															
		$query->from($db->quoteName('#__componentarchitect_sessiondata'));
		$query->where($db->quoteName('context').' = '.$db->quote($context));
		$query->where($db->quoteName('key').' = '.$db->quote($key));
		$query->order($db->quoteName('created').' DESC');
		
		try
		{
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}			
		catch (RuntimeException $e)
		{
			$error = array(JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE') =>JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0013_CANNOT_ACCESS_SESSION_DATA', $e->getMessage()),JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE') => 'gen0013');
			if ($this->_logging) $this->writeToLog($error);
		}		

		// Always take the most recent progress data.  All should be removed on delete of session data.
		if ($rows)
		{
			$registry = new JRegistry;
			$registry->loadString($rows[0]->data);
			$session_data = $registry->toArray();
			$registry = null; //release memory
		}
		
		return $session_data;
	}
	/**
	* get session data - return data stored for a componentarchitect session.
	* 
	* @param		string	Context for the session data
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* @param		array	Session data to store as a JSON string
	* 
	* @return		array	The current session data for the context and key specified
	*/ 	
	public function setSessionData($context, $key, $data)
	{
		$session_record = array();

		$registry = new JRegistry();
		$registry->loadArray($data);
		$session_data = (string)$registry;
		
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('*');															
		$query->from($db->quoteName('#__componentarchitect_sessiondata'));
		$query->where($db->quoteName('context').' = '.$db->quote($context));
		$query->where($db->quoteName('key').' = '.$db->quote($key));
		$query->order($db->quoteName('created').' DESC');

		try
		{
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}			
		catch (RuntimeException $e)
		{
			$error = array(JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE') =>JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0013_CANNOT_ACCESS_SESSION_DATA', $e->getMessage()),JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE') => 'gen0013');
			if ($this->_logging) $this->writeToLog($error);
			return false;
		}		

		$query->clear();
		$date	= JFactory::getDate();

		$config = JFactory::getConfig();		
		$cache_time = $config->get('cachetime') * 60;
		$expiry_date	= JFactory::getDate(time() + $cache_time);
		
		$current_datetime = $date->toSQL();
		$expiry_datetime = $expiry_date->toSQL();
		
		if (count($rows) == 0)
		{
			// First time set of session data so create a new record
			$query->insert($db->quoteName('#__componentarchitect_sessiondata'));
			$query->set($db->quoteName('context').' = '.$db->quote($context));
			$query->set($db->quoteName('key').' = '.$db->quote($key));
			$query->set($db->quoteName('created').' = '.$db->quote($current_datetime));
			$query->set($db->quoteName('expires').' = '.$db->quote($expiry_datetime));
		}
		else
		{
			$query->update($db->quoteName('#__componentarchitect_sessiondata'));
			$query->where($db->quoteName('id').' = '. $rows[0]->id);
		}

		$query->set($db->quoteName('data') . ' = ' . $db->quote($session_data));
		$query->set($db->quoteName('modified').' = '.$db->quote($current_datetime));

		try
		{
			$db->setQuery($query);
			$db->execute();	
		}			
		catch (RuntimeException $e)
		{
			$error = array(JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE') =>JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0013_CANNOT_ACCESS_SESSION_DATA', $e->getMessage()),JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE') => 'gen0013');
			if ($this->_logging) $this->writeToLog($error);
			return false;
		}				
		
		return true;		
	}	
	/**
	* delete session data
	* 
	* @param		string	Context for the session data
	* @param		string	Token from the generate dialog to be used to ensure unique session data
	* 
	* @return		true
	*/
	public function deleteSessionData($context, $key)
	{
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->delete();															
		$query->from($db->quoteName('#__componentarchitect_sessiondata'));
		$query->where($db->quoteName('context').' = '.$db->quote($context));
		$query->where($db->quoteName('key').' = '.$db->quote($key));

		try
		{
			$db->setQuery($query);
			$db->execute();	
		}			
		catch (RuntimeException $e)
		{
			$error = array(JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE') =>JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0013_CANNOT_ACCESS_SESSION_DATA', $e->getMessage()),JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE') => 'gen0013');
			if ($this->_logging) $this->writeToLog($error);
			return false;
		}			

		return true;
	}		
	/**
	* Open log function - creates a log object of a standard Joomla! log file.
	* 
	* @return		true
	*/ 	
	public function openLog()
	{
		
		// Load the parameters - this will only load those from config.xml
		$params = JComponentHelper::getParams('com_componentarchitect');	
		
		$options['text_file'] = 'com_componentarchitect_generate_log_'.date('Y_m_d_H_i_s').'.txt';
		$options['text_file_no_php'] = 1;
		$options['text_entry_format'] = "{".JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_DATE').
			"}\t{".JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_TIME').
			"}\t{".JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_DURATION').
			"}\t{".JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE').
			"}\t{".JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE').
			"}\t{".JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_FILE').
			"}\t{".JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_LINE_NUMBER')."}";		
		
		$log_path = JPATH_ROOT.'/'.$params->get('default_logging_folder');
		$dir = opendir($log_path);
		if (!is_dir($log_path))
		{
			$config = &JFactory::getConfig();
			$log_path = $config->getValue('config.log_path');			
		}
		
		$options['text_file_path'] = $log_path;
		try
		{
			JLog::addLogger($options, JLog::ALL, array('com_componentarchitect_generate'));
		}
		catch (RuntimeException $e)
		{
			return false;
		}	
				
		$this->_logging = true;
		return true;
	}
	/**
	* Write out a log record recording the progress of the component create progress
	* 
	* @param		array	log entry
	* @param		string	File name of current file being processed
	* 
	* @return		true
	*/ 	
	public function writeToLog($log_entry, $step_time = 0)
	{
		if (is_array($log_entry))
		{
			if ($log_entry['priority'] != '')
			{
				$priority = $log_entry['priority'];
			}
			else
			{
				$priority = 'JLog::INFO';
			}	
			
			$entry = new JLogEntry($log_entry[JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_MESSAGE')], $priority, 'com_componentarchitect_generate');
			
			$errorcode_field = JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE');
			$file_field = JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_FILE');
			$line_number_field = JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_LINE_NUMBER');

			$entry->$errorcode_field	= $log_entry[JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_ERRORCODE')];	
			$entry->$file_field			= $log_entry[JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_FILE')];	
			$entry->$line_number_field	= $log_entry[JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_LINE_NUMBER')];	
			
		}
		else
		{
			$priority = 'JLog::INFO';
			$entry = new JLogEntry($log_entry, $priority, 'com_componentarchitect_generate');

		}
		
		$duration_field = JText::_('COM_COMPONENTARCHITECT_GENERATE_LOG_FIELD_DURATION');
		$entry->$duration_field = number_format($step_time, 6);	
		
		JLog::add($entry, $priority, 'com_componentarchitect_generate');	
		
		return true;		
	}
} // End of class 
//[%%END_CUSTOM_CODE%%]
?> 	