<?php
/**
 * @version		$Id: view.html.php 411 2014-10-19 18:39:07Z BrianWade $
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
// No direct access
defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.view');
}	

/**
 * View class for a component wizard dialog.
 *
 */
class ComponentArchitectViewComponentWizard extends JViewLegacy
{
	protected $item;
	protected $state;
	protected $form;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// If 'close' set in url then just close the modal window by just having a script returned
		$app = JFactory::getApplication();
		$input = $app->input;	
		if ($input->get('close', '0') == '1')
		{	
			// close a modal window
			JFactory::getDocument()->addScriptDeclaration
			(
				"window.parent.location.href=window.parent.location.href;
				window.parent.SqueezeBox.close();"
			);	
			
			return;
		}	
			
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->document->setTitle(JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD'));

		$this->addToolbar();
		$this->_prepareDocument();
		parent::display($tpl);
		JRequest::setVar('hidemainmenu', true);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * 
	 */
	protected function addToolbar()
	{
		if (JRequest::getCmd('tmpl', '') != 'component')
		{
			JToolBarHelper::title(JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_HEADER'), 'componentwizard.png');
			
			JToolBarHelper::custom('componentwizard.wizardsave', 'new', '', JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_CREATE_BUTTON'), false);
			JToolbarHelper::cancel('componentwizard.cancel','JTOOLBAR_CANCEL');
			JToolBarHelper::divider();

			JToolbarHelper::help('JHELP_COMPONENTS_COMPONENTARCHITECT_COMPONENT_WIZARD', true, null, 'com_componentarchitect');
		}
	}	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$this->document->setTitle(JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_HEADER'));
		// Add Javscript functions for validation
		JHtml::_('behavior.formvalidation');
		if (version_compare(JVERSION, '3.0', 'lt'))
		{		
			JHtml::_('behavior.tooltip');	
		}
		else
		{
			JHtml::_('bootstrap.tooltip');		
			JHtml::_('behavior.keepalive');
		}				
		// load jQuery, if not loaded before in Joomla! 2.5
		if (version_compare(JVERSION, '3.0', 'lt') AND !JFactory::getApplication()->get('jquery'))
		{
			JFactory::getApplication()->set('jquery',true);
			$this->document->addScript(JUri::root()."media/com_componentarchitect/js/jquery.js");
			$this->document->addCustomTag( '<script type="text/javascript">jQuery.noConflict();</script>' );
		}

		
		$this->document->addStyleSheet(JUri::root()."media/com_componentarchitect/css/admin.css");

		$this->document->addScript(JUri::root() .'media/com_componentarchitect/js/formsubmitbutton.js');

	}	
}
//[%%END_CUSTOM_CODE%%]
