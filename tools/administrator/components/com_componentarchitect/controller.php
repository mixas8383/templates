<?php
/**
 * @version 		$Id: controller.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: controller.php 806 2013-12-24 13:24:16Z BrianWade $
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
	jimport('joomla.application.component.controller');
}

class ComponentArchitectController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * 
	 */
	protected $default_view = 'dashboard';

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JControllerLegacy		This object to support chaining.
	 * 
	 */
	public function display($cachable = false, $url_params = false)
	{
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			$view	= JRequest::getCmd('view', $this->default_view);
			$layout = JRequest::getCmd('layout', 'default');
			$id		= JRequest::getInt('id');
		}
		else
		{
			$view	= $this->input->get('view', $this->default_view);
			$layout = $this->input->get('layout', 'default');
			$id		= $this->input->getInt('id');
		}

		// Load the submenu.
		ComponentArchitectHelper::addSubmenu($view);

		// Check for edit form.
		switch ($view)
		{
			case 'component': 
				if ($layout == 'edit' AND !$this->checkEditId('com_componentarchitect.edit.component', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=components', false));

					return false;
				}
				break;				
			case 'componentobject': 
				if ($layout == 'edit' AND !$this->checkEditId('com_componentarchitect.edit.componentobject', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=componentobjects', false));

					return false;
				}
				break;				
			case 'fieldset': 
				if ($layout == 'edit' AND !$this->checkEditId('com_componentarchitect.edit.fieldset', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=fieldsets', false));

					return false;
				}
				break;				
			case 'field': 
				if ($layout == 'edit' AND !$this->checkEditId('com_componentarchitect.edit.field', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=fields', false));

					return false;
				}
				break;				
			case 'fieldtype': 
				if ($layout == 'edit' AND !$this->checkEditId('com_componentarchitect.edit.fieldtype', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=fieldtypes', false));

					return false;
				}
				break;				
			case 'codetemplate': 
				if ($layout == 'edit' AND !$this->checkEditId('com_componentarchitect.edit.codetemplate', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_componentarchitect&view=codetemplates', false));

					return false;
				}
				break;				
		}
		parent::display();

		return $this;
	}
}
