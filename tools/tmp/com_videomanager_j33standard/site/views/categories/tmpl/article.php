<?php
/**
 * @version 		$Id:$
 * @name			Videomanager (Release 1.0.0)
 * @author			 ()
 * @package			com_videomanager
 * @subpackage		com_videomanager.admin
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
			
// Add css files for the videomanager component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_videomanager.css');
$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_categorieses.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_videomanager-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_categorieses-rtl.css');
}
				
// Add Javascript behaviors
JHtml::_('behavior.caption');

/*
 *	Initialise values for the layout 
 */	
 		
// Create shortcuts to some parameters.
$params		= &$this->item->params;
$user		= JFactory::getUser();
$info		= $this->item->params->get('categories_info_block_position', 0);
$can_edit	= $params->get('access-edit');
$can_delete	= $params->get('access-delete');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_videomanager' );
$empty = $component->params->get('default_empty_field', '');
$dummy = false;
$use_def_list = (
			($params->get('show_categories_category')) OR 
			($params->get('show_categories_parent_category') AND $this->item->parent_slug != '1:root') OR
				($params->get('show_categories_tags')) OR
			($params->get('show_categories_created_by')) OR 									
			($params->get('show_categories_created')) OR 
			($params->get('show_categories_modified')) OR
			($params->get('show_categories_hits')) OR
			$dummy
			);

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_VIDEOMANAGER_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="videomanager categories-article<?php echo $params->get('pageclass_sfx')?>" itemscope itemtype="http://schema.org/Article">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">	
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<?php if (!$use_def_list AND $this->print) : ?>
		<div id="pop-print" class="btn">
			<?php echo JHtml::_('categoriesicon.print_screen', $this->item, $params); ?>
		</div>
		<div class="clearfix"> </div>
	<?php endif; ?>	
	<?php if ($params->get('show_categories_icons',-1) >= 0) : ?>
		<?php
			if ($params->get('show_categories_print_icon') 
					OR $params->get('show_categories_email_icon') 
					OR $can_edit 
					OR $can_delete 
					):
		?>
			<?php if (!$this->print) : ?>
				<?php if ($params->get('access-view')) : ?>
					<div class="btn-group pull-right">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
						<ul class="dropdown-menu">			
							<?php if ($params->get('show_categories_print_icon')) : ?>
							<li class="print-icon">
									<?php echo JHtml::_('categoriesicon.print_popup',  $this->item, $params); ?>
							</li>
							<?php endif; ?>

							<?php if ($params->get('show_categories_email_icon')) : ?>
							<li class="email-icon">
									<?php echo JHtml::_('categoriesicon.email',  $this->item, $params); ?>
							</li>
							<?php endif; ?>
							<?php if ($can_edit) : ?>
								<li class="edit-icon">
									<?php echo JHtml::_('categoriesicon.edit', $this->item, $params); ?>
								</li>
							<?php endif; ?>
							<?php if ($can_delete) : ?>
								<li class="delete-icon">
									<?php echo JHtml::_('categoriesicon.delete',$this->item, $params); ?>
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

	<?php if ($params->get('show_categories_name')
				OR $params->get('access-edit') 
				): ?>
		<h2 itemprop="name">
			<?php if ($params->get('link_categories_names') AND !empty($this->item->readmore_link)) : ?>
				<a href="<?php echo $this->item->readmore_link; ?>" itemprop="url">
				<?php echo $this->escape($this->item->name); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->item->name); ?>
			<?php endif; ?>
		</h2>
	<?php endif; ?>
	<?php  echo $this->item->event->afterDisplayCategoriesName;
	?>

	<?php echo $this->item->event->beforeDisplayCategories; ?>
	<?php $images = $this->item->images; ?>
	 
	<?php if (($params->get('show_categories_image', '0') == '1' AND isset($images['image_url']) AND $images['image_url'] != "")): ?>	
		<div class="pull-<?php echo htmlspecialchars($params->get('show_categories_image_float','right')); ?>">
			<?php 
				$image = $images['image_url'];
					 
				list($img_width, $img_height) = getimagesize($image);
				
				$display_width = (int) $params->get('show_categories_image_width','100');
				$display_height = (int) $params->get('show_categories_image_height','0');
									
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
			if (isset($this->item->urls) AND $params->get('show_categories_urls') == '1' AND $params->get('show_categories_urls_position')=='0') :
				echo $this->loadTemplate('urls');
			endif;
		?>	
		
		<div itemprop="articleBody">
		<?php echo $this->item->introdescription; ?>
		</div>
		
		<?php
			$dummy = false;
			$use_fields_list = (
						($params->get('show_categories_modified_time')) OR 
						($params->get('show_categories_modified_user_id')) OR 
						($params->get('show_categories_created_time')) OR 
						($params->get('show_categories_created_user_id')) OR 
						($params->get('show_categories_metadata')) OR 
						($params->get('show_categories_published')) OR 
						($params->get('show_categories_note')) OR 
						($params->get('show_categories_title')) OR 
						($params->get('show_categories_extension')) OR 
						($params->get('show_categories_path')) OR 
						($params->get('show_categories_level')) OR 
						($params->get('show_categories_rgt')) OR 
						($params->get('show_categories_lft')) OR 
						($params->get('show_categories_parent_id')) OR 
						$dummy
						);
		?>
		<?php if ($use_fields_list) : ?>		
		<dl class="info">
			<dt class="info-title"><?php  echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_INFO'); ?></dt>
		<?php endif; ?>		
		
			<?php if ($params->get('show_categories_modified_time')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_MODIFIED_TIME_LABEL'); ?></strong>
					<?php
						echo $this->item->modified_time != '' ? JHtml::date($this->item->modified_time, 'Y-m-d H:M:S') : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_modified_user_id')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_MODIFIED_USER_ID_LABEL'); ?></strong>
					<?php
						echo $this->item->modified_user_id != '' ? $this->item->modified_user_id : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_created_time')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_CREATED_TIME_LABEL'); ?></strong>
					<?php
						echo $this->item->created_time != '' ? JHtml::date($this->item->created_time, 'Y-m-d H:M:S') : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_created_user_id')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_CREATED_USER_ID_LABEL'); ?></strong>
					<?php
						echo $this->item->created_user_id != '' ? $this->item->created_user_id : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_metadata')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_METADATA_LABEL'); ?></strong>
					<?php
						echo $this->item->metadata != '' ? $this->item->metadata : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_published')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_PUBLISHED_LABEL'); ?></strong>
					<?php
						switch ($this->item->published) :
									case '0':
										echo JText::_('JNO');
										break;
									case '1':
										echo JText::_('JYES');
										break;
									default:
										echo JText::_('JNONE');
										break;
								endswitch;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_note')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_NOTE_LABEL'); ?></strong>
					<?php
						echo $this->item->note != '' ? $this->item->note : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_title')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_TITLE_LABEL'); ?></strong>
					<?php
						echo $this->item->title != '' ? $this->item->title : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_extension')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_EXTENSION_LABEL'); ?></strong>
					<?php
						echo $this->item->extension != '' ? $this->item->extension : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_path')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_PATH_LABEL'); ?></strong>
					<?php
						echo $this->item->path != '' ? $this->item->path : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_level')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_LEVEL_LABEL'); ?></strong>
					<?php
						echo $this->item->level != '' ? $this->item->level : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_rgt')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_RGT_LABEL'); ?></strong>
					<?php
						echo $this->item->rgt != '' ? $this->item->rgt : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_lft')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_LFT_LABEL'); ?></strong>
					<?php
						echo $this->item->lft != '' ? $this->item->lft : $empty;
					?>
				</dd>
			<?php endif; ?>
			<?php if ($params->get('show_categories_parent_id')) : ?>
				<dd class="field">
					<strong><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELD_PARENT_ID_LABEL'); ?></strong>
					<?php
						echo $this->item->parent_id != '' ? $this->item->parent_id : $empty;
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
			if (isset($this->item->urls) AND $params->get('show_categories_urls') == '1' AND $params->get('show_categories_urls_position')=='1') :
				echo $this->loadTemplate('urls');
			endif;
		?>	
		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_categories_noauth') AND $user->get('guest') ) : ?>
		<?php if (($params->get('show_categories_intro_image', '0') == '1' AND isset($images['intro_image_url']) AND $images['intro_image_url'] != "")): ?>	
			<div class="pull-<?php echo htmlspecialchars($params->get('show_categories_intro_image_float','right')); ?>">
				<?php 
						$image = $images['intro_image_url'];
						
						list($img_width, $img_height) = getimagesize($image);
						
						$display_width = (int) $params->get('show_categories_intro_image_width','100');
						$display_height = (int) $params->get('show_categories_intro_image_height','0');
											
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
			if ($params->get('show_categories_intro')) : 
				echo $this->item->intro; 
			endif;
		?>	
		
		<?php //Optional link to let them register to see the whole categories. ?>
		<?php
			if ($params->get('show_categories_readmore')) :
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
							if ($this->item->categories_alternative_readmore == null) :
								if ($params->get('show_categories_readmore_name') == 0) :					
									echo JText::_('COM_VIDEOMANAGER_REGISTER_TO_READ_MORE');
								else :
									echo JText::_('COM_VIDEOMANAGER_REGISTER_TO_READMORE_NAME');
									echo JHtml::_('string.truncate', ($this->item->name), $params->get('categories_readmore_limit'));
								endif;
							else :
								echo $this->item->categories_alternative_readmore;
								if ($params->get('show_categories_readmore_name') == 1) :
									echo JHtml::_('string.truncate', ': '.($this->item->name), $params->get('categories_readmore_limit'));
								endif;
							endif;
							?>
						</a>
					</p>
		<?php endif; ?>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayCategories; ?>
</div>