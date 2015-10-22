<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.mod_example
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: default.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.mod_architectcomp
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
?>
<div class="example mod_items<?php echo $module_class_sfx; ?>">
	<?php if($params->get('style','') <> '') : ?>
		<?php 
			$list_style_type = $params->get('style') == 'ul'? 'list-style-type: disc;' : '';
			echo '<'.$params->get('style').' style="list-style-position: inside;'. $list_style_type.'">'; 
		?>
	<?php endif; ?>
	<?php foreach ($list as $item) :  ?>
		<?php if($params->get('style','') <> '') : ?>
			<li>
		<?php endif; ?>
			<a href="<?php echo $item->link; ?>">
				<?php echo $item->name; ?>
			</a>
		<?php if($params->get('style','') <> '') : ?>
			</li>
		<?php else :?>
			<br />
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if($params->get('style','') <> '') : ?>
		<?php echo '</'.$params->get('style').'>'; ?>
	<?php endif; ?>
</ul>
</div>
