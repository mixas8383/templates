<?php
/**
 * @version 		$Id:$
 * @name			Simplepoll (Release 1.0.0)
 * @author			 ()
 * @package			com_simplepoll
 * @subpackage		com_simplepoll.finder
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
 * Smart Search Simplepoll Plugin
 *
 */
class PlgSimplepollFinder extends JPlugin
{
	/**
	 * Smart Search after save answers method
	 * Answers is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the answers is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$answers	A JTableAnswers object
	 * @param	boolean		$is_new		If the answers has just been created
	 */
	public function onAnswersAfterSave($context, $answers, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.answerses');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $answers, $is_new));

	}
	/**
	 * Smart Search before save answers method
	 * Answers is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$answers	A JTableAnswers object
	 * @param	boolean		$is_new		If the answers has just been created
	 */
	public function onAnswersBeforeSave($context, $answers, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.answerses');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $answers, $is_new));

	}
	/**
	 * Smart Search after delete answers method
	 * answers is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the answers is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$answers	A JTableAnswers object
	 * 
	 */
	public function onAnswersAfterDelete($context, $answers)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.answerses');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $answers));

	}
	/**
	 * Smart Search change state answers method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onAnswersChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.answerses');

		// Trigger the onFinderChangeState event.
		$dispatcher->trigger('onFinderChangeState', array($context, $pks, $value));
	}
	/**
	 * Smart Search after save poll method
	 * Poll is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the poll is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$poll	A JTablePoll object
	 * @param	boolean		$is_new		If the poll has just been created
	 */
	public function onPollAfterSave($context, $poll, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.polls');

		// Trigger the onFinderAfterSave event.
		$dispatcher->trigger('onFinderAfterSave', array($context, $poll, $is_new));

	}
	/**
	 * Smart Search before save poll method
	 * Poll is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$poll	A JTablePoll object
	 * @param	boolean		$is_new		If the poll has just been created
	 */
	public function onPollBeforeSave($context, $poll, $is_new)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.polls');

		// Trigger the onFinderBeforeSave event.
		$dispatcher->trigger('onFinderBeforeSave', array($context, $poll, $is_new));

	}
	/**
	 * Smart Search after delete poll method
	 * poll is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the poll is saved
	 *
	 * @param	string		$context	The context of the item passed to the plugin
	 * @param	object		$poll	A JTablePoll object
	 * 
	 */
	public function onPollAfterDelete($context, $poll)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.polls');

		// Trigger the onFinderAfterDelete event.
		$dispatcher->trigger('onFinderAfterDelete', array($context, $poll));

	}
	/**
	 * Smart Search change state poll method
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item's state,
	 * is changed from the list view.
	 *
	 * @param   string   $context  The context for the item passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the records that have changed state.
	 * @param   integer  $value    The value of the state that the records have been changed to.
	 * 
	 */
	public function onPollChangeState($context, $pks, $value)
	{
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder.polls');

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
