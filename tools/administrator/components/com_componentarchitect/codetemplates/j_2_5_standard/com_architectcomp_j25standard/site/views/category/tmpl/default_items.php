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
 * @version			$Id: default_items.php 418 2014-10-22 14:42:36Z BrianWade $
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

[%%FOREACH COMPONENT_OBJECT%%]
[%%IF GENERATE_CATEGORIES%%]
// Create some shortcuts.
$params		= &$this->params;
$user		= JFactory::getUser();

$n			= count($this->[%%compobjectplural%%]);
$list_order	= $this->state->get('list.ordering');
$list_dirn	= $this->state->get('list.direction');
$layout		= str_replace('_:','',$params->get('[%%compobject%%]_layout'));
[%%IF INCLUDE_ASSETACL%%]
$can_create	= $user->authorise('core.create', '[%%com_architectcomp%%]');
[%%ENDIF INCLUDE_ASSETACL%%]
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$empty = $component->params->get('default_empty_field', '');	
?>

<?php if (empty($this->[%%compobjectplural%%])) : ?>

	<?php if ($this->params->get('show_no_[%%compobjectplural%%]',1)) : ?>
	<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_CATEGORY_NO_ITEMS'); ?></p>
	<?php endif; ?>

<?php else : ?>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (($this->params->get('[%%compobject%%]_filter_field') != '' AND $this->params->get('[%%compobject%%]_filter_field') != 'hide') OR $this->params->get('show_[%%compobject%%]_pagination_limit')) :?>
		<fieldset class="filters">
			<div class="filter-search">
				<?php if ($this->params->get('[%%compobject%%]_filter_field') != '' AND $this->params->get('[%%compobject%%]_filter_field') != 'hide') :?>
					<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_'.$this->params->get('[%%compobject%%]_filter_field').'_FILTER_LABEL').'&#160;'; ?></label>
					<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FILTER_SEARCH_DESC'); ?>" />
				<?php endif; ?>						
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
							[%%IF FIELD_FILTER_LINK%%]
						<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL', '[%%FIELD_FOREIGN_OBJECT_ACRONYM%%]_[%%FIELD_FOREIGN_OBJECT_CODE_NAME%%]_[%%FIELD_FOREIGN_OBJECT_LABEL_FIELD%%]', $list_dirn, $list_order); ?>
							[%%ELSE FIELD_FILTER_LINK%%]
						<?php echo JHTML::_('grid.sort', '[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL', 'a.[%%FIELD_CODE_NAME%%]', $list_dirn, $list_order); ?>
							[%%ENDIF FIELD_FILTER_LINK%%]
						</th>
					<?php endif; ?>	
					[%%ENDFOR FILTER_FIELD%%] 
					[%%IF INCLUDE_ASSETACL%%]
					<?php $show_actions = false;
						foreach ($this->[%%compobjectplural%%] as $item) : ?>
						<?php if ($item->params->get('access-edit') 
								OR $item->params->get('access-delete')) : ?>
								<?php $show_actions = true;
									  continue; ?>
						<?php endif;?>
						
					<?php endforeach; ?>
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

		<?php foreach ($this->[%%compobjectplural%%] as $i => $item) : ?>
			[%%IF INCLUDE_ASSETACL%%]
			<?php
				$can_edit	= $item->params->get('access-edit');
				$can_delete	= $item->params->get('access-delete');
			?>
			[%%ENDIF INCLUDE_ASSETACL%%]	

			[%%IF INCLUDE_STATUS%%]
			<?php if ($item->state == 0) : ?>
				<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
			<?php else: ?>
				<tr class="cat-list-row<?php echo $i % 2; ?>" >
			<?php endif; ?>
			[%%ELSE INCLUDE_STATUS%%]
				<tr class="cat-list-row<?php echo $i % 2; ?>" >
			[%%ENDIF INCLUDE_STATUS%%]
				[%%IF INCLUDE_ACCESS%%]
				<?php if (in_array($item->access, $this->user->getAuthorisedViewLevels()) OR $item->params->get('show_[%%compobject%%]_noauth')) : ?>
				[%%ENDIF INCLUDE_ACCESS%%]
					[%%IF INCLUDE_NAME%%]
					<td class="list-name">
						[%%IF INCLUDE_LANGUAGE%%]
						<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																								$item->catid, 
																								$item->language,
																								$layout,									
																								$this->params->get('keep_[%%compobject%%]_itemid'))); ?>">
						[%%ELSE INCLUDE_LANGUAGE%%]
						<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																								$item->catid,
																								$layout,								
																								$this->params->get('keep_[%%compobject%%]_itemid'))); ?>">
						[%%ENDIF INCLUDE_LANGUAGE%%]
					
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
						<?php echo $item->hits; ?>
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

				[%%IF INCLUDE_ACCESS%%]
				<?php else : // Show unauth links. ?>
					<td>
						<?php
						echo $this->escape($item->title).' : ';
						$menu		= JFactory::getApplication()->getMenu();
						$active		= $menu->getActive();
						$item_id		= $active->id;
						$link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$item_id);
						
						[%%IF INCLUDE_LANGUAGE%%]
						$return_url = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																								$item->catid, 
																								$item->language,
																								$layout,									
																								$this->params->get('keep_[%%compobject%%]_itemid')));
						[%%ELSE INCLUDE_LANGUAGE%%]
						$return_url = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($item->slug, 
																								$item->catid,
																								$layout,								
																								$this->params->get('keep_[%%compobject%%]_itemid')));
						[%%ENDIF INCLUDE_LANGUAGE%%]
							
						$full_url = new JUri($link);
						$full_url->setVar('return', base64_encode(urlencode($return_url)));
						?>
						<a href="<?php echo $full_url; ?>" class="register">
							<?php echo JText::_( '[%%COM_ARCHITECTCOMP%%]_REGISTER_TO_READ_MORE' ); ?></a>
					</td>
				<?php endif; ?>
				[%%ENDIF INCLUDE_ACCESS%%]
				[%%IF INCLUDE_ASSETACL%%]
				<?php if ($show_actions) : ?>
				[%%ENDIF INCLUDE_ASSETACL%%]
					<td class="list-actions">
						[%%IF INCLUDE_ASSETACL%%]
						<?php if ($can_edit OR $can_delete) : ?>
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
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if (($this->params->def('show_[%%compobject%%]_pagination', 2) == 1  OR 
			   ($this->params->get('show_[%%compobject%%]_pagination') == 2)) AND ($this->pagination->get('pages.total') > 1)) : ?>
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
		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
	</div>
</form>
<?php endif; ?>
<?php // Code to add a link to submit an [%%compobject%%]. ?>
<?php if ($this->params->get('show_[%%compobject%%]_add_link',1)) : ?>
	[%%IF INCLUDE_ASSETACL%%]
	<?php if ($this->category->getParams()->get('access-create')) : ?>
	[%%ENDIF INCLUDE_ASSETACL%%]
		<?php echo JHtml::_('[%%compobject%%]icon.create', $params); ?>
	[%%IF INCLUDE_ASSETACL%%]
	<?php  endif; ?>
	[%%ENDIF INCLUDE_ASSETACL%%]
<?php  endif; ?>
[%%ENDIF GENERATE_CATEGORIES%%]
[%%ENDFOR COMPONENT_OBJECT%%]