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
 * @version			$Id: view.html.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_3_standard (Release 1.0.3)
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
 * View class for a list of [%%compobjectplural%%].
 *
 */
class [%%ArchitectComp%%]View[%%CompObjectPlural%%] extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	[%%IF INCLUDE_CREATED%%]
	protected $creators;
	[%%ENDIF INCLUDE_CREATED%%]	
	[%%IF INCLUDE_ASSETACL%%]
	protected $can_do;
	[%%ENDIF INCLUDE_ASSETACL%%]	
	[%%FOREACH FILTER_FIELD%%]
		[%%IF FIELD_LINK%%]
	protected $[%%FIELD_FOREIGN_OBJECT_PLURAL%%];
		[%%ELSE FIELD_LINK%%]
	protected $[%%FIELD_CODE_NAME%%]_values;
		[%%ENDIF FIELD_LINK%%]		
	[%%ENDFOR FILTER_FIELD%%]

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		[%%IF INCLUDE_ASSETACL%%]
		[%%IF GENERATE_CATEGORIES%%]
		$this->can_do = JHelperContent::getActions('[%%com_architectcomp%%]', 'category', $this->state->get('filter.category_id'));
		[%%ELSE GENERATE_CATEGORIES%%]
		$this->can_do = JHelperContent::getActions('[%%com_architectcomp%%]');
		[%%ENDIF GENERATE_CATEGORIES%%]		
		[%%ENDIF INCLUDE_ASSETACL%%]	
				
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() !== 'modal')
		{
			$this->addSidebar();
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
				JToolbarHelper::custom('[%%compobjectplural%%].publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
				JToolbarHelper::custom('[%%compobjectplural%%].unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1 ) 
			{
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
		}
		else 
		{
				[%%IF INCLUDE_ASSETACL%%]
			if ($this->can_do->get('core.edit.state')) 
			{
				JToolbarHelper::trash('[%%compobjectplural%%].trash','JTOOLBAR_TRASH');
			}
				[%%ELSE INCLUDE_ASSETACL%%]
			JToolbarHelper::trash('[%%compobjectplural%%].trash','JTOOLBAR_TRASH');
				[%%ENDIF INCLUDE_ASSETACL%%]			
		}
		[%%ELSE INCLUDE_STATUS%%]
			[%%IF INCLUDE_ASSETACL%%]
		if ($this->can_do->get('core.delete'))
		{
			JToolbarHelper::deleteList('', '[%%compobjectplural%%].delete','JTOOLBAR_DELETE');
		}
			[%%ELSE INCLUDE_ASSETACL%%]
		JToolbarHelper::deleteList('', '[%%compobjectplural%%].delete','JTOOLBAR_DELETE');
			[%%ENDIF INCLUDE_ASSETACL%%]						
		[%%ENDIF INCLUDE_STATUS%%]

		[%%IF INCLUDE_BATCH%%]
		// Add a batch button
			[%%IF INCLUDE_ASSETACL%%]
		if ($user->authorise('core.create', '[%%com_architectcomp%%]') AND $user->authorise('core.edit', '[%%com_architectcomp%%]') AND $user->authorise('core.edit.state', '[%%com_architectcomp%%]'))
		{
			[%%ENDIF INCLUDE_ASSETACL%%]
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
			[%%IF INCLUDE_ASSETACL%%]
		}
			[%%ENDIF INCLUDE_ASSETACL%%]		
		[%%ENDIF INCLUDE_BATCH%%]
				
		[%%IF INCLUDE_ASSETACL%%]
		if ($user->authorise('core.admin', '[%%com_architectcomp%%]')) 
		{
			JToolbarHelper::preferences('[%%com_architectcomp%%]');
		}
		[%%ELSE INCLUDE_ASSETACL%%]
		JToolbarHelper::preferences('[%%com_architectcomp%%]');
		[%%ENDIF INCLUDE_ASSETACL%%]
	}
	/**
	 * Add the page sidebar.
	 *
	 */
	protected function addSidebar()
	{	
		JHtmlSidebar::setAction('index.php?option=[%%com_architectcomp%%]&view=[%%compobjectplural%%]');
				
		$this->sidebar = JHtmlSidebar::render();			
	}	
	
	/**
	 * Prepares the document
	 */
	protected function prepareDocument()
	{
		// Include HTML Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');	
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

			[%%IF INCLUDE_ORDERING%%]
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			[%%ENDIF INCLUDE_ORDERING%%]
			[%%IF INCLUDE_NAME%%]
			'a.name' => JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_NAME_LABEL'),
			[%%ENDIF INCLUDE_NAME%%]
			[%%FOREACH FILTER_FIELD%%]
				[%%IF FIELD_LINK%%]
			'[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]' => JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_HEADING_[%%FIELD_CODE_NAME_UPPER%%]'),
				[%%ELSE FIELD_LINK%%]
			'a.[%%FIELD_CODE_NAME%%]' => JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_HEADING_[%%FIELD_CODE_NAME_UPPER%%]'),
				[%%ENDIF FIELD_LINK%%]
			[%%ENDFOR FILTER_FIELD%%]						
			[%%IF GENERATE_CATEGORIES%%]
			'category_title' => JText::_('JCATEGORY'),
			[%%ENDIF GENERATE_CATEGORIES%%]	
			[%%IF INCLUDE_STATUS%%]
			'a.state' => JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_STATUS_LABEL'),
			[%%ENDIF INCLUDE_STATUS%%]					
			[%%IF INCLUDE_ACCESS%%]
			'access_level' => JText::_('JGRID_HEADING_ACCESS'),
			[%%ENDIF INCLUDE_ACCESS%%]
			[%%IF INCLUDE_CREATED%%]
			'a.created_by' => JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_BY_LABEL'),
			'a.created' => JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_LABEL'),
			[%%ENDIF INCLUDE_CREATED%%]
			[%%IF INCLUDE_LANGUAGE%%]
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			[%%ENDIF INCLUDE_LANGUAGE%%]
			[%%IF INCLUDE_HITS%%]
			'a.hits' => JText::_('JGLOBAL_HITS'),
			[%%ENDIF INCLUDE_HITS%%]			
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}	
}
