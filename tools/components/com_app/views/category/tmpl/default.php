<?php
/**
 * @version 		$Id:$
 * @name			App (Release 1.0.0)
 * @author			 ()
 * @package			com_app
 * @subpackage		com_app.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default.php 418 2014-10-22 14:42:36Z BrianWade $
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

$page_class = $this->params->get('pageclass_sfx');
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_APP_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="app category-list <?php echo $page_class;?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<?php if ($this->params->get('show_cat_title', 1) OR $this->params->get('cat_page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('cat_page_subheading')); ?>
		<?php if ($this->params->get('show_cat_title')) : ?>
			<span class="subheading-category"><?php echo $this->category->title;?></span>
		<?php endif; ?>
	</h2>
	<?php endif; ?>
	<?php if ($this->params->get('show_cat_description', 1) OR $this->params->def('show_cat_description_image', 1)) : ?>
	<div class="category-desc">
		<?php if ($this->params->get('show_cat_description_image') AND $this->category->getParams()->get('image')) : ?>
			<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
		<?php endif; ?>
		<?php if ($this->params->get('show_cat_description') AND $this->category->description) : ?>
			<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_app.categories'); ?>
		<?php endif; ?>
		<div class="clr"></div>
	</div>
	<?php endif; ?>
	<?php if ($this->params->get('items_to_display') AND $this->params->get('items_to_display') !='None') : ?>
		<div class="cat-items">
			<?php 
			echo $this->loadTemplate('items'); 
			?>
		</div>
	<?php endif; ?>
	<?php if (!empty($this->children[$this->category->id])AND $this->max_level_category != 0) : ?>
	<div class="cat-children">
		<?php if ($this->params->get('show_cat_subcat_heading', 1) == 1) : ?>
			<h3>
				<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
			</h3>
		<?php endif; ?>
		<?php echo $this->loadTemplate('children'); ?>
	</div>
	<?php endif; ?>
</div>