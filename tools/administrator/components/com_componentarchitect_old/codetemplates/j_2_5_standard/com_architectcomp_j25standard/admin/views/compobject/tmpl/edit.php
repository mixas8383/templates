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
 * @version			$Id: edit.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_2_5_standard (Release 1.0.4)
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
$component = JComponentHelper::getComponent( '[%%com_architectcomp%%]' );
$params = new JParameter( $component->params );

?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
<form action="<?php echo JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="[%%compobject%%]-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_DETAILS_LABEL'); ?></legend>
			<ul class="adminformlist">
				[%%IF INCLUDE_NAME%%]
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
					[%%IF INCLUDE_ALIAS%%]
				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>
					[%%ENDIF INCLUDE_ALIAS%%]
				[%%ENDIF INCLUDE_NAME%%]				

				[%%IF GENERATE_CATEGORIES%%]				
				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>				
				[%%ENDIF GENERATE_CATEGORIES%%]
				[%%IF INCLUDE_ACCESS%%]
				<li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>
				[%%ENDIF INCLUDE_ACCESS%%]
				[%%IF INCLUDE_ASSETACL%%]
					[%%IF INCLUDE_ASSETACL_RECORD%%]
				<?php if ($this->can_do->get('core.admin')): ?>
					<li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
						<div class="button2-left"><div class="blank">
							<button type="button" onclick="document.location.href='#access-rules';">
	      					<?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?></button>
						</div></div>
					</li>

				<?php endif; ?>	
					[%%ENDIF INCLUDE_ASSETACL_RECORD%%]	
				[%%ENDIF INCLUDE_ASSETACL%%]			
				[%%IF INCLUDE_STATUS%%]
				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>
				[%%ENDIF INCLUDE_STATUS%%]
				[%%IF INCLUDE_ORDERING%%]
				<li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>
				[%%ENDIF INCLUDE_ORDERING%%]
				[%%IF INCLUDE_FEATURED%%]
				<li><?php echo $this->form->getLabel('featured'); ?>
				<?php echo $this->form->getInput('featured'); ?></li>
				[%%ENDIF INCLUDE_FEATURED%%]
				[%%IF INCLUDE_LANGUAGE%%]
				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>
				[%%ENDIF INCLUDE_LANGUAGE%%]				
				[%%IF INCLUDE_IMAGE%%]				
				<li>
				<div id="image">
					<?php foreach($this->form->getFieldset('image') as $field): ?>
						<?php echo $field->label; ?>
						<?php echo $field->input; ?>
					<?php endforeach; ?>
				</div>
				</li>
				[%%ENDIF INCLUDE_IMAGE%%]
				[%%FOREACH OBJECT_FIELDSET%%]
					[%%IF FIELDSET_BASIC_DETAILS%%]
						[%%FOREACH OBJECT_FIELD%%]
				<li>
							[%%IF FIELD_FILE%%]
					<div class="file" style="clear: both;">
						<?php echo $this->form->getLabel('[%%FIELD_CODE_NAME%%]'); ?>
						<?php echo $this->form->getLabel('[%%FIELD_CODE_NAME%%]'); ?>
						<img src="<?php echo JUri::root().trim($this->item->[%%FIELD_CODE_NAME%%]); ?>"/>
					</div>
							[%%ELSE FIELD_FILE%%]
					<?php echo $this->form->getLabel('[%%FIELD_CODE_NAME%%]'); ?>
								[%%IF FIELD_EDITOR%%]
					<div class="clr"></div>
								[%%ENDIF FIELD_EDITOR%%]								
					<?php echo $this->form->getInput('[%%FIELD_CODE_NAME%%]'); ?>
							[%%ENDIF FIELD_FILE%%]
				</li>
						[%%ENDFOR OBJECT_FIELD%%]
					[%%ENDIF FIELDSET_BASIC_DETAILS%%]
				[%%ENDFOR OBJECT_FIELDSET%%]								
				
				[%%FOREACH OBJECT_FIELDSET%%]
					[%%IF FIELDSET_NOT_BASIC_DETAILS%%]
				<li>
				<div id="[%%FIELDSET_CODE_NAME%%]">
					<?php foreach($this->form->getFieldset('[%%FIELDSET_CODE_NAME%%]') as $field): ?>
						<?php if (strtolower($field->type) == 'file') : ?>
							<?php $fieldname = (string) $field->fieldname; ?>
							<div class="file" style="clear: both;">
								<?php echo $field->label; ?>
								<?php echo $field->input; ?>
								<?php if (trim($this->item->$fieldname) != '') : ?>
									<div class="button2-left">
										<div class="blank">
											<a title="<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>" href="<?php echo JRoute::_(JUri::root().trim($this->item->$fieldname), false); ?>" target="_blank">
												<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_VIEW_FILE');?>
											</a>
										</div>
									</div>
								<?php endif; ?>									
							</div>
						<?php else : ?>	
							<?php echo $field->label; ?>
							<?php if (strtolower($field->type) == 'editor') : ?>
								<div class="clr"></div>
							<?php endif; ?>									
							<?php echo $field->input; ?>
						<?php endif; ?>	
					<?php endforeach; ?>
				</div>
				</li>
					[%%ENDIF FIELDSET_NOT_BASIC_DETAILS%%]
				[%%ENDFOR OBJECT_FIELDSET%%]			
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				
				[%%IF INCLUDE_DESCRIPTION%%]
					[%%IF INCLUDE_INTRO%%]
				<li>
					<?php echo $this->form->getLabel('introdescription'); ?>
					<div class="clr"></div>
					<?php echo $this->form->getInput('introdescription'); ?>
				</li>
					[%%ELSE INCLUDE_INTRO%%]
				<li>
					<?php echo $this->form->getLabel('description'); ?>
					<div class="clr"></div>
					<?php echo $this->form->getInput('description'); ?>
				</li>
					[%%ENDIF INCLUDE_INTRO%%]
				[%%ENDIF INCLUDE_DESCRIPTION%%]

			</ul>
			<div class="clr"> </div>

		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start','[%%compobject%%]_sliders_'.$this->item->id, array('useCookie'=>1)); ?>
		<!--- ??? For the moment alwaysshow the published fieldset but it may be empty if no publish fields set for inclusion ??? -->
		<!--- ??? When mark-up improved make this conditional ??? -->
		<?php echo JHtml::_('sliders.panel',JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_DETAILS_LABEL'), 'publishing'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach($this->form->getFieldset('publishing') as $field): ?>
						<li>
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</li>
					<?php endforeach; ?>
				</ul>

			</fieldset>
		[%%IF INCLUDE_URLS%%]
		<?php echo JHtml::_('sliders.panel',JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_URLS_LABEL'), 'links'); ?>
		
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach ($this->form->getGroup('urls') as $field) : ?>
						<li>
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>	
		[%%ENDIF INCLUDE_URLS%%]
		[%%IF INCLUDE_METADATA%%]
		<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'metadata'); ?>
		
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach($this->form->getFieldset('metadata') as $field): ?>
						<li>
							<?php if (!$field->hidden): ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>	
		[%%ENDIF INCLUDE_METADATA%%]
							
        [%%FOREACH REGISTRY_FIELD%%]
		<?php $fieldSets = $this->form->getFieldsets('[%%FIELD_CODE_NAME%%]');?>
			<?php foreach ($fieldSets as $name => $fieldSet) :?>
				<?php echo JHtml::_('sliders.panel',JText::_($fieldSet->label), $name);?>
				<?php if (isset($fieldSet->description) AND JString::trim($fieldSet->description)) :?>
					<p class="tip"><?php echo JText::_($fieldSet->description);?></p>
				<?php endif;?>
				<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li>
							<?php if (strtolower($field->type) == 'file') : ?>
								<?php $fieldname = (string) $field->fieldname; ?>
								<div class="file" style="clear: both;">
									<?php echo $field->label; ?>
									<?php echo $field->input; ?>
									<?php if ($this->item->id > 0) : ?>
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
								</div>
							<?php else : ?>	
								<?php echo $field->label; ?>
								<?php if (strtolower($field->type) == 'editor') : ?>
									<div class="clr"></div>
								<?php endif; ?>									
								<?php echo $field->input; ?>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				</fieldset>
			<?php endforeach; ?>	
		[%%ENDFOR REGISTRY_FIELD%%] 
		[%%IF INCLUDE_PARAMS_RECORD%%]
		<?php $fieldSets = $this->form->getFieldsets('params');?>
			<?php foreach ($fieldSets as $name => $fieldSet) :?>
				<?php echo JHtml::_('sliders.panel',JText::_($fieldSet->label), $name.'_params');?>
				<?php if (isset($fieldSet->description) AND JString::trim($fieldSet->description)) :?>
					<p class="tip"><?php echo JText::_($fieldSet->description);?></p>
				<?php endif;?>
					<fieldset class="panelform">
					<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
					<?php endforeach; ?>
				</ul>
				</fieldset>
			<?php endforeach; ?>	
		[%%ENDIF INCLUDE_PARAMS_RECORD%%]		
       
        
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div class="clr"></div>
	[%%IF INCLUDE_ASSETACL%%]
		[%%IF INCLUDE_ASSETACL_RECORD%%]
	<?php if ($this->can_do->get('core.admin')): ?>
		<div  class="width-100 fltlft">

			<?php echo JHtml::_('sliders.start','permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php echo JHtml::_('sliders.panel',JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_RULES'), 'access-rules'); ?>	
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>

			<?php echo JHtml::_('sliders.end'); ?>
		</div>
	<?php endif; ?>
		[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
	[%%ENDIF INCLUDE_ASSETACL%%]
	<div>	
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

</form>
