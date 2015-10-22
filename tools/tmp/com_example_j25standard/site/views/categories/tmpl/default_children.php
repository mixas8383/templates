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
if (count($this->children[$this->parent->id]) > 0 AND $this->max_level_categories != 0) :
?>
<ul>
<?php foreach($this->children[$this->parent->id] as $id => $child) : ?>
	<?php
	if ($this->params->get('show_empty_categories') OR $child->getNumItems(true) OR count($child->getChildren())) :
		if (!isset($this->children[$this->parent->id][$id + 1])) :
			$class = ' class="last"';
		endif;
		?>
		<li<?php echo $class; ?>>
		<?php $class = ''; ?>
			<span class="item-title"><a href="<?php echo JRoute::_(ExampleHelperRoute::getCategoryRoute($child->id, $this->params->get('keep_item_itemid'), $child->language));?>">
				<?php echo $this->escape($child->title); ?></a>
			</span>
			<?php if ($this->params->get('show_subcategories_desc') == 1) :?>
				<?php if ($child->description) : ?>
					<div class="category-desc">
						<?php echo JHtml::_('content.prepare', $child->description, '', 'com_example.categories'); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->params->get('items_to_display') AND 
						$this->params->get('items_to_display') !='None' AND 
						$this->params->get('show_categories_num_items',1)) : ?>
				<dl>
					<dt>
						<?php echo JText::_('COM_EXAMPLE_'.JString::strtoupper(str_replace(' ','',$this->params->get('items_to_display'))).'_NUM_ITEMS') ; ?>
					</dt>
					<dd>
						<?php echo $child->getNumItems(true); ?>
					</dd>
				</dl>
			<?php endif ; ?>        
			<?php if (count($child->getChildren()) > 0) :
				$this->children[$child->id] = $child->getChildren();
				$this->parent = $child;
				$this->max_level_categories--;
				echo $this->loadTemplate('children');
				$this->parent = $child->getParent();
				$this->max_level_categories++;
			endif; ?>

		</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>