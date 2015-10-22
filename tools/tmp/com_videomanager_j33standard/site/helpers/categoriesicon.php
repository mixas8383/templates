<?php
/**
 * @version 		$Id:$
 * @name			Videomanager (Release 1.0.0)
 * @author			 ()
 * @package			com_videomanager
 * @subpackage		com_videomanager.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjecticon.php 408 2014-10-19 18:31:00Z BrianWade $
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

class JHTMLCategoriesIcon
{
	/**
	 * Display an create icon for the categories.
	 *
	 * @param	json	   $params	  The categories parameters
	 * @param   array      $attribs   Optional attributes for the link
	 * @param   boolean    $legacy    True to use legacy images, false to use icomoon based graphic
	 *
	 * @return	string	The HTML for the categories create icon.
	 * 
	 */
	public static function create($params, $attribs = array(), $legacy = false)
	{
		JHtml::_('bootstrap.tooltip');
	
		$uri = JUri::getInstance();

		$url = 'index.php?option=com_videomanager&task=categories.add&layout=edit&return='.base64_encode($uri);
		
		if ($params->get('show_categories_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'com_videomanager/new.png', JText::_('COM_VIDEOMANAGER_CATEGORIESES_CREATE_ITEM'), NULL, true);
			}
			else
			{
				$text = '<span class="icon-plus"></span>&#160;' . JText::_('COM_VIDEOMANAGER_CATEGORIESES_CREATE_ITEM') . '&#160;';
			}		
		}
		else
		{
			$text = JText::_('JNEW') . '&#160;';
		}

		// Add the button classes to the attribs array
		if (isset($attribs['class']))
		{
			$attribs['class'] = $attribs['class'] . ' btn btn-primary';
		}
		else
		{
			$attribs['class'] = 'btn btn-primary';
		}
		
		$button =  JHtml::_('link', JRoute::_($url), $text, $attribs);

		$output = '<span class="hasTooltip tip" title="'.JHtml::tooltipText('COM_VIDEOMANAGER_CATEGORIESES_CREATE_ITEM').'">'.$button.'</span>';
		return $output;
	}
	/**
	 * Display an edit icon for the categories.
	 *
	 * This icon will not display in a popup window, nor if the categories is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$categories	The categories in question.
	 * @param	object	$params		The categories parameters
	 * @param	array	$attribs	Not used??
	 * @param   boolean $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return	string	The HTML for the categories edit icon.
	 * 
	 */
	public static function edit($categories, $params, $attribs = array(), $legacy = false)
	{
		
		$user	= JFactory::getUser();
		$user_id	= $user->get('id');
		$uri	= JUri::getInstance();

		// Ignore if in a popup window.
		if ($params AND $params->get('popup'))
		{
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($categories->state < 0)
		{
			return;
		}

		JHtml::_('bootstrap.tooltip');

		// Show checked_out icon if the categories is checked out by a different user
		if (property_exists($categories, 'checked_out') AND 
			property_exists($categories, 'checked_out_time') AND 
			$categories->checked_out > 0 AND 
			$categories->checked_out != $user->get('id'))
		{
			$check_out_user = JFactory::getUser($categories->checked_out);
			$button = JHtml::_('image','com_videomanager/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$categories->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_VIDEOMANAGER_CHECKED_OUT_BY', $check_out_user->name).' <br /> '.$date;
			return '<span class="hasTooltip" title="' . JHtml::tooltipText($tooltip. '', 0) . '">' . $button . '</span>';
		}

		$url	= 'index.php?option=com_videomanager&task=categories.edit&layout=edit&id='.$categories->id.'&return='.base64_encode($uri);

		$overlib = '';
		
		if ($categories->state == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date',$categories->created);
		$created_by = $categories->created_by_name;


		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_VIDEOMANAGER_CREATED_BY', htmlspecialchars($created_by, ENT_COMPAT, 'UTF-8'));

		if ($params->get('show_categories_icons'))
		{
			if ($legacy)
			{
				$icon	= $categories->state ? 'com_videomanager/edit.png' : 'com_videomanager/edit_unpublished.png';
				if (strtotime($categories->publish_up) > strtotime(JFactory::getDate())
					OR ((strtotime($categories->publish_down) < strtotime(JFactory::getDate())) 
					AND $categories->publish_down != '0000-00-00 00:00:00'))
				{
					$icon = 'com_videomanager/edit_unpublished.png';
				}
				$text	= JHtml::_('image','system/' .$icon, JText::_('COM_VIDEOMANAGER_CATEGORIESES_EDIT_ITEM'), NULL, true);
			}
			else
			{
				$icon = $categories->state ? 'edit' : 'eye-close';
				if (strtotime($categories->publish_up) > strtotime(JFactory::getDate())
					OR ((strtotime($categories->publish_down) < strtotime(JFactory::getDate())) 
					AND $categories->publish_down != '0000-00-00 00:00:00'))
				{
					$icon = 'eye-close';
				}
				$text = '<span class="hasTooltip icon-' . $icon . ' tip" title="' . JHtml::tooltipText(JText::_('COM_VIDEOMANAGER_CATEGORIESES_EDIT_ITEM'), $overlib, 0) . '"></span>&#160;' . JText::_('JGLOBAL_EDIT') . '&#160;';
			}			
		}
		else
		{
			$text = '<span class="hasTooltip tip" title="' . JHtml::tooltipText(JText::_('COM_VIDEOMANAGER_CATEGORIESES_EDIT_ITEM'), $overlib, 0) . '"></span>' . JText::_('JGLOBAL_EDIT') . '</span>';
		}
				
		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

		return $output;
	}
	/**
	 * Display an delete icon for the categories.
	 *
	 * This icon will not display in a popup window, nor if the categories is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$categories	The categories in question.
	 * @param	object	$params		The categories parameters
	 * @param	array	$attribs	Not used??
	 * @param   boolean    $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return	string	The HTML for the categories edit icon.
	 * 
	 */
	public static function delete($categories, $params, $attribs = array(), $legacy = false)
	{
		
		$user	= JFactory::getUser();
		$user_id	= $user->get('id');
		$uri	= JUri::getInstance();

		// Ignore if in a popup window.
		if ($params AND $params->get('popup'))
		{
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($categories->state < 0)
		{
			return;
		}

		JHtml::_('behavior.tooltip');

		// Show checked_out icon if the categories is checked out by a different user
		if (property_exists($categories, 'checked_out') AND 
			property_exists($categories, 'checked_out_time') AND 
			$categories->checked_out > 0 AND 
			$categories->checked_out != $user->get('id'))
		{
			$check_out_user = JFactory::getUser($categories->checked_out);
			$button = JHtml::_('image','com_videomanager/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$categories->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_VIDEOMANAGER_CHECKED_OUT_BY', $check_out_user->name).' <br /> '.$date;
			return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}

		$url = 'index.php?option=com_videomanager&task=categories.delete&id='.$categories->id.'&'.JSession::getFormToken().'=1'.'&return='.base64_encode($uri);

		$overlib = '';

		$date = JHtml::_('date',$categories->created);
		$created_by = $categories->created_by_name;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_VIDEOMANAGER_CREATED_BY', htmlspecialchars($created_by, ENT_COMPAT, 'UTF-8'));
		$attribs['onclick'] = "if(confirm('".JText::_('COM_VIDEOMANAGER_CATEGORIESES_DELETE_ITEM_CONFIRM')."'))
								this.href='".JRoute::_($url)."';
								else this.href='#';";


		if ($params->get('show_categories_icons'))
		{		
			if ($legacy)
			{
				$icon	= 'com_videomanager/delete.png';
				$text = JHtml::_('image', 'system/' . $icon, JText::_('COM_VIDEOMANAGER_CATEGORIESES_DELETE_ITEM'), null, true);
			}
			else
			{
				$text = '<span class="hasTooltip icon-delete tip" title="' . JHtml::tooltipText(JText::_('COM_VIDEOMANAGER_CATEGORIESES_DELETE_ITEM'), $overlib, 0) . '"></span>&#160;' . JText::_('JACTION_DELETE') . '&#160;';
			}
		}
		else
		{
			$text = '<span class="hasTooltip tip" title="' . JHtml::tooltipText(JText::_('COM_VIDEOMANAGER_CATEGORIESES_DELETE_ITEM'), $overlib, 0) . '">' . JText::_('JACTION_DELETE') . '</span>';
		}		
		
		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}
	/**
	 * Method to generate a link to the email item page for the given categories
	 *
	 * @param   object     $categories  The categories information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 * @param   boolean    $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the email item link
	 */	
	public static function email($categories, $params, $attribs = array(), $legacy = false)
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';	
		$uri	= JUri::getInstance();
		$base	= $uri->toString(array('scheme', 'host', 'port'));
		$app	= JFactory::getApplication();
		
		$layout = $app->input->getString('layout', 'default');
		
		$link	= $base.JRoute::_(VideomanagerHelperRoute::getCategoriesRoute($categories->slug,
									$categories->catid,
									$categories->language,
									$layout,
									$params->get('keep_categories_itemid')) , false);

		$url	= 'index.php?option=com_mailto&tmpl=component&link='.base64_encode($link);

		$window_params = 'width=400,height=350,menubar=yes,resizable=yes';

		if ($params->get('show_categories_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'com_videomanager/emailButton.png', JText::_('JGLOBAL_EMAIL'), null, true);
			}
			else
			{
				$text = '<span class="icon-envelope"></span> ' . JText::_('JGLOBAL_EMAIL');
			}			
		}
		else
		{
			$text = JText::_('JGLOBAL_EMAIL');
		}

		$attribs['title']	= JText::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','".$window_params."'); return false;";

		$output = JHtml::_('link',JRoute::_($url), $text, $attribs);
		return $output;
	}	
	/**
	 * Method to generate a popup link to print an categories
	 *
	 * @param   object     $categories  The categories information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 * @param   boolean    $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */	
	public static function print_popup($categories, $params, $attribs = array(), $legacy = false)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$request = $input->request;
		
		$layout = $app->input->getString('layout', 'default');
	
		$link	= JRoute::_(VideomanagerHelperRoute::getCategoriesRoute($categories->slug,
									$categories->catid,
									$categories->language,
									$layout,
									$params->get('keep_categories_itemid')) , false);
		
		if (strpos($link, '?') === false)
		{
			$link .= '?';
		}
		
		$link .= '&tmpl=component&print=1&layout=default&page='.@ $request->limitstart;

		$window_params = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_categories_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'com_videomanager/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
			}
			else
			{
				$text = '<span class="icon-print"></span>&#160;' . JText::_('JGLOBAL_PRINT') . '&#160;';
			}			
		}
		else
		{
			$text =  JText::_('JGLOBAL_PRINT');
		}

		$attribs['title']	= JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','".$window_params."'); return false;";
		$attribs['rel']		= 'nofollow';

		return JHtml::_('link',JRoute::_($link), $text, $attribs);
	}
	/**
	 * Method to generate a link to print an categories
	 *
	 * @param   object     $categories  The categories information
	 * @param   JRegistry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 * @param   boolean    $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	public static function print_screen($categories, $params, $attribs = array(), $legacy = false)
	{
		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_categories_icons') )
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'com_videomanager/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
			}
			else
			{
				$text = '<span class="icon-print"></span>&#160;' . JText::_('JGLOBAL_PRINT') . '&#160;';
			}			
		}
		else
		{
			$text =  JText::_('JGLOBAL_PRINT');
		}
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}

}
