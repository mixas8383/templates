<?php
/**
 * @version 		$Id:$
 * @name			Videomanager (Release 1.0.0)
 * @author			 ()
 * @package			com_videomanager
 * @subpackage		com_videomanager.admin
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
$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/admin.css');
	
// Add Javascript functions
JHtml::_('bootstrap.tooltip');	
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');		
		
$this->document->addScript(JUri::root() .'media/com_videomanager/js/videomanagervalidate.js');

$this->document->addScript(JUri::root() .'media/com_videomanager/js/formsubmitbutton.js');

JText::script('COM_VIDEOMANAGER_ERROR_ON_FORM');
/*
 *	Initialise values for the layout 
 */	
// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;
?>
<noscript>
<p style="color: red;"><?php echo JText::_('COM_VIDEOMANAGER_WARNING_NOSCRIPT'); ?><p>
</noscript>

<form action="<?php echo JRoute::_('index.php?option=com_videomanager&view=categories&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="categories-form" class="form-validate">

	<div class="form-inline form-inline-header">	
		<?php echo $this->form->renderField('name', null, null, array('group_id' => 'field_name')); ?>
		<?php echo $this->form->renderField('alias', null, null, array('group_id' => 'field_alias')); ?>
	</div>
	<!-- Begin Content -->
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'categories-tabs', array('active' => 'details')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'categories-tabs', 'details', JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELDSET_DETAILS_LABEL', true)); ?>
			<div class="row-fluid">
				<div class="span9">
					<fieldset class="adminform">
						<?php echo $this->form->getInput('introdescription'); ?>
					</fieldset>
				</div>
				<div class="span3">
					<fieldset class="form-vertical">
						<?php echo $this->form->renderField('catid', null, null, array('group_id' => 'field_catid')); ?>
						<?php echo $this->form->renderField('tags', null, null, array('group_id' => 'field_tags')); ?>
						<?php echo $this->form->renderField('state', null, null, array('group_id' => 'field_state')); ?>
						<?php echo $this->form->renderField('access', null, null, array('group_id' => 'field_access')); ?>
						<?php echo $this->form->renderField('featured', null, null, array('group_id' => 'field_featured')); ?>
						<?php echo $this->form->renderField('language', null, null, array('group_id' => 'field_language')); ?>
						<?php echo $this->form->renderField('modified_time', null, null, array('group_id' => 'field_modified_time')); ?>
						<?php echo $this->form->renderField('modified_user_id', null, null, array('group_id' => 'field_modified_user_id')); ?>
						<?php echo $this->form->renderField('created_time', null, null, array('group_id' => 'field_created_time')); ?>
						<?php echo $this->form->renderField('created_user_id', null, null, array('group_id' => 'field_created_user_id')); ?>
						<?php echo $this->form->renderField('metadata', null, null, array('group_id' => 'field_metadata')); ?>
						<?php echo $this->form->renderField('published', null, null, array('group_id' => 'field_published')); ?>
						<?php echo $this->form->renderField('note', null, null, array('group_id' => 'field_note')); ?>
						<?php echo $this->form->renderField('title', null, null, array('group_id' => 'field_title')); ?>
						<?php echo $this->form->renderField('extension', null, null, array('group_id' => 'field_extension')); ?>
						<?php echo $this->form->renderField('path', null, null, array('group_id' => 'field_path')); ?>
						<?php echo $this->form->renderField('level', null, null, array('group_id' => 'field_level')); ?>
						<?php echo $this->form->renderField('rgt', null, null, array('group_id' => 'field_rgt')); ?>
						<?php echo $this->form->renderField('lft', null, null, array('group_id' => 'field_lft')); ?>
						<?php echo $this->form->renderField('parent_id', null, null, array('group_id' => 'field_parent_id')); ?>
						<?php if ($this->item->hits) : ?>
							<?php echo $this->form->renderField('hits', null, null, array('group_id' => 'field_hits')); ?>
						<?php endif; ?>
						<?php if ($this->item->version AND $params->get('save_history') AND $params->get('categories_save_history')) : ?>
							<?php echo $this->form->renderField('version_note', null, null, array('group_id' => 'field_version_note')); ?>
						<?php endif; ?>
						<?php echo $this->form->renderField('ordering', null, null, array('group_id' => 'field_ordering')); ?>
						<?php echo $this->form->renderField('id', null, null, array('group_id' => 'field_id')); ?>
										
					</fieldset>
				</div>				
			</div>				
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'categories-tabs', 'publishing', JText::_('COM_VIDEOMANAGER_FIELDSET_PUBLISHING_LABEL', true)); ?>
				<?php echo $this->form->renderField('created_by', null, null, array('group_id' => 'field_created_by')); ?>
				<?php echo $this->form->renderField('created_by_alias', null, null, array('group_id' => 'field_created_by_alias')); ?>
				<?php echo $this->form->renderField('created', null, null, array('group_id' => 'field_created')); ?>
				<?php echo $this->form->renderField('publish_up', null, null, array('group_id' => 'field_publish_up')); ?>
				<?php echo $this->form->renderField('publish_down', null, null, array('group_id' => 'field_publish_down')); ?>
				<?php if ($this->item->modified_by) : ?>
					<?php echo $this->form->renderField('modified_by', null, null, array('group_id' => 'field_modified_by')); ?>
					<?php echo $this->form->renderField('modified', null, null, array('group_id' => 'field_modified')); ?>
				<?php endif; ?>
				<?php if ($this->item->version AND $params->get('save_history') AND $params->get('categories_save_history')) : ?>
					<?php echo $this->form->renderField('version', null, null, array('group_id' => 'field_version')); ?>
				<?php endif; ?>	
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'categories-tabs', 'imageslinks', JText::_('COM_VIDEOMANAGER_FIELDSET_IMAGES_URLS_LABEL', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php foreach ($this->form->getGroup('images') as $field) : ?>
						<?php if (!$field->hidden) : ?>
							<?php $fieldname = (string) $field->fieldname; ?>
							<?php echo $this->form->renderField($fieldname, 'images', null, array('group_id' => 'field_'.$fieldname)); ?>							
						<?php endif; ?>							
					<?php endforeach; ?>
				</div>

				<div class="span6">
					<?php foreach ($this->form->getGroup('urls') as $field) : ?>
						<?php if (!$field->hidden) : ?>
							<?php $fieldname = (string) $field->fieldname; ?>
							<?php echo $this->form->renderField($fieldname, 'urls', null, array('group_id' => 'field_'.$fieldname)); ?>							
						<?php endif; ?>						
					<?php endforeach; ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php $fieldsets = $this->form->getFieldsets('params');?>
			<?php foreach ($fieldsets as $name => $fieldset) :?>
				<?php echo JHtml::_('bootstrap.addTab', 'categories-tabs', 'params-'.$name, JText::_($fieldset->label, true)); ?>
				<div class="row-fluid form-horizontal-desktop">
					
						<?php
						if (isset($fieldset->description) AND trim($fieldset->description)) :
							echo '<p class="alert alert-info">'.$this->escape(JText::_($fieldset->description)).'</p>';
						endif;
						?>
						<?php foreach ($this->form->getFieldset($name) as $field) : ?>
							<?php $fieldname = (string) $field->fieldname; ?>
							<?php echo $this->form->renderField($fieldname, 'params', null, array('group_id' => 'field_'.$fieldname)); ?>
						<?php endforeach; ?>
				</div>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endforeach; ?>	

			<?php echo JHtml::_('bootstrap.addTab', 'categories-tabs', 'metadata', JText::_('COM_VIDEOMANAGER_FIELDSET_METADATA_LABEL', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<fieldset>
					<?php foreach($this->form->getFieldset('metadata') as $field): ?>
						<?php if (!$field->hidden): ?>
							<?php $fieldname = (string) $field->fieldname; ?>
							<?php echo $this->form->renderField($fieldname, null, null, array('group_id' => 'field_'.$fieldname)); ?>
						<?php endif; ?>
					<?php endforeach; ?>				
				</fieldset>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php if (isset($app->item_associations) AND JLanguageAssociations::isEnabled()) : ?>
				<?php echo JHtml::_('bootstrap.addTab', 'categories-tabs', 'associations', JText::_('COM_VIDEOMANAGER_FIELDSET_ASSOCIATIONS_LABEL', true)); ?>
					<?php echo JLayoutHelper::render('joomla.edit.associations', $this); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>
			<?php if ($this->can_do->get('core.admin')) : ?>
				<?php echo JHtml::_('bootstrap.addTab', 'categories-tabs', 'permissions', JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELDSET_RULES', true)); ?>
					<?php echo $this->form->getInput('rules'); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="form_id" id="form_id" value="categories-form" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
	<?php echo JHtml::_('form.token'); ?>
	<!-- End Content -->
</form>