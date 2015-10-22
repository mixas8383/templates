<?php
/**
 * @version 		$Id:$
 * @name			App (Release 1.0.0)
 * @author			 ()
 * @package			com_app
 * @subpackage		com_app.install
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			Id: architectcomp_install.php 48 2012-06-26 14:16:25Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.install
 * @CAtemplate		joomla_3_3_standard (Release 1.0.3)
 * @CAcopyright		Copyright (c)2013 - 2014  Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
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
class com_appInstallerScript
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
      
		$install_html_file = __DIR__ . '/app_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= file_get_contents($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="appinstall-info">
			<h1><?php echo JText::_('COM_APP_INSTALL_HEADER'); ?></h1>
			<div id="appinstall-intro">
				<?php echo JText::_('COM_APP_INSTALL_INTRO'); ?>
			</div>
			<table id="appinstall-table" class="adminlist">
				<thead class="appinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_APP_INSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="appinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_APP_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_APP_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="appinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_APP');?>
						</td>
						<td class="appinstall-success">
							<?php echo JText::_('COM_APP_INSTALL_PACKAGE_SUCCESS');?>
						</td>
					</tr>
					<tr>				
						<td colspan="3">
							<?php echo JText::_('COM_APP_INSTALL_CORE_COMPONENT_SUCCESS');?>
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
			<tr class="appinstall-subheading">
				<th>
					<?php echo JText::_('COM_APP_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_APP_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_APP_STATUS_HEADER');?>
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
			<tr class="appinstall-subheading">
				<th>
					<?php echo JText::_('COM_APP_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_APP_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_APP_STATUS_HEADER');?>
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
		
		// Populate the content types for UCM
		$this->populateUCM();
			
        // Closing HTML
			ob_start();
	?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" text-align="center">
							<?php if ($install_error) : ?>
								<div id="appinstall-component-error">
									<?php echo JText::_('COM_APP_INSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="appinstall-component-success">
									<?php echo JText::_('COM_APP_INSTALL_COMPONENT_SUCCESS'); ?>
								</div>			
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" text-align="center">
							<?php echo JText::_('COM_APP_JOOMLA_LOGO_DISCLAIMER'); ?>	
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
		$uninstall_html_file = __DIR__ . '/app_uninstall.html';

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
	<div id="appinstall-info">
		<h1><?php echo JText::_('COM_APP_UNINSTALL_HEADER'); ?></h1>
			<table id="appinstall-table" class="adminlist">
				<thead class="appinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_APP_UNINSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="appinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_APP_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_APP_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="appinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_APP');?>
						</td>
						<td class="appinstall-success">
							<?php echo JText::_('COM_APP_UNINSTALL_PACKAGE_SUCCESS');?>
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
			<tr class="appinstall-subheading">
				<th>
					<?php echo JText::_('COM_APP_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_APP_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_APP_STATUS_HEADER');?>
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
			<tr class="appinstall-subheading">
				<th>
					<?php echo JText::_('COM_APP_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_APP_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_APP_STATUS_HEADER');?>
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
		JFolder::delete(JPATH_SITE.'/images/app'); 
		JFolder::delete(JPATH_SITE.'/plugins/app'); 

		$db->setQuery(
						'DELETE FROM '.$db->quoteName('#__content_types')
						.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_app.item')
					 );

		$db->execute(); 
		

		$db->setQuery(
						'DELETE FROM '.$db->quoteName('#__content_types')
						.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('com_app.category')
					 );

		$db->execute(); 
					
        // Closing HTML
			ob_start();
		?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php if ($install_error) : ?>
								<div id="appinstall-component-error">
									<?php echo JText::_('COM_APP_UNINSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="appinstall-component-success">
									<?php echo JText::_('COM_APP_UNINSTALL_COMPONENT_SUCCESS'); ?>
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
        
       
		$install_html_file = __DIR__ . '/app_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= file_get_contents($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="appinstall-info">
			<h1><?php echo JText::_('COM_APP_UPDATE_HEADER'); ?></h1>
			<table id="appinstall-table" class="adminlist">
				<thead class="appinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_APP_UPDATE_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="appinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_APP_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_APP_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="appinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_APP');?>
						</td>
						<td class="appinstall-success">
							<?php echo JText::_('COM_APP_UPDATE_PACKAGE_SUCCESS');?>
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
			<tr class="appinstall-subheading">
				<th>
					<?php echo JText::_('COM_APP_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_APP_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_APP_STATUS_HEADER');?>
				</th>					
			</tr>

			<?php
			$buffer .= ob_get_clean();		
		        
			foreach($manifest->plugins->plugin as $plugin)
			{
				$attributes = $plugin->attributes();
				$plg = $source.'/'.$attributes['folder'].'/'.$attributes['plugin'];
	            
				// check if the plugin is a new version for this externsion or a new plugin and either update or install
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
			<tr class="appinstall-subheading">
				<th>
					<?php echo JText::_('COM_APP_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_APP_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_APP_STATUS_HEADER');?>
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
	           
		        $installer = new JInstaller();
		        
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
						<td colspan="3">
							<?php if ($install_error) : ?>
								<div id="appinstall-component-error">
									<?php echo JText::_('COM_APP_UPDATE_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="appinstall-component-success">
									<?php echo JText::_('COM_APP_UPDATE_COMPONENT_SUCCESS'); ?>
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
			Jerror::raiseWarning(null, JTEXT::sprintf('COM_APP_INSTALL_COMPONENT_ERROR_WRONG_JOOMLA_VERSION',$this->minimum_joomla_release));
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
					Jerror::raiseWarning(null, JTEXT::sprintf('COM_APP_UPDATE_COMPONENT_ERROR_WRONG_VERSION_SEQUENCE', $rel));
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
				JFolder::create(JPATH_SITE.'/images/app'); 
				JFile::copy('administrator/components/com_app/index.html','images/app/index.html', JPATH_SITE);
				
				// Make sure index.html files in all folders
				if (JFolder::exists(JPATH_SITE.'/plugins/app') AND !JFile::exists(JPATH_SITE.'/plugins/app/index.html'))
				{
					JFile::copy('administrator/components/com_app/index.html','plugins/app/index.html', JPATH_SITE);
				}	
				break;    				
			case 'update' :
				/*
				// Define in an array the param updates required and then use the setParams function to update them in the extensions table
				$param_array = array();
				// Repeat for each change
				$param_array[] = array('name' => 'value');
				setParams($param_array);
				*/
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
		$query->where($db->quoteName('name').' = '.$db->quote('com_app'));		
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
		$query->where($db->quoteName('name').' = '.$db->quote('com_app'));		
		$query->where($db->quoteName('type').' = '.$db->quote('component'));

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
			$query->where($db->quoteName('name').' = '.$db->quote('com_app'));
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
	<tr class="appinstall-row">
		<td>
			<?php echo $package;?>
		</td>
		<td>
			<?php echo $group;?>
		</td>								
		<td class="appinstall-error">
			<div>
				<?php echo JText::_('COM_APP_'.strtoupper($action).'_PACKAGE_ERROR');?><br />
				<span class="appinstall-errormsg">
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
		<tr class="appinstall-row">
			<td>
				<?php echo $package;?>
			</td>
			<td>
				<?php echo $group;?>
			</td>								
			<td class="appinstall-success">
				<div><?php echo JText::_('COM_APP_'.strtoupper($action).'_PACKAGE_SUCCESS');?></div>
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
		$content_type['type_title'] = 'Item';
		$content_type['type_alias'] = 'com_app.item';
		$content_type['table'] = '{"special":{"dbtable":"app_items","key":"id","type":"Items","prefix":"AppTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
		$content_type['field_mappings'] .= '"core_title":"name",';
		$content_type['field_mappings'] .= '"core_state":"null",';
		$content_type['field_mappings'] .= '"core_alias":"alias",';
		$content_type['field_mappings'] .= '"core_created_time":"created",';
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
		$content_type['field_mappings'] .= '"core_body":"description",';
		$content_type['field_mappings'] .= '"core_hits":"hits",';
		$content_type['field_mappings'] .= '"core_publish_up":"publish_up",';
		$content_type['field_mappings'] .= '"core_publish_down":"publish_down",';
		$content_type['field_mappings'] .= '"core_access":"access",';
		$content_type['field_mappings'] .= '"core_params":"params",';
		$content_type['field_mappings'] .= '"core_featured":"featured",';
		$content_type['field_mappings'] .= '"core_metadata":"metadata",';
		$content_type['field_mappings'] .= '"core_language":"language",';
		$content_type['field_mappings'] .= '"core_images":"images",';
		$content_type['field_mappings'] .= '"core_urls":"urls",';
		$content_type['field_mappings'] .= '"core_version":"version",';
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
		$content_type['field_mappings'] .= '"core_metakey":"metakey",';
		$content_type['field_mappings'] .= '"core_metadesc":"metadesc",';
		$content_type['field_mappings'] .= '"core_catid":"catid",';
		$content_type['field_mappings'] .= '"core_xreference":"xreference",';
		$content_type['field_mappings'] .= '"asset_id":"asset_id"';
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = 'AppHelperRoute::getItemRoute';

		$content_type['content_history_options'] = '{"formFile":"administrator\/components\/app\/models\/forms\/item.xml",';
		$content_type['content_history_options'] .= '"hideFields":[';
		$content_type['content_history_options'] .= '"asset_id",';
		$content_type['content_history_options'] .= '"checked_out","checked_out_time",';
		$content_type['content_history_options'] .= '"version"],"ignoreChanges":[';
		$content_type['content_history_options'] .= '"modified_by", "modified", ';
		$content_type['content_history_options'] .= '"checked_out", "checked_out_time", ';
		$content_type['content_history_options'] .= '"hits", ';
		$content_type['content_history_options'] .= '"version"],"convertToInt":[';
		$content_type['content_history_options'] .= '"publish_up", "publish_down", ';
		$content_type['content_history_options'] .= '"featured", ';
		$content_type['content_history_options'] .= '"ordering"],';
		$content_type['content_history_options'] .= '"displayLookup":[{';
		$content_type['content_history_options'] .= '"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"name"},';
		$content_type['content_history_options'] .= '{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},';
		$content_type['content_history_options'] .= '{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},';
		$content_type['content_history_options'] .= '{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ';
		$content_type['content_history_options'] .= ']}';	

		$db->setQuery('INSERT INTO '.$db->quoteName('#__content_types')
						.' ('
							.$db->quoteName('type_title').', '
							.$db->quoteName('type_alias').', '
							.$db->quoteName('table').', '
							.$db->quoteName('rules').', '
							.$db->quoteName('field_mappings').', '
							.$db->quoteName('router').', '
							.$db->quoteName('content_history_options')
						.') VALUES '
						.' ('
							  .$db->quote($content_type['type_title']).', '
							  .$db->quote($content_type['type_alias']).', '
							  .$db->quote($content_type['table']).', '
							  .$db->quote($content_type['rules']).', '
							  .$db->quote($content_type['field_mappings']).', '
							  .$db->quote($content_type['router']).', '
							  .$db->quote($content_type['content_history_options'])
						  .');'
					  );

		$db->execute(); 
		
		$content_type = array();
		$content_type['type_title'] = 'App Category';
		$content_type['type_alias'] = 'com_app.category';
		$content_type['table'] = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias",';
		$content_type['field_mappings'] .= '"core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description","core_hits":"hits",';
		$content_type['field_mappings'] .= '"core_publish_up":"null","core_publish_down":"null","core_access":"access","core_params":"params","core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"metadata","core_language":"language","core_images":"null","core_urls":"null","core_version":"version",';
		$content_type['field_mappings'] .= '"core_ordering":"null","core_metakey":"metakey","core_metadesc":"metadesc","core_catid":"parent_id","core_xreference":"null","asset_id":"asset_id"},';
		$content_type['field_mappings'] .= '	"special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';									
		$content_type['router'] = 'AppHelperRoute::getCategoryRoute';

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
}
