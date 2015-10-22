<?php
/**
 * @version			$Id: default.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

//[%%START_CUSTOM_CODE%%]

defined('_JEXEC') or die('Restricted access');
if(empty($this->tag)) $this->tag = null;
?>
<form name="adminForm" action="index.php" method="post">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif;?>
	<?php if(count($this->logs)): ?>
		<input name="option" value="com_componentarchitect" type="hidden" />
		<input name="view" value="logs" type="hidden" />
		<?php echo JHtml::_( 'form.token' ); ?>
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			<div class="form-horizontal">
		<?php else: ?>
			<fieldset class="adminform">
		<?php endif; ?>	
				<div class="control-group" id="field_logs">
					<div class="control-label">
						<label for="tag">
							<?php echo JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_CHOOSE_FILE_LABEL'); ?>
						</label>
					</div>
					<div class="controls">
						<?php echo JHtml::_('select.genericlist', $this->getLogList($this->logs), 'tag', array('onchange' => 'submitform();', 'style' => 'width: auto;'), 'value', 'text', $this->tag, 'tag') ?>
						
						<?php if(!empty($this->tag)): ?>
							<button class="btn btn-small btn-primary" onclick="window.location='<?php echo JUri::base(); ?>index.php?option=com_componentarchitect&view=logs&task=logs.download&tag=<?php echo urlencode($this->tag); ?>'; return false;"><?php echo JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_DOWNLOAD_LABEL'); ?></button>
						<?php endif; ?>
					</div>
					<br/>
				</div>
				<?php if(!empty($this->tag)): ?>
					<div class="control-group" id="field_log_contents" <?php echo version_compare(JVERSION, '3.0', 'lt') ? 'style="border: 1px #ccc solid; margin-top: 15px;"' : ''; ?>>
						<iframe
							src="<?php echo JUri::base(); ?>index.php?option=com_componentarchitect&view=logs&task=logs.iframe&layout=raw&tag=<?php echo urlencode($this->tag); ?>"
							width="100%" height="400px">
						</iframe>
					</div>
				<?php endif; ?>
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
		</div>
		<?php else: ?>
		</fieldset>
		<?php endif; ?>			
	<?php else: ?>
		<div>
			<h2><?php echo JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_NONE_FOUND') ?></h2>
		</div>
	<?php endif; ?>
</form>
<?php
//[%%END_CUSTOM_CODE%%]
?>