<?php
/**
 * @version 		$Id:$
 * @name			Car (Release 1.0.0)
 * @author			 ()
 * @package			com_car
 * @subpackage		com_car.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: article_info.php 408 2014-10-19 18:31:00Z BrianWade $
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
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_car' );
$empty = $component->params->get('default_empty_field', '');

/*
 *	Layout HTML
 */
?>
<div class="article-info muted">
	<dl class="info">
		<?php if ($params->get('show_model_created_by') ) : ?>
			<dd class="createdby" itemprop="author" itemscope itemtype="http://schema.org/Person"> 
				<?php $created_by =  $this->item->created_by ?>
				<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>
				<?php $created_by = '<span itemprop="name">' . $created_by . '</span>'; ?>
				<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_model_created_by') == 1):?>
					<?php echo JText::sprintf('COM_CAR_CREATED_BY',JHtml::_(
							'link',
							JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
							$created_by, array('itemprop' => 'url')));
					?>
				<?php else :?>
					<?php echo JText::sprintf('COM_CAR_CREATED_BY', $created_by); ?>
				<?php endif; ?>
			</dd>
		<?php endif; ?>
		<?php if ($params->get('show_model_created_by_alias')) : ?>
			<dd class="field" itemprop="author" itemscope itemtype="http://schema.org/Person">
				<strong><?php echo JText::_('COM_CAR_FIELD_CREATED_BY_ALIAS_LABEL'); ?></strong>
				<span itemprop="alternateName">
					<?php echo !empty($this->item->created_by_alias) ? $this->item->created_by_alias : $empty; ?>
				</span>
			</dd>
		<?php endif; ?>					
		<?php if ($params->get('show_model_tags', 1) AND !empty($this->item->tags) AND !empty($this->item->tags->itemTags)) : ?>
			<?php echo $this->item->tag_layout->render($this->item->tags->itemTags); ?>
		<?php endif; ?>
		<?php if ($params->get('show_model_created')) : ?>
			<dd class="create">
				<span class="icon-calendar"></span>
				<time datetime="<?php echo JHtml::_('date', $this->item->created, 'c'); ?>" itemprop="dateCreated">
					<?php echo JText::sprintf('COM_CAR_CREATED_DATE_ON', JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC3'))); ?>
				</time>
			</dd>
		<?php endif; ?>
		<?php if ($params->get('show_model_publish_up') AND $this->item->publish_up > 0) : ?>
			<dd class="published">
				<span class="icon-calendar"></span>
				<time datetime="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>" itemprop="datePublished">
					<?php echo JText::sprintf('COM_CAR_PUBLISH_UP_ON', JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC3'))); ?>
				</time>
			</dd>
		<?php endif; ?>
		<?php if ($params->get('show_model_publish_down') AND $this->item->publish_down > 0) : ?>
			<dd class="published">
				<span class="icon-calendar"></span>
				<time datetime="<?php echo JHtml::_('date', $this->item->publish_down, 'c'); ?>">
					<?php echo JText::sprintf('COM_CAR_PUBLISH_DOWN_ON', JHtml::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC3'))); ?>
				</time
			</dd>
		<?php endif; ?>
		<?php if ($params->get('show_model_modified')) : ?>
			<dd class="modified">
				<span class="icon-calendar"></span>
				<time datetime="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
					<?php echo JText::sprintf('COM_CAR_LAST_UPDATED', JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC3'))); ?>
				</time>
			</dd>
		<?php endif; ?>
		<?php if ($params->get('show_model_hits')) : ?>
			<dd class="hits">
				<span class="icon-eye-open"></span>
				<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>" />
				<?php echo JText::sprintf('COM_CAR_HITS', $this->item->hits); ?>
			</dd>
		<?php endif; ?>
	 </dl>
</div>