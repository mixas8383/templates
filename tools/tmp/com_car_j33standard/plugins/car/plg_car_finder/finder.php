<?php
/**
 * @version 		$Id:$
 * @name			Car (Release 1.0.0)
 * @author			 ()
 * @package			com_car
 * @subpackage		com_car.finder
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
 * Smart Search Car Plugin
 *
 */
class PlgCarFinder extends JPlugin
{
	/**
	 * Smart Search after save drive method
	 * Drive is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the drive is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$drive	A JTableDrive object
	 * @param	boolean		$is_new		If the drive has just been created
	 */
	public function onDriveAfterSave($context, $drive, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.drives');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $drive, $is_new));

	}
	/**
	 * Smart Search before save drive method
	 * Drive is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$drive	A JTableDrive object
	 * @param	boolean		$is_new		If the drive has just been created
	 */
	public function onDriveBeforeSave($context, $drive, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.drives');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $drive, $is_new));

	}
	/**
	 * Smart Search after delete drive method
	 * drive is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the drive is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$drive	A JTableDrive object
	 * 
	 */
	public function onDriveAfterDelete($context, $drive)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.drives');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $drive));

	}
	/**
	 * Smart Search change state drive method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onDriveChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.drives');

		// Trigger the onFinderChangeState event.
		$dispatcher->trigger('onFinderChangeState', array($context, $pks, $value));
	}
	/**
	 * Smart Search after save model method
	 * Model is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the model is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$model	A JTableModel object
	 * @param	boolean		$is_new		If the model has just been created
	 */
	public function onModelAfterSave($context, $model, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.models');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $model, $is_new));

	}
	/**
	 * Smart Search before save model method
	 * Model is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$model	A JTableModel object
	 * @param	boolean		$is_new		If the model has just been created
	 */
	public function onModelBeforeSave($context, $model, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.models');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $model, $is_new));

	}
	/**
	 * Smart Search after delete model method
	 * model is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the model is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$model	A JTableModel object
	 * 
	 */
	public function onModelAfterDelete($context, $model)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.models');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $model));

	}
	/**
	 * Smart Search change state model method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onModelChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.models');

		// Trigger the onFinderChangeState event.
		$dispatcher->trigger('onFinderChangeState', array($context, $pks, $value));
	}
	/**
	 * Smart Search after save car method
	 * Car is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the car is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$car	A JTableCar object
	 * @param	boolean		$is_new		If the car has just been created
	 */
	public function onCarAfterSave($context, $car, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.cars');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $car, $is_new));

	}
	/**
	 * Smart Search before save car method
	 * Car is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$car	A JTableCar object
	 * @param	boolean		$is_new		If the car has just been created
	 */
	public function onCarBeforeSave($context, $car, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.cars');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $car, $is_new));

	}
	/**
	 * Smart Search after delete car method
	 * car is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the car is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$car	A JTableCar object
	 * 
	 */
	public function onCarAfterDelete($context, $car)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.cars');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $car));

	}
	/**
	 * Smart Search change state car method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onCarChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.cars');

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
