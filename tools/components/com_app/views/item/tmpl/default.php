<?php
/**
 * @version 		$Id:$
 * @name			App (Release 1.0.0)
 * @author			 ()
 * @package			com_app
 * @subpackage		com_app.admin
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

// Create shortcuts to some parameters.
$params		= &$this->item->params;
$user		= JFactory::getUser();

$can_edit	= $params->get('access-edit');
$can_delete	= $params->get('access-delete');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_app' );
$empty = $component->params->get('default_empty_field', '');
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_APP_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="app item-view<?php echo $params->get('pageclass_sfx')?>">
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

	<?php if ($params->get('show_item_name')) : ?>
		<div style="float: left;">

			<h2>
				<?php if ($params->get('link_item_names') AND !empty($this->item->readmore_link)) : ?>
					<a href="<?php echo $this->item->readmore_link; ?>">
					<?php echo $this->escape($this->item->name); ?></a>
				<?php else : ?>
					<?php echo $this->escape($this->item->name); ?>
				<?php endif; ?>
			</h2>
		</div>
	<?php endif; ?>
	<?php  echo $this->item->event->afterDisplayItemName;	?>
	
	<?php echo $this->item->event->beforeDisplayItem; ?>
	<?php if ($params->get('show_item_hits') != '0' OR 
		($params->get('show_item_image', '0') == '1' AND $this->item->image_url != '')) : ?>	
		 <div style="float: right;">
			<?php if ($params->get('show_item_image') == '1' AND $this->item->image_url != '') : ?>
				<?php 
					$image = $this->item->image_url;
					list($img_width, $img_height) = getimagesize($image);
					$display_width = 0;
					$display_height = 0;
					 
					if ((int) $params->get('show_item_image_width') > 0) :
						$display_width = (int) $params->get('show_item_image_width');
					endif;
					
					if ((int) $params->get('show_item_image_height','0') > 0) :
						$display_height = (int) $params->get('show_item_image_height','0');
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
			<?php endif; ?>			 
			<?php if ($params->get('show_item_hits')) : ?>

				<?php echo '<br />'.JText::sprintf('COM_APP_HITS', $this->item->hits); ?>
			<?php endif; ?>	
		</div>
	<?php endif; ?>	
	<div style="clear:both; padding-top: 10px;">

		<?php if ($params->get('access-view')) :?>
			<?php
				if (isset($this->item->urls) AND $params->get('show_item_urls') == '1' AND $params->get('show_item_urls_position')=='0') :
					echo $this->loadTemplate('urls'); 
				endif;
			?>	
			<?php
				if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative) :
					echo $this->item->pagination;
				endif;
			?>	
		<?php echo $this->item->introdescription; ?>
			<?php
				if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND !$this->item->paginationrelative) :
					echo $this->item->pagination;
				endif;
			?>	
			<?php //optional teaser intro text for guests ?>
		<?php elseif ($params->get('show_item_noauth') == true AND  $user->get('guest') ) : ?>
			<?php if ($params->get('show_item_intro')) : ?>
				<?php echo $this->item->intro;
				endif;
			?>	
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
								echo JText::_('COM_APP_REGISTER_TO_READ_MORE');
							else :
								echo JText::_('COM_APP_REGISTER_TO_READMORE_NAME');
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
	</div>
	<div style="padding-top: 10px;">
		<?php if ($params->get('access-view')) : ?>	

			<form action="" name="itemForm" id="itemForm">
			<?php $dummy = false;
					$display_fieldset = (
								($params->get('show_item_category')) OR 
								($params->get('show_item_parent_category') AND $this->item->parent_slug != '1:root') OR
								($params->get('show_item_creator')) OR
								($params->get('show_item_created')) OR
								($params->get('show_item_modified')) OR
								($params->get('show_item_publish_up')) OR
								($params->get('show_item_publish_down')) OR
								($params->get('show_item_admin') AND $this->item->params->get('access-change')) OR							
								$dummy
								); ?>
			<?php if ($display_fieldset) : ?>				
					<fieldset>
						<legend><?php echo JText::_('COM_APP_FIELDSET_PUBLISHING_LABEL'); ?></legend>
			<?php endif; ?>
	
			<?php if ($params->get('show_item_parent_category') AND $this->item->parent_slug != '1:root') : ?>
				<?php $title = $this->escape($this->item->parent_title);
					  $url = '<a href="'.JRoute::_(AppHelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_item_itemid'))).'">'.$title.'</a>';
				?>				
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_APP_FIELD_PARENT_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_item_parent_category') AND $this->item->parent_slug) : ?>
							<?php echo $url; ?>
							<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>
					</span>
				</div>
			<?php endif;?>	
			<?php if ($params->get('show_item_category')) : ?>
				<?php $title = $this->escape($this->item->category_title);
				
					$url = '<a href="'.JRoute::_(AppHelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_item_itemid'))).'">'.$title.'</a>';
				?>
				<div class="formelm">				
					<label>
						<?php echo JText::_('COM_APP_FIELD_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_item_category') AND $this->item->catslug) : ?>
							<?php echo $url; ?>
						<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>	
					</span>
				</div>								
			<?php endif; ?>						
			<?php if ($params->get('show_item_creator') ) : ?>
				<?php $creator =  $this->item->created_by ?>
				<?php $creator = ($this->item->created_by_name ? $this->item->created_by_name : $creator);?>

				<div class="formelm">				
					<label>
						<?php echo JText::_('COM_APP_FIELD_CREATED_BY_LABEL'); ?> 
					</label>
					<span>
						<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_item_creator') == 1) :?>
							<?php echo JHTML::_(
									'link',
									JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
									$creator);
							 ?>

						<?php else :?>
							<?php echo $creator; ?>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>	
			<?php if ($params->get('show_item_created')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_APP_FIELD_CREATED_LABEL'); ?>
					</label>
					<span>
						<?php echo JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
					</span>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_item_modified')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_APP_FIELD_MODIFIED_LABEL'); ?>				
					</label>
					<span>
						<?php echo JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2')); ?>
					</span>
				</div>
			<?php endif; ?>	
			<?php if ($params->get('show_item_publish_up')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_APP_FIELD_PUBLISH_UP_LABEL'); ?>				
					</label>
					<span>
						<?php echo JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2')); ?>
					</span>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_item_publish_down')) : ?>
				<div class="formelm">
				<label>
					<?php echo JText::_('COM_APP_FIELD_PUBLISH_DOWN_LABEL'); ?>				
				</label>
				<span>
					<?php echo JHTML::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC2')); ?>
				</span>
				</div>
			<?php endif; ?>
			<?php if ($this->item->params->get('access-change')): ?>
				<?php if ($params->get('show_item_admin')) : ?>
				
					<div class="formelm">
						<label>
						<?php echo JText::_('COM_APP_FIELD_STATUS_LABEL'); ?>
						</label>
						<span>
							<?php 
								switch ($this->item->state) :
									case '1':
										echo JText::_('JPUBLISHED');
										break;
									case '0':
										echo JText::_('JUNPUBLISHED');
										break;
									case '2':
										echo JText::_('JARCHIVED');
										break;
									case '-2':
										echo JText::_('JTRASH');
										break;
								endswitch;
							?>
						</span>	
					</div>
					<div class="formelm">
						<label>
							<?php echo JText::_('COM_APP_FIELD_FEATURED_LABEL'); ?>
						</label>
						<span>
							<?php 
								switch ($this->item->featured) :
									case '0':
										echo JText::_('JNO');
										break;
									case '1':
										echo JText::_('JYES');
										break;
								endswitch;
							?>
						</span>						
					</div>
					<div class="formelm">
						<label>
							<?php echo JText::_('COM_APP_FIELD_ACCESS_LABEL'); ?>
						</label>
						<span>
							<?php echo $this->item->access_title; ?>
						</span>
					</div>
					<div class="formelm">
						<label>
							<?php echo JText::_('JFIELD_ORDERING_LABEL'); ?>
						</label>
						<span>
							<?php echo $this->item->ordering; ?>
						</span>
					</div>	
				<?php endif; ?>
				
			<?php endif; ?>
			
			<?php
				if (isset($this->item->urls) AND $params->get('show_item_urls') == '1' AND $params->get('show_item_urls_position')=='1') :
					echo $this->loadTemplate('urls');
				endif;
			?>	
			<?php if ($display_fieldset) : ?>				
					</fieldset>	
			<?php endif;?>	
				</form>
		<?php endif; ?>	
		<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative) :
				 echo $this->item->pagination;
			endif;
		?>							
		<?php echo $this->item->event->afterDisplayItem; ?>
	</div>		
</div>