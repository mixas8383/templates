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
 * View to edit a field.
 *
 */
class ComponentArchitectViewField extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	protected $can_do;
	
	//[%%START_CUSTOM_CODE%%]
	protected $field_types;	
	//[%%END_CUSTOM_CODE%%]		
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
				
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		
		$model = $this->getModel();

		//[%%START_CUSTOM_CODE%%]
		/* get all the published field types */			
		$field_types_model = JModelLegacy::getInstance('fieldtypes', 'ComponentArchitectModel', array('ignore_request' => true));
		$field_types_model->setState('list.ordering', 'a.name');
		$field_types_model->setState('list.direction', 'ASC');
		$field_types_model->setState('list.select', 'a.*');	

		$this->field_types = $field_types_model->getItems();
		//[%%END_CUSTOM_CODE%%]	
		// Get allowed actions
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		if ($this->getLayout() != 'modal')
		{	
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
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			JRequest::setVar('hidemainmenu', true);
		}
		else
		{
			JFactory::getApplication()->input->set('hidemainmenu', true);
		}
		
		$user		= JFactory::getUser();
		$user_id		= $user->get('id');
		$is_new		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 OR $this->item->checked_out == $user_id);
		//[%%START_CUSTOM_CODE%%]
		$is_predefined = ($this->item->predefined_field == '1');
		//[%%END_CUSTOM_CODE%%]
		
		JToolbarHelper::title($is_new ? JText::_('COM_COMPONENTARCHITECT_FIELDS_NEW_HEADER') : JText::_('COM_COMPONENTARCHITECT_FIELDS_EDIT_HEADER'), 'fields.png');

		//[%%START_CUSTOM_CODE%%]
		// Pre-defined fields cannot be changed from the edit form
		if (!$is_predefined)	
		{
		//[%%END_CUSTOM_CODE%%]			
			JToolbarHelper::apply('field.apply', 'JTOOLBAR_APPLY');
			JToolbarHelper::save('field.save', 'JTOOLBAR_SAVE');

			JToolbarHelper::custom('field.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		//[%%START_CUSTOM_CODE%%]
		}
		else
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_COMPONENTARCHITECT_FIELDS_ERROR_PREDEFINED_FIELD_CANNOT_CHANGE'), 'warning');
		}		
		//[%%END_CUSTOM_CODE%%]
		
		// If an existing item, can save to a copy.
		if (!$is_new )	
		{
			JToolbarHelper::custom('field.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if ($is_new)
		{
			JToolbarHelper::cancel('field.cancel','JTOOLBAR_CANCEL');
		}
		else
		{
			JToolbarHelper::cancel('field.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_COMPONENTARCHITECT_FIELD_EDIT', true, null, 'com_componentarchitect');
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
			
		// Add Javascript functions
		JHtml::_('behavior.formvalidation');
		
		if (version_compare(JVERSION, '3.0', 'lt'))
		{		
			JHtml::_('behavior.tooltip');	
		}
		else
		{
			JHtml::_('bootstrap.tooltip');		
			JHtml::_('behavior.keepalive');
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
				
		// load jQuery, if not loaded before in Joomla! 2.5
		if (version_compare(JVERSION, '3.0', 'lt') AND !JFactory::getApplication()->get('jquery'))
		{
			JFactory::getApplication()->set('jquery',true);
			$this->document->addScript(JUri::root().'media/com_componentarchitect/js/jquery.js');
			$this->document->addCustomTag( '<script type="text/javascript">jQuery.noConflict();</script>' );
		}		
		//[%%START_CUSTOM_CODE%%]
		$this->document->addScript(JUri::root() .'media/com_componentarchitect/js/componentarchitect.js');
		//[%%END_CUSTOM_CODE%%]	
						
		$this->document->addScript(JUri::root() .'media/com_componentarchitect/js/componentarchitectvalidate.js');
		
		$this->document->addScript(JUri::root() .'media/com_componentarchitect/js/formsubmitbutton.js');
		
		JText::script('COM_COMPONENTARCHITECT_ERROR_ON_FORM');
	}	
}
