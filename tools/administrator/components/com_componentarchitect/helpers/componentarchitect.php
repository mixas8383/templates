<?php
/**
 * @version 		$Id: componentarchitect.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (http://www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: architectcomp.php 806 2013-12-24 13:24:16Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
 * @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @Joomlacopyright Copyright (c)2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @CAlicense		GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
/**
 * Architectcomp_name component helper.
 *
 */
class ComponentArchitectHelper
{
	protected $category_component;
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * 
	 */
	public function __construct()
	{

	}	
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * 
	 */
	public static function addSubmenu($view_name)
	{
		$config = JComponentHelper::getParams(JText::_('COM_COMPONENTARCHITECT_FIELD_CATEGORY_COMPONENT_DEFAULT'));
		$category_component = $config->get('category_component', JText::_('COM_COMPONENTARCHITECT_FIELD_CATEGORY_COMPONENT_DEFAULT'));	
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$active = $view_name == 'components'? true : false;
			JHtmlSidebar::addEntry(
				JText::_('COM_COMPONENTARCHITECT_COMPONENTS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=components',
				$view_name == 'components',
				$active
			);
		
			$active = $view_name == 'componentobjects'? true : false;
			JHtmlSidebar::addEntry(
				JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=componentobjects',
				$view_name == 'componentobjects',
				$active
			);
		
			$active = $view_name == 'fieldsets'? true : false;
			JHtmlSidebar::addEntry(
				JText::_('COM_COMPONENTARCHITECT_FIELDSETS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=fieldsets',
				$view_name == 'fieldsets',
				$active
			);
		
			$active = $view_name == 'fields'? true : false;
			JHtmlSidebar::addEntry(
				JText::_('COM_COMPONENTARCHITECT_FIELDS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=fields',
				$view_name == 'fields',
				$active
			);
		
			$active = $view_name == 'fieldtypes'? true : false;
			JHtmlSidebar::addEntry(
				JText::_('COM_COMPONENTARCHITECT_FIELDTYPES_SUBMENU'),
				'index.php?option=com_componentarchitect&view=fieldtypes',
				$view_name == 'fieldtypes',
				$active
			);
		
			$active = $view_name == 'codetemplates'? true : false;
			JHtmlSidebar::addEntry(
				JText::_('COM_COMPONENTARCHITECT_CODETEMPLATES_SUBMENU'),
				'index.php?option=com_componentarchitect&view=codetemplates',
				$view_name == 'codetemplates',
				$active
			);
		
			if ($category_component != JText::_('COM_COMPONENTARCHITECT_FIELD_CATEGORY_COMPONENT_NO_CATEGORIES'))
			{
				$active = $view_name == 'categories'? true : false;
				JHtmlSidebar::addEntry(
					JText::_('COM_COMPONENTARCHITECT_CATEGORIES_SUBMENU'),
					'index.php?option=com_categories&extension='.$category_component,
					$view_name == 'categories',
					$active
				);
			}

			//[%%START_CUSTOM_CODE%%]
			$active = $view_name == 'logs'? true : false;
			JHtmlSidebar::addEntry(
				JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=logs',
				$view_name == 'logs',
				$active
			);
			//[%%END_CUSTOM_CODE%%]					
		}
		else
		{	
			JSubMenuHelper::addEntry(
				JText::_('COM_COMPONENTARCHITECT_COMPONENTS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=components',
				$view_name == 'components'
			);
		
			JSubMenuHelper::addEntry(
				JText::_('COM_COMPONENTARCHITECT_COMPONENTOBJECTS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=componentobjects',
				$view_name == 'componentobjects'
			);
		
			JSubMenuHelper::addEntry(
				JText::_('COM_COMPONENTARCHITECT_FIELDSETS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=fieldsets',
				$view_name == 'fieldsets'
			);
		
			JSubMenuHelper::addEntry(
				JText::_('COM_COMPONENTARCHITECT_FIELDS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=fields',
				$view_name == 'fields'
			);
		
			JSubMenuHelper::addEntry(
				JText::_('COM_COMPONENTARCHITECT_FIELDTYPES_SUBMENU'),
				'index.php?option=com_componentarchitect&view=fieldtypes',
				$view_name == 'fieldtypes'
			);
		
			JSubMenuHelper::addEntry(
				JText::_('COM_COMPONENTARCHITECT_CODETEMPLATES_SUBMENU'),
				'index.php?option=com_componentarchitect&view=codetemplates',
				$view_name == 'codetemplates'
			);
		
			if ($category_component != JText::_('COM_COMPONENTARCHITECT_FIELD_CATEGORY_COMPONENT_NO_CATEGORIES'))
			{	
				JSubMenuHelper::addEntry(
					JText::_('COM_COMPONENTARCHITECT_CATEGORIES_SUBMENU'),
					'index.php?option=com_categories&extension='.$category_component,
					$view_name == 'categories'
				);
			}

			//[%%START_CUSTOM_CODE%%]
			JSubMenuHelper::addEntry(
				JText::_('COM_COMPONENTARCHITECT_VIEW_LOGS_SUBMENU'),
				'index.php?option=com_componentarchitect&view=logs',
				$view_name == 'logs'
			);					
			//[%%END_CUSTOM_CODE%%]
		}
		if ($view_name=='categories')
		{
			JToolbarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE',JText::_($category_component)),
				$category_component.'-categories');
		}	
	}
	/**
	 * Generate a thumbnail image
	 * Uses the PHP GD library which must be installed for this to work
	 *
	 * @param	string		Source image filename and path
	 * @param	string		Filename and path for the thumbnail image to be created
	 * @param	int			Width of the Thumbnail
	 * @param	int			Height of the Thumbnail
	 *
	 * @return	boolean		True or False
	 * 
	 */	
	public static function createThumb($source_image, $tn_image, $new_width, $new_height)
	{
		// Modify path if in Admin
		if (JFactory::getApplication()->isAdmin())
		{
			// If path not real-path then modify
			if (strpos($source_image, ':') === false AND $source_image[0] != "/")
			{
				$source_image =  '../'.$source_image;
			}

			if (strpos($tn_image, ':') === false AND $tn_image[0] != "/")
			{
				$tn_image = '../'.$tn_image;
			}
		}
		
		// IMAGETYPE_XXXX failed to work consistently so have set up the values in variables
		$imgtype_gif = 1;
		$imgtype_jpg = 2;
		$imgtype_png = 3;
		$imgtype_wbmp = 15;
		
		if (!JFile::exists($source_image))
		{
			return false;
		}
		list($src_width, $src_height, $src_type) = getimagesize($source_image);
		
		// Create temporary copy of the source image
		switch ($src_type)
		{	
			case $imgtype_jpg:
				if (!$src_img = imagecreatefromjpeg($source_image))
				{
					return false;
				}
				break;
			case $imgtype_png:		
				if (!$src_img = imagecreatefrompng($source_image))
				{
					return false;
				}
				break;
			case $imgtype_gif:		
				if (!$src_img = imagecreatefromgif($source_image))
				{
					return false;
				}
				break;
			case $imgtype_wbmp:		
				if (!$src_img = imagecreatefromwbmp($source_image))
				{
					return false;
				}
				break;
			default:
				return false;
				break;								
		}
		if ($new_width == 0 and $new_height == 0)
		{
			// No change in the image size
			$tn_width = $new_width;
			$tn_height = $new_height;
			$dst_img = $src_img;
		}
		else
		{
			// Calculate proportional size
			if ($src_width > $src_height)
			{
				if ($new_width == 0)
				{
					$tn_width = (int) $src_width * ($new_height/$src_height);
				}
				else
				{
					$tn_width = $new_width;
				}
				if ($new_height == 0)
				{
					$tn_height = (int) $src_height*($new_width/$src_width);
				}
				else
				{
					$tn_height = (int) $src_height*($new_height/$src_width);
				}
			}
			if ($src_width < $src_height)
			{
				if ($new_width == 0)
				{
					$tn_width = (int) $src_width * ($new_height/$src_height);
				}
				else
				{
					$tn_width = (int) $src_width*($new_width/$src_height);
				}
				if ($new_height == 0)
				{
					$tn_height = (int) $src_height*($new_width/$src_width);
				}
				else
				{			
					$tn_height = $new_height;
				}
			}
			
			if ($src_width == $src_height)
			{
				if ($new_width == 0)
				{
					$tn_width = (int) $src_width * ($new_height/$src_height);
				}
				else
				{		
					$tn_width = $new_width;
				}
				if ($new_height == 0)
				{
					$tn_height = (int) $src_height*($new_width/$src_width);
				}
				else
				{			
					$tn_height = $new_height;
				}
			}
			$result = true;
			// Produce thumbnail copy of source image
			$dst_img = ImageCreateTrueColor($tn_width, $tn_height);

			// Before resampling ensure transparency set properly for gif and png files
			switch ($src_type)
			{	
				case $imgtype_png:
					// integer representation of the color black (rgb: 0,0,0)
					$background = imagecolorallocate($dst_img, 0, 0, 0);
					// removing the black from the placeholder
					imagecolortransparent($dst_img, $background);
					// turning off alpha blending (to ensure alpha channel information
					// is preserved, rather than removed (blending with the rest of the
					// image in the form of black))
					imagealphablending($dst_img, false);
					// turning on alpha channel information saving (to ensure the full range
					// of transparency is preserved)
					imagesavealpha($dst_img, true);
					break;					
				case $imgtype_gif:
					// integer representation of the color black (rgb: 0,0,0)
					$background = imagecolorallocate($dst_img, 0, 0, 0);
					// removing the black from the placeholder
					imagecolortransparent($dst_img, $background);					
					break;				
			}		
			
			imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $tn_width, $tn_height, $src_width, $src_height);
		}
		switch ($src_type)
		{	
			case $imgtype_jpg:
				if (!imagejpeg($dst_img,$tn_image))
				{
					$result = false;
				}	
				break;
			case $imgtype_png:
				if (!imagepng($dst_img,$tn_image))
				{
					$result = false;
				}
				break;
			case $imgtype_gif:
				if (!imagegif($dst_img,$tn_image))
				{
					$result = false;
				}	
				break;
			case $imgtype_wbmp:
				if (!imagewbmp($dst_img,$tn_image))
				{
					$result = false;
				}	
				break;
			
		}		

		imagedestroy($dst_img); 
		imagedestroy($src_img);
		
		return $result; 
	}
	/*	
	 * @param	&file		Copy of the $_FILE entry
	 * @param	string		The label for field name where the upload file is held - for use in error messages
	 * @param	string		The output field name.  If blank the file name of the uploaded file is used
	 * @param	integer		New Image width (if file an image file)
	 * @param	integer		New Image height (if file an image file)
	 *
	 * @return	JObject
	 * 
	 */
	public static function saveUpload(&$file, $field_name = '', $output_name = '', $img_width = 0, $img_height = 0)
	{
		// Make sure the file name is valid value
		$file['name'] = JFile::makeSafe($file['name']);					
	
		if ($field_name == '')
		{
			$field_name = $file['name'];
		}		
		// Get config global preferences for upload
		$config = JComponentHelper::getParams('com_componentarchitect');
		
		$default_mime_types = explode(',',str_replace(' ','',$config->get('default_allowed_mime_types', '*')));

		$default_file_types = explode(',',str_replace(' ','',$config->get('default_allowed_file_types', 'txt,csv')));
		$default_image_types = explode(',',str_replace(' ','',$config->get('default_allowed_image_types', 'gif,png,jpg')));
		// Upload file type is mime based so uses jpeg not jpg, adjust defaults to allow for this
		if (in_array('jpg', $default_image_types) AND !in_array('jpeg', $default_image_types))
		{
			$default_image_types[] = 'jpeg';
		}
		
		$default_media_types = explode(',',str_replace(' ','',$config->get('default_allowed_media_types', 'mp3,mp4,avi')));

		$default_max_size = self::convert_max_file_size($config->get('default_max_upload_size', '2mb'));
		
		// Get posted file values
		$file_name =  $file['name'];
		
		$file_name_parts = explode('.', $file_name);
		$file_type = $file_name_parts[count($file_name_parts)-1];
		$file_mime_type = $file['type'];
		$file_size = $file['size'];
		$file_path = $file['tmp_name'];
		$file_error = $file['error'];
		
		if ($output_name != '')
		{
			$output_name_parts = explode('.', $output_name);
			if (count($output_name_parts) > 1)
			{
				$output_file_type = $output_name_parts[count($output_name_parts)-1];
				
				if ($file_type != $output_file_type AND 
					str_replace('jpeg','jpg',$file_type) != str_replace('jpeg','jpg',$output_file_type) AND 
					str_replace('mpeg','mpg',$file_type) != str_replace('mpeg','mpg',$output_file_type))
				{
					// delete temp file and return error	
					if (JFile::exists($file_path))
					{
						JFile::delete($file_path);
					}			
					return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_OUTPUT_FILE_TYPE_MISMATCH', $field_name, $output_name, $file_type);					
				}
			}
			else
			{
				$output_name = $output_name.'.'.$file_type;
			}
		}
		else
		{
			$output_name = uniqid(str_replace('.'.$file_type,'', $file_name).'_').'.'.$file_type;
		}	
		
		switch ($file_error)
		{
			case 0:
				break;
			case 1:
				return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_UPLOAD_1', $field_name);
				break;
			case 2;
				return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_UPLOAD_2', $field_name);
				break;
			case 4;
				// No file specified for upload
				return false;
				break;						
			default:
				// delete temp file and return error	
				if (JFile::exists($file_path))
				{
					JFile::delete($file_path);
				}			
				return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_UPLOAD_GENERAL', $field_name);
				break;
		}
		
		// Check file
		if (!is_uploaded_file(realpath($file_path)))
		{
			return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_FILE_NOT_UPLOADED_FILE', $field_name, $file_name);
		}
		
		// Double check file less than maximum file size allowed
		if ($file_size > $default_max_size)
		{
			// delete temp file and return error	
			if (JFile::exists($file_path))
			{
				JFile::delete($file_path);
			}			
			return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_UPLOAD_2', $field_name);		
		}
		
		// If required (i.e. mime types set in config) then check the file matches the actual mime type of the file
		// Could check the mime type from then post but this is often incorrect
		$actual_mime_type = self::actualMIME($file_path);
		if (is_array($default_mime_types) AND (!in_array('*', $default_mime_types) AND $default_mime_types != '*'))
		{
			// If an actual mime type is returned or the mime type is not in the allowed list then raise and error 
			if ($actual_mime_type !== false AND !in_array($actual_mime_type, $default_mime_types))
			{
				// File type not allowed, delete temp file and return error	
				if (JFile::exists($file_path))
				{
					JFile::delete($file_path);
				}
				return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_MIME_TYPE_NOT_ALLOWED', $field_name, $actual_mime_type);				
			}
		}			
		
		if ((in_array($file_type, $default_image_types) OR 
			in_array('*', $default_image_types) OR $default_image_types == '*') AND
			(JString::strpos($actual_mime_type, 'image/') !== false OR $actual_mime_type === false))
		{
			$default_path = $config->get('default_image_path', 'images/componentarchitect');
			
			if (!JFolder::exists(JPATH_ROOT.'/'.$default_path))
			{
				// error ouput directory doesn't exists so delete temp file and return error	
				if (JFile::exists($file_path))
				{
					JFile::delete($file_path);
				}
				return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_OUTPUT_FOLDER_DOES_NOT_EXIST', $field_name, $default_path);
			}	
			
			// Process image file type 
			if  ($img_width > 0 OR $img_height > 0)
			{
				// If a new size has been passed then save the file after resizing.  The save is done on createThumb so file upload not required
				if (!self::createThumb($file_path, $default_path.'/'.$output_name, $img_width, $img_height))
				{
					// error moving file so delete temp file and return error	
					if (JFile::exists($file_path))
					{
						JFile::delete($file_path);
					}
					// and moved file if any (could be corrupted)	
					if (JFile::exists($default_path.'/'.$output_name))
					{
						JFile::delete($default_path.'/'.$output_name);
					}				
					return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_UPLOADED_FILE_NOT_SAVED', $field_name);				
				}	
			}
			else
			{
				// Process document file type 
				if (!JFile::upload($file_path, JPATH_ROOT.'/'.$default_path.'/'.$output_name))
				{
					// error moving file so delete temp file and return error	
					if (JFile::exists($file_path))
					{
						JFile::delete($file_path);
					}
					// and moved file if any (could be corrupted)	
					if (JFile::exists($default_path.'/'.$output_name))
					{
						JFile::delete($default_path.'/'.$output_name);
					}				
					return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_UPLOADED_FILE_NOT_SAVED', $field_name);
				}			
			}
		}
		else
		{
			if ((in_array($file_type, $default_file_types) OR in_array($file_type, $default_media_types)) OR 
				((in_array('*', $default_file_types) OR $default_file_types == '*') AND 
				(in_array('*', $default_media_types) OR $default_media_types == '*')))
			{
				if (in_array($file_type, $default_media_types))
				{
					$default_path = $config->get('default_video_path', 'media/com_componentarchitect/video');
				}
				else
				{			
					$default_path = $config->get('default_file_path', 'media/com_componentarchitect/documents');
				}
				
				if (!JFolder::exists('../'.$default_path))
				{
					// error ouput directory doesn't exists so delete temp file and return error	
					if (JFile::exists($file_path))
					{
						JFile::delete($file_path);
					}
					return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_OUTPUT_FOLDER_DOES_NOT_EXIST', $field_name, $default_path);
				}

				
				if (!JFile::upload($file_path, JPATH_ROOT.'/'.$default_path.'/'.$output_name))
				{
					// error moving file so delete temp file and return error	
					if (JFile::exists($file_path))
					{
						JFile::delete($file_path);
					}
					// and moved file if any (could be corrupted)	
					if (JFile::exists($default_path.'/'.$output_name))
					{
						JFile::delete($default_path.'/'.$output_name);
					}									
					return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_UPLOADED_FILE_NOT_SAVED', $field_name);
				}
			}
			else
			{
				// File type not allowed, delete temp file and return error	
				if (JFile::exists($file_path))
				{
					JFile::delete($file_path);
				}
				return JText::sprintf('COM_COMPONENTARCHITECT_ERROR_FILE_TYPE_NOT_ALLOWED', $field_name, $file_type);		
			}
		}
		
		// If here the file has been processed succsesfully so delete temp file	
		if (JFile::exists($file_path))
		{
			JFile::delete($file_path);
		}
		
		$file['name'] = $default_path.'/'.$output_name;
		return true;
	}
	/**
	* Get the actual mime type of the file uploaded.
	*
	* @param	string			The empm file uploaded
	*
	* @return	string/boolean	The mime type, blank (if windows system) or false
	* 
	* Mime code taken from Easy File Uploader Module for Joomla!
	* Copyright (C) 2010-2012  Michael Albert Gilkes
	* 
	*/
	private static function actualMIME($file)
	{
		$mime = false;
		// try to use recommended functions
		if (defined('FILEINFO_MIME_TYPE') AND
			function_exists('finfo_open') AND is_callable('finfo_open') AND 
			function_exists('finfo_file') AND is_callable('finfo_file') AND 
			function_exists('finfo_close') AND is_callable('finfo_close'))
		{
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $file);
			if ($mime === '')
			{
				$mime = false;
			}
			finfo_close($finfo);
		}
		else
		{
			if (strtoupper(substr(PHP_OS,0,3)) !== 'WIN')
			{
				$f = escapeshellarg($file);
				if (function_exists('exec') AND is_callable('exec'))
				{
					//didn't like how 'system' flushes output to browser. replaced with 'exec'
					//note: You can change this to: shell_exec("file -b --mime-type $f"); if you get
					//      "regular file" as the mime type
					$mime = exec("file -bi $f");
					//this removes the charset value if it was returned with the mime type. mime is first.
					$mime = strtok($mime, '; ');
					$mime = trim($mime); //remove any remaining whitespace
				}
				else
				{
					if (function_exists('shell_exec') AND is_callable('shell_exec'))
					{
						//note: You can change this to: shell_exec("file -b --mime-type $f"); if you get
						//      "regular file" as the mime type
						$mime = shell_exec("file -bi $f");
						//this removes the charset value if it was returned with the mime type. mime is first.
						$mime = strtok($mime, '; ');
						$mime = trim($mime); //remove any remaining whitespace
					}
				}
			}
			else
			{
				if (function_exists('mime_content_type') AND is_callable('mime_content_type'))
				{
					//test using mime_content_type last, since it sometimes detects the mime incorrectly
					$mime = mime_content_type($file);
				}
			}
		}
		return $mime;
	}	
	/**
	* Convert the max file size to bytes.
	*
	* @param	string		The value of max file size e.g. 2048, 2kb, 4 MB
	*
	* @return	string		Value in bytes
	* 
	*/
	public static function convert_max_file_size($size)
	{
		// Convert size to bytes
		$size = self::return_bytes(JString::strtolower(JString::trim($size)));
		
		// If returned value is empty then input string was not properly specified i.e. had non numeric chars besides k, kb, m, mb, g, gb
		if ($size == '')
		{
			//use php ini value
			$size = self::return_bytes(ini_get('upload_max_filesize'));
		}

		return $size;
	}
	/**
	* Return number of bytes for a file size string.
	*
	* @param	string		The value to be converted e.g. 2048, 2kb, 4 MB
	*
	* @return	string		Value in bytes
	* 
	*/	
	public static function return_bytes($value) 
	{
		$value = trim($value);
		switch (strtolower(substr($value, -1)))
		{
			case 'k':
				$value = (int) substr($value, 0, -1);
				$multiplier = 1024;
				break;
			case 'm':
				$value = (int) substr($value, 0, -1);
				$multiplier = 1024 * 1024;
				break;
			case 'g':
				$value = (int) substr($value, 0, -1);
				$multiplier = 1024 * 1024 * 1024;
				break;
			case 'b':
				switch (strtolower(substr($value, -2, 1)))
				{
					case 'k':
						$value = (int) substr($value, 0, -2);
						$multiplier = 1024;
						
						break;
					case 'm':
						$value = (int) substr($value, 0, -2);
						$multiplier = 1024 * 1024;
						break;
					case 'g':
						$value = (int) substr($value, 0, -2);
						$multiplier = 1024 * 1024 * 1024;
						break;
					default :

					break;
				}
				break;
			default:
		
				break; 
		}
		if (!is_numeric($value))
		{
			$value = '';
		}
		else
		{
			$value = $value * $multiplier;
		}		
		return $value;
	}
	/**
	 * Function to check a date string is valid
	 *
	 * @param	date			Date string to check
	 * @param	format			Format of date expected
	 *
	 * @return	parameters		boolean	true or false		
	 */		
	public function validateDate($date, $format = 'YYYY-MM-DD')
	{
		if(JString::strlen($date) >= 8 AND JString::strlen($date) <= 10){
			$separator_only = str_replace(array('M','D','Y'),'', $format);
			$separator = $separator_only[0];
			if($separator){
				$regexp = str_replace($separator, "\\" . $separator, $format);
				$regexp = str_replace('MM', '(0[1-9]|1[0-2])', $regexp);
				$regexp = str_replace('M', '(0?[1-9]|1[0-2])', $regexp);
				$regexp = str_replace('DD', '(0[1-9]|[1-2][0-9]|3[0-1])', $regexp);
				$regexp = str_replace('D', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
				$regexp = str_replace('YYYY', '\d{4}', $regexp);
				$regexp = str_replace('YY', '\d{2}', $regexp);
				if($regexp != $date AND preg_match('/'.$regexp.'$/', $date)){
					foreach (array_combine(explode($separator,$format), explode($separator,$date)) as $key=>$value) {
						if ($key == 'YY') $year = '20'.$value;
						if ($key == 'YYYY') $year = $value;
						if ($key[0] == 'M') $month = $value;
						if ($key[0] == 'D') $day = $value;
					}
					if (checkdate($month,$day,$year)) return true;
				}
			}
		}
		return false;
	}	
}