<?php
/**
 * @version 		$Id:$
 * @name			ModCreator (Release 1.0.0)
 * @author			 ()
 * @package			com_modcreator
 * @subpackage		com_modcreator.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: article.php 418 2014-10-22 14:42:36Z BrianWade $
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

// Create shortcuts to some parameters.
$params		= &$this->item->params;
$user		= JFactory::getUser();
$can_edit	= $params->get('access-edit');
$can_delete	= $params->get('access-delete');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_modcreator' );
$empty = $component->params->get('default_empty_field', '');
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_MODCREATOR_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="modcreator item-article<?php echo $params->get('pageclass_sfx')?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<?php
		if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND $this->item->paginationrelative) :
			echo $this->item->pagination;
		endif;
	 ?>
	<?php if ($params->get('show_item_print_icon') 
				OR $params->get('show_item_email_icon') 
				OR $can_edit 
				OR $can_delete 
				): ?>
			<ul class="actions">
				<?php if (!$this->print) : ?>
					<?php if ($params->get('access-view')) : ?>
						<?php if ($params->get('show_item_print_icon')) : ?>
						<li class="print-icon">
								<?php echo JHtml::_('itemicon.print_popup',  $this->item, $params); ?>
						</li>
						<?php endif; ?>

						<?php if ($params->get('show_item_email_icon')) : ?>
						<li class="email-icon">
								<?php echo JHtml::_('itemicon.email',  $this->item, $params); ?>
						</li>
						<?php endif; ?>
						<?php if ($can_edit) : ?>
							<li class="edit-icon">
								<?php echo JHtml::_('itemicon.edit', $this->item, $params); ?>
							</li>
						<?php endif; ?>
						<?php if ($can_delete) : ?>
							<li class="delete-icon">
								<?php echo JHtml::_('itemicon.delete',$this->item, $params); ?>
							</li>
						<?php endif; ?>
					<?php endif; ?>
				<?php else : ?>
					<li>
						<?php echo JHtml::_('itemicon.print_screen',  $this->item, $params); ?>
					</li>
				<?php endif; ?>
			</ul>
	<?php endif; ?>

	<?php if ($params->get('show_item_name')
				OR $params->get('access-edit') 
				): ?>
		<h2>
			<?php if ($params->get('link_item_names') AND !empty($this->item->readmore_link)) : ?>
				<a href="<?php echo $this->item->readmore_link; ?>">
				<?php echo $this->escape($this->item->name); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->item->name); ?>
			<?php endif; ?>
		</h2>
	<?php endif; ?>
	<?php  echo $this->item->event->afterDisplayItemName;
	?>

	<?php echo $this->item->event->beforeDisplayItem; ?>
	<?php if (($params->get('show_item_image', '0') == '1' AND $this->item->image_url != "")) : ?>	
		<div style="float: right;">
			<?php 
				$image = $this->item->image_url;
					 
				list($img_width, $img_height) = getimagesize($image);
				$display_width = 0;
				$display_height = 0; 
				
				if ((int) $params->get('show_item_image_width') > 0) :
					$display_width = (int) $params->get('show_item_image_width');
				endif;

				if ((int) $params->get('show_item_image_height','0') > 0) :
					$display_height = (int) $params->get('show_item_image_height');
				endif;

									
				if ($display_width > $img_width) :
					if ($display_height < $img_height AND $display_height > 0) :
						$display_width = 0;
					endif;
				else :
					$display_height = 0;
				endif;
			?>
			<img src="<?php echo $image; ?>"
				<?php if ($display_width > 0) : ?>
					width="<?php echo $display_width ?>"
				<?php endif; ?>	
				<?php if ($display_height > 0) : ?>
					height="<?php echo $display_height ?>"
				<?php endif; ?>	
					alt="<?php if ($this->item->image_alt_text != '') : echo  $this->escape($this->item->image_alt_text); else : echo $this->escape($this->item->name); endif; ?>"
			/>
		</div>
	<?php endif; ?>	
	<?php if ($params->get('access-view')) : ?>	
		<?php $dummy = false;
				$use_def_list = (
							($params->get('show_item_category')) OR 
							($params->get('show_item_parent_category') AND $this->item->parent_slug != '1:root') OR
							($params->get('show_item_creator')) OR 									
							($params->get('show_item_created')) OR 
							($params->get('show_item_modified')) OR
							($params->get('show_item_hits')) OR
							$dummy
							); ?>

		<?php if ($use_def_list) : ?>
		<dl class="info">
			<dt class="info-title"><?php  echo JText::_('COM_MODCREATOR_ITEMS_INFO'); ?></dt>
		<?php endif; ?>
		<?php if ($params->get('show_item_parent_category') AND $this->item->parent_slug != '1:root') : ?>
				<dd class="parent-category-name">
					<?php	$title = $this->escape($this->item->parent_title);
					$url = '<a href="'.JRoute::_(ModcreatorHelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_item_itemid'))).'">'.$title.'</a>';?>
					<?php if ($params->get('link_item_parent_category') AND $this->item->parent_slug) : ?>
						<?php echo JText::sprintf('COM_MODCREATOR_PARENT_CATEGORY', $url); ?>
						<?php else : ?>
						<?php echo JText::sprintf('COM_MODCREATOR_PARENT_CATEGORY', $title); ?>
					<?php endif; ?>
				</dd>
		<?php endif; ?>

		<?php if ($params->get('show_item_category')) : ?>
				<dd class="category-name">
					<?php 	$title = $this->escape($this->item->category_title);
					$url = '<a href="'.JRoute::_(ModcreatorHelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_item_itemid'))).'">'.$title.'</a>';?>
					<?php if ($params->get('link_item_category') AND $this->item->catslug) : ?>
						<?php echo JText::sprintf('COM_MODCREATOR_CATEGORY', $url); ?>
						<?php else : ?>
						<?php echo JText::sprintf('COM_MODCREATOR_CATEGORY', $title); ?>
					<?php endif; ?>
				</dd>
		<?php endif; ?>
		<?php if ($params->get('show_item_created')) : ?>
				<dd class="create">
				<?php echo JText::sprintf('COM_MODCREATOR_CREATED_DATE_ON', JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
				</dd>
		<?php endif; ?>
		<?php if ($params->get('show_item_publish_up')) : ?>
		<dd class="modified">
				<?php echo JText::sprintf('COM_MODCREATOR_PUBLISH_UP_ON', JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_item_publish_down')) : ?>
		<dd class="modified">
			<?php echo JText::sprintf('COM_MODCREATOR_PUBLISH_DOWN_ON', JHTML::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_item_modified')) : ?>
				<dd class="modified">
				<?php echo JText::sprintf('COM_MODCREATOR_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
				</dd>
		<?php endif; ?>
		<?php if ($params->get('show_item_creator') ) : ?>
			<dd class="createdby"> 
				<?php $creator =  $this->item->created_by ?>
				<?php $creator = ($this->item->created_by_name ? $this->item->created_by_name : $creator);?>

				<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_item_creator') == 1):?>
					<?php echo JText::sprintf('COM_MODCREATOR_CREATED_BY',JHTML::_(
							'link',
							JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
							$creator
					)); ?>

				<?php else :?>
					<?php echo JText::sprintf('COM_MODCREATOR_CREATED_BY', $creator); ?>
				<?php endif; ?>

			</dd>
		<?php endif; ?>	
		<?php if ($params->get('show_item_hits')) : ?>
				<dd class="hits">
				<?php echo JText::sprintf('COM_MODCREATOR_HITS', $this->item->hits); ?>
				</dd>
		<?php endif; ?>
		<?php if ($use_def_list) : ?>
		 </dl>
		<?php endif; ?>
		<?php
			if (isset ($this->item->toc)) :
				echo $this->item->toc;
			endif;
		?>	
			<?php
				if (isset($this->item->urls) AND $params->get('show_item_urls') == '1' AND $params->get('show_item_urls_position')=='0') :
					echo $this->loadTemplate('urls'); 
				endif;
			?>	
		<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
				echo $this->item->pagination;
			 endif;
		?>	
		<?php echo $this->item->introdescription; ?>
		<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND!$this->item->paginationrelative):
				echo $this->item->pagination;
			endif;
		?>		
		<?php
			$dummy = false;
			$use_fields_list = (
						$dummy
						);
		?>
		<?php if ($use_fields_list) : ?>		
		<dl class="info">
			<dt class="info-title"><?php  echo JText::_('COM_MODCREATOR_ITEMS_INFO'); ?></dt>
		<?php endif; ?>		
		
		<?php if ($use_fields_list) : ?>		
		</dl>	
		<?php endif; ?>
		<?php
			if (isset($this->item->urls) AND $params->get('show_item_urls') == '1' AND $params->get('show_item_urls_position')=='1') :
				echo $this->loadTemplate('urls');
			endif;
		?>	
		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_item_noauth') AND $user->get('guest') ) : ?>
		<?php if ($params->get('show_item_intro')) : ?>
			<?php echo $this->item->intro; ?>
		<?php endif; ?>
		<?php //Optional link to let them register to see the whole item. ?>
		<?php if ($params->get('show_item_readmore')) :
					$menu = JFactory::getApplication()->getMenu();
					$active = $menu->getActive();
					$item_id = $active->id;
					$link_1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $item_id);

					$return_url = $this->item->readmore_link;

					$link = new JUri($link_1);
					$link->setVar('return', base64_encode(urlencode($return_url)));?>
					<p class="readmore">
						<a href="<?php echo $link; ?>">
							<?php
							if ($this->item->item_alternative_readmore == null) :
								if ($params->get('show_item_readmore_name') == 0) :					
									echo JText::_('COM_MODCREATOR_REGISTER_TO_READ_MORE');
								else :
									echo JText::_('COM_MODCREATOR_REGISTER_TO_READMORE_NAME');
									echo JHtml::_('string.truncate', ($this->item->name), $params->get('item_readmore_limit'));
								endif;
							else :
								echo $this->item->item_alternative_readmore;
								if ($params->get('show_item_readmore_name') == 1) :
									echo JHtml::_('string.truncate', ': '.($this->item->name), $params->get('item_readmore_limit'));
								endif;
							endif;
							?>
						</a>
					</p>
		<?php endif; ?>
	<?php endif; ?>
	<?php
		if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative) :
			 echo $this->item->pagination;
		endif;
	?>	
	<?php echo $this->item->event->afterDisplayItem; ?>
</div>