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
function addObject(e)
{

    var object_entry = jQuery(e).parent().parent();
    jQuery(object_entry).find('input.text-displayed').addClass('required');
    var object_html = jQuery(object_entry).html();
    var last_entry = jQuery(e).parent().parent();

	last_entry.after('<div class ="object_entry"  style="display: block; clear: both;">'+object_html+'</div>');
	last_entry.nextAll().find('input').val('');
	last_entry.nextAll().find('input').attr('value','');
}

function removeObject(e)
{
	// If only one entry clear the values otherwise delete the entry
	if (jQuery(e).parent().parent().parent().parent().find('input.text-displayed').length == 1)
	{
		jQuery(e).parent().parent().parent().parent().find('input.text-displayed').removeClass('required');	
		jQuery(e).parent().parent().parent().find('input').val('');
	}
	else
	{
		jQuery(e).parent().parent().remove();
    }

}
</script>
<?php if (JRequest::getCmd('tmpl', '') == 'component') : ?>
<fieldset>
	<div class="fltlft pagetitle icon-32-componentwizard">
		<h3>
			<?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD'); ?>
		</h3>
	
	</div>	
</fieldset>
<?php endif; ?>	
<form action="<?php echo JRoute::_('index.php?option=com_componentarchitect&view=componentwizard'); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="componentwizard-form" class="form-validate">
	<div id="component-wizard">
		<div>
			<?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_INTRO'); ?>
		</div>	
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			<div class="form-horizontal">
		<?php else: ?>
			<fieldset class="adminform">
		<?php endif; ?>
			<div class="left">
				<div class="control-group" id="field_component_name">
					<div class="control-label"><?php echo $this->form->getLabel('component_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('component_name'); ?></div>
				</div>	
				<div class="control-group" id="field_component_objects">
					<div class="control-label"><?php echo $this->form->getLabel('component_objects'); ?></div>
					<div class="controls">
						<div style="clear:none; float: left;">
							<?php for ($i = 0; $i < count($this->item->component_objects); $i++ ) : ?>
							<div style="width: 900px; overflow: hidden; display: inline;" >
								<div class ="object_entry" style="display: block; clear: both;">
									<div style="display: block; float: left; width: 250px; padding-bottom: 10px;">
										<input class="inputbox" type="text" name="jform[component_objects][name][]" size="50" value="<?php echo $this->item->component_objects[$i]; ?>" />
									</div>
									<div style="float: left; width: 250px; padding-bottom: 10px; padding-right: 10px;">
										<select name="jform[component_objects][source_table][]" id="jform_source_table">
											<?php 
												$db = JFactory::getDbo();
												$tables = $db->getTableList();
												$tables_array[] = array('value' => '', 'text' => JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_FIELD_SOURCE_TABLE_VALUE_SELECT_TABLE_LABEL'));
												for ($i = 0; $i < count($tables); $i++)
												{
													$tables_array[] = array('value' => $tables[$i], 'text' => str_replace($db->getPrefix(), '', $tables[$i]));
												}
												$options = array('option.key' => 'value', 'option.text' => 'text');
												echo JHtml::_('select.options', $tables_array, $options);
											?>
										</select>	
									</div>									
									<div style="display: block; float: left; width: 150px; padding-bottom: 10px;">
										<button class="btn btn-small btn-success" onclick="javascript:addObject(this);" type="button" name="+">
											<?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_OBJECT_ADD_BUTTON'); ?>
										</button>
										<button class="btn btn-small btn-danger" onclick="javascript:removeObject(this);" type="button" name="-">
														<?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_OBJECT_REMOVE_BUTTON'); ?>
										</button>
									</div>
								</div>
							</div>
							<?php endfor; ?>
							<!-- If no object names entered yet output a single blank entry -->
							<?php if (count($this->item->component_objects) == '') : ?>
								<div style="width: 900px; overflow: hidden; display: inline;" >
									<div class ="object_entry" style="display: block; clear: both;">
										<div style="float: left; width: 250px; padding-bottom: 10px; padding-right: 10px;">
											<input class="inputbox" type="text" name="jform[component_objects][name][]" size="50" value="" />
										</div>
										<div style="float: left; width: 250px; padding-bottom: 10px; padding-right: 10px;">
											<select name="jformjform[component_objects][source_table][]" id="jform_source_table">
											<?php 
												$db = JFactory::getDbo();
												$tables = $db->getTableList();
												$tables_array[] = array('value' => '', 'text' => JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_FIELD_SOURCE_TABLE_VALUE_SELECT_TABLE_LABEL'));
												for ($i = 0; $i < count($tables); $i++)
												{
													$tables_array[] = array('value' => $tables[$i], 'text' => str_replace($db->getPrefix(), '', $tables[$i]));
												}
												$options = array('option.key' => 'value', 'option.text' => 'text');
												echo JHtml::_('select.options', $tables_array, $options);
											?>
											</select>	
										</div>	
										<div style=" float: left; width: 150px;padding-bottom: 10px;">
											<button class="btn btn-small btn-success" onclick="javascript:addObject(this);" type="button" name="+">
												<?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_OBJECT_ADD_BUTTON'); ?>
											</button>
											<button class="btn btn-small btn-danger" onclick="javascript:removeObject(this);" type="button" name="-">
												<?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_OBJECT_REMOVE_BUTTON'); ?>
											</button>
										</div>
									</div>
								</div>
							<?php endif; ?>									
						</div>								
					</div>	
				</div>								
				<div><?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_SINGULAR_REMINDER'); ?></div>						
			</div>
			<?php  if (JRequest::getCmd('tmpl', '') == 'component' OR version_compare(JVERSION, '3.0', 'lt')) : ?>
				<div style="clear:both;">
					<div class="formelm-buttons">
						<button type="button" class="btn btn-large btn-primary"  onclick="Joomla.submitbutton('componentwizard.wizardsave');">
							<?php echo JText::_('COM_COMPONENTARCHITECT_COMPONENT_WIZARD_CREATE_BUTTON') ?>
						</button>
							<?php  if (JRequest::getCmd('tmpl', '') == 'component') : ?>					
								<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();">
							<?php else: ?>
								<button type="button" class="btn" onclick="Joomla.submitbutton('componentwizard.cancel');">
							<?php endif; ?>
							<?php echo JText::_('JCANCEL') ?>
						</button>
						<img src="<?php echo '../media/com_componentarchitect/images/help.png'; ?>" alt="<?php echo JText::_('COM_COMPONENTARCHITECT_HELP'); ?>" onclick="Joomla.popupWindow('components/com_componentarchitect/help/en-GB/componentarchitect_component_wizard.html', '<?php echo JText::_('COM_COMPONENTARCHITECT_HELP'); ?>', 700, 500, 1)">
					</div>
				</div>	
			<?php endif; ?>
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			</div>
		<?php else: ?>
			</fieldset>
		<?php endif; ?>
	</div>	
	<div>	
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="form_id" id="form_id" value="componentwizard-form" />
		<?php if (JRequest::getCmd('tmpl', '') == 'component') : ?>
			<input type="hidden" name="close" value="modal" />
		<?php else : ?>
			<input type="hidden" name="close" value="" />
		<?php endif; ?>		
		<?php echo JHtml::_('form.token'); ?>
	</div>

	</div>
</form>
<!-- [%%END_CUSTOM_CODE%%] -->
