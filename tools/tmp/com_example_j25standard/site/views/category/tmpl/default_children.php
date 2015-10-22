<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: default_children.php 418 2014-10-22 14:42:36Z BrianWade $
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
$class = ' class="first"';
?>

<?php if (count($this->children[$this->category->id]) > 0) : ?>
	<ul>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php
			if ($this->params->get('show_empty_cat') OR $child->getNumItems(true) OR count($child->getChildren())) :
			if (!isset($this->children[$this->category->id][$id + 1])) :
				$class = ' class="last"';
			endif;
		?>

		<li<?php echo $class; ?>>
			<?php $class = ''; ?>

			<span class="item-title"><a href="<?php echo JRoute::_(ExampleHelperRoute::getCategoryRoute($child->id, $this->params->get('keep_item_itemid')));?>">
				<?php echo $this->escape($child->title); ?></a>
			</span>
			<?php if ($this->params->get('show_subcat_desc') == 1) :?>
				<?php if ($child->description) : ?>
				<div class="category-desc">
						<?php echo JHtml::_('content.prepare', $child->description, '', 'com_example.categories'); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->params->get('items_to_display') AND 
						$this->params->get('items_to_display') !='None' AND 
						$this->params->get('show_cat_num_items',1)) : ?>
			
				<dl>
					<dt>
						<?php echo JText::_('COM_EXAMPLE_'.JString::strtoupper(str_replace(' ','',$this->params->get('items_to_display'))).'_NUM_ITEMS') ; ?>
					</dt>
					<dd>
						<?php echo $child->getNumItems(true); ?>
					</dd>
				</dl>
			<?php endif ; ?>			
			<?php if (count($child->getChildren()) > 0 ) :
				$this->children[$child->id] = $child->getChildren();
				$this->category = $child;
				$this->max_level_category--;
				if ($this->max_level_category != 0) :
					echo $this->loadTemplate('children');
				endif;
				$this->category = $child->getParent();
				$this->max_level_category++;
			endif; ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
