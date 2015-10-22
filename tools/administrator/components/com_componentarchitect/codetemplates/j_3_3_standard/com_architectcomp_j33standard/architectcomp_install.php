<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].install
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
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
class [%%com_architectcomp%%]InstallerScript
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
      
		$install_html_file = __DIR__ . '/[%%architectcomp%%]_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= file_get_contents($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="[%%architectcomp%%]install-info">
			<h1><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_INSTALL_HEADER'); ?></h1>
			<div id="[%%architectcomp%%]install-intro">
				<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_INSTALL_INTRO'); ?>
			</div>
			<table id="[%%architectcomp%%]install-table" class="adminlist">
				<thead class="[%%architectcomp%%]install-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_INSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="[%%architectcomp%%]install-subheading">
						<th colspan="2">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="[%%architectcomp%%]install-row">
						<td  colspan="2">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]');?>
						</td>
						<td class="[%%architectcomp%%]install-success">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_INSTALL_PACKAGE_SUCCESS');?>
						</td>
					</tr>
					<tr>				
						<td colspan="3">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_INSTALL_CORE_COMPONENT_SUCCESS');?>
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
			<tr class="[%%architectcomp%%]install-subheading">
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
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
			<tr class="[%%architectcomp%%]install-subheading">
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
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
								<div id="[%%architectcomp%%]install-component-error">
									<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_INSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="[%%architectcomp%%]install-component-success">
									<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_INSTALL_COMPONENT_SUCCESS'); ?>
								</div>			
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" text-align="center">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_JOOMLA_LOGO_DISCLAIMER'); ?>	
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
		$uninstall_html_file = __DIR__ . '/[%%architectcomp%%]_uninstall.html';

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
	<div id="[%%architectcomp%%]install-info">
		<h1><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UNINSTALL_HEADER'); ?></h1>
			<table id="[%%architectcomp%%]install-table" class="adminlist">
				<thead class="[%%architectcomp%%]install-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UNINSTALL_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="[%%architectcomp%%]install-subheading">
						<th colspan="2">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="[%%architectcomp%%]install-row">
						<td  colspan="2">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]');?>
						</td>
						<td class="[%%architectcomp%%]install-success">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UNINSTALL_PACKAGE_SUCCESS');?>
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
			<tr class="[%%architectcomp%%]install-subheading">
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
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
			<tr class="[%%architectcomp%%]install-subheading">
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
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
		JFolder::delete(JPATH_SITE.'/images/[%%architectcomp%%]'); 
		JFolder::delete(JPATH_SITE.'/plugins/[%%architectcomp%%]'); 

		[%%FOREACH COMPONENT_OBJECT%%]
		$db->setQuery(
						'DELETE FROM '.$db->quoteName('#__content_types')
						.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('[%%com_architectcomp%%].[%%compobject%%]')
					 );

		$db->execute(); 
		[%%ENDFOR COMPONENT_OBJECT%%]
		
		[%%IF GENERATE_CATEGORIES%%]

		$db->setQuery(
						'DELETE FROM '.$db->quoteName('#__content_types')
						.' WHERE '.$db->quoteName('type_alias').' = '.$db->quote('[%%com_architectcomp%%].category')
					 );

		$db->execute(); 
		[%%ENDIF GENERATE_CATEGORIES%%]
					
        // Closing HTML
			ob_start();
		?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php if ($install_error) : ?>
								<div id="[%%architectcomp%%]install-component-error">
									<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UNINSTALL_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="[%%architectcomp%%]install-component-success">
									<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UNINSTALL_COMPONENT_SUCCESS'); ?>
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
        
       
		$install_html_file = __DIR__ . '/[%%architectcomp%%]_install.html';

        $buffer = '';

		if (file_exists($install_html_file))
		{
			$buffer .= file_get_contents($install_html_file);
		}

        $install_error = false;

		// Opening HTML
		ob_start();            
		?>
		<div id="[%%architectcomp%%]install-info">
			<h1><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UPDATE_HEADER'); ?></h1>
			<table id="[%%architectcomp%%]install-table" class="adminlist">
				<thead class="[%%architectcomp%%]install-heading">
					<tr>
						<th colspan="3">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UPDATE_HEADER');?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="[%%architectcomp%%]install-subheading">
						<th colspan="2">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_EXTENSION_HEADER');?>
						</th>
						<th width="50%">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
						</th>					
					</tr>			
					<tr class="[%%architectcomp%%]install-row">
						<td  colspan="2">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]');?>
						</td>
						<td class="[%%architectcomp%%]install-success">
							<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UPDATE_PACKAGE_SUCCESS');?>
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
			<tr class="[%%architectcomp%%]install-subheading">
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_PLUGIN_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_PLUGIN_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
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
			<tr class="[%%architectcomp%%]install-subheading">
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_MODULE_HEADER');?>
				</th>
				<th>
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_MODULE_GROUP_HEADER');?>
				</th>				
				<th width="50%">
					<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_STATUS_HEADER');?>
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
								<div id="[%%architectcomp%%]install-component-error">
									<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UPDATE_COMPONENT_ERROR'); ?>
								</div>			
							<?php else : ?>
								<div id="[%%architectcomp%%]install-component-success">
									<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_UPDATE_COMPONENT_SUCCESS'); ?>
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
			Jerror::raiseWarning(null, JTEXT::sprintf('[%%COM_ARCHITECTCOMP%%]_INSTALL_COMPONENT_ERROR_WRONG_JOOMLA_VERSION',$this->minimum_joomla_release));
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
					Jerror::raiseWarning(null, JTEXT::sprintf('[%%COM_ARCHITECTCOMP%%]_UPDATE_COMPONENT_ERROR_WRONG_VERSION_SEQUENCE', $rel));
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
				JFolder::create(JPATH_SITE.'/images/[%%architectcomp%%]'); 
				JFile::copy('administrator/components/[%%com_architectcomp%%]/index.html','images/[%%architectcomp%%]/index.html', JPATH_SITE);
				
				// Make sure index.html files in all folders
				if (JFolder::exists(JPATH_SITE.'/plugins/[%%architectcomp%%]') AND !JFile::exists(JPATH_SITE.'/plugins/[%%architectcomp%%]/index.html'))
				{
					JFile::copy('administrator/components/[%%com_architectcomp%%]/index.html','plugins/[%%architectcomp%%]/index.html', JPATH_SITE);
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
		$query->where($db->quoteName('name').' = '.$db->quote('[%%com_architectcomp%%]'));		
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
		$query->where($db->quoteName('name').' = '.$db->quote('[%%com_architectcomp%%]'));		
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
			$query->where($db->quoteName('name').' = '.$db->quote('[%%com_architectcomp%%]'));
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
	<tr class="[%%architectcomp%%]install-row">
		<td>
			<?php echo $package;?>
		</td>
		<td>
			<?php echo $group;?>
		</td>								
		<td class="[%%architectcomp%%]install-error">
			<div>
				<?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_'.strtoupper($action).'_PACKAGE_ERROR');?><br />
				<span class="[%%architectcomp%%]install-errormsg">
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
		<tr class="[%%architectcomp%%]install-row">
			<td>
				<?php echo $package;?>
			</td>
			<td>
				<?php echo $group;?>
			</td>								
			<td class="[%%architectcomp%%]install-success">
				<div><?php echo JText::_('[%%COM_ARCHITECTCOMP%%]_'.strtoupper($action).'_PACKAGE_SUCCESS');?></div>
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
    
		[%%FOREACH COMPONENT_OBJECT%%]
		$content_type = array();
		$content_type['type_title'] = '[%%CompObject_name%%]';
		$content_type['type_alias'] = '[%%com_architectcomp%%].[%%compobject%%]';
		$content_type['table'] = '{"special":{"dbtable":"[%%architectcomp%%]_[%%compobjectplural%%]","key":"id","type":"[%%CompObjectPlural%%]","prefix":"[%%ArchitectComp%%]Table","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"special":{},"common":{"core_content_item_id":"id",';
											[%%IF INCLUDE_NAME%%]
		$content_type['field_mappings'] .= '"core_title":"name",';
											[%%ELSE INCLUDE_NAME%%]
		$content_type['field_mappings'] .= '"core_title":"null",';
											[%%ENDIF INCLUDE_NAME%%]
											[%%IF INCLUDE_STATE%%]
		$content_type['field_mappings'] .= '"core_state":"state",';
											[%%ELSE INCLUDE_STATE%%]
		$content_type['field_mappings'] .= '"core_state":"null",';
											[%%ENDIF INCLUDE_STATE%%]
											[%%IF INCLUDE_ALIAS%%]
		$content_type['field_mappings'] .= '"core_alias":"alias",';
											[%%ELSE INCLUDE_ALIAS%%]
		$content_type['field_mappings'] .= '"core_alias":"null",';
											[%%ENDIF INCLUDE_ALIAS%%]
											[%%IF INCLUDE_CREATED%%]
		$content_type['field_mappings'] .= '"core_created_time":"created",';
											[%%ELSE INCLUDE_CREATED%%]
		$content_type['field_mappings'] .= '"core_created_time":"null",';
											[%%ENDIF INCLUDE_CREATED%%]
											[%%IF INCLUDE_MODIFIED%%]
		$content_type['field_mappings'] .= '"core_modified_time":"modified",';
											[%%ELSE INCLUDE_MODIFIED%%]
		$content_type['field_mappings'] .= '"core_modified_time":"null",';
											[%%ENDIF INCLUDE_MODIFIED%%]
											[%%IF INCLUDE_DESCRIPTION%%]
		$content_type['field_mappings'] .= '"core_body":"description",';
											[%%ELSE INCLUDE_DESCRIPTION%%]
		$content_type['field_mappings'] .= '"core_body":"null",';
											[%%ENDIF INCLUDE_DESCRIPTION%%]
											[%%IF INCLUDE_HITS%%]
		$content_type['field_mappings'] .= '"core_hits":"hits",';
											[%%ELSE INCLUDE_HITS%%]
		$content_type['field_mappings'] .= '"core_hits":"null",';
											[%%ENDIF INCLUDE_HITS%%]
											[%%IF INCLUDE_PUBLISHED_DATES%%]            
		$content_type['field_mappings'] .= '"core_publish_up":"publish_up",';
		$content_type['field_mappings'] .= '"core_publish_down":"publish_down",';
											[%%ELSE INCLUDE_PUBLISHED_DATES%%]
		$content_type['field_mappings'] .= '"core_publish_up":"null",';
		$content_type['field_mappings'] .= '"core_publish_down":"null",';
											[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
											[%%IF INCLUDE_ACCESS%%]
		$content_type['field_mappings'] .= '"core_access":"access",';
											[%%ELSE INCLUDE_ACCESS%%]            
		$content_type['field_mappings'] .= '"core_access":"null",';
											[%%ENDIF INCLUDE_ACCESS%%]            
											[%%IF INCLUDE_PARAMS_RECORD%%]
		$content_type['field_mappings'] .= '"core_params":"params",';
											[%%ELSE INCLUDE_PARAMS_RECORD%%]
		$content_type['field_mappings'] .= '"core_params":"null",';
											[%%ENDIF INCLUDE_PARAMS_RECORD%%]
											[%%IF INCLUDE_FEATURED%%]            
		$content_type['field_mappings'] .= '"core_featured":"featured",';
											[%%ELSE INCLUDE_FEATURED%%]
		$content_type['field_mappings'] .= '"core_featured":"null",';
											[%%ENDIF INCLUDE_FEATURED%%]            
											[%%IF INCLUDE_METADATA%%]
		$content_type['field_mappings'] .= '"core_metadata":"metadata",';
											[%%ELSE INCLUDE_METADATA%%]
		$content_type['field_mappings'] .= '"core_metadata":"null",';
											[%%ENDIF INCLUDE_METADATA%%]
											[%%IF INCLUDE_LANGUAGE%%]
		$content_type['field_mappings'] .= '"core_language":"language",';
											[%%ELSE INCLUDE_LANGUAGE%%]
		$content_type['field_mappings'] .= '"core_language":"null",';
											[%%ENDIF INCLUDE_LANGUAGE%%]
											[%%IF INCLUDE_IMAGE%%]
		$content_type['field_mappings'] .= '"core_images":"images",';
											[%%ELSE INCLUDE_IMAGE%%]
		$content_type['field_mappings'] .= '"core_images":"null",';
											[%%ENDIF INCLUDE_IMAGE%%]
											[%%IF INCLUDE_URLS%%]
		$content_type['field_mappings'] .= '"core_urls":"urls",';
											[%%ELSE INCLUDE_URLS%%]
		$content_type['field_mappings'] .= '"core_urls":"null",';
											[%%ENDIF INCLUDE_URLS%%]
											[%%IF INCLUDE_VERSIONS%%]
		$content_type['field_mappings'] .= '"core_version":"version",';
											[%%ELSE INCLUDE_VERSIONS%%]
		$content_type['field_mappings'] .= '"core_version":"null",';
											[%%ENDIF INCLUDE_VERSIONS%%]
											[%%IF INCLUDE_ORDERING%%]
		$content_type['field_mappings'] .= '"core_ordering":"ordering",';
											[%%ELSE INCLUDE_ORDERING%%]
		$content_type['field_mappings'] .= '"core_ordering":"null",';
											[%%ENDIF INCLUDE_ORDERING%%]
											[%%IF INCLUDE_METADATA%%]
		$content_type['field_mappings'] .= '"core_metakey":"metakey",';
		$content_type['field_mappings'] .= '"core_metadesc":"metadesc",';
											[%%ELSE INCLUDE_METADATA%%]
		$content_type['field_mappings'] .= '"core_metakey":"null",';
		$content_type['field_mappings'] .= '"core_metadesc":"null",';
											[%%ENDIF INCLUDE_METADATA%%]
											[%%IF GENERATE_CATEGORIES%%]
		$content_type['field_mappings'] .= '"core_catid":"catid",';
											[%%ELSE GENERATE_CATEGORIES%%]
		$content_type['field_mappings'] .= '"core_catid":"null",';
											[%%ENDIF GENERATE_CATEGORIES%%]
											[%%IF INCLUDE_METADATA%%]
		$content_type['field_mappings'] .= '"core_xreference":"xreference",';
											[%%ELSE INCLUDE_METADATA%%]
		$content_type['field_mappings'] .= '"core_xreference":"null",';
											[%%ENDIF INCLUDE_METADATA%%]
											[%%IF INCLUDE_ASSETACL_RECORD%%]
		$content_type['field_mappings'] .= '"asset_id":"asset_id"';
											[%%ELSE INCLUDE_ASSETACL_RECORD%%]
		$content_type['field_mappings'] .= '"asset_id":"null"';
											[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
		$content_type['field_mappings'] .= '}}';									
		$content_type['router'] = '[%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route';

		[%%IF INCLUDE_VERSIONS%%]
		$content_type['content_history_options'] = '{"formFile":"administrator\/components\/[%%architectcomp%%]\/models\/forms\/[%%compobject%%].xml",';
		$content_type['content_history_options'] .= '"hideFields":[';
			[%%IF INCLUDE_ASSETACL%%]
				[%%IF INCLUDE_ASSETACL_RECORD%%]
		$content_type['content_history_options'] .= '"asset_id",';
				[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
			[%%ENDIF INCLUDE_ASSETACL%%]
			[%%IF INCLUDE_CHECKOUT%%]
		$content_type['content_history_options'] .= '"checked_out","checked_out_time",';
			[%%ENDIF INCLUDE_CHECKOUT%%]		
		$content_type['content_history_options'] .= '"version"],"ignoreChanges":[';
			[%%IF INCLUDE_MODIFIED%%]
		$content_type['content_history_options'] .= '"modified_by", "modified", ';
			[%%ENDIF INCLUDE_MODIFIED%%]		
			[%%IF INCLUDE_CHECKOUT%%]
		$content_type['content_history_options'] .= '"checked_out", "checked_out_time", ';
			[%%ENDIF INCLUDE_CHECKOUT%%]		
			[%%IF INCLUDE_HITS%%]
		$content_type['content_history_options'] .= '"hits", ';
			[%%ENDIF INCLUDE_HITS%%]		
		$content_type['content_history_options'] .= '"version"],"convertToInt":[';
			[%%IF INCLUDE_PUBLISHED_DATES%%]
		$content_type['content_history_options'] .= '"publish_up", "publish_down", ';
			[%%ENDIF INCLUDE_PUBLISHED_DATES%%]		
			[%%IF INCLUDE_FEATURED%%];
		$content_type['content_history_options'] .= '"featured", ';
			[%%ENDIF INCLUDE_FEATURED%%]
			[%%IF INCLUDE_ORDERING%%]
		$content_type['content_history_options'] .= '"ordering"],';
			[%%ELSE INCLUDE_ORDERING%%]
		$content_type['content_history_options'] .= '],';
			[%%ENDIF INCLUDE_ORDERING%%]
		$content_type['content_history_options'] .= '"displayLookup":[{';
			[%%IF GENERATE_CATEGORIES%%]
		$content_type['content_history_options'] .= '"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"name"},';
			[%%ENDIF GENERATE_CATEGORIES%%]		
			[%%IF INCLUDE_CREATED%%]
		$content_type['content_history_options'] .= '{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},';
			[%%ENDIF INCLUDE_CREATED%%]		
			[%%IF INCLUDE_ACCESS%%]
		$content_type['content_history_options'] .= '{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},';
			[%%ENDIF INCLUDE_ACCESS%%]		
			[%%IF INCLUDE_MODIFIED%%]
		$content_type['content_history_options'] .= '{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ';
			[%%ENDIF INCLUDE_MODIFIED%%]		
		$content_type['content_history_options'] .= ']}';	
		[%%ELSE INCLUDE_VERSIONS%%]
		$content_type['content_history_options'] = '';	
		[%%ENDIF INCLUDE_VERSIONS%%]

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
		[%%ENDFOR COMPONENT_OBJECT%%]
		
		[%%IF GENERATE_CATEGORIES%%]
		$content_type = array();
		$content_type['type_title'] = '[%%ArchitectComp_name%%] Category';
		$content_type['type_alias'] = '[%%com_architectcomp%%].category';
		$content_type['table'] = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},';
		$content_type['table'] .= '"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
		$content_type['rules'] = '';
		$content_type['field_mappings'] = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias",';
		$content_type['field_mappings'] .= '"core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description","core_hits":"hits",';
		$content_type['field_mappings'] .= '"core_publish_up":"null","core_publish_down":"null","core_access":"access","core_params":"params","core_featured":"null",';
		$content_type['field_mappings'] .= '"core_metadata":"metadata","core_language":"language","core_images":"null","core_urls":"null","core_version":"version",';
		$content_type['field_mappings'] .= '"core_ordering":"null","core_metakey":"metakey","core_metadesc":"metadesc","core_catid":"parent_id","core_xreference":"null","asset_id":"asset_id"},';
		$content_type['field_mappings'] .= '	"special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';									
		$content_type['router'] = '[%%ArchitectComp%%]HelperRoute::getCategoryRoute';

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
		[%%ENDIF GENERATE_CATEGORIES%%]  
	}  
}
