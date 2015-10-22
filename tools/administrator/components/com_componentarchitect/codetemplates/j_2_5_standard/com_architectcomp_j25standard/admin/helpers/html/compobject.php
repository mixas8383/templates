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
 * @version			$Id: compobject.php 418 2014-10-22 14:42:36Z BrianWade $
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
 */
abstract class JHtml[%%CompObject%%]
{
	[%%IF INCLUDE_FEATURED%%]
	/**
	 * @param	int $value	The featured value
	 * @param	int $i
	 * @param	bool $can_change Whether the value can be changed or not
	 *
	 * @return	string	The anchor tag to toggle featured/unfeatured [%%compobject_plural_name%%].
	 * 
	 */
	static function featured($value = 0, $i, $can_change = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('disabled.png', '[%%compobjectplural%%].featured', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_UNFEATURED', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_TOGGLE_TO_FEATURE'),
			1	=> array('featured.png', '[%%compobjectplural%%].unfeatured', 'JFEATURED', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_TOGGLE_TO_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html	= JHtml::_('image','admin/'.$state[0], JText::_($state[2]), NULL, true);
		if ($can_change)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html .'</a>';
		}

		return $html;
	}
	[%%ENDIF INCLUDE_FEATURED%%]
}
