<?php
/**
 * @version 		$Id: componentarchitect_install.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (http://www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.install
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			Id: architectcomp_install.php 48 2012-06-26 14:16:25Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.install
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
 * @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @CAlicense		GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
/**
 * Script file of ArchitectComp_name component
 */
class com_componentarchitectInstallerScript
{
    /**
     * method to install the component
     * 
     * @param	object	parent installer application
     *
     * @return void
     */
    function install($parent) 
    {
        $manifest = $parent->get("manifest");
        $parent = $parent->getParent();
        $source = $parent->getPath("source");
        
        $db = JFactory::getDbo();       
  		$query = $db->getQuery(true);
      
		$install_html_file = __DIR__ . '/componentarchitect_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= file_get_contents($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="componentarchitectinstall-info">
			<h1><?php echo JText::_('COM_COMPONENTARCHITECT_INSTALL_HEADER'); ?></h1>
			<div id="componentarchitectinstall-intro">
				<?php echo JText::_('COM_COMPONENTARCHITECT_INSTALL_INTRO'); ?>
			</div>
			<table id="componentarchitectinstall-table" class="adminlist">
				<thead class="componentarchitectinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_COMPONENTARCHITECT_INSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="componentarchitectinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_COMPONENTARCHITECT_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="componentarchitectinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_COMPONENTARCHITECT');?>
						</td>
						<td class="componentarchitectinstall-success">
							<?php echo JText::_('COM_COMPONENTARCHITECT_INSTALL_PACKAGE_SUCCESS');?>
						</td>
					</tr>
					<tr>				
						<td colspan="3">
							<?php echo JText::_('COM_COMPONENTARCHITECT_INSTALL_CORE_COMPONENT_SUCCESS');?>
						</td>
					</tr>
		<?php
		$buffer .= ob_get_clean();

        // Install plugins
		
		if (count($manifest->plugins->plugin) > 0)
		{
			// Opening HTML
			ob_start();            
			?>
			<tr class="componentarchitectinstall-subheading">
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
				</th>					
			</tr>

			<?php
			$buffer .= ob_get_clean();		
			          
			foreach($manifest->plugins->plugin as $plugin)
			{
				$attributes = $plugin->attributes();
				$plg = $source.'/'.$attributes['folder'].'/'.$attributes['plugin'];

				$installer = new JInstaller();
				 
				if (!$installer->install($plg))
				{
					$error_msg = '';
					while ($error = JError::getError(true))
					{
						$error_msg .= $error;
						$install_error = true;
					}
					$buffer .= $this->printError($attributes['plugin'], $attributes['group'], 'install', $error_msg);
					//$this->abort();
					break;
				}
				else
				{
					$buffer .= $this->printSuccess($attributes['plugin'], $attributes['group'], 'install');
				}              
	            
 				$query->clear();
				$query->update($db->quoteName('#__extensions'));
				//Set any other field values as required
				$query->set($db->quoteName('enabled').' = 1');
				$query->where($db->quoteName('name').' = '.$db->quote($attributes['plugin']));
				$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
				$db->setQuery($query->__toString());
	            try
	            {
					$db->execute();
					$buffer .= $this->printSuccess($attributes['plugin'], $attributes['group'], 'publish');
				}
				catch (RuntimeException $e)
				{
					$install_error = true;
					$buffer .= $this->printError($attributes['plugin'], $attributes['group'], 'publish',  $e->getMessage());
				}
			}
		}  

		// Install modules
 		if (count($manifest->modules->module) > 0)
		{
			// Opening HTML
			ob_start();            
			?>
			<tr class="componentarchitectinstall-subheading">
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
				</th>					
			</tr>

			<?php
			$buffer .= ob_get_clean();		

			foreach($manifest->modules->module as $module)
			{
				$error_msg = '';
				$attributes = $module->attributes();
				$mod = $source.'/'.$attributes['folder'].'/'.$attributes['module'];
		
		        $installer = new JInstaller(); 
	            
				if (!$installer->install($mod))
				{
					while ($error = JError::getError(true))
					{
						$error_msg .= $error;
						$install_error = true;
					}
					$buffer .= $this->printError($attributes['module'], 'site', 'install', $error_msg);
					//$this->abort();
					break;
				}
				else
				{
					$buffer .= $this->printSuccess($attributes['module'], 'site', 'install');
				}  
			}
		}  


		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			// Populate the content types for UCM
			$this->populateUCM();
		}			
        // Closing HTML
			ob_start();
	?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" text-align="center">
							<?php if ($install_error) : ?>
								<div id="componentarchitectinstall-component-error">
									<?php echo JText::_('COM_COMPONENTARCHITECT_INSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="componentarchitectinstall-component-success">
									<?php echo JText::_('COM_COMPONENTARCHITECT_INSTALL_COMPONENT_SUCCESS'); ?>
								</div>			
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" text-align="center">
							<?php echo JText::_('COM_COMPONENTARCHITECT_JOOMLA_LOGO_DISCLAIMER'); ?>	
						</td>				
					</tr>
				</tfoot>
			</table>					
		</div>
		 <?php
		$buffer .= ob_get_clean();


    // Return stuff
		echo $buffer;		
	
    }

    /**
     * method to uninstall the component
     *
     * @param	object	parent installer application
     *
     * @return void
     */
    function uninstall($parent) 
    {
            // $parent is the class calling this method
        $manifest = $parent->get("manifest");
        $parent = $parent->getParent();
		$uninstall_html_file = __DIR__ . '/componentarchitect_uninstall.html';

		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);

        $buffer = '';

		// Drop out Style
		if (file_exists($uninstall_html_file))
		{
			$buffer .= file_get_contents($uninstall_html_file);
		}
        
        $install_error = false;
		             
		// Opening HTML
		ob_start();            
     ?>
	<div id="componentarchitectinstall-info">
		<h1><?php echo JText::_('COM_COMPONENTARCHITECT_UNINSTALL_HEADER'); ?></h1>
			<table id="componentarchitectinstall-table" class="adminlist">
				<thead class="componentarchitectinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_COMPONENTARCHITECT_UNINSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="componentarchitectinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_COMPONENTARCHITECT_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="componentarchitectinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_COMPONENTARCHITECT');?>
						</td>
						<td class="componentarchitectinstall-success">
							<?php echo JText::_('COM_COMPONENTARCHITECT_UNINSTALL_PACKAGE_SUCCESS');?>
						</td>
					</tr>
	<?php
		$buffer .= ob_get_clean();  
        // Uninstall plugins
		if (count($manifest->plugins->plugin) > 0)
		{
			// Opening HTML
			ob_start();            
			?>
			<tr class="componentarchitectinstall-subheading">
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
				</th>					
			</tr>

			<?php
			$buffer .= ob_get_clean();		
		        
			foreach($manifest->plugins->plugin as $plugin)
			{
				$attributes = $plugin->attributes();

 				$query->clear();
				$query->select($db->quoteName('extension_id'));
				$query->from($db->quoteName('#__extensions'));
				$query->where($db->quoteName('name').' = '.$db->quote($attributes['plugin']));
				$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
				$db->setQuery($query->__toString());
							
				$plg_id = $db->loadResult(); 
				if ($plg_id) 
				{
					$installer = new JInstaller(); 
				
					if (!$installer->uninstall('plugin', $plg_id))
					{
						$error_msg = '';
						while ($error = JError::getError(true))
						{
							$error_msg .= $error;
							$install_error = true;
						}
						$buffer .= $this->printError($attributes['plugin'], $attributes['group'], 'uninstall', $error_msg);
						//$this->abort();
						break;
					}
					else
					{
						$buffer .= $this->printSuccess($attributes['plugin'], $attributes['group'], 'uninstall');
					} 				
				}
			}  
		}  
		
		// Uninstall modules
 		if (count($manifest->modules->module) > 0)
		{
			// Opening HTML
			ob_start();            
			?>
			<tr class="componentarchitectinstall-subheading">
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
				</th>					
			</tr>

			<?php
			$buffer .= ob_get_clean();		
				
			foreach($manifest->modules->module as $module)
			{
				$attributes = $module->attributes();

 				$query->clear();
				$query->select($db->quoteName('extension_id'));
				$query->from($db->quoteName('#__extensions'));
				$query->where($db->quoteName('name').' = '.$db->quote($attributes['module']));
				$query->where($db->quoteName('type').' = '.$db->quote('module'));
				$db->setQuery($query->__toString());
							
				$mod_id = $db->loadResult(); 
				if ($mod_id) 
				{
					$installer = new JInstaller(); 
				
					if (!$installer->uninstall('module', $mod_id))
					{
						$error_msg = '';
						while ($error = JError::getError(true))
						{
							$error_msg .= $error;
							$install_error = true;
						}
						$buffer .= $this->printError($attributes['module'], 'site', 'uninstall', $error_msg);
						//$this->abort();
						break;
					}
					else
					{
						$buffer .= $this->printSuccess($attributes['module'], 'site', 'uninstall');
					} 					
				}
			}    
		}  

		//  Ensure all folders are deleted
		JFolder::delete(JPATH_SITE.'/images/componentarchitect'); 
		JFolder::delete(JPATH_SITE.'/plugins/componentarchitect'); 
		//[%%START_CUSTOM_CODE%%]
		JFolder::delete(JPATH_SITE.'/plugins/cacodetemplates'); 
		//[%%END_CUSTOM_CODE%%]
				
		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			$db->setQuery(
							'DELETE FROM '.$db->quoteName('#__content_types')
							.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_componentarchitect.component')
						 );

			$db->execute(); 
			$db->setQuery(
							'DELETE FROM '.$db->quoteName('#__content_types')
							.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_componentarchitect.componentobject')
						 );

			$db->execute(); 
			$db->setQuery(
							'DELETE FROM '.$db->quoteName('#__content_types')
							.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_componentarchitect.fieldset')
						 );

			$db->execute(); 
			$db->setQuery(
							'DELETE FROM '.$db->quoteName('#__content_types')
							.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_componentarchitect.field')
						 );

			$db->execute(); 
			$db->setQuery(
							'DELETE FROM '.$db->quoteName('#__content_types')
							.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_componentarchitect.fieldtype')
						 );

			$db->execute(); 
			$db->setQuery(
							'DELETE FROM '.$db->quoteName('#__content_types')
							.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_componentarchitect.codetemplate')
						 );

			$db->execute(); 
			

			$db->setQuery(
							'DELETE FROM '.$db->quoteName('#__content_types')
							.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_componentarchitect.category')
						 );

			$db->execute(); 
		}			
        // Closing HTML
			ob_start();
		?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" text-align="center">
							<?php if ($install_error) : ?>
								<div id="componentarchitectinstall-component-error">
									<?php echo JText::_('COM_COMPONENTARCHITECT_UNINSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="componentarchitectinstall-component-success">
									<?php echo JText::_('COM_COMPONENTARCHITECT_UNINSTALL_COMPONENT_SUCCESS'); ?>
								</div>			
							<?php endif; ?>
						</td>
					</tr>
				</tfoot>
			</table>					
		</div>		
		 <?php
		 $buffer .= ob_get_clean();


		// Return stuff
		echo $buffer;
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
        $manifest = $parent->get("manifest");
        $parent = $parent->getParent();
        $source = $parent->getPath("source");

        $db = JFactory::getDbo();
 		$query = $db->getQuery(true);
        
       
		$install_html_file = __DIR__ . '/componentarchitect_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= file_get_contents($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="componentarchitectinstall-info">
			<h1><?php echo JText::_('COM_COMPONENTARCHITECT_UPDATE_HEADER'); ?></h1>
			<table id="componentarchitectinstall-table" class="adminlist">
				<thead class="componentarchitectinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_COMPONENTARCHITECT_UPDATE_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="componentarchitectinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_COMPONENTARCHITECT_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="componentarchitectinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_COMPONENTARCHITECT');?>
						</td>
						<td class="componentarchitectinstall-success">
							<?php echo JText::_('COM_COMPONENTARCHITECT_UPDATE_PACKAGE_SUCCESS');?>
						</td>
					</tr>					
		<?php
		$buffer .= ob_get_clean();  
			          
        // Install plugins
        
		if (count($manifest->plugins->plugin) > 0)
		{
			// Opening HTML
			ob_start();            
			?>
			<tr class="componentarchitectinstall-subheading">
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
				</th>					
			</tr>

			<?php
			$buffer .= ob_get_clean();		
		        
			foreach($manifest->plugins->plugin as $plugin)
			{
				$attributes = $plugin->attributes();
				$plg = $source.'/'.$attributes['folder'].'/'.$attributes['plugin'];
	            
				// check if the plugin is a new version for this extension or a new plugin and either update or install
 				$query->clear();
				$query->select($db->quoteName('extension_id'));
				$query->from($db->quoteName('#__extensions'));
				$query->where($db->quoteName('name').' = '.$db->quote($attributes['plugin']));
				$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
				$db->setQuery($query->__toString());
							
				$plg_id = $db->loadResult(); 
				if ($plg_id) 
				{
					$plg_type = 'update';
				}
				else
				{          
					$plg_type = 'install';
				}

				$installer = new JInstaller();
				
				if (!$installer->$plg_type($plg))
				{
					$error_msg = '';
					while ($error = JError::getError(true))
					{
						$error_msg .= $error;
						$install_error = true;
					}
					$buffer .= $this->printError($attributes['plugin'], $attributes['group'], $plg_type, $error_msg);
					//$this->abort();
					break;
				}
				else
				{
					$buffer .= $this->printSuccess($attributes['plugin'], $attributes['group'], $plg_type);
				}              
				if ($plg_type == 'install')
				{

 					$query->clear();
					$query->update($db->quoteName('#__extensions'));
					//Set any other field values as required
					$query->set($db->quoteName('enabled').' = 1');
					$query->where($db->quoteName('name').' = '.$db->quote($attributes['plugin']));
					$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
					$db->setQuery($query->__toString());

					try
					{
						$db->execute();
						$buffer .= $this->printSuccess($attributes['plugin'], $attributes['group'], 'publish');
					}
					catch (RuntimeException $e)
					{
						$install_error = true;
						$buffer .= $this->printError($attributes['plugin'], $attributes['group'], 'publish',  $e->getMessage());
					}					
				}
			}
		}  
        
		// Install modules
 		if (count($manifest->modules->module) > 0)
		{
			// Opening HTML
			ob_start();            
			?>
			<tr class="componentarchitectinstall-subheading">
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_COMPONENTARCHITECT_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_COMPONENTARCHITECT_STATUS_HEADER');?>
				</th>					
			</tr>

			<?php
			$buffer .= ob_get_clean();		
				
			foreach($manifest->modules->module as $module)
			{
				$error_msg = '';
				$attributes = $module->attributes();
				$mod = $source.'/'.$attributes['folder'].'/'.$attributes['module'];
	            
				// check if the module is a new version for this externsion or a new plugin and either update or install
 				$query->clear();
				$query->select($db->quoteName('extension_id'));
				$query->from($db->quoteName('#__extensions'));
				$query->where($db->quoteName('name').' = '.$db->quote($attributes['module']));
				$query->where($db->quoteName('type').' = '.$db->quote('module'));
				$db->setQuery($query->__toString());
							
				$mod_id = $db->loadResult(); 
 				if ($mod_id) 
				{
					$mod_type = 'update';
				}
				else
				{
					$mod_type = 'install';
				}
	           
				if (!$installer->$mod_type($mod))
				{
					while ($error = JError::getError(true))
					{
						$error_msg .= $error;
						$install_error = true;
					}
					$buffer .= $this->printError($attributes['module'], 'site', $mod_type, $error_msg);
					//$this->abort();
					break;
				}
				else
				{
					$buffer .= $this->printSuccess($attributes['module'], 'site',$mod_type);
				}  
			}
		}  
		   
        // Closing HTML
			ob_start();
		?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" text-align="center">
							<?php if ($install_error) : ?>
								<div id="componentarchitectinstall-component-error">
									<?php echo JText::_('COM_COMPONENTARCHITECT_UPDATE_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="componentarchitectinstall-component-success">
									<?php echo JText::_('COM_COMPONENTARCHITECT_UPDATE_COMPONENT_SUCCESS'); ?>
								</div>			
							<?php endif; ?>
						</td>
					</tr>
				</tfoot>
			</table>					
		</div>		
		 <?php
		 $buffer .= ob_get_clean();


		// Return stuff
		echo $buffer;		
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
		$joomla_version = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Manifest file minimum Joomla! version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// abort if the current Joomla! release is older
		if( version_compare( $joomla_version->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_COMPONENT_ERROR_WRONG_JOOMLA_VERSION',$this->minimum_joomla_release));
			return false;
		}
		switch ($type)
		{
			case 'install' :
				break; 
			case 'uninstall' :
				break; 
			case 'update' :
				$manifest = $this->getManifest();
				$old_release = $manifest['version'];
				$rel = $old_release . ' to ' . $this->release;
				// abort if the component being installed is not newer than the currently installed version		
				if ( version_compare( $this->release, $old_release, 'lt' ) )
				{
					Jerror::raiseWarning(null, JTEXT::sprintf('COM_COMPONENTARCHITECT_UPDATE_COMPONENT_ERROR_WRONG_VERSION_SEQUENCE', $rel));
					return false;
				}			
			default :
				break; 
		}	
		return true;        
    }

    /**
     * method to run after an install/update/uninstall method
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
				//[%%START_CUSTOM_CODE%%]
	
				$db = JFactory::getDbo();
				$date	= JFactory::getDate();		
				$user = JFactory::getUser();	
				
				// Create Category records
				$install_categories = array();
				$install_categories[] = array('dummy_id' => 10101, 'id' => 0, 'parent_id' => 1, 'level' => 1, 'path' => 'joomla-code-templates', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! Code Templates', 'alias' => 'joomla-code-templates', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10102, 'id' => 0, 'parent_id' => 1, 'level' => 1, 'path' => 'joomla-field-types', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! Field Types', 'alias' => 'joomla-field-types', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10103, 'id' => 0, 'parent_id' => 1, 'level' => 1, 'path' => 'ca-field-types', 'extension' => 'com_componentarchitect', 'title' => 'CA Field Types', 'alias' => 'ca-field-types', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10104, 'id' => 0, 'parent_id' => 1, 'level' => 1, 'path' => 'mysql-field-types', 'extension' => 'com_componentarchitect', 'title' => 'MySQL Field Types', 'alias' => 'mysql-field-types', 'description' => '', 'published' => 1, 'access' => 1);

				$install_categories[] = array('dummy_id' => 10211, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-2-5-packages', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 2.5 Packages', 'alias' => 'joomla-2-5-packages', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10212, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-2-5-components', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 2.5 Components', 'alias' => 'joomla-2-5-components', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10212, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-2-5-plugins', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 2.5 Plugins', 'alias' => 'joomla-2-5-plugins', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10214, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-2-5-modules', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 2.5 Modules', 'alias' => 'joomla-2-5-modules', 'description' => '', 'published' => 1, 'access' => 1);

				$install_categories[] = array('dummy_id' => 10221, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-3-x-packages', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 3.x Packages', 'alias' => 'joomla-3-x-packages', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10222, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-3-x-components', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 3.x Components', 'alias' => 'joomla-3-x-components', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10223, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-3-x-plugins', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 3.x Plugins', 'alias' => 'joomla-3-x-plugins', 'description' => '', 'published' => 1, 'access' => 1);
				$install_categories[] = array('dummy_id' => 10224, 'id' => 0, 'parent_id' => 10101, 'level' => 2, 'path' => 'joomla-code-templates/joomla-3-x-modules', 'extension' => 'com_componentarchitect', 'title' => 'Joomla! 3.x Modules', 'alias' => 'joomla-3-x-modules', 'description' => '', 'published' => 1, 'access' => 1);

				for ($i = 0; $i < count($install_categories); $i++)
				{
					$category_table = JTable::getInstance('Category');
				
					$bind_array['parent_id'] = $install_categories[$i]['parent_id'];
					$bind_array['level'] = $install_categories[$i]['level'];
					$bind_array['path'] = $install_categories[$i]['path'];
					$bind_array['extension'] = $install_categories[$i]['extension'];
					$bind_array['title'] = $install_categories[$i]['title'];
					$bind_array['alias'] = $install_categories[$i]['alias'];
					$bind_array['description'] = $install_categories[$i]['description'];
					$bind_array['published'] = $install_categories[$i]['published'];
					$bind_array['access'] = $install_categories[$i]['access'];
					$bind_array['created_user_id'] = $user->id;
					$bind_array['created_time'] = $date->toSQL();
					$bind_array['language'] = '*';
				
					$category_table->setLocation($install_categories[$i]['parent_id'], 'last-child');

					$category_table->bind($bind_array);
					
					$category_table->setRules('{}');

					if ($category_table->check())
					{
						$result = $category_table->store();
					}
					else
					{
						$result = false;
					}	
					
					if (!$result)
					{
						JFactory::getApplication()->enqueueMessage(JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_ERROR_ON_CATEGORY_INSERT', $install_categories[$i]['title'] ), 'warning');
					}
					else
					{
						$install_categories[$i]['id'] = $category_table->id;
						
						for ($j = $i+1; $j < count($install_categories); $j++)
						{
							if ($install_categories[$j]['parent_id'] == $install_categories[$i]['dummy_id'] AND
								$install_categories[$j]['level'] == $install_categories[$i]['level'] + 1)
							{
								$install_categories[$j]['parent_id'] = $install_categories[$i]['id'];
							}
						}
					}
				}

				// Update category id's on field types
				$db->setQuery(
					'SELECT * FROM '.$db->quoteName('#__componentarchitect_fieldtypes')
				);
				$field_types = $db->loadObjectList(); 
				
				foreach ($field_types as $field_type)
				{
					$cat_id = 0;
					foreach ($install_categories as $category)
					{
						if ($category['dummy_id'] == $field_type->catid)
						{
							$cat_id =  $category['id'];
							break;
						}
					}
		 
					try
					{
						$db->setQuery
						(
							'UPDATE '.$db->quoteName('#__componentarchitect_fieldtypes').
							' SET '.$db->quoteName('catid').' = '.$cat_id.
							', '.$db->quoteName('created_by').' = '.$user->id.
							', '.$db->quoteName('created').' = '.$db->quote($date->toSQL()).
							', '.$db->quoteName('modified_by').' = '.$user->id.
							', '.$db->quoteName('modified').' = '.$db->quote($date->toSQL()).
							' WHERE '.$db->quoteName('id').' = '.$field_type->id
						);
						$db->execute();
					}			
					catch (RuntimeException $e)
					{
						JFactory::getApplication()->enqueueMessage(JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_ERROR_ON_FIELD_TYPE_UPDATE', $field_type->name), 'warning');
					}				
				}
							
				// Update category id's on code template
				$db->setQuery
				(
					'SELECT * FROM '.$db->quoteName('#__componentarchitect_codetemplates')
				);
				$code_templates = $db->loadObjectList(); 
				
				foreach ($code_templates as $code_template)
				{
					$cat_id = 0;
					foreach ($install_categories as $category)
					{
						if ($category['dummy_id'] == $code_template->catid)
						{
							$cat_id =  $category['id'];
							break;
						}
					}

					try
					{
						$db->setQuery
						(
							'UPDATE '.$db->quoteName('#__componentarchitect_codetemplates').
							' SET '.$db->quoteName('catid').' = '.$cat_id.
							' WHERE '.$db->quoteName('id').' = '.$code_template->id
						);
						$db->execute();
					}			
					catch (RuntimeException $e)
					{
						JFactory::getApplication()->enqueueMessage(JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_ERROR_ON_CODE_TEMPLATE_UPDATE', $code_template->name), 'warning');
					}			
				}
				//[%%END_CUSTOM_CODE%%]					
				
				if (JFolder::exists(JPATH_SITE.'/components/com_componentarchitect'))
				{
					JFolder::delete(JPATH_SITE.'/components/com_componentarchitect');
				}
									
				JFolder::create(JPATH_SITE.'/images/componentarchitect'); 
				JFile::copy('administrator/components/com_componentarchitect/index.html','images/componentarchitect/index.html', JPATH_SITE);
				
				// Make sure index.html files in all folders
				if (JFolder::exists(JPATH_SITE.'/plugins/componentarchitect') AND !JFile::exists(JPATH_SITE.'/plugins/componentarchitect/index.html'))
				{
					JFile::copy('administrator/components/com_componentarchitect/index.html','plugins/componentarchitect/index.html', JPATH_SITE);
				}	

				break;  
			case 'update' :
			
				$this->updateComponentConditions();
				$this->updateComponentObjectConditions();
				$this->updateEditorFieldFilter();
			
				// Define in an array the param updates required and then use the setParams function to update them in the extensions table
				// Repeat for each change
				$params_array = array('helpURL' => 'http://help.componentarchitect.com/componentarchitect/v_1_1/{language}/j{major}x/{keyref}.html');
				$this->setParams($params_array);

				//if (version_compare(JVERSION, '3.0', 'lt'))
				//{
				//	// Need to remove some parameters that are not valid in Joomla! 2.5
				//	$params_array = array();
				//	$this->setParams($params_array, true);
				//}					
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
		$query->where($db->quoteName('name').' = '.$db->quote('com_componentarchitect'));		
		$query->where($db->quoteName('type').' = '.$db->quote('component'));

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
		$query->where($db->quoteName('name').' = '.$db->quote('com_componentarchitect'));		
		$query->where($db->quoteName('type').' = '.$db->quote('component'));

		$db->setQuery($query->__toString());
		
		$params = json_decode( $db->loadResult(), true );
		return $params;
	}
	/**
	 * sets parameter values in the component's row of the extension table
	 * 
     * @param	array	$params_array	param array for the extension
     * @param	boolean	$remove			Flag set to false to add to extension params and true to remove
	 * 
	 */
	private function setParams($params_array, $remove = false)
	{
		if ( count($params_array) > 0 )
		{
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$params = $this->getParams();
			// add the new variable(s) to the existing one(s)
			foreach ( $params_array as $name => $value )
			{
				if ($remove)
				{
					unset($params[ (string) $name ]);
				}
				else
				{
					$params[ (string) $name ] = (string) $value;
				}
			}
			// store the combined new and existing values back as a JSON string
			$params_string = json_encode( $params );
			
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params').' = '.$db->quote( $params_string ));
			$query->where($db->quoteName('name').' = '.$db->quote('com_componentarchitect'));
			$query->where($db->quoteName('type').' = '.$db->quote('component'));

			$db->setQuery($query->__toString());
			
			$db->execute();
		}
	} 	   
    /**
     * method to output an error message for one of the packages in the component
     *
     * @param	string	the package being installed
     * @param	string	group - e.g. table name, or plugin group or part of site
     * @param	string	type of action - install, publish, update and uninstall
     * @param	string	the error message to display
     *
     * @return void
     */    
    private function printError($package, $group, $action, $msg)
    {
        ob_start();
        ?>
	<tr class="componentarchitectinstall-row">
		<td>
			<?php echo $package;?>
		</td>
		<td>
			<?php echo $group;?>
		</td>								
		<td class="componentarchitectinstall-error">
			<div>
				<?php echo JText::_('COM_COMPONENTARCHITECT_'.strtoupper($action).'_PACKAGE_ERROR');?><br />
				<span class="componentarchitectinstall-errormsg">
					<?php echo $msg; ?>
				</span>	
			</div>		
		</td>
	</tr>    
    <?php
            $out = ob_get_clean();
        return $out;
    }
    /**
     * method to output a successful install message for one of the packages in the component
     *
     * @param	string	the package being installed
     * @param	string	group - e.g. table name, or plugin group or part of site
     * @param	string	type of action - install, publish, update and uninstall
  	 *
     * @return void
     */   
    private function printSuccess($package, $group, $action)
    {
        ob_start();
        ?>
		<tr class="componentarchitectinstall-row">
			<td>
				<?php echo $package;?>
			</td>
			<td>
				<?php echo $group;?>
			</td>								
			<td class="componentarchitectinstall-success">
				<div><?php echo JText::_('COM_COMPONENTARCHITECT_'.strtoupper($action).'_PACKAGE_SUCCESS');?></div>
			</td>
		</tr> 		
		<?php
            $out = ob_get_clean();
        return $out;
    }
    /**
     * function to add entries for the component tables to the ucm content type table
     *
     * @return void
     */   
    private function populateUCM()
    {
        $db = JFactory::getDbo();       
    
		$content_type = array();
		$content_type['type_title'] = 'Component/Extension';
		$content_type['type_alias'] = 'com_componentarchitect.component';
		$content_type['table'] = '{"special":{"dbtable":"componentarchitect_components","key":"id","type":"Component/Extension","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
		$content_type['field_mappings'] .= '"core_title":"name",';
		$content_type['field_mappings'] .= '"core_state":"state",';
		$content_type['field_mappings'] .= '"core_alias":"null",';
		$content_type['field_mappings'] .= '"core_created_time":"created",';
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
		$content_type['field_mappings'] .= '"core_body":"description",';
		$content_type['field_mappings'] .= '"core_hits":"null",';
		$content_type['field_mappings'] .= '"core_publish_up":"null",';
		$content_type['field_mappings'] .= '"core_publish_down":"null",';
		$content_type['field_mappings'] .= '"core_access":"null",';
		$content_type['field_mappings'] .= '"core_params":"null",';
		$content_type['field_mappings'] .= '"core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"null",';
		$content_type['field_mappings'] .= '"core_language":"null",';
		$content_type['field_mappings'] .= '"core_images":"null",';
		$content_type['field_mappings'] .= '"core_urls":"null",';
		$content_type['field_mappings'] .= '"core_version":"null",';
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
		$content_type['field_mappings'] .= '"core_metakey":"null",';
		$content_type['field_mappings'] .= '"core_metadesc":"null",';
		$content_type['field_mappings'] .= '"core_catid":"null",';
		$content_type['field_mappings'] .= '"core_xreference":"null",';
		$content_type['field_mappings'] .= '"asset_id":"null"';
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = 'ComponentArchitectHelperRoute::getComponentRoute';

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router'])
						  .');'
					  );

		$db->execute(); 
		$content_type = array();
		$content_type['type_title'] = 'Object/Table';
		$content_type['type_alias'] = 'com_componentarchitect.componentobject';
		$content_type['table'] = '{"special":{"dbtable":"componentarchitect_componentobjects","key":"id","type":"Object/Table","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
		$content_type['field_mappings'] .= '"core_title":"name",';
		$content_type['field_mappings'] .= '"core_state":"state",';
		$content_type['field_mappings'] .= '"core_alias":"null",';
		$content_type['field_mappings'] .= '"core_created_time":"created",';
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
		$content_type['field_mappings'] .= '"core_body":"description",';
		$content_type['field_mappings'] .= '"core_hits":"null",';
		$content_type['field_mappings'] .= '"core_publish_up":"null",';
		$content_type['field_mappings'] .= '"core_publish_down":"null",';
		$content_type['field_mappings'] .= '"core_access":"null",';
		$content_type['field_mappings'] .= '"core_params":"null",';
		$content_type['field_mappings'] .= '"core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"null",';
		$content_type['field_mappings'] .= '"core_language":"null",';
		$content_type['field_mappings'] .= '"core_images":"null",';
		$content_type['field_mappings'] .= '"core_urls":"null",';
		$content_type['field_mappings'] .= '"core_version":"null",';
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
		$content_type['field_mappings'] .= '"core_metakey":"null",';
		$content_type['field_mappings'] .= '"core_metadesc":"null",';
		$content_type['field_mappings'] .= '"core_catid":"null",';
		$content_type['field_mappings'] .= '"core_xreference":"null",';
		$content_type['field_mappings'] .= '"asset_id":"null"';
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = 'ComponentArchitectHelperRoute::getComponentObjectRoute';

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router'])
						  .');'
					  );

		$db->execute(); 
		$content_type = array();
		$content_type['type_title'] = 'Fieldset';
		$content_type['type_alias'] = 'com_componentarchitect.fieldset';
		$content_type['table'] = '{"special":{"dbtable":"componentarchitect_fieldsets","key":"id","type":"Fieldset","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
		$content_type['field_mappings'] .= '"core_title":"name",';
		$content_type['field_mappings'] .= '"core_state":"state",';
		$content_type['field_mappings'] .= '"core_alias":"null",';
		$content_type['field_mappings'] .= '"core_created_time":"created",';
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
		$content_type['field_mappings'] .= '"core_body":"description",';
		$content_type['field_mappings'] .= '"core_hits":"null",';
		$content_type['field_mappings'] .= '"core_publish_up":"null",';
		$content_type['field_mappings'] .= '"core_publish_down":"null",';
		$content_type['field_mappings'] .= '"core_access":"null",';
		$content_type['field_mappings'] .= '"core_params":"null",';
		$content_type['field_mappings'] .= '"core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"null",';
		$content_type['field_mappings'] .= '"core_language":"null",';
		$content_type['field_mappings'] .= '"core_images":"null",';
		$content_type['field_mappings'] .= '"core_urls":"null",';
		$content_type['field_mappings'] .= '"core_version":"null",';
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
		$content_type['field_mappings'] .= '"core_metakey":"null",';
		$content_type['field_mappings'] .= '"core_metadesc":"null",';
		$content_type['field_mappings'] .= '"core_catid":"null",';
		$content_type['field_mappings'] .= '"core_xreference":"null",';
		$content_type['field_mappings'] .= '"asset_id":"null"';
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = 'ComponentArchitectHelperRoute::getFieldsetRoute';

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router'])
						  .');'
					  );

		$db->execute(); 
		$content_type = array();
		$content_type['type_title'] = 'Field';
		$content_type['type_alias'] = 'com_componentarchitect.field';
		$content_type['table'] = '{"special":{"dbtable":"componentarchitect_fields","key":"id","type":"Field","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
		$content_type['field_mappings'] .= '"core_title":"name",';
		$content_type['field_mappings'] .= '"core_state":"state",';
		$content_type['field_mappings'] .= '"core_alias":"null",';
		$content_type['field_mappings'] .= '"core_created_time":"created",';
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
		$content_type['field_mappings'] .= '"core_body":"description",';
		$content_type['field_mappings'] .= '"core_hits":"null",';
		$content_type['field_mappings'] .= '"core_publish_up":"null",';
		$content_type['field_mappings'] .= '"core_publish_down":"null",';
		$content_type['field_mappings'] .= '"core_access":"null",';
		$content_type['field_mappings'] .= '"core_params":"null",';
		$content_type['field_mappings'] .= '"core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"null",';
		$content_type['field_mappings'] .= '"core_language":"null",';
		$content_type['field_mappings'] .= '"core_images":"null",';
		$content_type['field_mappings'] .= '"core_urls":"null",';
		$content_type['field_mappings'] .= '"core_version":"null",';
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
		$content_type['field_mappings'] .= '"core_metakey":"null",';
		$content_type['field_mappings'] .= '"core_metadesc":"null",';
		$content_type['field_mappings'] .= '"core_catid":"null",';
		$content_type['field_mappings'] .= '"core_xreference":"null",';
		$content_type['field_mappings'] .= '"asset_id":"null"';
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = 'ComponentArchitectHelperRoute::getFieldRoute';

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router'])
						  .');'
					  );

		$db->execute(); 
		$content_type = array();
		$content_type['type_title'] = 'Field Type';
		$content_type['type_alias'] = 'com_componentarchitect.fieldtype';
		$content_type['table'] = '{"special":{"dbtable":"componentarchitect_fieldtypes","key":"id","type":"Field Type","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
		$content_type['field_mappings'] .= '"core_title":"name",';
		$content_type['field_mappings'] .= '"core_state":"state",';
		$content_type['field_mappings'] .= '"core_alias":"null",';
		$content_type['field_mappings'] .= '"core_created_time":"created",';
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
		$content_type['field_mappings'] .= '"core_body":"description",';
		$content_type['field_mappings'] .= '"core_hits":"null",';
		$content_type['field_mappings'] .= '"core_publish_up":"null",';
		$content_type['field_mappings'] .= '"core_publish_down":"null",';
		$content_type['field_mappings'] .= '"core_access":"null",';
		$content_type['field_mappings'] .= '"core_params":"null",';
		$content_type['field_mappings'] .= '"core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"null",';
		$content_type['field_mappings'] .= '"core_language":"null",';
		$content_type['field_mappings'] .= '"core_images":"null",';
		$content_type['field_mappings'] .= '"core_urls":"null",';
		$content_type['field_mappings'] .= '"core_version":"null",';
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
		$content_type['field_mappings'] .= '"core_metakey":"null",';
		$content_type['field_mappings'] .= '"core_metadesc":"null",';
		$content_type['field_mappings'] .= '"core_catid":"catid",';
		$content_type['field_mappings'] .= '"core_xreference":"null",';
		$content_type['field_mappings'] .= '"asset_id":"null"';
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = 'ComponentArchitectHelperRoute::getFieldTypeRoute';

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router'])
						  .');'
					  );

		$db->execute(); 
		$content_type = array();
		$content_type['type_title'] = 'Code Template';
		$content_type['type_alias'] = 'com_componentarchitect.codetemplate';
		$content_type['table'] = '{"special":{"dbtable":"componentarchitect_codetemplates","key":"id","type":"Code Template","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
		$content_type['field_mappings'] .= '"core_title":"name",';
		$content_type['field_mappings'] .= '"core_state":"state",';
		$content_type['field_mappings'] .= '"core_alias":"null",';
		$content_type['field_mappings'] .= '"core_created_time":"created",';
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
		$content_type['field_mappings'] .= '"core_body":"description",';
		$content_type['field_mappings'] .= '"core_hits":"null",';
		$content_type['field_mappings'] .= '"core_publish_up":"null",';
		$content_type['field_mappings'] .= '"core_publish_down":"null",';
		$content_type['field_mappings'] .= '"core_access":"null",';
		$content_type['field_mappings'] .= '"core_params":"null",';
		$content_type['field_mappings'] .= '"core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"null",';
		$content_type['field_mappings'] .= '"core_language":"null",';
		$content_type['field_mappings'] .= '"core_images":"null",';
		$content_type['field_mappings'] .= '"core_urls":"null",';
		$content_type['field_mappings'] .= '"core_version":"null",';
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
		$content_type['field_mappings'] .= '"core_metakey":"null",';
		$content_type['field_mappings'] .= '"core_metadesc":"null",';
		$content_type['field_mappings'] .= '"core_catid":"catid",';
		$content_type['field_mappings'] .= '"core_xreference":"null",';
		$content_type['field_mappings'] .= '"asset_id":"null"';
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = 'ComponentArchitectHelperRoute::getCodeTemplateRoute';

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router'])
						  .');'
					  );

		$db->execute(); 
		
		$content_type = array();
		$content_type['type_title'] = 'Component Architect Category';
		$content_type['type_alias'] = 'com_componentarchitect.category';
		$content_type['table'] = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias",';
		$content_type['field_mappings'] .= '"core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description","core_hits":"hits",';
		$content_type['field_mappings'] .= '"core_publish_up":"null","core_publish_down":"null","core_access":"access","core_params":"params","core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"metadata","core_language":"language","core_images":"null","core_urls":"null","core_version":"version",';
		$content_type['field_mappings'] .= '"core_ordering":"null","core_metakey":"metakey","core_metadesc":"metadesc","core_catid":"parent_id","core_xreference":"null","asset_id":"asset_id"},';
		$content_type['field_mappings'] .= '	"special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';									
		$content_type['router'] = 'ComponentArchitectHelperRoute::getCategoryRoute';

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router'])
						  .');'
					  );

		$db->execute(); 
	}
    /**
     * function to update any existing Component records with new Joomla Parts or Joomla Fields/Features conditions
     *
     * @return void
     */   
    private function updateComponentConditions()
    {
        $db = JFactory::getDbo(); 
 		$query	= $db->getQuery(true);

		// Select the required components from the table.
		$query->select('a.id, a.joomla_features'); 
		$query->from($db->quoteName('#__componentarchitect_components').' AS a');

		$db->setQuery($query);

		// Return the result
		$records = $db->loadObjectList();
		
		foreach ($records as $record)
		{
			$registry = new JRegistry;
			$registry->loadString($record->joomla_features);
			$joomla_features = $registry->toArray();
			$registry = null; //release memory
			
			if (!isset($joomla_features['include_microdata']))
			{
				$joomla_features['include_microdata'] = '1';
			}
				
			$registry = new JRegistry;
			$registry->loadArray($joomla_features);
			$joomla_features = (string)$registry;
			$registry = null; //release memory	
			try
			{
				$db->setQuery
				(
					'UPDATE '.$db->quoteName('#__componentarchitect_components').
					' SET '.$db->quoteName('joomla_features').' = '.$db->quote($joomla_features).
					' WHERE '.$db->quoteName('id').' = '.$record->id
				);
				$db->execute();
			}			
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_ERROR_ON_COMPONENT_UPDATE', $record->id), 'warning');
			}						
		}		
	} 
    /**
     * function to update any existing Component Object records with new Joomla Parts or Joomla Fields/Features conditions
     *
     * @return void
     */   
    private function updateComponentObjectConditions()
    {
        $db = JFactory::getDbo(); 
 		$query	= $db->getQuery(true);

		// Select the required components from the table.
		$query->select('a.id, a.joomla_features'); 
		$query->from($db->quoteName('#__componentarchitect_componentobjects').' AS a');

		$db->setQuery($query);

		// Return the result
		$records = $db->loadObjectList();
		
		foreach ($records as $record)
		{
			$registry = new JRegistry;
			$registry->loadString($record->joomla_features);
			$joomla_features = $registry->toArray();
			$registry = null; //release memory
			
			if (!isset($joomla_features['include_microdata']))
			{
				$joomla_features['include_microdata'] = '';
			}
				
			$registry = new JRegistry;
			$registry->loadArray($joomla_features);
			$joomla_features = (string)$registry;
			$registry = null; //release memory	
			try
			{
				$db->setQuery
				(
					'UPDATE '.$db->quoteName('#__componentarchitect_componentobjects').
					' SET '.$db->quoteName('joomla_features').' = '.$db->quote($joomla_features).
					' WHERE '.$db->quoteName('id').' = '.$record->id
				);
				$db->execute();
			}			
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_ERROR_ON_COMPONENTOBJECT_UPDATE', $record->id), 'warning');
			}	
		}	
	}
    /**
     * function to update the editor field type and all fields based on it to not have field_filter so to safe_editor
     *
     * @return void
     */   
    private function updateEditorFieldFilter()
    {
        $db = JFactory::getDbo(); 
 		$query	= $db->getQuery(true);

		// Select the required components from the table.
		$query->select('a.id'); 
		$query->from($db->quoteName('#__componentarchitect_fieldtypes').' AS a');
		$query->where($db->quoteName('code_name').' = '.$db->quote('editor'));
		$db->setQuery($query);

		// Return the result
		$records = $db->loadObjectList();
		
		foreach ($records as $record)
		{
			try
			{
				$db->setQuery
				(
					'UPDATE '.$db->quoteName('#__componentarchitect_fieldtypes').
					' SET '.$db->quoteName('field_filter').' = 1,'.$db->quoteName('field_filter_default').' = \'safe_editor\''.
					' WHERE '.$db->quoteName('id').' = '.$record->id.';'
				);
				$db->execute();
			}			
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_ERROR_ON_EDITORFIELDTYPE_UPDATE', $record->id), 'warning');
			}	
			
			try
			{
				$db->setQuery
				(
					'UPDATE '.$db->quoteName('#__componentarchitect_fields').
					' SET '.$db->quoteName('field_filter').' = \'safe_editor\''.
					' WHERE '.$db->quoteName('fieldtype_id').' = '.$record->id.' AND '.$db->quoteName('field_filter').' = \'\';'

				);
				$db->execute();
			}			
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JTEXT::sprintf('COM_COMPONENTARCHITECT_INSTALL_ERROR_ON_EDITORFIELD_UPDATE', $record->id), 'warning');
			}				
		}	
	} 		 	 	
}