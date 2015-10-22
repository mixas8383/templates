<?php
/**
 * @version 		$Id: edit.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: edit.php 34 2014-03-12 12:11:19Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
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
// Create shortcut to parameters.
$params = $this->state->get('params');
$max_file_size = (string) ComponentArchitectHelper::convert_max_file_size($params->get('default_max_upload_size', '2mb'));

$app = JFactory::getApplication();
$input = $app->input;

?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_COMPONENTARCHITECT_WARNING_NOSCRIPT'); ?><p>
</noscript>
<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
	<div id="nojquerywarning">
		<p style="color: red;"><?php echo JText::_('COM_COMPONENTARCHITECT_WARNING_NOJQUERY'); ?><p>
	</div>
	<script type="text/javascript">
		if(jQuery)
		{
			jQuery('#nojquerywarning').css('display','none');
		}
	</script>
<?php endif; ?>
<!--[%%START_CUSTOM_CODE%%]-->
<script type="text/javascript">

var field_type = 1;

// jQuery document load functions for generate progress
jQuery(document).ready(function()
{
    //Initialiase the form
    setFieldGlobals();
	hide_all_attributes();    
	<?php 
		// Set defaults on load only on a new record
		if ($this->item->id == 0)
		{   
			echo "\treset_attribute_defaults();";
			echo "\tset_attribute_defaults();";
		}
    ?>
	show_attributes();  
});

function setFieldGlobals()
{
    //Set global variable with the selected field type

    field_type = jQuery('#jform_fieldtype_id_id').val();
}
function hide_all_attributes()
{
	<?php 
		// Set all field type specific attributes to hidden and their values to empty
		echo "\n";
		echo "\t// Hide field type specific values\n";
		foreach($this->form->getFieldset('fieldset_field_type_specific_attributes') as $field):
			$field_name = $field->fieldname;
			$field_input = $field->id;
			echo "\tjQuery('#field_".strtolower($field_name)."').hide();\n ";
		endforeach;
	?>		
}
function reset_attribute_defaults()
{
	<?php 
		// Set all field type specific attributes to hidden and their values to empty
		echo "\n";
		echo "\t// Set validations values to blank\n";
		echo "\tjQuery('#jform_validation_type').val('');\n ";
		echo "\tjQuery('#jform_allowed_input').val('');\n ";
		echo "// Set field type specific values to blank\n";
		foreach($this->form->getFieldset('fieldset_field_type_specific_attributes') as $field):
			$field_name = $field->fieldname;
			$field_input = $field->id;
			echo "\tjQuery('#field_".strtolower($field_name)."').hide();\n ";
			echo "\tjQuery('#".strtolower($field_input)."').val('');\n ";
			echo "\tjQuery('#".strtolower($field_input)."_id').val(0);\n ";
			echo "\tjQuery('#".strtolower($field_input)."_name').val('".JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_SELECT_ITEM_LABEL')."');\n ";
		endforeach;
		echo "// Set mysql values to blank\n";
		echo "\tjQuery('#jform_mysql_default').val('');\n ";
		echo "\tjQuery('#jform_mysql_size').val('');\n ";
		echo "\tjQuery('#jform_mysql_datatype').val('None');\n ";
		echo "\tjQuery('#jform_mysql_datatype_chzn a.chzn-single span').html('None');\n ";
	?>		
}
function show_attributes()
{
    switch (field_type)
    {
		<?php 
			// Check the field type for each field type specific attribute to see if it should be shown or not
			echo "\n";
			foreach ($this->field_types as $field_type) : 
				echo "// field type: ".$field_type->name."\n";
				echo "\t\tcase '".$field_type->id."' :\n ";
				foreach($this->form->getFieldset('fieldset_field_type_specific_attributes') as $field):
					$field_name = $field->fieldname;
					$field_input = $field->id;
					$field_type_field_name = str_replace('jform_', '', $field_name);
					if (isset($field_type->$field_name)) :
						if ($field_type->$field_name == '1') : 
							echo "\t\t\tjQuery('#field_".strtolower($field_name)."').show();\n ";
						else :
							echo "\t\t\tjQuery('#field_".strtolower($field_name)."').hide();\n ";
							echo "\t\t\tjQuery('#".strtolower($field_input)."').val('');\n ";					
						endif;
					else :
						echo "\t\t\tjQuery('#field_".strtolower($field_name)."').show();\n ";
					endif;						
				endforeach;			
				echo "\t\t\tbreak;\n ";
			endforeach;
			echo "\t\tdefault:\n ";
			echo "\t\t\tbreak;\n ";
		?>
    }
}
function set_attribute_defaults()
{
    switch (field_type)
    {
		<?php 
		// Check the field type for each field type specific attribute to set the default value for the field
		echo "\n";
		foreach ($this->field_types as $field_type) : 
			echo "// field type: ".$field_type->name."\n";
			echo "\t\tcase '".$field_type->id."' :\n ";
			echo "\t// Set validation defaults\n";
			echo "\t\t\tjQuery('#jform_validation_type').val('".$field_type->validation_type_default."');\n ";
			echo "\t\t\tjQuery('#jform_allowed_input').val('".$field_type->allowed_input_default."');\n ";
			echo "\t// Set field type specific defaults\n";
			foreach($this->form->getFieldset('fieldset_field_type_specific_attributes') as $field):
				$field_name = $field->fieldname;
				$field_input = $field->id;
				$field_name_default = $field_name.'_default';
				if (isset($field_type->$field_name_default)) :
					if (strpos($field_type->$field_name_default, "'") === false) :
						echo "\t\t\tjQuery('#".strtolower($field_input)."').val('".$field_type->$field_name_default."');\n ";
						echo "\t\t\tif (jQuery('#".strtolower($field_input)."_chzn a.chzn-single').length)\n";
						echo "\t\t\t{\n"; 
						echo "\t\t\t\tjQuery('#".strtolower($field_input)."_chzn a.chzn-single span').html('".$field_type->$field_name_default."');\n ";
						echo "\t\t\t}\n"; 						
					else :
						echo "\t\t\tjQuery('#".strtolower($field_input)."').val(".$field_type->$field_name_default.");\n ";
						echo "\t\t\tif (jQuery('#".strtolower($field_input)."_chzn a.chzn-single').length)\n";
						echo "\t\t\t{\n"; 
						echo "\t\t\t\tjQuery('#".strtolower($field_input)."_chzn a.chzn-single span').html(".$field_type->$field_name_default.");\n ";
						echo "\t\t\t}\n"; 						
					endif;
				endif;
			endforeach;	
			echo "\t// Set mysql defaults\n";
			foreach($this->form->getFieldset('fieldset_field_mysql_settings') as $field):
				$field_name = $field->fieldname;
				$field_input = $field->id;
				$field_name_default = $field_name.'_default';
				if (isset($field_type->$field_name_default)) :
					if (strpos($field_type->$field_name_default, "'") === false) :
						if ($field_name == 'mysql_default')
						{
							echo "\t\t\tjQuery('#".strtolower($field_input)."').val(\"'".$field_type->$field_name_default."'\");\n ";
						}
						else
						{
							echo "\t\t\tjQuery('#".strtolower($field_input)."').val('".$field_type->$field_name_default."');\n ";
							echo "\t\t\tif (jQuery('#".strtolower($field_input)."_chzn a.chzn-single').length)\n";
							echo "\t\t\t{\n"; 
							echo "\t\t\t\tjQuery('#".strtolower($field_input)."_chzn a.chzn-single span').html('".$field_type->$field_name_default."');\n ";
							echo "\t\t\t}\n"; 
						}
					else :
						if ($field_name == 'mysql_default')
						{
							echo "\t\t\tjQuery('#".strtolower($field_input)."').val(\"".$field_type->$field_name_default."\");\n ";
						}
						else
						{
							echo "\t\t\tjQuery('#".strtolower($field_input)."').val(".$field_type->$field_name_default.");\n ";
							echo "\t\t\tif (jQuery('#".strtolower($field_input)."_chzn a.chzn-single').length)\n";
							echo "\t\t\t{\n"; 
							echo "\t\t\t\tjQuery('#".strtolower($field_input)."_chzn a.chzn-single span').html(".$field_type->$field_name_default.");\n ";
							echo "\t\t\t}\n"; 
						}					
					endif;
				endif;
			endforeach;			
			echo "\t\t\tbreak;\n ";
			endforeach;
		?>
    }
}

function addOption(e) {

    var option_entry = jQuery(e).parent().parent();
    jQuery(option_entry).find('input.text-displayed').addClass('required');
    var option_html = jQuery(option_entry).html();
    var last_entry = jQuery(e).parent().parent();

	if (last_entry.find('input.text-displayed').val() == '')
	{
	    alert ('<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELD_OPTION_VALUES_ERROR_NO_TEXT'); ?>');  			
	}
	else
	{
		last_entry.after('<div class ="option_entry"  style="display: block; clear: both;">'+option_html+'</div>');
		last_entry.nextAll().find('input').val('');
		last_entry.nextAll().find('input').attr('value','');
    }
}

function removeOption(e) {
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
<!--[%%END_CUSTOM_CODE%%]-->
<form action="<?php echo JRoute::_('index.php?option=com_componentarchitect&view=field&layout=edit&id='.(int) $this->item->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="form-validate">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
	<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
		<div class="form-inline form-inline-header">	
	<?php else: ?>
		<fieldset class="adminform">
	<?php endif; ?>		
			<div class="control-group" id="field_name">
				<div class="control-label">
					<?php echo $this->form->getLabel('name'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('name'); ?>
				</div>
			</div>
	<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
		</div>
	<?php else: ?>
		</fieldset>
	<?php endif; ?>

	<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
		<div class="form-horizontal">
	<?php else: ?>
		<fieldset class="adminform">
	<?php endif; ?>
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			<?php echo JHtml::_('bootstrap.startTabSet', 'field-tabs', array('active' => 'field-details')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'field-tabs', 'field-details', JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_DETAILS_LABEL', true)); ?>
		<?php else: ?>
			<?php echo JHtml::_('tabs.start','field-tabs', array('useCookie'=>1)); ?>
			<?php echo JHtml::_('tabs.panel',JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_DETAILS_LABEL'), 'field-details');?>
			<fieldset class="panelform">
		<?php endif; ?>		
			<div class="row-fluid">
				<div class="span9">
					<fieldset class="adminform">
						<?php echo $this->form->getInput('description'); ?>
					</fieldset>
				</div>
				<div class="span3">
					<fieldset class="form-vertical">
						<div class="control-group" id="field_code_name">
							<div class="control-label">
								<?php echo $this->form->getLabel('code_name'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('code_name'); ?>
							</div>
						</div>
						<div class="control-group" id="field_component_id">
							<div class="control-label">
								<?php echo $this->form->getLabel('component_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('component_id'); ?>
							</div>
						</div>
						<div class="control-group" id="field_component_object_id">
							<div class="control-label">
								<?php echo $this->form->getLabel('component_object_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('component_object_id'); ?>
							</div>
						</div>
						<div class="control-group" id="field_fieldset_id">
							<div class="control-label">
								<?php echo $this->form->getLabel('fieldset_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('fieldset_id'); ?>
							</div>
						</div>
						<div class="control-group" id="field_fieldtype_id">
							<div class="control-label">
								<?php echo $this->form->getLabel('fieldtype_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('fieldtype_id'); ?>
							</div>
						</div>
						<div class="control-group" id="field_predefined_field">
							<div class="control-label">
								<?php echo $this->form->getLabel('predefined_field'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('predefined_field'); ?>
							</div>
						</div>
						<div class="control-group" id="field_required">
							<div class="control-label">
								<?php echo $this->form->getLabel('required'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('required'); ?>
							</div>
						</div>
						<div class="control-group" id="field_filter">
							<div class="control-label">
								<?php echo $this->form->getLabel('filter'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('filter'); ?>
							</div>
						</div>
						<div class="control-group" id="field_order">
							<div class="control-label">
								<?php echo $this->form->getLabel('order'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('order'); ?>
							</div>
						</div>
						<div class="control-group" id="field_search">
							<div class="control-label">
								<?php echo $this->form->getLabel('search'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('search'); ?>
							</div>
						</div>
						<div class="control-group" id="field_readonly">
							<div class="control-label">
								<?php echo $this->form->getLabel('readonly'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('readonly'); ?>
							</div>
						</div>
						<div class="control-group" id="field_disabled">
							<div class="control-label">
								<?php echo $this->form->getLabel('disabled'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('disabled'); ?>
							</div>
						</div>
						<div class="control-group" id="field_hidden">
							<div class="control-label">
								<?php echo $this->form->getLabel('hidden'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('hidden'); ?>
							</div>
						</div>
						<div class="control-group" id="field_ordering">
							<div class="control-label">
								<?php echo $this->form->getLabel('ordering'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('ordering'); ?>
							</div>
						</div>
						<div class="control-group" id="field_id">
							<div class="control-label">
								<?php echo $this->form->getLabel('id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('id'); ?>
							</div>
						</div>
					</fieldset>
				</div>				
			</div>
	<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php else: ?>
		</fieldset>
	<?php endif; ?>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<?php echo JHtml::_('bootstrap.addTab', 'field-tabs', 'field-field_specific_attributes', JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_SPECIFIC_ATTRIBUTES_LABEL', true)); ?>
			<?php else: ?>
				<?php echo JHtml::_('tabs.panel',JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_SPECIFIC_ATTRIBUTES_LABEL'), 'field-field_specific_attributes');?>
				<fieldset class="panelform">
			<?php endif; ?>	
			<?php
				echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_SPECIFIC_ATTRIBUTES_DESC');
			?>				
			<?php foreach($this->form->getFieldset('fieldset_field_specific_attributes') as $field): ?>
				<?php if (!$field->hidden) : ?>
					<?php $fieldname = (string) $field->fieldname; ?>
					<div class="control-group" id="field_<?php echo $fieldname; ?>">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>					
						<div class="controls">
							<?php echo $field->input; ?>
							<?php if (strtolower($field->type) == 'file' AND trim($this->item->$fieldname) != '') : ?>
								<div class="file" style="display: inline; padding-left: 10px;">
									<img src="<?php echo JUri::root().trim($this->item->$fieldname); ?>"/>
								</div>
							<?php endif; ?>									
						</div>	
					</div>						
				<?php endif; ?>		
			<?php endforeach; ?>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php else: ?>
				</fieldset>
			<?php endif; ?>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<?php echo JHtml::_('bootstrap.addTab', 'field-tabs', 'field-field_type_specific_attributes', JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_TYPE_SPECIFIC_ATTRIBUTES_LABEL', true)); ?>
			<?php else: ?>
				<?php echo JHtml::_('tabs.panel',JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_TYPE_SPECIFIC_ATTRIBUTES_LABEL'), 'field-field_type_specific_attributes');?>
				<fieldset class="panelform">
			<?php endif; ?>	
			<?php
				echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_TYPE_SPECIFIC_ATTRIBUTES_DESC');
			?>				
			<?php foreach($this->form->getFieldset('fieldset_field_type_specific_attributes') as $field): ?>
				<?php if (!$field->hidden) : ?>
					<?php $fieldname = (string) $field->fieldname; ?>
					<div class="control-group" id="field_<?php echo $fieldname; ?>">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<!--[%%START_CUSTOM_CODE%%]-->
							<?php if ($fieldname == 'option_values') : ?>
								<div id="options_values" style="clear:none; float: left;">

									<div style="overflow: hidden;">
										<div style="display: block; float: left; width: 150px;">
											<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELD_OPTION_VALUES_VALUE_LABEL'); ?>
										</div>
										<div style="display: block; float: left; width: 150px;">
											<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELD_OPTION_VALUES_TEXT_LABEL'); ?>
										</div>
									</div>
									<?php for ($i = 0; $i < count($this->item->option_values['values']); $i++ ) : ?>
										<div style="width: 650px; overflow: hidden; display: inline;" >
											<div class ="option_entry" style="display: block; clear: both;">
												<div style="display: block; float: left; width: 150px;">
													<input class="inputbox" type="text" name="jform[option_values][values][]" size="25" value="<?php echo $this->item->option_values['values'][$i]; ?>" />
												</div>
												<div style="display: block; float: left; width: 350px;">
													<input class="inputbox required text-displayed" type="text" name="jform[option_values][labels][]" size="75" value="<?php echo $this->item->option_values['labels'][$i]; ?>" />
												</div>
												<div style="display: block; float: left; width: 150px;">
													<button class="button" onclick="javascript:addOption(this);" type='button' name="+">
														<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELD_OPTION_VALUES_ADD_BUTTON'); ?>
													</button>
													<button onclick="javascript:removeOption(this);" type='button' name="-">
														<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELD_OPTION_VALUES_REMOVE_BUTTON'); ?>
													</button>
												</div>
											</div>
										</div>
									<?php endfor; ?>
									<!-- If no option values entered yet output a single blank entry -->
									<?php if (count($this->item->option_values['values']) == 0) : ?>
										<div style="width: 650px; overflow: hidden; display: inline;" >
											<div class ="option_entry" style="display: block; clear: both;">
												<div style="display: block; float: left; width: 150px;">
													<input class="inputbox" type="text" name="jform[option_values][values][]" size="25" value="" />
												</div>
												<div style="display: block; float: left; width: 350px;">
													<input class="inputbox text-displayed" type="text" name="jform[option_values][labels][]" size="75" value="" />
												</div>
												<div style="display: block; float: left; width: 150px;">
													<button class="button" onclick="javascript:addOption(this);" type='button' name="+">
														<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELD_OPTION_VALUES_ADD_BUTTON'); ?>
													</button>
													<button onclick="javascript:removeOption(this);" type='button' name="-">
														<?php echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELD_OPTION_VALUES_REMOVE_BUTTON'); ?>
													</button>
												</div>
											</div>
										</div>
									<?php endif; ?>									
								</div>
							<?php else : ?>
								<?php echo $field->input; ?>
								<?php if (strtolower($field->type) == 'file' AND trim($this->item->$fieldname) != '') : ?>
									<div class="file" style="display: inline; padding-left: 10px;">
										<img src="<?php echo JUri::root().trim($this->item->$fieldname); ?>"/>
									</div>
								<?php endif; ?>									
							<?php endif; ?>
							<!--[%%END_CUSTOM_CODE%%]-->
						</div>		
					</div>						
				<?php endif; ?>		
			<?php endforeach; ?>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php else: ?>
				</fieldset>
			<?php endif; ?>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<?php echo JHtml::_('bootstrap.addTab', 'field-tabs', 'field-field_mysql_settings', JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_MYSQL_SETTINGS_LABEL', true)); ?>
			<?php else: ?>
				<?php echo JHtml::_('tabs.panel',JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_MYSQL_SETTINGS_LABEL'), 'field-field_mysql_settings');?>
				<fieldset class="panelform">
			<?php endif; ?>	
			<?php
				echo JText::_('COM_COMPONENTARCHITECT_FIELDS_FIELDSET_FIELD_MYSQL_SETTINGS_DESC');
			?>				
			<?php foreach($this->form->getFieldset('fieldset_field_mysql_settings') as $field): ?>
				<?php if (!$field->hidden) : ?>
					<?php $fieldname = (string) $field->fieldname; ?>
					<div class="control-group" id="field_<?php echo $fieldname; ?>">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
							<?php if (strtolower($field->type) == 'file' AND trim($this->item->$fieldname) != '') : ?>
								<div class="file" style="display: inline; padding-left: 10px;">
									<img src="<?php echo JUri::root().trim($this->item->$fieldname); ?>"/>
								</div>
							<?php endif; ?>									
						</div>	
					</div>						
				<?php endif; ?>		
			<?php endforeach; ?>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php else: ?>
				</fieldset>
			<?php endif; ?>



		
		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'field-tabs', 'field-publishing', JText::_('COM_COMPONENTARCHITECT_FIELDSET_PUBLISHING_LABEL', true)); ?>
		<?php else: ?>
			<?php echo JHtml::_('tabs.panel',JText::_('COM_COMPONENTARCHITECT_FIELDSET_PUBLISHING_LABEL'), 'field-publishing');?>
			<fieldset class="panelform">
		<?php endif; ?>				
			<div class="control-group" id="field_created_by">
				<div class="control-label">
					<?php echo $this->form->getLabel('created_by'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('created_by'); ?>
				</div>
			</div>
			<div class="control-group" id="field_created_by_alias">
				<div class="control-label">
					<?php echo $this->form->getLabel('created_by_alias'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('created_by_alias'); ?>
				</div>
			</div>
			<div class="control-group" id="field_created">
				<div class="control-label">
					<?php echo $this->form->getLabel('created'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('created'); ?>
				</div>
			</div>
			<?php if ($this->item->modified_by) : ?>
				<div class="control-group" id="field_modified_by">
					<div class="control-label">
						<?php echo $this->form->getLabel('modified_by'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('modified_by'); ?>
					</div>
				</div>
				<div class="control-group" id="field_modified">
					<div class="control-label">
						<?php echo $this->form->getLabel('modified'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('modified'); ?>
					</div>
				</div>
			<?php endif; ?>
			<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php else: ?>
				</fieldset>
			<?php endif; ?>

		<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<?php else: ?>
			<?php echo JHtml::_('tabs.end'); ?>
		<?php endif; ?>	
	<?php if (version_compare(JVERSION, '3.0', 'ge')) : ?>
	</div>
	<?php else: ?>
	</fieldset>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="form_id" id="form_id" value="adminForm" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
	<?php echo JHtml::_('form.token'); ?>
	<!-- End Content -->

</form>
