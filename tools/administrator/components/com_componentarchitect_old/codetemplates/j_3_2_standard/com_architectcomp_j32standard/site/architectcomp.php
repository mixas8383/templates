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
 * @version			$Id: architectcomp.php 417 2014-10-22 14:42:10Z BrianWade $
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


require_once JPATH_COMPONENT.'/helpers/route.php';
require_once JPATH_COMPONENT.'/helpers/query.php';
require_once JPATH_COMPONENT.'/helpers/[%%architectcomp%%].php';

[%%FOREACH COMPONENT_OBJECT%%]
	[%%IF INCLUDE_TAGS%%]
// Set Tags observer
JObserverMapper::addObserverClassToClass('JTableObserverTags', '[%%ArchitectComp%%]Table[%%CompObjectPlural%%]', array('typeAlias' => '[%%com_architectcomp%%].[%%compobject%%]'));
	[%%ENDIF INCLUDE_TAGS%%]

	[%%IF INCLUDE_VERSIONS%%]
// Set Version History observer
JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', '[%%ArchitectComp%%]Table[%%CompObjectPlural%%]', array('typeAlias' => '[%%com_architectcomp%%].[%%compobject%%]'));
	[%%ENDIF INCLUDE_VERSIONS%%]
[%%ENDFOR COMPONENT_OBJECT%%]

$controller = JControllerLegacy::getInstance('[%%ArchitectComp%%]');

$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
