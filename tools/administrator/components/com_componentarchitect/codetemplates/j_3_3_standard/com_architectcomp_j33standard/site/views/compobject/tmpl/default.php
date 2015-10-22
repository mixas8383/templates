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
 * @version			$Id: default.php 408 2014-10-19 18:31:00Z BrianWade $
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
 *	Add style sheets, javascript and behaviours here in the layout so they can be overridden, if required, in a template override 
 */
			
// Add css files for the [%%architectcomp%%] component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%architectcomp%%].css');
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%compobjectplural%%].css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%architectcomp%%]-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%compobjectplural%%]-rtl.css');
}
				
// Add Javascript behaviors
[%%IF INCLUDE_IMAGE%%]
JHtml::_('behavior.caption');
[%%ENDIF INCLUDE_IMAGE%%]

/*
 *	Initialise values for the layout 
 */	
 
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

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
[%%IF INCLUDE_MICRODATA%%]
<div class="[%%architectcomp%%] [%%compobject%%]-view<?php echo $params->get('pageclass_sfx')?>" itemscope itemtype="http://schema.org/CreativeWork">
	[%%IF INCLUDE_LANGUAGE%%]
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
	[%%ENDIF INCLUDE_LANGUAGE%%]
[%%ELSE INCLUDE_MICRODATA%%]
<div class="[%%architectcomp%%] [%%compobject%%]-view<?php echo $params->get('pageclass_sfx')?>">
[%%ENDIF INCLUDE_MICRODATA%%]
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	<?php
		if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND $this->item->paginationrelative):
			 echo $this->item->pagination;
		endif;
	?>	
	[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	<?php if ($params->get('show_[%%compobject%%]_icons',-1) >= 0) : ?>
		<?php if ($params->get('show_[%%compobject%%]_print_icon') 
			OR $params->get('show_[%%compobject%%]_email_icon') 
			[%%IF INCLUDE_ASSETACL%%]
			OR $can_edit 
			OR $can_delete 
			[%%ENDIF INCLUDE_ASSETACL%%]
			): ?>
			<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
				<ul class="dropdown-menu">
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
			</div>
		<?php endif; ?>
	<?php endif; ?>

	[%%IF INCLUDE_NAME%%]
	<?php if ($params->get('show_[%%compobject%%]_name')) : ?>
		<div style="float: left;">
		[%%IF INCLUDE_MICRODATA%%]
			<h2 itemprop="name">
		[%%ELSE INCLUDE_MICRODATA%%]
			<h2>
		[%%ENDIF INCLUDE_MICRODATA%%]
				<?php if ($params->get('link_[%%compobject%%]_names') AND !empty($this->item->readmore_link)) : ?>
		[%%IF INCLUDE_MICRODATA%%]
					<a href="<?php echo $this->item->readmore_link; ?>" itemprop="url">
		[%%ELSE INCLUDE_MICRODATA%%]
					<a href="<?php echo $this->item->readmore_link; ?>">
		[%%ENDIF INCLUDE_MICRODATA%%]
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
	<?php $images = $this->item->images; ?>
	
		[%%IF INCLUDE_HITS%%]
	<?php if ($params->get('show_[%%compobject%%]_hits') != '0' OR 
		($params->get('show_[%%compobject%%]_image', '0') == '1' AND isset($images['image_url']) AND $images['image_url'] != '')): ?>	
		[%%ENDIF INCLUDE_HITS%%]
			<div class="pull-<?php echo htmlspecialchars($params->get('show_[%%compobject%%]_image_float','right')); ?>">
			<?php if ($params->get('show_[%%compobject%%]_image') == '1' AND isset($images['image_url']) AND $images['image_url'] != '') : ?>
			
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
				[%%IF INCLUDE_MICRODATA%%]
						 itemprop="image"
				[%%ENDIF INCLUDE_MICRODATA%%]
					/>
			<?php endif; ?>			 
		[%%IF INCLUDE_HITS%%]
			<?php if ($params->get('show_[%%compobject%%]_hits')) : ?>
			[%%IF INCLUDE_MICRODATA%%]
				<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>" />
			[%%ENDIF INCLUDE_MICRODATA%%]
				<?php echo '<br />'.JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits); ?>
			<?php endif; ?>	
		[%%ENDIF INCLUDE_HITS%%]
		</div>
	<?php endif; ?>	
	[%%ELSE INCLUDE_IMAGE%%]
		[%%IF INCLUDE_HITS%%]
	<?php if ($params->get('show_[%%compobject%%]_hits') != '0') : ?>		
		 <div style="float: right;">
			<?php if ($params->get('show_[%%compobject%%]_hits')) : ?>
			[%%IF INCLUDE_MICRODATA%%]
				<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>" />
			[%%ENDIF INCLUDE_MICRODATA%%]
				<?php echo '<br /><span class="badge badge-info">'.JText::sprintf('[%%COM_ARCHITECTCOMP%%]_HITS', $this->item->hits).'</span>'; ?>
			<?php endif; ?>	
		</div>
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
		[%%IF INCLUDE_DESCRIPTION%%]
				[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
				<?php
					if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative) :
						echo $this->item->pagination;
					endif;
				?>		
				[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
				[%%IF INCLUDE_MICRODATA%%]
				<div itemprop="description">
				[%%ELSE INCLUDE_MICRODATA%%]
				<div>
				[%%ENDIF INCLUDE_MICRODATA%%]
				[%%IF INCLUDE_INTRO%%]
				<?php echo $this->item->introdescription; ?>
				[%%ELSE INCLUDE_INTRO%%]
				<?php echo $this->item->description; ?>
				[%%ENDIF INCLUDE_INTRO%%]
				</div>
				[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
				<?php
					if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND !$this->item->paginationrelative):
						echo $this->item->pagination;
					endif;
				?>		
				[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
		[%%ENDIF INCLUDE_DESCRIPTION%%]
		[%%IF INCLUDE_ACCESS%%]
			<?php //optional teaser intro text for guests ?>
		<?php elseif ($params->get('show_[%%compobject%%]_noauth') == true AND  $user->get('guest') ) : ?>
			[%%IF INCLUDE_INTRO%%]
			<?php if ($params->get('show_[%%compobject%%]_intro')) : ?>
				[%%IF INCLUDE_MICRODATA%%]
				<span itemprop="description">
				<?php echo $this->item->intro; ?>
				</span>
				[%%ELSE INCLUDE_MICRODATA%%]
				<?php echo $this->item->intro; ?>
				[%%ENDIF INCLUDE_MICRODATA%%]				
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
				$link->setVar('return', base64_encode($return_url));?>
				<p class="readmore">
			[%%IF INCLUDE_MICRODATA%%]
					<a href="<?php echo $link; ?>" itemprop="url">
			[%%ELSE INCLUDE_MICRODATA%%]
					<a href="<?php echo $link; ?>">
			[%%ENDIF INCLUDE_MICRODATA%%]
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
								);
			?>
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
			<?php
				$dummy = false;
		$display_fieldset = (
						[%%IF GENERATE_CATEGORIES%%]
							($params->get('show_[%%compobject%%]_category')) OR 
							($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') OR
						[%%ENDIF GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_TAGS%%]
							($params->get('show_[%%compobject%%]_tags')) OR
						[%%ENDIF INCLUDE_TAGS%%]
						[%%IF INCLUDE_CREATED%%]
							($params->get('show_[%%compobject%%]_created_by')) OR
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
							);
			?>
			<?php if ($display_fieldset) : ?>				
					<fieldset>
						<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL'); ?></legend>
			<?php endif; ?>
	
			[%%IF GENERATE_CATEGORIES%%]	
			<?php if ($params->get('show_[%%compobject%%]_parent_category') AND $this->item->parent_slug != '1:root') : ?>
				[%%IF INCLUDE_MICRODATA%%]
				<?php $title = '<span itemprop="genre">'.$this->escape($this->item->parent_title).'</span>'; ?>				
				[%%ELSE INCLUDE_MICRODATA%%]
				<?php $title = $this->escape($this->item->parent_title); ?>				
				[%%ENDIF INCLUDE_MICRODATA%%]
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_PARENT_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_[%%compobject%%]_parent_category') AND $this->item->parent_slug) : ?>
				[%%IF INCLUDE_MICRODATA%%]
							<?php $url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))).'" itemprop="url">'.$title.'</a>'; ?>
				[%%ELSE INCLUDE_MICRODATA%%]
							<?php $url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_[%%compobject%%]_itemid'))).'">'.$title.'</a>'; ?>
				[%%ENDIF INCLUDE_MICRODATA%%]
							<?php echo $url; ?>
						<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>
					</span>
				</div>
			<?php endif;?>	
			<?php if ($params->get('show_[%%compobject%%]_category')) : ?>
				[%%IF INCLUDE_MICRODATA%%]
				<?php $title = '<span itemprop="genre">'.$this->escape($this->item->category_title).'</span>'; ?>
				[%%ELSE INCLUDE_MICRODATA%%]
				<?php $title = $this->escape($this->item->category_title); ?>
				[%%ENDIF INCLUDE_MICRODATA%%]
				<div class="formelm">				
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_[%%compobject%%]_category') AND $this->item->catslug) : ?>
				[%%IF INCLUDE_MICRODATA%%]
							<?php $url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_[%%compobject%%]_itemid'))).'" itemprop="url">'.$title.'</a>'; ?>
				[%%ELSE INCLUDE_MICRODATA%%]
							<?php $url = '<a href="'.JRoute::_([%%ArchitectComp%%]HelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_[%%compobject%%]_itemid'))).'">'.$title.'</a>'; ?>
				[%%ENDIF INCLUDE_MICRODATA%%]
							<?php echo $url; ?>
						<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>	
					</span>
				</div>								
			<?php endif; ?>						
			[%%ENDIF GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_TAGS%%]
			<?php if ($params->get('show_[%%compobject%%]_tags')  == '1' AND !empty($this->item->tags) AND !empty($this->item->tags->itemTags)) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('JTAG'); ?>
					</label>			
					<?php echo $this->item->tag_layout->render($this->item->tags->itemTags); ?>
				</div>
			<?php endif; ?>
			[%%ENDIF INCLUDE_TAGS%%]				
			[%%IF INCLUDE_CREATED%%]
			<?php if ($params->get('show_[%%compobject%%]_created_by') ) : ?>
				<?php $created_by =  $this->item->created_by ?>
				<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>
				[%%IF INCLUDE_MICRODATA%%]
				<?php $created_by = '<span itemprop="name">' . $created_by . '</span>'; ?>
				[%%ENDIF INCLUDE_MICRODATA%%]
				<div class="formelm">				
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_BY_LABEL'); ?> 
					</label>
				[%%IF INCLUDE_MICRODATA%%]
					<span itemprop="creator" itemscope itemtype="http://schema.org/Person">
						<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_created_by') == 1):?>
							<?php echo JHtml::_(
									'link',
									JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
									$created_by, array('itemprop' => 'url'));
							 ?>

						<?php else :?>
							<?php echo $created_by; ?>
						<?php endif; ?>
					</span>
				[%%ELSE INCLUDE_MICRODATA%%]
						<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_[%%compobject%%]_created_by') == 1):?>
							<?php echo JHtml::_(
									'link',
									JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
									$created_by);
							 ?>

						<?php else :?>
							<?php echo $created_by; ?>
						<?php endif; ?>
				[%%ENDIF INCLUDE_MICRODATA%%]
				</div>
			<?php endif; ?>	
			<?php if ($params->get('show_[%%compobject%%]_created_by_alias')) : ?>
				[%%IF INCLUDE_MICRODATA%%]
				<div class="formelm" itemprop="creator" itemscope itemtype="http://schema.org/Person">				
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_BY_ALIAS_LABEL'); ?>
					</label>
					<span itemprop="alternateName">						
					<?php echo !empty($this->item->created_by_alias) ? $this->item->created_by_alias : $empty; ?>
					</span>
				</div>
				[%%ELSE INCLUDE_MICRODATA%%]
				<div class="formelm">				
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_BY_ALIAS_LABEL'); ?>
					</label>
					<?php echo !empty($this->item->created_by_alias) ? $this->item->created_by_alias : $empty; ?>
				</div>
				[%%ENDIF INCLUDE_MICRODATA%%]
			<?php endif; ?>				
			<?php if ($params->get('show_[%%compobject%%]_created')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_LABEL'); ?>
					</label>
				[%%IF INCLUDE_MICRODATA%%]
					<time datetime="<?php echo JHtml::_('date', $this->item->created, 'c'); ?>" itemprop="dateCreated">
				[%%ELSE INCLUDE_MICRODATA%%]
					<time datetime="<?php echo JHtml::_('date', $this->item->created, 'c'); ?>">
				[%%ENDIF INCLUDE_MICRODATA%%]
						<?php echo JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
					</time>
				</div>
			<?php endif; ?>
			[%%ENDIF INCLUDE_CREATED%%]
			[%%IF INCLUDE_MODIFIED%%]
			<?php if ($params->get('show_[%%compobject%%]_modified')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_MODIFIED_LABEL'); ?>				
					</label>
				[%%IF INCLUDE_MICRODATA%%]
					<time datetime="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
				[%%ELSE INCLUDE_MICRODATA%%]
					<time datetime="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>">
				[%%ENDIF INCLUDE_MICRODATA%%]					
						<?php echo JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2')); ?>
					</time>
				</div>
			<?php endif; ?>	
			[%%ENDIF INCLUDE_MODIFIED%%]		
			[%%IF INCLUDE_PUBLISHED_DATES%%]
			<?php if ($params->get('show_[%%compobject%%]_publish_up')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_PUBLISH_UP_LABEL'); ?>				
					</label>
				[%%IF INCLUDE_MICRODATA%%]
					<time datetime="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>" itemprop="datePublished">
				[%%ELSE INCLUDE_MICRODATA%%]
					<time datetime="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>">
				[%%ENDIF INCLUDE_MICRODATA%%]						
						<?php echo $this->item->publish_up > 0 ? JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2')) : JText::_('JNONE'); ?>
					</time>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_[%%compobject%%]_publish_down')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_PUBLISH_DOWN_LABEL'); ?>				
					</label>
					<time datetime="<?php echo JHtml::_('date', $this->item->publish_down, 'c'); ?>">
						<?php echo $this->item->publish_down > 0 ? JHtml::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC2')) : JText::_('JNONE'); ?>
					</time>
				</div>
			<?php endif; ?>
			[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			[%%IF INCLUDE_ASSETACL%%]
			<?php if ($params->get('access-change')): ?>
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
		[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
		<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative):
				 echo $this->item->pagination;
			endif;
		?>	
		[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]					
		<?php echo $this->item->event->afterDisplay[%%CompObject%%]; ?>
	</div>		
</div>