<?php
/**
 * @version 		$Id: components.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectplural.php 806 2013-12-24 13:24:16Z BrianWade $
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

defined('_JEXEC') or die;
if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.html.parameter');
}

	
/**
 * Component table
 *
 */
class ComponentArchitectTableComponents extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  Database connector object
	 *
	 */
	public function __construct(&$db)
	{
		
		parent::__construct('#__componentarchitect_components', 'id', $db);
		
		$date	= JFactory::getDate();		
		$user = JFactory::getUser();	
		
		$this->created = $date->toSQL();
		$this->created_by = $user->id;

		$this->modified = $date->toSQL();
		$this->modified_by = $user->id;	
	}

	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * 
	 */
	public function check()
	{
		// Set name
		$this->name = htmlspecialchars_decode($this->name, ENT_QUOTES);



		return true;		
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array to bind
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 * 
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * 
	 */
	public function bind($array, $ignore = array())
	{
				
				

		return parent::bind($array, $ignore);
	}

		
	/**
	 * Stores a Component/Extension
	 *
	 * @param	boolean	$update_nulls	True to update fields even if they are null.
	 * 
	 * @return	boolean	$result			True on success, false on failure.
	 * 
	 */
	public function store($update_nulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$app   = JFactory::getApplication();

		// Store any $_FILES input
		$files = $app->input->files->get('jform');
		
		if (empty($this->id))
		{
			// New Component/Extension. A created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->created))
			{
				$this->created = $date->toSQL();
			}
			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		// Existing item
		$this->modified		= $date->toSQL();
		$this->modified_by	= $user->get('id');		
			
		if (count($files) > 0 AND isset($files['icon_16px']) AND $files['icon_16px']['name'] != '')
		{
			$file = $files['icon_16px'];		

			// Add parameters to the saveUpload call to specify the output file name and the width and height for image file uploads
			// e.g. saveUpload($file,'icon_16px.png', 100, 0)
			//[%%START_CUSTOM_CODE%%]
			//$result = ComponentArchitectHelper::saveUpload($file, 'Icon 16px');
			$result = ComponentArchitectHelper::saveUpload($file, 'Icon 16px', '', 16, 16);
			//[%%END_CUSTOM_CODE%%]
			
			if ($result === true)
			{
				$this->icon_16px = $file['name'];
			}
			else
			{
				if ($result !== false)
				{
					$this->setError($result);
					return false;
				}				
			}			
		} 
			
		if (count($files) > 0 AND isset($files['icon_48px']) AND $files['icon_48px']['name'] != '')
		{
			$file = $files['icon_48px'];		

			// Add parameters to the saveUpload call to specify the output file name and the width and height for image file uploads
			// e.g. saveUpload($file,'icon_48px.png', 100, 0)
			//[%%START_CUSTOM_CODE%%]
			//$result = ComponentArchitectHelper::saveUpload($file, 'Icon 48px');
			$result = ComponentArchitectHelper::saveUpload($file, 'Icon 48px', '', 48, 48);
			//[%%END_CUSTOM_CODE%%]
			
			if ($result === true)
			{
				$this->icon_48px =  $file['name'];
			}
			else
			{
				if ($result !== false)
				{
					$this->setError($result);
					return false;
				}				
			}			
		} 
			
		if (isset($_FILES['jform']['name']['categories_icon_16px']) AND $_FILES['jform']['name']['categories_icon_16px'] != '')
		{
			// Add parameters to the saveUpload call to specify the output file name and the width and height for image file uploads
			// e.g. saveUpload('categories_icon_16px','categories_icon_16px.png', 100, 0)
			//[%%START_CUSTOM_CODE%%]
			//$result = ComponentArchitectHelper::saveUpload('categories_icon_16px', 'Categories Icon 16px');
			$result = ComponentArchitectHelper::saveUpload('categories_icon_16px', 'Categories Icon 16px', '', 16, 16);
			//[%%END_CUSTOM_CODE%%]
			
			if ($result === true)
			{
				$this->categories_icon_16px = $_FILES['jform']['name']['categories_icon_16px'];
			}
			else
			{
				if ($result !== false)
				{
					$this->setError($result);
					return false;
				}				
			}			
		} 
			
		if (isset($_FILES['jform']['name']['categories_icon_48px']) AND $_FILES['jform']['name']['categories_icon_48px'] != '')
		{
			// Add parameters to the saveUpload call to specify the output file name and the width and height for image file uploads
			// e.g. saveUpload('categories_icon_48px','categories_icon_48px.png', 100, 0)
			//[%%START_CUSTOM_CODE%%]
			//$result = ComponentArchitectHelper::saveUpload('categories_icon_48px', 'Categories Icon 48px');
			$result = ComponentArchitectHelper::saveUpload('categories_icon_48px', 'Categories Icon 48px', '', 48, 48);
			//[%%END_CUSTOM_CODE%%]
			
			if ($result === true)
			{
				$this->categories_icon_48px = $_FILES['jform']['name']['categories_icon_48px'];
			}
			else
			{
				if ($result !== false)
				{
					$this->setError($result);
					return false;
				}				
			}			
		} 
		
			// Check and reformat entries in the json array
			$field_array = $this->joomla_parts;
			
		
		$this->joomla_parts = $field_array;
					
		if (isset($this->joomla_parts) AND is_array($this->joomla_parts))
		{
			$registry = new JRegistry;
			$registry->loadArray($this->joomla_parts);
			$this->joomla_parts = (string)$registry;
			$registry = null; //release memory	
		}	
			// Check and reformat entries in the json array
			$field_array = $this->joomla_features;
		
		$this->joomla_features = $field_array;
					
		if (isset($this->joomla_features) AND is_array($this->joomla_features))
		{
			$registry = new JRegistry;
			$registry->loadArray($this->joomla_features);
			$this->joomla_features = (string)$registry;
			$registry = null; //release memory	
		}	


		// Attempt to store the data.
		return parent::store($update_nulls);
	}	
}

