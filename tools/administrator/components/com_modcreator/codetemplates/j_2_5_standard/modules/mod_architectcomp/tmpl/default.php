<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].mod_[%%architectcomp%%]
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: default.php 418 2014-10-22 14:42:36Z BrianWade $
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
defined ( '_JEXEC' ) or die;

if ( !empty ( $list ) )
{
    ?>
    <div class="[%%architectcomp%%] mod_[%%architectcomp%%]<?php echo $module_class_sfx; ?>">
        <?php
        foreach ( $list as $item )
        {
            $slug = $item->id . ':' . $item->alias;
            $catslug = $item->catid . ':' . $item->catslug;
            $link = JRoute::_ ( ContentHelperRoute::getArticleRoute ( $slug, $catslug, $item->language ), true );
            ?>
            <div class="[%%architectcomp%%] mod_[%%architectcomp%%]_inn<?php echo $module_class_sfx; ?>">
                <a href="<?php echo $link; ?>">
                    <?php echo $item->title; ?>
                </a>
            </div>
            <?php
        }
        ?>
        <div class="clr"></div>
    </div>
    <?php
}
else
{
    ?>
    <div class="not_found"><?php echo JText::_ ( 'NOT FOUND' ) ?></div>
    <?php
}
