<?php
/**
 * @version			$Id: j25standard_install.php 432 2014-10-23 16:12:50Z BrianWade $
 * @name			Joomla! 2.5 Standard Code Template (Release 1.0.4)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			componentarchitect
 * @subpackage		componentarchitect.cacodetemplates.j25standard
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

defined('JPATH_BASE') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Script file of Component Architect code templates J25 Standard plugin
 */
class plgCACodeTemplatesj25standardInstallerScript
{
    /**
     * method to install the code template
     * 
     * @param	object	parent installer application
     *
     * @return void
     */
    function install($parent) 
    {
        $parent = $parent->getParent();
 
		$params = $this->getParams();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$release = $parent->get( "manifest" )->version;

		// Construct the query
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__categories'));	
		$query->where($db->quoteName('extension').' = '.$db->quote('com_componentarchitect'));		
		$query->where($db->quoteName('alias').' = '.$db->quote('joomla-2-5-packages'));		

		$db->setQuery($query->__toString());
		
		$category_id = $db->loadResult();
		// This is only for the code templates installed with Component Architect
		if (empty($category_id) OR $category_id == 0)
		{
			$category_id = 10211;  // Dummy category id for joomla-2-5-packages
		}	

		$query->clear();
		$query->insert($db->quoteName('#__componentarchitect_codetemplates'));
		$query->set($db->quoteName('name').' = '.$db->quote('J 2.5 Standard'));
		$query->set($db->quoteName('catid').' = '.(int) $category_id);
		$query->set($db->quoteName('description').' = '.$db->quote(JText::_('PLG_CACODETEMPLATES_J25STANDARD_CODETEMPLATE_DESCRIPTION')));
		$query->set($db->quoteName('template_component_name').' = '.$db->quote('architect comp'));
		$query->set($db->quoteName('template_object_name').' = '.$db->quote('comp object'));
		$query->set($db->quoteName('template_markup_prefix').' = '.$db->quote('[%%'));
		$query->set($db->quoteName('template_markup_suffix').' = '.$db->quote('%%]'));
		$query->set($db->quoteName('version').' = '.$db->quote($release));
		$query->set($db->quoteName('source_path').' = '.$db->quote('j_2_5_standard'));
		$query->set($db->quoteName('predefined_code_template').' = 1');
		$query->set($db->quoteName('generate_predefined_fields').' = 0');
		$query->set($db->quoteName('multiple_category_objects').' = 0');
		$query->set($db->quoteName('platform').' = '.$db->quote('Joomla'));
		$query->set($db->quoteName('platform_version').' = '.$db->quote('2.5'));
		$query->set($db->quoteName('coding_language').' = '.$db->quote('PHP,JAVASCRIPT,XML,CSS'));
		$query->set($db->quoteName('created').' = '.$db->quote(JFactory::getDate()->toSQL()));
		$query->set($db->quoteName('created_by').' = '.JFactory::getUser()->get('id'));
		$query->set($db->quoteName('modified').' = '.$db->quote(JFactory::getDate()->toSQL()));
		$query->set($db->quoteName('modified_by').' = '.JFactory::getUser()->get('id'));
		
		try
		{
			$db->setQuery($query->__toString());
			$db->execute();	
		}			
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_CACODETEMPLATES_J25STANDARD_ERROR_CODETEMPLATE_INSERT', $e->getMessage()), 'warning');
			return false;
		}
		
          
        // Store the record id and folder location for this code template plugin 
        $param_array = array('codetemplate_record_id' => $db->insertid(), 'codetemplate_folder_path' => 'administrator/components/com_componentarchitect/codetemplates/j_2_5_standard');   
		$this->setParams($param_array);
		        
		return true;
	
    }

    /**
     * method to uninstall the code template
     *
     * @param	object	parent installer application
     *
     * @return void
     */
    function uninstall($parent) 
    {
        // $parent is the class calling this method
        $parent = $parent->getParent();
		$params = $this->getParams();
		$return = true;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__componentarchitect_codetemplates'));
		$query->where($db->quoteName('id').' = '.(int) $params['codetemplate_record_id']);
		$db->setQuery($query->__toString());
		
		try
		{
			$db->execute(); 
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_CACODETEMPLATES_J25STANDARD_ERROR_CODETEMPLATE_DELETE', $e->getMessage()), 'warning');
			$return = false;
		}		
		
		try
		{
			JFolder::delete(JPATH_SITE.'/administrator/components/com_componentarchitect/codetemplates/j_2_5_standard');
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_CACODETEMPLATES_J25STANDARD_ERROR_CODETEMPLATE_FILE_DELETE'), 'warning');
			$return = false;
		}	
		
		return $return;
    }

    /**
     * method to update the component
     *
     * @param	object	parent installer application
     *
     * @return void
     */
    function update($parent) 
    {
        $parent = $parent->getParent();
		$db = JFactory::getDbo();

		// Installing component manifest file version
		$release = $parent->get( "manifest" )->version;

		$params = $this->getParams();

		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__componentarchitect_codetemplates'));
		//Set any other field values as required
		$query->set($db->quoteName('version').' = '.$db->quote($release));
		$query->set($db->quoteName('modified').' = '.$db->quote(JFactory::getDate()->toSQL()));
		$query->set($db->quoteName('modified_by').' = '.JFactory::getUser()->get('id'));
		$query->where($db->quoteName('id').' = '.(int) $params['codetemplate_record_id']);

		try
		{
			$db->setQuery($query->__toString());
			$db->execute();	
		}			
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_CACODETEMPLATES_J25STANDARD_ERROR_CODETEMPLATE_UPDATE', $e->getMessage()), 'warning');
			return false;
		}
		
		return true;		
    }
    /**
     * method to run before an install/update/uninstall method
     *
     * @param	string	type of action being performed
     * @param	object	parent installer application
     *
     * @return boolean	result is true or false
     */
    function preflight($type, $parent) 
    {
		switch ($type)
		{
			case 'install' :
				break;
			case 'uninstall' :
				break;
			case 'update' :
				break;			
			default :
				break; 
		}	
		return true;        
    }
   /**
     * method to run after an install/update method
     *
     * @param	string	type of action being performed
     * @param	object	parent installer application
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
    
		switch ($type)
		{
			case 'install' :
				if (!JFolder::exists(JPATH_SITE.'/administrator/components/com_componentarchitect/codetemplates/j_2_5_standard'))
				{
					// Now need to copy the code template files to the com_componentarchitect/codetemplates folder
					$result = JFolder::create(JPATH_SITE.'/administrator/components/com_componentarchitect/codetemplates/j_2_5_standard');
					if ($result !== true) 
					{
						JFactory::getApplication()->enqueueMessage(JText::_('PLG_CACODETEMPLATES_J25STANDARD_ERROR_CODETEMPLATE_FILE_COPY').' - '.$result, 'warning');
						return false;
					}
				}
				
				// Make sure index.html files in all folders
				if (!JFile::exists(JPATH_SITE.'plugins/cacodetemplates/index.html'))
				{
					JFile::copy('plugins/cacodetemplates/j25standard/index.html','plugins/cacodetemplates/index.html', JPATH_SITE);
				}
				JFile::copy('plugins/cacodetemplates/j25standard/index.html','administrator/components/com_componentarchitect/codetemplates/j_2_5_standard/index.html', JPATH_SITE);
				$zip_adapter = JArchive::getAdapter('zip');
				$result = $zip_adapter->extract(JPATH_SITE.'/plugins/cacodetemplates/j25standard/com_architectcomp_j25standard.zip', JPATH_SITE.'\administrator\components\com_componentarchitect\codetemplates\j_2_5_standard\com_architectcomp_j25standard');

				if ($result !== true) 
				{
					JFactory::getApplication()->enqueueMessage(JText::_('PLG_CACODETEMPLATES_J25STANDARD_ERROR_CODETEMPLATE_FILE_COPY').' - '.$result, 'warning');
					return false;
				}			          
		 
				break;    				
			case 'update' :
				// Now need to copy the code template files to overwrite those in the com_componentarchitect/codetemplates folder
				$zip_adapter = JArchive::getAdapter('zip');
				$result = $zip_adapter->extract(JPATH_SITE.'/plugins/cacodetemplates/j25standard/com_architectcomp_j25standard.zip', JPATH_SITE.'/administrator/components/com_componentarchitect/codetemplates/j_2_5_standard/com_architectcomp_j25standard');
				if ($result !== true) 
				{
					JFactory::getApplication()->enqueueMessage(JText::_('PLG_CACODETEMPLATES_J25STANDARD_ERROR_CODETEMPLATE_FILE_COPY').' - '.$result, 'warning');
					return false;
				}
				
				break; 					
			default :
				break; 
		}				
    }        
 	/**
	 * get a  manifest value in the component's row of the extension table
	 * 
	 * @return	array	manifest values
	 */
	private function getManifest()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));	
		$query->where($db->quoteName('name').' = '.$db->quote('plg_cacodetemplates_j25standard'));		
		$query->where($db->quoteName('type').' = '.$db->quote('plugin'));

		$db->setQuery($query->__toString());
		
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest;
	}
  	/**
	 * get a  parameter value in the component's row of the extension table
	 * 
	 * @return	array	parameter values
	 */
	private function getParams()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__extensions'));	
		$query->where($db->quoteName('name').' = '.$db->quote('plg_cacodetemplates_j25standard'));		
		$query->where($db->quoteName('type').' = '.$db->quote('plugin'));

		$db->setQuery($query->__toString());
		
		$params = json_decode( $db->loadResult(), true );
		return $params;
	}
	/**
	 * sets parameter values in the component's row of the extension table
	 * 
     * @param	array	param array for the extension
	 * 
	 */
	private function setParams($param_array) {
		if ( count($param_array) > 0 )
		{
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$params = $this->getParams();
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value )
			{
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$params_string = json_encode( $params );
			
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params').' = '.$db->quote( $params_string ));
			$query->where($db->quoteName('name').' = '.$db->quote('plg_cacodetemplates_j25standard'));
			$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
			$db->setQuery($query->__toString());
			
			$db->execute();
		}
	}    
}
