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
 * HTML [%%CompObject_name%%] View class for the [%%ArchitectComp_name%%] component
 *
 */
class [%%ArchitectComp%%]View[%%CompObject%%]Form extends JView
{
	protected $state;
	protected $item;
	protected $return_page;
	protected $form;

	public function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();

		// Get model data.
		$state	= $this->get('State');
		$item	= $this->get('Item');
		$form	= $this->get('Form');

		[%%IF INCLUDE_ASSETACL%%]
		if (empty($item->id))
		{
			$authorised = $user->authorise('core.create', '[%%com_architectcomp%%]') 
			[%%IF GENERATE_CATEGORIES%%]
			OR (count($user->getAuthorisedCategories('[%%com_architectcomp%%]', 'core.create')))
			[%%ENDIF GENERATE_CATEGORIES%%]
			;
		}
		else
		{
			$authorised = $item->params->get('access-edit');
		}

		if ($authorised !== true)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		[%%ENDIF INCLUDE_ASSETACL%%]

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut to the parameters.
		$params	= &$state->params;

		$this->assignRef('state',	$state);
		$this->assignRef('params',	$params);
		$this->assignRef('item',	$item);
		$this->assignRef('form',	$form);
		$this->assignRef('user',	$user);

		[%%IF GENERATE_CATEGORIES%%]
		if ($this->params->get('enable_category') == 1)
		{
			$this->form->setFieldAttribute('catid', 'default',  $params->get('catid', 1));
		}
		[%%ENDIF GENERATE_CATEGORIES%%]

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
			$this->params->def('page_heading', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_EDIT_ITEM'));
		}

		if (!is_null($this->item->id))
		{
			$title = $this->params->def('page_title', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_EDIT_ITEM'));
			
			$pathway->addItem(JText::sprintf('[%%COM_ARCHITECTCOMP%%]_EDIT_ITEM', $this->escape($this->item->name)),''); 
		}
		else
		{
			$title = $this->params->def('page_title', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_CREATE_ITEM'));
			
			$pathway->addItem(JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_CREATE_ITEM'),'');
		}

		
		if ($app->getCfg('sitename_pagetitles', 0))
		{
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
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

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			[%%IF INCLUDE_NAME%%]
			$[%%compobject_code_name%%]->name = $[%%compobject_code_name%%]->name .' - '. $[%%compobject_code_name%%]->page_title;
			[%%ENDIF INCLUDE_NAME%%]			
			$this->document->setTitle($[%%compobject_code_name%%]->page_title.' - '.JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PAGEBREAK_PAGE_NUM', $this->state->get('page.offset') + 1));
		}

		// Add css files for the [%%architectcomp%%] component and categories if they exist
		$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/[%%architectcomp%%].css");
		$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/[%%compobjectplural%%].css");
	
		if ($lang->isRTL())
		{
			$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/[%%architectcomp%%]-rtl.css");
			$this->document->addStyleSheet(JUri::root()."components/[%%com_architectcomp%%]/assets/css/[%%compobjectplural%%]-rtl.css");
		}
		
		// Add Javscript functions for field display

		JHtml::_('behavior.keepalive');
		JHtml::_('behavior.tooltip');
		JHtml::_('behavior.calendar');		
		
		// Add Javscript functions for validation
		JHtml::_('behavior.formvalidation');
				
		$this->document->addScript(JUri::root() ."components/[%%com_architectcomp%%]/assets/js/[%%architectcomp%%]validate.js");
		
		$this->document->addScript(JUri::root() ."components/[%%com_architectcomp%%]/assets/js/formsubmitbutton.js");

		JText::script('[%%COM_ARCHITECTCOMP%%]_ERROR_ON_FORM');		// Include Helpers
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');	
	
	}
}
