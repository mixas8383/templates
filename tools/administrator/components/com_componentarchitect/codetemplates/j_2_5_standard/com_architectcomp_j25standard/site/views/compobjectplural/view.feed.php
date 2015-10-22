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
 * @version			$Id: view.feed.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
 * @CAtemplate		joomla_2_5_standard (Release 1.0.4)
 * @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
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

jimport('joomla.application.component.view');

/**
 * Frontpage View class
 *
 */
class [%%ArchitectComp%%]View[%%CompObjectPlural%%] extends JView
{
	function display($tpl = null)
	{
		// parameters
		$app		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$doc	= JFactory::getDocument();
		$state		= $this->get('State');
		$params		= $state->params;
		$feed_email	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'creator';
		$site_email	= $app->getCfg('mailfrom');
		$doc->link = JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobjectplural%%]');

		// Get some data from the model
		JRequest::setVar('limit', $app->getCfg('feed_limit'));
		[%%IF GENERATE_CATEGORIES%%]
		$options['countItems'] = false;
		$options['table'] = '#__[%%architectcomp%%]_[%%compobjectplural%%]';				
		$categories = JCategories::getInstance('[%%ArchitectComp%%]', $options);
		[%%ENDIF GENERATE_CATEGORIES%%]		
		$rows		= $this->get('Items');
		foreach ($rows as $row)
		{
			// strip html from feed item name
			[%%IF INCLUDE_NAME%%]
			$title = $this->escape($row->name);
			[%%ELSE INCLUDE_NAME%%]
			$title = $this->escape($row->id);
			[%%ENDIF INCLUDE_NAME%%]			
						
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the [%%compobject%%] slug
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
			[%%IF GENERATE_CATEGORIES%%]
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
			[%%ELSE GENERATE_CATEGORIES%%]
				[%%IF INCLUDE_LANGUAGE%%]
			$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->slug, 
																						$row->language,									
																						'default',								
																						$params->get('keep_[%%compobject%%]_itemid')), false);
				[%%ELSE INCLUDE_LANGUAGE%%]
			$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->slug, 
																						'default',								
																						$params->get('keep_[%%compobject%%]_itemid')), false);
				[%%ENDIF INCLUDE_LANGUAGE%%]	
			[%%ENDIF GENERATE_CATEGORIES%%]	
			t
			$description = '';
			[%%IF INCLUDE_DESCRIPTION%%]
				[%%IF INCLUDE_INTRO%%]
			$description = $row->description;
				[%%ELSE INCLUDE_INTRO%%]
			$description = $row->introdescription;
				[%%ENDIF INCLUDE_INTRO%%]
			[%%ENDIF INCLUDE_DESCRIPTION%%]
			[%%IF INCLUDE_INTRO%%]			
			// TODO: Only pull fulltext if necessary (actually, just get the necessary fields).
			if (!$params->get('feed_summary', 0))
			{	
				$description = $row->intro;
			}
			[%%ENDIF INCLUDE_INTRO%%]			
			[%%IF INCLUDE_CREATED%%]
			$creator			= $row->created_by_name ? $row->created_by_name : $row->created_by;
			[%%ENDIF INCLUDE_CREATED%%]

			[%%IF INCLUDE_PUBLISHED_DATES%%]
			@$date = ($row->publish_up ? date('r', strtotime($row->publish_up)) : '');
			[%%ELSE INCLUDE_PUBLISHED_DATES%%]
			@$date = ($row->created ? date('r', strtotime($row->created)) : '');
			[%%ENDIF INCLUDE_PUBLISHED_DATES%%]

			// load individual item creator class
			$item = new JFeedItem();
			$item->title		= $title;
			$item->link			= $link;
			[%%IF INCLUDE_DESCRIPTION%%]
			$item->description	= $description;
			[%%ENDIF INCLUDE_DESCRIPTION%%]
			[%%IF INCLUDE_CREATED%%]
			$item->date			= $date;
			[%%ENDIF INCLUDE_CREATED%%]
			[%%IF GENERATE_CATEGORIES%%]			
			$item_category		= $categories->get($row->catid);
			$item->category		= array();
			if ($item->featured == 1)
			{
				$item->category[]	= JText::_('JFEATURED'); // All featured [%%compobjectplural%%] are categorized as "Featured"
			}
			for ($item_category = $categories->get($row->catid); $item_category !== null; $item_category = $item_category->getParent())
			{
				if ($item_category->id > 1)
				{ // Only add non-root categories
					$item->category[] = $item_category->title;
				}
			}
			[%%ENDIF GENERATE_CATEGORIES%%]			
			[%%IF INCLUDE_CREATED%%]
			$item->creator		= $creator;
			if ($feed_email == 'site')
			{
				$item->creatorEmail = $site_email;
			}
			else
			{
				$item->creatorEmail = $row->creator_email;
			}
			[%%ENDIF INCLUDE_CREATED%%]
			// loads item info into rss array
			$doc->addItem($item);
		}
	}
}
?>
