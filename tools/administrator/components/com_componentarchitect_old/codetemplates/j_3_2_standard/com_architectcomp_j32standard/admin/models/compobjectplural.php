<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].admin
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @version			$Id: compobjectplural.php 422 2014-10-23 14:08:07Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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
 * Methods supporting a list of [%%compobject%%] records.
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
	 * @param	array	$config	An optional associative array of configuration settings.
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
				[%%ENDIF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
				'alias', 'a.alias',
				[%%ENDIF INCLUDE_ALIAS%%]								
				[%%FOREACH FILTER_FIELD%%]
				'[%%FIELD_CODE_NAME%%]', 'a.[%%FIELD_CODE_NAME%%]',
					[%%IF FIELD_FILTER_LINK%%]
				'[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]', '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]',				
					[%%ENDIF FIELD_FILTER_LINK%%]
				[%%ENDFOR FILTER_FIELD%%]
				[%%IF INCLUDE_CHECKOUT%%]
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				[%%ENDIF INCLUDE_CHECKOUT%%]
				[%%IF GENERATE_CATEGORIES%%]
				'catid', 'a.catid', 'category_title', 'category_id',
				[%%ENDIF GENERATE_CATEGORIES%%]				
				[%%IF INCLUDE_STATUS%%]
				'state', 'a.state',
				[%%ENDIF INCLUDE_STATUS%%]
				[%%IF INCLUDE_ACCESS%%]
				'access', 'a.access', 'access_level',
				[%%ENDIF INCLUDE_ACCESS%%]
				[%%IF INCLUDE_CREATED%%]
				'created', 'a.created',
				'created_by', 'a.created_by',
				[%%ENDIF INCLUDE_CREATED%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
				'publish_up', 'a.publish_up',				
				'publish_down', 'a.publish_down',	
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]			
				[%%IF INCLUDE_FEATURED%%]
				'featured', 'a.featured',
				[%%ENDIF INCLUDE_FEATURED%%]
				[%%IF INCLUDE_LANGUAGE%%]
				'language', 'a.language', 'l.title',
				[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_HITS%%]
				'hits', 'a.hits',
				[%%ENDIF INCLUDE_HITS%%]				
				[%%IF INCLUDE_ORDERING%%]
				'ordering', 'a.ordering',				
				[%%ENDIF INCLUDE_ORDERING%%]
				[%%IF INCLUDE_TAGS%%]
				'tag',
				[%%ENDIF INCLUDE_TAGS%%]
			);

			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}			
		}


		parent::__construct($config);
	}
	/**
	 * Returns a reference to a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * 
	 */
	public function getTable($type = '[%%CompObjectPlural%%]', $prefix = '[%%ArchitectComp%%]Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}	
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		
		$app = JFactory::getApplication('administrator');
		// Adjust the context to support modal layouts.
		if ($layout = $app->input->getString('layout'))
		{
			$this->context .= '.'.$layout;
		}

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		[%%FOREACH FILTER_FIELD%%]
		$[%%FIELD_CODE_NAME%%] = $app->getUserStateFromRequest($this->context.'.filter.[%%FIELD_CODE_NAME%%]', 'filter_[%%FIELD_CODE_NAME%%]', [%%FIELD_FILTER_DEFAULT%%], '[%%FIELD_PHP_VARIABLE_TYPE%%]');
		$this->setState('filter.[%%FIELD_CODE_NAME%%]', $[%%FIELD_CODE_NAME%%]);
		[%%ENDFOR FILTER_FIELD%%]
		[%%IF GENERATE_CATEGORIES%%]
		$category_id = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $category_id);		
		[%%ENDIF GENERATE_CATEGORIES%%]
		
		[%%IF INCLUDE_STATUS%%]
		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);
		[%%ENDIF INCLUDE_STATUS%%]
	
		[%%IF INCLUDE_ACCESS%%]
		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
		[%%ENDIF INCLUDE_ACCESS%%]

		[%%IF INCLUDE_LANGUAGE%%]
		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);	

		[%%IF INCLUDE_TAGS%%]
		$tag = $app->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);
		[%%ENDIF INCLUDE_TAGS%%]
		
		// Load the parameters.
		$params = JComponentHelper::getParams('[%%com_architectcomp%%]');
		$this->setState('params', $params);
		
		// List state information.
		[%%IF INCLUDE_NAME%%]
			parent::populateState('a.name', 'asc');		
		[%%ELSE INCLUDE_NAME%%]
			[%%IF INCLUDE_ORDERING%%]
			parent::populateState('a.ordering', 'asc');
			[%%ELSE INCLUDE_ORDERING%%]
			parent::populateState('a.id', 'desc');
			[%%ENDIF INCLUDE_ORDERING%%]
		[%%ENDIF INCLUDE_NAME%%]
		
		// force a language
		$forcedLanguage = $app->input->get('forcedLanguage');
		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}		
		[%%ENDIF INCLUDE_LANGUAGE%%]

	}
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * 
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		[%%IF GENERATE_CATEGORIES%%]		
		$id	.= ':'.$this->getState('filter.category_id');
		[%%ENDIF GENERATE_CATEGORIES%%]					
		[%%IF INCLUDE_STATUS%%]
		$id	.= ':'.$this->getState('filter.state');
		[%%ENDIF INCLUDE_STATUS%%]
		[%%IF INCLUDE_CREATED%%]
		$id	.= ':'.$this->getState('filter.created_by');	
		[%%ENDIF INCLUDE_CREATED%%]	
		[%%IF INCLUDE_ACCESS%%]
		$id	.= ':'.$this->getState('filter.access');
		[%%ENDIF INCLUDE_ACCESS%%]
		[%%IF INCLUDE_LANGUAGE%%]
		$id	.= ':'.$this->getState('filter.language');
		[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%FOREACH FILTER_FIELD%%]
		$id	.= ':'.$this->getState('filter.[%%FIELD_CODE_NAME%%]');	
		[%%ENDFOR FILTER_FIELD%%]	
		return parent::getStoreId($id);
	}	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 *
	 */
	protected function getListQuery()
	{
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$app	= JFactory::getApplication();

		// Select the required [%%compobject_short_plural_name%%] from the table.
		$query->select(
			$this->getState(
					'list.select',
					'a.*'
			)
		);
		$query->from($db->quoteName('#__[%%architectcomp%%]_[%%compobjectplural%%]').' AS a');

		[%%IF INCLUDE_LANGUAGE%%]
		// Join over the language
		$query->select($db->quoteName('l.title').' AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON '.$db->quoteName('l.lang_code').' = '.$db->quoteName('a.language'));
		[%%ENDIF INCLUDE_LANGUAGE%%]
		
		[%%IF INCLUDE_CHECKOUT%%]
		// Join over the users for the checked out user.
		$query->select($db->quoteName('uc.name').' AS editor');
		$query->join('LEFT', $db->quoteName('#__users').' AS uc ON '.$db->quoteName('uc.id').' = '.$db->quoteName('a.checked_out'));
		[%%ENDIF INCLUDE_CHECKOUT%%]
		
		[%%IF INCLUDE_CREATED%%]
		// Join over the users for the creator.
		$query->select('ua.name AS created_by_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');		
		[%%ENDIF INCLUDE_CREATED%%]		
		
		[%%IF INCLUDE_ACCESS%%]
		// Join over the access levels.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', $db->quoteName('#__viewlevels').' AS ag ON '.$db->quoteName('ag.id').' = '.$db->quoteName('a.access'));
		[%%ENDIF INCLUDE_ACCESS%%]	
		[%%IF GENERATE_CATEGORIES%%]		
		// Join over the categories.
		$query->select($db->quoteName('c.title').' AS category_title');
		$query->join('LEFT', $db->quoteName('#__categories').' AS c ON '.$db->quoteName('c.id').' = '.$db->quoteName('a.catid'));			
		[%%ENDIF GENERATE_CATEGORIES%%]
		// Filter by search in name
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where($db->quoteName('a.id').' = '.(int) JString::substr($search, 3));
			}
			else
			{
				$search = $db->quote('%'.$db->escape(JString::trim($search), true).'%', false);
				[%%IF INCLUDE_NAME%%]
				$where = $db->quoteName('a.name').' LIKE '.$search;
					[%%IF INCLUDE_ALIAS%%]
				$where .= ' OR '.$db->quoteName('a.alias').' LIKE '.$search;
					[%%ENDIF INCLUDE_ALIAS%%]
				[%%ENDIF INCLUDE_NAME%%]				
				$query->where('('.$where.')');
			}
		}
		[%%IF INCLUDE_LANGUAGE%%]
		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where($db->quoteName('a.language').' = '.$db->quote($language));
		}	
		
		// Join over the associations.
		$assoc = JLanguageAssociations::isEnabled();
		if ($assoc)
		{
			$query->select('COUNT('.$db->quoteName('as2.id').') > 1 AS association');
			$query->join('LEFT', $db->quoteName('#__associations').' AS as1 ON '.$db->quoteName('as1.id').' = '.$db->quoteName('a.id').' AND '.$db->quoteName('as1.context').' = '.$db->quote('[%%com_architectcomp%%].[%%compobject%%].item'));
			$query->join('LEFT', $db->quoteName('#__associations').' AS as2 ON '.$db->quoteName('as2.key').' = '.$db->quoteName('as1.key'));
			$query->group($db->quoteName('a.id'));
		}		
		[%%ENDIF INCLUDE_LANGUAGE%%]


		[%%IF INCLUDE_TAGS%%]
		// Filter by a single tag.
		$tag_id = $this->getState('filter.tag');
		if (is_numeric($tag_id))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tag_id);
			$query->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('[%%com_architectcomp%%].[%%compobject%%]')
				);
		}
		[%%ENDIF INCLUDE_TAGS%%]
		
		[%%IF INCLUDE_STATUS%%]
		// Filter by state e.g. published
		$state = $this->getState('filter.state');
		if (is_numeric($state))
		{
			$query->where($db->quoteName('a.state').' = '.(int) $state);
		}
		else
		{
			if ($state === '')
			{
				$query->where('('.$db->quoteName('a.state').' IN (0, 1))');
			}
		}
			
		[%%ENDIF INCLUDE_STATUS%%]
		
		[%%IF INCLUDE_ACCESS%%]
		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where($db->quoteName('a.access').' = ' . (int) $access);
		}
		[%%ENDIF INCLUDE_ACCESS%%]	
		
		[%%FOREACH OBJECT_FIELD%%]
			[%%IF FIELD_ACCESSLEVEL%%]
		// Join over the access levels.
		$query->select('[%%FIELD_CODE_NAME%%].title AS [%%FIELD_CODE_NAME%%]_title');
		$query->join('LEFT', $db->quoteName('#__viewlevels').' AS [%%FIELD_CODE_NAME%%] ON '.$db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quoteName('a.[%%FIELD_CODE_NAME%%]'));
			[%%ENDIF FIELD_ACCESSLEVEL%%]
			[%%IF FIELD_CATEGORY%%]
		// Join over the categories.
		$query->select($db->quoteName('[%%FIELD_CODE_NAME%%].title').' AS [%%FIELD_CODE_NAME%%]_title');
		$query->join('LEFT', $db->quoteName('#__categories').' AS [%%FIELD_CODE_NAME%%] ON '.$db->quoteName('[%%FIELD_CODE_NAME%%].id').' = '.$db->quoteName('a.[%%FIELD_CODE_NAME%%]'));			
			[%%ENDIF FIELD_CATEGORY%%]	
			[%%IF FIELD_USER%%]
		// Join over the users.
		$query->select('[%%FIELD_CODE_NAME%%].name AS [%%FIELD_CODE_NAME%%]_name');
		$query->join('LEFT', '#__users AS [%%FIELD_CODE_NAME%%] ON [%%FIELD_CODE_NAME%%].id = a.[%%FIELD_CODE_NAME%%]');		
			[%%ENDIF FIELD_USER%%]		
			[%%IF FIELD_USERGROUP%%]
		// Join over the usergroups.
		$query->select('[%%FIELD_CODE_NAME%%].title AS [%%FIELD_CODE_NAME%%]_title');
		$query->join('LEFT', '#__usergroups AS [%%FIELD_CODE_NAME%%] ON [%%FIELD_CODE_NAME%%].id = a.[%%FIELD_CODE_NAME%%]');		
			[%%ENDIF FIELD_USERGROUP%%]					
			[%%IF FIELD_LINK%%]
		// Filter by and return name for [%%FIELD_CODE_NAME%%] level.
		$query->select($db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]').' AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]');
		$query->select($db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_ORDERING_FIELD%%]').' AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_ORDERING_FIELD%%]');

		$query->join('LEFT', '#__[%%architectcomp%%]_[%%FIELD_FOREIGN_OBJECT_PLURAL%%] AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%] ON [%%FIELD_FOREIGN_OBJECT_ACRONYM%%].id = a.[%%FIELD_CODE_NAME%%]');	
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
				
		[%%IF GENERATE_CATEGORIES%%]			
		// Filter by a single or group of categories.
		$baselevel = 1;
		$category_id = $this->getState('filter.category_id');
		if (is_numeric($category_id))
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($category_id);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where($db->quoteName('c.lft').' >= '.(int) $lft);
			$query->where($db->quoteName('c.rgt').' <= '.(int) $rgt);
		}
		else 
		{
			if (is_array($category_id))
			{
				JArrayHelper::toInteger($category_id);
				$category_id = implode(',', $category_id);
				$query->where($db->quoteName('a.catid').' IN ('.$category_id.')');
			}
		}
		[%%ENDIF GENERATE_CATEGORIES%%]
		// Add the list ordering clause.
		$order_col	= '';
		$order_dirn	= $this->getState('list.direction');

		
		[%%IF GENERATE_CATEGORIES%%]		
		if ($this->getState('list.ordering') == 'category_title')
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
		if ($this->getState('list.ordering') == '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]')
		{
				[%%IF INCLUDE_ORDERING%%]
			$order_col = $db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]').' '.$order_dirn.', '.$db->quoteName('a.ordering').' '.$order_dirn;
				[%%ELSE INCLUDE_ORDERING%%]
			$order_col = $db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]').' '.$order_dirn;
				[%%ENDIF INCLUDE_ORDERING%%]
		}
			[%%ELSE FIELD_FILTER_LINK%%]
		if ($this->getState('list.ordering') == '[%%FIELD_CODE_NAME%%]')
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
		if ($this->getState('list.ordering') == 'a.ordering' OR $this->getState('list.ordering') == 'ordering')
		{
			$order_col	= '';
			[%%FOREACH ORDER_FIELD%%]
				[%%IF FIELD_LINK%%]
			$order_col	.= $db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_ORDERING_FIELD%%]').' '.$order_dirn.', ';
				[%%ENDIF FIELD_LINK%%]			
			[%%ENDFOR ORDER_FIELD%%]
			$order_col	.= $db->quoteName('a.ordering').' '.$order_dirn;		
			
		}
		[%%ENDIF INCLUDE_ORDERING%%]	
		
		if ($order_col == '' AND $this->getState('list.ordering') != '')
		{
			$order_col	=  $db->quoteName($this->getState('list.ordering')).' '.$order_dirn;
		}
		[%%IF INCLUDE_ACCESS%%]
			[%%IF INCLUDE_LANGUAGE%%]
		else
		{
			//sqlsrv change
			if ($order_col == 'language' OR $order_col == 'a.language')
			{
				$order_col = $db->quoteName('l.title').' '.$order_dirn;	
			}
			else
			{
				if ($order_col == 'access_level' OR $order_col =='access' OR $order_col =='a.access')
				{
					$order_col = $db->quoteName('ag.title').' '.$order_dirn;
				}
			}		
		}
			[%%ELSE INCLUDE_LANGUAGE%%]
		else
		{
			//sqlsrv change
			if ($order_col == 'access_level' OR $order_col =='a.access_level')
			{
				$order_col = $db->quoteName('ag.title').' '.$order_dirn;
			}
		}
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ELSE INCLUDE_ACCESS%%]	
			[%%IF INCLUDE_LANGUAGE%%]
		else
		{
			//sqlsrv change
			if ($order_col == 'language' OR $order_col == 'a.language')
			{
				$order_col = $db->quoteName('l.title').' '.$order_dirn;			
			}
		}
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ENDIF INCLUDE_ACCESS%%]
		// If order column still blank then set it to default order
		[%%IF INCLUDE_ORDERING%%]
		if ($order_col == '')
		{
			$order_col =  $db->quoteName('a.ordering').' '.$order_dirn;
		}
		[%%ELSE INCLUDE_ORDERING%%]
			[%%IF INCLUDE_NAME%%]
		if ($order_col == '')
		{
			$order_col =  $db->quoteName('a.name').' '.$order_dirn;
		}
			[%%ELSE INCLUDE_NAME%%]
		if ($order_col == '')
		{
			$order_col =  $db->quoteName('a.id').' '.$order_dirn;
		}
			[%%ENDIF INCLUDE_NAME%%]
		[%%ENDIF INCLUDE_ORDERING%%]
			
		$query->order($db->escape($order_col));

		return $query;
	}
	/**
	 * Method to get a set of records.
	 *
	 *
	 * @return	mixed	Objects on success, false on failure.
	 */
	public function getItems()
	{
		$db		= $this->getDbo();

		if ($items = parent::getItems())
		{
			// Include any manipulation of the data on the record e.g. expand out Registry fields
			// NB The params registry field - if used - is done automatcially in the JAdminModel parent class
			foreach ($items as $item)
			{
				[%%FOREACH FILTER_FIELD%%]
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
					[%%IF FIELD_TAG%%]
					
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
					[%%ENDIF FIELD_TAG%%]					
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
				[%%ENDFOR FILTER_FIELD%%]
				
				[%%IF INCLUDE_IMAGE%%]
				// Convert the images field to an array.
				$registry = new JRegistry;
				$registry->loadString($item->images);
				$item->images = $registry->toArray();
				$registry = null; //release memory	
				[%%ENDIF INCLUDE_IMAGE%%]

				[%%IF INCLUDE_URLS%%]
				// Convert the urls field to an array.
				$registry = new JRegistry;
				$registry->loadString($item->urls);
				$item->urls = $registry->toArray();
				$registry = null; //release memory	
				[%%ENDIF INCLUDE_URLS%%]
							
				[%%FOREACH REGISTRY_FIELD%%]
				$registry = new JRegistry;
				$registry->loadString($item->[%%FIELD_CODE_NAME%%]);
				$item->[%%FIELD_CODE_NAME%%] = $registry->toArray();
				$registry = null; //release memory	
							
				[%%ENDFOR REGISTRY_FIELD%%]
			}
		}

		return $items;
	}
	[%%IF INCLUDE_CREATED%%]
	/**
	 * Build a list of created_by/authors
	 *
	 * @return  array List of creators
	 */
	public function getCreators()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('u.id AS value, u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__[%%architectcomp%%]_[%%compobjectplural%%] AS a ON a.created_by = u.id');
		$query->group('u.id, u.name');
		$query->order('u.name');

		// Setup the query
		$db->setQuery($query);

		// Return the result
		return $db->loadObjectList();
	}	
	[%%ENDIF INCLUDE_CREATED%%]		
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