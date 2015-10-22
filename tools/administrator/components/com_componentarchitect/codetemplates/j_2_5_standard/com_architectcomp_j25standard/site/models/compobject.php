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
 * @version			$Id: compobject.php 424 2014-10-23 14:08:27Z BrianWade $
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

jimport('joomla.application.component.modelitem');

/**
 * [%%ArchitectComp_name%%] Component [%%CompObject_name%%] Model
 *
 */
class [%%ArchitectComp%%]Model[%%CompObject%%] extends JModelItem
{
	/**
	 * Model context string.  Used in setting the store id for the session
	 *
	 * @var		string
	 */
	protected $context = '[%%com_architectcomp%%].[%%compobject%%]';

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * 
	 */
	public function __construct($config = array())
	{
		if (empty($config['[%%compobject%%]_filter_fields']))
		{
			$config['[%%compobject%%]_filter_fields'] = array(
				'id', 'a.id',
				[%%IF INCLUDE_NAME%%]
				'name', 'a.name',
					[%%IF INCLUDE_ALIAS%%]
				'alias', 'a.alias',
					[%%ENDIF INCLUDE_ALIAS%%]				
				[%%ENDIF INCLUDE_NAME%%]				
				[%%FOREACH FILTER_FIELD%%]
				'[%%FIELD_CODE_NAME%%]', 'a.[%%FIELD_CODE_NAME%%]',
					[%%IF FIELD_FILTER_LINK%%]
				'[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]', '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]',
					[%%ENDIF FIELD_FILTER_LINK%%]
				[%%ENDFOR FILTER_FIELD%%]				
				[%%FOREACH OBJECT_FIELD%%]
				'[%%FIELD_CODE_NAME%%]','a.[%%FIELD_CODE_NAME%%]',
				[%%ENDFOR OBJECT_FIELD%%]					
				[%%IF INCLUDE_CHECKOUT%%]
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				[%%ENDIF INCLUDE_CHECKOUT%%]
				[%%IF GENERATE_CATEGORIES%%]				
				'catid', 'a.catid', 'category_title',
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
				[%%IF INCLUDE_FEATURED%%]
				'featured', 'a.featured',
				[%%ENDIF INCLUDE_FEATURED%%]
				[%%IF INCLUDE_LANGUAGE%%]
				'language', 'a.language',
				[%%ENDIF INCLUDE_LANGUAGE%%]				
				[%%IF INCLUDE_HITS%%]
				'hits', 'a.hits',
				[%%ENDIF INCLUDE_HITS%%]
				[%%IF INCLUDE_ORDERING%%]
				'ordering', 'a.ordering',
				[%%ENDIF INCLUDE_ORDERING%%]				
				);
		}

		parent::__construct($config);
	}
	[%%IF INCLUDE_ASSETACL%%]	
	/**	
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	record	A record object.
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
	
		[%%IF INCLUDE_STATUS%%]	
		if ($record->state != -2)
		{
			return ;
		}
		[%%ENDIF INCLUDE_STATUS%%]
		if (!empty($record->id))
		{
		[%%IF INCLUDE_CREATED%%]
			[%%IF INCLUDE_ASSETACL_RECORD%%]
			return $user->authorise('core.delete', '[%%com_architectcomp%%].[%%compobject%%].'.(int) $record->id)
					  OR ($user->authorise('core.delete', '[%%com_architectcomp%%].[%%compobject%%].'.(int) $record->id)
					  AND $record->created_by = $user->id);
			[%%ELSE INCLUDE_ASSETACL_RECORD%%]
			return $user->authorise('core.delete', '[%%com_architectcomp%%]')
					  OR ($user->authorise('core.delete', '[%%com_architectcomp%%]')
					  AND $record->created_by = $user->id);
			[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
		[%%ELSE INCLUDE_CREATED%%]
			[%%IF INCLUDE_ASSETACL_RECORD%%]
			return $user->authorise('core.delete', '[%%com_architectcomp%%].[%%compobject%%].'.(int) $record->id);
			[%%ELSE INCLUDE_ASSETACL_RECORD%%]
			return $user->authorise('core.delete', '[%%com_architectcomp%%]');
			[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
		[%%ENDIF INCLUDE_CREATED%%]				
		}
		return ;
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		[%%IF INCLUDE_ASSETACL_RECORD%%]
		// Check against the id.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', '[%%com_architectcomp%%].[%%compobject%%].'.(int) $record->id);
		}
		else
		{
			[%%IF GENERATE_CATEGORIES%%] 		
			// New [%%compobject_name%%], so check against the category.		
			if (!empty($record->catid))
			{
				return $user->authorise('core.edit.state', '[%%com_architectcomp%%].category.'.(int) $record->catid);
			}
			else 
			{
			// Default to component settings.			
				return $user->authorise('core.edit.state', '[%%com_architectcomp%%]');
			}
			[%%ELSE GENERATE_CATEGORIES%%] 	
			// Default to component settings.			
			return $user->authorise('core.edit.state', '[%%com_architectcomp%%]');
			[%%ENDIF GENERATE_CATEGORIES%%]									
		}
		[%%ELSE INCLUDE_ASSETACL_RECORD%%]
			[%%IF GENERATE_CATEGORIES%%] 		
		// New [%%compobject_name%%], so check against the category.		
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', '[%%com_architectcomp%%].category.'.(int) $record->catid);
		}
		else 
		{
		// Default to component settings.			
			return $user->authorise('core.edit.state', '[%%com_architectcomp%%]');
		}
			[%%ELSE GENERATE_CATEGORIES%%] 	
		// Default to component settings.			
		return $user->authorise('core.edit.state', '[%%com_architectcomp%%]');
			[%%ENDIF GENERATE_CATEGORIES%%]	
		[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
	}
	[%%ENDIF INCLUDE_ASSETACL%%]	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('[%%compobject%%].id', $pk);

		$offset = JRequest::getInt('limitstart');
		$this->setState('list.offset', $offset);

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

		// TODO: Tune these values based on other permissions.
		$user		= JFactory::getUser();
			
		[%%IF INCLUDE_STATUS%%]
			[%%IF INCLUDE_ASSETACL%%]
		if ((!$user->authorise('core.edit.state', '[%%com_architectcomp%%]')) AND  (!$user->authorise('core.edit', '[%%com_architectcomp%%]')))
		{
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}		
			[%%ELSE INCLUDE_ASSETACL%%]
		$this->setState('filter.published', 1);			
			[%%ENDIF INCLUDE_ASSETACL%%]

		if ($params->get('filter_[%%compobject%%]_archived'))
		{
			$this->setState('filter.archived', $params->get('filter_[%%compobject%%]_archived'));
			
		}
		[%%ENDIF INCLUDE_STATUS%%]	
		[%%IF INCLUDE_LANGUAGE%%]
		$this->setState('filter.language', JLanguageMultilang::isEnabled());
		[%%ENDIF INCLUDE_LANGUAGE%%]			
	}
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = '[%%CompObjectPlural%%]', $prefix = '[%%ArchitectComp%%]Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get [%%CompObject_name%%] data.
	 *
	 * @param	integer	The id of the [%%compobject_name%%].
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function &getItem($pk = null)
	{
		// Get current user for authorisation checks
		$user	= JFactory::getUser();
		
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('[%%compobject%%].id');
		// Get the global params
		$global_params = JComponentHelper::getParams('[%%com_architectcomp%%]', true);

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select($this->getState(
					'item.select',
					'a.*'

					)
				);
				$query->from($db->quoteName('#__[%%architectcomp%%]_[%%compobjectplural%%]').' AS a');
				[%%IF GENERATE_CATEGORIES%%]
				
				// Join on category table.
				$query->select($db->quoteName('c.title').' AS category_title, '.
								$db->quoteName('c.alias').' AS category_alias, '.
								$db->quoteName('c.access').' AS category_access'
								);
				$query->join('LEFT', $db->quoteName('#__categories').' AS c on '.$db->quoteName('c.id').' = '.$db->quoteName('a.catid'));
				
				// Join over the categories to get parent category titles
				$query->select($db->quoteName('parent.title').' AS parent_title, '.
								$db->quoteName('parent.id').' AS parent_id, '.
								$db->quoteName('parent.alias').' AS parent_alias, '.
								$db->quoteName('parent.path').' AS parent_route'
				);
				$query->join('LEFT', $db->quoteName('#__categories').' AS parent ON '.$db->quoteName('parent.id').' = '.$db->quoteName('c.parent_id'));				
				[%%ENDIF GENERATE_CATEGORIES%%]
				[%%IF INCLUDE_CREATED%%]
				// Join on user table.
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
				// Join over the language
				$query->select($db->quoteName('l.title').' AS language_title');
				$query->join('LEFT', $db->quoteName('#__languages').' AS l ON '.$db->quoteName('l.lang_code').' = '.$db->quoteName('a.language'));

				// Filter by language
				if ($this->getState('filter.language'))
				{
					$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}					
				[%%ENDIF INCLUDE_LANGUAGE%%]

				$query->where('a.id = ' . (int) $pk);
				
				[%%IF GENERATE_CATEGORIES%%]
				// Join to check for category published status in parent categories up the tree
				// If all categories are published, badcats.id will be null, and we just use the [%%compobject%%] state
				$sub_query = ' (SELECT '.$db->quoteName('cat.id').' as id FROM '.$db->quoteName('#__categories').' AS cat JOIN '.$db->quoteName('#__categories').' AS parent ';
				$sub_query .= 'ON '.$db->quoteName('cat.lft').' BETWEEN '.$db->quoteName('parent.lft').' AND '.$db->quoteName('parent.rgt').' ';
				$sub_query .= 'WHERE '.$db->quoteName('parent.extension').' = ' . $db->quote('[%%com_architectcomp%%]');
				$sub_query .= ' AND '.$db->quoteName('parent.published').' <= 0 GROUP BY '.$db->quoteName('cat.id').')';
				$query->join('LEFT OUTER', $sub_query . ' AS badcats ON '.$db->quoteName('badcats.id').' = '.$db->quoteName('c.id'));
				[%%ENDIF GENERATE_CATEGORIES%%]
					
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
				[%%IF INCLUDE_STATUS%%]
				// Filter by published status.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');
				if (is_numeric($published))
				{
					$query->where('('.$db->quoteName('a.state').' = ' . (int) $published . ' OR '.$db->quoteName('a.state').' = ' . (int) $archived . ')');
				
				}
				[%%ENDIF INCLUDE_STATUS%%]
				
				[%%IF INCLUDE_ACCESS%%]
				$query->select($db->quoteName('vl.title').' AS access_title');
				$query->join('LEFT', $db->quoteName('#__viewlevels').' AS vl on '.$db->quoteName('vl.id').' = '.$db->quoteName('a.access'));
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
				// Join over the user.
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
				$query->join('LEFT', $db->quoteName('#__[%%architectcomp%%]_[%%FIELD_FOREIGN_OBJECT_PLURAL%%]').' AS [%%FIELD_FOREIGN_OBJECT_ACRONYM%%] ON '.$db->quoteName('[%%FIELD_FOREIGN_OBJECT_ACRONYM%%].id').' = '.$db->quoteName('a.[%%FIELD_CODE_NAME%%]'));	
					[%%ENDIF FIELD_LINK%%]	
				[%%ENDFOR OBJECT_FIELD%%]
																				
				$db->setQuery($query);

				$item = $db->loadObject();

				if ($error = $db->getErrorMsg())
				{
					throw new Exception($error);
				}

				if (empty($item))
				{
					return JError::raiseError(404, JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_ERROR_ITEM_NOT_FOUND'));
				}
				// Include any manipulation of the data on the record e.g. expand out Registry fields
				// NB The params registry field - if used - is done automatcially in the JAdminModel parent class
				
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
							
				[%%IF INCLUDE_STATUS%%]
				// Check for published state if filter set.
				if (((is_numeric($published)) OR (is_numeric($archived))) AND (($item->state != $published) AND ($item->state != $archived)))
				{
					return JError::raiseError(404, JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_ERROR_ITEM_NOT_FOUND'));
				}
				[%%ENDIF INCLUDE_STATUS%%]
				[%%IF INCLUDE_DESCRIPTION%%]
					[%%IF INCLUDE_INTRO%%]
				$item->introdescription = trim($item->intro) != '' ? $item->intro . $item->description : $item->description;
					[%%ENDIF INCLUDE_INTRO%%]
				[%%ENDIF INCLUDE_DESCRIPTION%%]

				// Convert parameter fields to objects.
				$[%%compobject_code_name%%]_params = new JRegistry;
				[%%IF INCLUDE_PARAMS_RECORD%%]
				$[%%compobject_code_name%%]_params->loadString($item->params);

				// Unpack readmore and layout params
				$item->[%%compobject%%]_alternative_readmore = $[%%compobject_code_name%%]_params->get('[%%compobject%%]_alternative_readmore');
				$item->layout = $[%%compobject_code_name%%]_params->get('layout');
				[%%ENDIF INCLUDE_PARAMS_RECORD%%]
				
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


				[%%IF INCLUDE_ASSETACL%%]
				// Compute selected asset permissions.

				// Technically guest could edit an [%%compobject%%], but lets not check that to improve performance a little.
				if (!$user->get('guest')) 
				{
					$user_id	= $user->get('id');
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
								// If owner allow them to edit state in front end
								$item->params->set('access-change', true);
								
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
				// Check edit state permission.
					if ($user->authorise('core.edit.state', $asset)) 
					{				
						$item->params->set('access-change', true);
					}											
				}
				[%%ENDIF INCLUDE_ASSETACL%%]

				[%%IF INCLUDE_ACCESS%%]
				// Compute view access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$item->params->set('access-view', true);
				}
				else
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$groups = $user->getAuthorisedViewLevels();
					[%%IF GENERATE_CATEGORIES%%]
					if ($item->catid != 0 AND $item->category_access !== null)
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

				$this->_item[$pk] = $item;
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}
	[%%IF INCLUDE_STATUS%%]
	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function publish(&$pks, $value = 1)
	{
		// Initialise variables.
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$user	= JFactory::getUser();
	
		$pks = (array) $pks;

		// Include the [%%architectcomp%%] plugins for the change of state event.
		JPluginHelper::importPlugin('[%%architectcomp%%]');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());
			return false;
		}

		// Trigger the ChangeState event.
		$result = $dispatcher->trigger('on[%%CompObject%%]ChangeState', array('[%%com_architectcomp%%].[%%compobject%%]', $pks, $value));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
	[%%ENDIF INCLUDE_STATUS%%]

	[%%IF INCLUDE_ORDERING%%]
	/**
	* A protected method to get a set of ordering conditions.
	*
	* @param	object	A record object.
	* @return	array	An array of conditions to add to add to ordering queries.
	*/
	protected function getReorderConditions($table = null)
	{
		$db = JFactory::getDbo();
		
		$condition = array();
		[%%IF GENERATE_CATEGORIES%%]
		$condition[] = $db->quoteName('catid').' = '.(int) $table->catid;	
		[%%ENDIF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_STATUS%%]
		$condition[] = $db->quoteName('state').' >= 0';
		[%%ENDIF INCLUDE_STATUS%%]
		return $condition;
	}
	/**
	 * Method to adjust the ordering of a row.
	 *
	 * Returns NULL if the user did not have edit
	 * privileges for any of the selected primary keys.
	 *
	 * @param   integer  $pks    The ID of the primary key to move.
	 * @param   integer  $delta  Increment, usually +1 or -1
	 *
	 * @return  mixed  False on failure or error, true on success, null if the $pk is empty (no items selected).
	 *
	 */
	public function reorder($pks, $delta = 0)
	{
		
		$table = $this->getTable();
		$pks = (array) $pks;
		$result = true;

		$allowed = true;

		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				// Access checks.
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					$allowed = false;
					continue;
				}

				$where = array();
				$where = $this->getReorderConditions($table);

				if (!$table->move($delta, $where))
				{
					$this->setError($table->getError());
					unset($pks[$i]);
					$result = false;
				}

			}
			else
			{
				$this->setError($table->getError());
				unset($pks[$i]);
				$result = false;
			}
		}

		if ($allowed === false AND empty($pks))
		{
			$result = null;
		}

		// Clear the component's cache
		if ($result == true)
		{
			$this->cleanCache();
		}

		return $result;
	}
	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array    $pks    An array of primary key ids.
	 * @param   integer  $order  +1 or -1
	 *
	 * @return  mixed
	 *
	 */
	public function saveorder($pks = null, $order = null)
	{
		
		$table = $this->getTable();
		$conditions = array();

		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}

		// update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			// Access checks.
			if (!$this->canEditState($table))
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
			elseif ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}

				// Remember to reorder within position and client_id
				$condition = $this->getReorderConditions($table);
				$found = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$key = $table->getKeyName();
					$conditions[] = array($table->$key, $condition);
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}	   	
	[%%ENDIF INCLUDE_ORDERING%%]
		
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array    $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 * 
	 */
	public function delete(&$pks)
	{
		
		$dispatcher	= JEventDispatcher::getInstance();
		$pks		= (array) $pks;
		$table		= $this->getTable();

		// Include the [%%architectcomp%%] plugins for the on delete events.
		JPluginHelper::importPlugin('[%%architectcomp%%]');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{

			if ($table->load($pk))
			{

				if ($this->canDelete($table))
				{

					// Trigger the BeforeDelete event.
					$result = $dispatcher->trigger('on[%%CompObject%%]BeforeDelete', array('[%%com_architectcomp%%].[%%compobject%%]', &$table));
					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					}
					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}

					// Trigger the AfterDelete event.
					$dispatcher->trigger('on[%%CompObject%%]AfterDelete', array('[%%com_architectcomp%%].[%%compobject%%]', &$table));

				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						JError::raiseWarning(500, $error);
						return false;
					}
					else
					{
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
						return false;
					}
				}

			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
	[%%IF INCLUDE_HITS%%]
	/**
	 * Increment the hit counter for the [%%compobject_name%%].
	 *
	 * @pk		int		Optional primary key of the [%%compobject_name%%] to increment.
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$hit_count = JRequest::getInt('hitcount', 1);

		if ($hit_count)
		{
			// Initialise variables.
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('[%%compobject%%].id');
			$db = $this->getDbo();

			$db->setQuery(
				'UPDATE '.$db->quoteName('#__[%%architectcomp%%]_[%%compobjectplural%%]') .
				' SET '.$db->quoteName('hits').' = '.$db->quoteName('hits').' + 1' .
				' WHERE '.$db->quoteName('id').' = '.(int) $pk
				);

			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	[%%ENDIF INCLUDE_HITS%%]
	[%%IF GENERATE_PLUGINS_VOTE%%]
	/**
	 * Update the vote rating for the [%%compobject_name%%].
	 *
	 * @pk		int		Optional primary key of the [%%compobject_name%%] to rate.
	 * @rate	int		Optional rating for the [%%compobject_name%%].
	 *
	 * @return	boolean	True if successful; false otherwise and internal error set.
	 */	
    public function storeVote($pk = 0, $rate = 0)
    {
        if ( $rate >= 1 AND $rate <= 5 AND $pk > 0 )
        {
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $db = $this->getDbo();

            $db->setQuery(
                    'SELECT *' .
                    ' FROM '.$db->quoteName('#__[%%architectcomp%%]_rating') .
                    ' WHERE '.$db->quoteName('content_id').' = '.(int) $pk .
                    ' AND '.$db->quoteName('content_type').' = '.$db->quote('[%%compobjectplural%%]')
            );

            $rating = $db->loadObject();

            if (!$rating)
            {
                // There are no ratings yet, so lets insert our rating
                $db->setQuery(
                        'INSERT INTO '.$db->quoteName('#__[%%architectcomp%%]_rating').' ( '.$db->quoteName('content_type').', '.$db->quoteName('content_id').', '.$db->quoteName('lastip').', '.$db->quoteName('rating_sum').', '.$db->quoteName('rating_count').' )' .
                        ' VALUES ( '.$db->quote('[%%compobjectplural%%]').', '.(int) $pk.', '.$db->quote($user_ip).', '.(int) $rate.', 1 )'
                );

                if (!$db->query())
                {
                        $this->setError($db->getErrorMsg());
                        return false;
                }
            }
            else
            {
                if ($user_ip != ($rating->lastip))
                {
                    $db->setQuery(
                            'UPDATE '.$db->quoteName('#__[%%architectcomp%%]_rating') .
                            ' SET '.$db->quoteName('rating_count').' = '.$db->quoteName('rating_count').' + 1, '.$db->quoteName('rating_sum').' = '.$db->quoteName('rating_sum').' + '.(int) $rate.', '.$db->quoteName('lastip').' = '.$db->quote($user_ip) .
                            ' WHERE '.$db->quoteName('content_id').' = '.(int) $pk.
							' AND '.$db->quoteName('content_type').' = [%%compobjectplural%%]'
                    );
                    if (!$db->query())
                    {
                            $this->setError($db->getErrorMsg());
                            return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            return true;
        }
        JError::raiseWarning( 'SOME_ERROR_CODE', JText::sprintf('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_INVALID_RATING', $rate), "[%%ArchitectComp%%]Model[%%CompObject%%]::storeVote($rate)");
        return false;
    }
	[%%ENDIF GENERATE_PLUGINS_VOTE%%]
}
