<?php
/**
 * @version 		$Id:$
 * @name			Selectfile (Release 1.0.0)
 * @author			 ()
 * @package			com_selectfile
 * @subpackage		com_selectfile.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default.php 408 2014-10-19 18:31:00Z BrianWade $
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

/*
 *	Add style sheets, javascript and behaviours here in the layout so they can be overridden, if required, in a template override 
 */

// Add css files for the selectfile component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_selectfile.css');
$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_sizes.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_selectfile-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_sizes-rtl.css');
}

// Add Javscript functions for field display
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

$layout		= $this->params->get('size_layout', 'default');

// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_selectfile' );
$empty = $component->params->get('default_empty_field', '');

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_SELECTFILE_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="selectfile sizes-list<?php echo $this->params->get('pageclass_sfx');?>">
	<?php if ($this->params->get('show_page_heading')): ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>

	<?php
		$show_actions = false;
		if ($this->params->get('show_size_icons',-1) >= 0) :
			foreach ($this->items as $i => $item) :
				if ($item->params->get('access-edit') OR $item->params->get('access-delete')) :
					$show_actions = true;
					continue;
				endif;
			endforeach;
		endif;
	?>
	<form action="<?php echo JFilterOutput::ampReplace(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
		<?php if (($this->params->get('show_size_filter_field') != '' AND $this->params->get('show_size_filter_field') != 'hide')) :?>
			<div class="filter-search">
				<?php if ($this->params->get('show_size_filter_field') != '' AND $this->params->get('show_size_filter_field') != 'hide') :?>
					<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_SELECTFILE_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_SELECTFILE_'.$this->params->get('show_size_filter_field').'_FILTER_LABEL'); ?>" />
				<?php endif; ?>	
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_size_pagination_limit')) : ?>
			<div class="display-limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		<?php endif; ?>	
		<div style="clear:both;"></div>				
		<?php if (empty($this->items)) : ?>

			<?php if ($this->params->get('show_no_sizes',1)) : ?>
			<p><?php echo JText::_('COM_SELECTFILE_SIZES_NO_ITEMS'); ?></p>
			<?php endif; ?>

		<?php else : ?>
			<table class="table table-striped" id="sizes">
			<?php if ($this->params->get('show_size_headings')) :?>
			<thead>
				<tr>
					<th width="1%" style="display:none;">
					</th>				
					<th class="list-name" id="tableOrderingname">
					<?php  echo JHtml::_('grid.sort', 'COM_SELECTFILE_HEADING_NAME', 'a.name', $list_dirn, $list_order) ; ?>
					</th>
					<?php if ($date = $this->params->get('list_show_size_date')) : ?>
						<th class="list-date" id="tableOrderingdate">
							<?php echo JHtml::_('grid.sort', 'COM_SELECTFILE_FIELD_'.JString::strtoupper($date).'_LABEL', 'a.'.$date, $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>

					<?php if ($this->params->get('list_show_size_created_by',0)) : ?>
						<th class="list-created_by" id="tableOrderingcreated_by">
							<?php echo JHtml::_('grid.sort', 'COM_SELECTFILE_HEADING_CREATED_BY', 'created_by_name', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>
					<?php if ($this->params->get('list_show_size_ordering',0)) : ?>
						<th width="10%">
							<?php echo JHtml::_('grid.sort',  'COM_SELECTFILE_HEADING_ORDERING', 'a.ordering', $list_dirn, $list_order); ?>
						</th>
					<?php endif; ?>	
						<th width="12%" class="list-actions">
							<?php echo JText::_('COM_SELECTFILE_HEADING_ACTIONS'); ?>						
						</th> 					
				</tr>
			</thead>
			<?php endif; ?>

			<tbody>

				<?php foreach ($this->items as $i => $item) :
				
							
				?>			
					<?php $params = $item->params; ?>		

					<?php if ($item->state == 0) : ?>
						<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
					<?php else: ?>
						<tr class="cat-list-row<?php echo $i % 2; ?>">
					<?php endif; ?>
					<td class="center" style="display:none;">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>				
					<?php if (in_array($item->access, $user->getAuthorisedViewLevels()) OR $params->get('show_size_noauth')) : ?>
					<td class="list-name">
						<a href="<?php echo JRoute::_(SelectfileHelperRoute::getSizeRoute($item->slug, 
																									$layout,									
																									$params->get('keep_size_itemid'))); ?>" 
																									>
								<?php echo $this->escape($item->name); ?>
						</a>
					</td>

					<?php if ($this->params->get('list_show_size_date')) : ?>
						<td class="list-date">
							<time datetime="<?php echo JHtml::_('date', $item->display_date, 'c'); ?>">
								<?php echo JHtml::_('date',$item->display_date, $this->escape($this->params->get('size_date_format', JText::_('DATE_FORMAT_LC3')))); ?>
							</time>
						</td>
					<?php endif; ?>
					<?php if ($this->params->get('list_show_size_created_by',0)) : ?>
						<td class="createdby">
							<?php 
								$created_by =  $item->created_by;
								$created_by = ($item->created_by_name ? $item->created_by_name : $created_by);
								$created_by = ($item->created_by_alias ? $item->created_by_alias : $created_by);
								if (!empty($item->created_by )) :
									if ($this->params->get('link_size_created_by') == 1) :
										$created_by = JHtml::_('link', JRoute::_('index.php?option=com_users&view=profile&id='.$item->created_by), $created_by); 
									endif;
									if ($this->params->get('show_size_headings')) :
										echo $created_by;
									else :
										echo JText::sprintf('COM_SELECTFILE_CREATED_BY', $created_by);
									endif;
								else:
									echo $empty;
								endif;								
							?>
						</td>
					<?php endif; ?>
					<?php if ($this->params->get('list_show_size_ordering',0)) : ?>
						
						<td class="list-order">
								<?php if ($save_order) : ?>
									<?php 
										$condition_up = true;
										$condition_down = true;
									?>										 
									<?php if ($list_dirn == 'asc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i,$condition_up,'sizes.orderup', 'JLIB_HTML_MOVE_UP', $save_order); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, 'sizes.orderdown', 'JLIB_HTML_MOVE_DOWN', $save_order); ?></span>
									<?php elseif ($list_dirn == 'desc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, $condition_up, 'sizes.orderdown', 'JLIB_HTML_MOVE_UP', $save_order); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, 'sizes.orderup', 'JLIB_HTML_MOVE_DOWN', $save_order); ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $disabled = $save_order ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="1" value="<?php echo $item->ordering;?>" <?php echo $disabled;?> class="text-area-order" />
						</td>
					<?php endif; ?>
					
						<td class="list-actions">
								<ul class="actions">
										<li class="edit-icon">
											<?php echo JHtml::_('sizeicon.edit',$item, $params); ?>
										</li>
										<li class="delete-icon">
											<?php echo JHtml::_('sizeicon.delete',$item, $params); ?>
										</li>
								</ul>
						</td>															
				<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
			<?php if (($this->params->def('show_size_pagination', 2) == 1  OR ($this->params->get('show_size_pagination') == 2)) AND ($this->pagination->get('pages.total') > 1)) : ?>
			<div class="pagination">

				<?php if ($this->params->def('show_size_pagination_results', 0)) : ?>
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
		<?php // Code to add a link to submit an size. ?>
		<?php if ($this->params->get('show_size_add_link', 1)) : ?>
			<?php echo JHtml::_('sizeicon.create', $this->params); ?>
		<?php endif; ?>		
	</form>
</div>
