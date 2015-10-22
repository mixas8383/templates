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
 * @CAversion		Id: architectcomp.php 408 2014-10-19 18:31:00Z BrianWade $
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
 * Simplepoll controller class.
 *
 */
class SimplepollControllerSimplepoll extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * 
	 */
	protected $text_prefix = 'COM_SIMPLEPOLL';
	protected $category_component;
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * 
	 */
	public function __construct()
	{
		$config = JComponentHelper::getParams(JText::_('COM_SIMPLEPOLL_FIELD_CATEGORY_COMPONENT_DEFAULT'));
		$category_component = $config->get('category_component', JText::_('COM_SIMPLEPOLL_FIELD_CATEGORY_COMPONENT_DEFAULT'));	
		parent::__construct();

	}	

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 *
	 * @return	boolean
	 *
	 */
	protected function allowAdd($data = array())
	{
		
		$user		= JFactory::getUser();
		$allow		= null;
		$category_id	= JArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');

		if ($category_id) 
		{
			// If the category has been passed in the URL check it.
			$allow	= $user->authorise('core.create', $category_component.'.category.'.$category_id);
		}
		if ($allow === null) 
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		} 
		else 
		{
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * 
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		
		$user		= JFactory::getUser();
		$allow		= null;	
		$category_id	= JArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');


		if ($category_id) 
		{
			// The category has been set. Check the category permissions.
			$allow = $user->authorise('core.edit', $category_component.'.category.'.$category_id);
		} 
		if ($allow === null) 
		{
			// Since there is no asset tracking, revert to the component permissions.

			return parent::allowEdit($data, $key);
		} 
		else 
		{
			return $allow;
		}		

	}
}