<?php
/**
 * @version 		$Id:$
 * @name			Mmanager (Release 1.0.0)
 * @author			 ()
 * @package			com_mmanager
 * @subpackage		com_mmanager.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobjectplural.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
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
/**
 * Issue table
 *
 */
class MmanagerTableIssues extends JTable
{

	/**
	 * @var    $state	Integer	Variable to hold state.
	 */
	var $state				= 1;
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  Database connector object
	 *
	 */
	public function __construct(&$db)
	{
		
		parent::__construct('#__mmanager_issues', 'id', $db);
		
		$date	= JFactory::getDate();		
		$user = JFactory::getUser();	
		
		$this->created = $date->toSQL();
		$this->created_by = $user->id;

		$this->modified = $date->toSQL();
		$this->modified_by = $user->id;	

		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_mmanager.issue'));

		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_mmanager.issue'));
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
		// Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (empty($this->alias))
		{
			$this->alias = JApplication::stringURLSafe($this->name);
		}
		
		// Verify that the alias is unique - by this stage it should always be but this is a final check
		$table = JTable::getInstance('Issues', 'MmanagerTable');
		if ($table->load(array('alias'=>$this->alias)) AND ($table->id != $this->id OR $this->id==0))
		{
			$this->setError(JText::_('COM_MMANAGER_FIELD_ALIAS_DUPLICATED'));
			return false;
		}		

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
				$this->ordering = self::getNextOrder($additional_order.' state>=0');
			}
		}

		// Check the publish down date is not earlier than publish up.
		if (intval($this->publish_down) > 0 AND $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

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
			
		if ( !array_key_exists('published',$array)  ) 
		{
			$array['published'] = '0';
		}
		
		// Bind the rules.
		if (isset($array['rules']) AND is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  
	 * The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param	mixed	$pks	An optional array of primary key values to update.  If not
	 *							set the instance property value is used.
	 * @param	integer $state	The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
	 * @param	integer $user_id	The user id of the user performing the operation.
	 * @return	boolean	True on success.
	 * 
	 */
	public function publish($pks = null, $state = 1, $user_id = 0)
	{
		
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
		$table = JTable::getInstance('Issues','MmanagerTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the issue
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}

			// Verify checkout
			if($table->checked_out==0 OR $table->checked_out==$user_id)
			{
				// Change the state
				$table->state = $state;
				$table->checked_out=0;
				$table->checked_out_time=0;

				// Check the row
				$table->check();

				// Store the row
				if (!$table->store())
				{
					$this->setError($table->getError());
				}
			}
		}
		return count($this->getErrors())==0;
	}

	/**
	 * Override parent delete method to delete tags information.
	 *
	 * @param   integer  $pk		Primary key to delete.
	 *
	 * @return  boolean  $result	True on success.
	 *
	 * @throws  UnexpectedValueException
	 */
	public function delete($pk = null)
	{
		$result = parent::delete($pk);
		return $result;
	}
		
	/**
	 * Stores a Issue
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
			// New Issue. A created and created_by field can be set by the user,
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

		// Set publish_up to null date if not set
		if (!$this->publish_up)
		{
			$this->publish_up = $this->_db->getNullDate();
		}

		// Set publish_down to null date if not set
		if (!$this->publish_down)
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		// Set xreference to empty string if not set
		if (!$this->xreference)
		{
			$this->xreference = '';
		}

		if (isset($this->images) AND is_array($this->images))
		{
			$registry = new JRegistry;
			$registry->loadArray($this->images);
			$this->images = (string)$registry;
			$registry = null; //release memory	
		}		

		if (isset($this->urls) AND is_array($this->urls))
		{
			$registry = new JRegistry;
			$registry->loadArray($this->urls);
			$this->urls = (string)$registry;
			$registry = null; //release memory	
		}		

										

		if (isset($this->params) AND is_array($this->params))
		{
			$registry = new JRegistry;
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
			$registry = null; //release memory	
		}		

		// Attempt to store the data.
		return parent::store($update_nulls);
	}	
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
		$k = $this->_tbl_key;
		return 'com_mmanager.issue.'.(int) $this->$k;
	}
	
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return      string
	 * 
	 */
	protected function _getAssetTitle()
	{
		return $this->name;
		
	}
	
	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param	integer  $id     Id to look up
	 * 
	 * @return  integer
	 * 
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		
		$asset = JTable::getInstance('Asset');
		
		if ($asset->loadByName('com_mmanager'))
		{
			return $asset->id;
		}
		else
		{		
			return $asset->getRootId();
		}	
	}
}

