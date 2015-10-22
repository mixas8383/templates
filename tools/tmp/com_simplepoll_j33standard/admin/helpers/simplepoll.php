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
 * Architectcomp_name component helper.
 *
 */
class SimplepollHelper extends JHelperContent
{
	protected $category_component;
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * 
	 */
	public function __construct()
	{

	}	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $component  The component name.
	 * @param   string   $section    The access section name.
	 * @param   integer  $id         The item ID.
	 *
	 * @return  JObject
	 */
	public static function getActions($component = '', $section = '', $id = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if ($section AND $id)
		{
			$asset_name = $component . '.' . $section . '.' . (int) $id;
		}
		else
		{
			$section = 'component';
			$asset_name = $component;
		}
	
		if ($section == 'category')
		{
			$actions = JAccess::getActions($component, $section);
		}
		else
		{
			$actions = JAccess::getActions($component);
		}
		
		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $asset_name));
		}

		return $result;
	}
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * 
	 */
	public static function addSubmenu($view_name)
	{
		$config = JComponentHelper::getParams(JText::_('COM_SIMPLEPOLL_FIELD_CATEGORY_COMPONENT_DEFAULT'));
		$category_component = $config->get('category_component', JText::_('COM_SIMPLEPOLL_FIELD_CATEGORY_COMPONENT_DEFAULT'));	

		$active = $view_name == 'answerses'? true : false;
		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLEPOLL_ANSWERSES_SUBMENU'),
			'index.php?option=com_simplepoll&view=answerses',
			$view_name == 'answerses',
			$active
		);
	
		$active = $view_name == 'polls'? true : false;
		JHtmlSidebar::addEntry(
			JText::_('COM_SIMPLEPOLL_POLLS_SUBMENU'),
			'index.php?option=com_simplepoll&view=polls',
			$view_name == 'polls',
			$active
		);
	
		if ($category_component != JText::_('COM_SIMPLEPOLL_FIELD_CATEGORY_COMPONENT_NO_CATEGORIES'))
		{	
			$active = $view_name == 'categories'? true : false;
			
			JHtmlSidebar::addEntry(
				JText::_('COM_SIMPLEPOLL_CATEGORIES_SUBMENU'),
				'index.php?option=com_categories&extension='.$category_component,
				$view_name == 'categories',
				$active
			);
			if ($view_name=='categories')
			{
				JToolbarHelper::title(
					JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE',JText::_($category_component)),
					$category_component.'-categories');
			}	
		}		
	}
}