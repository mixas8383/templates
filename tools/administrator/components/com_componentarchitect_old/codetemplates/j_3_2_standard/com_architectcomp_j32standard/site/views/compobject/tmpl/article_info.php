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
 * @version			$Id: article_info.php 417 2014-10-22 14:42:10Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
 * @CAtemplate		joomla_3_2_standard (Release 1.0.4)
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
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$empty = $component->params->get('default_empty_field', '');

/*
 *	Layout HTML
 */
?>
<div class="article-info muted">
	<dl class="info">
		[%%IF INCLUDE_CREATED%%]
		<?php if ($params->get('show_[%%compobject%%]_created_by') ) : ?>
			<dd class="createdby"> 
				<?php $created_by =  $this->item->created_by ?>
				<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>

				<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_created_by') == 1):?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY',JHTML::_(
							'link',
							JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
							$created_by
					)); ?>

				<?php else :?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $created_by); ?>
				<?php endif; ?>

			</dd>
		<?php endif; ?>
		<?php if ($params->get('show_[%%compobject%%]_created_by_alias')) : ?>
			<dd class="field">
				<strong><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_BY_ALIAS_LABEL'); ?></strong>
				<?php
					echo !empty($this->item->created_by_alias) ? $this->item->created_by_alias : $empty;
				?>
			</dd>
		<?php endif; ?>					
		[%%ENDIF INCLUDE_CREATED%%]
		 [%%IF GENERATE_CATEGORIES%%]
		<?php if ($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') : ?>
			<dd class="parent-category-name">
				<?php	$title = $this->escape($this->item->parent_title);
				$url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))).'">'.$title.'</a>';?>
				<?php if ($params->get('link_[%%compobject%%]_parent_category') AND $this->item->parent_slug) : ?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $url); ?>
					<?php else : ?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $title); ?>
				<?php endif; ?>
			</dd>
		<?php endif; ?>

		<?php if ($params->get('show_[%%compobject%%]_category')) : ?>
			<dd class="category-name">
				<?php 	$title = $this->escape($this->item->category_title);
				$url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_[%%compobject%%]_itemid'))).'">'.$title.'</a>';?>
				<?php if ($params->get('link_[%%compobject%%]_category') AND $this->item->catslug) : ?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CATEGORY', $url); ?>
					<?php else : ?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CATEGORY', $title); ?>
				<?php endif; ?>
			</dd>
		<?php endif; ?>
		 [%%ENDIF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_TAGS%%]
		<?php if ($params->get('show_[%%compobject%%]_tags', 1) AND !empty($this->item->tags) AND !empty($this->item->tags->itemTags)) : ?>
			<?php echo $this->item->tag_layout->render($this->item->tags->itemTags); ?>
		<?php endif; ?>
		[%%ENDIF INCLUDE_TAGS%%]			 
		[%%IF INCLUDE_CREATED%%]
		<?php if ($params->get('show_[%%compobject%%]_created')) : ?>
			<dd class="create">
				<span class="icon-calendar"></span><?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_DATE_ON', JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC3'))); ?>
			</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_CREATED%%]
		[%%IF INCLUDE_PUBLISHED_DATES%%]
		<?php if ($params->get('show_[%%compobject%%]_publish_up') AND $this->item->publish_up > 0) : ?>
			<dd class="published">
				<span class="icon-calendar"></span><?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PUBLISH_UP_ON', JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC3'))); ?>
			</dd>
		<?php endif; ?>
		<?php if ($params->get('show_[%%compobject%%]_publish_down') AND $this->item->publish_down > 0) : ?>
			<dd class="published">
				<span class="icon-calendar"></span><?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PUBLISH_DOWN_ON', JHTML::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC3'))); ?>
			</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
		[%%IF INCLUDE_MODIFIED%%]
		<?php if ($params->get('show_[%%compobject%%]_modified')) : ?>
			<dd class="modified">
				<span class="icon-calendar"></span><?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC3'))); ?>
			</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_MODIFIED%%]
		[%%IF INCLUDE_HITS%%]
		<?php if ($params->get('show_[%%compobject%%]_hits')) : ?>
			<dd class="hits">
				<span class="icon-eye-open"></span><?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits); ?>
			</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_HITS%%]
	 </dl>
</div>