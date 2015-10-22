<?php
/**
 * @version 		$Id:$
 * @name			Car (Release 1.0.0)
 * @author			 ()
 * @package			com_car
 * @subpackage		com_car.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectbatch.php 408 2014-10-19 18:31:00Z BrianWade $
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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

/**
 * Extended Utility class for batch processing widgets.
 * 
 */
abstract class JHtmlDriveBatch
{
	/**
	 * Display a batch widget for the access level selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 */
	public static function access()
	{
		// Create the batch selector to change an access level on a selection list.
		$lines = array(
			'<label id="batch-access-lbl" for="batch-access" class="hasTip" title="' . JText::_('JLIB_HTML_BATCH_ACCESS_LABEL') . '::'
			. JText::_('JLIB_HTML_BATCH_ACCESS_LABEL_DESC') . '">', JText::_('JLIB_HTML_BATCH_ACCESS_LABEL'), '</label>',
			JHtml::_(
				'access.assetgrouplist',
				'batch[assetgroup_id]', '',
				array(
					'title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),
					'id' => 'batch-access')
			)
		);

		return implode("\n", $lines);
	}
	/**
	 * Display a batch widget for the tag selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 */
	public static function tag()
	{
		// Create the batch selector to tag items on a selection list.
		$lines = array(
			'<label id="batch-tag-lbl" for="batch-tag-id" class="hasTip"'
			. ' title="' . JText::_('JLIB_HTML_BATCH_TAG_LABEL') . '::' . JText::_('JLIB_HTML_BATCH_TAG_LABEL_DESC') . '">',
			JText::_('JLIB_HTML_BATCH_TAG_LABEL'),
			'</label>',
			'<select name="batch[tag]" id="batch-tag-id">',
			'<option value="">' . JText::_('JLIB_HTML_BATCH_TAG_NOCHANGE') . '</option>',
			JHtml::_('select.options', JHtml::_('tag.tags', array('filter.published' => array(1))), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}	
	/**
	 * Display a batch widget for the language selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 */
	public static function language()
	{
		// Create the batch selector to change the language on a selection list.
		$lines = array(
			'<label id="batch-language-lbl" for="batch-language" class="hasTip"'
			. ' title="' . JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL') . '::' . JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL_DESC') . '">',
			JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL'),
			'</label>',
			'<select name="batch[language_id]" id="batch-language-id">',
			'<option value="">' . JText::_('JLIB_HTML_BATCH_LANGUAGE_NOCHANGE') . '</option>',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}
	/**
	 * Displays a check box to select copying items.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 */
	public static function copy_items()
	{
		// Create the copy/move options.
		$lines = array('<label id="batch-copy-items-lbl" for="batch-copy-items"  class="hasTip"'
			. ' title="' . JText::_('COM_CAR_DRIVES_BATCH_COPY_ITEMS_LABEL') . '::' . JText::_('COM_CAR_DRIVES_BATCH_COPY_ITEMS_DESC') . '">',
			 JText::_('COM_CAR_DRIVES_BATCH_COPY_ITEMS_LABEL'), '</label>',
			'<input type="checkbox" name="batch[copy_items]" id="batch-copy-items" value="1"/>'
		);

		return implode("\n", $lines);
	}
}
