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
 * @version			$Id: blog_item.php 417 2014-10-22 14:42:10Z BrianWade $
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
 
// Create a shortcut for params.
$params = &$this->item->params;
$user		= JFactory::getUser();
$layout		= $params->get('[%%compobject%%]_layout', 'default');
$info		= $this->item->params->get('[%%compobject%%]_info_block_position', 0);
[%%IF INCLUDE_ASSETACL%%] 
$can_edit	= $params->get('access-edit');
$can_delete = $params->get('access-delete');
[%%ENDIF INCLUDE_ASSETACL%%]
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$empty = $component->params->get('default_empty_field', '');
[%%IF INCLUDE_IMAGE%%]
$images = $this->item->images;
[%%ENDIF INCLUDE_IMAGE%%]

/*
 *	Layout HTML
 */
?>

[%%IF INCLUDE_STATUS%%]
	[%%IF INCLUDE_PUBLISHED_DATES%%]
<?php if ($this->item->state == 0 
			OR strtotime($this->item->publish_up) > strtotime(JFactory::getDate())
			OR ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) 
			AND $this->item->publish_down != '0000-00-00 00:00:00' )) : ?>
	<div class="system-unpublished">
<?php endif; ?>
	[%%ELSE INCLUDE_PUBLISHED_DATES%%]
<?php if ($this->item->state == 0) : ?>
	<div class="system-unpublished">
<?php endif; ?>
	[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
[%%ENDIF INCLUDE_STATUS%%]
<?php if ($params->get('show_[%%compobject%%]_icons',-1) >= 0) : ?>
	<?php if (($params->get('show_[%%compobject%%]_print_icon') 
		OR $params->get('show_[%%compobject%%]_email_icon') 
		[%%IF INCLUDE_ASSETACL%%]
		OR $can_edit
		OR $can_delete
		[%%ENDIF INCLUDE_ASSETACL%%]
		[%%IF INCLUDE_ACCESS%%]
			) AND $params->get('access-view')
		[%%ELSE INCLUDE_ACCESS%%]
			)
		[%%ENDIF INCLUDE_ACCESS%%]
		) : ?>
		<ul class="actions">
			<?php if ($params->get('show_[%%compobject%%]_print_icon')) : ?>
			<li class="print-icon">
				<?php echo JHtml::_('[%%compobject%%]icon.print_popup', $this->item, $params); ?>
			</li>
			<?php endif; ?>
			<?php if ($params->get('show_[%%compobject%%]_email_icon')) : ?>
			<li class="email-icon">
				<?php echo JHtml::_('[%%compobject%%]icon.email', $this->item, $params); ?>
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
		</ul>
	<?php endif; ?>
<?php endif; ?>
[%%IF INCLUDE_NAME%%]
<?php if ($params->get('show_[%%compobject%%]_name')) : ?>
<h2>
		<?php if ($params->get('link_[%%compobject%%]_names') ) : ?>
		[%%IF GENERATE_CATEGORIES%%]		 
			[%%IF INCLUDE_LANGUAGE%%]
			<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
											$this->item->catid, 
											$this->item->language,
											$layout,									
											$params->get('keep_[%%compobject%%]_itemid'))); ?>">
			[%%ELSE INCLUDE_LANGUAGE%%]
			<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
											$this->item->catid,								
											$layout,									
											$params->get('keep_[%%compobject%%]_itemid'))); ?>">
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ELSE GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_LANGUAGE%%]
			<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
											$this->item->language,									
											$layout,									
											$params->get('keep_[%%compobject%%]_itemid'))); ?>">
			[%%ELSE INCLUDE_LANGUAGE%%]
			<a href="<?php echo JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
											$layout,									
											$params->get('keep_[%%compobject%%]_itemid'))); ?>">
			[%%ENDIF INCLUDE_LANGUAGE%%]	
		[%%ENDIF GENERATE_CATEGORIES%%]			
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
<?php if (($params->get('show_[%%compobject%%]_image', '0') == '1' AND isset($images['image_url'])  AND $images['image_url'] <> "")): ?>	
 <div style="float: right;">
		<?php 
					$image = $images['image_url'];
					
					list($img_width, $img_height) = getimagesize($image);
					
					$display_width = (int) $params->get('show_[%%compobject%%]_intro_image_width','100');
					$display_height = (int) $params->get('show_[%%compobject%%]_intro_image_height','0');
					
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
				<?php echo 'width="'.$display_width.'"'; ?>"
			<?php endif; ?>	
			<?php if ($display_height > 0) : ?>
				<?php echo 'height="'.$display_height.'"'; ?>
			<?php endif; ?>	
			<?php if ($images['image_caption']): ?>
				<?php echo 'class="caption"'.' title="' .htmlspecialchars($images['image_caption']) . '"'; ?>
			<?php endif; ?>				
	[%%IF INCLUDE_NAME%%]
			<?php echo  $images['image_alt_text'] != '' ?'alt="'.$this->escape($images['image_alt_text']).'"':'alt="'.$this->escape($this->item->name).'"';?>
	[%%ELSE INCLUDE_NAME%%]
			<?php echo  $images['image_alt_text'] != '' ?'alt="'.$this->escape($images['image_alt_text']).'"':''; ?>
	[%%ENDIF INCLUDE_NAME%%]
		/>
	</div>
<?php endif; ?>	
[%%ENDIF INCLUDE_IMAGE%%]
<?php // to do not that elegant would be nice to group the params ?>

<?php 
	$dummy = false;
	$use_def_list = (
		[%%IF GENERATE_CATEGORIES%%]
		($params->get('show_[%%compobject%%]_category')) OR 
		($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') OR 
		[%%ENDIF GENERATE_CATEGORIES%%]
		[%%IF INCLUDE_CREATED%%]
		($params->get('show_[%%compobject%%]_created')) OR 
		($params->get('show_[%%compobject%%]_created_by')) OR 			
		[%%ENDIF INCLUDE_CREATED%%]
		[%%IF INCLUDE_MODIFIED%%]
		($params->get('show_[%%compobject%%]_modified')) OR 
		[%%ENDIF INCLUDE_MODIFIED%%]
		[%%IF INCLUDE_HITS%%]
		($params->get('show_[%%compobject%%]_hits')) OR
		[%%ENDIF INCLUDE_HITS%%]
		$dummy
	);
?>

[%%IF INCLUDE_ACCESS%%]
<?php if ($params->get('access-view') 
		AND (!$params->get('show_[%%compobject%%]_readmore')
         [%%IF INCLUDE_INTRO%%]
		OR !$params->get('show_[%%compobject%%]_intro'))
		[%%ELSE INCLUDE_INTRO%%]
		)
		[%%ENDIF INCLUDE_INTRO%%]		
		):?>
[%%ELSE INCLUDE_ACCESS%%]
<?php if (!$params->get('show_[%%compobject%%]_readmore') 
         [%%IF INCLUDE_INTRO%%]
		OR !$params->get('show_[%%compobject%%]_intro')
		[%%ENDIF INCLUDE_INTRO%%]
		):?>
[%%ENDIF INCLUDE_ACCESS%%]
	<?php if ($use_def_list AND $info == 0) : ?>
		<dl class="[%%compobject%%]-info">
			<dt class="[%%compobject%%]-info-term"><?php  echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_INFO'); ?></dt>
		[%%IF GENERATE_CATEGORIES%%]
		<?php if ($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') : ?>
				<dd class="parent-category-name">
					<?php $title = $this->escape($this->item->parent_title);
					$url = '<a href="' . JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))) . '">' . $title . '</a>'; ?>
					<?php if ($params->get('link_[%%compobject%%]_parent_category') AND $this->item->parent_slug) : ?>
						<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $url); ?>
						<?php else : ?>
						<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $title); ?>
					<?php endif; ?>
				</dd>
		<?php endif; ?>
		<?php if ($params->get('show_[%%compobject%%]_category')) : ?>
				<dd class="category-name">
					<?php $title = $this->escape($this->item->category_title);
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
		[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
		[%%IF INCLUDE_MODIFIED%%]
		<?php if ($params->get('show_[%%compobject%%]_modified')) : ?>
				<dd class="modified">
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
				</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_MODIFIED%%]
		[%%IF INCLUDE_CREATED%%]
		<?php if ($params->get('show_[%%compobject%%]_created_by')) : ?>
			<dd class="createdby"> 
				<?php $created_by =  $this->item->created_by ?>
				<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>

				<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_created_by') == 1):?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY',JHTML::_('link',
					JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),$created_by)); ?>

				<?php else :?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $created_by); ?>
				<?php endif; ?>	
			</dd>
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
	<?php endif; ?>

	[%%IF INCLUDE_TAGS%%]
	<?php if ($this->params->get('show_[%%compobject%%]_tags', 1)) : ?>
		<?php 
			$this->item->tags = new JHelperTags;
			$this->item->tags->getItemTags('[%%com_architectcomp%%].[%%compobject%%]', $this->item->id);	
		
			$this->item->tag_layout = new JLayoutFile('joomla.content.tags');
			echo $this->item->tag_layout->render($this->item->tags->itemTags);
		?>
	<?php endif; ?>
	[%%ENDIF INCLUDE_TAGS%%]
		
	[%%IF INCLUDE_DESCRIPTION%%]
		[%%IF INCLUDE_INTRO%%]
	<?php echo $this->item->introdescription; ?>
		[%%ELSE INCLUDE_INTRO%%]
	<?php echo $this->item->description; ?>
		[%%ENDIF INCLUDE_INTRO%%]
	[%%ENDIF INCLUDE_DESCRIPTION%%]	
	
	<?php if ($use_def_list AND $info == 1) : ?>
		<dl class="[%%compobject%%]-info">
			<dt class="[%%compobject%%]-info-term"><?php  echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_INFO'); ?></dt>
		[%%IF GENERATE_CATEGORIES%%]
		<?php if ($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') : ?>
				<dd class="parent-category-name">
					<?php $title = $this->escape($this->item->parent_title);
					$url = '<a href="' . JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))) . '">' . $title . '</a>'; ?>
					<?php if ($params->get('link_[%%compobject%%]_parent_category') AND $this->item->parent_slug) : ?>
						<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $url); ?>
						<?php else : ?>
						<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_PARENT_CATEGORY', $title); ?>
					<?php endif; ?>
				</dd>
		<?php endif; ?>
		<?php if ($params->get('show_[%%compobject%%]_category')) : ?>
				<dd class="category-name">
					<?php $title = $this->escape($this->item->category_title);
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
		[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
		[%%IF INCLUDE_MODIFIED%%]
		<?php if ($params->get('show_[%%compobject%%]_modified')) : ?>
				<dd class="modified">
				<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
				</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_MODIFIED%%]
		[%%IF INCLUDE_CREATED%%]
		<?php if ($params->get('show_[%%compobject%%]_created_by')) : ?>
			<dd class="createdby"> 
				<?php $creator =  $this->item->created_by ?>
				<?php $creator = ($this->item->created_by_name ? $this->item->created_by_name : $creator);?>

				<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_created_by') == 1):?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY',JHTML::_('link',
					JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),$creator)); ?>

				<?php else :?>
					<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', $creator); ?>
				<?php endif; ?>	
			</dd>
		<?php endif; ?>	
		[%%ENDIF INCLUDE_CREATED%%]
		[%%IF INCLUDE_HITS%%]
		<?php if ($params->get('show_[%%compobject%%]_hits')) : ?>
				<dd class="hits">
					<span class="badge badge-info">
						<?php echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits); ?>
					</span>				
				</dd>
		<?php endif; ?>
		[%%ENDIF INCLUDE_HITS%%]

		</dl>
	<?php endif; ?>
	[%%IF INCLUDE_ACCESS%%]
<?php else : ?>
	[%%ENDIF INCLUDE_ACCESS%%]
		[%%IF INCLUDE_INTRO%%]
	<?php //optional teaser intro text for guests ?>		
	<?php if ($params->get('show_[%%compobject%%]_intro')) : ?>
			[%%IF INCLUDE_ACCESS%%]
		<?php if ($params->get('access-view') OR (!$params->get('access-view') AND $params->get('show_[%%compobject%%]_noauth'))) : ?>
			<?php echo $this->item->intro; ?>
		<?php endif; ?>	
			[%%ELSE INCLUDE_ACCESS%%]	
		<?php echo $this->item->intro; ?>
			[%%ENDIF INCLUDE_ACCESS%%]			
	<?php endif; ?>	
		[%%ENDIF INCLUDE_INTRO%%]		
	<?php //Optional link to let them register to see the whole [%%compobject_name%%]. ?>
	<?php if ($params->get('show_[%%compobject%%]_readmore')) :
			[%%IF INCLUDE_ACCESS%%]
			if ($params->get('access-view')) :
			[%%ENDIF INCLUDE_ACCESS%%]
				[%%IF GENERATE_CATEGORIES%%]		 
					[%%IF INCLUDE_LANGUAGE%%]
				$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																							$this->item->catid, 
																							$this->item->language,
																							$layout,									
																							$params->get('keep_[%%compobject%%]_itemid')));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																							$this->item->catid,								
																							$layout,									
																							$params->get('keep_[%%compobject%%]_itemid')));
					[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%ELSE GENERATE_CATEGORIES%%]
					[%%IF INCLUDE_LANGUAGE%%]
				$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																							$this->item->language,									
																							$layout,									
																							$params->get('keep_[%%compobject%%]_itemid')));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$link = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																							$layout,									
																							$params->get('keep_[%%compobject%%]_itemid')));
					[%%ENDIF INCLUDE_LANGUAGE%%]	
				[%%ENDIF GENERATE_CATEGORIES%%]	
			[%%IF INCLUDE_ACCESS%%]
			else :
				if ($params->get('show_[%%compobject%%]_noauth') AND $user->get('guest')) :
					$menu = JFactory::getApplication()->getMenu();
					$active = $menu->getActive();
					$item_id = $active->id;
					$link_1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $item_id);
					[%%IF GENERATE_CATEGORIES%%]		 
						[%%IF INCLUDE_LANGUAGE%%]
					$return_url = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																								$this->item->catid, 
																								$this->item->language,									
																								$layout,									
																								$params->get('keep_[%%compobject%%]_itemid')));
						[%%ELSE INCLUDE_LANGUAGE%%]
					$return_url = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																								$this->item->catid,								
																								$layout,									
																								$params->get('keep_[%%compobject%%]_itemid')));
						[%%ENDIF INCLUDE_LANGUAGE%%]
					[%%ELSE GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_LANGUAGE%%]
					$return_url = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																								$this->item->language,									
																								$layout,									
																								$params->get('keep_[%%compobject%%]_itemid')));
						[%%ELSE INCLUDE_LANGUAGE%%]
					$return_url = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($this->item->slug, 
																								$layout,									
																								$params->get('keep_[%%compobject%%]_itemid')));
						[%%ENDIF INCLUDE_LANGUAGE%%]	
					[%%ENDIF GENERATE_CATEGORIES%%]	
					$link = new JUri($link_1);
					$link->setVar('return', base64_encode($return_url));
				endif;
			endif;?>
			[%%ELSE INCLUDE_ACCESS%%]
			endif;?>
			[%%ENDIF INCLUDE_ACCESS%%]			
			<p class="readmore">
				<a href="<?php echo $link; ?>">
				<?php
				$item_params = new JRegistry;				
				$item_params->loadString($this->item->params);
				if ($item_params->get('[%%compobject%%]_alternative_readmore') == null) :
						if ($params->get('access-view')) :
							[%%IF INCLUDE_NAME%%]
							if ($params->get('show_[%%compobject%%]_readmore_name') == 0) :
								echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_READ_MORE');
							else :
								echo JText::_('[%%COM_ARCHITECTCOMP%%]_READMORE_NAME');
							echo JHtml::_('string.truncate', ($this->item->name), $params->get('[%%compobject%%]_readmore_limit'));
							[%%ELSE INCLUDE_NAME%%]
							echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_READ_MORE');
							[%%ENDIF INCLUDE_NAME%%]														
						endif; 
					else :
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
					
					endif;
				else :
					echo $this->item->[%%compobject%%]_alternative_readmore;
					[%%IF INCLUDE_NAME%%]
					if ($params->get('show_[%%compobject%%]_readmore_name') == 1) :
						echo JHtml::_('string.truncate', ': '.($this->item->name), $params->get('[%%compobject%%]_readmore_limit'));
					endif;	
					[%%ENDIF INCLUDE_NAME%%]	
				endif;?>
			</a>
		</p>
	<?php endif; ?>
	[%%IF INCLUDE_ACCESS%%]	
<?php endif; ?>
	[%%ENDIF INCLUDE_ACCESS%%]

[%%IF INCLUDE_STATUS%%]
	[%%IF INCLUDE_PUBLISHED_DATES%%]
<?php if ($this->item->state == 0 
			OR strtotime($this->item->publish_up) > strtotime(JFactory::getDate())
			OR ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) 
			AND $this->item->publish_down != '0000-00-00 00:00:00' )) : ?>
	</div>
<?php endif; ?>
	[%%ELSE INCLUDE_PUBLISHED_DATES%%]
<?php if ($this->item->state == 0) : ?>
	</div>
<?php endif; ?>
	[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
[%%ENDIF INCLUDE_STATUS%%]

<div class="item-separator"></div>
<?php echo $this->item->event->afterDisplay[%%CompObject%%]; ?>
