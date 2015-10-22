<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default.php 418 2014-10-22 14:42:36Z BrianWade $
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

$layout		= $this->params->get('item_layout', 'default');

$can_create	= $user->authorise('core.create', 'com_example');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_example' );
$empty = $component->params->get('default_empty_field', '');
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_EXAMPLE_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="example items-list<?php echo $this->params->get('pageclass_sfx');?>">
	<?php if ($this->params->get('show_page_heading')): ?>
		<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
	<?php if (empty($this->items)) : ?>

		<?php if ($this->params->get('show_no_items',1)) : ?>
		<p><?php echo JText::_('COM_EXAMPLE_ITEMS_NO_ITEMS'); ?></p>
		<?php endif; ?>

	<?php else : ?>
		<?php
			$show_actions = false;
			if ($this->params->get('show_item_icons',-1) >= 0) :
				foreach ($this->items as $i => $item) :
					if ($item->params->get('access-edit') OR $item->params->get('access-delete')) :
						$show_actions = true;
						continue;
					endif;
				endforeach;
			endif;
		?>
		<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
			<?php if (($this->params->get('item_filter_field') != '' AND $this->params->get('item_filter_field') != 'hide') OR $this->params->get('show_item_pagination_limit')) :?>
				<fieldset class="filters">
					<legend class="hidelabeltxt">
						<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
					</legend>
					<div class="ca-filter-search">
						<?php if ($this->params->get('item_filter_field') != '' AND $this->params->get('item_filter_field') != 'hide') :?>
							<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_EXAMPLE_'.$this->params->get('item_filter_field').'_FILTER_LABEL').'&#160;'; ?></label>
							<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_EXAMPLE_FILTER_SEARCH_DESC'); ?>" />
							<button type="submit">
								<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
							<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
								<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>							
						<?php endif; ?>						
						<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
							<?php echo JHtml::_('select.options', JHtml::_('categoryexample.options', 'com_example'), 'value', 'text', $this->state->get('filter.category_id'));?>
						</select>
					</div>

					<?php if ($this->params->get('show_item_pagination_limit')) : ?>
						<div class="display-limit">
							<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
					<?php endif; ?>			
				</fieldset>	
			<?php endif; ?>



			<table class="items">
			<?php if ($this->params->get('show_item_headings')) :?>
			<thead>
				<tr>
					<th width="1%" style="display:none;">
					</th>				
					<th class="list-name" id="tableOrderingname">
					<?php  echo JHTML::_('grid.sort', 'COM_EXAMPLE_HEADING_NAME', 'a.name', $list_dirn, $list_order) ; ?>
					</th>
					<?php if ($date = $this->params->get('list_show_item_date')) : ?>
						<th class="list-date" id="tableOrderingdate">
						<?php echo JHTML::_('grid.sort', 'COM_EXAMPLE_FIELD_'.JString::strtoupper($date).'_LABEL', 'a.'.$date, $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>

					<?php if ($this->params->get('list_show_item_creator',0)) : ?>
						<th class="list-creator" id="tableOrderingcreated_by">
						<?php echo JHTML::_('grid.sort', 'COM_EXAMPLE_HEADING_CREATED_BY', 'created_by_name', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>
					<?php if ($this->params->get('list_show_item_hits',0)) : ?>
						<th class="list-hits" id="tableOrderinghits">
						<?php echo JHTML::_('grid.sort', 'COM_EXAMPLE_HEADING_HITS', 'a.hits', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>
					<?php if ($this->params->get('list_show_item_ordering',0)) : ?>
						<th width="10%">
							<?php echo JHtml::_('grid.sort',  'COM_EXAMPLE_HEADING_ORDERING', 'a.ordering', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>	
					<?php if ($show_actions) : ?>
						<th width="8%" class="list-actions">
						</th> 					
					<?php endif; ?>
				</tr>
			</thead>
			<?php endif; ?>
			<tbody>
				<?php foreach ($this->items as $i => $item) :
					$can_edit	= $item->params->get('access-edit');
					$can_delete	= $item->params->get('access-delete');
				?>			
					<?php $params = $item->params; ?>		

					<?php if ($item->state == 0) : ?>
						<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
					<?php else: ?>
						<tr class="cat-list-row<?php echo $i % 2; ?>" >
					<?php endif; ?>
					<td class="center" style="display:none;">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>				
					<?php if (in_array($item->access, $user->getAuthorisedViewLevels()) OR $params->get('show_item_noauth')) : ?>
						<td class="list-name">
							<a href="<?php echo JRoute::_(ExampleHelperRoute::getItemRoute($item->slug, 
																									$item->catid, 
																									$item->language,
																									$layout,									
																									$params->get('keep_item_itemid'))); ?>">
							<?php echo $this->escape($item->name); ?></a>
						</td>

						<?php if ($this->params->get('list_show_item_date')) : ?>
						<td class="list-date">
							<?php echo JHTML::_('date',$item->display_date, $this->escape(
							$this->params->get('item_date_format', JText::_('DATE_FORMAT_LC3')))); ?>
						</td>
						<?php endif; ?>

						<?php if ($this->params->get('list_show_item_creator',0)) : ?>
						<td class="createdby">
							<?php $creator =  $item->created_by ?>
							<?php $creator = ($item->created_by_name ? $item->created_by_name : $creator);?>

							<?php if (!empty($item->created_by ) AND  $this->params->get('link_item_creator') == 1):?>
								<?php $creator = JHTML::_(
										'link',
										JRoute::_('index.php?option=com_users&view=profile&id='.$item->created_by),
										$creator
								); ?>
							<?php endif;?>
							<?php if ($this->params->get('show_item_headings')) :?>
								<?php echo $creator; ?>
							<?php else : ?>									
								<?php echo JText::sprintf('COM_EXAMPLE_CREATED_BY', $creator); ?>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if ($this->params->get('list_show_item_hits',0)) : ?>
						<td class="list-hits">
							<?php echo $this->escape($item->hits); ?>
						</td>
						<?php endif; ?>

						<?php if ($this->params->get('list_show_item_ordering',0)) : ?>
							
							<td class="list-order">
								<?php echo $item->ordering; ?>
							</td>
						<?php endif; ?>
						
						<?php if ($show_actions) : ?>
							<td class="list-actions">
								<?php if ($can_edit OR $can_delete ) : ?>
									<ul class="actions">
										<?php if ($can_edit ) : ?>
											<li class="edit-icon">
												<?php echo JHtml::_('itemicon.edit',$item, $params); ?>
											</li>
										<?php endif; ?>					
										<?php if ($can_delete) : ?>
											<li class="delete-icon">
												<?php echo JHtml::_('itemicon.delete',$item, $params); ?>
											</li>
										<?php endif; ?>					
									</ul>
								<?php endif; ?>
							</td>															
						<?php endif; ?>
					<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<?php if (($this->params->def('show_item_pagination', 2) == 1  OR ($this->params->get('show_item_pagination') == 2)) AND ($this->pagination->get('pages.total') > 1)) : ?>
			<div class="pagination">

				<?php if ($this->params->def('show_item_pagination_results', 0)) : ?>
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

	<?php // Code to add a link to submit an item. ?>
	<?php if ($this->params->get('show_item_add_link', 1)) : ?>
		<?php if ($can_create) : ?>
			<?php echo JHtml::_('itemicon.create', $this->params); ?>
		<?php  endif; ?>
	<?php endif; ?>	
</div>
