<?php
/**
 * @version 		$Id:$
 * @name			Simplepoll (Release 1.0.0)
 * @author			 ()
 * @package			com_simplepoll
 * @subpackage		com_simplepoll.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: controller.php 408 2014-10-19 18:31:00Z BrianWade $
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

class SimplepollController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * 
	 */
	protected $default_view = 'polls';

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
		$view	= $this->input->getString('view', $this->default_view);
		$layout = $this->input->getString('layout', 'default');
		$id		= $this->input->getInt('id');

		// Load the submenu.
		SimplepollHelper::addSubmenu($view);

		// Check for edit form.
		switch ($view)
		{
			case 'answers': 
				if ($layout == 'edit' AND !$this->checkEditId('com_simplepoll.edit.answers', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_simplepoll&view=answerses', false));

					return false;
				}
				break;				
			case 'poll': 
				if ($layout == 'edit' AND !$this->checkEditId('com_simplepoll.edit.poll', $id))
				{

					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_simplepoll&view=polls', false));

					return false;
				}
				break;				
		}
		parent::display();

		return $this;
	}
}