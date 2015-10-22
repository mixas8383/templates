<?php
/**
 * @version 		$Id:$
 * @name			Wstools (Release 1.0.0)
 * @author			 ()
 * @package			com_wstools
 * @subpackage		com_wstools.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: compobject.php 408 2014-10-19 18:31:00Z BrianWade $
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
 * Remotemedia controller class.
 *
 */
class WstoolsControllerRemotemedia extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * 
	 */
	protected $text_prefix = 'COM_WSTOOLS_REMOTEMEDIAS';
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 *
	 * @return	JControllerForm
	 * 
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);	

		$this->view_list = 'remotemedias';
	}
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 *
	 * @return	boolean
	 * 
	 */
	protected function allowAdd($data = array())
	{

		$user		= JFactory::getUser();
		// Check create access on the object.
		$result = $user->authorise('core.create', 'com_wstools');
		if($result === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);		
		}
		else
		{
			return $result;
		}		
	}			
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * 
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{

		$record_id	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$user_id		= $user->get('id');
		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_wstools.remotemedia.'.$record_id))
		{
			return true;
		}
		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_wstools.remotemedia.'.$record_id))
		{
			// Now test the owner is the user.
			$owner_id = 0;

			if ( isset($data['created_by']))
			{
				$owner_id	= (int) $data['created_by'];
			}

			if ($owner_id == 0 AND $record_id) 
			{
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($record_id);

				if (empty($record)) 
				{
					return false;
				}
				$owner_id = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($owner_id == $user_id) 
			{
				return true;
			}
		}
		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean	 True if successful, false otherwise and internal error is set.
	 *
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Remotemedia', 'WstoolsModel', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_wstools&view=remotemedias' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}	
	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   array         $valid_data   The validated data.
	 *
	 * @return	void
	 *
	 */
	protected function postSaveHook(JModelLegacy $model, $valid_data = array())
	{
		return;
	}	
}