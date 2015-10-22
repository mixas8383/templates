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
 * @CAsubpackage	architectcomp.site
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
					<?php echo $this->form->renderField('name', null, null, array('group_id' => 'field_name')); ?>
						[%%IF INCLUDE_ALIAS%%]
							[%%IF INCLUDE_ASSETACL%%]
					<?php if ($this->item->params->get('access-change')):?>
						<?php echo $this->form->renderField('alias', null, null, array('group_id' => 'field_alias')); ?>
					<?php endif; ?>
							[%%ELSE INCLUDE_ASSETACL%%]	
					<?php echo $this->form->renderField('alias', null, null, array('group_id' => 'field_alias')); ?>
							[%%ENDIF INCLUDE_ASSETACL%%]										
						[%%ENDIF INCLUDE_ALIAS%%]
					[%%ENDIF INCLUDE_NAME%%]
					[%%IF GENERATE_CATEGORIES%%]
					<?php echo $this->form->renderField('catid', null, null, array('group_id' => 'field_catid')); ?>
					[%%ENDIF GENERATE_CATEGORIES%%]	
					[%%IF INCLUDE_TAGS%%]
					<?php echo $this->form->renderField('tags', null, null, array('group_id' => 'field_tags')); ?>
					[%%ENDIF INCLUDE_TAGS%%]
					[%%IF INCLUDE_VERSIONS%%]
					<?php if ($params->get('save_history') AND $params->get('[%%compobject%%]_save_history')) : ?>
						<?php echo $this->form->renderField('version_note', null, null, array('group_id' => 'field_version_note')); ?>
					<?php endif; ?>	
					[%%ENDIF INCLUDE_VERSIONS%%]
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
					[%%IF INCLUDE_DESCRIPTION%%]
						[%%IF INCLUDE_INTRO%%]
					<?php echo $this->form->renderField('introdescription', null, null, array('group_id' => 'introdescription')); ?>
						[%%ELSE INCLUDE_INTRO%%]
					<?php echo $this->form->renderField('description', null, null, array('group_id' => 'description')); ?>
						[%%ENDIF INCLUDE_INTRO%%]
					[%%ENDIF INCLUDE_DESCRIPTION%%]	
				</div>
				[%%FOREACH OBJECT_FIELDSET%%]	
					[%%IF FIELDSET_NOT_BASIC_DETAILS%%]
				<div class="tab-pane" id="fieldset-[%%FIELDSET_CODE_NAME%%]">
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
						<?php echo $this->form->renderField('state', null, null, array('group_id' => 'state')); ?>
						[%%ENDIF INCLUDE_STATUS%%]
						[%%IF INCLUDE_ACCESS%%]
						<?php echo $this->form->renderField('access', null, null, array('group_id' => 'access')); ?>
						[%%ENDIF INCLUDE_ACCESS%%]
						[%%IF INCLUDE_FEATURED%%]
						<?php echo $this->form->renderField('featured', null, null, array('group_id' => 'featured')); ?>
						[%%ENDIF INCLUDE_FEATURED%%]					
						[%%IF INCLUDE_PUBLISHED_DATES%%]
						<?php echo $this->form->renderField('publish_up', null, null, array('group_id' => 'publish_up')); ?>
						<?php echo $this->form->renderField('publish_down', null, null, array('group_id' => 'publish_down')); ?>
						[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
						[%%IF INCLUDE_CREATED%%]
						<?php $user = '' ?>
						<?php if (!empty($this->item->created_by )):?>
							<?php $user =  $this->item->created_by ?>
							<?php $user = ($this->item->created_by_name ? $this->item->created_by_name : $user);?>								
							<?php $user = JHtml::_(
														'link',
														JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
														$user);
							?>
						<?php endif; ?>

						<?php echo $this->form->renderField('created_by', null, null, array('group_id' => 'created_by', 'user' => $user)); ?>						
						
						<?php echo $this->form->renderField('created_by_alias', null, null, array('group_id' => 'created_by_alias')); ?>						
						<?php echo $this->form->renderField('created', null, null, array('group_id' => 'created')); ?>						
						[%%ENDIF INCLUDE_CREATED%%]						
						[%%IF INCLUDE_MODIFIED%%]
						<?php if ($this->item->modified_by) : ?>
							<?php $user = '' ?>
							<?php $user =  $this->item->modified_by ?>
							<?php $user = ($this->item->modified_by_name ? $this->item->modified_by_name : $user);?>								
							<?php $user = JHtml::_(
														'link',
														JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->modified_by),
														$user);
							?>

							<?php echo $this->form->renderField('modified_by', null, null, array('group_id' => 'modified_by', 'user' => $user)); ?>							
							
							<?php echo $this->form->renderField('modified', null, null, array('group_id' => 'modified')); ?>						

						<?php endif; ?>
						[%%ENDIF INCLUDE_MODIFIED%%]
						[%%IF INCLUDE_ORDERING%%]
						<?php if (!is_null($this->item->id)):?>
							<?php echo $this->form->renderField('ordering', null, null, array('group_id' => 'ordering')); ?>						
						<?php else: ?>
							<div class="form-note">
								<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_ORDERING'); ?></p>
							</div>
						<?php endif; ?>
						[%%ENDIF INCLUDE_ORDERING%%]												
					</div>	
					[%%IF INCLUDE_METADATA%%]
					<div class="tab-pane" id="metadata">
						<?php echo $this->form->renderField('metakey', null, null, array('group_id' => 'metakey')); ?>						
						<?php echo $this->form->renderField('metadesc', null, null, array('group_id' => 'metadesc')); ?>						
						<?php echo $this->form->renderField('robots', null, null, array('group_id' => 'robots')); ?>						
						<?php echo $this->form->renderField('xreference', null, null, array('group_id' => 'xreference')); ?>						
					</div>					
					[%%ENDIF INCLUDE_METADATA%%]						
					[%%IF INCLUDE_LANGUAGE%%]
					<div class="tab-pane" id="language">
						<?php echo $this->form->renderField('language', null, null, array('group_id' => 'language')); ?>						
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