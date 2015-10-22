<?php
/**
 * @version 		$Id:$
 * @name			Selectfile (Release 1.0.0)
 * @author			 ()
 * @package			com_selectfile
 * @subpackage		com_selectfile.admin
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
 * Size table
 *
 */
class SelectfileTableSizes extends JTable
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
		
		parent::__construct('#__selectfile_sizes', 'id', $db);
		
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
		$table = JTable::getInstance('Sizes','SelectfileTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the size
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
	 * Stores a Size
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
			// New Size. A created and created_by field can be set by the user,
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

