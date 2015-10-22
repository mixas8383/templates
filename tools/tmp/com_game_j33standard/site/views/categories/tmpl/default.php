<?php
/**
 * @version 		$Id:$
 * @name			Game (Release 1.0.0)
 * @author			 ()
 * @package			com_game
 * @subpackage		com_game.site
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
				
// Add css files for the game component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/com_game/css/site_game.css');
$this->document->addStyleSheet(JUri::root().'media/com_game/css/site_categories.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/com_game/css/site_game-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/com_game/css/site_categories-rtl.css');
}

// Add Javascript behaviors
JHtml::_('behavior.caption');
JHtml::_('bootstrap.tooltip');	

/*
 *	Layout HTML
 */			
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_GAME_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="game">
	<?php
	echo JLayoutHelper::render('joomla.content.categories_default', $this);
	echo $this->loadTemplate('children');
	?>
</div>