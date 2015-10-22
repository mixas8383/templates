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
 * @version			$Id: default_compobjectbatch.php 418 2014-10-22 14:42:36Z BrianWade $
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


JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

?>
[%%IF INCLUDE_BATCH%%]
<fieldset class="adminform">
	<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECT%%]_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECT%%]_BATCH_TIP'); ?></p>
	<ul>
	[%%IF INCLUDE_ACCESS%%]
		<li><?php echo JHtml::_('[%%compobject%%]batch.access');?><li>
	[%%ENDIF INCLUDE_ACCESS%%]
	[%%IF INCLUDE_LANGUAGE%%]
		<li><?php echo JHtml::_('[%%compobject%%]batch.language'); ?></li>
	[%%ENDIF INCLUDE_LANGUAGE%%]
	
	[%%IF GENERATE_CATEGORIES%%]
		<li><?php echo JHtml::_('[%%compobject%%]batch.category', '[%%com_architectcomp%%]');?></li>
	[%%ENDIF GENERATE_CATEGORIES%%]
	[%%IF INCLUDE_COPY%%]
		<li><?php echo JHtml::_('[%%compobject%%]batch.copy_items', '[%%com_architectcomp%%]');?></li>
	[%%ENDIF INCLUDE_COPY%%]	
	</ul>	
	<div style ="clear: both;">
	<button type="submit" onclick="Joomla.submitbutton('[%%compobject%%].batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	[%%IF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_ACCESS%%]
			[%%IF INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value='';document.id('batch-copy-items').value='';">
				[%%ELSE INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value='';">
				[%%ENDIF INCLUDE_COPY%%]
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
			[%%ELSE INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-copy-items').value='';">
				[%%ELSE INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';">
				[%%ENDIF INCLUDE_COPY%%]
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
			[%%ENDIF INCLUDE_LANGUAGE%%]			
		[%%ELSE INCLUDE_ACCESS%%]
			[%%IF INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-language-id').value='';document.id('batch-copy-items').value='';">
				[%%ELSE INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-language-id').value='';">
				[%%ENDIF INCLUDE_COPY%%]
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
			[%%ELSE INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-copy-items').value='';">
				[%%ELSE INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-category-id').value='';">
				[%%ENDIF INCLUDE_COPY%%]
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ENDIF INCLUDE_ACCESS%%]		
	[%%ELSE GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_ACCESS%%]
			[%%IF INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-access').value='';document.id('batch-language-id').value='';document.id('batch-copy-items').value='';">
				[%%ELSE INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-access').value='';document.id('batch-language-id').value='';">
				[%%ENDIF INCLUDE_COPY%%]
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
			[%%ELSE INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-access').value='';document.id('batch-copy-items').value='';">
				[%%ELSE INCLUDE_COPY%%]
	<button type="button" onclick="document.id('batch-access').value='';">
				[%%ENDIF INCLUDE_COPY%%]
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ENDIF INCLUDE_ACCESS%%]
	[%%ENDIF GENERATE_CATEGORIES%%]	
	</div>	
</fieldset>
[%%ENDIF INCLUDE_BATCH%%]
