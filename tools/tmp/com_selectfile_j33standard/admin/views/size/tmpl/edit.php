<?php
/**
 * @version 		$Id:$
 * @name			Selectfile (Release 1.0.0)
 * @author			 ()
 * @package			com_selectfile
 * @subpackage		com_selectfile.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: edit.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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
// Include custom admin css
$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/admin.css');
	
// Add Javascript functions
JHtml::_('bootstrap.tooltip');	
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');		
		
$this->document->addScript(JUri::root() .'media/com_selectfile/js/selectfilevalidate.js');

$this->document->addScript(JUri::root() .'media/com_selectfile/js/formsubmitbutton.js');

JText::script('COM_SELECTFILE_ERROR_ON_FORM');
/*
 *	Initialise values for the layout 
 */	
// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_SELECTFILE_WARNING_NOSCRIPT'); ?><p>
</noscript>

<form action="<?php echo JRoute::_('index.php?option=com_selectfile&view=size&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="size-form" class="form-validate">

	<div class="form-inline form-inline-header">	
		<?php echo $this->form->renderField('name', null, null, array('group_id' => 'field_name')); ?>
	</div>
	<!-- Begin Content -->
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'size-tabs', array('active' => 'details')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'size-tabs', 'details', JText::_('COM_SELECTFILE_SIZES_FIELDSET_DETAILS_LABEL', true)); ?>
			<div class="row-fluid">
				<div class="span3">
					<fieldset class="form-vertical">
						<?php echo $this->form->renderField('state', null, null, array('group_id' => 'field_state')); ?>
						<?php echo $this->form->renderField('access', null, null, array('group_id' => 'field_access')); ?>
						<?php echo $this->form->renderField('ordering', null, null, array('group_id' => 'field_ordering')); ?>
						<?php echo $this->form->renderField('id', null, null, array('group_id' => 'field_id')); ?>
										
					</fieldset>
				</div>				
			</div>				
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'size-tabs', 'publishing', JText::_('COM_SELECTFILE_FIELDSET_PUBLISHING_LABEL', true)); ?>
				<?php echo $this->form->renderField('created_by', null, null, array('group_id' => 'field_created_by')); ?>
				<?php echo $this->form->renderField('created_by_alias', null, null, array('group_id' => 'field_created_by_alias')); ?>
				<?php echo $this->form->renderField('created', null, null, array('group_id' => 'field_created')); ?>
				<?php if ($this->item->modified_by) : ?>
					<?php echo $this->form->renderField('modified_by', null, null, array('group_id' => 'field_modified_by')); ?>
					<?php echo $this->form->renderField('modified', null, null, array('group_id' => 'field_modified')); ?>
				<?php endif; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>



		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="form_id" id="form_id" value="size-form" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
	<?php echo JHtml::_('form.token'); ?>
	<!-- End Content -->
</form>
