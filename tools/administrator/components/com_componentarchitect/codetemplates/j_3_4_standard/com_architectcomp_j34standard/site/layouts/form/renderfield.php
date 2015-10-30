<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].admin
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @version			$Id: renderfield.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site.layout
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

/**
 * Layout variables
 * ---------------------
 * 	$options         : (array)  Optional parameters
 * 	$label           : (string) The html code for the label (not required if $options['hiddenLabel'] is true)
 * 	$input           : (string) The input field html code
 */

?>

<?php
if (!empty($displayData['options']['showonEnabled']))
{
	JHtml::_('jquery.framework');
	JHtml::_('script', 'jui/cms.js', false, true);
}
?>

<div class="control-group <?php echo $displayData['options']['class']; ?>" id="<?php echo $displayData['options']['group_id']; ?>" <?php echo $displayData['options']['rel']; ?>>
	<?php if (empty($displayData['options']['hiddenLabel'])) : ?>
		<div class="control-label"><?php echo $displayData['label']; ?></div>
	<?php endif; ?>
	<div class="controls">
		<?php if (!isset($displayData['options']['user'])) : ?>
			<?php echo $displayData['input']; ?>
			<?php if (isset($displayData['options']['file']) AND $displayData['options']['file'] != '') : ?>
				<div class="button2-left">
					<div class="blank">
						<a title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>" href="<?php echo JRoute::_(JUri::root().trim($displayData['options']['file']), false); ?>" target="_blank">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>
						</a>
					</div>
				</div>
			<?php endif; ?>	
		<?php else: ?>
			<?php echo $displayData['options']['user']; ?>
		<?php endif; ?>					
	</div>
</div>
