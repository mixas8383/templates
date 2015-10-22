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
 * @version			$Id: edit.php 408 2014-10-19 18:31:00Z BrianWade $
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
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/admin.css');
	
// Add Javascript functions
JHtml::_('bootstrap.tooltip');	
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');		
		
$this->document->addScript(JUri::root() .'media/[%%com_architectcomp%%]/js/[%%architectcomp%%]validate.js');

$this->document->addScript(JUri::root() .'media/[%%com_architectcomp%%]/js/formsubmitbutton.js');

JText::script('[%%COM_ARCHITECTCOMP%%]_ERROR_ON_FORM');
/*
 *	Initialise values for the layout 
 */	
// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>

<form action="<?php echo JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="[%%compobject%%]-form" class="form-validate">

	[%%IF INCLUDE_NAME%%]
	<div class="form-inline form-inline-header">	
		<?php echo $this->form->renderField('name', null, null, array('group_id' => 'field_name')); ?>
		[%%IF INCLUDE_ALIAS%%]
		<?php echo $this->form->renderField('alias', null, null, array('group_id' => 'field_alias')); ?>
		[%%ENDIF INCLUDE_ALIAS%%]
	</div>
	[%%ENDIF INCLUDE_NAME%%]
	<!-- Begin Content -->
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', '[%%compobject%%]-tabs', array('active' => 'details')); ?>
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'details', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_DETAILS_LABEL', true)); ?>
			<div class="row-fluid">
				[%%IF INCLUDE_DESCRIPTION%%]
				<div class="span9">
					<fieldset class="adminform">
					[%%IF INCLUDE_INTRO%%]
						<?php echo $this->form->getInput('introdescription'); ?>
					[%%ELSE INCLUDE_INTRO%%]
						<?php echo $this->form->getInput('description'); ?>
					[%%ENDIF INCLUDE_INTRO%%]
					</fieldset>
				</div>
				[%%ENDIF INCLUDE_DESCRIPTION%%]
				<div class="span3">
					<fieldset class="form-vertical">
						[%%IF GENERATE_CATEGORIES%%]
						<?php echo $this->form->renderField('catid', null, null, array('group_id' => 'field_catid')); ?>
						[%%ENDIF GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_TAGS%%]
						<?php echo $this->form->renderField('tags', null, null, array('group_id' => 'field_tags')); ?>
						[%%ENDIF INCLUDE_TAGS%%]			
						[%%IF INCLUDE_STATUS%%]
						<?php echo $this->form->renderField('state', null, null, array('group_id' => 'field_state')); ?>
						[%%ENDIF INCLUDE_STATUS%%]
						[%%IF INCLUDE_ACCESS%%]
						<?php echo $this->form->renderField('access', null, null, array('group_id' => 'field_access')); ?>
						[%%ENDIF INCLUDE_ACCESS%%]
						[%%IF INCLUDE_FEATURED%%]
						<?php echo $this->form->renderField('featured', null, null, array('group_id' => 'field_featured')); ?>
						[%%ENDIF INCLUDE_FEATURED%%]
						[%%IF INCLUDE_LANGUAGE%%]
						<?php echo $this->form->renderField('language', null, null, array('group_id' => 'field_language')); ?>
						[%%ENDIF INCLUDE_LANGUAGE%%]
						[%%FOREACH OBJECT_FIELDSET%%]
							[%%IF FIELDSET_BASIC_DETAILS%%]
								[%%FOREACH OBJECT_FIELD%%]
									[%%IF FIELD_NOT_HIDDEN%%]
										[%%IF FIELD_NOT_FILE%%]
						<?php echo $this->form->renderField('[%%FIELD_CODE_NAME%%]', null, null, array('group_id' => 'field_[%%FIELD_CODE_NAME%%]')); ?>
										[%%ELSE FIELD_NOT_FILE%%]
						<?php if (trim($this->item->[%%FIELD_CODE_NAME%%] != '')) : ?>
							<?php echo $this->form->renderField('[%%FIELD_CODE_NAME%%]', null, null, array('group_id' => 'field_[%%FIELD_CODE_NAME%%]', 'file' => JRoute::_(JUri::root().trim($this->item->[%%FIELD_CODE_NAME%%]), false))); ?>
						<?php else: ?>	
							<?php echo $this->form->renderField('[%%FIELD_CODE_NAME%%]', null, null, array('group_id' => 'field_[%%FIELD_CODE_NAME%%]')); ?>
						<?php endif; ?>	
										[%%ENDIF FIELD_NOT_FILE%%]						
									[%%ENDIF FIELD_NOT_HIDDEN%%]
								[%%ENDFOR OBJECT_FIELD%%]
							[%%ENDIF FIELDSET_BASIC_DETAILS%%]
						[%%ENDFOR OBJECT_FIELDSET%%]						
						[%%IF INCLUDE_HITS%%]
						<?php if ($this->item->hits) : ?>
							<?php echo $this->form->renderField('hits', null, null, array('group_id' => 'field_hits')); ?>
						<?php endif; ?>
						[%%ENDIF INCLUDE_HITS%%]
						[%%IF INCLUDE_VERSIONS%%]
						<?php if ($this->item->version AND $params->get('save_history') AND $params->get('[%%compobject%%]_save_history')) : ?>
							<?php echo $this->form->renderField('version_note', null, null, array('group_id' => 'field_version_note')); ?>
						<?php endif; ?>
						[%%ENDIF INCLUDE_VERSIONS%%]
						[%%IF INCLUDE_ORDERING%%]
						<?php echo $this->form->renderField('ordering', null, null, array('group_id' => 'field_ordering')); ?>
						[%%ENDIF INCLUDE_ORDERING%%]										
						<?php echo $this->form->renderField('id', null, null, array('group_id' => 'field_id')); ?>
										
					</fieldset>
				</div>				
			</div>				
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			[%%IF INCLUDE_CREATED%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'publishing', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL', true)); ?>
			[%%ELSE INCLUDE_CREATED%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'publishing', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL', true)); ?>
				[%%ELSE INCLUDE_PUBLISHED_DATES%%]
					[%%IF INCLUDE_MODIFIED%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'publishing', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL', true)); ?>
					[%%ELSE INCLUDE_MODIFIED%%]
					[%%IF INCLUDE_VERSIONS%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'publishing', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL', true)); ?>
						[%%ENDIF INCLUDE_VERSIONS%%]
					[%%ENDIF INCLUDE_MODIFIED%%]
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			[%%ENDIF INCLUDE_CREATED%%]
				[%%IF INCLUDE_CREATED%%]
				<?php echo $this->form->renderField('created_by', null, null, array('group_id' => 'field_created_by')); ?>
				<?php echo $this->form->renderField('created_by_alias', null, null, array('group_id' => 'field_created_by_alias')); ?>
				<?php echo $this->form->renderField('created', null, null, array('group_id' => 'field_created')); ?>
				[%%ENDIF INCLUDE_CREATED%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
				<?php echo $this->form->renderField('publish_up', null, null, array('group_id' => 'field_publish_up')); ?>
				<?php echo $this->form->renderField('publish_down', null, null, array('group_id' => 'field_publish_down')); ?>
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
				[%%IF INCLUDE_MODIFIED%%]
				<?php if ($this->item->modified_by) : ?>
					<?php echo $this->form->renderField('modified_by', null, null, array('group_id' => 'field_modified_by')); ?>
					<?php echo $this->form->renderField('modified', null, null, array('group_id' => 'field_modified')); ?>
				<?php endif; ?>
				[%%ENDIF INCLUDE_MODIFIED%%]
				[%%IF INCLUDE_VERSIONS%%]
				<?php if ($this->item->version AND $params->get('save_history') AND $params->get('[%%compobject%%]_save_history')) : ?>
					<?php echo $this->form->renderField('version', null, null, array('group_id' => 'field_version')); ?>
				<?php endif; ?>	
				[%%ENDIF INCLUDE_VERSIONS%%]	
			[%%IF INCLUDE_CREATED%%]
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			[%%ELSE INCLUDE_CREATED%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
			<?php echo JHtml::_('bootstrap.endTab'); ?>
				[%%ELSE INCLUDE_PUBLISHED_DATES%%]
					[%%IF INCLUDE_MODIFIED%%]
			<?php echo JHtml::_('bootstrap.endTab'); ?>
					[%%ELSE INCLUDE_MODIFIED%%]
					[%%IF INCLUDE_VERSIONS%%]
			<?php echo JHtml::_('bootstrap.endTab'); ?>
						[%%ENDIF INCLUDE_VERSIONS%%]
					[%%ENDIF INCLUDE_MODIFIED%%]
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			[%%ENDIF INCLUDE_CREATED%%]							
			[%%FOREACH OBJECT_FIELDSET%%]
				[%%IF FIELDSET_NOT_BASIC_DETAILS%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'fieldset-[%%FIELDSET_CODE_NAME%%]', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_[%%FIELDSET_CODE_NAME_UPPER%%]_LABEL', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<?php foreach($this->form->getFieldset('fieldset_[%%FIELDSET_CODE_NAME%%]') as $field): ?>
					<?php if (!$field->hidden) : ?>
						<?php $fieldname = (string) $field->fieldname; ?>
						
						<?php if (strtolower($field->type) == 'file' AND trim($this->item->$fieldname) != '') : ?>
							<?php echo $this->form->renderField($fieldname, null, null, array('group_id' => 'field_'.$fieldname, 'file' => JRoute::_(JUri::root().trim($this->item->$fieldname), false))); ?>
						<?php else: ?>	
							<?php echo $this->form->renderField($fieldname, null, null, array('group_id' => 'field_'.$fieldname)); ?>
						<?php endif; ?>
					<?php endif; ?>	
				<?php endforeach; ?>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
				[%%ENDIF FIELDSET_NOT_BASIC_DETAILS%%]
			[%%ENDFOR OBJECT_FIELDSET%%]			

			[%%FOREACH REGISTRY_FIELD%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', '[%%FIELD_CODE_NAME%%]', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<?php $fieldsets = $this->form->getFieldsets('[%%FIELD_CODE_NAME%%]');?>
				<?php foreach ($fieldsets as $name => $fieldset) :?>
					<?php
						if (count($fieldsets > 1)) :
							echo '<h3>'.$fieldset->name.'</h3>';
						endif;
					?>
					<?php
						if (isset($fieldset->description) AND trim($fieldset->description)) :
							echo $this->escape(JText::_($fieldset->description));
						endif;
					?>
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<?php if (!$field->hidden) : ?>
							<?php $fieldname = (string) $field->fieldname; ?>

							<?php if ($this->item->id > 0 AND strtolower($field->type) == 'file') : ?>
								<?php $field_array = $this->item->$name; ?>
								<?php if (isset($field_array[$fieldname]) AND trim($field_array[$fieldname]) != '') : ?>
									<?php echo $this->form->renderField($fieldname, '[%%FIELD_CODE_NAME%%]', null, array('group_id' => 'field_'.$fieldname, 'file' => JRoute::_(JUri::root().trim($field_array[$fieldname]), false))); ?>
								<?php else: ?>	
									<?php echo $this->form->renderField($fieldname, '[%%FIELD_CODE_NAME%%]', null, array('group_id' => 'field_'.$fieldname)); ?>
								<?php endif; ?>									
							<?php else: ?>	
								<?php echo $this->form->renderField($fieldname, '[%%FIELD_CODE_NAME%%]', null, array('group_id' => 'field_'.$fieldname)); ?>
							<?php endif; ?>									
						<?php endif; ?>	
					<?php endforeach; ?>
					<?php
						if (count($fieldsets > 1)) :
							echo '<hr />';
						endif;
					?>				
				<?php endforeach; ?>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			[%%ENDFOR REGISTRY_FIELD%%] 
			[%%IF INCLUDE_IMAGE%%]
				[%%IF INCLUDE_URLS%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'imageslinks', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_IMAGES_URLS_LABEL', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				[%%ELSE INCLUDE_URLS%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'images', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_IMAGES_LABEL', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				[%%ENDIF INCLUDE_URLS%%]
			[%%ELSE INCLUDE_IMAGE%%]
				[%%IF INCLUDE_URLS%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'links', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_URLS_LABEL', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				[%%ENDIF INCLUDE_URLS%%]
			[%%ENDIF INCLUDE_IMAGE%%]
				[%%IF INCLUDE_IMAGE%%]
				<div class="span6">
					<?php foreach ($this->form->getGroup('images') as $field) : ?>
						<?php if (!$field->hidden) : ?>
							<?php $fieldname = (string) $field->fieldname; ?>
							<?php echo $this->form->renderField($fieldname, 'images', null, array('group_id' => 'field_'.$fieldname)); ?>							
						<?php endif; ?>							
					<?php endforeach; ?>
				</div>
				[%%ENDIF INCLUDE_IMAGE%%]

				[%%IF INCLUDE_URLS%%]
				<div class="span6">
					<?php foreach ($this->form->getGroup('urls') as $field) : ?>
						<?php if (!$field->hidden) : ?>
							<?php $fieldname = (string) $field->fieldname; ?>
							<?php echo $this->form->renderField($fieldname, 'urls', null, array('group_id' => 'field_'.$fieldname)); ?>							
						<?php endif; ?>						
					<?php endforeach; ?>
				</div>
				[%%ENDIF INCLUDE_URLS%%]
			[%%IF INCLUDE_IMAGE%%]
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			[%%ELSE INCLUDE_IMAGE%%]
				[%%IF INCLUDE_URLS%%]
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
				[%%ENDIF INCLUDE_URLS%%]
			[%%ENDIF INCLUDE_IMAGE%%]						
			[%%IF INCLUDE_PARAMS_RECORD%%]
			<?php $fieldsets = $this->form->getFieldsets('params');?>
			<?php foreach ($fieldsets as $name => $fieldset) :?>
				<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'params-'.$name, JText::_($fieldset->label, true)); ?>
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
			[%%ENDIF INCLUDE_PARAMS_RECORD%%]				

			[%%IF INCLUDE_METADATA%%]
			<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'metadata', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_METADATA_LABEL', true)); ?>
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
			[%%ENDIF INCLUDE_METADATA%%]			
			[%%IF INCLUDE_LANGUAGE%%]
			<?php if (isset($app->item_associations) AND JLanguageAssociations::isEnabled()) : ?>
				<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'associations', JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_ASSOCIATIONS_LABEL', true)); ?>
					<?php echo JLayoutHelper::render('joomla.edit.associations', $this); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>
			[%%ENDIF INCLUDE_LANGUAGE%%]
			[%%IF INCLUDE_ASSETACL_RECORD%%]
			<?php if ($this->can_do->get('core.admin')) : ?>
				<?php echo JHtml::_('bootstrap.addTab', '[%%compobject%%]-tabs', 'permissions', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_RULES', true)); ?>
					<?php echo $this->form->getInput('rules'); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>
			[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="form_id" id="form_id" value="[%%compobject%%]-form" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
	<?php echo JHtml::_('form.token'); ?>
	<!-- End Content -->
</form>
