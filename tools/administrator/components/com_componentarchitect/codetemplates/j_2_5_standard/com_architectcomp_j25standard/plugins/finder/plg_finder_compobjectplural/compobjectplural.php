<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].finder.[%%architectcomp%%].[%%compobjectplural%%]
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: compobjectplural.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.finder.architectcomp.compobjectplural
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

defined('JPATH_BASE') or die;

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder adapter for [%%com_architectcomp%%] - [%%compobjectplural%%]
 *
 */
class plgFinder[%%CompObjectPlural%%] extends FinderIndexerAdapter
{
	/**
	 * The plugin identifier.
	 *
	 * @var    string
	 * 
	 */
	protected $context = '[%%CompObjectPlural%%]';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * 
	 */
	protected $extension = '[%%com_architectcomp%%]';

	/**
	 * @var    $sub_layout	string	The sublayout to use when rendering the results.
	 */
	protected $sub_layout = 'default';

	/**
	 * @var    $layout	string	The layout (i.e. view) to use when rendering the results.
	 */
	protected $layout = '[%%compobject%%]';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var    string
	 * 
	 */
	protected $type_title = '[%%CompObject_plural_name%%]';

	/**
	 * The table name.
	 *
	 * @var    string
	 * 
	 */
	protected $table = '#__[%%architectcomp%%]_[%%compobjectplural%%]';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		if ($this->params->get('sub_layout') AND $this->params->get('sub_layout') != '_:default')
		{		
			$this->sub_layout = str_replace('_:','',$this->params->get('sub_layout'));
		}
		
		$this->loadLanguage();
	}

	[%%IF GENERATE_CATEGORIES%%]
	/**
	 * Method to update the item link information when the item category is
	 * changed. This is fired when the item category is published or unpublished
	 * from the list view.
	 *
	 * @param   string   $extension  The extension whose category has been updated.
	 * @param   array    $pks        A list of primary key ids of the content that has changed state.
	 * @param   integer  $value      The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 */
	public function onFinderCategoryChangeState($extension, $pks, $value)
	{
		// Make sure we're handling [%%com_architectcomp%%] categories
		if ($extension == '[%%com_architectcomp%%]')
		{
			$this->categoryStateChange($pks, $value);
		}
	}
	[%%ENDIF GENERATE_CATEGORIES%%]

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == '[%%com_architectcomp%%].[%%compobject%%]')
		{
			$id = $table->id;
		}
		elseif ($context == 'com_finder.index')
		{
			$id = $table->link_id;
		}
		else
		{
			return true;
		}
		// Remove the items.
		return $this->remove($id);
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string   $context  The context of the object passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $is_new    If the content has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $is_new)
	{
		// We only want to handle [%%compobject_plural_name%%] here
		if ($context == '[%%com_architectcomp%%].[%%compobject%%]' OR $context == '[%%com_architectcomp%%].[%%compobject%%]form')
		{
			[%%IF INCLUDE_ACCESS%%]
			// Check if the access levels are different
			if (!$is_new AND $this->old_access != $row->access)
			{
				// Process the change.
				$this->itemAccessChange($row);
			}
			[%%ENDIF INCLUDE_ACCESS%%]

			// Reindex the item
			$this->reindex($row->id);
		}

		[%%IF GENERATE_CATEGORIES%%]
		// Check for access changes in the category
		if ($context == 'com_categories.category')
		{
			// Check if the access levels are different
			if (!$is_new AND $this->old_cataccess != $row->access)
			{
				$this->categoryAccessChange($row);
			}
		}
		[%%ENDIF GENERATE_CATEGORIES%%]

		return true;
	}

	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @param   string   $context  The context of the object passed to the plugin.
	 * @param   JTable   $row     A JTable object
	 * @param   boolean  $is_new    If the content is just about to be created
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on database error.
	 */
	public function onFinderBeforeSave($context, $row, $is_new)
	{
		[%%IF INCLUDE_ACCESS%%]
		// We only want to handle [%%compobject_plural_name%%] here
		if ($context == '[%%com_architectcomp%%].[%%compobject%%]' OR $context == '[%%com_architectcomp%%].[%%compobject%%]form')
		{
			// Query the database for the old access level if the item isn't new
			if (!$is_new)
			{
				$this->checkItemAccess($row);
			}
		}
		[%%ENDIF INCLUDE_ACCESS%%]

		[%%IF GENERATE_CATEGORIES%%]
		// Check for access levels from the category
		if ($context == 'com_categories.category')
		{
			// Query the database for the old access level if the item isn't new
			if (!$is_new)
			{
				$this->checkCategoryAccess($row);
			}
		}
		[%%ENDIF GENERATE_CATEGORIES%%]

		return true;
	}

	[%%IF INCLUDE_STATUS%%]
	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param   string   $context  The context for the object passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the content that has changed state.
	 * @param   integer  $value    The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 */
	public function onFinderChangeState($context, $pks, $value)
	{
		[%%IF GENERATE_CATEGORIES%%]
		// We only want to handle [%%compobject_plural_name%%] here
		if ($context == '[%%com_architectcomp%%].[%%compobject%%]' OR $context == '[%%com_architectcomp%%].[%%compobject%%]form')
		{
			$this->itemStateChange($pks, $value);
		}
		[%%ENDIF GENERATE_CATEGORIES%%]
		// Handle when the plugin is disabled
		if ($context == 'com_plugins.plugin' AND $value === 0)
		{
			$this->pluginDisable($pks);
		}
	}
	[%%ENDIF INCLUDE_STATUS%%]

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   FinderIndexerResult  $item    The item to index as an FinderIndexerResult object.
	 * @param   string               $format  The item format
	 *
	 * @return  void
	 *
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{
		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// Initialize the item parameters.
		$registry = new JRegistry;
		$registry->loadString($item->params);
		$item->params = JComponentHelper::getParams('[%%com_architectcomp%%]', true);
		$item->params->merge($registry);

		$registry = new JRegistry;
		$registry->loadString($item->metadata);
		$item->metadata = $registry;

		// Trigger the onContentPrepare event.
		[%%IF INCLUDE_INTRO%%]
		$item->summary = FinderIndexerHelper::prepareContent($item->summary, $item->params);
		[%%ENDIF INCLUDE_INTRO%%]
		[%%IF INCLUDE_DESCRIPTION%%]
		$item->body = FinderIndexerHelper::prepareContent($item->body, $item->params);
		[%%ENDIF INCLUDE_DESCRIPTION%%]

		if ($this->sub_layout != 'default')
		{
			$view = $this->layout.'&layout='.$this->sub_layout;
		}
		else
		{
			$view = $this->layout;
		}

		// Build the necessary route and path information.
		$item->url = $this->getURL($item->id, $this->extension, $view);
	
		[%%IF GENERATE_CATEGORIES%%]		 
			[%%IF INCLUDE_LANGUAGE%%]
		$item->route = [%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->catslug, $item->language, $this->sub_layout);
			[%%ELSE INCLUDE_LANGUAGE%%]
		$item->route = [%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->catslug, $this->sub_layout);
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ELSE GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_LANGUAGE%%]
		$item->route = [%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $item->language, $this->sub_layout);
			[%%ELSE INCLUDE_LANGUAGE%%]
		$item->route = [%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, $this->sub_layout);
			[%%ENDIF INCLUDE_LANGUAGE%%]	
		[%%ENDIF GENERATE_CATEGORIES%%]					
		$item->path = FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->getItemMenuTitle($item->url);

		if (!empty($title) AND $this->params->get('use_menu_title', true))
		{
			$item->title = $title;
		}
		else
		{
			[%%IF INCLUDE_NAME%%]
			$item->title = $item->name;
			[%%ELSE INCLUDE_NAME%%]
			$item->title = $this->type_title.' - '.$item->id;
			[%%ENDIF INCLUDE_NAME%%]			
		}
		
		[%%IF INCLUDE_METADATA%%]
		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		[%%ENDIF INCLUDE_METADATA%%]
		
		[%%IF INCLUDE_CREATED%%]
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'created_by_name');
		[%%ENDIF INCLUDE_CREATED%%]

		[%%IF INCLUDE_STATUS%%]
		// Translate the state. 
		$item->state = $this->translateState($item->state);
		[%%ENDIF INCLUDE_STATUS%%]

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', '[%%CompObject_name%%]');

		[%%IF INCLUDE_CREATED%%]
		// Add the creator taxonomy data.
		$item->addTaxonomy('Author', !empty($item->created_by_name) ? $item->created_by_name : $item->created_by);
		[%%ENDIF INCLUDE_CREATED%%]

		[%%IF GENERATE_CATEGORIES%%]
		// Add the category taxonomy data.
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);
		[%%ENDIF GENERATE_CATEGORIES%%]

		[%%IF INCLUDE_LANGUAGE%%]
		// Add the language taxonomy data.
		$item->addTaxonomy('Language', $item->language);
		[%%ENDIF INCLUDE_LANGUAGE%%]

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	protected function setup()
	{
		// Load dependent classes.
		include_once JPATH_SITE . '/components/[%%com_architectcomp%%]/helpers/route.php';

		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $sql  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 */
	protected function getListQuery($sql = null)
	{
		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = $sql instanceof JDatabaseQuery ? $sql : $db->getQuery(true);
		$sql->select('a.id');
		[%%IF INCLUDE_NAME%%]
		$sql->select('a.name');
			[%%IF INCLUDE_ALIAS%%]
		$sql->select('a.alias');
			[%%ENDIF INCLUDE_ALIAS%%]
		[%%ENDIF INCLUDE_NAME%%]		
		[%%IF INCLUDE_INTRO%%]
		$sql->select('a.intro AS summary');
		[%%ENDIF INCLUDE_INTRO%%]
		[%%IF INCLUDE_DESCRIPTION%%]
		$sql->select('a.description AS body');
		[%%ENDIF INCLUDE_DESCRIPTION%%]
		[%%IF INCLUDE_STATUS%%]
		$sql->select('a.state');
		[%%ENDIF INCLUDE_STATUS%%]
		[%%IF GENERATE_CATEGORIES%%]
		$sql->select('a.catid');
		[%%ENDIF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_CREATED%%]
		$sql->select('a.created AS start_date, a.created_by');
		[%%ENDIF INCLUDE_CREATED%%]
		[%%IF INCLUDE_MODIFIED%%]
		$sql->select('a.modified, a.modified_by');
		[%%ENDIF INCLUDE_MODIFIED%%]
		[%%IF INCLUDE_METADATA%%]
		$sql->select('a.metakey, a.metadesc'); 
		[%%ENDIF INCLUDE_METADATA%%]
		[%%IF INCLUDE_LANGUAGE%%]
		$sql->select('a.language'); 
		[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%IF INCLUDE_ACCESS%%]
		$sql->select('a.access'); 
		[%%ENDIF INCLUDE_ACCESS%%]
		[%%IF INCLUDE_PUBLISHED_DATES%%]
		$sql->select('a.publish_up AS publish_start_date, a.publish_down AS publish_end_date');
		[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
		[%%IF GENERATE_CATEGORIES%%]
		$sql->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');
		[%%ENDIF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_PARAMS_RECORD%%]
		$sql->select('a.params');
		[%%ENDIF INCLUDE_PARAMS_RECORD%%]
		[%%IF INCLUDE_ORDERING%%]
		$sql->select('a.ordering');
		[%%ENDIF INCLUDE_ORDERING%%]		

		[%%IF INCLUDE_ACCESS%%]
		// Handle the alias CASE WHEN portion of the query
		$case_when_item_alias = ' CASE WHEN ';
		$case_when_item_alias .= $sql->charLength('a.alias');
		$case_when_item_alias .= ' THEN ';
		$a_id = $sql->castAsChar('a.id');
		$case_when_item_alias .= $sql->concatenate(array($a_id, 'a.alias'), ':');
		$case_when_item_alias .= ' ELSE ';
		$case_when_item_alias .= $a_id.' END as slug';
		$sql->select($case_when_item_alias);
		[%%ELSE INCLUDE_ACCESS%%]
		// Handle the alias CASE WHEN portion of the query
		$a_id = $sql->castAsChar('a.id');
		$case_when_item .= $a_id.' END as slug';
		$sql->select($case_when_item);
		[%%ENDIF INCLUDE_ACCESS%%]
		
		[%%IF GENERATE_CATEGORIES%%]
		$case_when_category_alias = ' CASE WHEN ';
		$case_when_category_alias .= $sql->charLength('c.alias');
		$case_when_category_alias .= ' THEN ';
		$c_id = $sql->castAsChar('c.id');
		$case_when_category_alias .= $sql->concatenate(array($c_id, 'c.alias'), ':');
		$case_when_category_alias .= ' ELSE ';
		$case_when_category_alias .= $c_id.' END as catslug';
		$sql->select($case_when_category_alias);
		[%%ENDIF GENERATE_CATEGORIES%%]

		$sql->from('#__[%%architectcomp%%]_[%%compobjectplural%%] AS a');
		[%%IF GENERATE_CATEGORIES%%]
		$sql->join('LEFT', '#__categories AS c ON c.id = a.catid');
		[%%ENDIF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_CREATED%%]
		$sql->select('u.name AS created_by_name');		
		$sql->join('LEFT', '#__users AS u ON u.id = a.created_by');
		[%%ENDIF INCLUDE_CREATED%%]

		return $sql;
	}
}
