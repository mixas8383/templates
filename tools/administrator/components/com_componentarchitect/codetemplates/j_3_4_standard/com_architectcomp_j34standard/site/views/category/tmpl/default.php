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
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_'.JString::strtolower(str_replace(' ','',$this->params->get('items_to_display'))).'.css');
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_categories.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%architectcomp%%]-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_'.JString::strtolower(str_replace(' ','',$this->params->get('items_to_display'))).'-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_categories-rtl.css');
}

// Add Javascript behaviors
[%%IF INCLUDE_IMAGE%%]
JHtml::_('behavior.caption');
[%%ENDIF INCLUDE_IMAGE%%]				
JHtml::_('behavior.core');		
JHtml::_('bootstrap.tooltip');

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
[%%IF INCLUDE_MICRODATA%%]
<div class="[%%architectcomp%%] category-list <?php echo $this->pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
[%%ELSE INCLUDE_MICRODATA%%]
<div class="[%%architectcomp%%] category-list <?php echo $this->pageclass_sfx;?>">
[%%ENDIF INCLUDE_MICRODATA%%]

	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">	
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_cat_title', 1) OR $this->params->get('cat_page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('cat_page_subheading')); ?>
		<?php if ($this->params->get('show_cat_title')) : ?>
			<span class="subheading-category"><?php echo $this->category->title;?></span>
		<?php endif; ?>
	</h2>
	<?php endif; ?>

	[%%IF INCLUDE_TAGS%%]
	<?php if ($this->params->get('show_cat_tags', 1) AND !empty($this->category->tags->itemTags)) : ?>
		<?php $this->category->tag_layout = new JLayoutFile('joomla.content.tags'); ?>
		<?php echo $this->category->tag_layout->render($this->category->tags->itemTags); ?>
	<?php endif; ?>
	[%%ENDIF INCLUDE_TAGS%%]
	
	<?php if ($this->params->get('show_cat_description', 1) OR $this->params->def('show_cat_description_image', 1)) : ?>
	<div class="category-desc clearfix">
		<?php if ($this->params->get('show_cat_description_image') AND $this->category->getParams()->get('image')) : ?>
			<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
		<?php endif; ?>
		<?php if ($this->params->get('show_cat_description') AND $this->category->description) : ?>
			<?php echo JHtml::_('content.prepare', $this->category->description, '', '[%%com_architectcomp%%].categories'); ?>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<?php if ($this->params->get('items_to_display') AND $this->params->get('items_to_display') !='') : ?>

		<div class="cat-items clearfix">
			<?php 
			echo $this->loadTemplate('items'); 
			?>
		</div>
	<?php endif; ?>
	<?php if (!empty($this->children[$this->category->id]) AND $this->category_max_level != 0) : ?>
		<div class="cat-children clearfix">
			<?php if ($this->params->get('show_cat_subcat_heading', 1) == 1) : ?>
				<h3>
					<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
				</h3>
			<?php endif; ?>

			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>