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
 * @version			$Id: view.html.php 417 2014-10-22 14:42:10Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_2_standard (Release 1.0.4)
 * @CAcopyright		Copyright (c)2013 - 2014  Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
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

/**
 * View to edit a [%%compobject%%].
 *
 */
class [%%ArchitectComp%%]View[%%CompObject%%] extends JViewLegacy
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
		[%%IF INCLUDE_DESCRIPTION%%]
			[%%IF GENERATE_PLUGINS%%]
				[%%IF GENERATE_PLUGINS_PAGEBREAK%%]
		if ($this->getLayout() == 'pagebreak')
		{
			// TODO: This is really dogy - should change this one day.
			$eName    = JRequest::getVar('e_name');
			$eName    = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);
			$document = JFactory::getDocument();
			$document->setTitle(JText::_('[%%COM_ARCHITECTCOMP%%]_PAGEBREAK_DOC_TITLE'));
			$this->eName = &$eName;
			parent::display($tpl);
			return;
		}
				[%%ENDIF GENERATE_PLUGINS_PAGEBREAK%%]
			[%%ENDIF GENERATE_PLUGINS%%]
		[%%ENDIF INCLUDE_DESCRIPTION%%]
				
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		[%%IF INCLUDE_ASSETACL%%]
			[%%IF INCLUDE_ASSETACL_RECORD%%]
		if (version_compare(JVERSION, '3.2.2', 'lt'))
		{
			$this->can_do = [%%ArchitectComp%%]Helper::getActions('[%%com_architectcomp%%]', '[%%compobject%%]', $this->item->id);
		}
		else
		{
			$this->can_do = JHelperContent::getActions('[%%com_architectcomp%%]', '[%%compobject%%]', $this->item->id);
		}
			[%%ELSE INCLUDE_ASSETACL_RECORD%%]
		if (version_compare(JVERSION, '3.2.2', 'lt'))
		{
			$this->can_do = [%%ArchitectComp%%]Helper::getActions('[%%com_architectcomp%%]');
		}
		else
		{
			$this->can_do = JHelperContent::getActions('[%%com_architectcomp%%]');
		}
			[%%ENDIF INCLUDE_ASSETACL_RECORD%%]			
		[%%ENDIF INCLUDE_ASSETACL%%]		

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() == 'modal')
		{
			[%%IF INCLUDE_LANGUAGE%%]
			$this->form->setFieldAttribute('language', 'readonly', 'true');
			[%%ENDIF INCLUDE_LANGUAGE%%]
			[%%IF GENERATE_CATEGORIES%%]
			$this->form->setFieldAttribute('catid', 'readonly', 'true');
			[%%ENDIF GENERATE_CATEGORIES%%]	
		}
		
		$this->addToolbar();
		$this->prepareDocument();		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
	
		$user		= JFactory::getUser();
		$user_id		= $user->get('id');
		$is_new		= ($this->item->id == 0);
		[%%IF INCLUDE_CHECKOUT%%]
		$checkedOut	= !($this->item->checked_out == 0 OR $this->item->checked_out == $user_id);
		[%%ENDIF INCLUDE_CHECKOUT%%]

		JToolbarHelper::title($is_new ? JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_NEW_HEADER') : JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_EDIT_HEADER'), '[%%compobjectplural%%].png');

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

		[%%IF INCLUDE_VERSIONS%%]
		if ($this->state->params->get('save_history', 1) AND $this->state->params->get('[%%compobject%%]_save_history', 1)
			AND !$is_new  
			[%%IF INCLUDE_ASSETACL%%]
				AND ($this->can_do->get('core.edit') 
				[%%IF INCLUDE_CREATED%%]
				OR ($this->can_do->get('core.edit.own') AND $this->item->created_by == $user_id)
				[%%ENDIF INCLUDE_CREATED%%]
				)
			[%%ENDIF INCLUDE_ASSETACL%%]		
			)
		{
			$item_id = $this->item->id;
			$type_alias = '[%%com_architectcomp%%].[%%compobject%%]';
			JToolbarHelper::versions($type_alias, $item_id);
		}
		[%%ENDIF INCLUDE_VERSIONS%%]
				
		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('[%%compobject%%].cancel','JTOOLBAR_CANCEL');
		}
		else
		{
			JToolbarHelper::cancel('[%%compobject%%].cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Prepares the document
	 */
	protected function prepareDocument()
	{
		// Include HTML Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');	

	}	
}
