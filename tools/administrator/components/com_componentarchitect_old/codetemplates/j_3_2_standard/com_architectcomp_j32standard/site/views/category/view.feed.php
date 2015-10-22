<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].site
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @version			$Id: view.feed.php 417 2014-10-22 14:42:10Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
 * @CAtemplate		joomla_3_2_standard (Release 1.0.4)
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
 * HTML View class for the [%%ArchitectComp_name%%] component
 *
 */
class [%%ArchitectComp%%]ViewCategory extends JViewLegacy
{
	public function display()
	{
		$app = JFactory::getApplication();

		$doc	= JFactory::getDocument();
		$params = $app->getParams();
		$feed_email	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'creator';

		// Get some data from the model
		$app->input->set('limit', $app->getCfg('feed_limit'));
		$category	= $this->get('Category');
		$rows		= $this->get('Items');

		[%%FOREACH COMPONENT_OBJECT%%]
			[%%IF GENERATE_CATEGORIES%%]
		$doc->link = JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($category->id, $params->get('keep_[%%compobject%%]_itemid')));

		foreach ($rows as $row)
		{
			// strip html from feed item title
			[%%IF INCLUDE_NAME%%]
			$title = $this->escape($row->name);
			[%%ELSE INCLUDE_NAME%%]
			$title = $this->escape($row->id);
			[%%ENDIF INCLUDE_NAME%%]
			
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the slug
			$row->slug = $row->id;
			[%%IF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
			if ($row->alias)
			{ 
				$row->slug .= ':'. $row->alias;
			}
				[%%ENDIF INCLUDE_ALIAS%%]
			[%%ENDIF INCLUDE_NAME%%]

			// url link to [%%compobject%%]
			// & used instead of &amp; as this is converted by feed creator
			[%%IF INCLUDE_LANGUAGE%%]
			$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->slug, 
																						$row->catid, 
																						$row->language,
																						'default',									
																						$params->get('keep_[%%compobject%%]_itemid')), false);
			[%%ELSE INCLUDE_LANGUAGE%%]
			$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->slug, 
																						$row->catid,
																						'default',								
																						$params->get('keep_[%%compobject%%]_itemid')), false);
			[%%ENDIF INCLUDE_LANGUAGE%%]
			
			[%%IF INCLUDE_DESCRIPTION%%]
			$description = $row->description;
			[%%ENDIF INCLUDE_DESCRIPTION%%]
			[%%IF INCLUDE_INTRO%%]
			// TODO: Only pull fulltext if necessary (actually, just get the necessary fields).
			$description	= ($params->get('feed_summary', 0) ? $description : $row->intro);
			[%%ENDIF INCLUDE_INTRO%%]
			[%%IF INCLUDE_CREATED%%]
			$creator		= $row->created_by_alias ? $row->created_by_alias : $row->creator;
			@$date			= ($row->created ? date('r', strtotime($row->created)) : '');
			[%%ENDIF INCLUDE_CREATED%%]

			// load individual item creator class
			$item = new JFeedItem;
			$item->title		= $title;
			$item->link			= $link;
			[%%IF INCLUDE_DESCRIPTION%%]
			$item->description	= $description;
			[%%ENDIF INCLUDE_DESCRIPTION%%]
			$item->date			= $date;
			$item->category		= $row->category;
			[%%IF INCLUDE_CREATED%%]
			$item->creator		= $creator;
			[%%ENDIF INCLUDE_CREATED%%]
			if ($feed_email == 'site')
			{
				$item->creatorEmail = $site_email;
			}
			else
			{
				$item->creatorEmail = $row->creator_email;
			}

			// loads item info into rss array
			$doc->addItem($item);
		}
			[%%ENDIF GENERATE_CATEGORIES%%]
		[%%ENDFOR COMPONENT_OBJECT%%]		
	}
}
