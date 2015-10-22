<?php
/**
 * @version 		$Id:$
 * @name			Slider (Release 1.0.0)
 * @author			 ()
 * @package			com_slider
 * @subpackage		com_slider.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: article.php 408 2014-10-19 18:31:00Z BrianWade $
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
			
// Add css files for the slider component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/com_slider/css/site_slider.css');
$this->document->addStyleSheet(JUri::root().'media/com_slider/css/site_items.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/com_slider/css/site_slider-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/com_slider/css/site_items-rtl.css');
}
				
// Add Javascript behaviors
JHtml::_('behavior.caption');

/*
 *	Initialise values for the layout 
 */	
 		
// Create shortcuts to some parameters.
$params		= &$this->item->params;
$user		= JFactory::getUser();
$info		= $this->item->params->get('item_info_block_position', 0);
$can_edit	= $params->get('access-edit');
$can_delete	= $params->get('access-delete');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_slider' );
$empty = $component->params->get('default_empty_field', '');
$dummy = false;
$use_def_list = (
			($params->get('show_item_category')) OR 
			($params->get('show_item_parent_category') AND $this->item->parent_slug != '1:root') OR
				($params->get('show_item_tags')) OR
			($params->get('show_item_created_by')) OR 									
			($params->get('show_item_created')) OR 
			($params->get('show_item_modified')) OR
			($params->get('show_item_hits')) OR
			$dummy
			);

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_SLIDER_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="slider item-article<?php echo $params->get('pageclass_sfx')?>" itemscope itemtype="http://schema.org/Article">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">	
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<?php if (!$use_def_list AND $this->print) : ?>
		<div id="pop-print" class="btn">
			<?php echo JHtml::_('itemicon.print_screen', $this->item, $params); ?>
		</div>
		<div class="clearfix"> </div>
	<?php endif; ?>	
	<?php if ($params->get('show_item_icons',-1) >= 0) : ?>
		<?php
			if ($params->get('show_item_print_icon') 
					OR $params->get('show_item_email_icon') 
					OR $can_edit 
					OR $can_delete 
					):
		?>
			<?php if (!$this->print) : ?>
				<?php if ($params->get('access-view')) : ?>
					<div class="btn-group pull-right">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
						<ul class="dropdown-menu">			
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
						</ul>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($use_def_list) : ?>
					<div id="pop-print" class="btn">
						<?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($params->get('show_item_name')
				OR $params->get('access-edit') 
				): ?>
		<h2 itemprop="name">
			<?php if ($params->get('link_item_names') AND !empty($this->item->readmore_link)) : ?>
				<a href="<?php echo $this->item->readmore_link; ?>" itemprop="url">
				<?php echo $this->escape($this->item->name); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->item->name); ?>
			<?php endif; ?>
		</h2>
	<?php endif; ?>
	<?php  echo $this->item->event->afterDisplayItemName;
	?>

	<?php echo $this->item->event->beforeDisplayItem; ?>
	<?php $images = $this->item->images; ?>
	 
	<?php if (($params->get('show_item_image', '0') == '1' AND isset($images['image_url']) AND $images['image_url'] != "")): ?>	
		<div class="pull-<?php echo htmlspecialchars($params->get('show_item_image_float','right')); ?>">
			<?php 
				$image = $images['image_url'];
					 
				list($img_width, $img_height) = getimagesize($image);
				
				$display_width = (int) $params->get('show_item_image_width','100');
				$display_height = (int) $params->get('show_item_image_height','0');
									
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
				<?php echo  $images['image_alt_text'] != '' ?'alt="'.$this->escape($images['image_alt_text']).'"':'alt="'.$this->escape($this->item->name).'"';?>
				 itemprop="image"
			/>
		</div>
	<?php endif; ?>	
	<?php if ($params->get('access-view')) : ?>	
		<?php 
			if (isset ($this->item->toc)) : 
				echo $this->item->toc; 
			endif;
		?>	
		<?php
			if ($use_def_list AND $info == 0) : 
				echo $this->loadTemplate('info');
			endif;
		?>	
		
		<?php
			if (isset($this->item->urls) AND $params->get('show_item_urls') == '1' AND $params->get('show_item_urls_position')=='0') :
				echo $this->loadTemplate('urls');
			endif;
		?>	
		
		<div itemprop="articleBody">
		<?php echo $this->item->introdescription; ?>
		</div>
		
		<?php
			$dummy = false;
			$use_fields_list = (
						($params->get('show_item_metadata')) OR 
						($params->get('show_item_parentid')) OR 
						($params->get('show_item_attribs')) OR 
						($params->get('show_item_mask')) OR 
						($params->get('show_item_sectionid')) OR 
						($params->get('show_item_fulltext')) OR 
						($params->get('show_item_introtext')) OR 
						($params->get('show_item_title_alias')) OR 
						($params->get('show_item_title')) OR 
						$dummy
						);
		?>
		<?php if ($use_fields_list) : ?>		
		<dl class="info">
			<dt class="info-title"><?php  echo JText::_('COM_SLIDER_ITEMS_INFO'); ?></dt>
		<?php endif; ?>		
		
			<?php if ($params->get('show_item_metadata')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_METADATA_LABEL'); ?></strong>
					<?php
						echo $this->item->metadata != '' ? $this->item->metadata : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_parentid')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_PARENTID_LABEL'); ?></strong>
					<?php
						echo $this->item->parentid != '' ? $this->item->parentid : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_attribs')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_ATTRIBS_LABEL'); ?></strong>
					<?php
						echo $this->item->attribs != '' ? $this->item->attribs : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_mask')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_MASK_LABEL'); ?></strong>
					<?php
						echo $this->item->mask != '' ? $this->item->mask : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_sectionid')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_SECTIONID_LABEL'); ?></strong>
					<?php
						echo $this->item->sectionid != '' ? $this->item->sectionid : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_fulltext')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_FULLTEXT_LABEL'); ?></strong>
					<?php
						echo $this->item->fulltext != '' ? $this->item->fulltext : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_introtext')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_INTROTEXT_LABEL'); ?></strong>
					<?php
						echo $this->item->introtext != '' ? $this->item->introtext : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_title_alias')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_TITLE_ALIAS_LABEL'); ?></strong>
					<?php
						echo $this->item->title_alias != '' ? $this->item->title_alias : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_item_title')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_SLIDER_ITEMS_FIELD_TITLE_LABEL'); ?></strong>
					<?php
						echo $this->item->title != '' ? $this->item->title : $empty;
					?>
				</dd>
			<?php endif; ?>
		<?php if ($use_fields_list) : ?>		
		</dl>	
		<?php endif; ?>
		<?php
			if ($use_def_list AND $info == 1) :
				echo $this->loadTemplate('info');
			endif;
		?>			
		<?php 
			if (isset($this->item->urls) AND $params->get('show_item_urls') == '1' AND $params->get('show_item_urls_position')=='1') :
				echo $this->loadTemplate('urls');
			endif;
		?>	
		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_item_noauth') AND $user->get('guest') ) : ?>
		<?php if (($params->get('show_item_intro_image', '0') == '1' AND isset($images['intro_image_url']) AND $images['intro_image_url'] != "")): ?>	
			<div class="pull-<?php echo htmlspecialchars($params->get('show_item_intro_image_float','right')); ?>">
				<?php 
						$image = $images['intro_image_url'];
						
						list($img_width, $img_height) = getimagesize($image);
						
						$display_width = (int) $params->get('show_item_intro_image_width','100');
						$display_height = (int) $params->get('show_item_intro_image_height','0');
											
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
					<?php if ($images['intro_image_caption']): ?>
						<?php echo 'class="caption"'.' title="' .htmlspecialchars($images['intro_image_caption']) . '"'; ?>
					<?php endif; ?>							
					<?php echo  $images['intro_image_alt_text'] != '' ?'alt="'.$this->escape($images['intro_image_alt_text']).'"':'alt="'.$this->escape($this->item->name).'"';?>
					 itemprop="image"
				/>
			</div>
		<?php endif; ?>	
		<?php
			if ($params->get('show_item_intro')) : 
				echo $this->item->intro; 
			endif;
		?>	
		
		<?php //Optional link to let them register to see the whole item. ?>
		<?php
			if ($params->get('show_item_readmore')) :
					$menu = JFactory::getApplication()->getMenu();
					$active = $menu->getActive();
					$item_id = $active->id;
					$link_1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $item_id);

					$return_url = $this->item->readmore_link;

					$link = new JUri($link_1);
					$link->setVar('return', base64_encode($return_url));?>
					<p class="readmore">
						<a href="<?php echo $link; ?>" itemprop="url">
							<?php
							if ($this->item->item_alternative_readmore == null) :
								if ($params->get('show_item_readmore_name') == 0) :					
									echo JText::_('COM_SLIDER_REGISTER_TO_READ_MORE');
								else :
									echo JText::_('COM_SLIDER_REGISTER_TO_READMORE_NAME');
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
	<?php echo $this->item->event->afterDisplayItem; ?>
</div>