<?php
/**
 * @version 		$Id:$
 * @name			Nicegallery (Release 1.0.0)
 * @author			 ()
 * @package			com_nicegallery
 * @subpackage		com_nicegallery.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: view.html.php 408 2014-10-19 18:31:00Z BrianWade $
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
 * View class for a list of comentses.
 *
 */
class NicegalleryViewComentses extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $creators;
	protected $can_do;

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
		
		$this->can_do = JHelperContent::getActions('com_nicegallery');
				
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
			
		JToolbarHelper::title(JText::_('COM_NICEGALLERY_COMENTSES_LIST_HEADER'), 'stack comentses');

		if ($this->can_do->get('core.create')) 
		{
			JToolbarHelper::addNew('coments.add','JTOOLBAR_NEW');
		}
		
		if ($this->can_do->get('core.edit') OR $this->can_do->get('core.edit.own')) 
		{
			JToolbarHelper::editList('coments.edit','JTOOLBAR_EDIT');
		}
		if ($this->can_do->get('core.edit.state') ) 
		{

			if ($this->state->get('filter.state') != 2)
			{
				JToolbarHelper::custom('comentses.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
				JToolbarHelper::custom('comentses.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1 ) 
			{
				if ($this->state->get('filter.state') != 2) 
				{
					JToolbarHelper::archiveList('comentses.archive','JTOOLBAR_ARCHIVE');
				}
				else 
				{
					if ($this->state->get('filter.state') == 2) 
					{
						JToolbarHelper::unarchiveList('comentses.publish', 'JTOOLBAR_UNARCHIVE');
					}
				}
			}
		}
		
		if ($this->can_do->get('core.edit.state')) 
		{
			JToolbarHelper::custom('comentses.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}

		if ($this->state->get('filter.state') == -2)
		{
			if ($this->can_do->get('core.delete'))
			{
				JToolbarHelper::deleteList('', 'comentses.delete','JTOOLBAR_EMPTY_TRASH');
			}
		}
		else 
		{
			if ($this->can_do->get('core.edit.state')) 
			{
				JToolbarHelper::trash('comentses.trash','JTOOLBAR_TRASH');
			}
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_nicegallery') AND $user->authorise('core.edit', 'com_nicegallery') AND $user->authorise('core.edit.state', 'com_nicegallery'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
				
		if ($user->authorise('core.admin', 'com_nicegallery')) 
		{
			JToolbarHelper::preferences('com_nicegallery');
		}
	}
	/**
	 * Add the page sidebar.
	 *
	 */
	protected function addSidebar()
	{	
		JHtmlSidebar::setAction('index.php?option=com_nicegallery&view=comentses');
				
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

			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.name' => JText::_('COM_NICEGALLERY_FIELD_NAME_LABEL'),
			'a.state' => JText::_('COM_NICEGALLERY_FIELD_STATUS_LABEL'),
			'access_level' => JText::_('JGRID_HEADING_ACCESS'),
			'a.created_by' => JText::_('COM_NICEGALLERY_FIELD_CREATED_BY_LABEL'),
			'a.created' => JText::_('COM_NICEGALLERY_FIELD_CREATED_LABEL'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.hits' => JText::_('JGLOBAL_HITS'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}	
}
