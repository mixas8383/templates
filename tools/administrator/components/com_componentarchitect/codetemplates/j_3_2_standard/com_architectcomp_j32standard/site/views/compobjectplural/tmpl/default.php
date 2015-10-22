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
 * @version			$Id: default.php 417 2014-10-22 14:42:10Z BrianWade $
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

/*
 *	Add style sheets, javascript and behaviours here in the layout so they can be overridden, if required, in a template override 
 */

// Add css files for the [%%architectcomp%%] component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%architectcomp%%].css');
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%compobjectplural%%].css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%architectcomp%%]-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%compobjectplural%%]-rtl.css');
}

// Add Javscript functions for field display
[%%IF INCLUDE_IMAGE%%]
JHtml::_('behavior.caption');
[%%ENDIF INCLUDE_IMAGE%%]				
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');	

/*
 *	Initialise values for the layout 
 */	
 
// Create some shortcuts.
$user		= JFactory::getUser();
$n			= count($this->items);
$list_order	= $this->state->get('list.ordering');
$list_dirn	= $this->state->get('list.direction');

$layout		= $this->params->get('[%%compobject%%]_layout', 'default');

[%%IF INCLUDE_ASSETACL%%]
$can_create	= $user->authorise('core.create', '[%%com_architectcomp%%]');
[%%ENDIF INCLUDE_ASSETACL%%]
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$empty = $component->params->get('default_empty_field', '');

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="[%%architectcomp%%] [%%compobjectplural%%]-list<?php echo $this->params->get('pageclass_sfx');?>">
	<?php if ($this->params->get('show_page_heading')): ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>

	<?php
		$show_actions = false;
		if ($this->params->get('show_[%%compobject%%]_icons',-1) >= 0) :
			foreach ($this->items as $i => $item) :
				if ($item->params->get('access-edit') OR $item->params->get('access-delete')) :
					$show_actions = true;
					continue;
				endif;
			endforeach;
		endif;
	?>
	<form action="<?php echo JFilterOutput::ampReplace(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
		<?php if (($this->params->get('show_[%%compobject%%]_filter_field') != '' AND $this->params->get('show_[%%compobject%%]_filter_field') != 'hide')) :?>
			<div class="filter-search">
				<?php if ($this->params->get('show_[%%compobject%%]_filter_field') != '' AND $this->params->get('show_[%%compobject%%]_filter_field') != 'hide') :?>
					<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_'.$this->params->get('show_[%%compobject%%]_filter_field').'_FILTER_LABEL'); ?>" />
				<?php endif; ?>	
				[%%IF GENERATE_CATEGORIES%%]
				<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', '[%%com_architectcomp%%]'), 'value', 'text', $this->state->get('filter.category_id'));?>
				</select>
				[%%ENDIF GENERATE_CATEGORIES%%]											
				[%%FOREACH FILTER_FIELD%%]
				<?php if ($this->params->get('list_show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]',0)) : ?>
					<select name="filter_[%%FIELD_CODE_NAME%%]" class="inputbox" onchange="this.form.submit()">
							[%%IF FIELD_FILTER_LINK%%]
					<option value=""><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_[%%FIELD_FOREIGN_OBJECT_UPPER%%]');?></option>
					<?php echo JHtml::_('select.options', $this->[%%FIELD_FOREIGN_OBJECT_PLURAL%%], 'value', 'text', $this->state->get('filter.[%%FIELD_CODE_NAME%%]'));?>
							[%%ELSE FIELD_FILTER_LINK%%]
					<option value=""><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_[%%FIELD_CODE_NAME_UPPER%%]');?></option>
					<?php echo JHtml::_('select.options', $this->[%%FIELD_CODE_NAME%%]_values, 'value', 'text', $this->state->get('filter.[%%FIELD_CODE_NAME%%]'));?>
							[%%ENDIF FIELD_FILTER_LINK%%]
					</select>
				<?php endif; ?>	
				[%%ENDFOR FILTER_FIELD%%]					
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_[%%compobject%%]_pagination_limit')) : ?>
			<div class="display-limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		<?php endif; ?>			
		<?php if (empty($this->items)) : ?>

			<?php if ($this->params->get('show_no_[%%compobjectplural%%]',1)) : ?>
			<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_NO_ITEMS'); ?></p>
			<?php endif; ?>

		<?php else : ?>
			<table class="table table-striped" id="[%%compobjectplural%%]">
			<?php if ($this->params->get('show_[%%compobject%%]_headings')) :?>
			<thead>
				<tr>
					<th width="1%" style="display:none;">
					</th>				
					[%%IF INCLUDE_NAME%%]
					<th class="list-name" id="tableOrderingname">
					<?php  echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_HEADING_NAME', 'a.name', $list_dirn, $list_order) ; ?>
					</th>
					[%%ENDIF INCLUDE_NAME%%]
					<?php if ($date = $this->params->get('list_show_[%%compobject%%]_date')) : ?>
						<th class="list-date" id="tableOrderingdate">
							<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_FIELD_'.JString::strtoupper($date).'_LABEL', 'a.'.$date, $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>

					[%%IF INCLUDE_CREATED%%]
					<?php if ($this->params->get('list_show_[%%compobject%%]_created_by',0)) : ?>
						<th class="list-created_by" id="tableOrderingcreated_by">
							<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_HEADING_CREATED_BY', 'created_by_name', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>
					[%%ENDIF INCLUDE_CREATED%%]
					[%%IF INCLUDE_HITS%%]
					<?php if ($this->params->get('list_show_[%%compobject%%]_hits',0)) : ?>
						<th class="list-hits" id="tableOrderinghits">
						<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_HEADING_HITS', 'a.hits', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>
					[%%ENDIF INCLUDE_HITS%%]			
					[%%FOREACH FILTER_FIELD%%] 
					<?php if ($this->params->get('list_show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]',0)) : ?>
						<th class="list-[%%FIELD_CODE_NAME%%]" id="tableOrdering[%%FIELD_CODE_NAME%%]">
								[%%IF FIELD_FILTER%%]
									[%%IF FIELD_LINK%%]
						<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL', '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]', $list_dirn, $list_order); ?>
									[%%ELSE FIELD_LINK%%]
						<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL', 'a.[%%FIELD_CODE_NAME%%]', $list_dirn, $list_order); ?>
									[%%ENDIF FIELD_LINK%%]						
								[%%ELSE FIELD_FILTER%%]
									[%%IF FIELD_ORDER%%]
						<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL', 'a.[%%FIELD_CODE_NAME%%]', $list_dirn, $list_order); ?>
									[%%ELSE FIELD_ORDER%%]
						<?php echo JTEXT::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL'); ?>
									[%%ENDIF FIELD_ORDER%%]						
								[%%ENDIF FIELD_FILTER%%]
						</th>
					<?php endif; ?>	
					[%%ENDFOR FILTER_FIELD%%]
					[%%IF INCLUDE_ORDERING%%]
					<?php if ($this->params->get('list_show_[%%compobject%%]_ordering',0)) : ?>
						<th width="10%">
							<?php echo JHtml::_('grid.sort',  '[%%COM_ARCHITECTCOMP%%]_HEADING_ORDERING', 'a.ordering', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>	
					[%%ENDIF INCLUDE_ORDERING%%]
					[%%IF INCLUDE_ASSETACL%%]
					<?php if ($show_actions) : ?>
					[%%ENDIF INCLUDE_ASSETACL%%]								
						<th width="12%" class="list-actions">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_HEADING_ACTIONS'); ?>						
						</th> 					
					[%%IF INCLUDE_ASSETACL%%]
					<?php endif; ?>
					[%%ENDIF INCLUDE_ASSETACL%%]									
				</tr>
			</thead>
			<?php endif; ?>

			<tbody>

				<?php foreach ($this->items as $i => $item) :
				
					[%%IF INCLUDE_ASSETACL%%]
					$can_edit	= $item->params->get('access-edit');
			
					$can_delete	= $item->params->get('access-delete');

					[%%ENDIF INCLUDE_ASSETACL%%]
							
				?>			
					<?php $params = $item->params; ?>		

					[%%IF INCLUDE_STATUS%%]
					<?php if ($item->state == 0) : ?>
						<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
					<?php else: ?>
						<tr class="cat-list-row<?php echo $i % 2; ?>" >
					<?php endif; ?>
					[%%ELSE INCLUDE_STATUS%%]
						<tr class="cat-list-row<?php echo $i % 2; ?>" >
					[%%ENDIF INCLUDE_STATUS%%]				
					<td class="center" style="display:none;">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>				
					[%%IF INCLUDE_ACCESS%%]
					<?php if (in_array($item->access, $user->getAuthorisedViewLevels()) OR $params->get('show_[%%compobject%%]_noauth')) : ?>
					[%%ENDIF INCLUDE_ACCESS%%]
					[%%IF INCLUDE_NAME%%]
						<td class="list-name">
						[%%IF GENERATE_CATEGORIES%%]		 
							[%%IF INCLUDE_LANGUAGE%%]
							<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																									$item->catid, 
																									$item->language,
																									$layout,									
																									$params->get('keep_[%%compobject%%]_itemid'))); ?>">
							[%%ELSE INCLUDE_LANGUAGE%%]
							<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																									$item->catid,								
																									$layout,									
																									$params->get('keep_[%%compobject%%]_itemid'))); ?>">
							[%%ENDIF INCLUDE_LANGUAGE%%]
						[%%ELSE GENERATE_CATEGORIES%%]
							[%%IF INCLUDE_LANGUAGE%%]
							<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																									$item->language,									
																									$layout,									
																									$params->get('keep_[%%compobject%%]_itemid'))); ?>">
							[%%ELSE INCLUDE_LANGUAGE%%]
							<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																									$layout,									
																									$params->get('keep_[%%compobject%%]_itemid'))); ?>">
							[%%ENDIF INCLUDE_LANGUAGE%%]	
						[%%ENDIF GENERATE_CATEGORIES%%]						
							<?php echo $this->escape($item->name); ?></a>
						</td>
					[%%ENDIF INCLUDE_NAME%%]

						<?php if ($this->params->get('list_show_[%%compobject%%]_date')) : ?>
						<td class="list-date">
							<?php echo JHTML::_('date',$item->display_date, $this->escape(
							$this->params->get('[%%compobject%%]_date_format', JText::_('DATE_FORMAT_LC3')))); ?>
						</td>
						<?php endif; ?>

					[%%IF INCLUDE_CREATED%%]
						<?php if ($this->params->get('list_show_[%%compobject%%]_created_by',0)) : ?>
						<td class="createdby">
						<?php 
							$created_by =  $item->created_by;
							$created_by = ($item->created_by_name ? $item->created_by_name : $created_by);
							$created_by = ($item->created_by_alias ? $item->created_by_alias : $created_by);

							if (!empty($item->created_by )) :
								if ($this->params->get('link_[%%compobject%%]_created_by') == 1) :
									$created_by = JHTML::_('link', JRoute::_('index.php?option=com_users&view=profile&id='.$item->created_by), $created_by); 
								endif;
								if ($this->params->get('show_[%%compobject%%]_headings')) :
									echo $created_by;
								else :
									echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $created_by);
								endif;
							else:
								echo $empty;
							endif;								
						?>
					</td>
					<?php endif; ?>
					[%%ENDIF INCLUDE_CREATED%%]
					[%%IF INCLUDE_HITS%%]
					<?php if ($this->params->get('list_show_[%%compobject%%]_hits',0)) : ?>
					<td class="list-hits">
						<?php echo $this->escape($item->hits); ?>
					</td>
					<?php endif; ?>
					[%%ENDIF INCLUDE_HITS%%]
					[%%IF GENERATE_CATEGORIES%%]
					<?php if ($this->params->get('list_show_[%%compobject%%]_category',0)) : ?>
					<td class="list-category">
						<?php 
							if (!empty($item->category_title )) :
								echo $this->escape($item->category_title);
							else:
								echo $empty;
							endif;								
						?>
					</td>
					<?php endif; ?>
					[%%ENDIF GENERATE_CATEGORIES%%]
					[%%FOREACH OBJECT_FIELD%%] 
						[%%IF FIELD_NOT_REGISTRY%%]
							[%%IF FIELD_NOT_HIDDEN%%]
					<?php if ($this->params->get('list_show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]',0)) : ?>
						<td class="list-[%%FIELD_CODE_NAME%%]">
							<?php 
								[%%IF FIELD_LINK%%]
								if ($params->get('list_link_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) :
									[%%FIELD_SITE_LIST_LINKED_VALUE%%]
								else :
									[%%FIELD_SITE_LIST_VALUE%%]
								endif; 
								[%%ELSE FIELD_LINK%%]
								[%%FIELD_SITE_LIST_VALUE%%]
								[%%ENDIF FIELD_LINK%%]								
							?>
						</td>
					<?php endif; ?>
							[%%ENDIF FIELD_NOT_HIDDEN%%]
						[%%ENDIF FIELD_NOT_REGISTRY%%]
					[%%ENDFOR OBJECT_FIELD%%]
					[%%IF INCLUDE_ORDERING%%]
					<?php if ($this->params->get('list_show_[%%compobject%%]_ordering',0)) : ?>
						
						<td class="list-order">
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
										<span><?php echo $this->pagination->orderUpIcon($i,$condition_up,'[%%compobjectplural%%].orderup', 'JLIB_HTML_MOVE_UP', $save_order); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, '[%%compobjectplural%%].orderdown', 'JLIB_HTML_MOVE_DOWN', $save_order); ?></span>
									<?php elseif ($list_dirn == 'desc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, $condition_up, '[%%compobjectplural%%].orderdown', 'JLIB_HTML_MOVE_UP', $save_order); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, '[%%compobjectplural%%].orderup', 'JLIB_HTML_MOVE_DOWN', $save_order); ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $disabled = $save_order ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="1" value="<?php echo $item->ordering;?>" <?php echo $disabled;?> class="text-area-order" />
							[%%IF INCLUDE_ASSETACL%%]
							<?php else : ?>
								<?php echo $item->ordering; ?>
							<?php endif; ?>
							[%%ENDIF INCLUDE_ASSETACL%%]
						</td>
					<?php endif; ?>
					
					[%%ENDIF INCLUDE_ORDERING%%]					
					[%%IF INCLUDE_ASSETACL%%]
					<?php if ($show_actions) : ?>
					[%%ENDIF INCLUDE_ASSETACL%%]						
						<td class="list-actions">
							[%%IF INCLUDE_ASSETACL%%]
							<?php if ($can_edit OR $can_delete ) : ?>
							[%%ENDIF INCLUDE_ASSETACL%%]						
								<ul class="actions">
									[%%IF INCLUDE_ASSETACL%%]
									<?php if ($can_edit ) : ?>
									[%%ENDIF INCLUDE_ASSETACL%%]
										<li class="edit-icon">
											<?php echo JHtml::_('[%%compobject%%]icon.edit',$item, $params); ?>
										</li>
									[%%IF INCLUDE_ASSETACL%%]
									<?php endif; ?>					
									<?php if ($can_delete) : ?>
									[%%ENDIF INCLUDE_ASSETACL%%]
										<li class="delete-icon">
											<?php echo JHtml::_('[%%compobject%%]icon.delete',$item, $params); ?>
										</li>
									[%%IF INCLUDE_ASSETACL%%]
									<?php endif; ?>					
									[%%ENDIF INCLUDE_ASSETACL%%]							
								</ul>
							[%%IF INCLUDE_ASSETACL%%]
							<?php endif; ?>
							[%%ENDIF INCLUDE_ASSETACL%%]							
						</td>															
					[%%IF INCLUDE_ASSETACL%%]
					<?php endif; ?>
					[%%ENDIF INCLUDE_ASSETACL%%]				
				[%%IF INCLUDE_ACCESS%%]
				<?php endif; ?>
				[%%ENDIF INCLUDE_ACCESS%%]
				</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
			<?php if (($this->params->def('show_[%%compobject%%]_pagination', 2) == 1  OR ($this->params->get('show_[%%compobject%%]_pagination') == 2)) AND ($this->pagination->get('pages.total') > 1)) : ?>
			<div class="pagination">

				<?php if ($this->params->def('show_[%%compobject%%]_pagination_results', 0)) : ?>
				<p class="counter">
						<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
				<?php endif; ?>

				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			<?php endif; ?>

			<div>
				<!-- @TODO add hidden inputs -->
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />			
				<input type="hidden" name="filter_order" value="" />
				<input type="hidden" name="filter_order_Dir" value="" />
				<input type="hidden" name="limitstart" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		<?php endif; ?>
		<?php // Code to add a link to submit an [%%compobject%%]. ?>
		<?php if ($this->params->get('show_[%%compobject%%]_add_link', 1)) : ?>
			[%%IF INCLUDE_ASSETACL%%]
			<?php if ($can_create) : ?>
				<?php echo JHtml::_('[%%compobject%%]icon.create', $this->params); ?>
			<?php  endif; ?>
			[%%ELSE INCLUDE_ASSETACL%%]
			<?php echo JHtml::_('[%%compobject%%]icon.create', $this->params); ?>
			[%%ENDIF INCLUDE_ASSETACL%%]
		<?php endif; ?>		
	</form>
</div>
