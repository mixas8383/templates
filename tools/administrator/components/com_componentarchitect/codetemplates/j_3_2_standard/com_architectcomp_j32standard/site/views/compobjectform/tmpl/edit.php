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
 * @CAsubpackage	architectcomp.site
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
	
// Add css files for the [%%architectcomp%%] component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%architectcomp%%].css');
$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%compobjectplural%%].css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%architectcomp%%]-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/[%%com_architectcomp%%]/css/site_[%%compobjectplural%%]-rtl.css');
}

// Add Javscript functions for field display
JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');	
JHtml::_('formbehavior.chosen', 'select');
[%%IF INCLUDE_VERSIONS%%]
if ($this->params->get('save_history') AND $this->params->get('[%%compobject%%]_save_history'))
{
	JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');
}
[%%ENDIF INCLUDE_VERSIONS%%]						
$this->document->addScript(JUri::root() .'media/[%%com_architectcomp%%]/js/[%%architectcomp%%]validate.js');

$this->document->addScript(JUri::root() .'media/[%%com_architectcomp%%]/js/formsubmitbutton.js');

JText::script('[%%COM_ARCHITECTCOMP%%]_ERROR_ON_FORM');

/*
 *	Initialise values for the layout 
 */	
 
// Create shortcut to parameters.
$params = $this->state->get('params');

/*
 *	Layout HTML
 */
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="[%%architectcomp%%] [%%compobject%%]-edit<?php echo $this->escape($params->get('pageclass_sfx')); ?>">
	<?php if ($params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	[%%IF INCLUDE_NAME%%]
	<?php if ($params->get('show_[%%compobject%%]_name')) : ?>
		<div style="float: left;">
		<h2>
			<?php  
				if (!is_null($this->item->id)) :
					echo JText::sprintf('[%%COM_ARCHITECTCOMP%%]_EDIT_ITEM', $this->escape($this->item->name)); 
				else :
					echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_CREATE_ITEM');
				endif;
			?>
		</h2>
		</div>
		<div style="clear:both;"></div>
	<?php endif; ?>
	[%%ENDIF INCLUDE_NAME%%]
	<form action="<?php echo JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]form&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="[%%compobject%%]-form" class="form-validate">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('[%%compobject%%].save')">
					<span class="icon-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('[%%compobject%%].cancel')">
					<span class="icon-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
			[%%IF INCLUDE_VERSIONS%%]
			<?php if ($params->get('save_history') AND $params->get('[%%compobject%%]_save_history')) : ?>
				<div class="btn-group">
					<?php echo $this->form->getInput('contenthistory'); ?>
				</div>
			<?php endif; ?>	
			[%%ENDIF INCLUDE_VERSIONS%%]		
		</div>		
		<div style="clear:both;padding-top: 10px;"></div>
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#basic-details" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_DETAILS_LABEL');?></a></li>
				[%%FOREACH OBJECT_FIELDSET%%]	
					[%%IF FIELDSET_NOT_BASIC_DETAILS%%]
				<li><a href="#fieldset-[%%FIELDSET_CODE_NAME%%]" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_[%%FIELDSET_CODE_NAME_UPPER%%]_LABEL');?></a></li>
					[%%ENDIF FIELDSET_NOT_BASIC_DETAILS%%]
				[%%ENDFOR OBJECT_FIELDSET%%]
				[%%FOREACH REGISTRY_FIELD%%]
				<li><a href="#[%%FIELD_CODE_NAME%%]" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL');?></a></li>
				[%%ENDFOR REGISTRY_FIELD%%]
				[%%IF INCLUDE_IMAGE%%]
					[%%IF INCLUDE_URLS%%]
				<li><a href="#imageslinks" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_IMAGES_URLS_LABEL');?></a></li>
					[%%ELSE INCLUDE_URLS%%]
				<li><a href="#images" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_IMAGES_LABEL');?></a></li>
					[%%ENDIF INCLUDE_URLS%%]
				[%%ELSE INCLUDE_IMAGE%%]
					[%%IF INCLUDE_URLS%%]
				<li><a href="#links" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_URLS_LABEL');?></a></li>
					[%%ENDIF INCLUDE_URLS%%]
				[%%ENDIF INCLUDE_IMAGE%%]
				[%%IF INCLUDE_ASSETACL%%]
				<?php if ($this->item->params->get('access-change')): ?>
				[%%ENDIF INCLUDE_ASSETACL%%]									
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL');?></a></li>
				[%%IF INCLUDE_METADATA%%]
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_METADATA_LABEL');?></a></li>
				[%%ENDIF INCLUDE_METADATA%%]
				[%%IF INCLUDE_LANGUAGE%%]
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_LANGUAGE_LABEL');?></a></li>
				[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%IF INCLUDE_ASSETACL%%]
				<?php endif; ?>				
				[%%ENDIF INCLUDE_ASSETACL%%]									
			</ul>		
		
		
			<div class="tab-content">
				<div class="tab-pane active" id="basic-details">
					[%%IF INCLUDE_NAME%%]
					<div class="control-group" id="field_name">
						<div class="control-label">
							<?php echo $this->form->getLabel('name'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('name'); ?>
						</div>
					</div>
						[%%IF INCLUDE_ALIAS%%]
							[%%IF INCLUDE_ASSETACL%%]
					<?php if ($this->item->params->get('access-change')):?>
							[%%ENDIF INCLUDE_ASSETACL%%]						
						<div class="control-group" id="field_alias">
							<div class="control-label">
								<?php echo $this->form->getLabel('alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
							[%%IF INCLUDE_ASSETACL%%]
					<?php endif; ?>
							[%%ENDIF INCLUDE_ASSETACL%%]			
						[%%ENDIF INCLUDE_ALIAS%%]
					[%%ENDIF INCLUDE_NAME%%]
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
						<div class="control-label">
							<?php echo $this->form->getLabel('tags'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('tags'); ?>
						</div>
					</div>
					[%%ENDIF INCLUDE_TAGS%%]
					[%%IF INCLUDE_VERSIONS%%]
					<?php if ($params->get('save_history') AND $params->get('[%%compobject%%]_save_history')) : ?>
					<div class="control-group" id="field_version_note">
						<div class="control-label">
							<?php echo $this->form->getLabel('version_note'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
					</div>
					<?php endif; ?>	
					[%%ENDIF INCLUDE_VERSIONS%%]
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
							<?php if (trim($this->item->[%%FIELD_CODE_NAME%%] != '')) : ?>
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
					[%%IF INCLUDE_DESCRIPTION%%]
					<div class="control-group" id="field_introdescription">
						[%%IF INCLUDE_INTRO%%]
						<div class="control-label">
							<?php echo $this->form->getLabel('introdescription'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('introdescription'); ?>
						</div>
						[%%ELSE INCLUDE_INTRO%%]
						<div class="control-label">
							<?php echo $this->form->getLabel('description'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('description'); ?>
						</div>
						[%%ENDIF INCLUDE_INTRO%%]
					</div>
					[%%ENDIF INCLUDE_DESCRIPTION%%]	
				</div>
				[%%FOREACH OBJECT_FIELDSET%%]	
					[%%IF FIELDSET_NOT_BASIC_DETAILS%%]
				<div class="tab-pane" id="fieldset-[%%FIELDSET_CODE_NAME%%]">
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
					[%%ENDIF FIELDSET_NOT_BASIC_DETAILS%%]
				[%%ENDFOR OBJECT_FIELDSET%%]			

				[%%FOREACH REGISTRY_FIELD%%]
				<div class="tab-pane" id="[%%FIELD_CODE_NAME%%]">
				
				<?php $fieldsets = $this->form->getFieldsets('[%%FIELD_CODE_NAME%%]');?>
					<?php foreach ($fieldsets as $name => $fieldset) :?>
						<?php
							if (count($fieldsets > 1)) :
								echo '<h3>'.$fieldset->name.'</h3>';
							endif;
						?>					
						<?php
							if (isset($fieldset->description) AND trim($fieldset->description)) :
								echo '<p class="alert alert-info">'.$this->escape(JText::_($fieldset->description)).'</p>';
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
				[%%ENDFOR REGISTRY_FIELD%%] 
				[%%IF INCLUDE_IMAGE%%]
					[%%IF INCLUDE_URLS%%]
				<div class="tab-pane" id="imageslinks">
					[%%ELSE INCLUDE_URLS%%]
				<div class="tab-pane" id="images">
					[%%ENDIF INCLUDE_URLS%%]
				[%%ELSE INCLUDE_IMAGE%%]
					[%%IF INCLUDE_URLS%%]
				<div class="tab-pane" id="links">
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
				[%%ELSE INCLUDE_IMAGE%%]
					[%%IF INCLUDE_URLS%%]
				</div>
					[%%ENDIF INCLUDE_URLS%%]
				[%%ENDIF INCLUDE_IMAGE%%]	
				
				[%%IF INCLUDE_ASSETACL%%]
				<?php if ($this->item->params->get('access-change')): ?>
				[%%ENDIF INCLUDE_ASSETACL%%]
					<div class="tab-pane" id="publishing">
						[%%IF INCLUDE_STATUS%%]
						<div class="control-group" id="field_state">
							<div class="control-label">
								<?php echo $this->form->getLabel('state'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('state'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_STATUS%%]
						[%%IF INCLUDE_ACCESS%%]
						<div class="control-group" id="field_access">
							<div class="control-label">
								<?php echo $this->form->getLabel('access'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_ACCESS%%]
						[%%IF INCLUDE_FEATURED%%]
						<div class="control-group" id="field_featured">
							<div class="control-label">
								<?php echo $this->form->getLabel('featured'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('featured'); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_FEATURED%%]					
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
							<div class="control-label"><?php echo $this->form->getLabel('publish_down'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
						</div>
						[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
						[%%IF INCLUDE_CREATED%%]
						<div class="control-group" id="field_created_by">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_by'); ?>
							</div>
							<div class="controls">
								<?php $created_by =  $this->item->created_by ?>
								<?php $created_by = ($this->item->created_by_name ? $this->item->created_by_name : $created_by);?>
								<?php if (!empty($this->item->created_by )):?>
									<?php echo JHTML::_(
											'link',
											JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
									$created_by);
									?>
								<?php else :?>
							<?php echo $created_by; ?>
								<?php endif; ?>
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
								<?php echo JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
							</div>
						</div>
						[%%ENDIF INCLUDE_CREATED%%]						
						[%%IF INCLUDE_MODIFIED%%]
						<?php if ($this->item->modified_by) : ?>
							<div class="control-group" id="field_modified_by">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified_by'); ?>
								</div>
								<div class="controls">
									<?php $modifier =  $this->item->modified_by ?>
									<?php $modifier = ($this->item->modified_by_name ? $this->item->modified_by_name : $creator);?>
									<?php if (!empty($this->item->modified_by )):?>
										<?php echo JHTML::_(
												'link',
												JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->modified_by),
												$modifier);
										?>
									<?php else :?>
										<?php echo $modifier; ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="control-group" id="field_modified">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified'); ?>
								</div>
								<div class="controls">
									<?php echo JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2')); ?>
								</div>
							</div>
						<?php endif; ?>
						[%%ENDIF INCLUDE_MODIFIED%%]
						[%%IF INCLUDE_ORDERING%%]
						<?php if (!is_null($this->item->id)):?>
							<div class="control-group" id="field_ordering">
								<div class="control-label">
									<?php echo JText::_('JFIELD_ORDERING_LABEL'); ?>
								</div>
								<div class="controls">
									<?php echo $this->item->ordering; ?>
								</div>
							</div>
						<?php else: ?>
							<div class="form-note">
							<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_ORDERING'); ?></p>
							</div>
						<?php endif; ?>
						[%%ENDIF INCLUDE_ORDERING%%]												
					</div>	
					[%%IF INCLUDE_METADATA%%]
					<div class="tab-pane" id="metadata">
						<div class="control-group" id="field_metakey">
							<div class="control-label">
								<?php echo $this->form->getLabel('metakey'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('metakey'); ?>
							</div>						
						</div>
						<div class="control-group" id="field_metadesc">
							<div class="control-label">
								<?php echo $this->form->getLabel('metadesc'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('metadesc'); ?>
							</div>						
						</div>	
						<div class="control-group" id="field_robots">
							<div class="control-label">
								<?php echo $this->form->getLabel('robots'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('robots'); ?>
							</div>						
						</div>
						<div class="control-group" id="field_xreference">
							<div class="control-label">
								<?php echo $this->form->getLabel('xreference'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('xreference'); ?>
							</div>						
						</div>																	
					</div>					
					[%%ENDIF INCLUDE_METADATA%%]						
					[%%IF INCLUDE_LANGUAGE%%]
					<div class="tab-pane" id="language">
						<div class="control-group" id="field_language">
							<div class="control-label">
								<?php echo $this->form->getLabel('language'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('language'); ?>
							</div>
						</div>
					</div>	
					[%%ENDIF INCLUDE_LANGUAGE%%]							
				[%%IF INCLUDE_ASSETACL%%]
				<?php endif; ?>
				[%%ENDIF INCLUDE_ASSETACL%%]				
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="form_id" id="form_id" value="[%%compobject%%]-form" />
				<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
				[%%IF GENERATE_CATEGORIES%%]
				<?php if($this->params->get('enable_category', 0) == 1) :?>
					<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
				<?php endif;?>
				[%%ENDIF GENERATE_CATEGORIES%%]
				<?php echo JHtml::_( 'form.token' ); ?>
			</div>
		</fieldset>													
	</form>
</div>