<?php
/**
 * @version 		$Id:$
 * @name			Mmanager (Release 1.0.0)
 * @author			 ()
 * @package			com_mmanager
 * @subpackage		com_mmanager.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default.php 408 2014-10-19 18:31:00Z BrianWade $
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
			
// Add css files for the mmanager component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/com_mmanager/css/site_mmanager.css');
$this->document->addStyleSheet(JUri::root().'media/com_mmanager/css/site_editions.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/com_mmanager/css/site_mmanager-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/com_mmanager/css/site_editions-rtl.css');
}
				
// Add Javascript behaviors
JHtml::_('behavior.caption');

/*
 *	Initialise values for the layout 
 */	
 
// Create shortcuts to some parameters.
$params		= &$this->item->params;
$user		= JFactory::getUser();

$can_edit	= $params->get('access-edit');
$can_delete	= $params->get('access-delete');
// Get from global settings the text to use for an empty field
$component = JComponentHelper::getComponent( 'com_mmanager' );
$empty = $component->params->get('default_empty_field', '');

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_MMANAGER_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="mmanager edition-view<?php echo $params->get('pageclass_sfx')?>" itemscope itemtype="http://schema.org/CreativeWork">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<?php if ($params->get('show_edition_icons',-1) >= 0) : ?>
		<?php if ($params->get('show_edition_print_icon') 
			OR $params->get('show_edition_email_icon') 
			OR $can_edit 
			OR $can_delete 
			): ?>
			<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <span class="icon-cog"></span> <span class="caret"></span> </a>
				<ul class="dropdown-menu">
					<?php if (!$this->print) : ?>
						<?php if ($params->get('access-view')) : ?>
							<?php if ($params->get('show_edition_print_icon')) : ?>
								<li class="print-icon">
										<?php echo JHtml::_('editionicon.print_popup',  $this->item, $params); ?>
								</li>
							<?php endif; ?>

							<?php if ($params->get('show_edition_email_icon')) : ?>
								<li class="email-icon">
										<?php echo JHtml::_('editionicon.email',  $this->item, $params); ?>
								</li>
							<?php endif; ?>
							<?php if ($can_edit) : ?>
								<li class="edit-icon">
									<?php echo JHtml::_('editionicon.edit', $this->item, $params); ?>
								</li>
							<?php endif; ?>
							<?php if ($can_delete) : ?>
								<li class="delete-icon">
									<?php echo JHtml::_('editionicon.delete',$this->item, $params); ?>
								</li>					
							<?php endif; ?>
						<?php endif; ?>
					<?php else : ?>
						<li>
							<?php echo JHtml::_('editionicon.print_screen',  $this->item, $params); ?>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($params->get('show_edition_name')) : ?>
		<div style="float: left;">
			<h2 itemprop="name">
				<?php if ($params->get('link_edition_names') AND !empty($this->item->readmore_link)) : ?>
					<a href="<?php echo $this->item->readmore_link; ?>" itemprop="url">
					<?php echo $this->escape($this->item->name); ?></a>
				<?php else : ?>
					<?php echo $this->escape($this->item->name); ?>
				<?php endif; ?>
			</h2>
		</div>
	<?php endif; ?>
	<?php  echo $this->item->event->afterDisplayEditionName;	?>
	
	<?php echo $this->item->event->beforeDisplayEdition; ?>
	<?php $images = $this->item->images; ?>
	
	<?php if ($params->get('show_edition_hits') != '0' OR 
		($params->get('show_edition_image', '0') == '1' AND isset($images['image_url']) AND $images['image_url'] != '')): ?>	
			<div class="pull-<?php echo htmlspecialchars($params->get('show_edition_image_float','right')); ?>">
			<?php if ($params->get('show_edition_image') == '1' AND isset($images['image_url']) AND $images['image_url'] != '') : ?>
			
					<?php 
						$image = $images['image_url'];
						
						list($img_width, $img_height) = getimagesize($image);
						
						$display_width = (int) $params->get('show_edition_intro_image_width','100');
						$display_height = (int) $params->get('show_edition_intro_image_height','0');
						
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
			<?php endif; ?>			 
			<?php if ($params->get('show_edition_hits')) : ?>
				<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>" />
				<?php echo '<br />'.JText::sprintf('COM_MMANAGER_HITS', $this->item->hits); ?>
			<?php endif; ?>	
		</div>
	<?php endif; ?>	
	<div style="clear:both; padding-top: 10px;">

		<?php if ($params->get('access-view')) :?>
		<?php
			if (isset($this->item->urls) AND $params->get('show_edition_urls') == '1' AND $params->get('show_edition_urls_position')=='0') :
				echo $this->loadTemplate('urls');
			endif;
		?>	
				<div itemprop="description">
				<?php echo $this->item->introdescription; ?>
				</div>
			<?php //optional teaser intro text for guests ?>
		<?php elseif ($params->get('show_edition_noauth') == true AND  $user->get('guest') ) : ?>
			<?php if ($params->get('show_edition_intro')) : ?>
				<span itemprop="description">
				<?php echo $this->item->intro; ?>
				</span>
			<?php endif; ?>	
			<?php //Optional link to let them register to see the whole edition. ?>
			<?php if ($params->get('show_edition_readmore')) :
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
						if ($this->item->edition_alternative_readmore == null) :
							if ($params->get('show_edition_readmore_name') == 0) :					
								echo JText::_('COM_MMANAGER_REGISTER_TO_READ_MORE');
							else :
								echo JText::_('COM_MMANAGER_REGISTER_TO_READMORE_NAME');
								echo JHtml::_('string.truncate', ($this->item->name), $params->get('edition_readmore_limit'));
							endif;
						else :
							echo $this->item->edition_alternative_readmore;
							if ($params->get('show_edition_readmore_name') == 1) :
								echo JHtml::_('string.truncate', ': '.($this->item->name), $params->get('edition_readmore_limit'));
							endif;
						endif;
						?>
					</a>
				</p>				
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div style="padding-top: 10px;">
		<?php if ($params->get('access-view')) : ?>	

			<form action="" name="editionForm" id="editionForm">
			<?php $dummy = false;
					$display_fieldset = (
								($params->get('show_edition_modified_time')) OR 
								($params->get('show_edition_modified_user_id')) OR 
								($params->get('show_edition_created_time')) OR 
								($params->get('show_edition_created_user_id')) OR 
								($params->get('show_edition_metadata')) OR 
								($params->get('show_edition_published')) OR 
								($params->get('show_edition_note')) OR 
								($params->get('show_edition_title')) OR 
								($params->get('show_edition_extension')) OR 
								($params->get('show_edition_path')) OR 
								($params->get('show_edition_level')) OR 
								($params->get('show_edition_rgt')) OR 
								($params->get('show_edition_lft')) OR 
								($params->get('show_edition_parent_id')) OR 
								$dummy
								);
			?>
			<?php if ($display_fieldset) : ?>				
				<fieldset>	
					<legend><?php echo JText::_('COM_MMANAGER_EDITIONS_FIELDSET_BASIC_DETAILS_LABEL'); ?></legend>
			<?php endif; ?>
					<div style="padding-top: 10px;">			
						<?php if ($params->get('show_edition_modified_time')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_MODIFIED_TIME_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->modified_time != '' ? JHtml::date($this->item->modified_time, 'Y-m-d H:M:S') : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_modified_user_id')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_MODIFIED_USER_ID_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->modified_user_id != '' ? $this->item->modified_user_id : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_created_time')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_CREATED_TIME_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->created_time != '' ? JHtml::date($this->item->created_time, 'Y-m-d H:M:S') : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_created_user_id')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_CREATED_USER_ID_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->created_user_id != '' ? $this->item->created_user_id : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_metadata')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_METADATA_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->metadata != '' ? $this->item->metadata : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_published')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_PUBLISHED_LABEL'); ?>
							</label>
							<span>
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
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_note')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_NOTE_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->note != '' ? $this->item->note : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_title')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_TITLE_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->title != '' ? $this->item->title : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_extension')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_EXTENSION_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->extension != '' ? $this->item->extension : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_path')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_PATH_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->path != '' ? $this->item->path : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_level')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_LEVEL_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->level != '' ? $this->item->level : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_rgt')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_RGT_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->rgt != '' ? $this->item->rgt : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_lft')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_LFT_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->lft != '' ? $this->item->lft : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
						<?php if ($params->get('show_edition_parent_id')) : ?>
						<div class="formelm">
							<label>
								<?php echo JText::_('COM_MMANAGER_EDITIONS_FIELD_PARENT_ID_LABEL'); ?>
							</label>
							<span>
								<?php
									echo $this->item->parent_id != '' ? $this->item->parent_id : $empty;
								?>
							</span>
						</div>	
						<?php endif; ?>
					</div>
			<?php if ($display_fieldset) : ?>				
				</fieldset>	
			<?php endif;?>	
			<?php
				$dummy = false;
		$display_fieldset = (
							($params->get('show_edition_category')) OR 
							($params->get('show_edition_parent_category') AND $this->item->parent_slug != '1:root') OR
							($params->get('show_edition_tags')) OR
							($params->get('show_edition_created_by')) OR
							($params->get('show_edition_created')) OR
							($params->get('show_edition_modified')) OR
							($params->get('show_edition_publish_up')) OR
							($params->get('show_edition_publish_down')) OR
							($params->get('show_edition_admin') AND $this->item->params->get('access-change')) OR							
							$dummy
							);
			?>
			<?php if ($display_fieldset) : ?>				
					<fieldset>
						<legend><?php echo JText::_('COM_MMANAGER_FIELDSET_PUBLISHING_LABEL'); ?></legend>
			<?php endif; ?>
	
			<?php if ($params->get('show_edition_parent_category') AND $this->item->parent_slug != '1:root') : ?>
				<?php $title = '<span itemprop="genre">'.$this->escape($this->item->parent_title).'</span>'; ?>				
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_PARENT_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_edition_parent_category') AND $this->item->parent_slug) : ?>
							<?php $url = '<a href="'.JRoute::_(MmanagerHelperRoute::getCategoryRoute($this->item->parent_slug, $params->get('keep_edition_itemid'))).'" itemprop="url">'.$title.'</a>'; ?>
							<?php echo $url; ?>
						<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>
					</span>
				</div>
			<?php endif;?>	
			<?php if ($params->get('show_edition_category')) : ?>
				<?php $title = '<span itemprop="genre">'.$this->escape($this->item->category_title).'</span>'; ?>
				<div class="formelm">				
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_CATEGORY_LABEL'); ?>
					</label>
					<span>
						<?php if ($params->get('link_edition_category') AND $this->item->catslug) : ?>
							<?php $url = '<a href="'.JRoute::_(MmanagerHelperRoute::getCategoryRoute($this->item->catslug, $params->get('keep_edition_itemid'))).'" itemprop="url">'.$title.'</a>'; ?>
							<?php echo $url; ?>
						<?php else : ?>
							<?php echo $title; ?>
						<?php endif; ?>	
					</span>
				</div>								
			<?php endif; ?>						
			<?php if ($params->get('show_edition_tags')  == '1' AND !empty($this->item->tags) AND !empty($this->item->tags->itemTags)) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('JTAG'); ?>
					</label>			
					<?php echo $this->item->tag_layout->render($this->item->tags->itemTags); ?>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_edition_created_by') ) : ?>
				<?php $created_by =  $this->item->created_by ?>
				<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>
				<?php $created_by = '<span itemprop="name">' . $created_by . '</span>'; ?>
				<div class="formelm">				
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_CREATED_BY_LABEL'); ?> 
					</label>
					<span itemprop="creator" itemscope itemtype="http://schema.org/Person">
						<?php if (!empty($this->item->created_by ) AND  $this->params->get('link_edition_created_by') == 1):?>
							<?php echo JHtml::_(
									'link',
									JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
									$created_by, array('itemprop' => 'url'));
							 ?>

						<?php else :?>
							<?php echo $created_by; ?>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>	
			<?php if ($params->get('show_edition_created_by_alias')) : ?>
				<div class="formelm" itemprop="creator" itemscope itemtype="http://schema.org/Person">				
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_CREATED_BY_ALIAS_LABEL'); ?>
					</label>
					<span itemprop="alternateName">						
					<?php echo !empty($this->item->created_by_alias) ? $this->item->created_by_alias : $empty; ?>
					</span>
				</div>
			<?php endif; ?>				
			<?php if ($params->get('show_edition_created')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_CREATED_LABEL'); ?>
					</label>
					<time datetime="<?php echo JHtml::_('date', $this->item->created, 'c'); ?>" itemprop="dateCreated">
						<?php echo JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
					</time>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_edition_modified')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_MODIFIED_LABEL'); ?>				
					</label>
					<time datetime="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
						<?php echo JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2')); ?>
					</time>
				</div>
			<?php endif; ?>	
			<?php if ($params->get('show_edition_publish_up')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_PUBLISH_UP_LABEL'); ?>				
					</label>
					<time datetime="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>" itemprop="datePublished">
						<?php echo $this->item->publish_up > 0 ? JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2')) : JText::_('JNONE'); ?>
					</time>
				</div>
			<?php endif; ?>
			<?php if ($params->get('show_edition_publish_down')) : ?>
				<div class="formelm">
					<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_PUBLISH_DOWN_LABEL'); ?>				
					</label>
					<time datetime="<?php echo JHtml::_('date', $this->item->publish_down, 'c'); ?>">
						<?php echo $this->item->publish_down > 0 ? JHtml::_('date',$this->item->publish_down, JText::_('DATE_FORMAT_LC2')) : JText::_('JNONE'); ?>
					</time>
				</div>
			<?php endif; ?>
			<?php if ($params->get('access-change')): ?>
				<?php if ($params->get('show_edition_admin')) : ?>
				
					<div class="formelm">
						<label>
						<?php echo JText::_('COM_MMANAGER_FIELD_STATUS_LABEL'); ?>
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
					<div class="formelm">
						<label>
							<?php echo JText::_('COM_MMANAGER_FIELD_FEATURED_LABEL'); ?>
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
					<div class="formelm">
						<label>
							<?php echo JText::_('COM_MMANAGER_FIELD_ACCESS_LABEL'); ?>
						</label>
						<span>
							<?php echo $this->item->access_title; ?>
						</span>
					</div>
					<div class="formelm">
						<label>
							<?php echo JText::_('JFIELD_ORDERING_LABEL'); ?>
						</label>
						<span>
							<?php echo $this->item->ordering; ?>
						</span>
					</div>	
				<?php endif; ?>
				
			<?php endif; ?>
			
			<?php
				if (isset($this->item->urls) AND $params->get('show_edition_urls') == '1' AND $params->get('show_edition_urls_position')=='1') :
					echo $this->loadTemplate('urls');
				endif;
			?>	
			<?php if ($display_fieldset) : ?>				
					</fieldset>	
			<?php endif;?>	
			</form>
		<?php endif; ?>	
		<?php echo $this->item->event->afterDisplayEdition; ?>
	</div>		
</div>