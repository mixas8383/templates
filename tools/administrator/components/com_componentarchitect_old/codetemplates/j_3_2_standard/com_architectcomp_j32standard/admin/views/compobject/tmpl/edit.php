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
 * @version			$Id: edit.php 417 2014-10-22 14:42:10Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_2_standard (Release 1.0.4)
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
		<div class="control-group" id="field_name">
			<div class="control-label">
				<?php echo $this->form->getLabel('name'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('name'); ?>
			</div>
		</div>
		[%%IF INCLUDE_ALIAS%%]
		<div class="control-group" id="field_alias">
			<div class="control-label">
				<?php echo $this->form->getLabel('alias'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('alias'); ?>
			</div>
		</div>
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
						<div class="control-group" id="field_catid">
							<div class="control-label">
								<?php echo $this->form->getLabel('catid'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('catid'); ?>
							</div>
						</div>
						[%%ENDIF GENERATE_CATEGORIES%%]
						[%%IF INCLUDE_TAGS%%]
						<div class="control-group" id="field_tags">
							<?php echo $this->form->getLabel('tags'); ?>
							<div class="controls">
								<?php echo $this->form->getInput('tags'); ?>
							</div>
						</div>	
						[%%ENDIF INCLUDE_TAGS%%]			
						[%%IF INCLUDE_STATUS%%]
						<div class="control-group" id="field_state">
							<?php echo $this->form->getLabel('state'); ?>
							<div class="controls">
								<?php echo $this->form->getInput('state'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_STATUS%%]
						[%%IF INCLUDE_ACCESS%%]
						<div class="control-group" id="field_access">
							<?php echo $this->form->getLabel('access'); ?>
							<div class="controls">
								<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_ACCESS%%]
						[%%IF INCLUDE_FEATURED%%]
						<div class="control-group" id="field_featured">
							<?php echo $this->form->getLabel('featured'); ?>
							<div class="controls">
								<?php echo $this->form->getInput('featured'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_FEATURED%%]
						[%%IF INCLUDE_LANGUAGE%%]
						<div class="control-group" id="field_language">
							<?php echo $this->form->getLabel('language'); ?>
							<div class="controls">
								<?php echo $this->form->getInput('language'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_LANGUAGE%%]
						[%%FOREACH OBJECT_FIELDSET%%]
							[%%IF FIELDSET_BASIC_DETAILS%%]
								[%%FOREACH OBJECT_FIELD%%]
									[%%IF FIELD_NOT_HIDDEN%%]
						<div class="control-group" id="field_[%%FIELD_CODE_NAME%%]">
							<div class="control-label">
								<?php echo $this->form->getLabel('[%%FIELD_CODE_NAME%%]'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('[%%FIELD_CODE_NAME%%]'); ?>
										[%%IF FIELD_FILE%%]
								<?php if (trim($this->item->[%%FIELD_CODE_NAME%%]) != '') : ?>
									<div class="button2-left">
										<div class="blank">
											<a title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>" href="<?php echo JRoute::_(JUri::root().trim($this->item->[%%FIELD_CODE_NAME%%]), false); ?>" target="_blank">
												<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>
											</a>
										</div>
									</div>
								<?php endif; ?>									
										[%%ENDIF FIELD_FILE%%]
							</div>
						</div>
									[%%ENDIF FIELD_NOT_HIDDEN%%]
								[%%ENDFOR OBJECT_FIELD%%]
							[%%ENDIF FIELDSET_BASIC_DETAILS%%]
						[%%ENDFOR OBJECT_FIELDSET%%]						
						[%%IF INCLUDE_HITS%%]
						<?php if ($this->item->hits) : ?>
							<div class="control-group" id="field_hits">
								<div class="control-label">
									<?php echo $this->form->getLabel('hits'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('hits'); ?>
								</div>
							</div>
						<?php endif; ?>
						[%%ENDIF INCLUDE_HITS%%]
						[%%IF INCLUDE_VERSIONS%%]
						<?php if ($this->item->version AND $params->get('save_history') AND $params->get('[%%compobject%%]_save_history')) : ?>
							<div class="control-group" id="field_version_note">
								<?php echo $this->form->getLabel('version_note'); ?>
								<div class="controls">
									<?php echo $this->form->getInput('version_note'); ?>
								</div>
							</div>
						<?php endif; ?>
						[%%ENDIF INCLUDE_VERSIONS%%]
						[%%IF INCLUDE_ORDERING%%]
						<div class="control-group" id="field_ordering">
							<div class="control-label">
								<?php echo $this->form->getLabel('ordering'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('ordering'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_ORDERING%%]										
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
				[%%ENDIF INCLUDE_CREATED%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
				<div class="control-group" id="field_publish_up">
					<div class="control-label">
						<?php echo $this->form->getLabel('publish_up'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('publish_up'); ?>
					</div>
				</div>
				<div class="control-group" id="field_publish_down">
					<div class="control-label">
						<?php echo $this->form->getLabel('publish_down'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('publish_down'); ?>
					</div>
				</div>
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
				[%%IF INCLUDE_MODIFIED%%]
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
				[%%ENDIF INCLUDE_MODIFIED%%]
				[%%IF INCLUDE_VERSIONS%%]
				<?php if ($this->item->version AND $params->get('save_history') AND $params->get('[%%compobject%%]_save_history')) : ?>
					<div class="control-group" id="field_version">
						<?php echo $this->form->getLabel('version'); ?>
						<div class="controls">
							<?php echo $this->form->getInput('version'); ?>
						</div>
					</div>
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
						<div class="control-group file"  id="field_<?php echo $fieldname; ?>" style="clear: both;">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
								<?php if (strtolower($field->type) == 'file' AND trim($this->item->$fieldname) != '') : ?>
									<div class="button2-left">
										<div class="blank">
											<a title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>" href="<?php echo JRoute::_(JUri::root().trim($this->item->$fieldname), false); ?>" target="_blank">
												<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>
											</a>
										</div>
									</div>
								<?php endif; ?>									
							</div>	
						</div>
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
							<div class="control-group" id="field_<?php echo $fieldname; ?>">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
									<?php if ($this->item->id > 0) : ?>
										<?php if (strtolower($field->type) == 'file') : ?>
											<?php $field_array = $this->item->$name; ?>
											<?php if (isset($field_array[$fieldname]) AND trim($field_array[$fieldname]) != '') : ?>
												<div class="button2-left">
													<div class="blank">
														<a title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>" href="<?php echo JRoute::_(JUri::root().trim($field_array[$fieldname]), false); ?>" target="_blank">
															<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>
														</a>
													</div>
												</div>
											<?php endif; ?>	
										<?php endif; ?>	
									<?php endif; ?>									
								</div>	
							</div>
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
						<?php $fieldname = (string) $field->fieldname; ?>
						<div class="control-group" id="field_<?php echo $fieldname; ?>">
							<?php if (!$field->hidden) : ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				[%%ENDIF INCLUDE_IMAGE%%]

				[%%IF INCLUDE_URLS%%]
				<div class="span6">
					<?php foreach ($this->form->getGroup('urls') as $field) : ?>
						<?php $fieldname = (string) $field->fieldname; ?>
						<div class="control-group" id="field_<?php echo $fieldname; ?>">
							<?php if (!$field->hidden) : ?>
									<?php echo $field->label; ?>
							<?php endif; ?>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
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
							<div class="control-group" id="field_<?php echo $fieldname; ?>">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
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
							<div class="control-group" id="field_<?php echo $fieldname; ?>">
								<div class="control-label">
									<?php echo $field->label ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
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
