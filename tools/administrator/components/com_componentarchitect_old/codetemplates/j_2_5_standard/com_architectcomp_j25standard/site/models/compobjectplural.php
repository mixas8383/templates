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
 * @version			$Id: compobjectplural.php 424 2014-10-23 14:08:27Z BrianWade $
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

jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of [%%compobject_plural_name%%].
 *
 */
class [%%ArchitectComp%%]Model[%%CompObjectPlural%%] extends JModelList
{
	/**
	 * Model context string.  Used in setting the store id for the session
	 *
	 * @var		string
	 */
	protected $context = '[%%com_architectcomp%%].[%%compobjectplural%%]';

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * 
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				[%%IF INCLUDE_NAME%%]
				'name', 'a.name',
					[%%IF INCLUDE_ALIAS%%]
				'alias', 'a.alias',
					[%%ENDIF INCLUDE_ALIAS%%]
				[%%ENDIF INCLUDE_NAME%%]				
				[%%FOREACH FILTER_FIELD%%]
				'[%%FIELD_CODE_NAME%%]','a.[%%FIELD_CODE_NAME%%]',
					[%%IF FIELD_FILTER_LINK%%]
				'[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]', '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]',				
					[%%ENDIF FIELD_FILTER_LINK%%]
				[%%ENDFOR FILTER_FIELD%%]	
				[%%IF INCLUDE_CHECKOUT%%]
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				[%%ENDIF INCLUDE_CHECKOUT%%]
				[%%IF GENERATE_CATEGORIES%%]				
				'catid', 'a.catid', 'category_title',
				[%%ENDIF GENERATE_CATEGORIES%%]				
				[%%IF INCLUDE_CREATED%%]
				'created', 'a.created',
				'created_by', 'a.created_by',
				'created_by_name', 'ua.name',
				[%%ENDIF INCLUDE_CREATED%%]				
				[%%IF INCLUDE_MODIFIED%%]
				'modified', 'a.modified',
				'modified_by', 'a.modified_by',
				'modified_by_name', 'uam.name',	
				[%%ENDIF INCLUDE_MODIFIED%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
				'publish_up', 'a.publish_up',				
				'publish_down', 'a.publish_down',	
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]			
				[%%IF INCLUDE_FEATURED%%]
				'featured', 'a.featured',
				[%%ENDIF INCLUDE_FEATURED%%]
				[%%IF INCLUDE_LANGUAGE%%]
				'language', 'a.language',
				[%%ENDIF INCLUDE_LANGUAGE%%]					
				[%%IF INCLUDE_HITS%%]
				'hits', 'a.hits',
				[%%ENDIF INCLUDE_HITS%%]
				[%%IF GENERATE_PLUGINS_VOTE%%]
				'rating',
				[%%ENDIF GENERATE_PLUGINS_VOTE%%]
				[%%IF INCLUDE_ORDERING%%]
				'ordering', 'a.ordering',
				[%%ENDIF INCLUDE_ORDERING%%]								
				);
		}

		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * 
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();
		$menu_params = new JRegistry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menu_params->loadString($menu->params);
		}

		$merged_params = clone $menu_params;
		$merged_params->merge($params);

		$this->setState('params', $merged_params);

		$params = $this->state->params;	
		
		$user		= JFactory::getUser();
		
		$item_id = JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);

		// Check to see if a single [%%compobject_name%%] has been specified either as a parameter or in the url Request
		$pk = $params->get('[%%compobject_code_name%%]_id', '') == '' ? JRequest::getInt('id', '') : $params->get('[%%compobject_code_name%%]_id');
		$this->setState('filter.[%%compobject_code_name%%]_id', $pk);
		
		// List state information
		[%%IF GENERATE_SITE_LAYOUT_BLOG%%]
		if (JRequest::getCmd('layout', 'default') == 'blog')
		{
			$limit = $params->def('[%%compobject%%]_num_leading', 1) + $params->def('[%%compobject%%]_num_intro', 4) + $params->def('[%%compobject%%]_num_links', 4);
		}
		else
		{		
			$limit = $app->getUserStateFromRequest($this->context.'.list.' . $item_id . '.limit', 'limit', $params->get('[%%compobject%%]_num_per_page'),'integer');
		}
		[%%ELSE GENERATE_SITE_LAYOUT_BLOG%%]
			$limit = $app->getUserStateFromRequest($this->context.'.list.' . $item_id . '.limit', 'limit', $params->get('[%%compobject%%]_num_per_page'),'integer');
		[%%ENDIF GENERATE_SITE_LAYOUT_BLOG%%]
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context.'.limitstart','limitstart',0,'integer');
		$this->setState('list.start', $value);

		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		[%%IF GENERATE_CATEGORIES%%]
		$category_id = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $category_id);		
		[%%ENDIF GENERATE_CATEGORIES%%]

		[%%IF INCLUDE_ORDERING%%]
		$order_col = $app->getUserStateFromRequest($this->context. '.filter_order', 'filter_order', $params->get('[%%compobject%%]_initial_sort','a.ordering'), 'string');
		if (!in_array($order_col, $this->filter_fields))
		{
			$order_col = $params->get('[%%compobject%%]_initial_sort','a.ordering');
		}
		[%%ELSE INCLUDE_ORDERING%%]
			[%%IF INCLUDE_NAME%%]
		$order_col = $app->getUserStateFromRequest($this->context. '.filter_order', 'filter_order', $params->get('[%%compobject%%]_initial_sort','a.name'), 'string');
		if (!in_array($order_col, $this->filter_fields))
		{
			$order_col = $params->get('[%%compobject%%]_initial_sort','a.name');
		}
			[%%ELSE INCLUDE_NAME%%]
		$order_col = $app->getUserStateFromRequest($this->context. '.filter_order', 'filter_order', $params->get('[%%compobject%%]_initial_sort','a.id'), 'string');
		if (!in_array($order_col, $this->filter_fields))
		{
			$order_col = $params->get('[%%compobject%%]_initial_sort','a.id');
		}
			[%%ENDIF INCLUDE_NAME%%]		
		[%%ENDIF INCLUDE_ORDERING%%]			

		$this->setState('list.ordering', $order_col);

		$list_order = $app->getUserStateFromRequest($this->context. '.filter_order_Dir', 'filter_order_Dir',  $params->get('[%%compobject%%]_initial_direction','ASC'), 'cmd');
		if (!in_array(JString::strtoupper($list_order), array('ASC', 'DESC', '')))
		{
			$list_order =  $params->get('[%%compobject%%]_initial_direction','ASC');
		}
		$this->setState('list.direction', $list_order);
		
		[%%FOREACH FILTER_FIELD%%]
		$[%%FIELD_CODE_NAME%%] = $app->getUserStateFromRequest($this->context.'.filter.[%%FIELD_CODE_NAME%%]', 'filter_[%%FIELD_CODE_NAME%%]', [%%FIELD_FILTER_DEFAULT%%], '[%%FIELD_PHP_VARIABLE_TYPE%%]');
		$this->setState('filter.[%%FIELD_CODE_NAME%%]', $[%%FIELD_CODE_NAME%%]);
		[%%ENDFOR FILTER_FIELD%%]
				
		[%%IF INCLUDE_STATUS%%]
			[%%IF INCLUDE_ASSETACL%%]
		if ((!$user->authorise('core.edit.state', '[%%com_architectcomp%%]')) AND  (!$user->authorise('core.edit', '[%%com_architectcomp%%]')))
		{
			// filter on status of published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}		
			[%%ELSE INCLUDE_ASSETACL%%]
		$this->setState('filter.published', 1);		
			[%%ENDIF INCLUDE_ASSETACL%%]
		[%%ENDIF INCLUDE_STATUS%%]

		[%%IF INCLUDE_LANGUAGE%%]
		$this->setState('filter.language',$app->getLanguageFilter());
		[%%ENDIF INCLUDE_LANGUAGE%%]
		
		[%%IF INCLUDE_ACCESS%%]
		// process show_[%%compobject%%]_noauth parameter
		if (!$params->get('show_[%%compobject%%]_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}
		[%%ENDIF INCLUDE_ACCESS%%]
		[%%IF GENERATE_CATEGORIES%%]		
		// check for category selection
		if ($params->get('filter_[%%compobject%%]_categories') AND implode(',', $params->get('filter_[%%compobject%%]_categories'))  == true)
		{
			$selected_categories = $params->get('filter_[%%compobject%%]_categories');
			$this->setState('filter.category_id', $selected_categories);
		}			
		[%%ENDIF GENERATE_CATEGORIES%%]			
		[%%IF INCLUDE_FEATURED%%]
		if ($params->get('filter_[%%compobject%%]_featured') <> "")
		{
			$this->setState('filter.featured', $params->get('filter_[%%compobject%%]_featured'));
			
		}
		[%%ENDIF INCLUDE_FEATURED%%]
		[%%IF INCLUDE_STATUS%%]
		if ($params->get('filter_[%%compobject%%]_archived'))
		{
			$this->setState('filter.archived', $params->get('filter_[%%compobject%%]_archived'));
			
		}
		[%%ENDIF INCLUDE_STATUS%%]
		$this->setState('layout', JRequest::getCmd('layout'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * 
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.search');				
		[%%IF INCLUDE_STATUS%%]
		$id .= ':'.serialize($this->getState('filter.published'));
		$id .= ':'.$this->getState('filter.archived');			
		[%%ENDIF INCLUDE_STATUS%%]
		[%%IF INCLUDE_ACCESS%%]
		$id .= ':'.$this->getState('filter.access');
		[%%ENDIF INCLUDE_ACCESS%%]
		[%%IF INCLUDE_FEATURED%%]
		$id .= ':'.$this->getState('filter.featured');
		[%%ENDIF INCLUDE_FEATURED%%]
		[%%IF GENERATE_CATEGORIES%%]		
		$id .= ':'.serialize($this->getState('filter.category_id'));
		$id .= ':'.serialize($this->getState('filter.category_id.include'));
		[%%ENDIF GENERATE_CATEGORIES%%]		
		[%%IF INCLUDE_CREATED%%]
		$id .= ':'.$this->getState('filter.created_by_id');
		$id .= ':'.$this->getState('filter.created_by_id.include');
		$id .= ':'.$this->getState('filter.created_by_name');
		$id .= ':'.$this->getState('filter.created_by_name.include');	
		[%%ENDIF INCLUDE_CREATED%%]	
		[%%FOREACH FILTER_FIELD%%]
		$id	.= ':'.$this->getState('filter.[%%FIELD_CODE_NAME%%]');	
		[%%ENDFOR FILTER_FIELD%%]	
		$id .= ':'.$this->getState('filter.[%%compobject_code_name%%]_id');
		$id .= ':'.$this->getState('filter.[%%compobject_code_name%%]_id.include');				
		

		return parent::getStoreId($id);
	}

	/**
	 * Get the main query for retrieving a list of [%%compobjectplural%%] subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 * 
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$user	= JFactory::getUser();
	
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
					'list.select',
					'a.*'
					)
				);


		$query->from($db->quoteName('#__[%%architectcomp%%]_[%%compobjectplural%%]').' AS a');
		[%%IF GENERATE_CATEGORIES%%]
		// Join over the categories.
		$query->select($db->quoteName('c.title').' AS category_title, '.
						$db->quoteName('c.alias').' AS category_alias, '.	
						$db->quoteName('c.access').' AS category_access, '.
						$db->quoteName('c.path').' AS category_route'
		);
		$query->join('LEFT', $db->quoteName('#__categories').' AS c ON '.$db->quoteName('c.id').' = '.$db->quoteName('a.catid'));
		// Join over the categories to get parent category titles
		$query->select($db->quoteName('parent.title').' AS parent_title, '.
						$db->quoteName('parent.id').' AS parent_id, '.
						$db->quoteName('parent.alias').' AS parent_alias, '.
						$db->quoteName('parent.path').' AS parent_route'
		);
		$query->join('LEFT', $db->quoteName('#__categories').' as parent ON '.$db->quoteName('parent.id').' = '.$db->quoteName('c.parent_id'));
		[%%ENDIF GENERATE_CATEGORIES%%]		


		[%%IF INCLUDE_CREATED%%]
		$query->select($db->quoteName('ua.name').' AS created_by_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS ua on '.$db->quoteName('ua.id').' = '.$db->quoteName('a.created_by'));
		[%%ENDIF INCLUDE_CREATED%%]

		[%%IF INCLUDE_MODIFIED%%]
		$query->select($db->quoteName('uam.name').' AS modified_by_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS uam on '.$db->quoteName('uam.id').' = '.$db->quoteName('a.modified_by'));
		[%%ENDIF INCLUDE_MODIFIED%%]
		
		[%%IF INCLUDE_LANGUAGE%%]
			[%%IF INCLUDE_CREATED%%]
		// Get contact id
		$sub_query = $db->getQuery(true);
		$sub_query->select('MAX(contact.id) AS id');
		$sub_query->from('#__contact_details AS contact');
		$sub_query->where('contact.published = 1');
		$sub_query->where('contact.user_id = a.created_by');

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$sub_query->where('(contact.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ') OR contact.language IS NULL)');
		}

		$query->select('(' . $sub_query . ') as contactid');
			[%%ENDIF INCLUDE_CREATED%%]
		[%%ENDIF INCLUDE_LANGUAGE%%]
		
		[%%IF GENERATE_PLUGINS_VOTE%%]
		// Join on vote rating table
		$query->select('ROUND('.$db->quoteName('v.rating_sum').' / '.$db->quoteName('v.rating_count').', 0) AS rating, '.$db->quoteName('v.rating_count').' as rating_count');
		$query->join('LEFT', $db->quoteName('#__[%%architectcomp%%]_rating').' AS v ON '.$db->quoteName('a.id').' = '.$db->quoteName('v.content_id').' AND '.$db->quoteName('v.content_type').' = '.$db->quote('[%%compobjectplural%%]'));
		[%%ENDIF GENERATE_PLUGINS_VOTE%%]
		[%%IF INCLUDE_PUBLISHED_DATES%%]	
		
			[%%IF INCLUDE_ASSETACL%%]
		if ((!$user->authorise('core.edit.state', '[%%com_architectcomp%%]')) AND (!$user->authorise('core.edit', '[%%com_architectcomp%%]')))
		{
			[%%ENDIF INCLUDE_ASSETACL%%]
			//  Do not show unless today's date is within the publish up and down dates (or they are empty)
			$null_date = $db->quote($db->getNullDate());
			$now_date = $db->quote(JFactory::getDate()->toSQL());	
			$query->where('('.$db->quoteName('a.publish_up').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_up').' <= ' . $now_date . ')');
			$query->where('('.$db->quoteName('a.publish_down').' = ' . $null_date . ' OR '.$db->quoteName('a.publish_down').' >= ' . $now_date . ')');
			[%%IF INCLUDE_ASSETACL%%]
		}
			[%%ENDIF INCLUDE_ASSETACL%%]				
		[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
		[%%IF INCLUDE_ACCESS%%]
		
		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where($db->quoteName('a.access').' IN ('.$groups.')');
			[%%IF GENERATE_CATEGORIES%%]
			$query->where($db->quoteName('c.access').' IN ('.$groups.')');
			[%%ENDIF GENERATE_CATEGORIES%%]
		}
		[%%ENDIF INCLUDE_ACCESS%%]


		[%%IF INCLUDE_STATUS%%]
		// Filter by published status
		$published = $this->getState('filter.published');
		$archived = $this->getState('filter.archived');		
		if (is_numeric($archived))
		{
			$query->where($db->quoteName('a.state').' = '. (int) $archived);
			
		}
		else
		{
			if (is_numeric($published))
			{
				$query->where($db->quoteName('a.state').' = '. (int) $published);
				
			}
			else 
			{
				if (is_array($published))
				{
					JArrayHelper::toInteger($published);
					$published = implode(',', $published);
					// Use [%%compobject%%] state 
					$query->where($db->quoteName('a.state').' IN ('.$published.')');
				}
			}
		}
		[%%ENDIF INCLUDE_STATUS%%]

		[%%IF INCLUDE_FEATURED%%]
		// Filter by featured state
		$featured = $this->getState('filter.featured');
		switch ($featured)
		{
			case '0':
				$query->where($db->quoteName('a.featured').' = 0');
				break;

			case '1':
				$query->where($db->quoteName('a.featured').' = 1');
				break;

			default:
				// Normally we do not discriminate
				// between featured/unfeatured items.
				break;
		}
		[%%ENDIF INCLUDE_FEATURED%%]
		
		[%%FOREACH OBJECT_FIELD%%]
			[%%IF FIELD_ACCESSLEVEL%%]
		// Join over the access level.
		$query->select('[%%FIELD_CODE_NAME%%].title AS [%%FIELD_CODE_NAME%%]_level');
		$query->join('LEFT', $db->quoteName('#__viewlevels').' AS [%%FIELD_CODE_NAME%%] ON '.$db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quoteName('a.[%%FIELD_CODE_NAME%%]'));
			[%%ENDIF FIELD_ACCESSLEVEL%%]
			[%%IF FIELD_CATEGORY%%]
		// Join over the category.
		$query->select($db->quoteName('[%%FIELD_CODE_NAME%%].title').' AS [%%FIELD_CODE_NAME%%]_title');
		$query->join('LEFT', $db->quoteName('#__categories').' AS [%%FIELD_CODE_NAME%%] ON '.$db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quoteName('a.[%%FIELD_CODE_NAME%%]'));			
			[%%ENDIF FIELD_CATEGORY%%]		
			[%%IF FIELD_USER%%]
		// Join over the user.
		$query->select('[%%FIELD_CODE_NAME%%].name AS [%%FIELD_CODE_NAME%%]_name');
		$query->join('LEFT', '#__users AS [%%FIELD_CODE_NAME%%] ON [%%FIELD_CODE_NAME%%].id = a.[%%FIELD_CODE_NAME%%]');		
			[%%ENDIF FIELD_USER%%]		
			[%%IF FIELD_USERGROUP%%]
		// Join over the user group.
		$query->select('[%%FIELD_CODE_NAME%%].title AS [%%FIELD_CODE_NAME%%]_title');
		$query->join('LEFT', '#__usergroups AS [%%FIELD_CODE_NAME%%] ON [%%FIELD_CODE_NAME%%].id = a.[%%FIELD_CODE_NAME%%]');		
			[%%ENDIF FIELD_USERGROUP%%]					
			[%%IF FIELD_LINK%%]
		// Filter by and return name for [%%FIELD_CODE_NAME%%] level.
		$query->select($db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]').' AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]');
		$query->select($db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_ORDERING_FIELD%%]').' AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_ORDERING_FIELD%%]');

		$query->join('LEFT', $db->quoteName('#__[%%architectcomp%%]_[%%FIELD_FOREIGN_OBJECT_PLURAL%%]').' AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%] ON '.$db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].id').' = '.$db->quoteName('a.[%%FIELD_CODE_NAME%%]'));	
			[%%ENDIF FIELD_LINK%%]								
		[%%ENDFOR OBJECT_FIELD%%]
	
		[%%FOREACH FILTER_FIELD%%]
		if ($[%%FIELD_CODE_NAME%%] = $this->getState('filter.[%%FIELD_CODE_NAME%%]'))
		{
			[%%IF FIELD_LINK%%]
			$query->where($db->quoteName('a.[%%FIELD_CODE_NAME%%]').' = ' . (int) $[%%FIELD_CODE_NAME%%]);
			[%%ELSE FIELD_LINK%%]
				[%%IF FIELD_MULTIPLE%%]
			$query->where("CONCAT('(',replace(".$db->quoteName('a.[%%FIELD_CODE_NAME%%]').",',','),('),'),') LIKE '%(".$[%%FIELD_CODE_NAME%%]."),%'");
				[%%ELSE FIELD_MULTIPLE%%]			
			$query->where($db->quoteName('a.[%%FIELD_CODE_NAME%%]').' = ' . $db->quote($[%%FIELD_CODE_NAME%%]));
				[%%ENDIF FIELD_MULTIPLE%%]	
			[%%ENDIF FIELD_LINK%%]			
		}	
		[%%ENDFOR FILTER_FIELD%%]				

		// Filter by a single or group of [%%compobjectplural%%].
		$[%%compobject_code_name%%]_id = $this->getState('filter.[%%compobject_code_name%%]_id');
		if ($[%%compobject_code_name%%]_id != '')
		{
			if (is_numeric($[%%compobject_code_name%%]_id))
			{
				$type = $this->getState('filter.[%%compobject_code_name%%]_id.include', true) ? '= ' : '<> ';
				$query->where($db->quoteName('a.id').' '.$type.(int) $[%%compobject_code_name%%]_id);
			}
			else
			{
				if (is_array($[%%compobject_code_name%%]_id))
				{
					JArrayHelper::toInteger($[%%compobject_code_name%%]_id);
					$[%%compobject_code_name%%]_id = implode(',', $[%%compobject_code_name%%]_id);
					$type = $this->getState('filter.[%%compobject_code_name%%]_id.include', true) ? 'IN' : 'NOT IN';
					$query->where($db->quoteName('a.id').' '.$type.' ('.$[%%compobject_code_name%%]_id.')');
				}
			}
		}
		[%%IF GENERATE_CATEGORIES%%]
		// Filter by a single or group of categories
		$category_id = $this->getState('filter.category_id');

		if (is_numeric($category_id))
		{
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$include_sub_categories = $this->getState('filter.subcategories', false);
			$category_equals = $db->quoteName('a.catid').' '.$type.(int) $category_id;

			if ($include_sub_categories)
			{
				$levels = (int) $this->getState('filter.max_category_levels', '1');
				// Create a subquery for the subcategory list
				$sub_query = $db->getQuery(true);
				$sub_query->select($db->quoteName('sub.id'));
				$sub_query->from($db->quoteName('#__categories').' as sub');
				$sub_query->join('INNER', $db->quoteName('#__categories').' as this ON '.$db->quoteName('sub.lft').' > '.$db->quoteName('this.lft').' AND '.$db->quoteName('sub.rgt').' < '.$db->quoteName('this.rgt'));
				$sub_query->where($db->quoteName('this.id').' = '.(int) $category_id);
				$sub_query->where($db->quoteName('sub.level').' <= '.$db->quoteName('this.level').' + '.$levels);

				// Add the subquery to the main query
				$query->where('('.$category_equals.' OR '.$db->quoteName('a.catid').' IN ('.$sub_query->__toString().'))');
			}
			else
			{
				$query->where($category_equals);
			}
		}
		else
		{
			if (is_array($category_id) AND (count($category_id) > 0))
			{
				JArrayHelper::toInteger($category_id);
				$category_id = implode(',', $category_id);
				if (!empty($category_id))
				{
					$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
					$query->where($db->quoteName('a.catid').' '.$type.' ('.$category_id.')');
				}
			}
		}
		[%%ENDIF GENERATE_CATEGORIES%%]
		
		[%%IF INCLUDE_CREATED%%]
		// Filter by creator
		$created_by_id = $this->getState('filter.created_by_id');
		$created_by_where = '';

		if (is_numeric($created_by_id))
		{
			$type = $this->getState('filter.created_by_id.include', true) ? '= ' : '<> ';
			$created_by_where = $db->quoteName('a.created_by').' '.$type.(int) $created_by_id;
		}
		else 
		{
			if (is_array($created_by_id))
			{
				JArrayHelper::toInteger($created_by_id);
				$created_by_id = implode(',', $created_by_id);

				if ($created_by_id)
				{
					$type = $this->getState('filter.created_by_id.include', true) ? 'IN' : 'NOT IN';
					$created_by_where = $db->quoteName('a.created_by').' '.$type.' ('.$created_by_id.')';
				}
			}
		}


		if (!empty($created_by_where) )
		{
			$query->where('('.$created_by_where.')');
		}
		
		// Filter by creator_name
		$created_by_name = $this->getState('filter.created_by_name');
		$created_by_name_where = '';

		if (is_string($created_by_name))
		{
			$type = $this->getState('filter.created_by_name.include', true) ? '= ' : '<> ';
			$created_by_name_where = $db->quoteName('ua.name').' '.$type.$db->quote($created_by_name);
		}
		else
		{
			if (is_array($created_by_name))
			{
				$first = current($created_by_name);

				if (!empty($first))
				{
					JArrayHelper::toString($created_by_name);

					foreach ($created_by_name as $key => $alias)
					{
						$created_by_name[$key] = $db->quote($alias);
					}

					$created_by_name = implode(',', $created_by_name);

					if ($created_by_name)
					{
						$type = $this->getState('filter.created_by_name.include', true) ? 'IN' : 'NOT IN';
						$created_by_name_where = $db->quoteName('ua.name').' '.$type.' ('.$created_by_name .
							')';
					}
				}
			}
		}

		if (!empty($created_by_name_where) )
		{
			$query->where('('.$created_by_name_where.')');
		}
		[%%ENDIF INCLUDE_CREATED%%]

		// process the filter for list views with user-entered filters
		$params = $this->getState('params');

		if ((is_object($params)) AND ($params->get('[%%compobject%%]_filter_field') != 'hide') AND ($filter = $this->getState('filter.search')))
		{
			// clean filter variable
			$filter = JString::strtolower($filter);
			[%%IF INCLUDE_HITS%%]
			$hits_filter = intval($filter);
			[%%ENDIF INCLUDE_HITS%%]
			$filter = $db->quote('%'.$db->escape($filter, true).'%', false);

			switch ($params->get('[%%compobject%%]_filter_field'))
			{
				[%%IF INCLUDE_HITS%%]
				case 'hits':
					$query->where($db->quoteName('a.hits').' >= '.(int) $hits_filter.' ');
					break;
				[%%ENDIF INCLUDE_HITS%%]
				[%%IF INCLUDE_CREATED%%]
				case 'created_by':
					$query->where('LOWER('.$db->quoteName('ua.name').') LIKE '.$filter.' ');
					break;	
				[%%ENDIF INCLUDE_CREATED%%]					
				[%%IF INCLUDE_NAME%%]
				case 'name':
				default: // default to 'name' if parameter is not valid
					$query->where('LOWER('.$db->quoteName('a.name').') LIKE '.$filter);
					break;
				[%%ELSE INCLUDE_NAME%%]
				default:
					break;
				[%%ENDIF INCLUDE_NAME%%]
				
			}
		}
		[%%IF INCLUDE_LANGUAGE%%]
		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where($db->quoteName('a.language').' IN ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
			[%%IF INCLUDE_CREATED%%]
			$query->where('('.$db->quoteName('contact.language').' IN ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR '.$db->quoteName('contact.language').' IS NULL)');
			[%%ENDIF INCLUDE_CREATED%%]
		}
		[%%ENDIF INCLUDE_LANGUAGE%%]

		// Add the list ordering clause.
		if (is_object($params))
		{
			$initial_sort = $params->get('field_initial_sort');
		}
		else
		{
			$initial_sort = '';
		}
		// Fall back to old style if the parameter hasn't been set yet.
		if (empty($initial_sort) OR $this->getState('list.ordering') != '')
		{
			$order_col	= '';
			$order_dirn	= $this->state->get('list.direction');

		
		[%%IF GENERATE_CATEGORIES%%]		
			if ($this->state->get('list.ordering') == 'category_title')
			{
			[%%IF INCLUDE_ORDERING%%]
				$order_col = $db->quoteName('c.title').' '.$order_dirn.', '.$db->quoteName('a.ordering');
			[%%ELSE INCLUDE_ORDERING%%]
				$order_col = $db->quoteName('c.title').' '.$order_dirn;
			[%%ENDIF INCLUDE_ORDERING%%]
			}		
		[%%ENDIF GENERATE_CATEGORIES%%]
		[%%FOREACH FILTER_FIELD%%]		
			[%%IF FIELD_FILTER_LINK%%]
			if ($this->state->get('list.ordering') == '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]')
			{
				[%%IF INCLUDE_ORDERING%%]
				$order_col = $db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]').' '.$order_dirn.', '.$db->quoteName('a.ordering').' '.$order_dirn;
				[%%ELSE INCLUDE_ORDERING%%]
				$order_col = $db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]').' '.$order_dirn;
				[%%ENDIF INCLUDE_ORDERING%%]
			}
			[%%ELSE FIELD_FILTER_LINK%%]
			if ($this->state->get('list.ordering') == '[%%FIELD_CODE_NAME%%]')
			{
				[%%IF INCLUDE_ORDERING%%]
				$order_col = $db->quoteName('[%%FIELD_CODE_NAME%%]').' '.$order_dirn.', '.$db->quoteName('a.ordering').' '.$order_dirn;
				[%%ELSE INCLUDE_ORDERING%%]
				$order_col = $db->quoteName('[%%FIELD_CODE_NAME%%]').' '.$order_dirn;
				[%%ENDIF INCLUDE_ORDERING%%]			
			}						
			[%%ENDIF FIELD_FILTER_LINK%%]		
		[%%ENDFOR FILTER_FIELD%%]


		[%%IF INCLUDE_ORDERING%%]
			if ($this->state->get('list.ordering') == 'a.ordering' OR $this->state->get('list.ordering') == 'ordering')
			{
				$order_col	= '';
			[%%FOREACH ORDER_FIELD%%]
				[%%IF FIELD_LINK%%]
				$order_col	.= $db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_ORDERING_FIELD%%]').' '.$order_dirn.', ';
				[%%ENDIF FIELD_LINK%%]			
			[%%ENDFOR ORDER_FIELD%%]
				$order_col	.= $db->quoteName('a.ordering').' '.$order_dirn;		
			}

			if ($order_col == '')
			{
				$order_col = is_string($this->getState('list.ordering')) ? $this->getState('list.ordering') : 'a.ordering';
				$order_col .= ' '.$order_dirn;
			}
		[%%ELSE INCLUDE_ORDERING%%]
			[%%IF INCLUDE_NAME%%]
			if ($order_col == '')
			{
				$order_col = is_string($this->getState('list.ordering')) ? $this->getState('list.ordering') : 'a.name';
				$order_col .= ' '.$order_dirn;
			}
			[%%ELSE INCLUDE_NAME%%]
			if ($order_col == '')
			{
				$order_col = is_string($this->getState('list.ordering')) ? $this->getState('list.ordering') : 'a.id';
				$order_col .= ' '.$order_dirn;
			}
			[%%ENDIF INCLUDE_NAME%%]
		[%%ENDIF INCLUDE_ORDERING%%]		
			$query->order($db->escape($order_col));			
					
		}
		else
		{
			$query->order($db->quoteName('a.'.$initial_sort).' '.$db->escape($this->getState('list.direction', 'ASC')));
			
		}	
		return $query;
	}

	/**
	 * Method to get a list of [%%compobject_plural_name%%].
	 *
	 * Overriden to inject convert the params field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * 
	 */
	public function getItems()
	{

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user	= JFactory::getUser();
		$user_id	= $user->get('id');
		$guest	= $user->get('guest');
		[%%IF INCLUDE_ACCESS%%]
		$groups	= $user->getAuthorisedViewLevels();
		[%%ENDIF INCLUDE_ACCESS%%]

		// Get the global params
		$global_params = JComponentHelper::getParams('[%%com_architectcomp%%]', true);

		if ($items = parent::getItems())
		{
			// Convert the parameter fields into objects.
			foreach ($items as &$item)
			{
				$query->clear();
				$[%%compobject_code_name%%]_params = new JRegistry;
				[%%IF INCLUDE_PARAMS_RECORD%%]
				$[%%compobject_code_name%%]_params->loadString($item->params);
				[%%ENDIF INCLUDE_PARAMS_RECORD%%]
				[%%IF INCLUDE_URLS%%]
				// Convert the urls field to an array.
				$registry = new JRegistry;
				$registry->loadString($item->urls);
				$item->urls = $registry->toArray();
				$registry = null; //release memory	
				[%%ENDIF INCLUDE_URLS%%]
				
				[%%FOREACH OBJECT_FIELD%%]
					[%%IF FIELD_CHECKBOXES%%]
				if ($item->[%%FIELD_CODE_NAME%%] !='')
				{
					$item->[%%FIELD_CODE_NAME%%] = explode(',',$item->[%%FIELD_CODE_NAME%%]);
				}	
					[%%ENDIF FIELD_CHECKBOXES%%]
					[%%IF FIELD_SQL%%]
				if (isset($item->[%%FIELD_CODE_NAME%%]) AND $item->[%%FIELD_CODE_NAME%%] !='')
				{
					$sql = 'SELECT '.$db->quoteName('list.[%%FIELD_SQL_KEY_FIELD%%]').' AS id, '.$db->quoteName('list.[%%FIELD_SQL_VALUE_FIELD%%]').' AS value FROM ([%%FIELD_SQL_QUERY%%]) AS list';
					$sql .= ' WHERE '.$db->quoteName('list.[%%FIELD_SQL_KEY_FIELD%%]').' IN ('.JString::trim($item->[%%FIELD_CODE_NAME%%], ',').');';

					$db->setQuery($sql);
					
					$rows = $db->loadAssocList();
					$result_array = array();
					foreach ($rows as $row)
					{
						$result_array[] = $row;
					}					
					$item->[%%FIELD_CODE_NAME%%] = $result_array;
				}
				else
				{
					$item->[%%FIELD_CODE_NAME%%] = array();
				}
					[%%ENDIF FIELD_SQL%%]
					[%%IF FIELD_MULTIPLE%%]
						[%%IF FIELD_NOT_CHECKBOXES%%]
							[%%IF FIELD_NOT_SQL%%]
								[%%IF FIELD_NOT_TAG%%]
			if (isset($item->[%%FIELD_CODE_NAME%%]) AND $item->[%%FIELD_CODE_NAME%%] !='')
			{
				$item->[%%FIELD_CODE_NAME%%] =	explode(',',$item->[%%FIELD_CODE_NAME%%]);
			}
								[%%ENDIF FIELD_NOT_TAG%%]
							[%%ENDIF FIELD_NOT_SQL%%]
						[%%ENDIF FIELD_NOT_CHECKBOXES%%]
					[%%ENDIF FIELD_MULTIPLE%%]
				[%%ENDFOR OBJECT_FIELD%%]
		
				[%%FOREACH REGISTRY_FIELD%%]
				$registry = new JRegistry;
				$registry->loadString($item->[%%FIELD_CODE_NAME%%]);
				$item->[%%FIELD_CODE_NAME%%] = $registry->toArray();
				$registry = null; //release memory	
		
				// Check and reformat entries in the json array
				$field_array = $item->[%%FIELD_CODE_NAME%%];
				
					[%%FOREACH REGISTRY_ENTRY%%]
						[%%IF FIELD_ACCESSLEVEL%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$query->clear();
					// Get the title for the access level.
					$query->select('[%%FIELD_CODE_NAME%%].title AS [%%FIELD_CODE_NAME%%]_title');
					$query->from($db->quoteName('#__viewlevels').' AS [%%FIELD_CODE_NAME%%]');
					$query->where($db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quote($field_array['[%%FIELD_CODE_NAME%%]']));

					$db->setQuery($query);

					$result = $db->loadResult();
					$field_array['[%%FIELD_CODE_NAME%%]_title'] = $result ? $result : '';
				}
				else
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = '';
					$field_array['[%%FIELD_CODE_NAME%%]_title'] = '';
				}
						[%%ENDIF FIELD_ACCESSLEVEL%%]
						[%%IF FIELD_CATEGORY%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$query->clear();
					// Get the title for the category
					$query->select('[%%FIELD_CODE_NAME%%].title AS [%%FIELD_CODE_NAME%%]_title');
					$query->from($db->quoteName('#__categories').' AS [%%FIELD_CODE_NAME%%]');
					$query->where($db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quote($field_array['[%%FIELD_CODE_NAME%%]']));

					$db->setQuery($query);

					$result = $db->loadResult();
					$field_array['[%%FIELD_CODE_NAME%%]_title'] = $result ? $result : '';
				}
				else
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = '';
					$field_array['[%%FIELD_CODE_NAME%%]_title'] = '';
				}
						[%%ENDIF FIELD_CATEGORY%%]
						[%%IF FIELD_USER%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$query->clear();
					// Get the name for the user
					$query->select('[%%FIELD_CODE_NAME%%].name AS [%%FIELD_CODE_NAME%%]_name');
					$query->from($db->quoteName('#__users').' AS [%%FIELD_CODE_NAME%%]');
					$query->where($db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quote($field_array['[%%FIELD_CODE_NAME%%]']));

					$db->setQuery($query);

					$result = $db->loadResult();
					$field_array['[%%FIELD_CODE_NAME%%]_name'] = $result ? $result : '';
				}
				else
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = '';
					$field_array['[%%FIELD_CODE_NAME%%]_name'] = '';
				}
						[%%ENDIF FIELD_USER%%]		
						[%%IF FIELD_USERGROUP%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$query->clear();
					// Get the title for the user group
					$query->select('[%%FIELD_CODE_NAME%%].title AS [%%FIELD_CODE_NAME%%]_title');
					$query->from($db->quoteName('#__usergroups').' AS [%%FIELD_CODE_NAME%%]');
					$query->where($db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quote($field_array['[%%FIELD_CODE_NAME%%]']));

					$db->setQuery($query);

					$result = $db->loadResult();
					$field_array['[%%FIELD_CODE_NAME%%]_title'] = $result ? $result : '';
				}
				else
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = '';
					$field_array['[%%FIELD_CODE_NAME%%]_title'] = '';
				}
						[%%ENDIF FIELD_USERGROUP%%]
						[%%IF FIELD_MODAL%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$query->clear();
					// Get the name for the [%%FIELD_FOREIGN_OBJECT%%]
					$query->select('[%%FIELD_CODE_NAME%%].name AS [%%FIELD_CODE_NAME%%]_name');
					$query->from($db->quoteName('#__[%%architectcomp%%]_[%%FIELD_FOREIGN_OBJECT_PLURAL%%]').' AS [%%FIELD_CODE_NAME%%]');
					$query->where($db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quote($field_array['[%%FIELD_CODE_NAME%%]']));

					$db->setQuery($query);

					$result = $db->loadResult();
					$field_array['[%%FIELD_CODE_NAME%%]_name'] = $result ? $result : '';
				}
				else
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = '';
					$field_array['[%%FIELD_CODE_NAME%%]_name'] = '';
				}
						[%%ENDIF FIELD_MODAL%%]						
						[%%IF FIELD_CHECKBOXES%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = explode(',',$field_array['[%%FIELD_CODE_NAME%%]']);
				}	
						[%%ENDIF FIELD_CHECKBOXES%%]
						[%%IF FIELD_SQL%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$sql = 'SELECT '.$db->quoteName('list.[%%FIELD_SQL_KEY_FIELD%%]').' AS id, '.$db->quoteName('list.[%%FIELD_SQL_VALUE_FIELD%%]').' AS value FROM ([%%FIELD_SQL_QUERY%%]) AS list';
					$sql .= ' WHERE '.$db->quoteName('list.[%%FIELD_SQL_KEY_FIELD%%]').' IN ('.JString::trim($field_array['[%%FIELD_CODE_NAME%%]'], ',').');';

					$db->setQuery($sql);
					
					$rows = $db->loadAssocList();
					$result_array = array();
					foreach ($rows as $row)
					{
						$result_array[] = $row;
					}					
					$field_array['[%%FIELD_CODE_NAME%%]'] = $result_array;
				}
				else
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = array();
				}						
						[%%ENDIF FIELD_SQL%%]
						[%%IF FIELD_MULTIPLE%%]
							[%%IF FIELD_NOT_CHECKBOXES%%]
								[%%IF FIELD_NOT_SQL%%]
									[%%IF FIELD_NOT_TAG%%]
				if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND $field_array['[%%FIELD_CODE_NAME%%]'] !='')
				{
					$field_array['[%%FIELD_CODE_NAME%%]'] = explode(',',$field_array['[%%FIELD_CODE_NAME%%]']);
				}
									[%%ENDIF FIELD_NOT_TAG%%]
								[%%ENDIF FIELD_NOT_SQL%%]
							[%%ENDIF FIELD_NOT_CHECKBOXES%%]
						[%%ENDIF FIELD_MULTIPLE%%]
					[%%ENDFOR REGISTRY_ENTRY%%]
						
				$item->[%%FIELD_CODE_NAME%%] = $field_array;					
				[%%ENDFOR REGISTRY_FIELD%%]
				[%%IF INCLUDE_DESCRIPTION%%]
					[%%IF INCLUDE_INTRO%%]
				$item->introdescription = trim($item->intro) != '' ? $item->intro . $item->description : $item->description;
					[%%ENDIF INCLUDE_INTRO%%]
				[%%ENDIF INCLUDE_DESCRIPTION%%]

				
				[%%IF INCLUDE_PARAMS_RECORD%%]
				// Unpack readmore and layout params
				$item->[%%compobject%%]_alternative_readmore = $[%%compobject_code_name%%]_params->get('[%%compobject%%]_alternative_readmore');
				$item->layout = $[%%compobject_code_name%%]_params->get('layout');
				[%%ENDIF INCLUDE_PARAMS_RECORD%%]
							
				if (!is_object($this->getState('params')))
				{
					$item->params = $[%%compobject_code_name%%]_params;
				}
				else
				{
					$item->params = clone $this->getState('params');

					// [%%CompObject%%] params override menu item params only if menu param = 'use_[%%compobject%%]'
					// Otherwise, menu item params control the layout
					// If menu item is 'use_[%%compobject%%]' and there is no [%%compobject%%] param, use global

					// create an array of just the params set to 'use_[%%compobject%%]'
					$menu_params_array = $this->getState('params')->toArray();
					$[%%compobject_code_name%%]_array = array();

					foreach ($menu_params_array as $key => $value)
					{
						if ($value === 'use_[%%compobject%%]')
						{
							// if the [%%compobject%%] has a value, use it
							if ($[%%compobject_code_name%%]_params->get($key) != '')
							{
								// get the value from the [%%compobject%%]
								$[%%compobject_code_name%%]_array[$key] = $[%%compobject_code_name%%]_params->get($key);
							}
							else
							{
								// otherwise, use the global value
								$[%%compobject_code_name%%]_array[$key] = $global_params->get($key);
							}
						}
					}

					// merge the selected [%%compobject%%] params
					if (count($[%%compobject_code_name%%]_array) > 0)
					{
						$[%%compobject_code_name%%]_params = new JRegistry;
						$[%%compobject_code_name%%]_params->loadArray($[%%compobject_code_name%%]_array);
						$item->params->merge($[%%compobject_code_name%%]_params);
					}


					// get display date
					switch ($item->params->get('list_show_[%%compobject%%]_date'))
					{
						[%%IF INCLUDE_MODIFIED%%]
						case 'modified':
							$item->display_date = $item->modified;
							break;
						[%%ENDIF INCLUDE_MODIFIED%%]
						[%%IF INCLUDE_PUBLISHED_DATES%%]
						case 'publish_up':
							$item->display_date = $item->publish_up;
							[%%IF INCLUDE_CREATED%%]
							if ($item->publish_up == 0)
							{
								$item->display_date = $item->created;
							}
							[%%ENDIF INCLUDE_CREATED%%]
							break;
						[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
						[%%IF INCLUDE_CREATED%%]
						case 'created':
							$item->display_date = $item->created;
							break;
						[%%ENDIF INCLUDE_CREATED%%]
						default:
							$item->display_date = 0;
							break;
					}
				}
				[%%IF INCLUDE_ASSETACL%%]
				// Compute the asset access permissions.
				// Technically guest could edit an [%%compobject%%], but lets not check that to improve performance a little.
				if (!$guest) 
				{
					[%%IF INCLUDE_ASSETACL_RECORD%%]
					$asset		= '[%%com_architectcomp%%].[%%compobject%%].'.$item->id;
					[%%ELSE INCLUDE_ASSETACL_RECORD%%]
					$asset		= '[%%com_architectcomp%%]';
					[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
					
					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset))
					{
						$item->params->set('access-edit', true);
					}
					[%%IF INCLUDE_CREATED%%]
					// Now check if edit.own is available.
					else
					{ 
						if (!empty($user_id) AND $user->authorise('core.edit.own', $asset)) 
						{
							// Check for a valid user and that they are the owner.
							if ($user_id == $item->created_by) 
							{
								$item->params->set('access-edit', true);
							}
						}
					}
					[%%ENDIF INCLUDE_CREATED%%]
					if ($user->authorise('core.create', $asset))
					{
						$item->params->set('access-create', true);
					}	
						
					
					if ($user->authorise('core.delete', $asset)) 
					{
						$item->params->set('access-delete', true);
					}
					[%%IF INCLUDE_CREATED%%]
					// Now check if delete.own is available.
					else
					{ 
						if (!empty($user_id) AND $user->authorise('core.delete.own', $asset)) 
						{
							// Check for a valid user and that they are the owner.
							if ($user_id == $item->created_by) 
							{
								$item->params->set('access-delete', true);
							}
						}
					}
					[%%ENDIF INCLUDE_CREATED%%]								
				}
				[%%ENDIF INCLUDE_ASSETACL%%]

				[%%IF INCLUDE_ACCESS%%]
				$access = $this->getState('filter.access');

				if ($access) 
				{
					// If the access filter has been set, we already have only the [%%compobjectplural%%] this user can view.
					$item->params->set('access-view', true);
				}
				else 
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					[%%IF GENERATE_CATEGORIES%%]	
					if ($item->catid != 0  AND $item->category_access !== null)
					{ 
						$item->params->set('access-view', in_array($item->access, $groups) AND in_array($item->category_access, $groups));
					}
					else 
					{
						$item->params->set('access-view', in_array($item->access, $groups));
					}							
					[%%ELSE GENERATE_CATEGORIES%%]
					$item->params->set('access-view', in_array($item->access, $groups));
					[%%ENDIF GENERATE_CATEGORIES%%]									
					
				}
			[%%ENDIF INCLUDE_ACCESS%%]
			}
		}

		return $items;
	}
	[%%FOREACH FILTER_FIELD%%]
		[%%IF FIELD_FILTER_LINK%%]
	/**
	 * Build a list of [%%FIELD_FOREIGN_OBJECT_PLURAL%%]
	 *
	 * @return	JDatabaseQuery
	 */
	public function get[%%FIELD_FOREIGN_OBJECT_PLURAL_UCFIRST%%]()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select($db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].id').' AS value, '.$db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]').' AS text');
		$query->from($db->quoteName('#__[%%architectcomp%%]_[%%FIELD_FOREIGN_OBJECT_PLURAL%%]').' AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%]');
		$query->join('INNER', $db->quoteName('#__[%%architectcomp%%]_[%%compobjectplural%%]').' AS a ON '.$db->quoteName('a.[%%FIELD_CODE_NAME%%]').' = '.$db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].id'));
		$query->group($db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].id').', '.$db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]'));
		$query->order($db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]'));

		// Setup the query
		$db->setQuery($query->__toString());

		// Return the result
		return $db->loadObjectList();
	}
		[%%ELSE FIELD_FILTER_LINK%%]
	/**
	 * Build a list of distinct values in the [%%FIELD_NAME%%] field
	 *
	 * @return	JDatabaseQuery
	 */
	public function get[%%FIELD_CODE_NAME_UCFIRST%%]values()
	{
		[%%FIELD_FILTER_VALUE_ARRAY%%]
	}				
		[%%ENDIF FIELD_FILTER_LINK%%]
	[%%ENDFOR FILTER_FIELD%%]	
}
