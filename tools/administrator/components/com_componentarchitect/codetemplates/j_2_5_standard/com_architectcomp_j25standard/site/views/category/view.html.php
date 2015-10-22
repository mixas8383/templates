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
 * @version			$Id: view.html.php 418 2014-10-22 14:42:36Z BrianWade $
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
 * HTML View class for the [%%ArchitectComp_name%%] component
 *
 */
class [%%ArchitectComp%%]ViewCategory extends JView
{
	protected $state;
	protected $category;
	protected $children;

	protected $items;
	protected $pagination;



	function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$state		= $this->get('State');
		$params		= $state->params;
		$category	= $this->get('Category');
		$children	= $this->get('Children');
		$parent		= $this->get('Parent');
		if ($params->get('items_to_display') AND $params->get('items_to_display') != 'None')
		{			
			$items		= $this->get('Items');
			$pagination = $this->get('Pagination');
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($category == false)
		{
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		if ($parent == false)
		{
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		// Setup the category parameters.
		$cparams = $category->getParams();
		$category->params = clone($params);
		$category->params->merge($cparams);

		// Check whether category access level allows access.
		$user	= JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		if (!in_array($category->access, $groups))
		{
			return JError::raiseError(403, JText::_("JERROR_ALERTNOAUTHOR"));
		}

		// PREPARE THE DATA
		// Compute the item slugs.
		if ($params->get('items_to_display') AND $params->get('items_to_display') != 'None')
		{	
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				$item = &$items[$i];
				$item->slug = $item->id;

				if (isset($item->alias))
				{
					$item->slug .= ':' . $item->alias;
				}
				// No link for ROOT category
				if ($item->parent_alias == 'root') {
					$item->parent_slug = null;
				}

				$item->catslug = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
			}
		}
		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$active	= $app->getMenu()->getActive();
		if ((!$active) OR ((JString::strpos($active->link, 'view=category') === false) OR (JString::strpos($active->link, '&id=' . (string) $category->id) === false)))
		{
			// Get the layout from the merged category params
			if ($layout = $category->params->get('category_layout'))
			{
				$this->setLayout($layout);
			}
		}
		// At this point, we are in a menu item, so we don't override the layout
		elseif (isset($active->query['layout']))
			{
				// We need to set the layout from the query in case this is an alternative menu item (with an alternative layout)
				$this->setLayout($active->query['layout']);
			}
		$children = array($category->id => $children);

		$this->max_level_category = $params->get('show_category_max_level', -1);
		$this->assignRef('state', $state);
		$this->assignRef('category', $category);
		$this->assignRef('children', $children);
		$this->assignRef('params', $params);
		$this->assignRef('parent', $parent);
		$this->assignRef('user', $user);
		if ($params->get('items_to_display') AND $params->get('items_to_display') != 'None')
		{			
			$object_lower_case = JString::strtolower(str_replace(' ','',$params->get('items_to_display')));
			$this->assignRef($object_lower_case, $items);		
			$this->assignRef('pagination', $pagination);
		}
		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$lang		= JFactory::getLanguage();		
		$pathway	= $app->getPathway();
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('[%%COM_ARCHITECTCOMP%%]_CATEGORY_HEADER'));
		}

		$id = (int) @$menu->query['id'];

		if ($menu AND ($menu->query['option'] != '[%%com_architectcomp%%]' OR
					[%%FOREACH COMPONENT_OBJECT%%]
						[%%IF GENERATE_CATEGORIES%%]
					 $menu->query['view'] == '[%%compobject%%]' OR 
						[%%ENDIF GENERATE_CATEGORIES%%]
					[%%ENDFOR COMPONENT_OBJECT%%]
					 $id != $this->category->id))
		{
			$path = array(array('title' => $this->category->title, 'link' => ''));
			$category = $this->category->getParent();

			while (($menu->query['option'] != '[%%com_architectcomp%%]' OR
					[%%FOREACH COMPONENT_OBJECT%%]
						[%%IF GENERATE_CATEGORIES%%]
					$menu->query['view'] == '[%%compobject%%]' OR
						[%%ENDIF GENERATE_CATEGORIES%%] 
					[%%ENDFOR COMPONENT_OBJECT%%]
					$id != $category->id) AND $category->id > 1)
			{

				[%%FOREACH COMPONENT_OBJECT%%]
					[%%IF GENERATE_CATEGORIES%%]
				$path[] = array('title' => $category->title, 'link' => [%%ArchitectComp%%]HelperRoute::getCategoryRoute($category->id, $this->params->get('keep_[%%compobject%%]_itemid')));
					[%%ENDIF GENERATE_CATEGORIES%%]
				[%%ENDFOR COMPONENT_OBJECT%%]
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0))
			{
				$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
			}

		$this->document->setTitle($title);

		// Get Menu Item meta description, Keywords and robots instruction to insert in page header

		if ($this->category->metadesc)
		{
			$this->document->setDescription($this->category->metadesc);
		}
		elseif (!$this->category->metadesc AND $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->category->metakey)
		{
			$this->document->setMetadata('keywords', $this->category->metakey);
		}
		elseif (!$this->category->metakey AND $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		if ($app->getCfg('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->category->getMetadata()->get('author'));
		}		
		
		// Add css files for the [%%architectcomp%%] component and categories if they exist
		$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/[%%architectcomp%%].css");
		$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/".JString::strtolower(str_replace(' ','',$this->params->get('items_to_display'))).".css");
		$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/categories.css");
	
		if ($lang->isRTL())
		{
			$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/[%%architectcomp%%]-rtl.css");
			$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/".JString::strtolower(str_replace(' ','',$this->params->get('items_to_display')))."-rtl.css");
			$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/categories-rtl.css");
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

		// Add Javascript to display fields
		JHtml::_('behavior.tooltip');
		JHtml::core();				
	}
}