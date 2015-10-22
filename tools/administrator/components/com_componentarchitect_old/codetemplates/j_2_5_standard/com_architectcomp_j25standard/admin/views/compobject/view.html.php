<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].admin
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @version			$Id: view.html.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_2_5_standard (Release 1.0.4)
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

jimport('joomla.application.component.view');

/**
 * View to edit a [%%compobject%%].
 *
 */
class [%%ArchitectComp%%]View[%%CompObject%%] extends JView
{
	protected $form;
	protected $item;
	protected $state;
	protected $can_do;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		[%%IF INCLUDE_ASSETACL%%]
			[%%IF INCLUDE_ASSETACL_RECORD%%]
				[%%IF GENERATE_CATEGORIES%%]		
		$this->can_do = [%%ArchitectComp%%][%%CompObjectPlural%%]Helper::getActions(0, $this->item->id);
				[%%ELSE GENERATE_CATEGORIES%%]
		$this->can_do = [%%ArchitectComp%%][%%CompObjectPlural%%]Helper::getActions($this->item->id);
				[%%ENDIF GENERATE_CATEGORIES%%]
			[%%ELSE INCLUDE_ASSETACL_RECORD%%]
		$this->can_do = [%%ArchitectComp%%][%%CompObjectPlural%%]Helper::getActions();
			[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
		[%%ENDIF INCLUDE_ASSETACL%%]		

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->_prepareDocument();		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		
	
		$user		= JFactory::getUser();
		$user_id		= $user->get('id');
		$is_new		= ($this->item->id == 0);
		[%%IF INCLUDE_CHECKOUT%%]
		$checkedOut	= !($this->item->checked_out == 0 OR $this->item->checked_out == $user_id);
		[%%ENDIF INCLUDE_CHECKOUT%%]

		JToolBarHelper::title($is_new ? JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_NEW_HEADER') : JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_EDIT_HEADER'), '[%%compobjectplural%%].png');

		[%%IF INCLUDE_ASSETACL%%]
		// If not checked out, can save the item.
		if (($this->can_do->get('core.edit') 
			OR $this->can_do->get('core.create') 
			[%%IF INCLUDE_CREATED%%]
			OR ($this->can_do->get('core.edit.own') AND $this->item->created_by == $user_id)
			[%%ENDIF INCLUDE_CREATED%%]
			)
			[%%IF INCLUDE_CHECKOUT%%]
			AND !$checkedOut 
			[%%ENDIF INCLUDE_CHECKOUT%%]			
			) 
		{
			JToolbarHelper::apply('[%%compobject%%].apply', 'JTOOLBAR_APPLY');
			JToolbarHelper::save('[%%compobject%%].save', 'JTOOLBAR_SAVE');

			if ($this->can_do->get('core.create'))
			{
				JToolbarHelper::custom('[%%compobject%%].save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		}
			[%%IF INCLUDE_COPY%%]
		// If an existing item, can save to a copy.
		if (!$is_new AND $this->can_do->get('core.create'))		
			[%%ENDIF INCLUDE_COPY%%]
		[%%ELSE INCLUDE_ASSETACL%%]

		JToolbarHelper::apply('[%%compobject%%].apply', 'JTOOLBAR_APPLY');
		JToolbarHelper::save('[%%compobject%%].save', 'JTOOLBAR_SAVE');

		JToolbarHelper::custom('[%%compobject%%].save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			[%%IF INCLUDE_COPY%%]
		// If an existing item, can save to a copy.
		if (!$is_new )
			[%%ENDIF INCLUDE_COPY%%]				
		[%%ENDIF INCLUDE_ASSETACL%%]		
		[%%IF INCLUDE_COPY%%]
		{
			JToolbarHelper::custom('[%%compobject%%].save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		[%%ENDIF INCLUDE_COPY%%]
		
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('[%%compobject%%].cancel','JTOOLBAR_CANCEL');
		}
		else
		{
			JToolbarHelper::cancel('[%%compobject%%].cancel', 'JTOOLBAR_CLOSE');
		}
		
		JToolbarHelper::divider();
	}
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		// Include HTML Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');	

		// Include custom admin css
		$this->document->addStyleSheet(JUri::root()."administrator/components/[%%com_architectcomp%%]/assets/css/admin.css");
			
		// Add Javscript functions for field display
		JHtml::_('behavior.tooltip');	
		
		// Add Javscript functions for validation
		JHtml::_('behavior.formvalidation');
				
		$this->document->addScript(JUri::root() ."administrator/components/[%%com_architectcomp%%]/assets/js/[%%architectcomp%%]validate.js");
		
		$this->document->addScript(JUri::root() ."administrator/components/[%%com_architectcomp%%]/assets/js/formsubmitbutton.js");
		
		JText::script('[%%COM_ARCHITECTCOMP%%]_ERROR_ON_FORM');
	}	
}
