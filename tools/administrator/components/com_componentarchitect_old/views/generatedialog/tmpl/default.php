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
// no direct access
defined('_JEXEC') or die;

// Get default values from Options
$params = $this->state->get('params');	

$output_path_default = $params->get('default_output_path', 'tmp');
$zip_format_default = $params->get('default_zip_format', '');
$logging_default = $params->get('default_logging', '0');

?>
<!-- Javascript and jQuery detection. Shows a warning if either is missing -->

<noscript>
<p style="color: red;"><?php echo JText::_('COM_COMPONENTARCHITECT_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div id="nojquerywarning">
	<p style="color: red;"><?php echo JText::_('COM_COMPONENTARCHITECT_WARNING_NOJQUERY'); ?><p>
</div>

<script type="text/javascript">
	if(jQuery)
	{
		jQuery('#nojquerywarning').css('display','none');
	}
</script>
<script type="text/javascript">
// jQuery document load functions for generate progress
jQuery(document).ready(function()
{
	// The return URL

	// Push some translations
	generate_translations['GENERATE-SUCCESS'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_SUCCESS', true) ?>';
	generate_translations['GENERATE-SUCCESS-PATH'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_SUCCESS_PATH', true) ?>';
	generate_translations['GENERATE-SUCCESS-ZIP'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_SUCCESS_ZIP', true) ?>';
	generate_translations['GENERATE-DOWNLOAD-ZIP'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DOWNLOAD_BUTTON', true) ?>';
	generate_translations['GENERATE-FAILED'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_FAILED', true) ?>';
	generate_translations['GENERATE-ERROR-GENERAL'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_ERROR_GENERAL', true) ?>';
	generate_translations['GENERATE-ERROR-AJAX-LOADING'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_ERROR_AJAX_LOADING', true) ?>';
	generate_translations['GENERATE-ERROR-AJAX-INVALID-DATA'] = '<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_ERROR_AJAX_INVALID_DATA', true) ?>';

}); 

Joomla.submitbutton = function(task)
{
	if (document.formvalidator.isValid(document.id('generate-form'))) {
		Joomla.submitform(task, document.getElementById('generate-form'));
	}
}

</script>
<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
	<div class="form-horizontal">
<?php else: ?>
	<fieldset class="adminform">
<?php endif; ?>
		<div class="fltlft left" id="generate-instructions">
			<h3>
				<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_INSTRUCTIONS_LABEL'); ?>
			</h3>
			<div>
				<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_INSTRUCTIONS_DESC'); ?>
			</div>		
		</div>
		<div class="fltlft left" id="generate-advice" style="display: none;">
			<h3>
				<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_DIALOG_PROGRESS_LABEL'); ?>
			</h3>
			<div id="generate-advice-stay">
				<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_ADVICE_STAY_ON_PAGE'); ?>
			</div>
			<?php if (!function_exists('ini_set')) : ?>	
				<div id="generate-advice-php">
					<?php echo JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_ADVICE_PHP_MAX_EXECUTION'); ?>
				</div>
			<?php endif; ?>
		</div>			
<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
	</div>
<?php else: ?>
	</fieldset>
<?php endif; ?>
<form action="<?php echo JRoute::_('index.php?option=com_componentarchitect');?>" id="generate-form" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div id="generate-setup">
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			<div class="form-horizontal">
		<?php else: ?>
			<fieldset class="adminform">
		<?php endif; ?>
			<div class="left">		
				<div class="control-group" id="field_component_id">
					<div class="control-label"><?php echo $this->form->getLabel('component_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('component_id'); ?></div>
				</div>
				<div class="control-group" id="field_code_template_id">
					<div class="control-label"><?php echo $this->form->getLabel('code_template_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('code_template_id'); ?></div>
				</div>				
				<div class="control-group" id="field_output_path">
					<div class="control-label"><?php echo $this->form->getLabel('output_path'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('output_path', null, $output_path_default); ?></div>
				</div>				
				<div class="control-group" id="field_zip_format">
					<div class="control-label"><?php echo $this->form->getLabel('zip_format'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('zip_format', null, $zip_format_default); ?></div>
				</div>					
				<div class="control-group" id="field_logging">
					<div class="control-label"><?php echo $this->form->getLabel('logging'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('logging', null, $logging_default); ?></div>
				</div>					
			</div>
			<div class="left" style="clear: both; padding-top: 15px; text-align: center;">
				<button id="generate-start" type="button" class="btn btn-large btn-primary hasTip" title="<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_BUTTON_DESC');?>" onclick="generate_start(); return false;">
					<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_BUTTON');?>
				</button>
				<img src="<?php echo '../media/com_componentarchitect/images/help.png'; ?>" alt="<?php echo JText::_('COM_COMPONENTARCHITECT_HELP'); ?>" onclick="Joomla.popupWindow('components/com_componentarchitect/help/en-GB/componentarchitect_generate.html', '<?php echo JText::_('COM_COMPONENTARCHITECT_HELP'); ?>', 700, 500, 1)">
			</div>
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			</div>
		<?php else: ?>
			</fieldset>
		<?php endif; ?>
		<input type="hidden" name="task" value="componentarchitect.generate" />
		<div id="token" style="display: none;">
		<?php echo JHtml::_('form.token'); ?>
		</div>

	</div>
	<div id="generate-progress" style="display: none;">
		<div id="generate-progress-stage-1">
			<div class="progress-stage-title"><?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_STAGE_1_LABEL'); ?></div>
			<div id="generate-step-stage-1" class="progress-stage-text">
			</div>		
			<div id="generate-percentage-stage-1" class="progress-container">
				<div class="progress-bar">
					<div class="progress-text"></div>
				</div>				
			</div>
		</div>
		<div id="generate-progress-stage-2">
			<div class="progress-stage-title"><?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_STAGE_2_LABEL'); ?></div>
			<div id="generate-step-stage-2" class="progress-stage-text">
			</div>		
			<div id="generate-percentage-stage-2" class="progress-container">
				<div class="progress-bar">
					<div class="progress-text"></div>
				</div>				
			</div>
		</div>
		<div id="generate-progress-stage-3">
			<div class="progress-stage-title"><?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_STAGE_3_LABEL'); ?></div>
			<div id="generate-step-stage-3" class="progress-stage-text">
			</div>		
			<div id="generate-percentage-stage-3" class="progress-container">
				<div class="progress-bar">
					<div class="progress-text"></div>
				</div>				
			</div>
		</div>	
		<div id="generate-progress-cancel" style="clear: both; padding-top: 15px; text-align: center;">
			<button type="button" class="btn btn-large btn-danger" onclick="Joomla.submitbutton('generatedialog.cancel');">
				<?php echo JText::_('JCANCEL');?>
			</button>
		</div>				
	</div>

	<div id="generate-warnings" style="display: none;">
		<legend><?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNINGS_LABEL') ?></legend>
		<p><?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNINGS_DESC') ?></p>
		<ul id="generate-warnings-list">
		</ul>	
	</div>	
	<div id="generate-complete" style="display: none;">
	
		<div id="generate-success" style="<?php echo version_compare(JVERSION, '3.0', 'lt') ? '':'text-align: center; ' ?>display: none;">
			<div id="generate-success-message">
			</div>
			<div id="generate-success-path">
			</div>			
			<div id="generate-success-zip"  style="display: none;">
			</div>			
		</div>
		<div id="generate-failed" style="display: none;">
			<div id="generate-failed-message">
			</div>
			<ul id="generate-error">
				<li id="generate-error-message">
				</li>			
			</ul>
		</div>
		<div id="generate-next" class="left" style="clear: both; padding-top: 15px; text-align: center; ">
			<button type="button" class="btn btn-large" onclick="window.location='index.php?option=com_componentarchitect&view=components'; return false;"  id="generate-back">
				<?php echo JText::_('JTOOLBAR_CLOSE');?>
			</button>
			<button type="button" class="btn btn-large hasTip" title="<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_VIEW_LOGS_BUTTON_DESC');?>" onclick="window.location='index.php?option=com_componentarchitect&view=logs'; return false;"  id="generate-next-view-logs"  style="display: none;">
				<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_VIEW_LOGS_BUTTON');?>
			</button>
			<button id="generate-another" type="button" class="btn btn-large btn-primary hasTip" title="<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_ANOTHER_BUTTON_DESC');?>" onclick="window.location='index.php?option=com_componentarchitect&view=generatedialog'; return false;">
				<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_ANOTHER_BUTTON');?>
			</button>
			<button id="generate-install" type="button" class="btn btn-large btn-success hasTip" title="<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_INSTALL_BUTTON_DESC');?>" onclick="Joomla.submitbutton('generatedialog.install'); return false;" style="display: none;">
				<?php echo JText::_('COM_COMPONENTARCHITECT_GENERATE_INSTALL_BUTTON');?>
			</button>
			<span id="generate-download" style="display: none;">
			</span>				
			<input type="hidden" id="install-url" name="install_url" value="" />									
		</div>
	</div>

</form>
<!-- [%%END_CUSTOM_CODE%%] -->
