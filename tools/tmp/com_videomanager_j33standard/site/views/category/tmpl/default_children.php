<?php
/**
 * @version 		$Id:$
 * @name			Videomanager (Release 1.0.0)
 * @author			 ()
 * @package			com_videomanager
 * @subpackage		com_videomanager.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default_children.php 408 2014-10-19 18:31:00Z BrianWade $
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
 
 $class = ' class="first"';

/*
 *	Layout HTML
 */
?>

<?php if (count($this->children[$this->category->id]) > 0) : ?>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php if (($this->params->get('show_empty_cat') OR $child->getNumItems(true) OR count($child->getChildren())) AND $this->category_max_level > 1) : ?>
			<?php 
				if (!isset($this->children[$this->category->id][$id + 1])) :
					$class = ' class="last"';
				endif;
			?>

			<div<?php echo $class; ?>>
				<?php $class = ''; ?>

				<h3 class="page-header item-title"><a href="<?php echo JRoute::_(VideomanagerHelperRoute::getCategoryRoute($child->id, $this->params->get('keep_video_itemid')));?>">
					<?php echo $this->escape($child->title); ?></a>
					<?php if ($this->params->get('items_to_display') AND 
								$this->params->get('items_to_display') !='' AND 
								$this->params->get('show_cat_num_items',1)) : ?>
							<span class="badge badge-info">
								<?php echo JText::_('COM_VIDEOMANAGER_'.JString::strtoupper(str_replace(' ','',$this->params->get('items_to_display'))).'_NUM_ITEMS') ; ?>
								<?php echo $child->getNumItems(true); ?>
							</span>

					<?php endif ; ?>
					<?php if (count($child->getChildren()) > 0) : ?>
						<a href="#category-<?php echo $child->id;?>" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right"><span class="icon-plus"></span></a>
					<?php endif;?>									
				</h3>
				<?php if ($this->params->get('show_subcat_desc') == 1) :?>
					<?php if ($child->description) : ?>
					<div class="category-desc">
							<?php echo JHtml::_('content.prepare', $child->description, '', 'com_videomanager.categories'); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
		
				<?php if (count($child->getChildren()) > 0 AND $this->category_max_level > 1) : ?>
					<div class="collapse fade" id="category-<?php echo $child->id; ?>">
						<?php
							$this->children[$child->id] = $child->getChildren();
							$this->category = $child;
							$this->category_max_level--;
							if ($this->category_max_level != 0) :
								echo $this->loadTemplate('children');
							endif;
							$this->category = $child->getParent();
							$this->category_max_level++;
						?>
					</div>			
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
