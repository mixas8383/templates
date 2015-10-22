<?php
/**
 * @version 		$Id:$
 * @name			Game (Release 1.0.0)
 * @author			 ()
 * @package			com_game
 * @subpackage		com_game.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default_items.php 408 2014-10-19 18:31:00Z BrianWade $
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
 *	Initialise values for the layout 
 */	
 
// Create some shortcuts.
$params		= &$this->params;
$user		= JFactory::getUser();

$n			= count($this->items);
$list_order	= $this->state->get('list.ordering');
$list_dirn	= $this->state->get('list.direction');
$layout		= str_replace('_:','',$params->get('item_layout'));
$can_create	= $user->authorise('core.create', 'com_game');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_game' );
$empty = $component->params->get('default_empty_field', '');	

/*
 *	Layout HTML
 */
?>

<?php if (empty($this->items)) : ?>

	<?php if ($this->params->get('show_no_items',1)) : ?>
	<p><?php echo JText::_('COM_GAME_ITEMS_CATEGORY_NO_ITEMS'); ?></p>
	<?php endif; ?>

<?php else : ?>
<div class="items-list">
	<form action="<?php echo JFilterOutput::ampReplace(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
		<?php if (($this->params->get('show_item_filter_field') != '' AND $this->params->get('show_item_filter_field') != 'hide') OR $this->params->get('show_item_pagination_limit')) :?>
			<div class="filter-search">
				<?php if ($this->params->get('show_item_filter_field') != '' AND $this->params->get('show_item_filter_field') != 'hide') :?>
					<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_GAME_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_GAME_'.$this->params->get('show_item_filter_field').'_FILTER_LABEL'); ?>" />
				<?php endif; ?>						
				<?php if ($this->params->get('show_item_pagination_limit')) : ?>
					<div class="display-limit">
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				<?php endif; ?>
			</div>						
		<?php endif; ?>


		<table class="items">
			<?php if ($this->params->get('show_item_headings')) :?>
				<thead>
					<tr>
						<th class="list-name" id="tableOrderingname">
							<?php  echo JHtml::_('grid.sort', 'COM_GAME_HEADING_NAME', 'a.name', $list_dirn, $list_order) ; ?>
						</th>
						<?php if ($date = $this->params->get('list_show_item_date')) : ?>
							<th class="list-date" id="tableOrderingdate">
								<?php echo JHtml::_('grid.sort', 'COM_GAME_FIELD_'.JString::strtoupper($date).'_LABEL', 'a.'.$date, $list_dirn, $list_order); ?>
							</th>
						<?php endif; ?>

						<?php if ($this->params->get('list_show_item_created_by',0)) : ?>
							<th class="list-creator" id="tableOrderingcreated_by">
								<?php echo JHtml::_('grid.sort', 'COM_GAME_HEADING_CREATED_BY', 'created_by_name', $list_dirn, $list_order); ?>
							</th>
						<?php endif; ?>
						<?php if ($this->params->get('list_show_item_hits',0)) : ?>
							<th class="list-hits" id="tableOrderinghits">
								<?php echo JHtml::_('grid.sort', 'COM_GAME_HEADING_HITS', 'a.hits', $list_dirn, $list_order); ?>
							</th>
						<?php endif; ?>
						<?php $show_actions = false;
							foreach ($this->items as $item) : ?>
							<?php if ($item->params->get('access-edit') 
									OR $item->params->get('access-delete')) : ?>
									<?php $show_actions = true;
										  continue; ?>
							<?php endif;?>
							
						<?php endforeach; ?>
						<?php if ($show_actions) : ?>
							<th width="12%" class="list-actions">
								<?php echo JText::_('COM_GAME_HEADING_ACTIONS'); ?>						
							</th> 				
						<?php endif; ?>
					</tr>
				</thead>
			<?php endif; ?>

			<tbody>

			<?php foreach ($this->items as $i => $item) : ?>
				<?php
					$can_edit	= $item->params->get('access-edit');
					$can_delete	= $item->params->get('access-delete');
				?>

				<?php if ($item->state == 0) : ?>
					<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
				<?php else: ?>
					<tr class="cat-list-row<?php echo $i % 2; ?>" >
				<?php endif; ?>
					<?php if (in_array($item->access, $this->user->getAuthorisedViewLevels()) OR $item->params->get('show_item_noauth')) : ?>
						<td class="list-name">
							<a href="<?php echo JRoute::_(GameHelperRoute::getItemRoute($item->slug, 
																									$item->catid, 
																									$item->language,
																									$layout,									
																									$this->params->get('keep_item_itemid'))); ?>">
						
							<?php echo $this->escape($item->name); ?></a>
						</td>

						<?php if ($this->params->get('list_show_item_date')) : ?>
						<td class="list-date">
							<?php echo JHtml::_('date',$item->display_date, $this->escape(
							$this->params->get('item_date_format', JText::_('DATE_FORMAT_LC3')))); ?>
						</td>
						<?php endif; ?>

						<?php if ($this->params->get('list_show_item_created_by',0)) : ?>
						<td class="createdby">
							<?php $creator =  $item->created_by ?>
							<?php $creator = ($item->created_by_name ? $item->created_by_name : $creator);?>
							<?php $creator = ($item->created_by_alias ? $item->created_by_alias : $creator);?>

							<?php if (!empty($item->created_by ) AND  $this->params->get('link_item_created_by') == 1):?>
								<?php $creator = JHtml::_(
										'link',
										JRoute::_('index.php?option=com_users&view=profile&id='.$item->created_by),
										$creator
								); ?>

							<?php endif;?>
							<?php if ($this->params->get('show_item_headings')) :?>
								<?php echo $creator; ?>
							<?php else : ?>									
								<?php echo JText::sprintf('COM_GAME_CREATED_BY', $creator); ?>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if ($this->params->get('list_show_item_hits',0)) : ?>
						<td class="list-hits">
							<span class="badge badge-info">
								<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
							</span>
						</td>
						<?php endif; ?>

					<?php else : // Show unauth links. ?>
						<td>
							<?php
							echo $this->escape($item->title).' : ';
							$menu		= JFactory::getApplication()->getMenu();
							$active		= $menu->getActive();
							$item_id		= $active->id;
							$link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$item_id);
							
							$return_url = JRoute::_(GameHelperRoute::getItemRoute($item->slug, 
																									$item->catid, 
																									$item->language,
																									$layout,									
																									$this->params->get('keep_item_itemid')));
								
							$full_url = new JUri($link);
							$full_url->setVar('return', base64_encode($return_url));
							?>
							<a href="<?php echo $full_url; ?>" class="register">
								<?php echo JText::_( 'COM_GAME_REGISTER_TO_READ_MORE' ); ?></a>
						</td>
					<?php endif; ?>
					<?php if ($show_actions) : ?>
						<td class="list-actions">
							<?php if ($can_edit OR $can_delete) : ?>
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
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php if (($this->params->def('show_item_pagination', 2) == 1  OR 
				   ($this->params->get('show_item_pagination') == 2)) AND ($this->pagination->get('pages.total') > 1)) : ?>
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
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" name="limitstart" value="" />
		</div>
	</form>
</div>
<?php endif; ?>
<?php // Code to add a link to submit an item. ?>
<?php if ($this->params->get('show_item_add_link',1)) : ?>
	<?php if ($this->category->getParams()->get('access-create')) : ?>
		<?php echo JHtml::_('itemicon.create', $params); ?>
	<?php  endif; ?>
<?php  endif; ?>
