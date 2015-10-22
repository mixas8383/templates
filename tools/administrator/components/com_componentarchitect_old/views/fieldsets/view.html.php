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

defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.view');
}	

/**
 * View class for a list of fieldsets.
 *
 */
class ComponentArchitectViewFieldsets extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $creators;
	protected $c1_components;
	protected $o_componentobjects;
	protected $predefined_fieldset_values;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		// First set pagination prefix to allow for uniqueness of pagination on different layouts
		if ($this->_layout == 'default' OR $this->_layout == '')
		{
			$pagination_prefix	= '';
		}
		else
		{
			$pagination_prefix	= $this->_layout;		
		}
		$model = $this->getModel();			
		$this->pagination	= $model->getPagination($pagination_prefix);
		
		// Now get Items and the State
		$this->items		= $this->get('Items');
		$this->state		= $this->get('State');
		$this->creators		= $this->get('Creators');
		
		// Get allowed actions
				
		$this->c1_components	= $this->get('C1Components');
		$this->o_componentobjects	= $this->get('OComponentobjects');
		$this->predefined_fieldset_values	= $this->get('Predefinedfieldsetvalues');
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() !== 'modal')
		{
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$this->addSidebar();
			}
			$this->addToolbar();
		}
		$this->prepareDocument();		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		$user  = JFactory::getUser();	
		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');
			
		JToolbarHelper::title(JText::_('COM_COMPONENTARCHITECT_FIELDSETS_LIST_HEADER'), 'stack fieldsets');

		JToolbarHelper::addNew('fieldset.add','JTOOLBAR_NEW');
		
		JToolbarHelper::editList('fieldset.edit','JTOOLBAR_EDIT');
		
		JToolbarHelper::custom('fieldsets.copy', 'copy.png', 'copy_f2.png','COM_COMPONENTARCHITECT_COPY', true);		

			JToolbarHelper::divider();
		
		
		JToolbarHelper::custom('fieldsets.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);

		JToolbarHelper::deleteList('', 'fieldsets.delete','JTOOLBAR_DELETE');
		JToolbarHelper::divider();
				
		JToolbarHelper::preferences('com_componentarchitect');
		
		JToolbarHelper::help('JHELP_COMPONENTS_COMPONENTARCHITECT_FIELDSETS', true, null, 'com_componentarchitect');
		
		// Add a dashboard button.
		$bar = JToolbar::getInstance('toolbar');
		$bar->appendButton('Link', 'dashboard', JText::_('COM_COMPONENTARCHITECT_DASHBOARD'), 'index.php?option=com_componentarchitect&view=dashboard');		
	}
	/**
	 * Add the page sidebar.
	 *
	 */
	protected function addSidebar()
	{	
			
		JHtmlSidebar::setAction('index.php?option=com_componentarchitect&view=fieldsets');
				
		$this->sidebar = JHtmlSidebar::render();			
	}	
	/**
	 * Prepares the document
	 */
	protected function prepareDocument()
	{
		// Include HTML Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');	
	
		// Include custom admin css
		$this->document->addStyleSheet(JUri::root().'media/com_componentarchitect/css/admin.css');
		
		// Add Javscript functions
		if (version_compare(JVERSION, '3.0', 'lt'))
		{		
			JHtml::_('behavior.tooltip');	
			JHtml::_('script','system/multiselect.js',false,true);
		}
		else
		{
			JHtml::_('bootstrap.tooltip');		
			JHtml::_('behavior.multiselect');
			JHtml::_('formbehavior.chosen', 'select');		
		}
		if (version_compare(JVERSION, '3.2', 'ge'))
		{		
			// Set some basic options
			$data['options'] = array(
				'filtersHidden'       => false,
				'defaultLimit'        => JFactory::getApplication()->getCfg('list_limit', 20),
				'searchFieldSelector' => '#filter_search',
				'orderFieldName'  => 'filter_order'
				);

			// Load search tools
			JHtml::_('searchtools.form', '#adminForm', $data['options']);	
		}		
		$this->document->addScript(JUri::root() .'media/com_componentarchitect/js/formsubmitbutton.js');
		
	}
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 */
	protected function getSortFields()
	{
		return array(

			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.name' => JText::_('COM_COMPONENTARCHITECT_FIELD_NAME_LABEL'),
			'c1_component_name' => JText::_('COM_COMPONENTARCHITECT_FIELDSETS_HEADING_COMPONENT_ID'),
			'o_component_object_name' => JText::_('COM_COMPONENTARCHITECT_FIELDSETS_HEADING_COMPONENT_OBJECT_ID'),
			'a.predefined_fieldset' => JText::_('COM_COMPONENTARCHITECT_FIELDSETS_HEADING_PREDEFINED_FIELDSET'),
			'a.created_by' => JText::_('COM_COMPONENTARCHITECT_FIELD_CREATED_BY_LABEL'),
			'a.created' => JText::_('COM_COMPONENTARCHITECT_FIELD_CREATED_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}			
}
