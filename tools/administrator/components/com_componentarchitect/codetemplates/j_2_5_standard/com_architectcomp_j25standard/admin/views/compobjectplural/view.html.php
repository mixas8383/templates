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
 * View class for a list of [%%compobjectplural%%].
 *
 */
class [%%ArchitectComp%%]View[%%CompObjectPlural%%] extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	[%%IF INCLUDE_ASSETACL%%]
	protected $can_do;
	[%%ENDIF INCLUDE_ASSETACL%%]	
	[%%FOREACH FILTER_FIELD%%]
		[%%IF FIELD_FILTER_LINK%%]
	protected $[%%FIELD_FOREIGN_OBJECT_PLURAL%%];
		[%%ELSE FIELD_FILTER_LINK%%]
	protected $[%%FIELD_CODE_NAME%%]_values;
		[%%ENDIF FIELD_FILTER_LINK%%]		
	[%%ENDFOR FILTER_FIELD%%]

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		[%%IF INCLUDE_ASSETACL%%]
			[%%IF GENERATE_CATEGORIES%%]
		$this->can_do		= [%%ArchitectComp%%][%%CompObjectPlural%%]Helper::getActions($this->state->get('filter.category_id'));
			[%%ELSE GENERATE_CATEGORIES%%]
		$this->can_do		= [%%ArchitectComp%%][%%CompObjectPlural%%]Helper::getActions();
			[%%ENDIF GENERATE_CATEGORIES%%]
		[%%ENDIF INCLUDE_ASSETACL%%]	
				
		[%%FOREACH FILTER_FIELD%%]
			[%%IF FIELD_FILTER_LINK%%]
		$this->[%%FIELD_FOREIGN_OBJECT_PLURAL%%]	= $this->get('[%%FIELD_FOREIGN_OBJECT_PLURAL_UCFIRST%%]');
			[%%ELSE FIELD_FILTER_LINK%%]
		$this->[%%FIELD_CODE_NAME%%]_values	= $this->get('[%%FIELD_CODE_NAME_UCFIRST%%]values');
			[%%ENDIF FIELD_FILTER_LINK%%]		
		[%%ENDFOR FILTER_FIELD%%]
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
		JToolbarHelper::title(JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_LIST_HEADER'), 'stack [%%compobjectplural%%]');

		[%%IF INCLUDE_ASSETACL%%]
		if ($this->can_do->get('core.create')) 
		{
			JToolbarHelper::addNew('[%%compobject%%].add','JTOOLBAR_NEW');
		}
		[%%ELSE INCLUDE_ASSETACL%%]
		JToolbarHelper::addNew('[%%compobject%%].add','JTOOLBAR_NEW');
		[%%ENDIF INCLUDE_ASSETACL%%]
		[%%IF INCLUDE_ASSETACL%%]
		if ($this->can_do->get('core.edit') OR $this->can_do->get('core.edit.own')) 
		{
			JToolbarHelper::editList('[%%compobject%%].edit','JTOOLBAR_EDIT');
		}
		[%%ELSE INCLUDE_ASSETACL%%]
		JToolbarHelper::editList('[%%compobject%%].edit','JTOOLBAR_EDIT');
		[%%ENDIF INCLUDE_ASSETACL%%]

		[%%IF INCLUDE_STATUS%%]
			[%%IF INCLUDE_ASSETACL%%]
		if ($this->can_do->get('core.edit.state') ) 
		{
			[%%ENDIF INCLUDE_ASSETACL%%]

			if ($this->state->get('filter.state') != 2)
			{
				JToolbarHelper::divider();
				JToolbarHelper::custom('[%%compobjectplural%%].publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
				JToolbarHelper::custom('[%%compobjectplural%%].unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1 ) 
			{
				JToolbarHelper::divider();
				if ($this->state->get('filter.state') != 2) 
				{
					JToolbarHelper::archiveList('[%%compobjectplural%%].archive','JTOOLBAR_ARCHIVE');
				}
				else 
				{
					if ($this->state->get('filter.state') == 2) 
					{
						JToolbarHelper::unarchiveList('[%%compobjectplural%%].publish', 'JTOOLBAR_UNARCHIVE');
					}
				}
			}
			[%%IF INCLUDE_ASSETACL%%]
		}
			[%%ENDIF INCLUDE_ASSETACL%%]
		[%%ENDIF INCLUDE_STATUS%%]
		
		[%%IF INCLUDE_CHECKOUT%%]
			[%%IF INCLUDE_ASSETACL%%]
		if ($this->can_do->get('core.edit.state')) 
		{
			JToolbarHelper::custom('[%%compobjectplural%%].checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}
			[%%ELSE INCLUDE_ASSETACL%%]
		JToolbarHelper::custom('[%%compobjectplural%%].checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			[%%ENDIF INCLUDE_ASSETACL%%]
		[%%ENDIF INCLUDE_CHECKOUT%%]

		[%%IF INCLUDE_STATUS%%]
		if ($this->state->get('filter.state') == -2)
		{
				[%%IF INCLUDE_ASSETACL%%]
			if ($this->can_do->get('core.delete'))
			{
				JToolbarHelper::deleteList('', '[%%compobjectplural%%].delete','JTOOLBAR_EMPTY_TRASH');
			}
				[%%ELSE INCLUDE_ASSETACL%%]
			JToolbarHelper::deleteList('', '[%%compobjectplural%%].delete','JTOOLBAR_EMPTY_TRASH');
				[%%ENDIF INCLUDE_ASSETACL%%]
			
			JToolbarHelper::divider();
		}
		else 
		{
				[%%IF INCLUDE_ASSETACL%%]
			if ($this->can_do->get('core.edit.state')) 
			{
				JToolbarHelper::trash('[%%compobjectplural%%].trash','JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
				[%%ELSE INCLUDE_ASSETACL%%]
			JToolbarHelper::trash('[%%compobjectplural%%].trash','JTOOLBAR_TRASH');
			JToolbarHelper::divider();
				[%%ENDIF INCLUDE_ASSETACL%%]			
		}
		[%%ELSE INCLUDE_STATUS%%]
			[%%IF INCLUDE_ASSETACL%%]
		if ($this->can_do->get('core.delete'))
		{
			JToolbarHelper::deleteList('', '[%%compobjectplural%%].delete','JTOOLBAR_DELETE');
			JToolBarHelper::divider();
		}
			[%%ELSE INCLUDE_ASSETACL%%]
		JToolbarHelper::deleteList('', '[%%compobjectplural%%].delete','JTOOLBAR_DELETE');
		JToolbarHelper::divider();
			[%%ENDIF INCLUDE_ASSETACL%%]						
		[%%ENDIF INCLUDE_STATUS%%]
		
		[%%IF INCLUDE_ASSETACL%%]
		if ($this->can_do->get('core.admin')) 
		{
			JToolbarHelper::preferences('[%%com_architectcomp%%]');
			JToolbarHelper::divider();
		}
		[%%ELSE INCLUDE_ASSETACL%%]
			JToolbarHelper::preferences('[%%com_architectcomp%%]');
			JToolbarHelper::divider();
		[%%ENDIF INCLUDE_ASSETACL%%]
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
		
		JHTML::_('script','system/multiselect.js',false,true);
	}	
}
