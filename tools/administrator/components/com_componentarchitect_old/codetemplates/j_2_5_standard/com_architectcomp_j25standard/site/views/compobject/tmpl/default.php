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
 * @version			$Id: default.php 418 2014-10-22 14:42:36Z BrianWade $
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
<div class="[%%architectcomp%%] [%%compobject%%]-view<?php echo $params->get('pageclass_sfx')?>">
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
	<?php if ($params->get('show_[%%compobject%%]_name')) : ?>
		<div style="float: left;">

			<h2>
				<?php if ($params->get('link_[%%compobject%%]_names') AND !empty($this->item->readmore_link)) : ?>
					<a href="<?php echo $this->item->readmore_link; ?>">
					<?php echo $this->escape($this->item->name); ?></a>
				<?php else : ?>
					<?php echo $this->escape($this->item->name); ?>
				<?php endif; ?>
			</h2>
		</div>
	<?php endif; ?>
	<?php  echo $this->item->event->afterDisplay[%%CompObject%%]Name;	?>
	[%%ENDIF INCLUDE_NAME%%]
	
	<?php echo $this->item->event->beforeDisplay[%%CompObject%%]; ?>
	[%%IF INCLUDE_IMAGE%%]
		[%%IF INCLUDE_HITS%%]
	<?php if ($params->get('show_[%%compobject%%]_hits') != '0' OR 
		($params->get('show_[%%compobject%%]_image', '0') == '1' AND $this->item->image_url != '')) : ?>	
		[%%ELSE INCLUDE_HITS%%]			
	<?php if (($params->get('show_[%%compobject%%]_image', '0') == '1' AND $this->item->image_url != '')) : ?>	
		[%%ENDIF INCLUDE_HITS%%]	
		 <div style="float: right;">
			<?php if ($params->get('show_[%%compobject%%]_image') == '1' AND $this->item->image_url != '') : ?>
				<?php 
					$image = $this->item->image_url;
					list($img_width, $img_height) = getimagesize($image);
					$display_width = 0;
					$display_height = 0;
					 
					if ((int) $params->get('show_[%%compobject%%]_image_width') > 0) :
						$display_width = (int) $params->get('show_[%%compobject%%]_image_width');
					endif;
					
					if ((int) $params->get('show_[%%compobject%%]_image_height','0') > 0) :
						$display_height = (int) $params->get('show_[%%compobject%%]_image_height','0');
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
			<?php endif; ?>			 
		[%%IF INCLUDE_HITS%%]
			<?php if ($params->get('show_[%%compobject%%]_hits')) : ?>

				<?php echo '<br />'.JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits); ?>
			<?php endif; ?>	
		[%%ENDIF INCLUDE_HITS%%]
		</div>
	<?php endif; ?>	
	[%%ELSE INCLUDE_IMAGE%%]
		[%%IF INCLUDE_HITS%%]
	<?php if ($params->get('show_[%%compobject%%]_hits') != '0'): ?>	
			<?php if ($params->get('show_[%%compobject%%]_hits')) : ?>

				<?php echo '<br />'.JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits); ?>
			<?php endif; ?>	
	<?php endif; ?>	
		[%%ENDIF INCLUDE_HITS%%]
	[%%ENDIF INCLUDE_IMAGE%%]	
	<div style="clear:both; padding-top: 10px;">

		[%%IF INCLUDE_ACCESS%%]
		<?php if ($params->get('access-view')) :?>
		[%%ENDIF INCLUDE_ACCESS%%]	
		[%%IF INCLUDE_URLS%%]
			<?php
				if (isset($this->item->urls) AND $params->get('show_[%%compobject%%]_urls') == '1' AND $params->get('show_[%%compobject%%]_urls_position')=='0') :
					echo $this->loadTemplate('urls'); 
				endif;
			?>	
		[%%ENDIF INCLUDE_URLS%%]				
		[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
			<?php
				if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative) :
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
				if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND !$this->item->paginationrelative) :
					echo $this->item->pagination;
				endif;
			?>	
		[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
		[%%IF INCLUDE_ACCESS%%]
			<?php //optional teaser intro text for guests ?>
		<?php elseif ($params->get('show_[%%compobject%%]_noauth') == true AND  $user->get('guest') ) : ?>
			[%%IF INCLUDE_INTRO%%]
			<?php if ($params->get('show_[%%compobject%%]_intro')) : ?>
				<?php echo $this->item->intro;
				endif;
			?>	
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
	</div>
	<div style="padding-top: 10px;">
		[%%IF INCLUDE_ACCESS%%]	
		<?php if ($params->get('access-view')) : ?>	
		[%%ENDIF INCLUDE_ACCESS%%]			

			<form action="" name="[%%compobject%%]Form" id="[%%compobject%%]Form">
			[%%FOREACH OBJECT_FIELDSET%%]
			<?php $dummy = false;
					$display_fieldset = (
							[%%FOREACH OBJECT_FIELD%%]
								[%%IF FIELD_NOT_HIDDEN%%]
								($params->get('show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) OR 
								[%%ENDIF FIELD_NOT_HIDDEN%%]
							[%%ENDFOR OBJECT_FIELD%%]
								$dummy
								); ?>
			<?php if ($display_fieldset) : ?>				
					<fieldset>	
						<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_[%%FIELDSET_CODE_NAME_UPPER%%]_LABEL'); ?></legend>
			<?php endif; ?>
						<div style="padding-top: 10px;">			
						[%%FOREACH OBJECT_FIELD%%]
							[%%IF FIELD_NOT_HIDDEN%%]
							<?php if ($params->get('show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) : ?>
							<div class="formelm">
								<label>
									<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL'); ?>
								</label>
								<span>
									<?php
										[%%FIELD_SITE_VALUE%%]
									?>
								</span>
							</div>	
							<?php endif; ?>
							[%%ENDIF FIELD_NOT_HIDDEN%%]
								
						[%%ENDFOR OBJECT_FIELD%%]	
						</div>
			<?php if ($display_fieldset) : ?>				
					</fieldset>	
			<?php endif;?>	
			[%%ENDFOR OBJECT_FIELDSET%%]
			[%%FOREACH REGISTRY_FIELD%%]
			<?php $field_array = $this->item->[%%FIELD_CODE_NAME%%]; ?>
			<?php if ($params->get('show_[%%compobject%%]_[%%FIELD_CODE_NAME%%]')) : ?>
					<fieldset>	
						<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL'); ?></legend>
						[%%FOREACH REGISTRY_ENTRY%%]
							[%%IF FIELD_NOT_HIDDEN%%]
						<div class="formelm">
							<label>
								<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL'); ?>
							</label>
							<span>
								<?php
									[%%REGISTRY_ENTRY_SITE_VALUE%%]
								?>
							</span>
						</div>	
							[%%ENDIF FIELD_NOT_HIDDEN%%]						
						[%%ENDFOR REGISTRY_ENTRY%%]
					</fieldset>	
			<?php endif;?>				
			
			[%%ENDFOR REGISTRY_FIELD%%]	
			<?php $dummy = false;
					$display_fieldset = (
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
							[%%IF INCLUDE_PUBLISHED_DATES%%]
								($params->get('show_[%%compobject%%]_publish_up')) OR
								($params->get('show_[%%compobject%%]_publish_down')) OR
							[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
								($params->get('show_[%%compobject%%]_admin') AND $this->item->params->get('access-change')) OR							
								$dummy
								); ?>
			<?php if ($display_fieldset) : ?>				
					<fieldset>
						<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL'); ?></legend>
			<?php endif; ?>
	
			[%%IF GENERATE_CATEGORIES%%]	
			<?php if ($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') : ?>
				<?php $title = $this->escape($this->item->parent_title);
					  $url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))).'">'.$title.'</a>';
				?>				
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_PARENT_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_[%%compobject%%]_parent_category') AND $this->item->parent_slug) : ?>
							<?php echo $url; ?>
							<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>
					</span>
				</div>
			<?php endif;?>	
			<?php if ($params->get('show_[%%compobject%%]_category')) : ?>
				<?php $title = $this->escape($this->item->category_title);
				
					$url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_[%%compobject%%]_itemid'))).'">'.$title.'</a>';
				?>
				<div class="formelm">				
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_[%%compobject%%]_category') AND $this->item->catslug) : ?>
							<?php echo $url; ?>
						<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>	
					</span>
				</div>								
			<?php endif; ?>						
			[%%ENDIF GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_CREATED%%]
			<?php if ($params->get('show_[%%compobject%%]_creator') ) : ?>
				<?php $creator =  $this->item->created_by ?>
				<?php $creator = ($this->item->created_by_name ? $this->item->created_by_name : $creator);?>

				<div class="formelm">				
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_BY_LABEL'); ?> 
					</label>
					<span>
						<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_creator') == 1) :?>
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
			<?php if ($params->get('show_[%%compobject%%]_created')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_LABEL'); ?>
					</label>
					<span>
						<?php echo JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
					</span>
				</div>
			<?php endif; ?>
			[%%ENDIF INCLUDE_CREATED%%]
			[%%IF INCLUDE_MODIFIED%%]
			<?php if ($params->get('show_[%%compobject%%]_modified')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_MODIFIED_LABEL'); ?>				
					</label>
					<span>
						<?php echo JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2')); ?>
					</span>
				</div>
			<?php endif; ?>	
			[%%ENDIF INCLUDE_MODIFIED%%]		
			[%%IF INCLUDE_PUBLISHED_DATES%%]
			<?php if ($params->get('show_[%%compobject%%]_publish_up')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_PUBLISH_UP_LABEL'); ?>				
					</label>
					<span>
						<?php echo JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2')); ?>
					</span>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_[%%compobject%%]_publish_down')) : ?>
				<div class="formelm">
				<label>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_PUBLISH_DOWN_LABEL'); ?>				
				</label>
				<span>
					<?php echo JHTML::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC2')); ?>
				</span>
				</div>
			<?php endif; ?>
			[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			[%%IF INCLUDE_ASSETACL%%]
			<?php if ($this->item->params->get('access-change')): ?>
			[%%ENDIF INCLUDE_ASSETACL%%]
				<?php if ($params->get('show_[%%compobject%%]_admin')) : ?>
				
					[%%IF INCLUDE_STATUS%%]
					<div class="formelm">
						<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_STATUS_LABEL'); ?>
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
					[%%ENDIF INCLUDE_STATUS%%]
					[%%IF INCLUDE_FEATURED%%]
					<div class="formelm">
						<label>
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_FEATURED_LABEL'); ?>
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
					[%%ENDIF INCLUDE_FEATURED%%]
					[%%IF INCLUDE_ACCESS%%]
					<div class="formelm">
						<label>
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_ACCESS_LABEL'); ?>
						</label>
						<span>
							<?php echo $this->item->access_title; ?>
						</span>
					</div>
					[%%ENDIF INCLUDE_ACCESS%%]
					[%%IF INCLUDE_ORDERING%%]
					<div class="formelm">
						<label>
							<?php echo JText::_('JFIELD_ORDERING_LABEL'); ?>
						</label>
						<span>
							<?php echo $this->item->ordering; ?>
						</span>
					</div>	
					[%%ENDIF INCLUDE_ORDERING%%]					
				<?php endif; ?>
				
			[%%IF INCLUDE_ASSETACL%%]
			<?php endif; ?>
			[%%ENDIF INCLUDE_ASSETACL%%]
			
			[%%IF INCLUDE_URLS%%]
			<?php
				if (isset($this->item->urls) AND $params->get('show_[%%compobject%%]_urls') == '1' AND $params->get('show_[%%compobject%%]_urls_position')=='1') :
					echo $this->loadTemplate('urls');
				endif;
			?>	
			[%%ENDIF INCLUDE_URLS%%]
			<?php if ($display_fieldset) : ?>				
					</fieldset>	
			<?php endif;?>	
				</form>
		[%%IF INCLUDE_ACCESS%%]			
		<?php endif; ?>	
		[%%ENDIF INCLUDE_ACCESS%%]	
		<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative) :
				 echo $this->item->pagination;
			endif;
		?>							
		<?php echo $this->item->event->afterDisplay[%%CompObject%%]; ?>
	</div>		
</div>