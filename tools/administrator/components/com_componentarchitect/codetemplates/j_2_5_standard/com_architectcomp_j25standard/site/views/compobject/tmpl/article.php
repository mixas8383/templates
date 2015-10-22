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
 * @version			$Id: article.php 418 2014-10-22 14:42:36Z BrianWade $
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
[%%IF INCLUDE_ASSETACL%%]
$can_edit	= $params->get('access-edit');
$can_delete	= $params->get('access-delete');
[%%ENDIF INCLUDE_ASSETACL%%]
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$empty = $component->params->get('default_empty_field', '');
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="[%%architectcomp%%] [%%compobject%%]-article<?php echo $params->get('pageclass_sfx')?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	<?php
		if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND $this->item->paginationrelative) :
			echo $this->item->pagination;
		endif;
	 ?>
	[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	<?php if ($params->get('show_[%%compobject%%]_print_icon') 
				OR $params->get('show_[%%compobject%%]_email_icon') 
				[%%IF INCLUDE_ASSETACL%%]
				OR $can_edit 
				OR $can_delete 
				[%%ENDIF INCLUDE_ASSETACL%%]
				): ?>
			<ul class="actions">
				<?php if (!$this->print) : ?>
					[%%IF INCLUDE_ACCESS%%]		
					<?php if ($params->get('access-view')) : ?>
					[%%ENDIF INCLUDE_ACCESS%%]
						<?php if ($params->get('show_[%%compobject%%]_print_icon')) : ?>
						<li class="print-icon">
								<?php echo JHtml::_('[%%compobject%%]icon.print_popup',  $this->item, $params); ?>
						</li>
						<?php endif; ?>

						<?php if ($params->get('show_[%%compobject%%]_email_icon')) : ?>
						<li class="email-icon">
								<?php echo JHtml::_('[%%compobject%%]icon.email',  $this->item, $params); ?>
						</li>
						<?php endif; ?>
						[%%IF INCLUDE_ASSETACL%%]
						<?php if ($can_edit) : ?>
						[%%ENDIF INCLUDE_ASSETACL%%]
							<li class="edit-icon">
								<?php echo JHtml::_('[%%compobject%%]icon.edit', $this->item, $params); ?>
							</li>
						[%%IF INCLUDE_ASSETACL%%]
						<?php endif; ?>
						<?php if ($can_delete) : ?>
						[%%ENDIF INCLUDE_ASSETACL%%]						
							<li class="delete-icon">
								<?php echo JHtml::_('[%%compobject%%]icon.delete',$this->item, $params); ?>
							</li>
						[%%IF INCLUDE_ASSETACL%%]
						<?php endif; ?>
						[%%ENDIF INCLUDE_ASSETACL%%]
					[%%IF INCLUDE_ACCESS%%]		
					<?php endif; ?>
					[%%ENDIF INCLUDE_ACCESS%%]		
				<?php else : ?>
					<li>
						<?php echo JHtml::_('[%%compobject%%]icon.print_screen',  $this->item, $params); ?>
					</li>
				<?php endif; ?>
			</ul>
	<?php endif; ?>

	[%%IF INCLUDE_NAME%%]
	<?php if ($params->get('show_[%%compobject%%]_name')
		[%%IF INCLUDE_ASSETACL%%]
				OR $params->get('access-edit') 
		[%%ENDIF INCLUDE_ASSETACL%%]
				): ?>
		<h2>
			<?php if ($params->get('link_[%%compobject%%]_names') AND !empty($this->item->readmore_link)) : ?>
				<a href="<?php echo $this->item->readmore_link; ?>">
				<?php echo $this->escape($this->item->name); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->item->name); ?>
			<?php endif; ?>
		</h2>
	<?php endif; ?>
	<?php  echo $this->item->event->afterDisplay[%%CompObject%%]Name;
	?>
	[%%ENDIF INCLUDE_NAME%%]

	<?php echo $this->item->event->beforeDisplay[%%CompObject%%]; ?>
	 [%%IF INCLUDE_IMAGE%%]
	<?php if (($params->get('show_[%%compobject%%]_image', '0') == '1' AND $this->item->image_url != "")) : ?>	
		<div style="float: right;">
			<?php 
				$image = $this->item->image_url;
					 
				list($img_width, $img_height) = getimagesize($image);
				$display_width = 0;
				$display_height = 0; 
				
				if ((int) $params->get('show_[%%compobject%%]_image_width') > 0) :
					$display_width = (int) $params->get('show_[%%compobject%%]_image_width');
				endif;

				if ((int) $params->get('show_[%%compobject%%]_image_height','0') > 0) :
					$display_height = (int) $params->get('show_[%%compobject%%]_image_height');
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
		[%%IF INCLUDE_NAME%%]
					alt="<?php if ($this->item->image_alt_text != '') : echo  $this->escape($this->item->image_alt_text); else : echo $this->escape($this->item->name); endif; ?>"
		[%%ELSE INCLUDE_NAME%%]
					alt="<?php if ($this->item->image_alt_text != '') : echo  $this->escape($this->item->image_alt_text); endif; ?>"
		[%%ENDIF INCLUDE_NAME%%]
			/>
		</div>
	<?php endif; ?>	
	[%%ENDIF INCLUDE_IMAGE%%]
	[%%IF INCLUDE_ACCESS%%]
	<?php if ($params->get('access-view')) : ?>	
	[%%ENDIF INCLUDE_ACCESS%%]
		<?php $dummy = false;
				$use_def_list = (
							 [%%IF GENERATE_CATEGORIES%%]
							($params->get('show_[%%compobject%%]_category')) OR 
							($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') OR
							[%%ENDIF GENERATE_CATEGORIES%%]	
							[%%IF INCLUDE_CREATED%%]
							($params->get('show_[%%compobject%%]_creator')) OR 									
							($params->get('show_[%%compobject%%]_created')) OR 
							[%%ENDIF INCLUDE_CREATED%%]
							[%%IF INCLUDE_MODIFIED%%]
							($params->get('show_[%%compobject%%]_modified')) OR
							[%%ENDIF INCLUDE_MODIFIED%%]
							[%%IF INCLUDE_HITS%%]
							($params->get('show_[%%compobject%%]_hits')) OR
							[%%ENDIF INCLUDE_HITS%%]
							$dummy
							); ?>

		<?php if ($use_def_list) : ?>
		<dl class="info">
			<dt class="info-title"><?php  echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_INFO'); ?></dt>
		<?php endif; ?>
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
		[%%IF INCLUDE_CREATED%%]
		<?php if ($params->get('show_[%%compobject%%]_created')) : ?>
				<dd class="create">
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_DATE_ON', JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
				</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_CREATED%%]
		[%%IF INCLUDE_PUBLISHED_DATES%%]
		<?php if ($params->get('show_[%%compobject%%]_publish_up')) : ?>
		<dd class="modified">
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PUBLISH_UP_ON', JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_[%%compobject%%]_publish_down')) : ?>
		<dd class="modified">
			<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PUBLISH_DOWN_ON', JHTML::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
		[%%IF INCLUDE_MODIFIED%%]
		<?php if ($params->get('show_[%%compobject%%]_modified')) : ?>
				<dd class="modified">
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
				</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_MODIFIED%%]
		[%%IF INCLUDE_CREATED%%]
		<?php if ($params->get('show_[%%compobject%%]_creator') ) : ?>
			<dd class="createdby"> 
				<?php $creator =  $this->item->created_by ?>
				<?php $creator = ($this->item->created_by_name ? $this->item->created_by_name : $creator);?>

				<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_creator') == 1):?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY',JHTML::_(
							'link',
							JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
							$creator
					)); ?>

				<?php else :?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $creator); ?>
				<?php endif; ?>

			</dd>
		<?php endif; ?>	
		[%%ENDIF INCLUDE_CREATED%%]
		[%%IF INCLUDE_HITS%%]
		<?php if ($params->get('show_[%%compobject%%]_hits')) : ?>
				<dd class="hits">
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits); ?>
				</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_HITS%%]
		<?php if ($use_def_list) : ?>
		 </dl>
		<?php endif; ?>
		<?php
			if (isset ($this->item->toc)) :
				echo $this->item->toc;
			endif;
		?>	
		[%%IF INCLUDE_URLS%%]
			<?php
				if (isset($this->item->urls) AND $params->get('show_[%%compobject%%]_urls') == '1' AND $params->get('show_[%%compobject%%]_urls_position')=='0') :
					echo $this->loadTemplate('urls'); 
				endif;
			?>	
		[%%ENDIF INCLUDE_URLS%%]		
		[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
		<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
				echo $this->item->pagination;
			 endif;
		?>	
		[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]	
		[%%IF INCLUDE_DESCRIPTION%%]
			[%%IF INCLUDE_INTRO%%]
		<?php echo $this->item->introdescription; ?>
			[%%ELSE INCLUDE_INTRO%%]
		<?php echo $this->item->description; ?>
			[%%ENDIF INCLUDE_INTRO%%]
		[%%ENDIF INCLUDE_DESCRIPTION%%]
		[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
		<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND!$this->item->paginationrelative):
				echo $this->item->pagination;
			endif;
		?>		
		[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]			
		<?php
			$dummy = false;
			$use_fields_list = (
					[%%FOREACH OBJECT_FIELD%%]
						[%%IF FIELD_NOT_HIDDEN%%]
						($params->get('show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) OR 
						[%%ENDIF FIELD_NOT_HIDDEN%%]
					[%%ENDFOR OBJECT_FIELD%%]
					[%%FOREACH REGISTRY_FIELD%%]
						($params->get('show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) OR 
					[%%ENDFOR REGISTRY_FIELD%%]
						$dummy
						);
		?>
		<?php if ($use_fields_list) : ?>		
		<dl class="info">
			<dt class="info-title"><?php  echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_INFO'); ?></dt>
		<?php endif; ?>		
		
		[%%FOREACH OBJECT_FIELD%%]
			[%%IF FIELD_NOT_HIDDEN%%]
			<?php if ($params->get('show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL'); ?></strong>
					<?php
						[%%FIELD_SITE_VALUE%%]
					?>
				</dd>
			<?php endif; ?>
			[%%ENDIF FIELD_NOT_HIDDEN%%]
		[%%ENDFOR OBJECT_FIELD%%]
		[%%FOREACH REGISTRY_FIELD%%]
			<?php $field_array = $this->item->[%%FIELD_CODE_NAME%%]; ?>
			<?php if ($params->get('show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) : ?>
				[%%FOREACH REGISTRY_ENTRY%%]
					[%%IF FIELD_NOT_HIDDEN%%]
				<dd class="field">					
					<strong><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL'); ?></strong>
					<?php
						[%%REGISTRY_ENTRY_SITE_VALUE%%]
					?>
				</dd>
					[%%ENDIF FIELD_NOT_HIDDEN%%]							
				[%%ENDFOR REGISTRY_ENTRY%%]
			<?php endif; ?>	
		[%%ENDFOR REGISTRY_FIELD%%]
		<?php if ($use_fields_list) : ?>		
		</dl>	
		<?php endif; ?>
		[%%IF INCLUDE_URLS%%]
		<?php
			if (isset($this->item->urls) AND $params->get('show_[%%compobject%%]_urls') == '1' AND $params->get('show_[%%compobject%%]_urls_position')=='1') :
				echo $this->loadTemplate('urls');
			endif;
		?>	
		[%%ENDIF INCLUDE_URLS%%]		
	[%%IF INCLUDE_ACCESS%%]	
		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_[%%compobject%%]_noauth') AND $user->get('guest') ) : ?>
		[%%IF INCLUDE_INTRO%%]
		<?php if ($params->get('show_[%%compobject%%]_intro')) : ?>
			<?php echo $this->item->intro; ?>
		<?php endif; ?>
		[%%ENDIF INCLUDE_INTRO%%]
		<?php //Optional link to let them register to see the whole [%%compobject_name%%]. ?>
		<?php if ($params->get('show_[%%compobject%%]_readmore')) :
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
							[%%IF INCLUDE_PARAMS_RECORD%%] 
							if ($this->item->[%%compobject%%]_alternative_readmore == null) :
							[%%ENDIF INCLUDE_PARAMS_RECORD%%]
							[%%IF INCLUDE_NAME%%]
								if ($params->get('show_[%%compobject%%]_readmore_name') == 0) :					
									echo JText::_('[%%COM_ARCHITECTCOMP%%]_REGISTER_TO_READ_MORE');
								else :
									echo JText::_('[%%COM_ARCHITECTCOMP%%]_REGISTER_TO_READMORE_NAME');
									echo JHtml::_('string.truncate', ($this->item->name), $params->get('[%%compobject%%]_readmore_limit'));
								endif;
							[%%ELSE INCLUDE_NAME%%]
									echo JText::_('[%%COM_ARCHITECTCOMP%%]_REGISTER_TO_READ_MORE');
							[%%ENDIF INCLUDE_NAME%%]							
							[%%IF INCLUDE_PARAMS_RECORD%%]	
							else :
								echo $this->item->[%%compobject%%]_alternative_readmore;
								[%%IF INCLUDE_NAME%%]
								if ($params->get('show_[%%compobject%%]_readmore_name') == 1) :
									echo JHtml::_('string.truncate', ': '.($this->item->name), $params->get('[%%compobject%%]_readmore_limit'));
								endif;
								[%%ENDIF INCLUDE_NAME%%]
							endif;
							[%%ENDIF INCLUDE_PARAMS_RECORD%%]
							?>
						</a>
					</p>
		<?php endif; ?>
		[%%ENDIF INCLUDE_INTRO%%]
	<?php endif; ?>
	[%%ENDIF INCLUDE_ACCESS%%]	
	[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	<?php
		if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative) :
			 echo $this->item->pagination;
		endif;
	?>	
	[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	<?php echo $this->item->event->afterDisplay[%%CompObject%%]; ?>
</div>