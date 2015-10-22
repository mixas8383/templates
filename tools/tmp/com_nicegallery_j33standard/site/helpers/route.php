<?php
/**
 * @version 		$Id:$
 * @name			Nicegallery (Release 1.0.0)
 * @author			 ()
 * @package			com_nicegallery
 * @subpackage		com_nicegallery.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: route.php 408 2014-10-19 18:31:00Z BrianWade $
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
 * Nicegallery Component Route Helper
 *
 */
abstract class NicegalleryHelperRoute
{
	protected static $lookup = array();
	
	protected static $lang_lookup = array();
			
	/**
	 * @param	integer	The route of the Coments
	 */
	public static function getComentsRoute($id, $language = '*', $layout = 'default', $keep_item_id = false)
	{
		$needles = array(
			'coments'  => array((int) $id)
		);
		// Remove lead string from the form field value
		$layout = str_replace('_:', '', $layout);	
				
		if ($layout == '' OR $layout == 'default')
		{
			//Create the link
			$link = 'index.php?option=com_nicegallery&view=coments&id='. $id;
		}
		else
		{
			//Create the link with a layout
			$link = 'index.php?option=com_nicegallery&view=coments&layout='.$layout.'&id='. $id;
		}

		
		if ($language AND $language != "*" AND JLanguageMultilang::isEnabled())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}
		if ($item = self::findItem($needles, $keep_item_id, $layout))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}
	/**
	 * @param	integer	The route of the Vote
	 */
	public static function getVoteRoute($id, $language = '*', $layout = 'default', $keep_item_id = false)
	{
		$needles = array(
			'vote'  => array((int) $id)
		);
		// Remove lead string from the form field value
		$layout = str_replace('_:', '', $layout);	
				
		if ($layout == '' OR $layout == 'default')
		{
			//Create the link
			$link = 'index.php?option=com_nicegallery&view=vote&id='. $id;
		}
		else
		{
			//Create the link with a layout
			$link = 'index.php?option=com_nicegallery&view=vote&layout='.$layout.'&id='. $id;
		}

		
		if ($language AND $language != "*" AND JLanguageMultilang::isEnabled())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}
		if ($item = self::findItem($needles, $keep_item_id, $layout))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}
	/**
	 * @param	integer	The route of the Config
	 */
	public static function getConfigRoute($id, $language = '*', $layout = 'default', $keep_item_id = false)
	{
		$needles = array(
			'config'  => array((int) $id)
		);
		// Remove lead string from the form field value
		$layout = str_replace('_:', '', $layout);	
				
		if ($layout == '' OR $layout == 'default')
		{
			//Create the link
			$link = 'index.php?option=com_nicegallery&view=config&id='. $id;
		}
		else
		{
			//Create the link with a layout
			$link = 'index.php?option=com_nicegallery&view=config&layout='.$layout.'&id='. $id;
		}

		
		if ($language AND $language != "*" AND JLanguageMultilang::isEnabled())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}
		if ($item = self::findItem($needles, $keep_item_id, $layout))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}
	/**
	 * @param	integer	The route of the Image
	 */
	public static function getImageRoute($id, $cat_id = 0, $language = '*', $layout = 'default', $keep_item_id = false)
	{
		$needles = array(
			'image'  => array((int) $id)
		);
		// Remove lead string from the form field value
		$layout = str_replace('_:', '', $layout);	
				
		if ($layout == '' OR $layout == 'default')
		{
			//Create the link
			$link = 'index.php?option=com_nicegallery&view=image&id='. $id;
		}
		else
		{
			//Create the link with a layout
			$link = 'index.php?option=com_nicegallery&view=image&layout='.$layout.'&id='. $id;
		}

		if ($cat_id > 1)
		{
			$options['countItems'] = false;
			$options['table'] = '#__nicegallery_images';		
			$categories = JCategories::getInstance('Nicegallery', $options);
		
			$category = $categories->get($cat_id);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$cat_id;
			}
		}
		
		if ($language AND $language != "*" AND JLanguageMultilang::isEnabled())
		{
			self::buildLanguageLookup();

			if (isset(self::$lang_lookup[$language]))
			{
				$link .= '&lang=' . self::$lang_lookup[$language];
				$needles['language'] = $language;
			}
		}
		if ($item = self::findItem($needles, $keep_item_id, $layout))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}
	public static function getCategoryRoute($cat_id, $keep_item_id = false, $language = 0)
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
			$category = JCategories::getInstance('Nicegallery', $options)->get($id);
		}

		if($id < 1 OR !($category instanceof JCategoryNode))
		{
			$link = '';
		}
		else
		{
			$needles = array();
					
			$link = 'index.php?option=com_nicegallery&view=category&id='.$id;
		
			$cat_ids = array_reverse($category->getPath());
			$needles['category'] = $cat_ids;
			$needles['categories'] = $cat_ids;

			if ($language AND $language != "*" AND JLanguageMultilang::isEnabled())
			{
				self::buildLanguageLookup();

				if (isset(self::$lang_lookup[$language]))
				{
					$link .= '&lang=' . self::$lang_lookup[$language];
					$needles['language'] = $language;
				}
			}
			
			if ($item = self::findItem($needles, $keep_item_id))
			{
				$link .= '&Itemid='.$item;
			}
			else
			{
				//Create the link
				$link = 'index.php?option=com_nicegallery&view=category&id='.$id;
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
	protected static function findItem($needles = null, $keep_item_id = false, $layout = 'default')
	{
		if($keep_item_id)
		{
			return null;	
		}	

		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$language	= isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('com_nicegallery');

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
		if ($active AND $active->component == 'com_nicegallery' AND ($language == '*' OR in_array($active->language, array('*', $language)) OR !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}

		// if not found, return language specific home link
		$default = $menus->getDefault($language);
		return !empty($default->id) ? $default->id : null;
		
	}
}