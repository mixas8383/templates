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
 * HTML [%%CompObject_name%%] View class for the [%%ArchitectComp_name%%] component
 *
 */
class [%%ArchitectComp%%]View[%%CompObject%%] extends JViewLegacy
{
	protected $item;
	protected $params;
	protected $print;
	protected $state;
	protected $user;
	protected $return_page;
	protected $form;
	
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
		[%%IF INCLUDE_NAME%%]
			[%%IF INCLUDE_ALIAS%%]
		$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			[%%ELSE INCLUDE_ALIAS%%]
		$item->slug = $item->id;
			[%%ENDIF INCLUDE_ALIAS%%]			
		[%%ENDIF INCLUDE_NAME%%]
		
		[%%IF GENERATE_CATEGORIES%%]
		$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
		$item->parent_slug	= $item->parent_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;
		
		// No link for ROOT category
		if ($item->parent_alias == 'root')
		{
			$item->parent_slug = null;
		}		
		[%%ENDIF GENERATE_CATEGORIES%%]
		
		// Merge [%%compobject%%] params. If this is single-[%%compobject%%] view, menu params override [%%compobject%%] params
		// Otherwise, [%%compobject%%] params override menu item params
		$this->params	= $this->state->get('params');
		$active	= $app->getMenu()->getActive();
		$temp	= clone ($this->params);


		// Check to see which parameters should take priority
		if ($active)
		{
			$current_link = $active->link;
			// If the current view is the active item and an [%%compobject%%] view for this [%%compobject%%], then the menu item params take priority
			if (JString::strpos($current_link, 'view=[%%compobject%%]') AND (JString::strpos($current_link, '&id='.(string) $item->id)))
			{
				// $item->params are the [%%compobject%%] params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);
				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
				// Check for alternative layout of [%%compobject%%]
				else
					{
					if ($layout = $item->params->get('[%%compobject%%]_layout'))
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
				// Current view is not a single [%%compobject%%], so the [%%compobject%%] params take priority here
				// Merge the menu item params with the [%%compobject%%] params so that the [%%compobject%%] params take priority
				$temp->merge($item->params);
				$item->params = $temp;
				
				[%%IF GENERATE_SITE_LAYOUT_BLOG%%]
					[%%IF GENERATE_SITE_LAYOUT_ARTICLE%%]
				if ($this->getLayout() == 'blog')
				{
					$this->setLayout('article');
				}
					[%%ENDIF GENERATE_SITE_LAYOUT_ARTICLE%%]
				[%%ENDIF GENERATE_SITE_LAYOUT_BLOG%%]
			}
		}
		else
		{
			// Merge so that [%%compobject%%] params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			// Check for alternative layouts (since we are not in a single-[%%compobject%%] menu item)
			// Single-[%%compobject%%] menu item layout takes priority over alt layout for an [%%compobject%%]
			if ($layout = $item->params->get('[%%compobject%%]_layout'))
			{
				$this->setLayout($layout);
			}
		}

		// TODO: Change based on shownoauth
		
		[%%IF GENERATE_CATEGORIES%%]		 
			[%%IF INCLUDE_LANGUAGE%%]
		$item->readmore_link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
										$item->catid, 
										$item->language,
										$this->getLayout(),									
										$this->params->get('keep_[%%compobject%%]_itemid')));
			[%%ELSE INCLUDE_LANGUAGE%%]
		$item->readmore_link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
										$item->catid,
										$this->getLayout(),								
										$this->params->get('keep_[%%compobject%%]_itemid')));
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ELSE GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_LANGUAGE%%]
		$item->readmore_link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
										$item->language,
										$this->getLayout(),									
										$this->params->get('keep_[%%compobject%%]_itemid')));
			[%%ELSE INCLUDE_LANGUAGE%%]
		$item->readmore_link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug,
										$this->getLayout(), 
										$this->params->get('keep_[%%compobject%%]_itemid')));
			[%%ENDIF INCLUDE_LANGUAGE%%]	
		[%%ENDIF GENERATE_CATEGORIES%%]		


		[%%IF INCLUDE_TAGS%%]
		if ($this->params->get('show_[%%compobject%%]_tags')  == '1')
		{
			$item->tags = new JHelperTags;
			$item->tags->getItemTags('[%%com_architectcomp%%].[%%compobject%%]', $item->id);	
			
			$item->tag_layout = new JLayoutFile('joomla.content.tags');
		}
		[%%ENDIF INCLUDE_TAGS%%]
				
		$offset = $this->state->get('list.offset');

		[%%IF INCLUDE_ACCESS%%]
		// Check the view access to the [%%compobject%%] (the model has already computed the values).
		if ($item->params->get('access-view') != true)
		{
			// If a no authority viewing is allowed show the item and rely on display code to show correct
			if (!$item->params->get('show_[%%compobject%%]_noauth'))
			{
				// If a guest user, they may be able to log in to view the full [%%compobject%%]
				
				if( $user->get('guest'))
				{
					// Redirect to login
					$uri = JUri::getInstance();
					$app->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri), JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_ERROR_LOGIN_TO_VIEW_ITEM'));
					
				}
				else
				{
					JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				}
				return;

			}
		}
		[%%ENDIF INCLUDE_ACCESS%%]
	

		//
		// Process the [%%architectcomp%%] plugins.
		//
		JPluginHelper::importPlugin('[%%architectcomp%%]');
		$results = $dispatcher->trigger('on[%%CompObject%%]Prepare', array ('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$this->params, $offset));

		$item->event = new stdClass;
		[%%IF INCLUDE_NAME%%]
		$results = $dispatcher->trigger('on[%%CompObject%%]AfterName', array('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$this->params, $offset));
		$item->event->afterDisplay[%%CompObject%%]Name = JString::trim(implode("\n", $results));
		[%%ENDIF INCLUDE_NAME%%]
		
		$results = $dispatcher->trigger('on[%%CompObject%%]BeforeDisplay', array('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$this->params, $offset));
		$item->event->beforeDisplay[%%CompObject%%] = JString::trim(implode("\n", $results));

		$results = $dispatcher->trigger('on[%%CompObject%%]AfterDisplay', array('[%%com_architectcomp%%].[%%compobject%%]', &$item, &$this->params, $offset));
		$item->event->afterDisplay[%%CompObject%%] = JString::trim(implode("\n", $results));
		
		[%%IF INCLUDE_HITS%%]
		// Increment the hit counter of the [%%compobject%%].
		$model = $this->getModel();
		$model->hit();
		[%%ENDIF INCLUDE_HITS%%]

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
			[%%IF INCLUDE_NAME%%]
			$this->params->def('page_heading', $this->item->name);
			[%%ELSE INCLUDE_NAME%%]
			$this->params->def('page_heading', JText::sprintf('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECT%%]_ID_TITLE', $this->item->id);
			[%%ENDIF INCLUDE_NAME%%]
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this [%%compobject%%]
		if ($menu AND ($menu->query['option'] != '[%%com_architectcomp%%]' OR $menu->query['view'] != '[%%compobject%%]' OR $id != $this->item->id))
		{
			[%%IF INCLUDE_NAME%%]
			// If this is not a single [%%compobject%%] menu item, set the page title to the [%%compobject%%] name
			if ($this->item->name)
			{
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->item->name, 'link' => ''));
			[%%ELSE INCLUDE_NAME%%]
			$path = array(array('title' => $this->item->id, 'link' => ''));
			[%%ENDIF INCLUDE_NAME%%]			
			[%%IF GENERATE_CATEGORIES%%]	
			if ( $this->params->get('show_[%%compobject%%]_category_breadcrumb', '0'))
			{			
				$options['countItems'] = false;
				$options['table'] = '#__[%%architectcomp%%]_[%%compobjectplural%%]';					
				$category = JCategories::getInstance('[%%ArchitectComp%%]', $options)->get($this->item->catid);
				while ($category AND ($menu->query['option'] != '[%%com_architectcomp%%]' OR $menu->query['view'] == '[%%compobject%%]' OR $id != $category->id) AND $category->id > 1)
				{
					$path[] = array('title' => $category->title, 'link' => [%%ArchitectComp%%]HelperRoute::getCategoryRoute($category->id, $this->params->get('keep_[%%compobject%%]_itemid')));
					$category = $category->getParent();
				}
			}
			[%%ENDIF GENERATE_CATEGORIES%%]			
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0))
		{
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		[%%IF INCLUDE_NAME%%]
		if (empty($title))
		{
			$title = $this->item->name;
		}
		[%%ENDIF INCLUDE_NAME%%]
		$this->document->setTitle($title);

		[%%IF INCLUDE_METADATA%%]
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

		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}			
		[%%ELSE INCLUDE_METADATA%%]
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
		[%%ENDIF INCLUDE_METADATA%%]

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			[%%IF INCLUDE_NAME%%]
			$this->item->name = $this->item->name . ' - ' . $this->item->page_title;
			[%%ENDIF INCLUDE_NAME%%]
			$this->document->setTitle($this->item->page_title . ' - ' . JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
		}

		
		// Include Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');	
	}
}
