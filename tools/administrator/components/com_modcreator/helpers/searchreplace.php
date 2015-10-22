<?php 
/**
 * @version			$Id: searchreplace.php 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_modcreator
 * @subpackage		com_modcreator.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

//[%%START_CUSTOM_CODE%%]
// no direct access
defined('_JEXEC') or die;
jimport( 'joomla.filesystem.folder' );

require_once JPATH_COMPONENT.'/'.'helpers'.'/'.'generateprogress.php';


/* Search and Replace Class */
class ModcreatorSearchReplaceHelper
{ 

	protected $component_objects;
	protected $template_component_name;
	protected $template_object_name;
	protected $markup_prefix;
	protected $markup_suffix;
	protected $component_name;
	protected $component_conditions = array();
	protected $current_copy_object_name;	
	protected $current_dir_copy_object_name;
	protected $current_expand_object_name;	
	protected $current_copy_child_object_name;
	protected $current_expand_child_object_name;	
	protected $current_fieldset_name;	
	protected $current_registry_field;
	protected $process_child = false;		
	protected $search_replace = array(); 
	protected $expand_code_flag;
	protected $files; 
	protected $directories;
	protected $include_subdir; 
	protected $original_source_path = '';
	protected $generation_placeholder;
	protected $current_file;
	protected $file_line_no = 0;
	protected $ignore_lines; 
	protected $ignore_sep; 
	protected $occurrences; 
	protected $search_function;
	
	protected $progress;
	protected $token = '';
	protected $source_files	= array();
	
	protected $logging = false; 
	
	/**
	* Constructor function. Sets up the required classes and objects.
	* 
	*  
	*/ 
	public function __construct(&$generate_progress)
	{
		$this->component_objects =  new stdClass(); 
		$this->progress = $generate_progress;
	}
	/**
	* Set up Search and Replace parameters function. 
	* 
	* @param		array	Search - Replace terms
	* @param		array	Files to process
	* @param		array	Directories to process
	* @param		array	Array of text to search lines for and if present not perform search - replace
	* 
	* 
	*/ 
	public function initialiseSearchReplace($search_replace, $files, $directories = '', $ignore_lines = array())
	{ 

		$this->search_replace   = $search_replace; 
		$this->expand_code_flag  = true; 
		
		$this->files           = $files; 
		$this->directories     = $directories;
		
		$this->include_subdir  = true; 
		$this->ignore_lines    = $ignore_lines; 

		$this->occurrences      = 0; 
		$this->search_function = 'search';
	} 
	/**
	* Accessor for retrieving occurrences. 
	* 
	* @return		integer	Count of occurrences of the search term
	*/ 
	public function getNumOccurrences()
	{ 
		return $this->occurrences; 
	} 


	/**
	* Accessor for setting search_replace variable.
	* 
	* @param		array	Search Replace pairs
	*   
	*/ 
	public function setSearchReplace($search_replace)
	{ 
		$this->search_replace = $search_replace; 
	} 
	/**
	* Accessor for setting expand_code variable. 
	* 
	* @param		boolean	Whether to expand code or not
	*   
	*/ 
	public function setExpandCodeFlag($expand_code_flag)
	{ 
		$this->expand_code_flag = $expand_code_flag; 
	}      
	/**
	* Accessor for setting template_component_name variable. 
	* 
	* @param		string	Markup name used in the template for the component name e.g. Architect Comp
	*   
	*/ 
	public function setTemplateComponentName($template_component_name)
	{ 
		$this->template_component_name = str_replace(' ','',JString::strtolower($template_component_name)); 
	} 
	/**
	* Accessor for setting template_object_name variable. 
	* 
	* @param		string	Markup name used in the template for the component object name e.g. Comp Object
	*   
	*/ 
	public function setTemplateObjectName($template_object_name)
	{ 
		$this->template_object_name = str_replace(' ','',JString::strtolower($template_object_name)); 
	} 
	/**
	* Accessor for setting the markup prefix. 
	* 
	* @param		string	Markup prefix used in the template e.g. [%%
	*   
	*/ 
	public function setMarkupPrefix($prefix)
	{ 
		$this->markup_prefix = $prefix; 
	} 
	/**
	* Accessor for setting the markup suffix. 
	* 
	* @param		string	Markup suffix used in the template e.g. %%]
	*   
	*/ 
	public function setMarkupSuffix($suffix)
	{ 
		$this->markup_suffix = $suffix; 
	} 	/**
	* Accessor for setting component_name variable. 
	* 
	* @param		string	Name of the component
	*   
	*/ 
	public function setComponentName($component_name)
	{ 
		$this->component_name = $component_name; 
	} 
	/**
	* Accessor for setting search_replace variable. 
	* 
	* @param		object	Collection of component objects
	*   
	*/ 
	public function setComponentObjects(&$component_objects)
	{ 
		$this->component_objects = $component_objects; 
	} 
	/**
	* Accessor for setting component conditions array variable. 
	* 
	* @param		string	The component conditions(used by IF statements)
	* @param		integer Set to 0 or 1 for whether the condition is set for the component or not
	*   
	*/ 
	public function setComponentConditions($condition,$value)
	{ 
		$this->component_conditions[$condition] = $value; 
	} 
	/**
	* Accessor to get component conditions array variable. 
	* 
	* @param		string	The component conditions(used by IF statements)
	*   
	* @return		integer The value the condition is set to for the component (0 or 1) or false if not set
	*/ 
	public function getComponentConditions($condition)
	{ 
		if (isset($this->component_conditions[$condition]))
		{
			return $this->component_conditions[$condition];
		}
		else
		{
			return false;
		} 
	} 
	/**
	* Accessor for setting files variable. 
	* 
	* @param		array	An array of files
	*   
	*/ 
	public function setFiles($files)
	{ 
		$this->files = $files; 
	} 

	/**
	* Accessor for setting directories variable. 
	* 
	* @param		array	An array of directories/folders
	*   
	*/ 
	public function setDirectories($directories)
	{ 
		$this->directories = $directories; 
	} 
	/** 
	* Accessor for setting include_subdir variable. 
	* 
	* @param		boolean	True or false as to whether to include sub directories
	*   
	*/ 
	public function setIncludeSubdir($include_subdir)
	{ 
		$this->include_subdir = $include_subdir; 
	} 

	/**
	* Accessor for setting ignore_lines variable. 
	* 
	* @param		array	text which if in a line will result in that line not being search for any of the search replace terms
	*   
	*/ 
	public function setIgnoreLines($ignore_lines)
	{ 
		$this->ignore_lines = $ignore_lines; 
	} 
	/**
	* Accessor for setting the token variable. 
	* 
	* @param		string	The token used to access session data
	*   
	*/ 
	public function setToken($token = '')
	{ 
		$this->token = $token; 
	} 
	/**
	* Accessor for setting the logging variable. 
	* 
	* @param		integer 0 or 1 for whether logging is required
	*   
	*/ 
	public function setLogging($logging = 0)
	{ 
		$this->logging = $logging; 
	} 	
	/**
	* Function to determine which search function is used. 
	* 
	* @param		string	Search function to be performed
	*   
	*/ 
	public function setSearchFunction($search_function)
	{ 
		switch($search_function)
		{ 
			case 'normal': 
				$this->search_function = 'search'; 
				break; 

			case 'quick' : 
				$this->search_function = 'quickSearch'; 
				break; 
			default : 
				$this->search_function = 'search'; 
				break; 
		} 
	} 
	/**
	* Recursive loop through to copy all of the template to the component install directory 
	*  
	* 
	* @param		string	Path for the source files i.e. the code template files
	* @param		string	Path for the output files i.e. where the generated component will be placed
	*   
	*/	
	function recursiveCopy($src,$dst)
	{ 
		$dir = opendir($src); 
		JFolder::create($dst);
		if ($this->original_source_path == '')
		{
			$this->original_source_path = $src;
		} 
		
		while(false !== ( $file = readdir($dir)) )
		{ 
			if (( $file != '.' ) AND ( $file != '..' ))
			{
				if ( is_dir($src.'/'.$file) )
				{ 			
					switch ($file)
					{
						case 'admin':
							$this->generation_placeholder = 'admin';
							break;
						case 'site':
							$this->generation_placeholder = 'site';
							break;
						case 'plugins':
							$this->generation_placeholder = 'plugins';
							break;
						case 'modules':
							$this->generation_placeholder = 'modules';
							break;
						case 'dashboard':
							if (JString::substr($this->generation_placeholder,0,5) == 'admin')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,5).'_dashboard';
							}
							break;								
						case 'help':
							if (JString::substr($this->generation_placeholder,0,5) == 'admin')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,5).'_help';
							}
							break;								
						case 'views':
							if (JString::substr($this->generation_placeholder,0,5) == 'admin')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,5).'_views';
							}
							if (JString::substr($this->generation_placeholder,0,4) == 'site')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,4).'_views';
							}							
							break;
						case 'tmpl':

							break;
						case 'language':

							break;
						case 'en-GB':

							break;																																
						case 'categories':
							if (JString::substr($this->generation_placeholder,0,10) == 'site_views')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,10).'_categories';
							}
							break;
						case 'category':
							if (JString::substr($this->generation_placeholder,0,10) == 'site_views')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,10).'_category';
							}
							break;	
						case 'finder':
							if (JString::substr($this->generation_placeholder,0,7) == 'plugins')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,7).'_finder';
							}
							break;
						case 'plg_finder_compobjectplural':

							break;							
						case 'search':
							if (JString::substr($this->generation_placeholder,0,7) == 'plugins')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,7).'_search';
							}
							break;
						case 'plg_search_compobjectplural':

							break;								
						case 'plg_architectcomp_events':
							if (JString::substr($this->generation_placeholder,0,7) == 'plugins')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,7).'_events';
							}
							break;	
						case 'plg_architectcomp_finder':
							if (JString::substr($this->generation_placeholder,0,7) == 'plugins')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,7).'_finder';
							}
							break;								
						case 'plg_architectcomp_vote':
							if (JString::substr($this->generation_placeholder,0,7) == 'plugins')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,7).'_vote';
							}
							break;
						case 'plg_architectcomp_pagebreak':
							if (JString::substr($this->generation_placeholder,0,7) == 'plugins')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,7).'_pagebreak';
							}
							break;							
						case 'plg_architectcomp_itemnavigation':
							if (JString::substr($this->generation_placeholder,0,7) == 'plugins')
							{
								$this->generation_placeholder = JString::substr($this->generation_placeholder,0,7).'_itemnavigation';
							}
							break;																																									
						default:
							if (JString::substr($this->generation_placeholder,0,6) == 'admin_')
							{
								$this->generation_placeholder = 'admin';
							}
							else
							{							
								if (JString::substr($this->generation_placeholder,0,5) == 'site_')
								{
									$this->generation_placeholder = 'site';
								}
								else
								{						
									if (JString::substr($this->generation_placeholder,0,8) == 'plugins_')
									{
										$this->generation_placeholder = 'plugins';
									}
									else
									{
										if ($src == $this->original_source_path)
										{
											$this->generation_placeholder = 'root';
										} 										
									}
								}
							}								
							break;																															
					}
				}
				else
				{
					if ($src == $this->original_source_path)
					{
						$this->generation_placeholder = 'root';
					} 
					
					
				}			
				
				if (((isset($this->component_conditions['generate_categories']) AND $this->component_conditions['generate_categories']) OR JString::strpos(JString::strtolower($file),'categor') === False)
					AND ((isset($this->component_conditions['generate_admin_dashboard']) AND $this->component_conditions['generate_admin_dashboard']) OR JString::strpos(JString::strtolower($file),'dashboard') === False))
				{				
					if ($this->template_object_name != '' AND JString::strpos($file,$this->template_object_name) !== False)
					{
						if ( is_dir($src.'/'.$file) )
						{ 
							
							foreach ($this->component_objects as $component_object)
							{
								$this->current_dir_copy_object_name = $component_object->code_name;							
								$this->current_copy_object_name = $component_object->code_name;
								
								if (($this->generation_placeholder == 'root'
										OR (isset($component_object->conditions['generate_'.$this->generation_placeholder])
											AND $component_object->conditions['generate_'.$this->generation_placeholder])
										OR ($component_object->conditions['generate_categories']	 
											AND (($this->generation_placeholder == 'site_views_categories' AND $component_object->conditions['generate_categories_site_views_categories']) 
												OR ($this->generation_placeholder == 'site_views_category' AND $component_object->conditions['generate_categories_site_views_category']))
											)
										)
									)
								{	

									$object_name = str_replace ("_", "", JString::strtolower($component_object->code_name));
									$object_name_plural = str_replace ("_", "", JString::strtolower($component_object->plural_code_name));

									if (JString::strpos($file,$this->template_object_name.'plural') !== False)
									{
										
										$this->recursiveCopy($src.'/'.$file,$dst.'/'.str_replace($this->template_object_name.'plural',$object_name_plural,str_replace($this->template_component_name,$this->component_name,$file))); 
									}
									else
									{
										$this->recursiveCopy($src.'/'.$file,$dst.'/'.str_replace($this->template_object_name,$object_name,str_replace($this->template_component_name,$this->component_name,$file))); 
									}
								}
							}
							$this->current_dir_copy_object_name	 = '';						
							$this->current_copy_object_name = '';
						} 
						else
						{ 
							if (!in_array($src.'/'.$file, $this->source_files))
							{
								$this->source_files[] = 	$src.'/'.$file;
								if ($file != 'index.html')
								{
									// Update Stage 2 progress - if logging requested this will also create a log record
									$step = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_STEP_STAGE_2', str_replace(JPATH_ROOT, '', $src.'/'.$file));
									$this->progress->setProgress($this->token, 'stage_2', $step);
								}
							}						
							foreach ($this->component_objects as $component_object)
							{
								if ($this->current_dir_copy_object_name == '' OR $this->current_dir_copy_object_name == $component_object->code_name)
								{								
									$this->current_copy_object_name = $component_object->code_name;
									
									if (($this->generation_placeholder == 'root'
											OR (isset($component_object->conditions['generate_'.$this->generation_placeholder])
												AND $component_object->conditions['generate_'.$this->generation_placeholder])
											OR ($component_object->conditions['generate_categories'] 
												AND (($this->generation_placeholder == 'site_views_categories' AND $component_object->conditions['generate_categories_site_views_categories']) 
													OR ($this->generation_placeholder == 'site_views_category' AND $component_object->conditions['generate_categories_site_views_category'])))
										 )
										 AND (JString::strpos($file,'site') === False 
											  OR (JString::strpos($file,'site') !== False AND $component_object->conditions['generate_site']))
										)
									{										
										$object_name = str_replace ("_", "", JString::strtolower($component_object->code_name));
										$object_name_plural = str_replace ("_", "", JString::strtolower($component_object->plural_code_name));
										
										$child_component_objects = $component_object->child_component_objects;
										
										if (JString::strpos($file,$this->template_object_name.'fieldvalidate') !== False)
										{
											foreach ($component_object->validate_fields as $validate_field)
											{
												copy($src.'/'.$file,$dst.'/'.str_replace($this->template_object_name.'fieldvalidate',str_replace('_','',$validate_field->field_validate_name),str_replace($this->template_component_name,$this->component_name,$file))); 
												
												$this->initialiseSearchReplace($validate_field->search_replace,$dst.'/'.str_replace($this->template_object_name.'fieldvalidate',str_replace('_','',$validate_field->field_validate_name),str_replace($this->template_component_name,$this->component_name,$file)));

												$this->doSearchReplace();										
											}
										}
										else
										{
											if (JString::strpos($file,'child'.$this->template_object_name) !== False)
											{
												if (JString::strpos($file,$this->template_object_name.'plural') !== False)
												{
													foreach ($child_component_objects as $child_component_object)
													{
														if (($this->generation_placeholder == 'root'
																OR (isset($child_component_object->conditions['generate_'.$this->generation_placeholder])
																	AND $child_component_object->conditions['generate_'.$this->generation_placeholder])
																OR ($child_component_object->conditions['generate_categories']	 
																	AND (($this->generation_placeholder == 'site_views_categories' AND $child_component_object->conditions['generate_categories_site_views_categories']) 
																		OR ($this->generation_placeholder == 'site_views_category' AND $child_component_object->conditions['generate_categories_site_views_category'])))
																)
																AND (JString::strpos($file,'site') === False 
																	OR (JString::strpos($file,'site') !== False AND $child_component_object->conditions['generate_site']))
															)
														{
															$this->current_copy_child_object_name = $child_component_object->code_name;
															$this->process_child = true;
															
															
															copy($src.'/'.$file, $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name.'plural',str_replace('_','',$child_component_object->plural_code_name),str_replace($this->template_component_name,$this->component_name,$file)))); 

															$this->initialiseSearchReplace($child_component_object->search_replace, $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name.'plural',str_replace('_','',$child_component_object->plural_code_name),str_replace($this->template_component_name,$this->component_name,$file))));

															$this->doSearchReplace();
														}
													}

												}
												else
												{
													foreach ($child_component_objects as $child_component_object)
													{
														if (($this->generation_placeholder == 'root'
																OR (isset($child_component_object->conditions['generate_'.$this->generation_placeholder])
																	AND $child_component_object->conditions['generate_'.$this->generation_placeholder])
																OR ($child_component_object->conditions['generate_categories']	 
																	AND (($this->generation_placeholder == 'site_views_categories' AND $child_component_object->conditions['generate_categories_site_views_categories']) 
																		OR ($this->generation_placeholder == 'site_views_category' AND $child_component_object->conditions['generate_categories_site_views_category'])))
																)
																AND (JString::strpos($file,'site') === False 
																	OR (JString::strpos($file,'site') !== False AND $child_component_object->conditions['generate_site']))
															)
														{	
															$this->current_copy_child_object_name = $child_component_object->code_name;
															$this->process_child = true;
															
															copy($src.'/'.$file, $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name,str_replace('_','',$child_component_object->code_name),str_replace($this->template_component_name,$this->component_name,$file)))); 
															
															$this->initialiseSearchReplace($child_component_object->search_replace, $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name,str_replace('_','',$child_component_object->code_name),str_replace($this->template_component_name,$this->component_name,$file))));

															$this->doSearchReplace();
														}
													}

												}											
												$this->current_copy_child_object_name = '';
												$this->process_child = false;
												
												
											}
											else
											{
												if (JString::strpos($file,'batch') === False 
													OR (JString::strpos($file,'batch') !== False AND $component_object->conditions['include_batch'])
												)
												{																						
													if (JString::strpos($file,$this->template_object_name.'plural') !== False)
													{							
														copy($src.'/'.$file, $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name.'plural',$object_name_plural,str_replace($this->template_component_name,$this->component_name,$file)))); 

														$this->initialiseSearchReplace(array(), $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name.'plural',$object_name_plural,str_replace($this->template_component_name,$this->component_name,$file))));

														$this->doSearchReplace();

													}
													else
													{
														copy($src.'/'.$file, $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name,$object_name,str_replace($this->template_component_name,$this->component_name,$file)))); 
														
														$this->initialiseSearchReplace(array(), $dst.'/'.str_replace($this->template_component_name,$this->component_name,str_replace($this->template_object_name,$object_name,str_replace($this->template_component_name,$this->component_name,$file))));

														$this->doSearchReplace();
														
													}
												}
											}
										}
									}
								}
							}
							if ($this->current_dir_copy_object_name == '')
							{
								$this->current_copy_object_name = '';
							}
						}
					}
					else
					{
						if ($this->template_component_name != '' AND JString::strpos($file,$this->template_component_name) !== False)
						{
							if ( is_dir($src.'/'.$file) )
							{ 
								if (!isset($this->component_conditions['generate_'.$this->generation_placeholder])
									OR $this->component_conditions['generate_'.$this->generation_placeholder])
								{								
									$this->recursiveCopy($src.'/'.$file,$dst.'/'.str_replace($this->template_component_name,$this->component_name,$file)); 
								}
							} 
							else
							{ 
								if (!in_array($src.'/'.$file, $this->source_files))
								{
									$this->source_files[] = 	$src.'/'.$file;
									if ($file != 'index.html')
									{
										// Update Stage 2 progress - if logging requested this will also create a log record
										$step = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_STEP_STAGE_2', str_replace(JPATH_ROOT, '', $src.'/'.$file));
										$this->progress->setProgress($this->token, 'stage_2', $step);
									}
								}	
								if (JString::strpos($file,'site') === False 
									OR (JString::strpos($file,'site') !== False AND $this->component_conditions['generate_site'])
								)
								{
									copy($src.'/'.$file,$dst.'/'.str_replace($this->template_component_name,$this->component_name,$file)); 
									
									$this->initialiseSearchReplace(array(),$dst.'/'.str_replace($this->template_component_name,$this->component_name,$file));

									$this->doSearchReplace();
								}
							}						
						}
						else
						{
							if ( is_dir($src.'/'.$file) )
							{ 
								if (!isset($this->component_conditions['generate_'.$this->generation_placeholder])
									OR $this->component_conditions['generate_'.$this->generation_placeholder])
								{															
									$this->recursiveCopy($src.'/'.$file,$dst.'/'.$file); 
								}
							} 
							else
							{
								if (!in_array($src.'/'.$file, $this->source_files))
								{
									$this->source_files[] = 	$src.'/'.$file;
									if ($file != 'index.html')
									{
										// Update Stage 2 progress - if logging requested this will also create a log record
										$step = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_STEP_STAGE_2', str_replace(JPATH_ROOT, '', $src.'/'.$file));
										$this->progress->setProgress($this->token, 'stage_2', $step);
									}
								}
								
								//Check if site layout is required and the file is a site layout file.  All other files are processed
								$file_name = explode('.',$file);
								$file_name = explode('_',$file_name[0]);
								if (JString::substr($this->generation_placeholder,0,4) != 'site'
									OR (!isset($this->component_conditions['generate_site_layout_'.$file_name[0]])
											OR $this->component_conditions['generate_site_layout_'.$file_name[0]]))
								{
									$copy_file = true;
								}
								else
								{
									$copy_file = false;
								}
								//Check if file is a pagebreak file and if it is and the component condition is not sent then do not copy
								if (JString::strpos($file,'pagebreak') !== False AND !$this->component_conditions['generate_plugins_pagebreak'])
								{
									$copy_file = false;
								}
								//Check if file is a site file and if it is and the component condition is not sent then do not copy
								if (JString::strpos($file,'site') !== False AND !$this->component_conditions['generate_site'])
								{	
									$copy_file = false;
								}								

								if (JString::substr($this->generation_placeholder,0,4) == 'site' AND $this->current_copy_object_name != '' )
								{
									foreach ($this->component_objects as $component_object)
									{
										if ($component_object->code_name == $this->current_copy_object_name
											OR $component_object->code_name == $this->current_expand_object_name)
										{				
											if (isset($component_object->conditions['generate_site_layout_'.$file_name[0]]))
											{	
												$copy_file = (boolean) $component_object->conditions['generate_site_layout_'.$file_name[0]];
											}
											break;						
										}
									}
								}								
								if ($copy_file)
								{								
									copy($src.'/'.$file,$dst.'/'.$file); 
									
									$this->initialiseSearchReplace(array()	,$dst.'/'.$file);

									$this->doSearchReplace();
								}
								
							}	
						}									
					}
				}
			} 
		}
		closedir($dir); 
		// Don't output an empty directory 
		$dst_dir = opendir($dst); 
		$remove_dst = true;	
		$index_file_found = false;
		while(false !== ( $file = readdir($dst_dir)) )
		{ 
			if (( $file != '.' ) AND ( $file != '..' ))
			{
				if ( is_dir($dst.'/'.$file) )	
				{
					// folder found in directory so don't remove destination folder just exit
					$remove_dst = false;
				}
				else
				{
					if ($file !== false)
					{
						// file found in directory so don't remove destination folder just exit
						$remove_dst = false;
						
						// keep on looping through directory files/folders until an index.html file is found
						if ($file == 'index.html')
						{
							$index_file_found = true;
							break;					
						}
					}
				}
			}
		}
		if (!$index_file_found)
		{
			$warning = array('message' => JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1030_DIRECTORY_HAS_NO_INDEX_FILE',$dst),'errorcode' => 'gen1030');
			$this->progress->outputWarning($this->token, $warning);
		}
		
		if ($remove_dst)
		{
			$result = JFolder::delete($dst);
		}
	}

	/**
	* The main search and replace routine. 
	* 
	* @param		string	Name of file to be searched
	*   
	*/ 
	private function search($file_name)
	{ 

		$this->occurrences = 0; 
		$file_array = file($file_name); 
		$output_file_array = array();
		
		$file = fread($fp = fopen($file_name, 'r'), filesize($file_name)); fclose($fp); 		      
		
		$k = 0;
		
		if ($this->expand_code_flag OR substr($file_name,strlen($file_name)-10,10) != 'index.html')
		{
			// First pass to expand out all the tags within the file
			foreach ($this->component_objects as $component_object)
			{
				
				if ($component_object->code_name == $this->current_copy_object_name
					OR $component_object->code_name == $this->current_expand_object_name)
				{	
					$file_array = $this->process_object_conditions($component_object, $file_array);
				}
			}
			
			$output_file_array = $this->expandCode($file_array, $file_name);
		}
		else
		{
			$output_file_array = $file_array;
		}
		// Second pass through just to do search replace	
		for($i=0; $i<count($output_file_array); $i++)
		{ 

			$line_store = $output_file_array[$i];

			foreach ($this->component_objects as $component_object)
			{
				
				if ($component_object->code_name == $this->current_copy_object_name
					OR $component_object->code_name == $this->current_expand_object_name)
				{	
					$line_store = $this->searchline($line_store, $component_object->search_replace);
				}
			}
			
			if (count($this->search_replace) > 0)
			{
				$line_store = $this->searchline($line_store,$this->search_replace);
				
			}
			$output_file_array[$i] = $line_store;
		}
		
		$return = array($this->occurrences, implode('', $output_file_array)); 
		
		return $return; 

	} 
	/**
	* A recursive prodedure to expand out the tagged areas of input. 
	* 
	* @param		array	lines of code to be checked and if necessary expanded
	* @param		string	Path and file name of the file being processed
	*   
	*/ 
	private function expandCode($input_array = array(), $file_name = '')
	{ 
		// If this is the top level call to expandCode output log message
		if ($file_name != '')
		{
			$this->current_file = $file_name;
			$this->file_line_no = 0;
		}
		
		$output_array = array();
		
		for($i=0; $i<count($input_array); $i++)
		{ 
			// If this is the top level call to expandCode i.e. with the array of lines in a file then increment the file line number
			if ($file_name != '')
			{
				$this->file_line_no++;
			}
			// Check for expand of FOREACH COMPONENT OBJECT
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH COMPONENT_OBJECT'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				if ($this->current_copy_object_name  != '')
				{
					$warning = array('message' => JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1010_FOREACH_COMPONENT_OBJECT_IN_COMPOBJECT'),'errorcode' => 'gen1010');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $this->file_line_no);
				}
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR COMPONENT_OBJECT'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				
				if ($j == count($input_array))
				{
					$warning= array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1011_FOREACH_COMPONENT_OBJECT_NO_ENDFOR'),'errorcode' => 'gen1011');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					
					foreach ($this->component_objects as $component_object)
					{
						if ($this->generation_placeholder == 'root'
								OR (isset($component_object->conditions['generate_'.$this->generation_placeholder])
									AND $component_object->conditions['generate_'.$this->generation_placeholder])
								OR ($component_object->conditions['generate_categories']	 
									AND (($this->generation_placeholder == 'site_views_categories' AND $component_object->conditions['generate_categories_site_views_categories']) 
										OR ($this->generation_placeholder == 'site_views_category' AND $component_object->conditions['generate_categories_site_views_category'])))
							)
						{
							
							//$output_array[] = "\n";
							
							$this->current_expand_object_name = $component_object->code_name;						
							$expanded_array = $this->expandCode($store_array);

							//Now we have the component object lets check in any IF OBJECT_ conditions in the code need handling
							$object_expanded_array = $this->process_object_conditions($component_object, $expanded_array);
							
							for ($l= 0; $l < count($object_expanded_array); $l++)
							{
								$line_store = $object_expanded_array[$l];
								
								if (count($component_object->search_replace) > 0)
								{
									$line_store = $this->searchline($line_store,$component_object->search_replace);

								} 
								$output_array[] = $line_store;
							}
						}
					}
					$this->current_expand_object_name = '';
					//$output_array[] = "\n";

					$i =$j;
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}					
				}
			} 
			
			// Check for expand of FOREACH CHILD COMPONENT OBJECT
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH CHILD_COMPONENT_OBJECT'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;

				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR CHILD_COMPONENT_OBJECT'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1012_FOREACH_CHILD_COMPONENT_OBJECT_NO_ENDFOR'),'errorcode' => 'gen1012');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					
					foreach ($this->component_objects as $component_object)
					{
						if ($component_object->code_name == $this->current_copy_object_name
							OR $component_object->code_name == $this->current_expand_object_name)
						{
							foreach ($component_object->child_component_objects as $child_component_object)
							{
								
								//$output_array[] = "\n";
								
								$this->current_expand_child_object_name = $child_component_object->code_name;
								$this->process_child = true;						
								$expanded_array = $this->expandCode($store_array);
								
								//Now we have the component object lets check in any IF OBJECT_ conditions in the code need handling
								$child_object_expanded_array = $this->process_object_conditions($child_component_object, $expanded_array);
								
								for ($l= 0; $l < count($child_object_expanded_array); $l++)
								{
									$line_store = $child_object_expanded_array[$l];
									
									if (count($child_component_object->search_replace) > 0)
									{
										$line_store = $this->searchline($line_store, $child_component_object->search_replace);
									} 
									$output_array[] = $line_store;
								}
							}
							$this->current_expand_child_object_name = '';
							$this->process_child = false;
						}
					}
					//$output_array[] = "\n";

					$i =$j;
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}						
				}
			} 			
			// Check for expand of FOREACH OBJECT_FIELDSET
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH OBJECT_FIELDSET'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR OBJECT_FIELDSET'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1013_FOREACH_FIELDSET_NO_ENDFOR'),'errorcode' => 'gen1013');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					
					foreach ($this->component_objects as $component_object)
					{
						if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
							OR $component_object->code_name == $this->current_expand_object_name))
								OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
										OR $component_object->code_name == $this->current_expand_child_object_name)))
						{
							foreach ($component_object->fieldsets as $fieldset)
							{
								// Mark the default fieldset
								if (isset($component_object->default_fieldset_id) AND $component_object->default_fieldset_id == $fieldset->id)
								{
									$fieldset->default = true;
								}
								else
								{
									$fieldset->default = false;
								}
								$this->current_fieldset_name = $fieldset->name;
								
								$expanded_array = $this->expandCode($store_array);
								
								//Now we have the fieldset lets check in any IF FIELDSET_ conditions in the code need handling
								$fieldset_expanded_array = $this->process_fieldset_conditions($fieldset, $expanded_array);
								
								for ($l= 0; $l < count($fieldset_expanded_array); $l++)
								{
									$line_store = $fieldset_expanded_array[$l];
									
									if (count($fieldset->search_replace > 0))
									{
										$line_store = $this->searchline($line_store,$fieldset->search_replace);
									} 
									$output_array[] = $line_store;
								}
							}
							$this->current_fieldset_name = '';
						}
					}

					$i =$j;
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}						
				}
			} 			
			// Check for expand of FOREACH OBJECT_FIELD
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH OBJECT_FIELD'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR OBJECT_FIELD'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1014_FOREACH_FIELD_NO_ENDFOR'),'errorcode' => 'gen1014');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						foreach ($this->component_objects as $component_object)
						{
							if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
								OR $component_object->code_name == $this->current_expand_object_name))
									OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
											OR $component_object->code_name == $this->current_expand_child_object_name)))
							{
								if ($this->current_fieldset_name <> '')
								{	
									foreach ($component_object->fieldsets as $fieldset)
									{
										if ($fieldset->name == $this->current_fieldset_name)
										{
											foreach ($fieldset->fields as $field)
											{
												//Now we have the field lets check in any IF FIELD_ conditions in the code need handling
												$field_expanded_array = $this->process_field_conditions($field, $expanded_array);
												
												for ($l= 0; $l < count($field_expanded_array); $l++)
												{
													$line_store = $field_expanded_array[$l];
													
													if (count($field->search_replace > 0))
													{
														$line_store = $this->searchline($line_store,$field->search_replace);
													} 
													$output_array[] = $line_store;
													
												}
											}
										}
									}														
								}
								else
								{
									foreach ($component_object->fields as $field)
									{
										//No we have the field lets check in any IF FIELD_ conditions in the code need handling
										$field_expanded_array = $this->process_field_conditions($field, $expanded_array);
										
										for ($l= 0; $l < count($field_expanded_array); $l++)
										{
											$line_store = $field_expanded_array[$l];
											
											if (count($field->search_replace) > 0)
											{
												$line_store = $this->searchline($line_store,$field->search_replace);
											} 
											$output_array[] = $line_store;
										}
									}
								}
							}
						}
					}
					$i =$j;	
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}									
				}
			} 

			// Check for expand of FOREACH FILTER_FIELD
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH FILTER_FIELD'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR FILTER_FIELD'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1015_FOREACH_FILTER_FIELD_NO_ENDFOR'),'errorcode' => 'gen1015');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						foreach ($this->component_objects as $component_object)
						{
							if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
								OR $component_object->code_name == $this->current_expand_object_name))
									OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
											OR $component_object->code_name == $this->current_expand_child_object_name)))
							{
								foreach ($component_object->filter_fields as $filter_field)
								{
									//Now we have the field lets check in any IF FIELD_ conditions in the code need handling
									$field_expanded_array = $this->process_field_conditions($filter_field, $expanded_array);
									
									for ($l= 0; $l < count($field_expanded_array); $l++)
									{
										$line_store = $field_expanded_array[$l];
										
										if (count($filter_field->search_replace) > 0)
										{
											$line_store = $this->searchline($line_store,$filter_field->search_replace);
										} 
										$output_array[] = $line_store;
									}
								}
							}
						}
					}
					$i =$j;	
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}									
				}
			}	
			// Check for expand of FOREACH ORDER_FIELD
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH ORDER_FIELD'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR ORDER_FIELD'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1016_FOREACH_ORDER_FIELD_NO_ENDFOR'),'errorcode' => 'gen1016');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						foreach ($this->component_objects as $component_object)
						{
							if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
								OR $component_object->code_name == $this->current_expand_object_name))
									OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
											OR $component_object->code_name == $this->current_expand_child_object_name)))
							{
								foreach ($component_object->order_fields as $order_field)
								{
									//Now we have the field lets check in any IF FIELD_ conditions in the code need handling
									$field_expanded_array = $this->process_field_conditions($order_field, $expanded_array);
									
									for ($l= 0; $l < count($field_expanded_array); $l++)
									{
										$line_store = $field_expanded_array[$l];
										
										if (count($order_field->search_replace) > 0)
										{
											$line_store = $this->searchline($line_store,$order_field->search_replace);
										} 
										$output_array[] = $line_store;
									}
								}
							}
						}
					}
					$i =$j;	
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}									
				}
			}			
			// Check for expand of FOREACH LINK_FIELD
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH LINK_FIELD'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR LINK_FIELD'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1017_FOREACH_LINK_FIELD_NO_ENDFOR'),'errorcode' => 'gen1017');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						foreach ($this->component_objects as $component_object)
						{
							if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
								OR $component_object->code_name == $this->current_expand_object_name))
									OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
											OR $component_object->code_name == $this->current_expand_child_object_name)))
							{
								foreach ($component_object->link_fields as $link_field)
								{
									//Now we have the field lets check in any IF FIELD_ conditions in the code need handling
									$field_expanded_array = $this->process_field_conditions($link_field, $expanded_array);
									
									for ($l= 0; $l < count($field_expanded_array); $l++)
									{
										$line_store = $field_expanded_array[$l];
										
										if (count($link_field->search_replace) > 0)
										{
											$line_store = $this->searchline($line_store,$link_field->search_replace);
										} 
										$output_array[] = $line_store;
									}
								}
							}
						}
					}
					$i =$j;	
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}									
				}
			}	
			// Check for expand of FOREACH SEARCH_FIELD
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH SEARCH_FIELD'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR SEARCH_FIELD'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1018_FOREACH_SEARCH_FIELD_NO_ENDFOR'),'errorcode' => 'gen1018');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						foreach ($this->component_objects as $component_object)
						{
							if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
								OR $component_object->code_name == $this->current_expand_object_name))
									OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
											OR $component_object->code_name == $this->current_expand_child_object_name)))
							{
								foreach ($component_object->search_fields as $search_field)
								{
									//Now we have the field lets check in any IF FIELD_ conditions in the code need handling
									$field_expanded_array = $this->process_field_conditions($search_field, $expanded_array);
									
									for ($l= 0; $l < count($field_expanded_array); $l++)
									{
										$line_store = $field_expanded_array[$l];
										
										if (count($search_field->search_replace) > 0)
										{
											$line_store = $this->searchline($line_store,$search_field->search_replace);
										} 
										$output_array[] = $line_store;
									}
								}
							}
						}
					}
					$i =$j;	
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}									
				}
			}	
			// Check for expand of FOREACH VALIDATE_FIELD
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH VALIDATE_FIELD'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR VALIDATE_FIELD'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1019_FOREACH_VALIDATE_FIELD_NO_ENDFOR'),'errorcode' => 'gen1019');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						foreach ($this->component_objects as $component_object)
						{
							if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
								OR $component_object->code_name == $this->current_expand_object_name))
									OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
											OR $component_object->code_name == $this->current_expand_child_object_name)))
							{
								foreach ($component_object->validate_fields as $validate_field)
								{
									//Now we have the field lets check in any IF FIELD_ conditions in the code need handling
									$field_expanded_array = $this->process_field_conditions($validate_field, $expanded_array);
									
									for ($l= 0; $l < count($field_expanded_array); $l++)
									{
										$line_store = $field_expanded_array[$l];
										
										if (count($validate_field->search_replace) > 0)
										{
											$line_store = $this->searchline($line_store,$validate_field->search_replace);
										} 
										$output_array[] = $line_store;
									}
								}
							}
						}
					}
					$i =$j;	
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}									
				}
			}
			// NB Registry Fields and Registry Entries do not call process_field_conditions but may do in future			
			// Check for expand of FOREACH REGISTRY_FIELD
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH REGISTRY_FIELD'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR REGISTRY_FIELD'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1020_FOREACH_REGISTRY_FIELD_NO_ENDFOR'),'errorcode' => 'gen1020');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					foreach ($this->component_objects as $component_object)
					{
						if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
							OR $component_object->code_name == $this->current_expand_object_name))
								OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
										OR $component_object->code_name == $this->current_expand_child_object_name)))
						{					
							foreach ($component_object->registry_fields as $registry_field)
							{
								$this->current_registry_field = $registry_field->name;						
								$expanded_array = $this->expandCode($store_array);
								
								for ($l= 0; $l < count($expanded_array); $l++)
								{
									$line_store = $expanded_array[$l];
									
									if (count($registry_field->search_replace) > 0)
									{
										$line_store = $this->searchline($line_store,$registry_field->search_replace);

									} 
									$output_array[] = $line_store;
									
								}
							}
							$this->current_registry_field = '';

						}
					}
					$i =$j;
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}						
				}
			} 
			// Check for expand of FOREACH REGISTRY_ENTRY
			if(JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH REGISTRY_ENTRY'.$this->markup_suffix) !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$store_array = array();
				for($j=$i+1; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR REGISTRY_ENTRY'.$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						$store_array[] = $input_array[$j];
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1021_FOREACH_REGISTRY_ENTRY_NO_ENDFOR'),'errorcode' => 'gen1021');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						foreach ($this->component_objects as $component_object)
						{
							if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
								OR $component_object->code_name == $this->current_expand_object_name))
									OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
											OR $component_object->code_name == $this->current_expand_child_object_name)))
							{
								if ($this->current_registry_field <> '')
								{	
									foreach ($component_object->registry_fields as $registry_field)
									{
										if ($registry_field->name == $this->current_registry_field)
										{
											foreach ($registry_field->registry_entries as $registry_entry)
											{	
												//Now we have the field lets check in any IF FIELD_ conditions in the code need handling
												$field_expanded_array = $this->process_field_conditions($registry_entry, $expanded_array);
												
												for ($l= 0; $l < count($field_expanded_array); $l++)
												{
													$line_store = $field_expanded_array[$l];
													
													if (count($registry_entry->search_replace > 0))
													{
														$line_store = $this->searchline($line_store,$registry_entry->search_replace);
													} 
													$output_array[] = $line_store;
													
												}
											}
										}
									}														
								}
								else
								{
									$warning = array('message' => JText::_('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1022_FOREACH_REGISTRY_ENTRY_NOT_IN_REGISTRY_FIELD'),'errorcode' => 'gen1022');
									$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);

									$output_array[] = $line_store;
								}

							}
						}
					}
					$i =$j;	
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}									
				}
			}															
			// Check for IF conditions
			
			if(JString::strpos($input_array[$i],$this->markup_prefix.'IF INCLUDE_') !== false OR JString::strpos($input_array[$i],$this->markup_prefix.'IF GENERATE_') !== false)
			{
				$block_start = $this->file_line_no;
				
				$start = JString::strpos($input_array[$i],$this->markup_prefix.'IF ')+6;
				$end = JString::strpos($input_array[$i],$this->markup_suffix);
				$length = $end - $start;					
				$condition = JString::strtolower(JString::substr($input_array[$i],$start,$length));
				// Set the condition to include code based on the global component setting
				if (isset($this->component_conditions[$condition]) AND $this->component_conditions[$condition] == 1)
				{
					$includecode = true;
				}
				else
				{
					$includecode = false;
				}

				if ($this->current_copy_object_name != '' OR $this->current_expand_object_name != '')
				{
					// Override the component condition with any condition set at the object level				
					foreach ($this->component_objects as $component_object)
					{
						if ((!$this->process_child AND ($component_object->code_name == $this->current_copy_object_name
							OR $component_object->code_name == $this->current_expand_object_name))
								OR ($this->process_child AND ($component_object->code_name == $this->current_copy_child_object_name
										OR $component_object->code_name == $this->current_expand_child_object_name)))
						{				
							if (isset($component_object->conditions[$condition]))
							{	
								$includecode = (boolean) $component_object->conditions[$condition];
							}
							else
							{
								$includecode = false;
							}							
							break;						
						}
					}
				}
				
				$i++;
				$store_array = array();
				$not_else = true;
				$component_object_loop = false;
				for($j=$i; $j<count($input_array); $j++)
				{
					// Check for a component object loop or a child component object loop, inside an IF clause and ignore ENDIF if found
					//Possibility of a matching IF clause within loop i.e. condition at Object level			
					if(JString::strpos($input_array[$j],$this->markup_prefix.'FOREACH COMPONENT_OBJECT'.$this->markup_suffix) !== false || JString::strpos($input_array[$j],$this->markup_prefix.'FOREACH CHILD_COMPONENT_OBJECT'.$this->markup_suffix) !== false)
					{
						$component_object_loop = true;
					}
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR COMPONENT_OBJECT'.$this->markup_suffix) !== false || JString::strpos($input_array[$j],$this->markup_prefix.'ENDFOR CHILD_COMPONENT_OBJECT'.$this->markup_suffix) !== false)
					{
						$component_object_loop = false;
					}						
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDIF '.JString::strtoupper($condition).$this->markup_suffix) !== false AND $component_object_loop == false)
					{
						break;							
					}
					else
					{
						if(JString::strpos($input_array[$j],$this->markup_prefix.'ELSE '.JString::strtoupper($condition).$this->markup_suffix) !== false AND $component_object_loop == false)
						{
							$not_else = false;							
						}
						else
						{
							$i++;
							if (($includecode AND $not_else) OR (!$includecode AND !$not_else))
							{
								$store_array[] = $input_array[$j];
							}
						}
					}
				}
				
				$expanded_array = $this->expandCode($store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1023_IF_STATEMENT_NO_ENDIF', JString::strtoupper($condition)),'errorcode' => 'gen1023');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						for ($l= 0; $l < count($expanded_array); $l++)
						{
							$output_array[] = $expanded_array[$l];
						}						
					}
					$i =$j;
					if ($file_name != '')
					{
						$this->file_line_no = $j;
					}						
				}

			}
			
			
			// Nothing to handle so just output the line	
			if (JString::strpos($input_array[$i],$this->markup_prefix.'FOREACH ') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ENDFOR ') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'IF INCLUDE_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ELSE INCLUDE_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ENDIF INCLUDE_') === FALSE AND	
				JString::strpos($input_array[$i],$this->markup_prefix.'IF GENERATE_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ELSE GENERATE_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ENDIF GENERATE_') === FALSE)				
			
			{
				// This just a double check to ensure that no substitutions are missed
				$line_store = $this->searchline($input_array[$i],$this->search_replace);
				
				$output_array[] =$line_store;
			}
		}

		return $output_array;
	}
	/**
	* A recursive prodedure to expand out the tagged areas of input for a fieldset
	* this may contain a set of conditions which are set on the fieldset data itself. 
	* 
	* @param		object	Component Object object
	* @param		array	Array of code lines to be checked for object conditions
	*   
	* @return		array   Array of code lines that have been processed
	*/ 
	private function process_object_conditions($component_object, $input_array)
	{
		$output_array = array();
		// Check for IF OBJECT_ conditions
		for($i=0; $i<count($input_array); $i++)
		{ 
			if(JString::strpos($input_array[$i],$this->markup_prefix.'IF OBJECT_') !== FALSE)
			{
				$block_start = $this->file_line_no;

				$start = JString::strpos($input_array[$i],$this->markup_prefix.'IF OBJECT_')+13;
				$end = JString::strpos($input_array[$i],$this->markup_suffix);
				$length = $end - $start;					
				$object_condition = JString::substr($input_array[$i],$start,$length);
				$includecode = false;			
				switch ($object_condition)
				{
					// Conditions on fieldset status				
					case 'READONLY':
						if ($component_object->readonly)
						{
							$includecode = true;
						}
						break;						

					// NOT conditions on fieldset status	
					case 'NOT_READONLY':
						if (!$component_object->readonly)
						{
							$includecode = true;
						}
						break;						
					
					default:
						$includecode = false;					
						break;
				}

				
				$i++;
				$store_array = array();
				$not_else = true;
				for($j=$i; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDIF OBJECT_'.JString::strtoupper($object_condition).$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						if(JString::strpos($input_array[$j],$this->markup_prefix.'ELSE OBJECT_'.JString::strtoupper($object_condition).$this->markup_suffix) !== FALSE)
						{
							$not_else = false;							
						}
						else
						{
							$i++;
							if (($includecode AND $not_else) OR (!$includecode AND !$not_else))
							{
								$store_array[] = $input_array[$j];
							}
						}
					}
				}
				
				$expanded_array = $this->process_object_conditions($component_object, $store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1026_IF_OBJECT_STATEMENT_NO_ENDIF', JString::strtoupper($object_condition)),'errorcode' => 'gen1026');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						for ($l= 0; $l < count($expanded_array); $l++)
						{
							$output_array[] = $expanded_array[$l];
						}						
					}
					$i =$j;
				}

			}
			// Nothing to handle so just output the line	
			if (JString::strpos($input_array[$i],$this->markup_prefix.'IF OBJECT_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ELSE OBJECT_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ENDIF OBJECT_') === FALSE)				
			{
				// This just a double check to ensure that no substitutions are missed
				$line_store = $this->searchline($input_array[$i],$this->search_replace);
				
				$output_array[] =$line_store;
			}			
		}
		
		return $output_array;
	}		
	/**
	* A recursive prodedure to expand out the tagged areas of input for a fieldset
	* this may contain a set of conditions which are set on the fieldset data itself. 
	* 
	* @param		object	Fieldset object
	* @param		array	Array of code lines to be checked for fieldset conditions
	*   
	* @return		array   Array of code lines that have been processed
	*/ 
	private function process_fieldset_conditions($fieldset, $input_array)
	{
		$output_array = array();
		// Check for IF FIELDSET_ conditions
		for($i=0; $i<count($input_array); $i++)
		{ 
			if(JString::strpos($input_array[$i],$this->markup_prefix.'IF FIELDSET_') !== FALSE)
			{
				$block_start = $this->file_line_no;

				$start = JString::strpos($input_array[$i],$this->markup_prefix.'IF FIELDSET_')+15;
				$end = JString::strpos($input_array[$i],$this->markup_suffix);
				$length = $end - $start;					
				$fieldset_condition = JString::substr($input_array[$i],$start,$length);
				$includecode = false;			
				switch ($fieldset_condition)
				{
					// Conditions on fieldset status				
					case 'DEFAULT':
						if ($fieldset->default)
						{
							$includecode = true;
						}
						break;						
					case 'PREDEFINED':
						if ($fieldset->predefined_fieldset)
						{
							$includecode = true;
						}
						break;
					// NOT conditions on fieldset status	
					case 'NOT_DEFAULT':
						if (!$fieldset->default)
						{
							$includecode = true;
						}
						break;						
					case 'NOT_PREDEFINED':
						if (!$fieldset->predefined_fieldset)
						{
							$includecode = true;
						}
						break;
					default:
						if (strpos($fieldset_condition, 'NOT_') === false)
						{
							if ($fieldset->code_name == strtolower($fieldset_condition))
							{
								$includecode = true;
							}
							break;
						}
						else
						{
							if ($fieldset->code_name != strtolower(str_replace('NOT_','', $fieldset_condition)))
							{
								$includecode = true;
							}
							break;						
						}
						
						$includecode = false;					
						break;
				}

				
				$i++;
				$store_array = array();
				$not_else = true;
				for($j=$i; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDIF FIELDSET_'.JString::strtoupper($fieldset_condition).$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						if(JString::strpos($input_array[$j],$this->markup_prefix.'ELSE FIELDSET_'.JString::strtoupper($fieldset_condition).$this->markup_suffix) !== FALSE)
						{
							$not_else = false;							
						}
						else
						{
							$i++;
							if (($includecode AND $not_else) OR (!$includecode AND !$not_else))
							{
								$store_array[] = $input_array[$j];
							}
						}
					}
				}
				
				$expanded_array = $this->process_fieldset_conditions($fieldset, $store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1024_IF_FIELDSET_STATEMENT_NO_ENDIF', JString::strtoupper($fieldset_condition)),'errorcode' => 'gen1024');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						for ($l= 0; $l < count($expanded_array); $l++)
						{
							$output_array[] = $expanded_array[$l];
						}						
					}
					$i =$j;
				}

			}
			// Nothing to handle so just output the line	
			if (JString::strpos($input_array[$i],$this->markup_prefix.'IF FIELDSET_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ELSE FIELDSET_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ENDIF FIELDSET_') === FALSE)				
			{
				// This just a double check to ensure that no substitutions are missed
				$line_store = $this->searchline($input_array[$i],$this->search_replace);
				
				$output_array[] =$line_store;
			}			
		}
		
		return $output_array;
	}	
	/**
	* A recursive prodedure to expand out the tagged areas of input for a field
	* this may contain a set of conditions which are set on the field data itself. 
	* 
	* @param		object	Field object
	* @param		array	Array of code lines to be checked for field conditions
	* 
	* @return		array   Array of code lines that have been processed
	*/ 
	private function process_field_conditions($field, $input_array)
	{
		$output_array = array();
		// Check for IF FIELD_ conditions
		for($i=0; $i<count($input_array); $i++)
		{ 
			if(JString::strpos($input_array[$i],$this->markup_prefix.'IF FIELD_') !== FALSE)
			{
				$block_start = $this->file_line_no;
				
				$start = JString::strpos($input_array[$i],$this->markup_prefix.'IF FIELD_')+12;
				$end = JString::strpos($input_array[$i],$this->markup_suffix);
				$length = $end - $start;					
				$field_condition = JString::substr($input_array[$i],$start,$length);
				$includecode = false;			
				switch ($field_condition)
				{
					// Conditions on field type				
					case 'HIDDEN':
						if ($field->hidden == 1 OR $field->ft_fieldtype_code_name == 'hidden')
						{
							$includecode = true;
						}
						break;							
					case 'INTEGER':
						if ($field->ft_fieldtype_code_name == 'integer' OR 
							($field->ft_fieldtype_code_name == 'text' AND $field->validation_type == 'numeric'))
						{
							$includecode = true;
						}
						break;
					case 'LANGUAGE':
						if ($field->ft_fieldtype_code_name == 'contentlanguage')
						{
							$includecode = true;
						}
						break;							
					case 'LIST':
						if ($field->ft_fieldtype_code_name == 'list' OR
							$field->ft_fieldtype_code_name == 'combo')
						{
							$includecode = true;
						}
						break;											
					case 'HAS_OPTIONS':
						if (in_array($field->ft_fieldtype_code_name, array('list','groupedlist','combo','checkboxes','radio')))
						{
							$includecode = true;
						}
						break;	
					// Conditions on field flags				
					case 'FILTER':
						if ($field->filter == 1)
						{
							$includecode = true;
						}

						break;																																					
					case 'FILTER_LINK':
						if ($field->filter == 1)
						{
							if ($field->ft_fieldtype_code_name == 'modal')
							{
								$includecode = true;
							}
						}
						break;
					case 'LINK':
						if ($field->ft_fieldtype_code_name == 'modal')
						{
							$includecode = true;
						}
						break;
					case 'MULTIPLE':
						if ($field->multiple == 1)
						{
							$includecode = true;
						}
						break;							
					case 'ORDER':
						if ($field->order == 1)
						{
							$includecode = true;
						}
						break;
					case 'PREDEFINED':
						if ($field->predefined_field == 1)
						{
							$includecode = true;
						}
						break;									
					case 'REQUIRED':
						if ($field->required == 1)
						{
							$includecode = true;
						}
						break;	
					case 'SEARCH':
						if ($field->search == 1)
						{
							$includecode = true;
						}
						break;	
					// NOT conditions on field type	
					case 'NOT_HIDDEN':
						if ($field->hidden != 1 AND $field->ft_fieldtype_code_name != 'hidden')
						{
							$includecode = true;
						}
						break;												
					case 'NOT_INTEGER':
						if ($field->ft_fieldtype_code_name != 'integer' AND 
							($field->ft_fieldtype_code_name != 'text' OR $field->validation_type != 'numeric'))
						{
							$includecode = true;
						}
						break;
					case 'NOT_LANGUAGE':
						if ($field->ft_fieldtype_code_name != 'contentlanguage')
						{
							$includecode = true;
						}
						break;								
					case 'NOT_LIST':
						if ($field->ft_fieldtype_code_name != 'list' AND
							$field->ft_fieldtype_code_name != 'combo')
						{
							$includecode = true;
						}
						break;	
					case 'NOT_HAS_OPTIONS':
						if (!in_array($field->ft_fieldtype_code_name, array('list','groupedlist','combo','checkboxes','radio')))
						{
							$includecode = true;
						}
						break;																
					// NOT conditions on field flags				
					case 'NOT_FILTER':
						if ($field->filter != 1)
						{
							$includecode = true;
						}

						break;	
					case 'NOT_FILTER_LINK':
						if ($field->filter != 1 OR is_null($field->foreign_object_id) OR $field->foreign_object_id <= 0 )
						{
							$includecode = true;
						}
						break;
					case 'NOT_LINK':
						if (is_null($field->foreign_object_id) OR $field->foreign_object_id <= 0 )
						{
							$includecode = true;
						}
						break;
					case 'NOT_MULTIPLE':
						if ($field->multiple != 1)
						{
							$includecode = true;
						}
						break;													
					case 'NOT_ORDER':
						if ($field->order != 1)
						{
							$includecode = true;
						}
						break;												
					case 'NOT_REQUIRED':
						if ($field->required != 1)
						{
							$includecode = true;
						}
						break;
					case 'NOT_PREDEFINED':
						if ($field->predefined_field != 1)
						{
							$includecode = true;
						}
						break;									
					case 'NOT_SEARCH':
						if ($field->search != 1)
						{
							$includecode = true;
						}
						break;																	
					default:
						if (strpos($field_condition, 'NOT_') === false)
						{
							if ($field->ft_fieldtype_code_name == strtolower($field_condition))
							{
								$includecode = true;
							}
							break;
						}
						else
						{
							if ($field->ft_fieldtype_code_name != strtolower(str_replace('NOT_','', $field_condition)))
							{
								$includecode = true;
							}
							break;						
						}					
						$includecode = false;					
						break;
				}

				
				$i++;
				$store_array = array();
				$not_else = true;
				for($j=$i; $j<count($input_array); $j++)
				{
					if(JString::strpos($input_array[$j],$this->markup_prefix.'ENDIF FIELD_'.JString::strtoupper($field_condition).$this->markup_suffix) !== FALSE)
					{
						break;							
					}
					else
					{
						if(JString::strpos($input_array[$j],$this->markup_prefix.'ELSE FIELD_'.JString::strtoupper($field_condition).$this->markup_suffix) !== FALSE)
						{
							$not_else = false;							
						}
						else
						{
							$i++;
							if (($includecode AND $not_else) OR (!$includecode AND !$not_else))
							{
								$store_array[] = $input_array[$j];
							}
						}
					}
				}
				
				$expanded_array = $this->process_field_conditions($field, $store_array);
				
				if ($j == count($input_array))
				{
					$warning = array('message' => JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1025_IF_FIELD_STATEMENT_NO_ENDIF', JString::strtoupper($field_condition)),'errorcode' => 'gen1025');
					$this->progress->outputWarning($this->token, $warning, $this->current_file, $block_start);
				}
				else
				{
					if (count($expanded_array) > 0)
					{
						for ($l= 0; $l < count($expanded_array); $l++)
						{
							$output_array[] = $expanded_array[$l];
						}						
					}
					$i =$j;
				}

			}
			// Nothing to handle so just output the line	
			if (JString::strpos($input_array[$i],$this->markup_prefix.'IF FIELD_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ELSE FIELD_') === FALSE AND
				JString::strpos($input_array[$i],$this->markup_prefix.'ENDIF FIELD_') === FALSE)				
			{
				// This just a double check to ensure that no substitutions are missed
				$line_store = $this->searchline($input_array[$i],$this->search_replace);
				
				$output_array[] =$line_store;
			}			
		}
		
		return $output_array;
	}
	/**
	* The main search and replace by line routine. 
	* 
	* @param		string	A single line of code
	* @param		array	Search Replace pairs to be sued to search line and then replace if found
	* 
	* @return		string	Amended line of code
	*/ 
	private function searchline($codeline, $search_replace)
	{ 
		$result = '';
		if(count($this->ignore_lines) > 0)
		{ 
			for($j=0; $j<count($this->ignore_lines); $j++)
			{ 
				if(JString::substr($codeline,0,JString::strlen($this->ignore_lines[$j])) == $this->ignore_lines[$j])
				{
					$result = $codeline;
					return $result;
				} 
			} 
		} 
		$result = $codeline;	
		foreach ($search_replace as $s_r_pair)
		{
			$this->occurrences += count(explode($s_r_pair['search'], $result)) - 1; 

			$result = str_replace($s_r_pair['search'], $s_r_pair['replace'], $result);
		}	
		if ($result == $codeline OR JString::trim($result) <> '')
		{
			return $result;	
		}
		else
		{
			return '';
		}
	}
	/**
	* The quick search function. Does not support the ignore_lines feature. 
	* 
	* @param	string	File name to search
	* 
	* @return	array or false	Array of occurrences in files or false if error
	*/ 
	private function quickSearch($file_name)
	{ 

		clearstatcache(); 

		$file = fread($fp = fopen($file_name, 'r'), filesize($file_name)); fclose($fp); 
		
		foreach ($this->search_replace as $s_r_pair)
		{		
			$occurrences = +count(explode($s_r_pair['search'], $file)) - 1; 

			$s_r_occurrences = count(explode($s_r_pair['search'], $file)) - 1;	
			
			$file       = str_replace($s_r_pair['search'], $s_r_pair['replace'], $file); 
			
		}

		if($occurrences > 0) $return = array($occurrences, $file); else $return = FALSE; 
		return $return; 
	} 

	/**
	* Function for writing out a new file. 
	* 
	* @param		string	Path and file name of file to be output
	* @param		array	Array of the contents of the file
	* 
	* @return		array   Array of code lines that have been processed
	*/ 
	private function writeOutFile($file_name, $contents)
	{ 
		if ($fp = @fopen($file_name, 'w'))
		{ 
			flock($fp,2); 
			fwrite($fp, $contents); 
			flock($fp,3); 
			fclose($fp); 
		}
		else
		{ 
			$warning = array('message' => JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1051_FAILED_FILE_OPEN',$file_name),'errorcode' => 'gen1051');
			$this->progress->outputWarning($this->token, $warning);
		} 

	} 


	/**
	* Internal function called by doSearchReplace() to sort out any files that need searching. 
	* 
	* @param		string	Search function
	* 
	*/ 
	private function _doFiles($search_function)
	{ 
		if(!is_array($this->files)) $this->files = explode(',', $this->files); 
		for($i=0; $i<count($this->files); $i++)
		{
			if (is_file($this->files[$i]))
			{
				$newfile = $this->$search_function($this->files[$i]); 
				if(is_array($newfile) == TRUE)
				{ 
					$this->writeOutFile($this->files[$i], $newfile[1]); 
					$this->occurrences += $newfile[0]; 
				} 
			}
		} 
	} 
	/**
	* Internal function called by do_directories() * to process the search in a directory and 
	* iterate through sub directories if required. 
	* 
	* @param		string	Search function
	* @param		string	Directory path
	* @param		string	Path of sub directory
	* 
	*/ 
	private function _processDirectory($search_function, $dir, $subdir = '')
	{
		if (JString::substr($dir,JString::strlen($dir)-1,1) != '/')
			$dir = $dir.'/';
		
		
		$dh = opendir($dir.$subdir);
		while($file = readdir($dh))
		{ 
			if($file == '.' OR $file == '..' OR $file == 'index.html') continue; 

			if (is_dir($dir.$subdir.$file))
			{ 
				if($this->include_subdir == true)
				{ 
					$this->_processDirectory($search_function,$dir.$subdir,$file.'/'); 
				} 
			}
			else
			{
				// Update Stage 3 progress - if logging requested this will also create a log record
				$step = JText::sprintf('COM_COMPONENTARCHITECT_GENERATE_STEP_STAGE_3', str_replace(JPATH_ROOT, '', $dir.$subdir.$file));
				$this->progress->setProgress($this->token, 'stage_3', $step);
				
				$newfile = $this->$search_function($dir.$subdir.$file); 
				if(is_array($newfile) == TRUE)
				{ 
					$this->writeOutFile($dir.$subdir.$file, $newfile[1]); 
					$this->occurrences += $newfile[0]; 
				} 
			}
		}
		closedir($dh);
	}
	/**
	* Internal function called by doSearchReplace() to sort out any dirs that need searching. 
	* 
	* @param		string	Search function
	* 
	*/ 
	private function _doDirectories($search_function)
	{ 
		if(!is_array($this->directories)) $this->directories = explode(',', $this->directories); 
		for($i=0; $i<count($this->directories); $i++)
		{ 
			$this->_processDirectory($search_function,$this->directories[$i]);
		} 
	} 

	/**
	* This starts the search/replace off. 
	* Call this to do the search. 
	* First do whatever files are specified, and/or if directories are specified, do those too. 
	* 
	* 
	*/ 
	public function doSearchReplace()
	{ 
		if((is_array($this->files) AND count($this->files) > 0) OR $this->files != '') $this->_doFiles($this->search_function); 
		if($this->directories != '')  $this->_doDirectories($this->search_function); 

	} 

} // End of class 
//[%%END_CUSTOM_CODE%%]
?> 
