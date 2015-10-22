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
 * @version			$Id: route.php 417 2014-10-22 14:42:10Z BrianWade $
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
 * [%%ArchitectComp_name%%] Component Route Helper
 *
 */
abstract class [%%ArchitectComp%%]HelperRoute
{
	protected static $lookup = array();
	
	[%%IF INCLUDE_LANGUAGE%%]
	protected static $lang_lookup = array();
	[%%ENDIF INCLUDE_LANGUAGE%%]
			
	[%%FOREACH COMPONENT_OBJECT%%]	
	/**
	 * @param	integer	The route of the [%%CompObject_name%%]
	 */
	[%%IF GENERATE_CATEGORIES%%]		 
		[%%IF INCLUDE_LANGUAGE%%]
	public static function get[%%CompObject%%]Route($id, $cat_id = 0, $language = '*', $layout = 'default', $keep_item_id = false)
		[%%ELSE INCLUDE_LANGUAGE%%]
	public static function get[%%CompObject%%]Route($id, $cat_id = 0, $layout = 'default', $keep_item_id = false)
		[%%ENDIF INCLUDE_LANGUAGE%%]
	[%%ELSE GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_LANGUAGE%%]
	public static function get[%%CompObject%%]Route($id, $language = '*', $layout = 'default', $keep_item_id = false)
		[%%ELSE INCLUDE_LANGUAGE%%]
	public static function get[%%CompObject%%]Route($id, $layout = 'default', $keep_item_id = false)
		[%%ENDIF INCLUDE_LANGUAGE%%]	
	[%%ENDIF GENERATE_CATEGORIES%%]												 
	{
		$needles = array(
			'[%%compobject%%]'  => array((int) $id)
		);
		// Remove lead string from the form field value
		$layout = str_replace('_:', '', $layout);	
				
		if ($layout == '' OR $layout == 'default')
		{
			//Create the link
			$link = 'index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]&id='. $id;
		}
		else
		{
			//Create the link with a layout
			$link = 'index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]&layout='.$layout.'&id='. $id;
		}

		[%%IF GENERATE_CATEGORIES%%]		
		if ($cat_id > 1)
		{
			$options['countItems'] = false;
			$options['table'] = '#__[%%architectcomp%%]_[%%compobjectplural%%]';		
			$categories = JCategories::getInstance('[%%ArchitectComp%%]', $options);
		
			$category = $categories->get($cat_id);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$cat_id;
			}
		}
		[%%ENDIF GENERATE_CATEGORIES%%]
		
		[%%IF INCLUDE_LANGUAGE%%]
		if ($language AND $language != "*" AND JLanguageMultilang::isEnabled())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}
		[%%ENDIF INCLUDE_LANGUAGE%%]		
		if ($item = self::findItem($needles, $keep_item_id, $layout))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}
	[%%ENDFOR COMPONENT_OBJECT%%]	
	[%%IF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_LANGUAGE%%]
	public static function getCategoryRoute($cat_id, $keep_item_id = false, $language = 0)
		[%%ELSE INCLUDE_LANGUAGE%%]
	public static function getCategoryRoute($cat_id, $keep_item_id = false)
		[%%ENDIF INCLUDE_LANGUAGE%%]
	{
		if ($cat_id instanceof JCategoryNode)
		{
			$id = $cat_id->id;
			$category = $cat_id;
		}
		else
		{
			$id = (int) $cat_id;
			$options['countItems'] = false;
			$options['table'] = '';			
			$category = JCategories::getInstance('[%%ArchitectComp%%]', $options)->get($id);
		}

		if($id < 1 OR !($category instanceof JCategoryNode))
		{
			$link = '';
		}
		else
		{
			$needles = array();
					
			$link = 'index.php?option=[%%com_architectcomp%%]&view=category&id='.$id;
		
			$cat_ids = array_reverse($category->getPath());
			$needles['category'] = $cat_ids;
			$needles['categories'] = $cat_ids;

			[%%IF INCLUDE_LANGUAGE%%]
			if ($language AND $language != "*" AND JLanguageMultilang::isEnabled())
			{
				self::buildLanguageLookup();

				if (isset(self::$lang_lookup[$language]))
				{
					$link .= '&lang=' . self::$lang_lookup[$language];
					$needles['language'] = $language;
				}
			}
			[%%ENDIF INCLUDE_LANGUAGE%%]
			
			if ($item = self::findItem($needles, $keep_item_id))
			{
				$link .= '&Itemid='.$item;
			}
			else
			{
				//Create the link
				$link = 'index.php?option=[%%com_architectcomp%%]&view=category&id='.$id;
				if($category)
				{
					$cat_ids = array_reverse($category->getPath());
					
					$needles['category'] = $cat_ids;
					$needles['categories'] = $cat_ids;
					
					if ($item = self::findItem($needles, $keep_item_id))
					{
						$link .= '&Itemid='.$item;
					}
					else
					{
						if ($item = self::findItem(null, $keep_item_id))
						{
							$link .= '&Itemid='.$item;
						}
					}
				}
			}
		}

		return $link;
	}
	[%%ENDIF GENERATE_CATEGORIES%%]
	
	[%%IF INCLUDE_LANGUAGE%%]
	protected static function buildLanguageLookup()
	{
		if (count(self::$lang_lookup) == 0)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('a.sef AS sef')
				->select('a.lang_code AS lang_code')
				->from('#__languages AS a');

			$db->setQuery($query);
			$langs = $db->loadObjectList();

			foreach ($langs as $lang)
			{
				self::$lang_lookup[$lang->lang_code] = $lang->sef;
			}
		}
	}
	[%%ENDIF INCLUDE_LANGUAGE%%]	
	protected static function findItem($needles = null, $keep_item_id = false, $layout = 'default')
	{
		if($keep_item_id)
		{
			return null;	
		}	
	[%%IF INCLUDE_LANGUAGE%%]

		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$language	= isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('[%%com_architectcomp%%]');

			$attributes = array('component_id');
			$values = array($component->id);

			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}

			$items		= $menus->getItems($attributes, $values);

			foreach ($items as $item)
			{
				if (isset($item->query) AND isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (isset($item->query['layout']))
					{
						$item_layout = $item->query['layout'];					
						if (!isset(self::$lookup[$language][$view.'-'.$item_layout]))
						{
							self::$lookup[$language][$view.'-'.$item_layout] = array();
						}
						if (isset($item->query['id'])) {

							// here it will become a bit tricky
							// language != * can override existing entries
							// language == * cannot override existing entries
							if (!isset(self::$lookup[$language][$view.'-'.$item_layout][$item->query['id']]) OR $item->language != '*')
							{
								self::$lookup[$language][$view.'-'.$item_layout][$item->query['id']] = $item->id;
							}
						}
					}
					else
					{
						if (!isset(self::$lookup[$language][$view]))
						{
							self::$lookup[$language][$view] = array();
						}
						if (isset($item->query['id']))
						{
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}					
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if ($layout == '' OR $layout == 'default')
				{			
					if (isset(self::$lookup[$language][$view]))
					{
						foreach ($ids as $id)
						{
							if (isset(self::$lookup[$language][$view][(int) $id]))
							{
								return self::$lookup[$language][$view][(int) $id];
							}
						}
					}
				}
				else
				{
					if (isset(self::$lookup[$language][$view.'-'.$layout]))
					{
						foreach ($ids as $id)
						{
							if (isset(self::$lookup[$language][$view.'-'.$layout][(int) $id]))
							{
								return self::$lookup[$language][$view.'-'.$layout][(int) $id];
							}
						}
					}
				}				
			}
		}

		$active = $menus->getActive();
		if ($active AND $active->component == '[%%com_architectcomp%%]' AND ($language == '*' OR in_array($active->language, array('*', $language)) OR !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}

		// if not found, return language specific home link
		$default = $menus->getDefault($language);
		return !empty($default->id) ? $default->id : null;
	[%%ELSE INCLUDE_LANGUAGE%%]		
			
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('[%%com_architectcomp%%]');
			$items		= $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) AND isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (isset($item->query['layout']))
					{
						$item_layout = $item->query['layout'];
					
						if (!isset(self::$lookup[$view.'-'.$item_layout]))
						{
							self::$lookup[$view.'-'.$item_layout] = array();
						}
						if (isset($item->query['id']))
						{
							self::$lookup[$view.'-'.$item_layout][$item->query['id']] = $item->id;
						}						
					}
					else
					{
						if (!isset(self::$lookup[$view]))
						{
							self::$lookup[$view] = array();
						}
						if (isset($item->query['id']))
						{
							self::$lookup[$view][$item->query['id']] = $item->id;
						}
					}					
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if ($layout == '' OR $layout == 'default')
				{
					if (isset(self::$lookup[$view]))
					{
						foreach($ids as $id)
						{
							if (isset(self::$lookup[$view][(int)$id]))
							{
								return self::$lookup[$view][(int)$id];
							}
						}
					}
				}
				else
				{
					if (isset(self::$lookup[$view.'-'.$layout]))
					{
						foreach($ids as $id)
						{
							if (isset(self::$lookup[$view.'-'.$layout][(int)$id]))
							{
								return self::$lookup[$view.'-'.$layout][(int)$id];
							}
						}
					}				
				}
			}
		}
		return null;
	[%%ENDIF INCLUDE_LANGUAGE%%]		
		
	}
}
