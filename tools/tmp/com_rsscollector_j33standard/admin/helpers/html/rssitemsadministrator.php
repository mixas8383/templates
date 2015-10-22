<?php
/**
 * @version 		$Id:$
 * @name			Rss_collector (Release 1.0.0)
 * @author			 ()
 * @package			com_rsscollector
 * @subpackage		com_rsscollector.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectadministrator.php 408 2014-10-19 18:31:00Z BrianWade $
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

JLoader::register('ContentHelper', JPATH_ADMINISTRATOR . '/components/com_rsscollector/helpers/rsscollector.php');
/**
 * Rss_itemses component helper.
 *
 */
abstract class JHtmlRssItemsAdministrator
{
	/**
	 * Render the list of associated items
	 * 
	 * @param	integer $rssitems_id	The rss_items item id
	 * 
	 * @return  string  The language HTML
     */
	public static function association($rssitems_id)
	{
		// Get the associations
		// Defaults
		$html = '';

		// Get the associations
		if ($associations = JLanguageAssociations::getAssociations('com_rsscollector', '#__rsscollector_rssitemses', 'com_rsscollector.rssitems.item',  $rssitems_id, 'id', 'alias', null))
		{		
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated menu items
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('a.*');
			$query->from($db->quoteName('#__rsscollector_rssitemses').' as a');
			$query->where($db->quoteName('a.id').' IN ('.implode(',', array_values($associations)).')');
			$query->join('LEFT', $db->quoteName('#__languages').' as l ON '.$db->quoteName('a.language').' = '.$db->quoteName('l.lang_code'));
			$query->select($db->quoteName('l.image'));
			$query->select($db->quoteName('l.title').' as language_title');
			$query->select($db->quoteName('l.sef').' as lang_sef');
			
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (runtimeException $e)
			{
				throw new Exception($e->getMessage(), 500);
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text = strtoupper($item->lang_sef);
					$url = JRoute::_('index.php?option=com_rsscollector&task=rssitems.edit&id=' . (int) $item->id);
					$tooltip_parts = array(
						JHtml::_('image', 'mod_languages/' . $item->image . '.gif',
							$item->language_title,
							array('title' => $item->language_title),
							true
						),
						$item->name
					);
					$item->link = JHtml::_('tooltip', implode(' ', $tooltip_parts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->lang_sef);
				}
			}

			$html = JLayoutHelper::render('joomla.content.associations', $items);
		}
		return $html;
	}
	/**
	 * Show the feature/unfeature links
	 *
	 * @param   int      $value      The state value
	 * @param   int      $i          Row number
	 * @param   boolean  $can_change  Is user allowed to change?
	 *
	 * @return  string       HTML code
	 */
	public static function featured($value = 0, $i, $can_change = true)
	{
		JHtml::_('bootstrap.tooltip');
			
		// Array of image, task, title, action
		$states	= array(
			0	=> array('unfeatured', 'rssitemses.featured', 'COM_RSSCOLLECTOR_RSSITEMSES_UNFEATURED', 'COM_RSSCOLLECTOR_RSSITEMSES_TOGGLE_TO_FEATURE'),
			1	=> array('featured', 'rssitemses.unfeatured', 'JFEATURED', 'COM_RSSCOLLECTOR_RSSITEMSES_TOGGLE_TO_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		if ($can_change)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="'.JText::_($state[2]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}
		return $html;
	}
}
