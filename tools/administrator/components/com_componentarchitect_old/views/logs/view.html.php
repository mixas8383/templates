<?php
/**
 * @version			$Id: view.html.php 411 2014-10-19 18:39:07Z BrianWade $
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
	jimport('joomla.application.component.view');
}	

/**
 * MVC View for Log
 *
 */
class ComponentArchitectViewLogs extends JViewLegacy
{
	protected $params;
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		$this->state	= $this->get('State');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$tag = JRequest::getCmd('tag',null);
		if(empty($tag)) $tag = null;
		$this->assign('tag', $tag);
		if ($this->getLayout() !== 'raw')
		{		
			// Get a list of log names
			if(!class_exists('ComponentArchitectModelLogs')) JLoader::import('models.log', JPATH_COMPONENT_ADMINISTRATOR);
			$model = new ComponentArchitectModelLogs();
			$this->assign('logs', $model->getLogFiles());
			
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$this->addSidebar();
			}
					
			$this->addToolbar();
			$this->_prepareDocument();
		}

		parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * 
	 */
	protected function addToolbar()
	{

		
		JToolBarHelper::title(JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_HEADER'), 'logs.png');

		JToolbarHelper::preferences('com_componentarchitect');
		JToolbarHelper::help('JHELP_COMPONENTS_COMPONENTARCHITECT_VIEW_LOGS', true, null, 'com_componentarchitect');

		// Add a dashboard button.
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Link', 'dashboard', JText::_('COM_COMPONENTARCHITECT_DASHBOARD'), 'index.php?option=com_componentarchitect&view=dashboard');		
		JToolBarHelper::divider();		
	}
	/**
	 * Add the page sidebar.
	 *
	 */
	protected function addSidebar()
	{	
		
		JHtmlSidebar::setAction('index.php?option=com_componentarchitect&view=logs');
		
		$this->sidebar = JHtmlSidebar::render();			
	}	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$this->document->setTitle(JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_HEADER'));
	
		$this->document->addStyleSheet(JUri::root()."media/com_componentarchitect/css/admin.css");

		if (version_compare(JVERSION, '3.0', 'lt'))
		{		
			JHtml::_('behavior.tooltip');	
		}
		else
		{
			JHtml::_('bootstrap.tooltip');		
		}
	}
	
	/**
	 * From the array of log files this creates a set of select optins for the drop down list
	 */	
	function getLogList($logs)
	{
		$options = array();
		if(!empty($logs))
		{
			$options[] = JHtml::_('select.option',null,JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_CHOOSE_FILE_OPTION'));
			foreach($logs as $item)
			{
				$text = JText::_($item);
				$options[] = JHtml::_('select.option',$item,$text);
			}
		}
		return $options;
	}			
}
//[%%END_CUSTOM_CODE%%]