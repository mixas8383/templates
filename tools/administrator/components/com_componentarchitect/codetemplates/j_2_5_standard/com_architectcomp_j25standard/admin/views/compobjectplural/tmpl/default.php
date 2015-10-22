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
 * @version			$Id: default.php 418 2014-10-22 14:42:36Z BrianWade $
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

$user		= JFactory::getUser();
$user_id		= $user->get('id');
$list_order	= $this->state->get('list.ordering');
$list_dirn	= $this->state->get('list.direction');

[%%IF INCLUDE_ASSETACL%%]
	[%%IF INCLUDE_ORDERING%%]
$can_order	= $user->authorise('core.edit.state', '[%%com_architectcomp%%]');
	[%%ENDIF INCLUDE_ORDERING%%]
[%%ENDIF INCLUDE_ASSETACL%%]

[%%IF INCLUDE_ORDERING%%]
$save_order	= ($list_order=='ordering' OR $list_order=='a.ordering');
[%%ENDIF INCLUDE_ORDERING%%]
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$empty = $component->params->get('default_empty_field', '');
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
<form action="<?php echo JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobjectplural%%]'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_SEARCH_IN_NAME'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			[%%FOREACH FILTER_FIELD%%]
				[%%IF FIELD_FILTER_LINK%%]
			<select name="filter_[%%FIELD_CODE_NAME%%]" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_[%%FIELD_FOREIGN_OBJECT_UPPER%%]');?></option>
				<?php echo JHtml::_('select.options', $this->[%%FIELD_FOREIGN_OBJECT_PLURAL%%], 'value', 'text', $this->state->get('filter.[%%FIELD_CODE_NAME%%]'));?>
			</select>	
				[%%ELSE FIELD_FILTER_LINK%%]
			<select name="filter_[%%FIELD_CODE_NAME%%]" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_[%%FIELD_CODE_NAME_UPPER%%]');?></option>
				<?php echo JHtml::_('select.options', $this->[%%FIELD_CODE_NAME%%]_values, 'value', 'text', $this->state->get('filter.[%%FIELD_CODE_NAME%%]'));?>
			</select>	
				[%%ENDIF FIELD_FILTER_LINK%%]				
			[%%ENDFOR FILTER_FIELD%%]
			[%%IF GENERATE_CATEGORIES%%]
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', '[%%com_architectcomp%%]'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>		
			[%%ENDIF GENERATE_CATEGORIES%%]

			[%%IF INCLUDE_STATUS%%]
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>			
			[%%ENDIF INCLUDE_STATUS%%]
			
			[%%IF INCLUDE_ACCESS%%]
            <select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
			[%%ENDIF INCLUDE_ACCESS%%]			
			[%%IF INCLUDE_LANGUAGE%%]
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
			[%%ENDIF INCLUDE_LANGUAGE%%]
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				[%%IF INCLUDE_NAME%%]
				<th>
					<?php echo JHtml::_('grid.sort',  '[%%COM_ARCHITECTCOMP%%]_HEADING_NAME', 'a.name', $list_dirn, $list_order); ?>
				</th>
				[%%ENDIF INCLUDE_NAME%%]
				[%%FOREACH FILTER_FIELD%%]
				<th width="10%">
					[%%IF FIELD_FILTER_LINK%%]
					<?php echo JHtml::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_HEADING_[%%FIELD_CODE_NAME_UPPER%%]', '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]', $list_dirn, $list_order); ?>
					[%%ELSE FIELD_FILTER_LINK%%]
					<?php echo JHtml::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_HEADING_[%%FIELD_CODE_NAME_UPPER%%]', 'a.[%%FIELD_CODE_NAME%%]', $list_dirn, $list_order); ?>
					[%%ENDIF FIELD_FILTER_LINK%%]
				</th>	
				[%%ENDFOR FILTER_FIELD%%]			
				[%%IF GENERATE_CATEGORIES%%]
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $list_dirn, $list_order); ?>
				</th>
				[%%ENDIF GENERATE_CATEGORIES%%]				

				[%%IF INCLUDE_STATUS%%]
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $list_dirn, $list_order); ?>
				</th>
				[%%ENDIF INCLUDE_STATUS%%]

				[%%IF INCLUDE_FEATURED%%]
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JFEATURED', 'a.featured', $list_dirn, $list_order, NULL, 'desc'); ?>
				</th>
				[%%ENDIF INCLUDE_FEATURED%%]
				[%%IF INCLUDE_ACCESS%%]
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'a.access', $list_dirn, $list_order); ?>
				</th>
				[%%ENDIF INCLUDE_ACCESS%%]	
				[%%IF INCLUDE_LANGUAGE%%]
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $list_dirn, $list_order); ?>
				</th>		
				[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_ORDERING%%]
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $list_dirn, $list_order); ?>
					[%%IF INCLUDE_ASSETACL%%]
					<?php if ($can_order): ?>
					[%%ENDIF INCLUDE_ASSETACL%%]
						<?php if ($save_order): ?>					
							<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', '[%%compobjectplural%%].saveorder'); ?>
						<?php endif;?>
					[%%IF INCLUDE_ASSETACL%%]
					<?php endif;?>
					[%%ENDIF INCLUDE_ASSETACL%%]
				</th>
				[%%ENDIF INCLUDE_ORDERING%%]
					
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $list_dirn, $list_order); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :

			[%%IF INCLUDE_ORDERING%%]
			$ordering	= ($list_order=='ordering' OR $list_order=='a.ordering');
			[%%ENDIF INCLUDE_ORDERING%%]			
			$can_change = true;
			[%%IF INCLUDE_CHECKOUT%%]
				[%%IF INCLUDE_ASSETACL%%]
				$can_checkin	= $user->authorise('core.manage', 'com_checkin') OR $item->checked_out == $user_id OR $item->checked_out == 0;
				[%%ELSE INCLUDE_ASSETACL%%]
				$can_checkin	= $item->checked_out == $user_id OR $item->checked_out == 0;
				[%%ENDIF INCLUDE_ASSETACL%%]
			[%%ENDIF INCLUDE_CHECKOUT%%]			
			[%%IF INCLUDE_ASSETACL%%]
				[%%IF INCLUDE_ASSETACL_RECORD%%]
			$can_edit	= $user->authorise('core.edit',	'[%%com_architectcomp%%].[%%compobject%%].'.$item->id);
	
			$can_edit_own	= $user->authorise('core.edit.own',		'[%%com_architectcomp%%].[%%compobject%%].'.$item->id) 
							[%%IF INCLUDE_CREATED%%]
							AND $item->created_by == $user_id
							[%%ENDIF INCLUDE_CREATED%%]
							;
			$can_change	= $user->authorise('core.edit.state',	'[%%com_architectcomp%%].[%%compobject%%].'.$item->id) 
							[%%IF INCLUDE_CHECKOUT%%]
							AND $can_checkin
							[%%ENDIF INCLUDE_CHECKOUT%%]
							;
				[%%ELSE INCLUDE_ASSETACL_RECORD%%]
			$can_edit	= $user->authorise('core.edit',	'[%%com_architectcomp%%]');
	
			$can_edit_own	= $user->authorise('core.edit.own',		'[%%com_architectcomp%%]') 
							[%%IF INCLUDE_CREATED%%]
							AND $item->created_by == $user_id
							[%%ENDIF INCLUDE_CREATED%%]
							;
			$can_change	= $user->authorise('core.edit.state',	'[%%com_architectcomp%%]') 
							[%%IF INCLUDE_CHECKOUT%%]
							AND $can_checkin
							[%%ENDIF INCLUDE_CHECKOUT%%]
							;
				[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
			[%%ENDIF INCLUDE_ASSETACL%%]
						
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				[%%IF INCLUDE_NAME%%]				
				<td>
					[%%IF INCLUDE_CHECKOUT%%]
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, '[%%compobjectplural%%].', $can_checkin); ?>
					<?php endif; ?>
					[%%ENDIF INCLUDE_CHECKOUT%%]

					[%%IF INCLUDE_ASSETACL%%]
					<?php if ($can_edit OR $can_edit_own) : ?>
					[%%ENDIF INCLUDE_ASSETACL%%]
						<a href="<?php echo JRoute::_('index.php?option=[%%com_architectcomp%%]&task=[%%compobject%%].edit&id='.(int) $item->id); ?>">
						<?php echo $this->escape($item->name); ?></a>
					[%%IF INCLUDE_ASSETACL%%]
					<?php else : ?>
						<?php echo $this->escape($item->name); ?>
					<?php endif; ?>
					[%%ENDIF INCLUDE_ASSETACL%%]
					[%%IF INCLUDE_ALIAS%%]
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
					</p>
					[%%ENDIF INCLUDE_ALIAS%%]
				</td>
				[%%ENDIF INCLUDE_NAME%%]				
				[%%FOREACH FILTER_FIELD%%]
				<td class="center">
					<?php 
					[%%FIELD_ADMIN_LIST_VALUE%%] 
					?>				
				</td>	
				[%%ENDFOR FILTER_FIELD%%]			
				[%%IF GENERATE_CATEGORIES%%]				
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>	
				[%%ENDIF GENERATE_CATEGORIES%%]	
					
				[%%IF INCLUDE_STATUS%%]
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, '[%%compobjectplural%%].', $can_change); ?>
				</td>
				
				[%%ENDIF INCLUDE_STATUS%%]

				[%%IF INCLUDE_FEATURED%%]
				<td class="center">
					<?php echo JHtml::_('[%%compobject%%].featured', $item->featured, $i, $can_change); ?>
				</td>
				[%%ENDIF INCLUDE_FEATURED%%]
				[%%IF INCLUDE_ACCESS%%]
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				[%%ENDIF INCLUDE_ACCESS%%]	
				[%%IF INCLUDE_LANGUAGE%%]
				<td class="center">
					<?php if ($item->language=='*'):?>
						<?php echo JText::alt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php endif;?>
				</td>	
				[%%ENDIF INCLUDE_LANGUAGE%%]							

				[%%IF INCLUDE_ORDERING%%]
				<td class="order">
					[%%IF INCLUDE_ASSETACL%%]
					<?php if ($can_order) : ?>
					[%%ENDIF INCLUDE_ASSETACL%%]
						<?php if ($save_order) : ?>
							<?php 
								$condition_up = true;
								$condition_down = true;
								[%%IF GENERATE_CATEGORIES%%]									
								if ($item->catid != @$this->items[$i-1]->catid) :
									$condition_up = false; 
								endif;
								if ($item->catid != @$this->items[$i+1]->catid) :
									$condition_down = false; 
								endif;
								[%%ENDIF GENERATE_CATEGORIES%%]
								[%%FOREACH ORDER_FIELD%%]
								if ($item->[%%FIELD_CODE_NAME%%] != @$this->items[$i-1]->[%%FIELD_CODE_NAME%%]) :
									$condition_up = false; 
								endif;
								if ($item->[%%FIELD_CODE_NAME%%] != @$this->items[$i+1]->[%%FIELD_CODE_NAME%%]) :
									$condition_down = false; 
								endif;	
								[%%ENDFOR ORDER_FIELD%%]								
							?>						
							<?php if ($list_dirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i,$condition_up,'[%%compobjectplural%%].orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, '[%%compobjectplural%%].orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($list_dirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, $condition_up, '[%%compobjectplural%%].orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, '[%%compobjectplural%%].orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $save_order ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled;?> class="text-area-order" />
					[%%IF INCLUDE_ASSETACL%%]
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
					[%%ENDIF INCLUDE_ASSETACL%%]
				</td>
				[%%ENDIF INCLUDE_ORDERING%%]

				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	[%%IF INCLUDE_BATCH%%]
		[%%IF INCLUDE_ASSETACL%%]
	<?php //Load the batch processing form. ?>
	<?php if ($user->authorize('core.create', '[%%com_architectcomp%%]') AND $user->authorize('core.edit', '[%%com_architectcomp%%]') AND $user->authorize('core.edit.state', '[%%com_architectcomp%%]')) : ?>
		<?php echo $this->loadTemplate('[%%compobject%%]batch'); ?>
	<?php endif;?>
		[%%ELSE INCLUDE_ASSETACL%%]
	<?php //Load the batch processing form. ?>
	<?php echo $this->loadTemplate('[%%compobject%%]batch'); ?>
		[%%ENDIF INCLUDE_ASSETACL%%]
	[%%ENDIF INCLUDE_BATCH%%]

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $list_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $list_dirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
