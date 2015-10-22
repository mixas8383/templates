<?php
/**
 * @version 		$Id:$
 * @name			Selectfile (Release 1.0.0)
 * @author			 ()
 * @package			com_selectfile
 * @subpackage		com_selectfile.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: view.feed.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
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
 * Frontpage View class
 *
 */
class SelectfileViewSizes extends JViewLegacy
{

	/**
	 * Create an item feed.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * 
	 */
	public function display($tpl = null)
	{
		// parameters
		$app		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$doc	= JFactory::getDocument();
		$state		= $this->get('State');
		$params		= $state->params;
		$feed_email	= (@$app->get('feed_email')) ? $app->get('feed_email') : 'creator';
		$site_email	= $app->get('mailfrom');
		$doc->link = JRoute::_('index.php?option=com_selectfile&view=sizes');

		// Get some data from the model
		$app->input->set('limit', $app->get('feed_limit'));
		$rows		= $this->get('Items');
		foreach ($rows as $row)
		{
			// strip html from feed item name
			$title = $this->escape($row->name);
						
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the size slug
			$row->slug = $row->id;

			// url link to size
			$link = JRoute::_(SelectfileHelperRoute::getSizeRoute($row->slug, 
																						'default',								
																						$params->get('keep_size_itemid')), false);
			
			$description = '';
			$created_by			= $row->created_by_name ? $row->created_by_name : $row->created_by;

			@$date = ($row->created ? date('r', strtotime($row->created)) : '');

			// load individual item creator class
			$item = new JFeedItem();
			$item->title		= $title;
			$item->link			= $link;
			$item->date			= $date;
			$item->creator		= $created_by;
			if ($feed_email == 'site')
			{
				$item->creatorEmail = $site_email;
			}
			else
			{
				$item->creatorEmail = $row->created_by_email;
			}
			// loads item info into rss array
			$doc->addItem($item);
		}
	}
}
?>
