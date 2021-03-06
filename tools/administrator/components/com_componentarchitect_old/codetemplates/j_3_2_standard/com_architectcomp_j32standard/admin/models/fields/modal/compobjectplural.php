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
 * @version			$Id: compobjectplural.php 417 2014-10-22 14:42:10Z BrianWade $
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

defined('JPATH_BASE') or die;
/**
 * Supports a modal [%%compobject_name%%] picker.
 *
 */
class JFormFieldModal_[%%CompObjectPlural%%] extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * 
	 */
	protected $type = 'Modal_[%%CompObjectPlural%%]';

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

		[%%IF INCLUDE_LANGUAGE%%]
		// Load language
		JFactory::getLanguage()->load('com_architectcomp', JPATH_ADMINISTRATOR);
		[%%ENDIF INCLUDE_LANGUAGE%%]
	
		// Load the javascript
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		
		// Select button script
		$script[] = '';		
		$script[] = '	function jSelect[%%CompObject%%]_'.$this->id.'(id, name, object)';
		$script[] = '	{';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		[%%IF INCLUDE_NAME%%]
		$script[] = '		document.getElementById("'.$this->id.'_name").value = name;';
		[%%ENDIF INCLUDE_NAME%%]
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
		
		// Clear button script
		static $script_clear;

		if ($allow_clear AND !$script_clear)
		{
			$script_clear = true;

			$script[] = '	function jClear[%%CompObject%%](id) {';
			[%%IF INCLUDE_NAME%%]
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "'.htmlspecialchars(JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_ITEM_LABEL', true), ENT_COMPAT, 'UTF-8').'";';
			[%%ELSE INCLUDE_NAME%%]
			$script[] = '		document.getElementById(id + "_id").value = "'.htmlspecialchars(JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_ITEM_LABEL', true), ENT_COMPAT, 'UTF-8').'";';
			[%%ENDIF INCLUDE_NAME%%]
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}		
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
		[%%IF INCLUDE_NAME%%]
		// Get the title of the linked chart
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT name' .
			' FROM #__[%%architectcomp%%]_[%%compobjectplural%%]' .
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
			$title = JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_ITEM_LABEL');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');		
		[%%ELSE INCLUDE_NAME%%]
		$title = JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_ITEM_LABEL');
		[%%ENDIF INCLUDE_NAME%%]		
		$link = 'index.php?option=[%%com_architectcomp%%]&amp;view=[%%compobjectplural%%]&amp;layout=modal&amp;tmpl=component&amp;function=jSelect[%%CompObject%%]_'.$this->id;

		[%%IF INCLUDE_LANGUAGE%%]
		if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage='.$this->element['language'];
		}	
		[%%ENDIF INCLUDE_LANGUAGE%%]	

		// The active [%%compobject%%] id field.
		if (0 == (int)$this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int)$this->value;
		}

		// The current [%%compobject_name%%] display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '<a class="modal btn hasTooltip" title="'.JHtml::tooltipText('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_BUTTON_DESC').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSELECT').'</a>';

		// Edit [%%compobject_name%%] button
		if ($allow_edit)
		{
			$html[] = '<a class="btn hasTooltip'.($value ? '' : ' hidden').'" href="index.php?option=[%%com_architectcomp%%]&layout=modal&tmpl=component&task=[%%compobject%%].edit&id=' . $value. '" target="_blank" title="'.JHtml::tooltipText('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_EDIT_DESC').'" ><span class="icon-edit"></span> ' . JText::_('JACTION_EDIT') . '</a>';
		}

		// Clear [%%compobject_name%%] button
		if ($allow_clear)
		{
			$html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? '' : ' hidden').'" onclick="return jClear[%%CompObject%%](\''.$this->id.'\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
		}

		$html[] = '</span>';
		// class='required' for client side validation
		$class = '';
		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		[%%IF INCLUDE_NAME%%]
		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';
		[%%ELSE INCLUDE_NAME%%]
		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->id.'_name" value="'.$value.'" />';
		[%%ENDIF INCLUDE_NAME%%]
		return implode("\n", $html);		
	}
}
