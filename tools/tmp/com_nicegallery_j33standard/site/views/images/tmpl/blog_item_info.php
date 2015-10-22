<?php
/**
 * @version 		$Id:$
 * @name			Nicegallery (Release 1.0.0)
 * @author			 ()
 * @package			com_nicegallery
 * @subpackage		com_nicegallery.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: blog_item_info.php 408 2014-10-19 18:31:00Z BrianWade $
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
 
// Create shortcuts to some parameters.
$params = $this->item->params;

/*
 *	Layout HTML
 */	
?>
<dl class="image-info">
	<dt class="image-info-term"><?php  echo JText::_('COM_NICEGALLERY_IMAGES_INFO'); ?></dt>
	<?php if ($params->get('show_image_parent_category') AND $this->item->parent_slug != '1:root') : ?>
		<dd class="parent-category-name">
			<?php $title = '<span itemprop="genre">'.$this->escape($this->item->parent_title).'</span>'; ?>
			<?php if ($params->get('link_image_parent_category') AND $this->item->parent_slug) : ?>
				<?php $url = '<a href="' . JRoute::_(NicegalleryHelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_image_itemid'))) . '" itemprop="url">' . $title . '</a>'; ?>
				<?php echo JText::sprintf('COM_NICEGALLERY_PARENT_CATEGORY', $url); ?>
			<?php else : ?>
				<?php echo JText::sprintf('COM_NICEGALLERY_PARENT_CATEGORY', $title); ?>
			<?php endif; ?>
		</dd>
	<?php endif; ?>
	<?php if ($params->get('show_image_category')) : ?>
		<dd class="category-name">
			<?php $title = '<span itemprop="genre">'.$this->escape($this->item->category_title).'</span>'; ?>
			<?php if ($params->get('link_image_category') AND $this->item->catslug) : ?>
				<?php $url = '<a href="'.JRoute::_(NicegalleryHelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_image_itemid'))).'" itemprop="url">'.$title.'</a>';?>
				<?php echo JText::sprintf('COM_NICEGALLERY_CATEGORY', $url); ?>
			<?php else : ?>
				<?php echo JText::sprintf('COM_NICEGALLERY_CATEGORY', $title); ?>
			<?php endif; ?>
		</dd>
	<?php endif; ?>
	<?php if ($params->get('show_image_tags')) : ?>
		<?php 
			$this->item->tags = new JHelperTags;
			$this->item->tags->getItemTags('com_nicegallery.image', $this->item->id);	
		
			$this->item->tag_layout = new JLayoutFile('joomla.content.tags');
			echo $this->item->tag_layout->render($this->item->tags->itemTags);
		?>
	<?php endif; ?>
	<?php if ($params->get('show_image_created')) : ?>
		<dd class="create">
				<time datetime="<?php echo JHtml::_('date', $this->item->created, 'c'); ?>" itemprop="dateCreated">
				<?php echo JText::sprintf('COM_NICEGALLERY_CREATED_DATE_ON', JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
			</time>
		</dd>
	<?php endif; ?>
	<?php if ($params->get('show_image_publish_up')) : ?>
		<dd class="published">
			<time datetime="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>" itemprop="datePublished">
				<?php echo JText::sprintf('COM_NICEGALLERY_PUBLISH_UP_ON', JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
			</time>
		</dd>
	<?php endif; ?>
	<?php if ($params->get('show_image_modified')) : ?>
		<dd class="modified">
			<time datetime="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
				<?php echo JText::sprintf('COM_NICEGALLERY_LAST_UPDATED', JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
			</time>
		</dd>
	<?php endif; ?>
	<?php if ($params->get('show_image_created_by')) : ?>
	<dd class="createdby" itemprop="author" itemscope itemtype="http://schema.org/Person"> 
		<?php $created_by =  $this->item->created_by ?>
		<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>
		<?php $created_by = '<span itemprop="name">' . $created_by . '</span>'; ?>
		<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_image_created_by') == 1):?>
			<?php echo JText::sprintf('COM_NICEGALLERY_CREATED_BY',JHtml::_('link',
			JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),$created_by)); ?>

		<?php else :?>
			<?php echo JText::sprintf('COM_NICEGALLERY_CREATED_BY', $created_by); ?>
		<?php endif; ?>
	</dd>
	<?php endif; ?>	
	<?php if ($params->get('show_image_hits') AND !empty($this->item->hits)) : ?>
			<dd class="hits">
		<?php echo JText::sprintf('COM_NICEGALLERY_HITS', $this->item->hits); ?>
		</dd>
	<?php endif; ?>
</dl>