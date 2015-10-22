<?php
/**
 * @version 		$Id:$
 * @name			Videomanager (Release 1.0.0)
 * @author			 ()
 * @package			com_videomanager
 * @subpackage		com_videomanager.finder
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: finder.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.finder
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
 * Smart Search Videomanager Plugin
 *
 */
class PlgVideomanagerFinder extends JPlugin
{
	/**
	 * Smart Search after save config method
	 * Config is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the config is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$config	A JTableConfig object
	 * @param	boolean		$is_new		If the config has just been created
	 */
	public function onConfigAfterSave($context, $config, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.configs');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $config, $is_new));

	}
	/**
	 * Smart Search before save config method
	 * Config is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$config	A JTableConfig object
	 * @param	boolean		$is_new		If the config has just been created
	 */
	public function onConfigBeforeSave($context, $config, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.configs');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $config, $is_new));

	}
	/**
	 * Smart Search after delete config method
	 * config is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the config is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$config	A JTableConfig object
	 * 
	 */
	public function onConfigAfterDelete($context, $config)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.configs');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $config));

	}
	/**
	 * Smart Search change state config method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onConfigChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.configs');

		// Trigger the onFinderChangeState event.
		$dispatcher->trigger('onFinderChangeState', array($context, $pks, $value));
	}
	/**
	 * Smart Search after save videos method
	 * Videos is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the videos is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$videos	A JTableVideos object
	 * @param	boolean		$is_new		If the videos has just been created
	 */
	public function onVideosAfterSave($context, $videos, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.videoses');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $videos, $is_new));

	}
	/**
	 * Smart Search before save videos method
	 * Videos is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$videos	A JTableVideos object
	 * @param	boolean		$is_new		If the videos has just been created
	 */
	public function onVideosBeforeSave($context, $videos, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.videoses');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $videos, $is_new));

	}
	/**
	 * Smart Search after delete videos method
	 * videos is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the videos is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$videos	A JTableVideos object
	 * 
	 */
	public function onVideosAfterDelete($context, $videos)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.videoses');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $videos));

	}
	/**
	 * Smart Search change state videos method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onVideosChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.videoses');

		// Trigger the onFinderChangeState event.
		$dispatcher->trigger('onFinderChangeState', array($context, $pks, $value));
	}
	/**
	 * Smart Search after save categories method
	 * Categories is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the categories is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$categories	A JTableCategories object
	 * @param	boolean		$is_new		If the categories has just been created
	 */
	public function onCategoriesAfterSave($context, $categories, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.categorieses');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $categories, $is_new));

	}
	/**
	 * Smart Search before save categories method
	 * Categories is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$categories	A JTableCategories object
	 * @param	boolean		$is_new		If the categories has just been created
	 */
	public function onCategoriesBeforeSave($context, $categories, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.categorieses');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $categories, $is_new));

	}
	/**
	 * Smart Search after delete categories method
	 * categories is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the categories is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$categories	A JTableCategories object
	 * 
	 */
	public function onCategoriesAfterDelete($context, $categories)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.categorieses');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $categories));

	}
	/**
	 * Smart Search change state categories method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onCategoriesChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.categorieses');

		// Trigger the onFinderChangeState event.
		$dispatcher->trigger('onFinderChangeState', array($context, $pks, $value));
	}
	/**
	 * Smart Search change category state method
	 *
	 * @param   string   $extension  The extension whose category has been updated.
	 * @param   array    $pks        A list of primary key ids of the content that has changed state.
	 * @param   integer  $value      The value of the state that the content has been changed to.
	 * 
	 */
	public function onCategoryChangeState($extension, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');

		// Trigger the onFinderCategoryChangeState event.
		$dispatcher->trigger('onFinderCategoryChangeState', array($extension, $pks, $value));

	}
}
