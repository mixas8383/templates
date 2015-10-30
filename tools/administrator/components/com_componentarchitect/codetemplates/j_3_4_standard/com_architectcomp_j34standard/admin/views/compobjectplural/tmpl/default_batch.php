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
 * @version			$Id: default_batch.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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

[%%IF INCLUDE_BATCH%%]

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">&#215;</button>
		<h3><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECT%%]_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECT%%]_BATCH_TIP'); ?></p>
	[%%IF INCLUDE_ACCESS%%]
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('[%%compobject%%]batch.access');?>
			</div>
		</div>
	[%%ENDIF INCLUDE_ACCESS%%]
	[%%IF INCLUDE_TAGS%%]
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('[%%compobject%%]batch.tag');?>
			</div>
		</div>	
	[%%ENDIF INCLUDE_TAGS%%]
	[%%IF INCLUDE_LANGUAGE%%]
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('[%%compobject%%]batch.language'); ?>
			</div>
		</div>
	[%%ENDIF INCLUDE_LANGUAGE%%]
	[%%IF GENERATE_CATEGORIES%%]
		<div class="control-group">
			<div class="controls">	
				<?php echo JHtml::_('[%%compobject%%]batch.category', '[%%com_architectcomp%%]');?>
			</div>
		</div>
	[%%ENDIF GENERATE_CATEGORIES%%]	
	[%%IF INCLUDE_COPY%%]
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('[%%compobject%%]batch.copy_items', '[%%com_architectcomp%%]');?>
			</div>
		</div>
	[%%ENDIF INCLUDE_COPY%%]
	</div>
	<div class="modal-footer">
		<button type="button" onclick="
			[%%IF GENERATE_CATEGORIES%%]
			document.id('batch-category-id').value='';	
			[%%ENDIF GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_TAGS%%]
			document.id('batch-tag-id)').value='';
			[%%ENDIF INCLUDE_TAGS%%]		
			[%%IF INCLUDE_LANGUAGE%%]
			document.id('batch-language-id').value='';	
			[%%ENDIF INCLUDE_LANGUAGE%%]
			[%%IF INCLUDE_COPY%%]
			document.id('batch-copy-items').value='';
			[%%ENDIF INCLUDE_COPY%%]					
		">	
			<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
		</button>				
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('[%%compobject%%].batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
[%%ENDIF INCLUDE_BATCH%%]
