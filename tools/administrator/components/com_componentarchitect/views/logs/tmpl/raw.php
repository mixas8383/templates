<?php
/**
 * @version			$Id: raw.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

//[%%START_CUSTOM_CODE%%]

defined('_JEXEC') or die('Restricted access');
// Get default values from Options
$params = JComponentHelper::getParams('com_componentarchitect');	

$default_logging_folder = $params->get('default_logging_folder', 'logs');

?>
<script language="javascript" type="text/javascript">
// Disable right-click
var isNS = (navigator.appName == "Netscape") ? 1 : 0;
if(navigator.appName == "Netscape") document.captureEvents(Event.MOUSEDOWNOREvent.MOUSEUP);
function mischandler()
{
 return false;
}
function mousehandler(e)
{
	var myevent = (isNS) ? e : event;
	var eventbutton = (isNS) ? myevent.which : myevent.button;
  if((eventbutton==2)OR(eventbutton==3)) return false;
}
document.oncontextmenu = mischandler;
document.onmousedown = mousehandler;
document.onmouseup = mousehandler;

// Disable CTRL-C, CTRL-V
function onKeyDown()
{
	return false;
}

document.onkeydown = onKeyDown;
</script>
<?php

// -- Get the log's file name
$tag = JRequest::getCmd('tag','null');
$log_path = JPATH_ROOT.'/'.$default_logging_folder.'/';
$log_name = $log_path.$tag;

// Load JFile class
jimport('joomla.filesystem.file');

@ob_end_clean();

if(!JFile::exists($log_name))
{
	// Oops! The log doesn't exist!
	echo '<p>'.JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_ERROR_FILE_DOES_NOT_EXIST').'</p>';
	return;
}
else
{
	// Allright, let's load and render it
	$fp = fopen( $log_name, "rt" );
	if ($fp === FALSE)
	{
		// Oops! The log isn't readable?!
		echo '<p>'.JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_ERROR_UNREADABLE_FILE').'</p>';
		return;
	}

	while( !feof($fp) )
	{
		$line = fgets( $fp );
		if(!$line) return;
		// Don't output the Joomla! log program line
		if (JString::strpos($line, '#Software:') === False)
		{
			echo "<span style=\"font-size: small;\">".$line. "</span><br/>\n";
		}
		unset( $line );
	}
}

@ob_start();
//[%%END_CUSTOM_CODE%%]