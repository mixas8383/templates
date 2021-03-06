<?php
/**
 * @version 		$Id:$
 * @name			App (Release 1.0.0)
 * @author			 ()
 * @package			com_app
 * @subpackage		com_app.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: view.html.php 418 2014-10-22 14:42:36Z BrianWade $
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
 * HTML Item View class for the App component
 *
 */
class AppViewItem extends JView
{
	protected $item;
	protected $params;
	protected $print;
	protected $state;
	protected $user;
	protected $return_page;
	protected $form;
	
	function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$user_id		= $user->get('id');
		$dispatcher	= JDispatcher::getInstance();

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->print	= JRequest::getBool('print');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Create a shortcut for $item.
		$item = &$this->item;

		// Add router helpers.
		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
		
		$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
		$item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;
		
		// Merge item params. If this is single-item view, menu params override item params
		// Otherwise, item params override menu item params
		$this->params	= $this->state->get('params');
		$active	= $app->getMenu()->getActive();
		$temp	= clone ($this->params);


		// Check to see which parameters should take priority
		if ($active)
		{
			$current_link = $active->link;
			// If the current view is the active item and an item view for this item, then the menu item params take priority
			if (JString::strpos($current_link, 'view=item') AND (JString::strpos($current_link, '&id='.(string) $item->id)))
			{
				// $item->params are the item params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);
				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
			}
			else
			{
				// Current view is not a single item, so the item params take priority here
				// Merge the menu item params with the item params so that the item params take priority
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
			// Merge so that item params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			// Check for alternative layouts (since we are not in a single-item menu item)
			// Single-item menu item layout takes priority over alt layout for an item
			if ($layout = $item->params->get('item_layout'))
			{
				$this->setLayout($layout);
			}
		}

		// TODO: Change based on shownoauth
		
		$item->readmore_link = JRoute::_(AppHelperRoute::getItemRoute($item->slug, 
										$item->catid, 
										$item->language,
										$this->getLayout(),									
										$this->params->get('keep_item_itemid')));
		
		$offset = $this->state->get('list.offset');

		// Check the view access to the item (the model has already computed the values).
		if ($item->params->get('access-view') != true)
		{
			// If a no authority viewing is allowed show the item and rely on display code to show correct
			if (!$this->params->get('show_item_noauth'))
			{
				// If a guest user, they may be able to log in to view the full item
				
				if( $user->get('guest'))
				{
					// Redirect to login
					$uri = JFactory::getURI();
					$app->redirect('index.php?option=com_users&view=login&return=' . base64_encode(urlencode($uri)), JText::_('COM_APP_ITEMS_ERROR_LOGIN_TO_VIEW_ITEM'));
					
				}
				else
				{
					JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				}
				return;

			}
		}
		

		//
		// Process the app plugins.
		//
		JPluginHelper::importPlugin('app');
		$results = $dispatcher->trigger('onItemPrepare', array ('com_app.item', &$item, &$this->params, $offset));

		$item->event = new stdClass();
		$results = $dispatcher->trigger('onItemAfterName', array('com_app.item', &$item, &$this->params, $offset));
		$item->event->afterDisplayItemName = JString::trim(implode("\n", $results));
		
		$results = $dispatcher->trigger('onItemBeforeDisplay', array('com_app.item', &$item, &$this->params, $offset));
		$item->event->beforeDisplayItem = JString::trim(implode("\n", $results));

		$results = $dispatcher->trigger('onItemAfterDisplay', array('com_app.item', &$item, &$this->params, $offset));
		$item->event->afterDisplayItem = JString::trim(implode("\n", $results));
		
		// Increment the hit counter of the item.

		$model = $this->getModel();
		$model->hit();

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
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

		// if the menu item does not concern this item
		if ($menu AND ($menu->query['option'] != 'com_app' OR $menu->query['view'] != 'item' OR $id != $this->item->id))
		{
			// If this is not a single item menu item, set the page title to the item name
			if ($this->item->name)
			{
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->item->name, 'link' => ''));
			if ( $this->params->get('show_item_category_breadcrumb', '0'))
			{			
				$options['countItems'] = false;
				$options['table'] = '#__app_items';					
				$category = JCategories::getInstance('App', $options)->get($this->item->catid);
				while ($category AND ($menu->query['option'] != 'com_app' OR $menu->query['view'] == 'item' OR $id != $category->id) AND $category->id > 1)
				{
					$path[] = array('title' => $category->title, 'link' => AppHelperRoute::getCategoryRoute($category->id, $this->params->get('keep_item_itemid')));
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
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0))
		{
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
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

		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}			


		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			$this->item->name = $this->item->name . ' - ' . $this->item->page_title;
			$this->document->setTitle($this->item->page_title . ' - ' . JText::sprintf('COM_APP_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
		}
		
		// Add css files for the app component and categories if they exist
		$this->document->addStyleSheet(JUri::root()."components/com_app/assets/css/app.css");
		$this->document->addStyleSheet(JUri::root()."components/com_app/assets/css/items.css");
	
		if ($lang->isRTL())
		{
			$this->document->addStyleSheet(JUri::root()."components/com_app/assets/css/app-rtl.css");
			$this->document->addStyleSheet(JUri::root()."components/com_app/assets/css/items-rtl.css");
		}
		
		// Include Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');	
			
		
	}
}
