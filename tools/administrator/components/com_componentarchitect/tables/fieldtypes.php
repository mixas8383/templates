<?php
/**
 * @version 		$Id: fieldtypes.php 411 2014-10-19 18:39:07Z BrianWade $
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
 * FieldType table
 *
 */
class ComponentArchitectTableFieldTypes extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  Database connector object
	 *
	 */
	public function __construct(&$db)
	{
		
		parent::__construct('#__componentarchitect_fieldtypes', 'id', $db);
		
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
				
		if ( !array_key_exists('class',$array)  ) 
		{
			$array['class'] = '0';
		}
		if ( !array_key_exists('size',$array)  ) 
		{
			$array['size'] = '0';
		}
		if ( !array_key_exists('width',$array)  ) 
		{
			$array['width'] = '0';
		}
		if ( !array_key_exists('maxlength',$array)  ) 
		{
			$array['maxlength'] = '0';
		}
		if ( !array_key_exists('height',$array)  ) 
		{
			$array['height'] = '0';
		}
		if ( !array_key_exists('cols',$array)  ) 
		{
			$array['cols'] = '0';
		}
		if ( !array_key_exists('rows',$array)  ) 
		{
			$array['rows'] = '0';
		}
		if ( !array_key_exists('value_source',$array)  ) 
		{
			$array['value_source'] = '0';
		}
		if ( !array_key_exists('option_values',$array)  ) 
		{
			$array['option_values'] = '0';
		}
		if ( !array_key_exists('multiple',$array)  ) 
		{
			$array['multiple'] = '0';
		}
		if ( !array_key_exists('format',$array)  ) 
		{
			$array['format'] = '0';
		}
		if ( !array_key_exists('first',$array)  ) 
		{
			$array['first'] = '0';
		}
		if ( !array_key_exists('last',$array)  ) 
		{
			$array['last'] = '0';
		}
		if ( !array_key_exists('step',$array)  ) 
		{
			$array['step'] = '0';
		}
		if ( !array_key_exists('hide_none',$array)  ) 
		{
			$array['hide_none'] = '0';
		}
		if ( !array_key_exists('hide_default',$array)  ) 
		{
			$array['hide_default'] = '0';
		}
		if ( !array_key_exists('buttons',$array)  ) 
		{
			$array['buttons'] = '0';
		}
		if ( !array_key_exists('hide_buttons',$array)  ) 
		{
			$array['hide_buttons'] = '0';
		}
		if ( !array_key_exists('foreign_object_id',$array)  ) 
		{
			$array['foreign_object_id'] = '0';
		}
		if ( !array_key_exists('field_filter',$array)  ) 
		{
			$array['field_filter'] = '0';
		}
		if ( !array_key_exists('max_file_size',$array)  ) 
		{
			$array['max_file_size'] = '0';
		}
		if ( !array_key_exists('exclude_files',$array)  ) 
		{
			$array['exclude_files'] = '0';
		}
		if ( !array_key_exists('accept_file_types',$array)  ) 
		{
			$array['accept_file_types'] = '0';
		}
		if ( !array_key_exists('directory',$array)  ) 
		{
			$array['directory'] = '0';
		}
		if ( !array_key_exists('link',$array)  ) 
		{
			$array['link'] = '0';
		}
		if ( !array_key_exists('sql_query',$array)  ) 
		{
			$array['sql_query'] = '0';
		}
		if ( !array_key_exists('sql_key_field',$array)  ) 
		{
			$array['sql_key_field'] = '0';
		}
		if ( !array_key_exists('sql_value_field',$array)  ) 
		{
			$array['sql_value_field'] = '0';
		}
		if ( !array_key_exists('translate',$array)  ) 
		{
			$array['translate'] = '0';
		}
		if ( !array_key_exists('client',$array)  ) 
		{
			$array['client'] = '0';
		}
		if ( !array_key_exists('stripext',$array)  ) 
		{
			$array['stripext'] = '0';
		}
		if ( !array_key_exists('preview',$array)  ) 
		{
			$array['preview'] = '0';
		}
		if ( !array_key_exists('autocomplete',$array)  ) 
		{
			$array['autocomplete'] = '0';
		}
		if ( !array_key_exists('onclick',$array)  ) 
		{
			$array['onclick'] = '0';
		}
		if ( !array_key_exists('onchange',$array)  ) 
		{
			$array['onchange'] = '0';
		}
				

		return parent::bind($array, $ignore);
	}

		
	/**
	 * Stores a Field Type
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

	
					
		if (empty($this->id))
		{
			// New Field Type. A created and created_by field can be set by the user,
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

		


		// Attempt to store the data.
		return parent::store($update_nulls);
	}	
}

