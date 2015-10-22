<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].site
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @version			$Id: compobjecticon.php 418 2014-10-22 14:42:36Z BrianWade $
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

class JHTML[%%CompObject%%]Icon
{
	/**
	 * Display an create icon for the [%%compobject%%].
	 *
	 * @param	object	$params		The [%%compobject%%] parameters
	 *
	 * @return	string	The HTML for the [%%compobject%%] create icon.
	 * 
	 */
	static function create($params)
	{
		$uri = JFactory::getURI();

		$url = 'index.php?option=[%%com_architectcomp%%]&task=[%%compobject%%].add&layout=edit&return='.base64_encode(urlencode($uri));
		
		if ($params->get('show_[%%compobject%%]_icons'))
		{
			$text = JHtml::_('image', '[%%com_architectcomp%%]/new.png', JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_CREATE_ITEM'), NULL, true);
		}
		else
		{
			$text = JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_CREATE_ITEM').'&#160;';
		}

		$button =  JHtml::_('link', JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_CREATE_ITEM').'">'.$button.'</span>';
		return $output;
	}
	/**
	 * Display an edit icon for the [%%compobject%%].
	 *
	 * This icon will not display in a popup window, nor if the [%%compobject_name%%] is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$[%%compobject_code_name%%]	The [%%compobject%%] in question.
	 * @param	object	$params		The [%%compobject%%] parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the [%%compobject%%] edit icon.
	 * 
	 */
	static function edit($[%%compobject_code_name%%], $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$user_id	= $user->get('id');
		$uri	= JFactory::getURI();

		// Ignore if in a popup window.
		if ($params AND $params->get('popup'))
		{
			return;
		}

		[%%IF INCLUDE_STATUS%%]
		// Ignore if the state is negative (trashed).
		if ($[%%compobject_code_name%%]->state < 0)
		{
			return;
		}
		[%%ENDIF INCLUDE_STATUS%%]

		JHtml::_('behavior.tooltip');

		[%%IF INCLUDE_CHECKOUT%%]
		// Show checked_out icon if the [%%compobject_name%%] is checked out by a different user
		if (property_exists($[%%compobject_code_name%%], 'checked_out') AND 
			property_exists($[%%compobject_code_name%%], 'checked_out_time') AND 
			$[%%compobject_code_name%%]->checked_out > 0 AND 
			$[%%compobject_code_name%%]->checked_out != $user->get('id'))
		{
			$check_out_user = JFactory::getUser($[%%compobject_code_name%%]->checked_out);
			$button = JHtml::_('image','[%%com_architectcomp%%]/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$[%%compobject_code_name%%]->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CHECKED_OUT_BY', $check_out_user->name).' <br /> '.$date;
			return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}
		[%%ENDIF INCLUDE_CHECKOUT%%]

		$url	= 'index.php?option=[%%com_architectcomp%%]&view=[%%compobject%%]form&task=[%%compobject%%].edit&layout=edit&id='.$[%%compobject_code_name%%]->id.'&return='.base64_encode(urlencode($uri));
		if ($params->get('show_[%%compobject%%]_icons'))
		{
			[%%IF INCLUDE_STATUS%%]
					$icon	= $[%%compobject_code_name%%]->state ? '[%%com_architectcomp%%]/edit.png' : '[%%com_architectcomp%%]/edit_unpublished.png';
			[%%ELSE INCLUDE_STATUS%%]
					$icon	=  '[%%com_architectcomp%%]/edit.png';
			[%%ENDIF INCLUDE_STATUS%%]
			$text	= JHtml::_('image',$icon, JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_EDIT_ITEM'), NULL, true);
		}
		else
		{
			$text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_EDIT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
		}
		$overlib = '';
		
		[%%IF INCLUDE_STATUS%%]
		if ($[%%compobject_code_name%%]->state == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}
		[%%ENDIF INCLUDE_STATUS%%]

		[%%IF INCLUDE_CREATED%%]
		$date = JHtml::_('date',$[%%compobject_code_name%%]->created);
		$creator = $[%%compobject_code_name%%]->created_by_name;


		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', htmlspecialchars($creator, ENT_COMPAT, 'UTF-8'));
		[%%ENDIF INCLUDE_CREATED%%]
		$button = JHtml::_('link',JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_EDIT_ITEM').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
	/**
	 * Display an delete icon for the [%%compobject%%].
	 *
	 * This icon will not display in a popup window, nor if the [%%compobject_name%%] is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$[%%compobject_code_name%%]	The [%%compobject%%] in question.
	 * @param	object	$params		The [%%compobject%%] parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the [%%compobject%%] edit icon.
	 * 
	 */
	static function delete($[%%compobject_code_name%%], $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$user_id	= $user->get('id');
		$uri	= JFactory::getURI();

		// Ignore if in a popup window.
		if ($params AND $params->get('popup'))
		{
			return;
		}

		[%%IF INCLUDE_STATUS%%]
		// Ignore if the state is negative (trashed).
		if ($[%%compobject_code_name%%]->state < 0)
		{
			return;
		}
		[%%ENDIF INCLUDE_STATUS%%]

		JHtml::_('behavior.tooltip');

		[%%IF INCLUDE_CHECKOUT%%]
		// Show checked_out icon if the [%%compobject_name%%] is checked out by a different user
		if (property_exists($[%%compobject_code_name%%], 'checked_out') AND 
			property_exists($[%%compobject_code_name%%], 'checked_out_time') AND 
			$[%%compobject_code_name%%]->checked_out > 0 AND 
			$[%%compobject_code_name%%]->checked_out != $user->get('id'))
		{
			$check_out_user = JFactory::getUser($[%%compobject_code_name%%]->checked_out);
			$button = JHtml::_('image','[%%com_architectcomp%%]/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$[%%compobject_code_name%%]->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CHECKED_OUT_BY', $check_out_user->name).' <br /> '.$date;
			return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}
		[%%ENDIF INCLUDE_CHECKOUT%%]

		$url = 'index.php?option=[%%com_architectcomp%%]&task=[%%compobject%%].delete&cid='.$[%%compobject_code_name%%]->id.'&'.JSession::getFormToken().'=1'.'&return='.base64_encode(urlencode($uri));

		if ($params->get('show_[%%compobject%%]_icons'))
		{
			$icon	= '[%%com_architectcomp%%]/delete.png';
			$text	= JHtml::_('image',$icon, JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_DELETE_ITEM'), NULL, true);
		}
		else
		{
			$text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JACTION_DELETE') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
		}
		$overlib = '';

		[%%IF INCLUDE_CREATED%%]
		$date = JHtml::_('date',$[%%compobject_code_name%%]->created);
		$creator = $[%%compobject_code_name%%]->created_by_name;
		
		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('[%%COM_ARCHITECTCOMP%%]_CREATED_BY', htmlspecialchars($creator, ENT_COMPAT, 'UTF-8'));
		[%%ENDIF INCLUDE_CREATED%%]
		$attribs['onclick'] = "if(confirm('".JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_DELETE_ITEM_CONFIRM')."'))
								this.href='".JRoute::_($url)."';
								else this.href='#';";

		$button = JHtml::_('link',JRoute::_($url), $text,$attribs);
		$output = '<span class="hasTip" title="'.JText::_('[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_DELETE_ITEM').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
	/**
	 * Method to generate a link to the email item page for the given [%%compobject%%]
	 *
	 * @param   object     $[%%compobject_code_name%%]  The [%%compobject%%] information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the email item link
	 */		
	static function email($[%%compobject_code_name%%], $params, $attribs = array())
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';	
		$uri	= JUri::getInstance();
		$base	= $uri->toString(array('scheme', 'host', 'port'));
		
		$layout = JRequest::getCmd('layout', 'default');
		
		[%%IF GENERATE_CATEGORIES%%]		 
			[%%IF INCLUDE_LANGUAGE%%]
		$link	= $base.JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$[%%compobject_code_name%%]->catid,
									$[%%compobject_code_name%%]->language,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ELSE INCLUDE_LANGUAGE%%]
		$link	= $base.JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$[%%compobject_code_name%%]->catid,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ELSE GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_LANGUAGE%%]
		$link	= $base.JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$[%%compobject_code_name%%]->language,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ELSE INCLUDE_LANGUAGE%%]
		$link	= $base.JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ENDIF INCLUDE_LANGUAGE%%]	
		[%%ENDIF GENERATE_CATEGORIES%%]			

		$url	= 'index.php?option=com_mailto&tmpl=component&link='.base64_encode(urlencode($link));

		$window_params = 'width=400,height=350,menubar=yes,resizable=yes';

		if ($params->get('show_[%%compobject%%]_icons'))
		{
			$text = JHTML::_('image','[%%com_architectcomp%%]/emailButton.png', JText::_('JGLOBAL_EMAIL'), NULL, true);
		}
		else
		{
			$text = '&#160;'.JText::_('JGLOBAL_EMAIL');
		}

		$attribs['title']	= JText::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','".$window_params."'); return false;";

		$output = JHTML::_('link',JRoute::_($url), $text, $attribs);
		return $output;
	}	
	/**
	 * Method to generate a popup link to print an [%%compobject%%]
	 *
	 * @param   object     $[%%compobject_code_name%%]  The [%%compobject%%] information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the popup link
	 */	
	
	static function print_popup($[%%compobject_code_name%%], $params, $attribs = array())
	{
		$layout = JRequest::getCmd('layout', 'default');
	
		[%%IF GENERATE_CATEGORIES%%]		 
			[%%IF INCLUDE_LANGUAGE%%]
		$link	= JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$[%%compobject_code_name%%]->catid,
									$[%%compobject_code_name%%]->language,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ELSE INCLUDE_LANGUAGE%%]
		$link	= JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$[%%compobject_code_name%%]->catid,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ENDIF INCLUDE_LANGUAGE%%]
		[%%ELSE GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_LANGUAGE%%]
		$link	= JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$[%%compobject_code_name%%]->language,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ELSE INCLUDE_LANGUAGE%%]
		$link	= JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($[%%compobject_code_name%%]->slug,
									$layout,
									$params->get('keep_[%%compobject%%]_itemid')) , false);
			[%%ENDIF INCLUDE_LANGUAGE%%]	
		[%%ENDIF GENERATE_CATEGORIES%%]	
			
		if (strpos($link, '?') === false)
		{
			$link .= '?';
		}
		
		$link .= '&tmpl=component&print=1&layout=default&page='.@ $request->limitstart;

		$window_params = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_[%%compobject%%]_icons'))
		{
			$text = JHTML::_('image','[%%com_architectcomp%%]/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
		}
		else
		{
			$text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_PRINT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
		}

		$attribs['title']	= JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','".$window_params."'); return false;";
		$attribs['rel']		= 'nofollow';

		return JHTML::_('link',JRoute::_($link), $text, $attribs);
	}
	/**
	 * Method to generate a link to print an [%%compobject%%]
	 *
	 * @param   object     $[%%compobject_code_name%%]  The [%%compobject%%] information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	static function print_screen($[%%compobject_code_name%%], $params, $attribs = array())
	{
		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_[%%compobject%%]_icons') )
		{
				$text = JHtml::_('image', '[%%com_architectcomp%%]/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
		}
		else
		{
			$text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_PRINT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
		}
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}

}
