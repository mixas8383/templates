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
 * @version			$Id: view.html.php 408 2014-10-19 18:31:00Z BrianWade $
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
 * HTML Items View class for the [%%ArchitectComp_name%%] component
 *
 */
class [%%ArchitectComp%%]ViewItems extends JViewLegacy
{
	protected $item;
	protected $params;
	protected $print;
	protected $state;
	protected $user;
	protected $return_page;
	protected $form;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */	
	public function display($tpl = null)
	{
		
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$user_id	= $user->get('id');
		$dispatcher = JEventDispatcher::getInstance();

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->print	= $app->input->getBool('print');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Create a shortcut for $item.
		$item = $this->item;
			
		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
		
		$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
		$item->parent_slug	= $item->parent_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;
		
		// No link for ROOT category
		if ($item->parent_alias == 'root')
		{
			$item->parent_slug = null;
		}		
		
		// Merge items params. If this is single-items view, menu params override items params
		// Otherwise, items params override menu item params
		$this->params	= $this->state->get('params');
		$active	= $app->getMenu()->getActive();
		$temp	= clone ($this->params);


		// Check to see which parameters should take priority
		if ($active)
		{
			$current_link = $active->link;
			// If the current view is the active item and an items view for this items, then the menu item params take priority
			if (JString::strpos($current_link, 'view=items') AND (JString::strpos($current_link, '&id='.(string) $item->id)))
			{
				// $item->params are the items params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);
				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
				// Check for alternative layout of items
				else
					{
					if ($layout = $item->params->get('items_layout'))
					{
						$this->setLayout($layout);
					}
				}

				// $item->params are the article params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);				
			}
			else
			{
				// Current view is not a single items, so the items params take priority here
				// Merge the menu item params with the items params so that the items params take priority
				$temp->merge($item->params);
				$item->params = $temp;
				
				if ($this->getLayout() == 'blog')
				{
					$this->setLayout('article');
				}
			}
		}
		else
		{
			// Merge so that items params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			// Check for alternative layouts (since we are not in a single-items menu item)
			// Single-items menu item layout takes priority over alt layout for an items
			if ($layout = $item->params->get('items_layout'))
			{
				$this->setLayout($layout);
			}
		}
		
		$item->readmore_link = JRoute::_([%%ArchitectComp%%]HelperRoute::getItemsRoute($item->slug, 
										$item->catid, 
										$item->language,
										$this->getLayout(),									
										$this->params->get('keep_items_itemid')));


		if ($this->params->get('show_items_tags')  == '1')
		{
			$item->tags = new JHelperTags;
			$item->tags->getItemTags('[%%com_architectcomp%%].items', $item->id);	
			
			$item->tag_layout = new JLayoutFile('joomla.content.tags');
		}
				
		$offset = $this->state->get('list.offset');

		// Check the view access to the items (the model has already computed the values).
		if ($item->params->get('access-view') != true)
		{
			// If a no authority viewing is allowed show the item and rely on display code to show correct
			if (!$item->params->get('show_items_noauth'))
			{
				// If a guest user, they may be able to log in to view the full items
				
				if( $user->get('guest'))
				{
					// Redirect to login
					$uri = JUri::getInstance();
					$app->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri), JText::_('[%%COM_ARCHITECTCOMP%%]_ITEMSES_ERROR_LOGIN_TO_VIEW_ITEM'));
					
				}
				else
				{
					JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				}
				return;

			}
		}
	

		//
		// Process the [%%architectcomp%%] plugins.
		//
		JPluginHelper::importPlugin('[%%architectcomp%%]');
		$results = $dispatcher->trigger('onItemsPrepare', array ('[%%com_architectcomp%%].items', &$item, &$this->params, $offset));

		$item->event = new stdClass;
		$results = $dispatcher->trigger('onItemsAfterName', array('[%%com_architectcomp%%].items', &$item, &$this->params, $offset));
		$item->event->afterDisplayItemsName = JString::trim(implode("\n", $results));
		
		$results = $dispatcher->trigger('onItemsBeforeDisplay', array('[%%com_architectcomp%%].items', &$item, &$this->params, $offset));
		$item->event->beforeDisplayItems = JString::trim(implode("\n", $results));

		$results = $dispatcher->trigger('onItemsAfterDisplay', array('[%%com_architectcomp%%].items', &$item, &$this->params, $offset));
		$item->event->afterDisplayItems = JString::trim(implode("\n", $results));
		
		// Increment the hit counter of the items.
		$model = $this->getModel();
		$model->hit();

		$this->prepareDocument();

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$lang	= JFactory::getLanguage();		
		$pathway = $app->getPathway();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu  AND $menu->params->get('show_page_heading'))
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', $this->item->name);
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this items
		if ($menu AND ($menu->query['option'] != '[%%com_architectcomp%%]' OR $menu->query['view'] != 'items' OR $id != $this->item->id))
		{
			// If this is not a single items menu item, set the page title to the items name
			if ($this->item->name)
			{
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->item->name, 'link' => ''));
			if ( $this->params->get('show_items_category_breadcrumb', '0'))
			{			
				$options['countItems'] = false;
				$options['table'] = '#__[%%architectcomp%%]_itemses';					
				$category = JCategories::getInstance('[%%ArchitectComp%%]', $options)->get($this->item->catid);
				while ($category AND ($menu->query['option'] != '[%%com_architectcomp%%]' OR $menu->query['view'] == 'items' OR $id != $category->id) AND $category->id > 1)
				{
					$path[] = array('title' => $category->title, 'link' => [%%ArchitectComp%%]HelperRoute::getCategoryRoute($category->id, $this->params->get('keep_items_itemid')));
					$category = $category->getParent();
				}
			}
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = htmlspecialchars_decode($app->get('sitename'));
		}
		elseif ($app->get('sitename_pagetitles', 0))
		{
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->get('sitename')), $title);
		}
		if (empty($title))
		{
			$title = $this->item->name;
		}
		$this->document->setTitle($title);

		// Get Menu Item meta description, Keywords and robots instruction to insert in page header

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		else
		{
			if (!$this->item->metadesc AND $this->params->get('menu-meta_description'))
			{
				$this->document->setDescription($this->params->get('menu-meta_description'));
			}
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		else
		{
			if (!$this->item->metakey AND $this->params->get('menu-meta_keywords'))
			{
				$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
			}
		}
	
		// Get Robots instruction to insert in page header
		if ($this->item->robots)
		{
			$this->document->setMetadata('robots', $this->item->robots);
		}
		else
		{
			if (!$this->item->robots AND $this->params->get('robots'))
			{
				$this->document->setMetadata('robots', $this->params->get('robots'));
			}
		}

		if ($app->get('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}			

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			$this->item->name = $this->item->name . ' - ' . $this->item->page_title;
			$this->document->setTitle($this->item->page_title . ' - ' . JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
		}

		
		// Include Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');	
	}
}
