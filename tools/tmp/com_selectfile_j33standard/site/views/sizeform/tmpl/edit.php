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
	
// Add css files for the selectfile component and categories if they exist
$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_selectfile.css');
$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_sizes.css');

if ($lang->isRTL())
{
	$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_selectfile-rtl.css');
	$this->document->addStyleSheet(JUri::root().'media/com_selectfile/css/site_sizes-rtl.css');
}

// Add Javscript functions for field display
JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');	
JHtml::_('formbehavior.chosen', 'select');
$this->document->addScript(JUri::root() .'media/com_selectfile/js/selectfilevalidate.js');

$this->document->addScript(JUri::root() .'media/com_selectfile/js/formsubmitbutton.js');

JText::script('COM_SELECTFILE_ERROR_ON_FORM');

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
<p style="color: red;"><?php echo JText::_('COM_SELECTFILE_WARNING_NOSCRIPT'); ?><p>
</noscript>
<div class="selectfile size-edit<?php echo $this->escape($params->get('pageclass_sfx')); ?>">
	<?php if ($params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>
	<?php if ($params->get('show_size_name')) : ?>
		<div style="float: left;">
		<h2>
			<?php  
				if (!is_null($this->item->id)) :
					echo JText::sprintf('COM_SELECTFILE_EDIT_ITEM', $this->escape($this->item->name)); 
				else :
					echo JText::_('COM_SELECTFILE_SIZES_CREATE_ITEM');
				endif;
			?>
		</h2>
		</div>
		<div style="clear:both;"></div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_selectfile&view=sizeform&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="size-form" class="form-validate">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('size.save')">
					<span class="icon-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('size.cancel')">
					<span class="icon-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
		</div>		
		<div style="clear:both;padding-top: 10px;"></div>
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#basic-details" data-toggle="tab"><?php echo JText::_('COM_SELECTFILE_SIZES_FIELDSET_DETAILS_LABEL');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_SELECTFILE_FIELDSET_PUBLISHING_LABEL');?></a></li>
			</ul>		
		
		
			<div class="tab-content">
				<div class="tab-pane active" id="basic-details">
					<?php echo $this->form->renderField('name', null, null, array('group_id' => 'field_name')); ?>
				</div>


				
					<div class="tab-pane" id="publishing">
						<?php echo $this->form->renderField('state', null, null, array('group_id' => 'state')); ?>
						<?php echo $this->form->renderField('access', null, null, array('group_id' => 'access')); ?>
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
								<p><?php echo JText::_('COM_SELECTFILE_SIZES_ORDERING'); ?></p>
							</div>
						<?php endif; ?>
					</div>	
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="form_id" id="form_id" value="size-form" />
				<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
				<?php echo JHtml::_( 'form.token' ); ?>
			</div>
		</fieldset>													
	</form>
</div>