<?php
/**
 * @version 		$Id: default.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default.php 34 2014-03-12 12:11:19Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
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

$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$user_id		= $user->get('id');
$list_order	= $this->state->get('list.ordering');
$list_dirn	= $this->state->get('list.direction');
$search		= $this->escape($this->state->get('filter.search',''));
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_componentarchitect' );
$empty = $component->params->get('default_empty_field', '');

$save_order	= ($list_order=='ordering' OR $list_order=='a.ordering');

if ($save_order AND version_compare(JVERSION, '3.0', 'ge'))
{
	$save_ordering_url = 'index.php?option=com_componentarchitect&task=fields.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'field-list', 'adminForm', strtolower($list_dirn), $save_ordering_url);
}

$sort_fields = $this->getSortFields();
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_COMPONENTARCHITECT_WARNING_NOSCRIPT'); ?><p>
</noscript>
<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
<div id="nojquerywarning">
	<p style="color: red;"><?php echo JText::_('COM_COMPONENTARCHITECT_WARNING_NOJQUERY'); ?><p>
</div>
<script type="text/javascript">
	if(jQuery)
	{
		jQuery('#nojquerywarning').css('display','none');
	}
</script>
<?php endif; ?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sort_table");
		direction = document.getElementById("direction_table");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $list_order; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_componentarchitect&view=fields'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
	
	<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
		<div class="js-stools clearfix">
	<?php else : ?>
		<fieldset id="filter-bar" class="btn-toolbar">
	<?php endif; ?>	
			<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
				<div class="clearfix"></div>
				<div class="js-stools-container-bar">
			<?php else : ?>
				<div class="filter-search fltlft">
			<?php endif; ?>						
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="btn-wrapper input-append">
							<input type="text" name="filter_search" id="filter_search" value="<?php echo $search; ?>"  placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
							<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
								<i class="icon-search"></i>
							</button>
						</div>
					<?php else : ?>
						<input type="text" name="filter_search" id="filter_search" value="<?php echo $search; ?>"  placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
						<button type="submit" class="btn hasTip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
							<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
					<?php endif; ?>	
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="btn-wrapper hidden-phone">
							<button type="button" class="btn hasTooltip js-stools-btn-filter btn-primary" title="" data-original-title="Filter the list items">
								<?php echo JText::_('JSEARCH_TOOLS'); ?><i class="caret"></i>
							</button>
						</div>
					<?php endif ; ?>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="btn-wrapper">
							<button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
								<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
							</button>
						</div>						
					<?php else : ?>
						<button type="button" class="btn hasTip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
							<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
						</button>
					<?php endif; ?>						
					<div class="clearfix"></div>				
				</div>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<div class="js-stools-container-list hidden-phone hidden-tablet shown" style="">		
					<div class="btn-group pull-right">
						<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="btn-group pull-right">
							<label for="direction_table" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
							<select name="direction_table" id="direction_table" class="input-medium" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
								<option value="asc" <?php if ($list_dirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
								<option value="desc" <?php if ($list_dirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');  ?></option>
							</select>
						</div>
						<div class="btn-group pull-right">
							<label for="sort_table" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
							<select name="sort_table" id="sort_table" class="input-medium js-stools-field-order" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
								<?php echo JHtml::_('select.options', $sort_fields, 'value', 'text', $list_order); ?>
							</select>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
				<div class="clearfix"></div>
				<div class="js-stools-container-filters hidden-phone clearfix shown" style="display: block;">
			<?php else : ?>
				<div class="filter-select fltrt">
			<?php endif; ?>				
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="js-stools-field-filter">	
					<?php endif; ?>				
							<select name="filter_component_id" class="input-medium" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_SELECT_C1_COMPONENT');?></option>
								<?php echo JHtml::_('select.options', $this->c1_components, 'value', 'text', $this->state->get('filter.component_id'));?>
							</select>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						</div>	
					<?php endif; ?>				
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="js-stools-field-filter">	
					<?php endif; ?>				
							<select name="filter_component_object_id" class="input-medium" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_SELECT_O_COMPONENT_OBJECT');?></option>
								<?php echo JHtml::_('select.options', $this->o_componentobjects, 'value', 'text', $this->state->get('filter.component_object_id'));?>
							</select>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						</div>	
					<?php endif; ?>				
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="js-stools-field-filter">	
					<?php endif; ?>				
							<select name="filter_fieldset_id" class="input-medium" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_SELECT_F_FIELDSET');?></option>
								<?php echo JHtml::_('select.options', $this->f_fieldsets, 'value', 'text', $this->state->get('filter.fieldset_id'));?>
							</select>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						</div>	
					<?php endif; ?>				
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="js-stools-field-filter">	
					<?php endif; ?>				
							<select name="filter_predefined_field" class="input-medium" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_SELECT_PREDEFINED_FIELD');?></option>
								<?php echo JHtml::_('select.options', $this->predefined_field_values, 'value', 'text', $this->state->get('filter.predefined_field'));?>
							</select>	
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						</div>	
					<?php endif; ?>				
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="js-stools-field-filter">	
					<?php endif; ?>				
							<select name="filter_fieldtype_id" class="input-medium" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_SELECT_FT_FIELD_TYPE');?></option>
								<?php echo JHtml::_('select.options', $this->ft_fieldtypes, 'value', 'text', $this->state->get('filter.fieldtype_id'));?>
							</select>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						</div>	
					<?php endif; ?>				
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="js-stools-field-filter">	
					<?php endif; ?>				
							<select name="filter_foreign_object_id" class="input-medium" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_SELECT_O1_COMPONENT_OBJECT');?></option>
								<?php echo JHtml::_('select.options', $this->o1_componentobjects, 'value', 'text', $this->state->get('filter.foreign_object_id'));?>
							</select>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						</div>	
					<?php endif; ?>				
					
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						<div class="js-stools-field-filter">	
					<?php endif; ?>	
							<select name="filter_created_by" class="input-medium" onchange="this.form.submit()">
								<option value=""><?php echo JText::_('COM_COMPONENTARCHITECT_SELECT_CREATED_BY');?></option>
								<?php echo JHtml::_('select.options', $this->creators, 'value', 'text', $this->state->get('filter.created_by'));?>
							</select>
					<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
						</div>	
					<?php endif; ?>				
				</div>		
	<?php if (version_compare(JVERSION, '3.2', 'ge')) : ?>
		</div>
	<?php else : ?>
		</fieldset/>
	<?php endif; ?>						
	
	<div class="clearfix clr"> </div>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="<?php echo version_compare(JVERSION, '3.0', 'ge') ? 'table table-striped':'adminlist'; ?>" id="field-list">
			<thead>
				<tr>
					<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $list_dirn, $list_order, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>	
					<?php endif; ?>
					<th width="1%">
						<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
								<?php echo JHtml::_('grid.checkall'); ?>
						<?php else : ?>
								<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
						<?php endif; ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort',  'COM_COMPONENTARCHITECT_HEADING_NAME', 'a.name', $list_dirn, $list_order); ?>
					</th>
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_COMPONENTARCHITECT_FIELDS_HEADING_COMPONENT_ID', 'c1_component_name', $list_dirn, $list_order); ?>
					</th>	
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_COMPONENTARCHITECT_FIELDS_HEADING_COMPONENT_OBJECT_ID', 'o_component_object_name', $list_dirn, $list_order); ?>
					</th>	
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_COMPONENTARCHITECT_FIELDS_HEADING_FIELDSET_ID', 'f_fieldset_name', $list_dirn, $list_order); ?>
					</th>	
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_COMPONENTARCHITECT_FIELDS_HEADING_PREDEFINED_FIELD', 'a.predefined_field', $list_dirn, $list_order); ?>
					</th>	
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_COMPONENTARCHITECT_FIELDS_HEADING_FIELDTYPE_ID', 'ft_field_type_name', $list_dirn, $list_order); ?>
					</th>	
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_COMPONENTARCHITECT_FIELDS_HEADING_FOREIGN_OBJECT_ID', 'o1_component_object_name', $list_dirn, $list_order); ?>
					</th>	

					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort',  'COM_COMPONENTARCHITECT_HEADING_CREATED_BY', 'a.created_by', $list_dirn, $list_order); ?>
					</th>
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_COMPONENTARCHITECT_HEADING_CREATED', 'a.created', $list_dirn, $list_order); ?>
					</th>		
					<?php if (version_compare(JVERSION, '3.1', 'lt')) :?>
						<th width="10%" class="center">
							<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $list_dirn, $list_order); ?>
								<?php if ($save_order): ?>					
									<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'fields.saveorder'); ?>
								<?php endif;?>
						</th>
						<?php endif; ?>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $list_dirn, $list_order); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :

				$item->max_ordering = 0; //??
				$ordering	= ($list_order=='ordering' OR $list_order=='a.ordering');
				$can_change = true;
				$can_checkin	= $item->checked_out == $user_id OR $item->checked_out == 0;
							
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
						<td class="order nowrap center hidden-phone">
							<?php if ($can_change) :
								$disable_class_name = '';
								$disabled_label	  = '';

								if (!$save_order) :
									$disabled_label    = JText::_('JORDERINGDISABLED');
									$disable_class_name = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler hasTooltip <?php echo $disable_class_name; ?>" title="<?php echo $disabled_label; ?>">
									<i class="icon-menu"></i>
								</span>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php else : ?>
								<span class="sortable-handler inactive" >
									<i class="icon-menu"></i>
								</span>
							<?php endif; ?>
						</td>
					<?php endif; ?>
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="nowrap has-context">
						<div class="pull-left fltlft">
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'fields.', $can_checkin); ?>
						<?php endif; ?>

							<a href="<?php echo JRoute::_('index.php?option=com_componentarchitect&task=field.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?></a>
						</div>
					</td>
					<td class="nowrap small center hidden-phone">
						<?php 
								echo '<a href="index.php?option=com_componentarchitect&task=component.edit&id='.$item->component_id.'">'.JString::trim($item->c1_component_name).'</a>';
						?>				
					</td>	
					<td class="nowrap small center hidden-phone">
						<?php 
								echo '<a href="index.php?option=com_componentarchitect&task=componentobject.edit&id='.$item->component_object_id.'">'.JString::trim($item->o_component_object_name).'</a>';
						?>				
					</td>	
					<td class="nowrap small center hidden-phone">
						<?php 
								echo '<a href="index.php?option=com_componentarchitect&task=fieldset.edit&id='.$item->fieldset_id.'">'.JString::trim($item->f_fieldset_name).'</a>';
						?>				
					</td>	
					<td class="nowrap small center hidden-phone">
						<?php 
								switch ($item->predefined_field) :
									case '0':
										echo JText::_('JNO');
										break;
									case '1':
										echo JText::_('JYES');
										break;
								endswitch;
						?>				
					</td>	
					<td class="nowrap small center hidden-phone">
						<?php 
								echo '<a href="index.php?option=com_componentarchitect&task=fieldtype.edit&id='.$item->fieldtype_id.'">'.JString::trim($item->ft_field_type_name).'</a>';
						?>				
					</td>	
					<td class="nowrap small center hidden-phone">
						<?php 
								echo '<a href="index.php?option=com_componentarchitect&task=componentobject.edit&id='.$item->foreign_object_id.'">'.JString::trim($item->o1_component_object_name).'</a>';
						?>				
					</td>	
						
					<td class="nowrap small center hidden-phone">
						<?php if ($item->created_by_alias) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_by); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
								<?php echo $this->escape($item->created_by_name); ?>
							</a>
							<p class="smallsub"> <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->created_by_alias)); ?></p>
						<?php else : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_by); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
								<?php echo $this->escape($item->created_by_name); ?>
							</a>
						<?php endif; ?>
					</td>
					<td class="nowrap small center hidden-phone">
						<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
					</td>	

					<?php if (version_compare(JVERSION, '3.1', 'lt')) :?>
						<td class="order">
								<?php if ($save_order) : ?>
									<?php 
										$condition_up = true;
										$condition_down = true;
										if ($item->component_id != @$this->items[$i-1]->component_id) :
											$condition_up = false; 
										endif;
										if ($item->component_id != @$this->items[$i+1]->component_id) :
											$condition_down = false; 
										endif;	
										if ($item->component_object_id != @$this->items[$i-1]->component_object_id) :
											$condition_up = false; 
										endif;
										if ($item->component_object_id != @$this->items[$i+1]->component_object_id) :
											$condition_down = false; 
										endif;	
										if ($item->fieldset_id != @$this->items[$i-1]->fieldset_id) :
											$condition_up = false; 
										endif;
										if ($item->fieldset_id != @$this->items[$i+1]->fieldset_id) :
											$condition_down = false; 
										endif;	
										if ($item->foreign_object_id != @$this->items[$i-1]->foreign_object_id) :
											$condition_up = false; 
										endif;
										if ($item->foreign_object_id != @$this->items[$i+1]->foreign_object_id) :
											$condition_down = false; 
										endif;	
									?>
									<div class="order-icons">										 
									<?php if ($list_dirn == 'asc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i,$condition_up,'fields.orderup', 'JLIB_HTML_MOVE_UP', $save_order); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, 'fields.orderdown', 'JLIB_HTML_MOVE_DOWN', $save_order); ?></span>
									<?php elseif ($list_dirn == 'desc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, $condition_up, 'fields.orderdown', 'JLIB_HTML_MOVE_UP', $save_order); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total,$condition_down, 'fields.orderup', 'JLIB_HTML_MOVE_DOWN', $save_order); ?></span>
									<?php endif; ?>
									</div>
								<?php endif; ?>
								<?php $disabled = $save_order ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled;?> class="text-area-order" />
						</td>
					<?php endif; ?>

					<td class="center">
						<?php echo $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="12">
						<?php echo $this->pagination->getListFooter();?>
					</td>
				</tr>
			</tfoot>	
		</table>
	<?php endif; ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="form_id" id="form_id" value="adminForm" />
		<input type="hidden" name="filter_order" value="<?php echo $list_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $list_dirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
