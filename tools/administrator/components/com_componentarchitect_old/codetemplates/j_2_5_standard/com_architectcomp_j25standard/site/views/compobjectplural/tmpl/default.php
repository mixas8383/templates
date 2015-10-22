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
 * @version			$Id: default.php 418 2014-10-22 14:42:36Z BrianWade $
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
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="[%%architectcomp%%] [%%compobjectplural%%]-list<?php echo $this->params->get('pageclass_sfx');?>">
	<?php if ($this->params->get('show_page_heading')): ?>
		<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
	<?php if (empty($this->items)) : ?>

		<?php if ($this->params->get('show_no_[%%compobjectplural%%]',1)) : ?>
		<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_NO_ITEMS'); ?></p>
		<?php endif; ?>

	<?php else : ?>
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
		<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
			<?php if (($this->params->get('[%%compobject%%]_filter_field') != '' AND $this->params->get('[%%compobject%%]_filter_field') != 'hide') OR $this->params->get('show_[%%compobject%%]_pagination_limit')) :?>
				<fieldset class="filters">
					<legend class="hidelabeltxt">
						<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
					</legend>
					<div class="ca-filter-search">
						<?php if ($this->params->get('[%%compobject%%]_filter_field') != '' AND $this->params->get('[%%compobject%%]_filter_field') != 'hide') :?>
							<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_'.$this->params->get('[%%compobject%%]_filter_field').'_FILTER_LABEL').'&#160;'; ?></label>
							<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FILTER_SEARCH_DESC'); ?>" />
							<button type="submit">
								<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
							<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
								<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>							
						<?php endif; ?>						
						[%%IF GENERATE_CATEGORIES%%]
						<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('category[%%architectcomp%%].options', '[%%com_architectcomp%%]'), 'value', 'text', $this->state->get('filter.category_id'));?>
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

					<?php if ($this->params->get('show_[%%compobject%%]_pagination_limit')) : ?>
						<div class="display-limit">
							<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
					<?php endif; ?>			
				</fieldset>	
			<?php endif; ?>



			<table class="[%%compobjectplural%%]">
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
					<?php if ($this->params->get('list_show_[%%compobject%%]_creator',0)) : ?>
						<th class="list-creator" id="tableOrderingcreated_by">
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
						<th width="8%" class="list-actions">
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
						<?php if ($this->params->get('list_show_[%%compobject%%]_creator',0)) : ?>
						<td class="createdby">
							<?php $creator =  $item->created_by ?>
							<?php $creator = ($item->created_by_name ? $item->created_by_name : $creator);?>

							<?php if (!empty($item->created_by ) AND  $this->params->get('link_[%%compobject%%]_creator') == 1):?>
								<?php $creator = JHTML::_(
										'link',
										JRoute::_('index.php?option=com_users&view=profile&id='.$item->created_by),
										$creator
								); ?>
							<?php endif;?>
							<?php if ($this->params->get('show_[%%compobject%%]_headings')) :?>
								<?php echo $creator; ?>
							<?php else : ?>									
								<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $creator); ?>
							<?php endif; ?>
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

						[%%FOREACH FILTER_FIELD%%] 
						<?php if ($this->params->get('list_show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]',0)) : ?>
							<td class="list-[%%FIELD_CODE_NAME%%]">
								<?php 
									[%%FIELD_SITE_LIST_VALUE%%] 
								?>
							</td>
						<?php endif; ?>
						[%%ENDFOR FILTER_FIELD%%] 
						[%%IF INCLUDE_ORDERING%%]
						<?php if ($this->params->get('list_show_[%%compobject%%]_ordering',0)) : ?>
							
							<td class="list-order">
								<?php echo $item->ordering; ?>
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
		</form>
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
</div>
