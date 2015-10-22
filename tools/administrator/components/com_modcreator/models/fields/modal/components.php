<?php
/**
 * @version 		$Id: components.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_modcreator
 * @subpackage		com_modcreator.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectplural.php 780 2013-12-05 13:54:10Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
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

defined('JPATH_BASE') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.form.formfield');
}
/**
 * Supports a modal component/extension picker.
 *
 */
class JFormFieldModal_Components extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * 
	 */
	protected $type = 'Modal_Components';
	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel()
	{
		$label = '';

		if ($this->hidden)
		{
			return $label;
		}

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTooltip' : '';
		$class = $this->required == true ? $class . ' required' : $class;
		$class = !empty($this->labelClass) ? $class . ' ' . $this->labelClass : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '_id-lbl" for="' . $this->id . '_id" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			if (version_compare(JVERSION, '3.0', 'lt'))
			{
				JHtml::_('behavior.tooltip');
				$label .= ' title="' . trim($text, ': ').JText::_($this->description).'"';			
			}
			else
			{
				JHtml::_('bootstrap.tooltip');
				$label .= ' title="' . JHtml::tooltipText(trim($text, ':'), JText::_($this->description), 0) . '"';
			}
		}

		// Add the label text and closing tag.
		if ($this->required)
		{
			$label .= '>' . $text . '<span class="star">&#160;*</span></label>';
		}
		else
		{
			$label .= '>' . $text . '</label>';
		}

		return $label;
	}
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * 
	 */
	protected function getInput()
	{
	
		$allow_edit		= ((string) $this->element['edit'] == 'true') ? true : false;
		$allow_clear		= ((string) $this->element['clear'] != 'false') ? true : false;

	
		// Load the javascript
		JHtml::_('behavior.modal', 'a.modal');

		//[%%START_CUSTOM_CODE%%]
		// Build the Select Button script original
		//$script = array();
		//$script[] = '';		
		//$script[] = '	function jSelectComponent_'.$this->id.'(id, name, object)';
		//$script[] = '	{';
		//$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		//$script[] = '		document.getElementById("'.$this->id.'_name").value = name;';
		//if ($allow_edit)
		//{
		//	$script[] = '		jQuery("#'.$this->id.'_edit").removeClass("hidden");';
		//}

		//if ($allow_clear)
		//{
		//	$script[] = '		jQuery("#'.$this->id.'_clear").removeClass("hidden");';
		//}		
		//$script[] = '		SqueezeBox.close();';
		//$script[] = '	}';
		// Build the Select Button script custom
		$script = array();
		
		$script[] = '';		
		$script[] = '	function jSelectComponent_'.$this->id.'(id, name, object)';
		$script[] = '	{';
		$script[] = '		if (document.getElementById("'.$this->id.'_id").value != id)';
		$script[] = '		{';		
		$script[] = '			document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '			document.getElementById("'.$this->id.'_name").value = name;';
		$script[] = '			if (document.getElementById("jform_component_object_id_id") != undefined)';
		$script[] = '			{';
		$script[] = '				document.getElementById("jform_component_object_id_id").value = 0;';
		$script[] = '				document.id("jform_component_object_id_name").value = "'.JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_SELECT_ITEM_LABEL').'";';
		$script[] = '				jQuery("#jform_component_object_id_clear").addClass("hidden");';
		$script[] = '			}';
		$script[] = '			if (document.getElementById("jform_fieldset_id_id") != undefined)';
		$script[] = '			{';
		$script[] = '				document.getElementById("jform_fieldset_id_id").value = 0;';
		$script[] = '				document.getElementById("jform_fieldset_id_name").value = "'.JText::_('COM_COMPONENTARCHITECT_FIELDSETS_SELECT_ITEM_LABEL').'";';
		$script[] = '				jQuery("#jform_fieldset_id_clear").addClass("hidden");';
		$script[] = '			}';
		$script[] = '		}';
		if ($allow_edit)
		{
			$script[] = '		jQuery("#'.$this->id.'_edit").removeClass("hidden");';
		}

		if ($allow_clear)
		{
			$script[] = '		jQuery("#'.$this->id.'_clear").removeClass("hidden");';
		}		
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		//[%%END_CUSTOM_CODE%%]

		// Clear button script
		static $script_clear;

		if ($allow_clear AND !$script_clear)
		{
			$script_clear = true;

			$script[] = '	function jClearComponent(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "'.htmlspecialchars(JText::_('COM_COMPONENTARCHITECT_COMPONENTS_SELECT_ITEM_LABEL', true), ENT_COMPAT, 'UTF-8').'";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		if (document.getElementById("jform_component_object_id_id") != undefined)';
			$script[] = '		{';
			$script[] = '			document.getElementById("jform_component_object_id_id").value = 0;';
			$script[] = '			document.id("jform_component_object_id_name").value = "'.JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_SELECT_ITEM_LABEL').'";';
			$script[] = '			jQuery("#jform_component_object_id_clear").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		if (document.getElementById("jform_fieldset_id_id") != undefined)';
			$script[] = '		{';
			$script[] = '			document.getElementById("jform_fieldset_id_id").value = 0;';
			$script[] = '			document.getElementById("jform_fieldset_id_name").value = "'.JText::_('COM_COMPONENTARCHITECT_FIELDSETS_SELECT_ITEM_LABEL').'";';
			$script[] = '			jQuery("#jform_fieldset_id_clear").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}		
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		// Get the title of the linked chart
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT name' .
			' FROM #__componentarchitect_components' .
			' WHERE id = '.(int) $this->value
		);

		try
		{
			$title = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		if (empty($title))
		{
			$title = JText::_('COM_COMPONENTARCHITECT_COMPONENTS_SELECT_ITEM_LABEL');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');		
		$link = 'index.php?option=com_modcreator&amp;view=components&amp;layout=modal&amp;tmpl=component&amp;function=jSelectComponent_'.$this->id;


		// The active component id field.
		if (0 == (int)$this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int)$this->value;
			$link .= '&amp;currentid='.$this->value;
		}

		// The current component/extension display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			$html[] = '<div class="button2-left">';
			$html[] = '	<div class="blank">';
			$html[] = '		<a class="modal btn" title="'.JText::_('COM_COMPONENTARCHITECT_COMPONENTS_SELECT_BUTTON_DESC').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.JText::_('JSELECT').'</a>';
			$html[] = '	</div>';
			$html[] = '</div>';
		}
		else
		{		
			$html[] = '<a class="modal btn hasTooltip" title="'.JHtml::tooltipText('COM_COMPONENTARCHITECT_COMPONENTS_SELECT_BUTTON_DESC').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i>'.JText::_('JSELECT').'</a>';
		}	
		
		// Edit component/extension button
		if ($allow_edit)
		{
			if (version_compare(JVERSION, '3.0', 'lt'))
			{
				$html[] = '<div class="button2-left">';
				$html[] = '	<div class="blank">';
				$html[] = '<a class="btn'.($value ? '' : ' hidden').'" href="index.php?option=com_modcreator&layout=modal&tmpl=component&task=component.edit&id=' . $value. '" target="_blank" title="'.JText::_('COM_COMPONENTARCHITECT_COMPONENTS_EDIT_DESC').'" >' . JText::_('JACTION_EDIT') . '</a>';
				$html[] = '	</div>';
				$html[] = '</div>';
			}
			else
			{		
				$html[] = '<a class="btn hasTooltip'.($value ? '' : ' hidden').'" href="index.php?option=com_modcreator&layout=modal&tmpl=component&task=component.edit&id=' . $value. '" target="_blank" title="'.JHtml::tooltipText('COM_COMPONENTARCHITECT_COMPONENTS_EDIT_DESC').'" ><span class="icon-edit"></span>' . JText::_('JACTION_EDIT') . '</a>';
			}	
		}

		// Clear component/extension button
		if ($allow_clear)
		{
			if (version_compare(JVERSION, '3.0', 'lt'))
			{
				$html[] = '<div class="button2-left">';
				$html[] = '	<div class="blank">';
				$html[] = '		<a class="btn'.($value ? '' : ' hidden').'" title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '"' . ' href="#" onclick="';
				$html[] = 'document.id(\'' . $this->id . '_name\').value=\''.JText::_('COM_COMPONENTARCHITECT_COMPONENTS_SELECT_ITEM_LABEL').'\';';
				$html[] = 'document.id(\'' . $this->id . '_id\').value=\'\';';
				$html[] = 'return false;';
				$html[] = '">';
				$html[] = JText::_('JLIB_FORM_BUTTON_CLEAR') . '</a>';
				$html[] = '	</div>';
				$html[] = '</div>';
			}
			else
			{			
				$html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? '' : ' hidden').'" onclick="return jClearComponent(\''.$this->id.'\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
			}
		}

		$html[] = '</span>';
		// class='required' for client side validation
		$class = '';
		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';
		return implode("\n", $html);		
	}
}
