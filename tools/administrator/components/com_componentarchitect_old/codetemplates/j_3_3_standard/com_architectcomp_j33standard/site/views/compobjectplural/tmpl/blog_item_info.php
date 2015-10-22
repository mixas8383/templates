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
 * @version			$Id: blog_item_info.php 408 2014-10-19 18:31:00Z BrianWade $
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
<dl class="[%%compobject%%]-info">
	<dt class="[%%compobject%%]-info-term"><?php  echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_INFO'); ?></dt>
	[%%IF GENERATE_CATEGORIES%%]
	<?php if ($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') : ?>
		<dd class="parent-category-name">
		[%%IF INCLUDE_MICRODATA%%]
			<?php $title = '<span itemprop="genre">'.$this->escape($this->item->parent_title).'</span>'; ?>
			<?php if ($params->get('link_[%%compobject%%]_parent_category') AND $this->item->parent_slug) : ?>
				<?php $url = '<a href="' . JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))) . '" itemprop="url">' . $title . '</a>'; ?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $url); ?>
			<?php else : ?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $title); ?>
			<?php endif; ?>
		[%%ELSE INCLUDE_MICRODATA%%]
			<?php $title = $this->escape($this->item->parent_title); ?>
			<?php if ($params->get('link_[%%compobject%%]_parent_category') AND $this->item->parent_slug) : ?>
				<?php $url = '<a href="' . JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))) . '">' . $title . '</a>'; ?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $url); ?>
			<?php else : ?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $title); ?>
			<?php endif; ?>
		[%%ENDIF INCLUDE_MICRODATA%%]
		</dd>
	<?php endif; ?>
	<?php if ($params->get('show_[%%compobject%%]_category')) : ?>
		<dd class="category-name">
			[%%IF INCLUDE_MICRODATA%%]
			<?php $title = '<span itemprop="genre">'.$this->escape($this->item->category_title).'</span>'; ?>
			<?php if ($params->get('link_[%%compobject%%]_category') AND $this->item->catslug) : ?>
				<?php $url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_[%%compobject%%]_itemid'))).'" itemprop="url">'.$title.'</a>';?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CATEGORY', $url); ?>
			<?php else : ?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CATEGORY', $title); ?>
			<?php endif; ?>
			[%%ELSE INCLUDE_MICRODATA%%]
			<?php $title = $this->escape($this->item->category_title); ?>
			<?php if ($params->get('link_[%%compobject%%]_category') AND $this->item->catslug) : ?>
				<?php $url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_[%%compobject%%]_itemid'))).'">'.$title.'</a>';?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CATEGORY', $url); ?>
			<?php else : ?>
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CATEGORY', $title); ?>
			<?php endif; ?>
			[%%ENDIF INCLUDE_MICRODATA%%]
		</dd>
	<?php endif; ?>
	[%%ENDIF GENERATE_CATEGORIES%%]
	[%%IF INCLUDE_TAGS%%]
	<?php if ($params->get('show_[%%compobject%%]_tags')) : ?>
		<?php 
			$this->item->tags = new JHelperTags;
			$this->item->tags->getItemTags('[%%com_architectcomp%%].[%%compobject%%]', $this->item->id);	
		
			$this->item->tag_layout = new JLayoutFile('joomla.content.tags');
			echo $this->item->tag_layout->render($this->item->tags->itemTags);
		?>
	<?php endif; ?>
	[%%ENDIF INCLUDE_TAGS%%]	
	[%%IF INCLUDE_CREATED%%]
	<?php if ($params->get('show_[%%compobject%%]_created')) : ?>
		<dd class="create">
			[%%IF INCLUDE_MICRODATA%%]
				<time datetime="<?php echo JHtml::_('date', $this->item->created, 'c'); ?>" itemprop="dateCreated">
			[%%ELSE INCLUDE_MICRODATA%%]
				<time datetime="<?php echo JHtml::_('date', $this->item->created, 'c'); ?>">
			[%%ENDIF INCLUDE_MICRODATA%%]
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_DATE_ON', JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
			</time>
		</dd>
	<?php endif; ?>
	[%%ENDIF INCLUDE_CREATED%%]
	[%%IF INCLUDE_PUBLISHED_DATES%%]
	<?php if ($params->get('show_[%%compobject%%]_publish_up')) : ?>
		<dd class="published">
			[%%IF INCLUDE_MICRODATA%%]
			<time datetime="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>" itemprop="datePublished">
			[%%ELSE INCLUDE_MICRODATA%%]
			<time datetime="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>">
			[%%ENDIF INCLUDE_MICRODATA%%]
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PUBLISH_UP_ON', JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
			</time>
		</dd>
	<?php endif; ?>
	[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
	[%%IF INCLUDE_MODIFIED%%]
	<?php if ($params->get('show_[%%compobject%%]_modified')) : ?>
		<dd class="modified">
			[%%IF INCLUDE_MICRODATA%%]
			<time datetime="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
			[%%ELSE INCLUDE_MICRODATA%%]
			<time datetime="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>">
			[%%ENDIF INCLUDE_MICRODATA%%]			
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_LAST_UPDATED', JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
			</time>
		</dd>
	<?php endif; ?>
	[%%ENDIF INCLUDE_MODIFIED%%]
	[%%IF INCLUDE_CREATED%%]
	<?php if ($params->get('show_[%%compobject%%]_created_by')) : ?>
		[%%IF INCLUDE_MICRODATA%%]
	<dd class="createdby" itemprop="author" itemscope itemtype="http://schema.org/Person"> 
		<?php $created_by =  $this->item->created_by ?>
		<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>
		<?php $created_by = '<span itemprop="name">' . $created_by . '</span>'; ?>
		<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_created_by') == 1):?>
			<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY',JHtml::_('link',
			JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),$created_by)); ?>

		<?php else :?>
			<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $created_by); ?>
		<?php endif; ?>
	</dd>
		[%%ELSE INCLUDE_MICRODATA%%]
	<dd class="createdby"> 
		<?php $created_by =  $this->item->created_by ?>
		<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>
		<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_created_by') == 1):?>
			<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY',JHtml::_('link',
			JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),$created_by)); ?>

		<?php else :?>
			<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $created_by); ?>
		<?php endif; ?>
	</dd>
		[%%ENDIF INCLUDE_MICRODATA%%]
	<?php endif; ?>	
	[%%ENDIF INCLUDE_CREATED%%]
	[%%IF INCLUDE_HITS%%]
	<?php if ($params->get('show_[%%compobject%%]_hits') AND !empty($this->item->hits)) : ?>
			<dd class="hits">
		<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits); ?>
		</dd>
	<?php endif; ?>
	[%%ENDIF INCLUDE_HITS%%]
</dl>