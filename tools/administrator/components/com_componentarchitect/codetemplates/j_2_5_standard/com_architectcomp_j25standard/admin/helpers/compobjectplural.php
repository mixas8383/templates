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
 * @version			$Id: compobjectplural.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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
/**
 * [%%CompObject_plural_name%%] component helper.
 *
 * 
 */
class [%%ArchitectComp%%][%%CompObjectPlural%%]Helper
{
[%%IF INCLUDE_ASSETACL%%]
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The [%%compobject_short_name%%] ID.
	 *
	 * @return	JObject
	 * 
	 */
	[%%IF INCLUDE_ASSETACL_RECORD%%]
		[%%IF GENERATE_CATEGORIES%%] 	
	public static function getActions($category_id = 0, $[%%compobject_code_name%%]_id = 0)
		[%%ELSE GENERATE_CATEGORIES%%] 		
	public static function getActions($[%%compobject_code_name%%]_id = 0)
		[%%ENDIF GENERATE_CATEGORIES%%] 		
	[%%ELSE INCLUDE_ASSETACL_RECORD%%]
		[%%IF GENERATE_CATEGORIES%%] 	
	public static function getActions($category_id = 0)
		[%%ELSE GENERATE_CATEGORIES%%] 		
	public static function getActions()
		[%%ENDIF GENERATE_CATEGORIES%%] 		
	[%%ENDIF INCLUDE_ASSETACL_RECORD%%]	
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
		$level = 'component';		
		[%%IF INCLUDE_ASSETACL_RECORD%%]
		if (!empty($[%%compobject_code_name%%]_id) AND $[%%compobject_code_name%%]_id !== 0) 
		{
			$asset_name = '[%%com_architectcomp%%].[%%compobject%%].'.(int) $[%%compobject_code_name%%]_id;
		}
		else
		{
		[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
			[%%IF GENERATE_CATEGORIES%%] 		
			if (!empty($category_id) AND $category_id !== 0)
			{
				$level = 'category';			
				$asset_name = '[%%com_architectcomp%%].category.'.(int) $category_id;
			}
			else
			{
				$asset_name = '[%%com_architectcomp%%]';
			}
			[%%ELSE GENERATE_CATEGORIES%%]
			$asset_name = '[%%com_architectcomp%%]';
			[%%ENDIF GENERATE_CATEGORIES%%]			
		[%%IF INCLUDE_ASSETACL_RECORD%%]
		}
		[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
		$actions = JAccess::getActions('[%%com_architectcomp%%]', $level);

		foreach ($actions as $action) 
		{
			$result->set($action->name,	$user->authorise($action->name, $asset_name));
		}

		return $result;
	}
[%%ENDIF INCLUDE_ASSETACL%%]


}