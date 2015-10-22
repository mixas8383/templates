<?php
/**
 * @version 		$Id:$
 * @name			App (Release 1.0.0)
 * @author			 ()
 * @package			com_app
 * @subpackage		com_app.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: blog_item.php 418 2014-10-22 14:42:36Z BrianWade $
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

// Create a shortcut for params.
$params = &$this->item->params;
$user		= JFactory::getUser();
$layout		= $params->get('item_layout', 'default');

$can_edit	= $params->get('access-edit');
$can_delete = $params->get('access-delete');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_app' );
$empty = $component->params->get('default_empty_field', '');
?>

<?php if ($this->item->state == 0 OR strtotime($this->item->publish_up) > strtotime(JFactory::getDate())) : ?>
<div class="system-unpublished">
<?php endif; ?>

<?php if (($params->get('show_item_print_icon') 
	OR $params->get('show_item_email_icon') 
	OR $can_edit
	OR $can_delete
		) AND $params->get('access-view')
	) : ?>
	<ul class="actions">
		<?php if ($params->get('show_item_print_icon')) : ?>
		<li class="print-icon">
			<?php echo JHtml::_('itemicon.print_popup', $this->item, $params); ?>
		</li>
		<?php endif; ?>
		<?php if ($params->get('show_item_email_icon')) : ?>
		<li class="email-icon">
			<?php echo JHtml::_('itemicon.email', $this->item, $params); ?>
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
	</ul>
<?php endif; ?>
<?php if ($params->get('show_item_name')) : ?>
<h2>
		<?php if ($params->get('link_item_names') ) : ?>
			<a href="<?php echo JRoute::_(AppHelperRoute::getItemRoute($this->item->slug, 
											$this->item->catid, 
											$this->item->language,
											$layout,									
											$params->get('keep_item_itemid'))); ?>">
			<?php echo $this->escape($this->item->name); ?></a>
		<?php else : ?>
			<?php echo $this->escape($this->item->name); ?>
		<?php endif; ?>
</h2>
<?php endif; ?>

<?php  echo $this->item->event->afterDisplayItemName;
?>
<?php echo $this->item->event->beforeDisplayItem; ?>
<?php if (($params->get('show_item_image', '0') == '1' AND $this->item->image_url <> "")): ?>	
 <div style="float: right;">
		<?php 
			$image = $this->item->image_url;
			
			list($img_width, $img_height) = getimagesize($image);
			$display_width = 0;
			$display_height = 0; 
			if ((int) $params->get('show_item_image_width') > 0) :
				$display_width = (int) $params->get('show_item_image_width','100');
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
	</div>
<?php endif; ?>	
<?php // to do not that elegant would be nice to group the params ?>

<?php 
	$dummy = false;
	if (
		($params->get('show_item_category')) OR 
		($params->get('show_item_parent_category') AND $this->item->parent_slug != '1:root') OR 
		($params->get('show_item_created')) OR 
		($params->get('show_item_creator')) OR 			
		($params->get('show_item_modified')) OR 
		($params->get('show_item_hits')) OR
		$dummy
		) : ?>
	<dl class="item-info">
 <dt class="item-info-term"><?php  echo JText::_('COM_APP_ITEMS_INFO'); ?></dt>
<?php endif; ?>
<?php if ($params->get('show_item_parent_category') AND $this->item->parent_slug != '1:root') : ?>
		<dd class="parent-category-name">
			<?php $title = $this->escape($this->item->parent_title);
			$url = '<a href="' . JRoute::_(AppHelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_item_itemid'))) . '">' . $title . '</a>'; ?>
			<?php if ($params->get('link_item_parent_category') AND $this->item->parent_slug) : ?>
				<?php echo JText::sprintf('COM_APP_PARENT_CATEGORY', $url); ?>
				<?php else : ?>
				<?php echo JText::sprintf('COM_APP_PARENT_CATEGORY', $title); ?>
			<?php endif; ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_item_category')) : ?>
		<dd class="category-name">
			<?php $title = $this->escape($this->item->category_title);
			$url = '<a href="'.JRoute::_(AppHelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_item_itemid'))).'">'.$title.'</a>';?>
			<?php if ($params->get('link_item_category') AND $this->item->catslug) : ?>
				<?php echo JText::sprintf('COM_APP_CATEGORY', $url); ?>
				<?php else : ?>
				<?php echo JText::sprintf('COM_APP_CATEGORY', $title); ?>
			<?php endif; ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_item_created')) : ?>
		<dd class="create">
		<?php echo JText::sprintf('COM_APP_CREATED_DATE_ON', JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_item_publish_up')) : ?>
<dd class="modified">
		<?php echo JText::sprintf('COM_APP_PUBLISH_UP_ON', JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
</dd>
<?php endif; ?>
<?php if ($params->get('show_item_modified')) : ?>
		<dd class="modified">
		<?php echo JText::sprintf('COM_APP_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_item_creator')) : ?>
	<dd class="createdby"> 
		<?php $creator =  $this->item->created_by ?>
		<?php $creator = ($this->item->created_by_name ? $this->item->created_by_name : $creator);?>

		<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_item_creator') == 1):?>
			<?php echo JText::sprintf('COM_APP_CREATED_BY',JHTML::_('link',
			JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),$creator)); ?>

		<?php else :?>
			<?php echo JText::sprintf('COM_APP_CREATED_BY', $creator); ?>
		<?php endif; ?>	
	</dd>
<?php endif; ?>	
<?php if ($params->get('show_item_hits')) : ?>
		<dd class="hits">
		<?php echo JText::sprintf('COM_APP_HITS', $this->item->hits); ?>
		</dd>
<?php endif; ?>
<?php
	$dummy = false;
	if (
		($params->get('show_item_category')) OR 
		($params->get('show_item_parent_category')) OR 
		($params->get('show_item_created')) OR 
		($params->get('show_item_creator')) OR 			
		($params->get('show_item_modified')) OR 
		($params->get('show_item_hits')) OR
		$dummy) : ?>
 </dl>
<?php endif; ?>

<?php if ($params->get('access-view') 
		AND (!$params->get('show_item_readmore')
		OR !$params->get('show_item_intro'))
		):?>
	<?php echo $this->item->introdescription; ?>

<?php else : ?>
	<?php //optional teaser intro text for guests ?>		
	<?php if ($params->get('show_item_intro')) : ?>
		<?php if ($params->get('access-view') OR (!$params->get('access-view') AND $params->get('show_item_noauth'))) : ?>
			<?php echo $this->item->intro; ?>
		<?php endif; ?>	
	<?php endif; ?>	
	<?php //Optional link to let them register to see the whole item. ?>
	<?php if ($params->get('show_item_readmore')) :
			if ($params->get('access-view')) :
				$link = JRoute::_(AppHelperRoute::getItemRoute($this->item->slug, 
																							$this->item->catid, 
																							$this->item->language,
																							$layout,									
																							$params->get('keep_item_itemid')));
			else :
				if ($params->get('show_item_noauth') AND $user->get('guest')) :
					$menu = JFactory::getApplication()->getMenu();
					$active = $menu->getActive();
					$item_id = $active->id;
					$link_1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $item_id);
					$return_url = JRoute::_(AppHelperRoute::getItemRoute($this->item->slug, 
																								$this->item->catid, 
																								$this->item->language,									
																								$layout,									
																								$params->get('keep_item_itemid')));
					$link = new JUri($link_1);
					$link->setVar('return', base64_encode(urlencode($return_url)));
				endif;
			endif;?>
			<p class="readmore">
				<a href="<?php echo $link; ?>">
				<?php
				$item_params = new JRegistry;				
				$item_params->loadString($this->item->params);
				if ($item_params->get('item_alternative_readmore') == null) :
						if ($params->get('access-view')) :
							if ($params->get('show_item_readmore_name') == 0) :
								echo JText::sprintf('COM_APP_READ_MORE');
							else :
								echo JText::_('COM_APP_READMORE_NAME');
							echo JHtml::_('string.truncate', ($this->item->name), $params->get('item_readmore_limit'));
						endif; 
					else :
						if ($params->get('show_item_readmore_name') == 0) :
							echo JText::_('COM_APP_REGISTER_TO_READ_MORE');
						else :	
							echo JText::_('COM_APP_REGISTER_TO_READMORE_NAME');
							echo JHtml::_('string.truncate', ($this->item->name), $params->get('item_readmore_limit'));
						endif;
					
					endif;
				else :
					echo $this->item->item_alternative_readmore;
					if ($params->get('show_item_readmore_name') == 1) :
						echo JHtml::_('string.truncate', ': '.($this->item->name), $params->get('item_readmore_limit'));
					endif;	
				endif;?>
			</a>
		</p>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->item->state == 0 OR strtotime($this->item->publish_up) > strtotime(JFactory::getDate())) : ?>
</div>
<?php endif; ?>

<div class="item-separator"></div>
<?php echo $this->item->event->afterDisplayItem; ?>
