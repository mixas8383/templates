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
 * @version			$Id: view.html.php 417 2014-10-22 14:42:10Z BrianWade $
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
 * Frontpage View class
 *
 */
class [%%ArchitectComp%%]View[%%CompObjectPlural%%] extends JViewLegacy
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;

	protected $lead_items = array();
	protected $intro_items = array();
	protected $link_items = array();
	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	public function display($tpl = null)
	{
		
		$app = JFactory::getApplication();
		$state		= $this->get('State');
		$params		= $state->params;
		$items 		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		[%%IF GENERATE_SITE_LAYOUT_BLOG%%]
		if ($app->input->getString('layout', 'default') != 'blog')
		{			
			[%%FOREACH FILTER_FIELD%%]
				[%%IF FIELD_FILTER_LINK%%]
			$this->[%%FIELD_FOREIGN_OBJECT_PLURAL%%]	= $this->get('[%%FIELD_FOREIGN_OBJECT_PLURAL_UCFIRST%%]');
				[%%ELSE FIELD_FILTER_LINK%%]
			$this->[%%FIELD_CODE_NAME%%]_values	= $this->get('[%%FIELD_CODE_NAME_UCFIRST%%]values');
				[%%ENDIF FIELD_FILTER_LINK%%]		
			[%%ENDFOR FILTER_FIELD%%]
		}	
		[%%ELSE GENERATE_SITE_LAYOUT_BLOG%%]
			[%%FOREACH FILTER_FIELD%%]
				[%%IF FIELD_FILTER_LINK%%]
		$this->[%%FIELD_FOREIGN_OBJECT_PLURAL%%]	= $this->get('[%%FIELD_FOREIGN_OBJECT_PLURAL_UCFIRST%%]');
				[%%ELSE FIELD_FILTER_LINK%%]
		$this->[%%FIELD_CODE_NAME%%]_values	= $this->get('[%%FIELD_CODE_NAME_UCFIRST%%]values');
				[%%ENDIF FIELD_FILTER_LINK%%]		
			[%%ENDFOR FILTER_FIELD%%]
		[%%ENDIF GENERATE_SITE_LAYOUT_BLOG%%]	
			
		$dispatcher	= JEventDispatcher::getInstance();		

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// PREPARE THE DATA

		// Compute the [%%compobject%%] slugs and set the trigger events.
		foreach ($items as $i => &$item)
		{
			// Add router helpers.
			[%%IF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
			$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
				[%%ELSE INCLUDE_ALIAS%%]
			$item->slug = $item->id;
				[%%ENDIF INCLUDE_ALIAS%%]			
			[%%ENDIF INCLUDE_NAME%%]
			
			[%%IF GENERATE_CATEGORIES%%]
			$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
			$item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;
			[%%ENDIF GENERATE_CATEGORIES%%]		
			//
			// Process the [%%architectcomp%%] plugins.
			//
			
			JPluginHelper::importPlugin('[%%architectcomp%%]');
			$dispatcher->trigger('on[%%CompObject%%]Prepare', array ('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$item->params,$i));

			$item->event = new stdClass;

			[%%IF INCLUDE_NAME%%]
			$results = $dispatcher->trigger('on[%%CompObject%%]AfterName', array('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$item->params, $i));
			$item->event->afterDisplay[%%CompObject%%]Name = JString::trim(implode("\n", $results));
			[%%ENDIF INCLUDE_NAME%%]

			$results = $dispatcher->trigger('on[%%CompObject%%]BeforeDisplay', array('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$item->params, $i));
			$item->event->beforeDisplay[%%CompObject%%] = JString::trim(implode("\n", $results));

			$results = $dispatcher->trigger('on[%%CompObject%%]AfterDisplay', array('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$item->params,$i));
			$item->event->afterDisplay[%%CompObject%%] = JString::trim(implode("\n", $results));

			$dispatcher = JEventDispatcher::getInstance();

		}
		[%%IF GENERATE_SITE_LAYOUT_BLOG%%]
		if ($app->input->getString('layout', 'default') == 'blog')
		{			
			// Get the metrics for the structural page layout.
			$num_leading = (int) $params->def('[%%compobject%%]_num_leading', 1);
			$num_intro   = (int) $params->def('[%%compobject%%]_num_intro', 4);
			$num_links   = (int) $params->def('[%%compobject%%]_num_links', 4);
		
			// Preprocess the breakdown of leading, intro and linked [%%compobject_plural_name%%].
			// This makes it much easier for the designer to just interogate the arrays.
			$max = count($items);

			// The first group is the leading [%%compobject_plural_name%%].
			$limit = $num_leading;
			for ($i = 0; $i < $limit AND $i < $max; $i++)
			{
				$this->lead_items[$i] = &$items[$i];
			}

			// The second group is the intro [%%compobject_plural_name%%].
			$limit = $num_leading + $num_intro;
			// Order [%%compobject_plural_name%%] across, then down (or single column mode)
			for ($i = $num_leading; $i < $limit AND $i < $max; $i++)
			{
				$this->intro_items[$i] = &$items[$i];
			}

			$this->columns = max(1, $params->def('[%%compobject%%]_num_columns', 1));
			$order = $params->def('[%%compobject%%]_multi_column_order', 1);

			if ($order == 0 AND $this->columns > 1)
			{
				// call order down helper
				$this->intro_items = [%%ArchitectComp%%]HelperQuery::orderDownColumns($this->intro_items, $this->columns);
			}

			// The remainder are the links.
			$limit = $num_leading + $num_intro + $num_links;			
			for ($i = $num_leading + $num_intro; $i < $limit AND $i < $max; $i++)
			{
				$this->link_items[$i] = &$items[$i];
			}
		}
		[%%ENDIF GENERATE_SITE_LAYOUT_BLOG%%]
		
		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		
		$this->params     = &$params;
		$this->state      = &$state;
		$this->items      = &$items;
		$this->pagination = &$pagination;				

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$lang		= JFactory::getLanguage();		
		$title 		= null;
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu  AND $menu->params->get('show_page_heading'))
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title))
		{
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		else
		{
			if ($app->getCfg('sitename_pagetitles', 0))
			{
				$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
			}
		}
		$this->document->setTitle($title);
		// Get Menu Item meta description, Keywords and robots instruction to insert in page header
		
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}	
		
		// Add feed links
		if ($this->params->get('show_feed_link', 1))
		{
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}

		// Include Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');	
	}
}
