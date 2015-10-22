<?php
/**
 * @version 		$Id:$
 * @name			ModCreator (Release 1.0.0)
 * @author			 ()
 * @package			com_modcreator
 * @subpackage		com_modcreator.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjecticon.php 418 2014-10-22 14:42:36Z BrianWade $
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

class JHTMLItemIcon
{
	/**
	 * Display an create icon for the item.
	 *
	 * @param	object	$params		The item parameters
	 *
	 * @return	string	The HTML for the item create icon.
	 * 
	 */
	static function create($params)
	{
		$uri = JFactory::getURI();

		$url = 'index.php?option=com_modcreator&task=item.add&layout=edit&return='.base64_encode(urlencode($uri));
		
		if ($params->get('show_item_icons'))
		{
			$text = JHtml::_('image', 'com_modcreator/new.png', JText::_('COM_MODCREATOR_ITEMS_CREATE_ITEM'), NULL, true);
		}
		else
		{
			$text = JText::_('COM_MODCREATOR_ITEMS_CREATE_ITEM').'&#160;';
		}

		$button =  JHtml::_('link', JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('COM_MODCREATOR_ITEMS_CREATE_ITEM').'">'.$button.'</span>';
		return $output;
	}
	/**
	 * Display an edit icon for the item.
	 *
	 * This icon will not display in a popup window, nor if the item is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$item	The item in question.
	 * @param	object	$params		The item parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the item edit icon.
	 * 
	 */
	static function edit($item, $params, $attribs = array())
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

		// Ignore if the state is negative (trashed).
		if ($item->state < 0)
		{
			return;
		}

		JHtml::_('behavior.tooltip');

		// Show checked_out icon if the item is checked out by a different user
		if (property_exists($item, 'checked_out') AND 
			property_exists($item, 'checked_out_time') AND 
			$item->checked_out > 0 AND 
			$item->checked_out != $user->get('id'))
		{
			$check_out_user = JFactory::getUser($item->checked_out);
			$button = JHtml::_('image','com_modcreator/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$item->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_MODCREATOR_CHECKED_OUT_BY', $check_out_user->name).' <br /> '.$date;
			return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}

		$url	= 'index.php?option=com_modcreator&view=itemform&task=item.edit&layout=edit&id='.$item->id.'&return='.base64_encode(urlencode($uri));
		if ($params->get('show_item_icons'))
		{
					$icon	= $item->state ? 'com_modcreator/edit.png' : 'com_modcreator/edit_unpublished.png';
			$text	= JHtml::_('image',$icon, JText::_('COM_MODCREATOR_ITEMS_EDIT_ITEM'), NULL, true);
		}
		else
		{
			$text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_EDIT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
		}
		$overlib = '';
		
		if ($item->state == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date',$item->created);
		$creator = $item->created_by_name;


		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_MODCREATOR_CREATED_BY', htmlspecialchars($creator, ENT_COMPAT, 'UTF-8'));
		$button = JHtml::_('link',JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('COM_MODCREATOR_ITEMS_EDIT_ITEM').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
	/**
	 * Display an delete icon for the item.
	 *
	 * This icon will not display in a popup window, nor if the item is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$item	The item in question.
	 * @param	object	$params		The item parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the item edit icon.
	 * 
	 */
	static function delete($item, $params, $attribs = array())
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

		// Ignore if the state is negative (trashed).
		if ($item->state < 0)
		{
			return;
		}

		JHtml::_('behavior.tooltip');

		// Show checked_out icon if the item is checked out by a different user
		if (property_exists($item, 'checked_out') AND 
			property_exists($item, 'checked_out_time') AND 
			$item->checked_out > 0 AND 
			$item->checked_out != $user->get('id'))
		{
			$check_out_user = JFactory::getUser($item->checked_out);
			$button = JHtml::_('image','com_modcreator/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$item->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_MODCREATOR_CHECKED_OUT_BY', $check_out_user->name).' <br /> '.$date;
			return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}

		$url = 'index.php?option=com_modcreator&task=item.delete&cid='.$item->id.'&'.JSession::getFormToken().'=1'.'&return='.base64_encode(urlencode($uri));

		if ($params->get('show_item_icons'))
		{
			$icon	= 'com_modcreator/delete.png';
			$text	= JHtml::_('image',$icon, JText::_('COM_MODCREATOR_ITEMS_DELETE_ITEM'), NULL, true);
		}
		else
		{
			$text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JACTION_DELETE') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
		}
		$overlib = '';

		$date = JHtml::_('date',$item->created);
		$creator = $item->created_by_name;
		
		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_MODCREATOR_CREATED_BY', htmlspecialchars($creator, ENT_COMPAT, 'UTF-8'));
		$attribs['onclick'] = "if(confirm('".JText::_('COM_MODCREATOR_ITEMS_DELETE_ITEM_CONFIRM')."'))
								this.href='".JRoute::_($url)."';
								else this.href='#';";

		$button = JHtml::_('link',JRoute::_($url), $text,$attribs);
		$output = '<span class="hasTip" title="'.JText::_('COM_MODCREATOR_ITEMS_DELETE_ITEM').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
	/**
	 * Method to generate a link to the email item page for the given item
	 *
	 * @param   object     $item  The item information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the email item link
	 */		
	static function email($item, $params, $attribs = array())
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';	
		$uri	= JUri::getInstance();
		$base	= $uri->toString(array('scheme', 'host', 'port'));
		
		$layout = JRequest::getCmd('layout', 'default');
		
		$link	= $base.JRoute::_(ModcreatorHelperRoute::getItemRoute($item->slug,
									$item->language,
									$layout,
									$params->get('keep_item_itemid')) , false);

		$url	= 'index.php?option=com_mailto&tmpl=component&link='.base64_encode(urlencode($link));

		$window_params = 'width=400,height=350,menubar=yes,resizable=yes';

		if ($params->get('show_item_icons'))
		{
			$text = JHTML::_('image','com_modcreator/emailButton.png', JText::_('JGLOBAL_EMAIL'), NULL, true);
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
	 * Method to generate a popup link to print an item
	 *
	 * @param   object     $item  The item information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the popup link
	 */	
	
	static function print_popup($item, $params, $attribs = array())
	{
		$layout = JRequest::getCmd('layout', 'default');
	
		$link	= JRoute::_(ModcreatorHelperRoute::getItemRoute($item->slug,
									$item->language,
									$layout,
									$params->get('keep_item_itemid')) , false);
			
		if (strpos($link, '?') === false)
		{
			$link .= '?';
		}
		
		$link .= '&tmpl=component&print=1&layout=default&page='.@ $request->limitstart;

		$window_params = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_item_icons'))
		{
			$text = JHTML::_('image','com_modcreator/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
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
	 * Method to generate a link to print an item
	 *
	 * @param   object     $item  The item information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	static function print_screen($item, $params, $attribs = array())
	{
		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_item_icons') )
		{
				$text = JHtml::_('image', 'com_modcreator/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
		}
		else
		{
			$text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_PRINT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');
		}
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}

}
