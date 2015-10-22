<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.install
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			Id: architectcomp_install.php 48 2012-06-26 14:16:25Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.install
 * @CAtemplate		joomla_2_5_standard (Release 1.0.4)
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
class com_exampleInstallerScript
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
      
		$install_html_file = dirname(__FILE__) . '/example_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= JFile::read($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="exampleinstall-info">
			<h1><?php echo JText::_('COM_EXAMPLE_INSTALL_HEADER'); ?></h1>
			<div id="exampleinstall-intro">
				<?php echo JText::_('COM_EXAMPLE_INSTALL_INTRO'); ?>
			</div>
			<table id="exampleinstall-table" class="adminlist">
				<thead class="exampleinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_EXAMPLE_INSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="exampleinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_EXAMPLE_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="exampleinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_EXAMPLE');?>
						</td>
						<td class="exampleinstall-success">
							<?php echo JText::_('COM_EXAMPLE_INSTALL_PACKAGE_SUCCESS');?>
						</td>
					</tr>
					<tr>				
						<td colspan="3">
							<?php echo JText::_('COM_EXAMPLE_INSTALL_CORE_COMPONENT_SUCCESS');?>
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
			<tr class="exampleinstall-subheading">
				<th>
					<?php echo JText::_('COM_EXAMPLE_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_EXAMPLE_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
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
	            
				$db->query(); 
				
				if ($db->getErrorNum())
				{
					$install_error = true;
					$error_msg = $db->getErrorMsg();
					$buffer .= $this->printError($attributes['plugin'], $attributes['group'], 'publish',  $error_msg);
				}
				else
				{
					$buffer .= $this->printSuccess($attributes['plugin'], $attributes['group'], 'publish');
				} 
        }
		}  

		// Install modules
 		if (count($manifest->modules->module) > 0)
		{
			// Opening HTML
			ob_start();            
			?>
			<tr class="exampleinstall-subheading">
				<th>
					<?php echo JText::_('COM_EXAMPLE_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_EXAMPLE_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
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

        // Closing HTML
			ob_start();
	?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php if ($install_error) : ?>
								<div id="exampleinstall-component-error">
									<?php echo JText::_('COM_EXAMPLE_INSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="exampleinstall-component-success">
									<?php echo JText::_('COM_EXAMPLE_INSTALL_COMPONENT_SUCCESS'); ?>
								</div>			
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" text-align="center">
							<?php echo JText::_('COM_EXAMPLE_JOOMLA_LOGO_DISCLAIMER'); ?>	
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
		$uninstall_html_file = dirname(__FILE__) . '/example_uninstall.html';

		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);

        $buffer = '';

		// Drop out Style
		if (file_exists($uninstall_html_file))
		{
			$buffer .= JFile::read($uninstall_html_file);
		}
        
        $install_error = false;
		             
		// Opening HTML
		ob_start();            
     ?>
	<div id="exampleinstall-info">
		<h1><?php echo JText::_('COM_EXAMPLE_UNINSTALL_HEADER'); ?></h1>
			<table id="exampleinstall-table" class="adminlist">
				<thead class="exampleinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_EXAMPLE_UNINSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="exampleinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_EXAMPLE_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="exampleinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_EXAMPLE');?>
						</td>
						<td class="exampleinstall-success">
							<?php echo JText::_('COM_EXAMPLE_UNINSTALL_PACKAGE_SUCCESS');?>
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
			<tr class="exampleinstall-subheading">
				<th>
					<?php echo JText::_('COM_EXAMPLE_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_EXAMPLE_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
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
			<tr class="exampleinstall-subheading">
				<th>
					<?php echo JText::_('COM_EXAMPLE_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_EXAMPLE_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
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
		JFolder::delete(JPATH_SITE.'/images/example'); 
		JFolder::delete(JPATH_SITE.'/plugins/example'); 

        // Closing HTML
			ob_start();
		?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php if ($install_error) : ?>
								<div id="exampleinstall-component-error">
									<?php echo JText::_('COM_EXAMPLE_UNINSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="exampleinstall-component-success">
									<?php echo JText::_('COM_EXAMPLE_UNINSTALL_COMPONENT_SUCCESS'); ?>
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
        
       
		$install_html_file = dirname(__FILE__) . '/example_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= JFile::read($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="exampleinstall-info">
			<h1><?php echo JText::_('COM_EXAMPLE_UPDATE_HEADER'); ?></h1>
			<table id="exampleinstall-table" class="adminlist">
				<thead class="exampleinstall-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('COM_EXAMPLE_UPDATE_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="exampleinstall-subheading">
						<th colspan="2">
							<?php echo JText::_('COM_EXAMPLE_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="exampleinstall-row">
						<td  colspan="2">
							<?php echo JText::_('COM_EXAMPLE');?>
						</td>
						<td class="exampleinstall-success">
							<?php echo JText::_('COM_EXAMPLE_UPDATE_PACKAGE_SUCCESS');?>
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
			<tr class="exampleinstall-subheading">
				<th>
					<?php echo JText::_('COM_EXAMPLE_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_EXAMPLE_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
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
						            
					$db->query(); 
					
					if ($db->getErrorNum())
					{
						$install_error = true;
						$error_msg = $db->getErrorMsg();
						$buffer .= $this->printError($attributes['plugin'], $attributes['group'],  'publish', $error_msg);
					}
					else
					{
						$buffer .= $this->printSuccess($attributes['plugin'], $attributes['group'], 'publish');
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
			<tr class="exampleinstall-subheading">
				<th>
					<?php echo JText::_('COM_EXAMPLE_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('COM_EXAMPLE_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('COM_EXAMPLE_STATUS_HEADER');?>
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
								<div id="exampleinstall-component-error">
									<?php echo JText::_('COM_EXAMPLE_UPDATE_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="exampleinstall-component-success">
									<?php echo JText::_('COM_EXAMPLE_UPDATE_COMPONENT_SUCCESS'); ?>
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
			Jerror::raiseWarning(null, JTEXT::sprintf('COM_EXAMPLE_INSTALL_COMPONENT_ERROR_WRONG_JOOMLA_VERSION',$this->minimum_joomla_release));
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
					Jerror::raiseWarning(null, JTEXT::sprintf('COM_EXAMPLE_UPDATE_COMPONENT_ERROR_WRONG_VERSION_SEQUENCE', $rel));
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
				JFolder::create(JPATH_SITE.'/images/example'); 
				JFile::copy('administrator/components/com_example/index.html','images/example/index.html', JPATH_SITE);
				
				// Make sure index.html files in all folders
				if (JFolder::exists(JPATH_SITE.'/plugins/example') AND !JFile::exists(JPATH_SITE.'/plugins/example/index.html'))
				{
					JFile::copy('administrator/components/com_example/index.html','plugins/example/index.html', JPATH_SITE);
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
		$query->where($db->quoteName('name').' = '.$db->quote('com_example'));		
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
		$query->where($db->quoteName('name').' = '.$db->quote('com_example'));		
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
			$query->where($db->quoteName('name').' = '.$db->quote('com_example'));
			$query->where($db->quoteName('type').' = '.$db->quote('component'));

			$db->setQuery($query->__toString());
			
			$db->query();
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
	<tr class="exampleinstall-row">
		<td>
			<?php echo $package;?>
		</td>
		<td>
			<?php echo $group;?>
		</td>								
		<td class="exampleinstall-error">
			<div>
				<?php echo JText::_('COM_EXAMPLE_'.strtoupper($action).'_PACKAGE_ERROR');?><br />
				<span class="exampleinstall-errormsg">
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
		<tr class="exampleinstall-row">
			<td>
				<?php echo $package;?>
			</td>
			<td>
				<?php echo $group;?>
			</td>								
			<td class="exampleinstall-success">
				<div><?php echo JText::_('COM_EXAMPLE_'.strtoupper($action).'_PACKAGE_SUCCESS');?></div>
			</td>
		</tr> 		
		<?php
            $out = ob_get_clean();
        return $out;
    }
}
