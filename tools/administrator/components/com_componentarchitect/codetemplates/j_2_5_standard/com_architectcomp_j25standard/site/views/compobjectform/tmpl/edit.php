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
 * @CAsubpackage	architectcomp.site
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

// Create shortcut to parameters.
$params = $this->state->get('params');
?>
<noscript>
<p style="color: red;"><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="[%%architectcomp%%] [%%compobject%%]-edit<?php echo $this->escape($params->get('pageclass_sfx')); ?>">
	<?php if ($params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($params->get('page_heading')); ?>
	</h1>
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
	<form action="<?php echo JRoute::_('index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]form&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
		<div class="formelm-buttons" style="float: right;width: 40%;">
			<div class="formelm-buttons">
				<button type="button" onclick="Joomla.submitbutton('[%%compobject%%].save')">
					<?php echo JText::_('JSAVE') ?>
				</button>
				<button type="button" onclick="Joomla.submitbutton('[%%compobject%%].cancel')">
					<?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
		</div>
		<div style="clear:both;padding-top: 10px;"></div>
		<fieldset>
			<div>
				[%%IF INCLUDE_NAME%%]
				<div class="formelm">
					<?php echo $this->form->getLabel('name'); ?>
					<?php echo $this->form->getInput('name'); ?>
				</div>
					[%%IF INCLUDE_ALIAS%%]
				<?php if (is_null($this->item->id)):?>
					<div class="formelm">
						<?php echo $this->form->getLabel('alias'); ?>
						<?php echo $this->form->getInput('alias'); ?>
					</div>
				<?php endif; ?>
					[%%ENDIF INCLUDE_ALIAS%%]
				[%%ENDIF INCLUDE_NAME%%]
				
				[%%IF GENERATE_CATEGORIES%%]		
				<div class="formelm">
				<?php echo $this->form->getLabel('catid'); ?>
				<span class="category">
					<?php   echo $this->form->getInput('catid'); ?>
				</span>
				</div>		
				[%%ENDIF GENERATE_CATEGORIES%%]			
			</div>
			[%%IF INCLUDE_DESCRIPTION%%]
			<div style="padding-top: 10px;">
				[%%IF INCLUDE_INTRO%%]
				<?php echo $this->form->getLabel('introdescription'); ?>
				<?php echo $this->form->getInput('introdescription'); ?>
				[%%ELSE INCLUDE_INTRO%%]
				<?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?>
				[%%ENDIF INCLUDE_INTRO%%]
			</div>
			[%%ENDIF INCLUDE_DESCRIPTION%%]

		</fieldset>
	
		[%%FOREACH OBJECT_FIELDSET%%]
		<fieldset>
			<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELDSET_[%%FIELDSET_CODE_NAME_UPPER%%]_LABEL'); ?></legend>

			[%%FOREACH OBJECT_FIELD%%]
			<div class="formelm">
				<?php echo $this->form->getLabel('[%%FIELD_CODE_NAME%%]'); ?>
				<?php echo $this->form->getInput('[%%FIELD_CODE_NAME%%]'); ?>
			</div>		
			[%%ENDFOR OBJECT_FIELD%%]	
		</fieldset>	
		[%%ENDFOR OBJECT_FIELDSET%%]
		[%%FOREACH REGISTRY_FIELD%%]
		<fieldset>
			<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_FIELD_[%%FIELD_CODE_NAME_UPPER%%]_LABEL'); ?></legend>
			<?php $fieldSets = $this->form->getFieldsets('[%%FIELD_CODE_NAME%%]');?>
			<?php foreach ($fieldSets as $name => $fieldSet) :?>

				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<div class="formelm">					
						<?php echo $field->label; ?>
						<?php echo $field->input; ?>
				</div>							
				<?php endforeach; ?>
			<?php endforeach; ?>	
		</fieldset>	
		[%%ENDFOR REGISTRY_FIELD%%]
		[%%IF INCLUDE_IMAGE%%]	
		<fieldset>
			<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_IMAGE_LABEL'); ?></legend>
			<!-- Slightly different for image edit function to allow for buttons and preview --> 	
			<div class="formelm" style="float: left;">
				<?php echo $this->form->getLabel('image_url'); ?>
			</div>		
			<div class="formelm" style="float: left;">
				<?php echo $this->form->getInput('image_url'); ?>
			</div>		
			<div class="formelm">
				<?php echo $this->form->getLabel('image_alt_text'); ?>
				<?php echo $this->form->getInput('image_alt_text'); ?>
			</div>	
		</fieldset>	
		[%%ENDIF INCLUDE_IMAGE%%]
		[%%IF INCLUDE_URLS%%]
		<fieldset>
			<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_URLS_LABEL'); ?></legend>
			<?php foreach ($this->form->getGroup('urls') as $field) : ?>
				<div class="formelm">
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				</div>
			<?php endforeach; ?>
		</fieldset>	
		[%%ENDIF INCLUDE_URLS%%]			
		[%%IF INCLUDE_ASSETACL%%]
		<?php if ($this->item->params->get('access-change')): ?>
		[%%ENDIF INCLUDE_ASSETACL%%]
		<fieldset>
			<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_PUBLISHING_LABEL'); ?></legend>						
			[%%IF INCLUDE_CREATED%%]
			<?php $creator =  $this->item->created_by ?>
			<?php $creator = ($this->item->created_by_name ? $this->item->created_by_name : $creator);?>

			<div class="formelm">
				<label>				
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_BY_LABEL'); ?> 
				</label>
				<span>
					<?php if (!empty($this->item->created_by )):?>
						<?php echo JHTML::_(
								'link',
								JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->created_by),
								$creator);
						?>

					<?php else :?>
						<?php echo $creator; ?>
					<?php endif; ?>
				</span>
			</div>
			<div class="formelm">
				<label>				
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_CREATED_LABEL'); ?>
				</label>				
				<span>				
					<?php echo JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
				</span>
			</div>
						
			[%%ENDIF INCLUDE_CREATED%%]
			[%%IF INCLUDE_MODIFIED%%]
			<?php $modifier =  $this->item->modified_by ?>
			<?php $modifier = ($this->item->modified_by_name ? $this->item->modified_by_name : $creator);?>
			<div class="formelm">				
				<label>				
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_MODIFIED_BY_LABEL'); ?> 
				</label>				
				<span>				
					<?php if (!empty($this->item->modified_by )):?>
						<?php echo JHTML::_(
								'link',
								JRoute::_('index.php?option=com_users&view=profile&id='.$this->item->modified_by),
								$modifier);
						?>

					<?php else :?>
						<?php echo $modifier; ?>
					<?php endif; ?>
				</span>					
			</div>
			<div class="formelm">
				<label>				
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_MODIFIED_LABEL'); ?>				
				</label>				
				<span>				
					<?php echo JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2')); ?>
				</span>				
			</div>

			[%%ENDIF INCLUDE_MODIFIED%%]		
			[%%IF INCLUDE_PUBLISHED_DATES%%]
			<div class="formelm">
				<?php echo $this->form->getLabel('publish_up'); ?>
				<?php echo $this->form->getInput('publish_up'); ?>
			</div>
			<div class="formelm">
				<?php echo $this->form->getLabel('publish_down'); ?>
				<?php echo $this->form->getInput('publish_down'); ?>
			</div>	
			[%%ENDIF INCLUDE_PUBLISHED_DATES%%]			
			[%%IF INCLUDE_FEATURED%%]
			<div class="formelm">
				<?php echo $this->form->getLabel('featured'); ?>
				<?php echo $this->form->getInput('featured'); ?>
			</div>
			[%%ENDIF INCLUDE_FEATURED%%]
			[%%IF INCLUDE_LANGUAGE%%]
				<div class="formelm">
				<?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?>
				</div>
			[%%ENDIF INCLUDE_LANGUAGE%%]
			[%%IF INCLUDE_STATUS%%]
			<div class="formelm">
				<?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?>
			</div>
			[%%ENDIF INCLUDE_STATUS%%]
			[%%IF INCLUDE_ACCESS%%]
			<div class="formelm">
				<?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?>
			</div>	
			[%%ENDIF INCLUDE_ACCESS%%]
			[%%IF INCLUDE_ORDERING%%]
			<?php if (!is_null($this->item->id)):?>
				<div class="formelm">
					<label>
						<?php echo JText::_('JFIELD_ORDERING_LABEL'); ?>
					</label>
					<span>
						<?php echo $this->item->ordering; ?>
					</span>
				</div>	
			<?php endif; ?>
			[%%ENDIF INCLUDE_ORDERING%%]					
		</fieldset>			
		[%%IF INCLUDE_ASSETACL%%]
		<?php endif; ?>
		[%%ENDIF INCLUDE_ASSETACL%%]		
		[%%IF INCLUDE_METADATA%%]
		<fieldset>
			<legend><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_FIELDSET_METADATA_LABEL'); ?></legend>
			<div class="formelm-area">
			<?php echo $this->form->getLabel('metakey'); ?>
			<?php echo $this->form->getInput('metakey'); ?>
			</div>
			<div class="formelm-area">
			<?php echo $this->form->getLabel('metadesc'); ?>
			<?php echo $this->form->getInput('metadesc'); ?>
			</div>
			<div class="formelm-area">
			<?php echo $this->form->getLabel('robots'); ?>
			<?php echo $this->form->getInput('robots'); ?>
			</div>
			<div class="formelm-area">
			<?php echo $this->form->getLabel('author'); ?>
			<?php echo $this->form->getInput('author'); ?>
			</div>				
			<div class="formelm-area">
			<?php echo $this->form->getLabel('xreference'); ?>
			<?php echo $this->form->getInput('xreference'); ?>
			</div>						
		</fieldset>													
		[%%ENDIF INCLUDE_METADATA%%]
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
		[%%IF GENERATE_CATEGORIES%%]
		<?php if($this->params->get('enable_category', 0) == 1) :?>
			<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
		<?php endif;?>
		[%%ENDIF GENERATE_CATEGORIES%%]
		<?php echo JHtml::_( 'form.token' ); ?>
		[%%IF INCLUDE_ORDERING%%]
		<?php if (is_null($this->item->id)):?>
			<div class="form-note">
			<p><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_ORDERING'); ?></p>
			</div>
		<?php endif; ?>
		[%%ENDIF INCLUDE_ORDERING%%]
	</form>
</div>