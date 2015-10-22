<?php
/**
 * @version 		$Id: view.html.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: view.html.php 147 2014-05-07 14:53:41Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
 * @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @Joomlacopyright Copyright (c)2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @CAlicense		GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */
// Protect from unauthorized access
defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.view');
}	
/**
 * MVC View for Dashboard
 *
 */
class ComponentArchitectViewDashboard extends JViewLegacy
{
	protected $params;
	
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');


		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$buttons = $this->prepareButtons();
		$this->assignRef('buttons',$buttons);
		
		$this->addToolbar();
		$this->prepareDocument();


		parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * 
	 */
	protected function addToolbar()
	{

		
		JToolbarHelper::title(JText::_('COM_COMPONENTARCHITECT_VIEW_DASHBOARD_HEADER'), 'componentarchitect.png');
		
		JToolbarHelper::preferences('com_componentarchitect');
		JToolbarHelper::help('JHELP_COMPONENTS_COMPONENTARCHITECT_DASHBOARD', true, null, 'com_componentarchitect');
	}
	
	/**
	 * Prepare the dashboard buttons
	 */
	protected function prepareButtons()
	{
		$buttons = array();

		//[%%START_CUSTOM_CODE%%]
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=componentwizard',
			'object'=>'componentwizard',
			'text'=>JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD'),
			'desc'=>JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_DESCRIPTION_DASHBOARD')
			);
			
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=generatedialog',
			'object'=>'generate',
			'text'=>JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG'),
			'desc'=>JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_DESCRIPTION_DASHBOARD')
			);

		$buttons[] = array('link'=>'',
			'object'=>'spacer',
			'text'=>'',
			'desc'=>''
			);
		//[%%END_CUSTOM_CODE%%]
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=components',
						   'object'=>'components',
						   'text'=>JText::_('COM_COMPONENTARCHITECT_COMPONENTS'),
						   'desc'=>JText::_('COM_COMPONENTARCHITECT_COMPONENT_DESCRIPTION_DASHBOARD')
						   );
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=componentobjects',
						   'object'=>'componentobjects',
						   'text'=>JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS'),
						   'desc'=>JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECT_DESCRIPTION_DASHBOARD')
						   );
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=fieldsets',
						   'object'=>'fieldsets',
						   'text'=>JText::_('COM_COMPONENTARCHITECT_FIELDSETS'),
						   'desc'=>JText::_('COM_COMPONENTARCHITECT_FIELDSET_DESCRIPTION_DASHBOARD')
						   );
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=fields',
						   'object'=>'fields',
						   'text'=>JText::_('COM_COMPONENTARCHITECT_FIELDS'),
						   'desc'=>JText::_('COM_COMPONENTARCHITECT_FIELD_DESCRIPTION_DASHBOARD')
						   );
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=fieldtypes',
						   'object'=>'fieldtypes',
						   'text'=>JText::_('COM_COMPONENTARCHITECT_FIELDTYPES'),
						   'desc'=>JText::_('COM_COMPONENTARCHITECT_FIELDTYPE_DESCRIPTION_DASHBOARD')
						   );
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=codetemplates',
						   'object'=>'codetemplates',
						   'text'=>JText::_('COM_COMPONENTARCHITECT_CODETEMPLATES'),
						   'desc'=>JText::_('COM_COMPONENTARCHITECT_CODETEMPLATE_DESCRIPTION_DASHBOARD')
						   );

		$buttons[] = array('link'=>'index.php?option=com_categories&extension=com_componentarchitect',
						   'object'=>'componentarchitect-categories',
						   'text'=>JText::_('JCATEGORIES'),
						   'desc'=>JText::_('COM_COMPONENTARCHITECT_CATEGORIES_DESCRIPTION_DASHBOARD')
						   );	
						   
		//[%%START_CUSTOM_CODE%%]
		$buttons[] = array('link'=>'index.php?option=com_componentarchitect&view=logs',
			'object'=>'logs',
			'text'=>JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS'),
			'desc'=>JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_DESCRIPTION_DASHBOARD')
			);
		//[%%END_CUSTOM_CODE%%]

		return $buttons;
	}	
	/**
	 * Prepares the document
	 */
	protected function prepareDocument()
	{
		$this->document->setTitle(JText::_('COM_COMPONENTARCHITECT_VIEW_DASHBOARD_HEADER'));

		// Include custom admin css
		$this->document->addStyleSheet(JUri::root().'media/com_componentarchitect/css/admin.css');
		$this->document->addStyleSheet(JURI::root()."media/com_componentarchitect/css/categories.css");		

		// Add Javscript functions for field display
		JHtml::_('behavior.tooltip');
	
	}
}