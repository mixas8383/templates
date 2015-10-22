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
	
// Add css files for the videomanager component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_videomanager.css');
$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_categorieses.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_videomanager-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/com_videomanager/css/site_categorieses-rtl.css');
}

// Add Javscript functions for field display
JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');	
JHtml::_('formbehavior.chosen', 'select');
if ($this->params->get('save_history') AND $this->params->get('categories_save_history'))
{
	JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');
}
$this->document->addScript(JUri::root() .'media/com_videomanager/js/videomanagervalidate.js');

$this->document->addScript(JUri::root() .'media/com_videomanager/js/formsubmitbutton.js');

JText::script('COM_VIDEOMANAGER_ERROR_ON_FORM');

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
<p style="color: red;"><?php echo JText::_('COM_VIDEOMANAGER_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="videomanager categories-edit<?php echo $this->escape($params->get('pageclass_sfx')); ?>">
	<?php if ($params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<?php if ($params->get('show_categories_name')) : ?>
		<div style="float: left;">
		<h2>
			<?php  
				if (!is_null($this->item->id)) :
					echo JText::sprintf('COM_VIDEOMANAGER_EDIT_ITEM', $this->escape($this->item->name)); 
				else :
					echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_CREATE_ITEM');
				endif;
			?>
		</h2>
		</div>
		<div style="clear:both;"></div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_videomanager&view=categoriesform&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="categories-form" class="form-validate">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('categories.save')">
					<span class="icon-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('categories.cancel')">
					<span class="icon-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
			<?php if ($params->get('save_history') AND $params->get('categories_save_history')) : ?>
				<div class="btn-group">
					<?php echo $this->form->getInput('contenthistory'); ?>
				</div>
			<?php endif; ?>	
		</div>		
		<div style="clear:both;padding-top: 10px;"></div>
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#basic-details" data-toggle="tab"><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_FIELDSET_DETAILS_LABEL');?></a></li>
				<li><a href="#imageslinks" data-toggle="tab"><?php echo JText::_('COM_VIDEOMANAGER_FIELDSET_IMAGES_URLS_LABEL');?></a></li>
				<?php if ($this->item->params->get('access-change')): ?>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_VIDEOMANAGER_FIELDSET_PUBLISHING_LABEL');?></a></li>
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_VIDEOMANAGER_FIELDSET_METADATA_LABEL');?></a></li>
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('COM_VIDEOMANAGER_FIELDSET_LANGUAGE_LABEL');?></a></li>
				<?php endif; ?>				
			</ul>		
		
		
			<div class="tab-content">
				<div class="tab-pane active" id="basic-details">
					<?php echo $this->form->renderField('name', null, null, array('group_id' => 'field_name')); ?>
					<?php if ($this->item->params->get('access-change')):?>
						<?php echo $this->form->renderField('alias', null, null, array('group_id' => 'field_alias')); ?>
					<?php endif; ?>
					<?php echo $this->form->renderField('catid', null, null, array('group_id' => 'field_catid')); ?>
					<?php echo $this->form->renderField('tags', null, null, array('group_id' => 'field_tags')); ?>
					<?php if ($params->get('save_history') AND $params->get('categories_save_history')) : ?>
						<?php echo $this->form->renderField('version_note', null, null, array('group_id' => 'field_version_note')); ?>
					<?php endif; ?>	
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
					<?php echo $this->form->renderField('introdescription', null, null, array('group_id' => 'introdescription')); ?>
				</div>

				<div class="tab-pane" id="imageslinks">
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
				
				<?php if ($this->item->params->get('access-change')): ?>
					<div class="tab-pane" id="publishing">
						<?php echo $this->form->renderField('state', null, null, array('group_id' => 'state')); ?>
						<?php echo $this->form->renderField('access', null, null, array('group_id' => 'access')); ?>
						<?php echo $this->form->renderField('featured', null, null, array('group_id' => 'featured')); ?>
						<?php echo $this->form->renderField('publish_up', null, null, array('group_id' => 'publish_up')); ?>
						<?php echo $this->form->renderField('publish_down', null, null, array('group_id' => 'publish_down')); ?>
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
						<?php if (!is_null($this->item->id)):?>
							<?php echo $this->form->renderField('ordering', null, null, array('group_id' => 'ordering')); ?>						
						<?php else: ?>
							<div class="form-note">
								<p><?php echo JText::_('COM_VIDEOMANAGER_CATEGORIESES_ORDERING'); ?></p>
							</div>
						<?php endif; ?>
					</div>	
					<div class="tab-pane" id="metadata">
						<?php echo $this->form->renderField('metakey', null, null, array('group_id' => 'metakey')); ?>						
						<?php echo $this->form->renderField('metadesc', null, null, array('group_id' => 'metadesc')); ?>						
						<?php echo $this->form->renderField('robots', null, null, array('group_id' => 'robots')); ?>						
						<?php echo $this->form->renderField('xreference', null, null, array('group_id' => 'xreference')); ?>						
					</div>					
					<div class="tab-pane" id="language">
						<?php echo $this->form->renderField('language', null, null, array('group_id' => 'language')); ?>						
					</div>	
				<?php endif; ?>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="form_id" id="form_id" value="categories-form" />
				<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
				<?php if($this->params->get('enable_category', 0) == 1) :?>
					<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
				<?php endif;?>
				<?php echo JHtml::_( 'form.token' ); ?>
			</div>
		</fieldset>													
	</form>
</div>