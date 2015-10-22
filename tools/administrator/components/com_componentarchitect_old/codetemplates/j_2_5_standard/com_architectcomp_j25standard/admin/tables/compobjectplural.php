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
 * @version			$Id: compobjectplural.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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
[%%IF INCLUDE_ASSETACL%%]
jimport('joomla.access.rules');
[%%ENDIF INCLUDE_ASSETACL%%]
jimport('joomla.html.parameter');

/**
 * [%%CompObject%%] table
 *
 */
class [%%ArchitectComp%%]Table[%%CompObjectPlural%%] extends JTable
{
[%%IF INCLUDE_STATUS%%]
	var $state				= 1;
[%%ENDIF INCLUDE_STATUS%%]
	/**
	 * Constructor
	 *
	 */
	function __construct(&$_db)
	{
		
		parent::__construct('#__[%%architectcomp%%]_[%%compobjectplural%%]', 'id', $_db);
		
		$date	= JFactory::getDate();		
		$user = JFactory::getUser();	
		
		[%%IF INCLUDE_CREATED%%]
		$this->created = $date->toSQL();
		$this->created_by = $user->id;
		[%%ENDIF INCLUDE_CREATED%%]

		[%%IF INCLUDE_MODIFIED%%]
		$this->modified = $date->toSQL();
		$this->modified_by = $user->id;	
		[%%ENDIF INCLUDE_MODIFIED%%]	
	}

	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * 
	 */
	function check()
	{
		// Set name
		[%%IF INCLUDE_NAME%%]
		$this->name = htmlspecialchars_decode($this->name, ENT_QUOTES);
			[%%IF INCLUDE_ALIAS%%]
		// Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (empty($this->alias))
		{
			$this->alias = JApplication::stringURLSafe($this->name);
		}
		
		// Verify that the alias is unique - by this stage it should always be but this is a final check
		$table = JTable::getInstance('[%%CompObjectPlural%%]', '[%%ArchitectComp%%]Table');
		if ($table->load(array('alias'=>$this->alias)) AND ($table->id != $this->id OR $this->id==0))
		{
			$this->setError(JText::_('[%%COM_ARCHITECTCOMP%%]_FIELD_ALIAS_DUPLICATED'));
			return false;
		}		
			[%%ENDIF INCLUDE_ALIAS%%]
		[%%ENDIF INCLUDE_NAME%%]		

		[%%IF INCLUDE_STATUS%%]
			[%%IF INCLUDE_ORDERING%%]
		// Set ordering
		if ($this->state < 0)
		{
			// Set ordering to 0 if state is trashed
			$this->ordering = 0;
		} 
		else
		{ 
			if (empty($this->ordering))
			{
				// Set ordering to last if ordering was 0
				
				$additional_order = '';	
				[%%FOREACH ORDER_FIELD%%]
				$additional_order .= $this->_db->quoteName('[%%FIELD_CODE_NAME%%]').'=' . $this->_db->Quote($this->[%%FIELD_CODE_NAME%%]).' AND ';
				[%%ENDFOR ORDER_FIELD%%]				
				$this->ordering = self::getNextOrder($additional_order.' state>=0');
			}
		}
			[%%ENDIF INCLUDE_ORDERING%%]
		[%%ENDIF INCLUDE_STATUS%%]

		[%%IF INCLUDE_PUBLISHED_DATES%%]
		// Check the publish down date is not earlier than publish up.
		if (intval($this->publish_down) > 0 AND $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}
		[%%ENDIF INCLUDE_PUBLISHED_DATES%%]

		[%%IF INCLUDE_METADATA%%]
		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey))
		{
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = str_replace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();
			foreach($keys as $key)
			{
				if (JString::trim($key))
				{  // ignore blank keywords
					$clean_keys[] = JString::trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		// clean up description -- eliminate quotes and <> brackets
		if (!empty($this->metadesc))
		{
			// only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->metadesc = str_replace($bad_characters, "", $this->metadesc);
		}
		[%%ENDIF INCLUDE_METADATA%%]		
		return true;		
	}

	/**
	 * Overloaded bind function
	 *
	 * @param	array		$hash named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * 
	 */
	public function bind($array, $ignore = array())
	{
				
		[%%IF INCLUDE_DESCRIPTION%%]
			[%%IF INCLUDE_INTRO%%]		
		// Search for the {readmore} tag and split the text up accordingly.
		if (isset($array['introdescription']))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tag_pos	= preg_match($pattern, $array['introdescription']);

			if ($tag_pos == 0)
			{
				$array['description'] = $array['introdescription'];
				$array['intro'] = '';
			}
			else
			{
				list($array['intro'], $array['description']) = preg_split($pattern, $array['introdescription'], 2);
			}
		}
			[%%ENDIF INCLUDE_INTRO%%]				
		[%%ENDIF INCLUDE_DESCRIPTION%%]

		[%%FOREACH OBJECT_FIELD%%]
			[%%IF FIELD_CHECKBOX%%]
		if ( !array_key_exists('[%%FIELD_CODE_NAME%%]',$array)  ) 
		{
			$array['[%%FIELD_CODE_NAME%%]'] = '0';
		}
			[%%ENDIF FIELD_CHECKBOX%%]
			[%%IF FIELD_RADIO%%]
			
		if (!array_key_exists('[%%FIELD_CODE_NAME%%]', $array)) 
		{
			$array['[%%FIELD_CODE_NAME%%]'] = '';
		}
			[%%ENDIF FIELD_RADIO%%]				
		[%%ENDFOR OBJECT_FIELD%%]
		[%%FOREACH REGISTRY_FIELD%%]
		
		if (is_array($array['[%%FIELD_CODE_NAME%%]']))
		{
			$field_array = $array['[%%FIELD_CODE_NAME%%]'];
			[%%FOREACH REGISTRY_ENTRY%%]
			if (!array_key_exists('[%%FIELD_CODE_NAME%%]', $field_array)) 
			{
				$field_array['[%%FIELD_CODE_NAME%%]'] = [%%FIELD_DEFAULT%%];
			}
			[%%ENDFOR REGISTRY_ENTRY%%]
			$array['[%%FIELD_CODE_NAME%%]'] = $field_array; 
		}
		[%%ENDFOR REGISTRY_FIELD%%]	
		[%%IF INCLUDE_ASSETACL%%]
		// Bind the rules.
		if (isset($array['rules']) AND is_array($array['rules']))
		{
			$rules = new JRules($array['rules']);
			$this->setRules($rules);
		}
		[%%ENDIF INCLUDE_ASSETACL%%]

		return parent::bind($array, $ignore);
	}

	[%%IF INCLUDE_STATUS%%]
	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  
	[%%IF INCLUDE_CHECKOUT%%]
	 * The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	[%%ENDIF INCLUDE_CHECKOUT%%]
	 *
	 * @param	mixed	An optional array of primary key values to update.  If not
	 *					set the instance property value is used.
	 * @param	integer The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
	 * @param	integer The user id of the user performing the operation.
	 * @return	boolean	True on success.
	 * 
	 */
	public function publish($pks = null, $state = 1, $user_id = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$user_id = (int) $user_id;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('[%%CompObjectPlural%%]','[%%ArchitectComp%%]Table');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the [%%compobject%%]
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}

			// Verify checkout
			[%%IF INCLUDE_CHECKOUT%%]
			if($table->checked_out==0 OR $table->checked_out==$user_id)
			{
			[%%ENDIF INCLUDE_CHECKOUT%%]
				// Change the state
				$table->state = $state;
				[%%IF INCLUDE_CHECKOUT%%]
				$table->checked_out=0;
				$table->checked_out_time=0;
				[%%ENDIF INCLUDE_CHECKOUT%%]

				// Check the row
				$table->check();

				// Store the row
				if (!$table->store())
				{
					$this->setError($table->getError());
				}
			[%%IF INCLUDE_CHECKOUT%%]
			}
			[%%ENDIF INCLUDE_CHECKOUT%%]
		}
		return count($this->getErrors())==0;
	}
	[%%ENDIF INCLUDE_STATUS%%]
	/**
	 * Stores a [%%CompObject_name%%]
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * 
	 */
	public function store($update_nulls = false)
	{

		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		
		[%%IF INCLUDE_CREATED%%]
		if (empty($this->id))
		{
			// New [%%CompObject_name%%]. A created and created_by field can be set by the user,
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
		[%%ENDIF INCLUDE_CREATED%%]

		[%%IF INCLUDE_MODIFIED%%]
		// Existing item
		$this->modified		= $date->toSQL();
		$this->modified_by	= $user->get('id');		
		[%%ENDIF INCLUDE_MODIFIED%%]

		[%%IF INCLUDE_URLS%%]
		if (isset($this->urls) AND is_array($this->urls))
		{
			$registry = new JRegistry;
			$registry->loadArray($this->urls);
			$this->urls = (string)$registry;
			$registry = null; //release memory	
		}		
		[%%ENDIF INCLUDE_URLS%%]

		[%%FOREACH OBJECT_FIELD%%]
			[%%IF FIELD_CHECKBOXES%%]
		if (isset($this->[%%FIELD_CODE_NAME%%]) AND is_array($this->[%%FIELD_CODE_NAME%%]))
		{
			$this->[%%FIELD_CODE_NAME%%] = implode(',',$this->[%%FIELD_CODE_NAME%%]);
		}
			[%%ELSE FIELD_CHECKBOXES%%]
				[%%IF FIELD_MULTIPLE%%]
		if (isset($this->[%%FIELD_CODE_NAME%%]) AND is_array($this->[%%FIELD_CODE_NAME%%]))
		{
			$this->[%%FIELD_CODE_NAME%%] = implode(',',$this->[%%FIELD_CODE_NAME%%]);
		}	
				[%%ENDIF FIELD_MULTIPLE%%]				
			[%%ENDIF FIELD_CHECKBOXES%%]
		[%%ENDFOR OBJECT_FIELD%%]	
						
		[%%FOREACH REGISTRY_FIELD%%]
			// Check and reformat entries in the json array
			$field_array = $this->[%%FIELD_CODE_NAME%%];
			
			[%%FOREACH REGISTRY_ENTRY%%]
				[%%IF FIELD_CHECKBOXES%%]
		if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND is_array($field_array['[%%FIELD_CODE_NAME%%]']))
		{
			$field_array['[%%FIELD_CODE_NAME%%]'] = implode(',',$field_array['[%%FIELD_CODE_NAME%%]']);
		}	
				[%%ELSE FIELD_CHECKBOXES%%]
					[%%IF FIELD_MULTIPLE%%]
		if (isset($field_array['[%%FIELD_CODE_NAME%%]']) AND is_array($field_array['[%%FIELD_CODE_NAME%%]']))
		{
			$field_array['[%%FIELD_CODE_NAME%%]'] = implode(',',$field_array['[%%FIELD_CODE_NAME%%]']);
		}	
					[%%ENDIF FIELD_MULTIPLE%%]
				[%%ENDIF FIELD_CHECKBOXES%%]
			[%%ENDFOR REGISTRY_ENTRY%%]
					
		$this->[%%FIELD_CODE_NAME%%] = $field_array;
				
		if (isset($this->[%%FIELD_CODE_NAME%%]) AND is_array($this->[%%FIELD_CODE_NAME%%]))
		{
			$registry = new JRegistry();
			$registry->loadArray($this->[%%FIELD_CODE_NAME%%]);
			$this->[%%FIELD_CODE_NAME%%] = (string)$registry;
			$registry = null; //release memory	
		}
		[%%ENDFOR REGISTRY_FIELD%%]		

		[%%IF INCLUDE_PARAMS_RECORD%%]
		if (isset($this->params) AND is_array($this->params))
		{
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
			$registry = null; //release memory	
		}		
		[%%ENDIF INCLUDE_PARAMS_RECORD%%]

		// Attempt to store the data.
		return parent::store($update_nulls);
	}	
[%%IF INCLUDE_ASSETACL%%]
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return      string
	 * 
	 */
	protected function _getAssetName()
	{
		[%%IF INCLUDE_ASSETACL_RECORD%%]
		$k = $this->_tbl_key;
		return '[%%com_architectcomp%%].[%%compobject%%].'.(int) $this->$k;
		[%%ELSE INCLUDE_ASSETACL_RECORD%%]
		return '[%%com_architectcomp%%]';
		[%%ENDIF INCLUDE_ASSETACL_RECORD%%]		
	}
	
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return      string
	 * 
	 */
	protected function _getAssetTitle()
	{
		[%%IF INCLUDE_NAME%%]
		return $this->name;
		[%%ELSE INCLUDE_NAME%%]
		return $this->id;
		[%%ENDIF INCLUDE_NAME%%]
		
	}
	
	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
	 * 
	 * @return      int
	 * 
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		
		$asset = JTable::getInstance('Asset');
		[%%IF GENERATE_CATEGORIES%%]
		// Find the parent-asset
		if (($this->catid) AND !empty($this->catid))
		{
			// The item has a category as asset-parent
			if ($asset->loadByName('[%%com_architectcomp%%].category.' . (int) $this->catid))
			{
				return $asset->id;
			}
		}
		[%%ENDIF GENERATE_CATEGORIES%%]		
		
		if ($asset->loadByName('[%%com_architectcomp%%]'))
		{
			return $asset->id;
		}
		else
		{		
			return $asset->getRootId();
		}	
	}
[%%ENDIF INCLUDE_ASSETACL%%]
}

