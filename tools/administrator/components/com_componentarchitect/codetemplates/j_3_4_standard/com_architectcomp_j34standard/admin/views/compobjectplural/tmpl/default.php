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
 * @version			$Id: default.php 408 2014-10-19 18:31:00Z BrianWade $
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
/*
 *	Add style sheets, javascript and behaviours here in the layout so they can be overridden, if required, in a template override 
 */
	
// Include custom admin css
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/admin.css');

// Add Javscript functions
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Set some basic options
$data['options'] = array(
	'filtersHidden'       => false,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldName'  => 'filter_order'
	);

// Load search tools
JHtml::_('searchtools.form', '#adminForm', $data['options']);		 
/*
 *	Initialise values for the layout 
 */	
$app		= JFactory::getApplication();
$user		= JFactory::getUser();
$user_id	= $user->get('id');
$list_order	= $this->state->get('list.ordering');
$list_dirn	= $this->state->get('list.direction');
$archived	= $this->state->get('filter.state') == 2 ? true : false;
$trashed	= $this->state->get('filter.state') == -2 ? true : false;
$search		= $this->state->get('filter.search','');

// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$empty = $component->params->get('default_empty_field', '');
[%%IF INCLUDE_ASSETACL%%]
	[%%IF INCLUDE_ORDERING%%]
$can_order	= $user->authorise('core.edit.state', '[%%com_architectcomp%%]');
	[%%ENDIF INCLUDE_ORDERING%%]
[%%ENDIF INCLUDE_ASSETACL%%]

[%%IF INCLUDE_ORDERING%%]
$save_order	= ($list_order=='ordering' OR $list_order=='a.ordering');

if ($save_order)
{
	$save_ordering_url = 'index.php?option=[%%com_architectcomp%%]&task=[%%compobjectplural%%].saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', '[%%compobject%%]-list', 'adminForm', strtolower($list_dirn), $save_ordering_url);
}

[%%ENDIF INCLUDE_ORDERING%%]
$sort_fields = $this->getSortFields();
[%%IF INCLUDE_LANGUAGE%%]
$assoc	= JLanguageAssociations::isEnabled();
[%%ENDIF INCLUDE_LANGUAGE%%]
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
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
<form action="<?php echo JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobjectplural%%]'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>

	<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
	?>	
	
	<div class="clearfix"> </div>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped" id="[%%compobject%%]-list">
			<thead>
				<tr>
					[%%IF INCLUDE_ORDERING%%]
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $list_dirn, $list_order, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>	
					[%%ENDIF INCLUDE_ORDERING%%]		
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					[%%IF INCLUDE_STATUS%%]
					<th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $list_dirn, $list_order); ?>
					</th>
					[%%ENDIF INCLUDE_STATUS%%]				
					[%%IF INCLUDE_NAME%%]
					<th>
						<?php echo JHtml::_('searchtools.sort',  '[%%COM_ARCHITECTCOMP%%]_HEADING_NAME', 'a.name', $list_dirn, $list_order); ?>
					</th>
					[%%ENDIF INCLUDE_NAME%%]
					[%%FOREACH FILTER_FIELD%%]
					<th width="10%" class="nowrap center hidden-phone">
						[%%IF FIELD_ORDER%%]
							[%%IF FIELD_LINK%%]
						<?php echo JHtml::_('searchtools.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_HEADING_[%%FIELD_CODE_NAME_UPPER%%]', '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]', $list_dirn, $list_order); ?>
							[%%ELSE FIELD_LINK%%]
						<?php echo JHtml::_('searchtools.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_HEADING_[%%FIELD_CODE_NAME_UPPER%%]', 'a.[%%FIELD_CODE_NAME%%]', $list_dirn, $list_order); ?>
							[%%ENDIF FIELD_LINK%%]
						[%%ELSE FIELD_ORDER%%]
						<?php echo JTEXT::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_HEADING_[%%FIELD_CODE_NAME_UPPER%%]'); ?>						
						[%%ENDIF FIELD_ORDER%%]						
					</th>	
					[%%ENDFOR FILTER_FIELD%%]			
					[%%IF GENERATE_CATEGORIES%%]
					<th width="10%" class="center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'category_title', $list_dirn, $list_order); ?>
					</th>
					[%%ENDIF GENERATE_CATEGORIES%%]				
					[%%IF INCLUDE_ACCESS%%]
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $list_dirn, $list_order); ?>
					</th>
					[%%ENDIF INCLUDE_ACCESS%%]
					[%%IF INCLUDE_STATUS%%]
					[%%ELSE INCLUDE_STATUS%%]
						[%%IF INCLUDE_FEATURED%%]
					<th width="5%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', 'JFEATURED', 'a.featured', $list_dirn, $list_order, NULL, 'desc'); ?>
					</th>
						[%%ENDIF INCLUDE_FEATURED%%]
					[%%ENDIF INCLUDE_STATUS%%]					
					[%%IF INCLUDE_CREATED%%]
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort',  '[%%COM_ARCHITECTCOMP%%]_HEADING_CREATED_BY', 'a.created_by', $list_dirn, $list_order); ?>
					</th>
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '[%%COM_ARCHITECTCOMP%%]_HEADING_CREATED', 'a.created', $list_dirn, $list_order); ?>
					</th>		
					[%%ENDIF INCLUDE_CREATED%%]						
					[%%IF INCLUDE_LANGUAGE%%]
					<?php if ($assoc) : ?>
						<th width="5%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '[%%COM_ARCHITECTCOMP%%]_HEADING_ASSOCIATION', 'association', $list_dirn, $list_order); ?>
						</th>
					<?php endif;?>				
					<th width="5%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $list_dirn, $list_order); ?>
					</th>		
					[%%ENDIF INCLUDE_LANGUAGE%%]
					[%%IF INCLUDE_HITS%%]
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $list_dirn, $list_order); ?>
					</th>	
					[%%ENDIF INCLUDE_HITS%%]			
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $list_dirn, $list_order); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :

				[%%IF INCLUDE_ORDERING%%]
				$item->max_ordering = 0; //??
				$ordering	= ($list_order=='ordering' OR $list_order=='a.ordering');
				[%%ENDIF INCLUDE_ORDERING%%]			
				$can_change = true;
				[%%IF INCLUDE_CHECKOUT%%]
					[%%IF INCLUDE_ASSETACL%%]
					$can_checkin	= $user->authorise('core.manage',		'com_checkin') OR $item->checked_out == $user_id OR $item->checked_out == 0;
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
				[%%IF INCLUDE_LANGUAGE%%]
				if ($item->language == '*'):
					$language = JText::alt('JALL', 'language');
				else:
					$language = $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED');
				endif;
				[%%ENDIF INCLUDE_LANGUAGE%%]			
							
				?>
				[%%IF GENERATE_CATEGORIES%%]
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
				[%%ELSE GENERATE_CATEGORIES%%]
				<tr class="row<?php echo $i % 2; ?>">
				[%%ENDIF GENERATE_CATEGORIES%%]
					[%%IF INCLUDE_ORDERING%%]
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
					[%%ENDIF INCLUDE_ORDERING%%]
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					[%%IF INCLUDE_STATUS%%]
					<td class="center">
						<div class="btn-group">
						[%%IF INCLUDE_PUBLISHED_DATES%%]
							<?php echo JHtml::_('jgrid.published', $item->state, $i, '[%%compobjectplural%%].', $can_change, 'cb', $item->publish_up, $item->publish_down); ?>
						[%%ELSE INCLUDE_PUBLISHED_DATES%%]
							<?php echo JHtml::_('jgrid.published', $item->state, $i, '[%%compobjectplural%%].', $can_change); ?>
						[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
						[%%IF INCLUDE_FEATURED%%]
							<?php echo JHtml::_('[%%compobject%%]administrator.featured', $item->featured, $i, $can_change); ?>
						[%%ENDIF INCLUDE_FEATURED%%]
							<?php
								if ($archived) :
									JHtml::_('actionsdropdown.unarchive', 'cb' . $i, '[%%compobjectplural%%]');
								else :
									JHtml::_('actionsdropdown.archive', 'cb' . $i, '[%%compobjectplural%%]');
								endif;
								if ($trashed) :
									JHtml::_('actionsdropdown.untrash', 'cb' . $i, '[%%compobjectplural%%]');
								else :
									JHtml::_('actionsdropdown.trash', 'cb' . $i, '[%%compobjectplural%%]');
								endif;

								// Render dropdown list
								[%%IF INCLUDE_NAME%%]
								echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
								[%%ELSE INCLUDE_NAME%%]
								echo JHtml::_('actionsdropdown.render');
								[%%ENDIF INCLUDE_NAME%%]
							?>
						</div>
					</td>	
					[%%ENDIF INCLUDE_STATUS%%]			
					[%%IF INCLUDE_NAME%%]				
					<td class="nowrap has-context">
						<div class="pull-left">
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
						</div>
					</td>
					[%%ENDIF INCLUDE_NAME%%]				
					[%%FOREACH FILTER_FIELD%%]
					<td class="nowrap small center hidden-phone">
						<?php 
							[%%FIELD_ADMIN_LIST_VALUE%%] 
						?>				
					</td>	
					[%%ENDFOR FILTER_FIELD%%]			
					[%%IF GENERATE_CATEGORIES%%]				
					<td class="nowrap small center hidden-phone">
						<?php echo $this->escape($item->category_title); ?>
					</td>	
					[%%ENDIF GENERATE_CATEGORIES%%]	
					[%%IF INCLUDE_ACCESS%%]
					<td class="nowrap small center hidden-phone">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					[%%ENDIF INCLUDE_ACCESS%%]
					[%%IF INCLUDE_STATUS%%]
					[%%ELSE INCLUDE_STATUS%%]
						[%%IF INCLUDE_FEATURED%%]
					<td class="center hidden-phone">
						<?php echo JHtml::_('[%%compobject%%]administrator.featured', $item->featured, $i, $can_change); ?>
					</td>
						[%%ENDIF INCLUDE_FEATURED%%]
					[%%ENDIF INCLUDE_STATUS%%]
					[%%IF INCLUDE_CREATED%%]
					<td class="small hidden-phone">
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
					<td class="nowrap small hidden-phone">
						<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
					</td>	
					[%%ENDIF INCLUDE_CREATED%%]									
					[%%IF INCLUDE_LANGUAGE%%]
					<?php if ($assoc) : ?>
						<td class="small hidden-phone">
							<?php if ($item->association) : ?>
								<?php echo JHtml::_('[%%compobject%%]administrator.association', $item->id); ?>
							<?php endif; ?>
						</td>
					<?php endif;?>				
					<td class="small center hidden-phone">
						<?php if ($item->language=='*'):?>
							<?php echo JText::alt('JALL', 'language'); ?>
						<?php else:?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
						<?php endif;?>
					</td>	
					[%%ENDIF INCLUDE_LANGUAGE%%]							
					[%%IF INCLUDE_HITS%%]
					<td class="nowrap hidden-phone">
						<?php echo (int) $item->hits; ?>
					</td>
					[%%ENDIF INCLUDE_HITS%%]
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
		<?php echo $this->loadTemplate('batch'); ?>
	<?php endif; ?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
<!--		<input type="hidden" name="filter_order" value="<?php echo $list_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $list_dirn; ?>" />-->
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
