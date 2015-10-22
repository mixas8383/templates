<?php

/**
 * @version			$Id: generate.php 419 2014-10-22 16:10:04Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
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
defined ( '_JEXEC' ) or die;

jimport ( 'joomla.filesystem.archive' );
jimport ( 'joomla.filesystem.archive.zip' );

require_once JPATH_COMPONENT . '/' . 'helpers' . '/' . 'searchreplace.php';
require_once JPATH_COMPONENT . '/' . 'helpers' . '/' . 'generateprogress.php';

class ComponentArchitectGenerateHelper
{

    protected $_component = null;
    protected $_code_template = null;
    protected $_code_templates_root = '';
    protected $_component_objects = null;
    protected $_search_replace_helper = null;
    protected $_fieldtypes = null;
    protected $_fieldtypes_index = array ();
    protected $_generic_values = array ();
    protected $_acronyms = array ();
    protected $_zip_file = '';
    protected $_component_path = '';
    protected $_progress;
    protected $_token = '';
    protected $_name_regex = '/[^a-zA-Z0-9\s\\/\-_+&()]/';

    /**
     * Constructor function. Sets up the required classes and objects.
     * 
     *  
     */
    public function __construct ()
    {
        $this->_progress = new ComponentArchitectGenerateProgressHelper();
    }

    /**
     * Generate component
     *
     * @param	component_id	integer		Id of the component to be generated
     * @param	code_template_id	integer	Id of the template to use as source
     * @param	output_path		string		Path to where the generated component will be stored
     * @param	zip_format		string		format of the zip file, currently only 'zip' allowed.  No zip if blank
     * @param	logging			integer		0 or 1 specfying whether a log file will be created
     *
     * @return	true or false
     */
    public function generateComponent ( $component_id, $code_template_id, $token, $output_path = 'tmp', $zip_format = '', $logging = 0 )
    {
        $this->_token = $token;
        $this->_logging = $logging;

        // Generate can be a very long running process so if possible set the php max execution time so it does not expire
        if ( function_exists ( 'ini_set' ) )
        {
            ini_set ( 'max_execution_time', '0' ); // 0 = no limit.
        }

        // If logging requested then set up the log file.
        if ( $this->_logging )
        {
            if ( !$this->_progress->openLog () )
            {
                $error = array ( 'message' => JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0000_LOG_FILE_OPEN_FAILED' ), 'errorcode' => 'gen0000' );
                $this->_progress->outputError ( $this->_token, $error );

                $this->_progress->completeProgress ( $this->_token );

                return false;
            }
        }

        $this->_search_replace_helper = new ComponentArchitectSearchReplaceHelper ( $this->_progress );

        $this->_search_replace_helper->setToken ( $this->_token );
        $this->_search_replace_helper->setLogging ( $this->_logging );



        if ( $code_template_id > 0 )
        {
            $code_template_model = JModelLegacy::getInstance ( 'codetemplate', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
            $this->_code_template = $code_template_model->getItem ( $code_template_id );

            if ( $this->_code_template === false )
            {
                $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0001_CANNOT_LOAD_COMPONENT', $code_template_id, $code_template_model->getError () ), 'errorcode' => 'gen0001' );
                $this->_progress->outputError ( $this->_token, $error );
                $this->_progress->completeProgress ( $this->_token );

                return false;
            }
            // Double check that only limited punctuation is present in name as this may cause php code or sql problems
            $this->_code_template->template_component_name = preg_replace ( $this->_name_regex, '', $this->_code_template->template_component_name );
            $this->_code_template->template_object_name = preg_replace ( $this->_name_regex, '', $this->_code_template->template_object_name );
        }
        else
        {
            /* No code template id provided */
            $error = array ( 'message' => JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0002_NO_CODE_TEMPLATE_SELECTED' ), 'errorcode' => 'gen0002' );
            $this->_progress->outputError ( $this->_token, $error );
            $this->_progress->completeProgress ( $this->_token );

            return false;
        }

        if ( $component_id > 0 )
        {

            $component_model = JModelLegacy::getInstance ( 'component', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
            $this->_component = $component_model->getItem ( $component_id );

            if ( $this->_component === false )
            {
                $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0003_CANNOT_LOAD_COMPONENT', $component_id, $component_model->getError () ), 'errorcode' => 'gen0003' );
                $this->_progress->outputError ( $this->_token, $error );
                $this->_progress->completeProgress ( $this->_token );

                return false;
            }
            // Double check that only limited punctuation is present in name as this may cause php code or sql problems
            $this->_component->name = preg_replace ( $this->_name_regex, '', $this->_component->name );

            $this->_component->set ( 'search_replace_pairs', $this->_getComponentSearchPairs ( $this->_code_template->template_component_name, $this->_component ) );
        }
        else
        {
            /* No component id provided */
            $error = array ( 'message' => JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0004_NO_COMPONENT_SELECTED' ), 'errorcode' => 'gen0004' );
            $this->_progress->outputError ( $this->_token, $error );
            $this->_progress->completeProgress ( $this->_token );

            return false;
        }
        // Initialise the Progress session data with the generate data

        $this->_progress->setInitialiseStage ( $this->_token, $logging, $this->_code_template->name, $this->_component->name );

        /*
         * Stage 1 - Analyse component
         */
        $count_fields = ( int ) $this->_countFields ( $component_id );

        $this->_progress->setProgressStage ( $this->_token, 'stage_1', $count_fields );

        $this->_search_replace_helper->setTemplateComponentName ( str_replace ( " ", "", JString::strtolower ( $this->_code_template->template_component_name ) ) );
        $this->_search_replace_helper->setTemplateObjectName ( str_replace ( " ", "", JString::strtolower ( $this->_code_template->template_object_name ) ) );

        $this->_search_replace_helper->setMarkupPrefix ( $this->_code_template->template_markup_prefix );
        $this->_search_replace_helper->setMarkupSuffix ( $this->_code_template->template_markup_suffix );

        $this->_search_replace_helper->setComponentName ( str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $this->_component->code_name ) ) ) );

        //Conditions are a generic set for the generate which may include other conditions besides Joomla! Features
        //Convert Joomla! Parts to conditions.  
        $no_generate_array = array ();
        // 1st pass to find all high level conditions that are set to not generate
        foreach ( $this->_component->get ( 'joomla_parts' ) as $name => $value )
        {
            // Higher levels must be set to generate otherwise all levels below will need to be set to not generate
            if ( ($name == 'generate_admin' OR $name == 'generate_site'
                    OR $name == 'generate_site_views' OR $name == 'generate_categories'
                    OR $name == 'generate_plugins' OR $name == 'generate_modules')
                    AND $value == '0' )
            {
                $no_generate_array[] = $name;
            }
        }

        // 2nd pass to check each of the conditions against the no generate ones
        foreach ( $this->_component->get ( 'joomla_parts' ) as $name => $value )
        {
            if ( $value == '1' )
            {
                for ( $i = 0; $i < count ( $no_generate_array ); $i++ )
                {
                    if ( JString::strpos ( $name, $no_generate_array[ $i ] ) !== false )
                    {
                        $value = '0';
                        break;
                    }
                }

                if ( $value == '1'
                        AND ( (JString::strpos ( $name, 'generate_categories_site' ) !== false
                        AND in_array ( 'generate_site', $no_generate_array ))
                        OR ( JString::strpos ( $name, 'generate_site_layout' ) !== false
                        AND in_array ( 'generate_site_views', $no_generate_array )))
                )
                {
                    $value = '0';
                }
            }
            $this->_search_replace_helper->setComponentConditions ( $name, ( int ) $value );
        }

        //Convert Joomla! Features to conditions.  

        foreach ( $this->_component->get ( 'joomla_features' ) as $name => $value )
        {
            $this->_search_replace_helper->setComponentConditions ( $name, ( int ) $value );
        }

        // Populate the Component Objects
        if ( !$this->_getFieldTypes () )
        {
            $this->_progress->completeProgress ( $this->_token );

            return false;
        }
        $this->_component_objects = $this->_getComponentObjects ( $component_id, $this->_component->get ( 'default_object_id' ), $this->_code_template->template_component_name, $this->_code_template->template_object_name );
        if ( $this->_component_objects === false )
        {
            /* Problem loading component objects */
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0012_CANNOT_POPULATE_COMPONENT_OBJECTS', $this->_component->name ), 'errorcode' => 'gen0012' );
            $this->_progress->outputError ( $this->_token, $error );
            $this->_progress->completeProgress ( $this->_token );

            return false;
        }

        $this->_search_replace_helper->setComponentObjects ( $this->_component_objects );

        $generic_language_vars = implode ( '', $this->_generic_values );

        // Save search replace pair for the all generic field values
        array_push ( $this->_component->search_replace_pairs, array ( 'search' => $this->_markupText ( 'COM_' . JString::strtoupper ( str_replace ( ' ', '', $this->_code_template->template_component_name ) ) . '_GENERIC_FIELD_VALUES' ), 'replace' => $generic_language_vars ) );


        // Update Stage 1 progress as being complete if logging requested this will also create a log record
        $step = JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_END_STAGE_1' );
        $this->_progress->setProgress ( $this->_token, 'stage_1', $step, true );

        /*
         * Stage 2 - Create Files
         */
        $this->_code_templates_root = JPATH_COMPONENT_ADMINISTRATOR . '/' . 'codetemplates';
        $files_count = $this->_countFiles ( JPath::clean ( $this->_code_templates_root . '/' . $this->_code_template->source_path ) );

        $excluded_files_count = $this->_countExcludedFiles ( JPath::clean ( $this->_code_templates_root . '/' . $this->_code_template->source_path ) );

        $files_count = $files_count - $excluded_files_count;

        $this->_progress->setProgressStage ( $this->_token, 'stage_2', $files_count );

        $template_source_path = JPath::clean ( $this->_code_templates_root . '/' . $this->_code_template->source_path );
        $dir = opendir ( $template_source_path );
        while ( false !== ( $file = readdir ( $dir )) )
        {
            if ( $file != '.' AND $file != '..' AND ! is_file ( $template_source_path . '/' . $file ) )
            {
                $src_file = $file;
                $src_path = $template_source_path . '/' . $file;
                break;
            }
        }
        closedir ( $dir );
        $dst_file = str_replace ( str_replace ( " ", "", JString::strtolower ( $this->_code_template->template_component_name ) ), str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $this->_component->code_name ) ) ), $src_file );
        $dst_path = JPath::clean ( JPATH_SITE . '/' . $output_path . '/' . $dst_file );

        // Call the main function to recursively make copies of the folders and files in the Code Template
        $this->_search_replace_helper->recursiveCopy ( $src_path, $dst_path );

        // Update Stage 2 progress as being complete if logging requested this will also create a log record
        $step = JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_END_STAGE_2' );
        $this->_progress->setProgress ( $this->_token, 'stage_2', $step, true );
        /*
         * Stage 3 - Search/Replace Markup
         */

        $files_count = $this->_countFiles ( $dst_path );
        $this->_progress->setProgressStage ( $this->_token, 'stage_3', $files_count );

        // Initialise the search - replace parameters
        $this->_search_replace_helper->initialiseSearchReplace ( $this->_component->get ( 'search_replace_pairs' ), '', $dst_path );

        // Call the main search and replace function
        $this->_search_replace_helper->doSearchReplace ();

        // Finally need to copy icons for components and component object
        if ( $this->_component->icon_16px != '' )
        {
            copy ( JPATH_SITE . '/' . $this->_component->icon_16px, $dst_path . '/media/images/' . str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $this->_component->code_name ) ) ) . '.png' );
            copy ( JPATH_SITE . '/' . $this->_component->icon_16px, $dst_path . '/media/images/icon-16-' . str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $this->_component->code_name ) ) ) . '.png' );
        }

        if ( $this->_component->icon_48px != '' )
        {
            $image_name = str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $this->_component->code_name ) ) ) . '.png';
            copy ( JPATH_SITE . '/' . $this->_component->icon_48px, $dst_path . '/media/images/icon-48-' . $image_name );
            ComponentArchitectHelper::createThumb ( JPATH_SITE . '/' . $this->_component->icon_48px, $dst_path . '/media/images/icon-32-' . $image_name, 32, 32 );
            ComponentArchitectHelper::createThumb ( JPATH_SITE . '/' . $this->_component->icon_48px, $dst_path . '/media/images/icon-24-' . $image_name, 24, 24 );
        }
        // If component has to generate categories then include images for those in both admin assets and media (for auto menu items and Header images)
        if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) )
        {
            if ( $this->_component->categories_icon_16px != '' )
            {
                copy ( JPATH_SITE . '/' . $this->_component->categories_icon_16px, $dst_path . '/media/images/' . str_replace ( "_", "", JString::strtolower ( $this->_component->code_name ) ) . '-categories.png' );
                copy ( JPATH_SITE . '/' . $this->_component->categories_icon_16px, $dst_path . '/media/images/icon-16-categories.png' );
            }

            if ( $this->_component->categories_icon_48px != '' )
            {
                copy ( JPATH_SITE . '/' . $this->_component->categories_icon_48px, $dst_path . '/media/images/icon-48-categories.png' );
                ComponentArchitectHelper::createThumb ( JPATH_SITE . '/' . $this->_component->categories_icon_48px, $dst_path . '/media/images/icon-32-categories.png', 32, 32 );
                ComponentArchitectHelper::createThumb ( JPATH_SITE . '/' . $this->_component->categories_icon_48px, $dst_path . '/media/images/icon-24-categories.png', 24, 24 );
            }
        }

        foreach ( $this->_component_objects as $component_object )
        {
            if ( $component_object->icon_16px != '' )
            {
                copy ( JPATH_SITE . '/' . $component_object->icon_16px, $dst_path . '/media/images/' . str_replace ( "_", "", JString::strtolower ( $this->_component->code_name ) ) . '-' . str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $component_object->plural_code_name ) ) ) . '.png' );
                copy ( JPATH_SITE . '/' . $component_object->icon_16px, $dst_path . '/media/images/icon-16-' . str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $component_object->plural_code_name ) ) ) . '.png' );
            }

            if ( $component_object->icon_48px != '' )
            {
                $image_name = str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $component_object->plural_code_name ) ) ) . '.png';
                copy ( JPATH_SITE . '/' . $component_object->icon_48px, $dst_path . '/media/images/icon-48-' . $image_name );
                ComponentArchitectHelper::createThumb ( JPATH_SITE . '/' . $component_object->icon_48px, $dst_path . '/media/images/icon-32-' . $image_name, 32, 32 );
                ComponentArchitectHelper::createThumb ( JPATH_SITE . '/' . $component_object->icon_48px, $dst_path . '/media/images/icon-24-' . $image_name, 24, 24 );
            }
        }

        // Update Stage 3 progress as being complete if logging requested this will also create a log record
        $step = JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_END_STAGE_3' );
        $this->_progress->setProgress ( $this->_token, 'stage_3', $step, true );

        $component_path = $output_path . '/' . $dst_file;

        $zip_file = $this->_zip_file;

        if ( $zip_format <> '' )
        {
            jimport ( 'joomla.filesystem.archive' );

            $zipFilesArray = array ();
            $dirs = JFolder::folders ( $dst_path, '.', true, true );
            array_push ( $dirs, $dst_path );
            foreach ( $dirs as $dir )
            {
                $files = JFolder::files ( $dir, '.', false, true );
                foreach ( $files as $file )
                {
                    $data = JFile::read ( $file );
                    $zipFilesArray[] = array ( 'name' => str_replace ( $dst_path . '\\', '', str_replace ( $dst_path . '/', '', $file ) ), 'data' => $data );
                }
            }

            $zip = JArchive::getAdapter ( $zip_format );
            $zip->create ( JPATH_SITE . '/' . $output_path . '/' . $dst_file . '.' . $zip_format, $zipFilesArray );
            $zip_file = $dst_file . '.' . $zip_format;

            $zip_path = JUri::root () . $output_path;
        }
        else
        {
            $zip_file = '';
            $zip_path = '';
        }

        $this->_progress->completeProgress ( $this->_token, $component_path, $zip_path, $zip_file );

        return true;
    }

    /**
     * Method to get all the published component objects for a component.
     *
     * @param	component_id			Int		Id of the component
     * @param	default_object_id		Int		Id of the component object set as the default object for the component
     * @param	template_component_name	String	Template component name used to generate search/replace pairs
     * @param	template_object_name	String	Template object name used to generate search/replace pairs
     *
     * @return	componentobjects		
     */
    protected function _getComponentObjects ( $component_id, $default_object_id, $template_component_name, $template_object_name )
    {
        /* get all the published component objects for the component */
        $component_objects_model = JModelLegacy::getInstance ( 'componentobjects', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
        $component_objects_model->setState ( 'filter.component_id', $component_id );
        $component_objects_model->setState ( 'list.ordering', 'a.ordering' );
        $component_objects_model->setState ( 'list.direction', 'ASC' );
        $component_objects_model->setState ( 'list.select', 'a.*' );

        $component_objects = $component_objects_model->getItems ();

        if ( $component_objects === false )
        {
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0005_CANNOT_LOAD_COMPONENT_OBJECTS', $this->_component->name, $component_objects_model->getError () ), 'errorcode' => 'gen0005' );
            $this->_progress->outputError ( $this->_token, $error );

            return false;
        }

        if ( count ( $component_objects ) <= 0 )
        {
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0006_NO_COMPONENT_OBJECTS', $this->_component->name ), 'errorcode' => 'gen0006' );
            $this->_progress->outputError ( $this->_token, $error );

            return false;
        }
        // If default object not set then set it to the first component object
        if ( !isset ( $default_object_id ) OR $default_object_id <= 0 )
        {
            $default_object_id = $component_objects[ 0 ]->id;
        }
        else
        {
            // Check default object is one of the current set of component objects and if not set it to the first component object
            $default_object_id_found = false;
            foreach ( $component_objects as $component_object )
            {
                if ( $component_object->id == $default_object_id )
                {
                    $default_object_id_found = true;
                    break;
                }
            }
            if ( !$default_object_id_found )
            {
                $default_object_id = $component_objects[ 0 ]->id;
            }
        }

        foreach ( $component_objects as $component_object )
        {

            if ( $this->_populateComponentObject ( $component_object, $default_object_id, $template_component_name, $template_object_name ) === false )
            {
                return false;
            }
            else
            {
                if ( $this->_getChildComponentObjects ( $component_object, $default_object_id, $template_component_name, $template_object_name ) === false )
                {
                    return false;
                }
            }
        }

        // Once all the component objects are set then want to set include (Joomla! features) conditions 
        // so that if any component object has the condition as include it is also included at the component level
        //Convert Joomla! Features to conditions.  
        foreach ( $component_objects as $component_object )
        {
            foreach ( $component_object->joomla_features as $name => $value )
            {
                if ( $value == "1" )
                {
                    $this->_search_replace_helper->setComponentConditions ( $name, 1 );
                }
            }
        }
        return $component_objects;
    }

    /**
     * Method to get all the child component objects for a component object.
     *
     * @param	componentobject				integer	Component Object
     * @param	default_object_id			integer	Id of the component object set as the default object for the component
     * @param	template_component_name		string	Template component name used to generate search/replace pairs
     * @param	template_object_name		string	Template object name used to generate search/replace pairs
     *
     * @return	childcomponentobjects		
     */
    protected function _getChildComponentObjects ( &$component_object, $default_object_id, $template_component_name, $template_object_name )
    {
        /* get all cascade fields with the Foreign Object set as the current component object */
        $fields_model = JModelLegacy::getInstance ( 'fields', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
        $fields_model->setState ( 'filter.foreign_object_id', $component_object->id );
        $fields_model->setState ( 'filter.cascade_object', 1 );
        $fields_model->setState ( 'list.ordering', 'a.ordering' );
        $fields_model->setState ( 'list.direction', 'ASC' );
        $fields_model->setState ( 'list.select', 'a.*' );

        $cascade_fields = $fields_model->getItems ();


        if ( $cascade_fields === false )
        {
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0007_CANNOT_LOAD_CHILD_COMPONENT_OBJECTS', $component_object->name, $fields_model->getError () ), 'errorcode' => 'gen0007' );
            $this->_progress->outputError ( $this->_token, $error );

            return false;
        }
        $child_component_object_array = array ();
        if ( count ( $cascade_fields ) > 0 )
        {
            foreach ( $cascade_fields as $cascade_field )
            {

                /* get all the published child component objects for the component */
                $child_component_object_model = JModelLegacy::getInstance ( 'componentobject', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );


                $child_component_object = $child_component_object_model->getItem ( $cascade_field->component_object_id );

                if ( $child_component_object === false )
                {
                    $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0007_CANNOT_LOAD_CHILD_COMPONENT_OBJECTS', $component_object->name, $child_component_object_model->getError () ), 'errorcode' => 'gen0007' );
                    $this->_progress->outputError ( $this->_token, $error );

                    return false;
                }

                if ( $this->_populateComponentObject ( $child_component_object, $default_object_id, $template_component_name, $template_object_name ) === false )
                {
                    return false;
                }

                $child_search_replace = $this->_getComponentObjectSearchPairs ( $template_object_name, $child_component_object, true );

                $child_component_object->search_replace = $child_search_replace;

                $child_component_object_array[] = $child_component_object;
            }
        }
        $component_object->child_component_objects = $child_component_object_array;
        return $component_object;
    }

    /**
     * Method to populate the data for a component object.
     *
     * @param	component_object		object	Component object passed by ref
     * @param	default_object_id		integer	Id of the component object set as the default object for the component
     * @param	template_component_name	string	Template component name used to generate search/replace pairs
     * @param	template_object_name	string	Template object name used to generate search/replace pairs
     *
     * @return	fieldsets				array	An array of fieldset objects		
     */
    protected function _populateComponentObject ( &$component_object, $default_object_id, $template_component_name, $template_object_name )
    {

        // Double check that only limited punctuation is present in name as this may cause php code or sql problems
        $component_object->name = preg_replace ( $this->_name_regex, '', $component_object->name );

        require_once JPATH_COMPONENT . '/helpers/pluralise.php';
        if ( $component_object->plural_name == '' )
        {
            // ??? Need to add a check if language is english first	once Component Architect is made multi-lingual
            $component_object->plural_name = ComponentArchitectPluraliseHelper::pluralise ( $component_object->name );
        }
        else
        {
            // Double check that no punctuation is present in name as this may cause php code or sql problems
            $component_object->plural_name = preg_replace ( $this->_name_regex, '', $component_object->plural_name );
        }

        if ( $component_object->code_name == '' )
        {
            $component_object->code_name = str_replace ( ' ', '_', JString::strtolower ( JString::trim ( $component_object->name ) ) );
        }
        if ( $component_object->plural_code_name == '' )
        {
            $component_object->plural_code_name = str_replace ( ' ', '_', JString::strtolower ( JString::trim ( $component_object->plural_name ) ) );
        }
        if ( $component_object->short_name == '' )
        {
            $component_object->short_name = str_replace ( ' ', '_', JString::strtolower ( JString::trim ( $component_object->name ) ) );
        }
        else
        {
            // Double check that no punctuation is present in name as this may cause php code or sql problems
            $component_object->short_name = preg_replace ( $this->_name_regex, '', $component_object->short_name );
        }
        if ( $component_object->short_plural_name == '' )
        {
            $component_object->short_plural_name = str_replace ( ' ', '_', JString::strtolower ( JString::trim ( $component_object->plural_name ) ) );
        }
        else
        {
            // Double check that no punctuation is present in name as this may cause php code or sql problems
            $component_object->short_plural_name = preg_replace ( $this->_name_regex, '', $component_object->short_plural_name );
        }
        //Conditions are a generic set for the generate which may include other conditions besides Joomla! Features
        //Convert Joomla! Parts to conditions.  
        $no_generate_array = array ();

        // 1st pass to find all high level conditions that are set to not generate
        foreach ( $component_object->joomla_parts as $name => $value )
        {
            // Higher levels must be set to generate otherwise all levels below will need to be set to not generate
            if ( ($name == 'generate_admin' OR $name == 'generate_site'
                    OR $name == 'generate_plugins' OR $name == 'generate_modules')
                    AND $value == '0' )
            {
                $no_generate_array[] = $name;
            }
        }

        // 2nd pass to check each of the conditions against the no generate ones
        foreach ( $component_object->joomla_parts as $name => $value )
        {
            // For the generate conditions in Joomla! Parts then only do so at object level if set at component level
            if ( $this->_search_replace_helper->getComponentConditions ( $name ) )
            {
                if ( $value == '1' )
                {
                    for ( $i = 0; $i < count ( $no_generate_array ); $i++ )
                    {
                        if ( JString::strpos ( $name, $no_generate_array[ $i ] ) !== false )
                        {
                            // make sure the value is set to not generate
                            $value = '0';
                            break;
                        }
                    }

                    if ( $value == '1'
                            AND ( (JString::strpos ( $name, 'generate_categories_site' ) !== false
                            AND in_array ( 'generate_site', $no_generate_array ))
                            OR ( JString::strpos ( $name, 'generate_site_layout' ) !== false
                            AND in_array ( 'generate_site_views', $no_generate_array )))
                    )
                    {
                        $value = '0';
                    }
                }
            }
            else
            {
                $value = '0';
            }
            $component_object->conditions[ $name ] = ( int ) $value;
        }

        //Convert Joomla! Features to conditions.  

        foreach ( $component_object->joomla_features as $name => $value )
        {
            if ( $value == "" )
            {
                $component_object->conditions[ $name ] = ( int ) $this->_search_replace_helper->getComponentConditions ( $name ) ? $this->_search_replace_helper->getComponentConditions ( $name ) : 0;
            }
            else
            {
                $component_object->conditions[ $name ] = ( int ) $value;
            }
        }

        if ( $component_object->id == $default_object_id )
        {
            if ( !$this->_code_template->multiple_category_objects AND $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 1 )
            {
                $component_object->conditions[ 'generate_categories' ] = 1;
            }
            $component_object->default_object = true;
            // Search/Replace set here as in not known earlier for _getComponentSearchPairs
            // e.g. 'search' => '[%%architectcomp_default_admin_view%%]', 'replace' => 'groups'
            array_push ( $this->_component->search_replace_pairs, array ( 'search' => $this->_markupText ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) . '_default_admin_view' ), 'replace' => JString::strtolower ( str_replace ( "_", "", $component_object->plural_code_name ) ) ) );
            // e.g. 'search' => '[%%architectcomp_default_view%%]', 'replace' => 'groups'
            array_push ( $this->_component->search_replace_pairs, array ( 'search' => $this->_markupText ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) . '_default_view' ), 'replace' => JString::strtolower ( str_replace ( "_", "", $component_object->plural_code_name ) ) ) );
        }
        else
        {
            if ( !$this->_code_template->multiple_category_objects )
            {
                $component_object->conditions[ 'generate_categories' ] = 0;
                $component_object->conditions[ 'generate_categories_site_views_categories' ] = 0;
                $component_object->conditions[ 'generate_categories_site_views_category' ] = 0;
            }

            $component_object->default_object = false;
        }

        // Initialise acronym store 
        $this->_acronyms = array ();

        $this->_acronyms[ 'id' ] = 'a';
        if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 1 )
        {
            $this->_acronyms[ 'catid' ] = 'c';
        }
        if ( $component_object->conditions[ 'include_access' ] == 1
                OR $this->_search_replace_helper->getComponentConditions ( 'include_access' ) == 1 )
        {
            $this->_acronyms[ 'access' ] = 'ag';
        }
        if ( $component_object->conditions[ 'include_checkout' ] == 1
                OR $this->_search_replace_helper->getComponentConditions ( 'include_checkout' ) == 1 )
        {
            $this->_acronyms[ 'checked_out' ] = 'uc';
        }
        if ( $component_object->conditions[ 'include_created' ] == 1
                OR $this->_search_replace_helper->getComponentConditions ( 'include_created' ) == 1 )
        {
            $this->_acronyms[ 'created_by' ] = 'ua';
        }
        if ( $component_object->conditions[ 'include_modified' ] == 1
                OR $this->_search_replace_helper->getComponentConditions ( 'include_modified' ) == 1 )
        {
            $this->_acronyms[ 'modified_by' ] = 'uam';
        }
        if ( $component_object->conditions[ 'generate_plugins_vote' ] == 1
                OR $this->_search_replace_helper->getComponentConditions ( 'generate_plugins_vote' ) == 1 )
        {
            $this->_acronyms[ 'rating' ] = 'v';
        }


        $component_object->search_replace = $this->_getComponentObjectSearchPairs ( $template_object_name, $component_object );

        if ( $this->_getFieldsets ( $component_object ) === false )
        {
            return false;
        }


        if ( $this->_getFields ( $component_object ) === false )
        {
            return false;
        }

        return $component_object;
    }

    /**
     * Method to get all the published fieldsets for a component object.
     *
     * @param	component_object		object	Component object passed by ref
     *
     * @return	fieldsets				array	An array of fieldset objects		
     */
    protected function _getFieldsets ( &$component_object )
    {
        /* get all the published field sets for a component object */
        $fieldsets_model = JModelLegacy::getInstance ( 'fieldsets', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
        $fieldsets_model->setState ( 'filter.component_object_id', $component_object->id );
        $fieldsets_model->setState ( 'list.ordering', 'a.ordering' );
        $fieldsets_model->setState ( 'list.direction', 'ASC' );
        $fieldsets_model->setState ( 'list.select', 'a.*' );

        $fieldsets = $fieldsets_model->getItems ();

        if ( $fieldsets === false )
        {
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0008_CANNOT_LOAD_FIELDSETS', $component_object->name, $fieldsets_model->getError () ), 'errorcode' => 'gen0008' );
            $this->_progress->outputError ( $this->_token, $error );

            return false;
        }

        if ( count ( $fieldsets ) <= 0 )
        {
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0009_NO_FIELDSETS', $component_object->name ), 'errorcode' => 'gen0009' );
            $this->_progress->outputError ( $this->_token, $error );

            return false;
        }
        $fieldsets_array = array ();
        foreach ( $fieldsets as $fieldset )
        {
            // Double check that no punctuation is present in name as this may cause php code or sql problems
            $fieldset->name = preg_replace ( $this->_name_regex, '', $fieldset->name );

            if ( $fieldset->code_name == '' )
            {
                $fieldset->code_name = str_replace ( ' ', '_', JString::strtolower ( JString::trim ( $fieldset->name ) ) );
            }

            $search_replace_pairs = array ();

            array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_CODE_NAME_UPPER' ), 'replace' => JString::strtoupper ( str_replace ( ' ', '_', $fieldset->code_name ) ) ) );
            array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_CODE_NAME' ), 'replace' => str_replace ( ' ', '', $fieldset->code_name ) ) );
            array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_ALIAS_NAME' ), 'replace' => str_replace ( '_', '-', str_replace ( ' ', '', $fieldset->code_name ) ) ) );
            array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_NAME' ), 'replace' => $fieldset->name ) );
            array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_DESCRIPTION' ), 'replace' => $fieldset->description ) );
            array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_DESCRIPTION_INI' ), 'replace' => str_replace ( '"', '"_QQ_"', $fieldset->description ) ) );

            if ( isset ( $fieldset->intro ) AND $fieldset->intro != '' )
            {
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_INTRO' ), 'replace' => $fieldset->intro ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_INTRO_INI' ), 'replace' => str_replace ( '"', '"_QQ_"', $fieldset->intro ) ) );
            }
            else
            {
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_INTRO' ), 'replace' => $fieldset->description ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELDSET_INTRO_INI' ), 'replace' => str_replace ( '"', '"_QQ_"', $fieldset->description ) ) );
            }

            $fieldset->search_replace = $search_replace_pairs;


            if ( $this->_getFields ( $component_object, $fieldset ) === false )
            {
                return false;
            }

            if ( count ( $fieldset->fields ) > 0 )
            {
                $fieldsets_array[] = $fieldset;
            }
        }

        $component_object->fieldsets = $fieldsets_array;

        return $component_object;
    }

    /**
     * Method to get all the published fields for a component object or a fieldset.
     *
     * @param	component_object		object	Component object passed by ref
     * @param	fieldset				object	Fieldset passed by ref
     *
     * @return	fields					array	An array of field objects		
     */
    protected function _getFields ( &$component_object, &$fieldset = null )
    {
        $db = JFactory::getDbo ();

        $fields_model = JModelLegacy::getInstance ( 'fields', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
        $fields_model->setState ( 'filter.component_object_id', $component_object->id );
        if ( isset ( $fieldset ) AND $fieldset->id > 0 )
        {
            $fields_model->setState ( 'filter.fieldset_id', $fieldset->id );
        }
        // ??? Should order by fieldset ordering as well ??? //		
        $fields_model->setState ( 'list.ordering', 'a.ordering' );
        $fields_model->setState ( 'list.direction', 'ASC' );
        $fields_model->setState ( 'list.select', 'a.*' );



        $fields = $fields_model->getItems ();

        if ( $fields === false )
        {
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0010_CANNOT_LOAD_FIELDS', $component_object->name, $fields_model->getError () ), 'errorcode' => 'gen0010' );
            $this->_progress->outputError ( $this->_token, $error );

            return false;
        }
        $object_fields = array ();
        $filter_fields = array ();
        $order_fields = array ();
        $link_fields = array ();
        $search_fields = array ();
        $registry_fields = array ();
        $registry_entries = array ();
        $fieldset_fields = array ();
        $validate_fields = array ();


        foreach ( $fields as $field )
        {
            //If template is set to process predefined fields or this is a predefined field then process otherwise ignore
            if ( $this->_code_template->generate_predefined_fields == 1 OR $field->predefined_field == 0 )
            {
                // Double check that no punctuation is present in name as this may cause php code or sql problems
                $field->name = preg_replace ( $this->_name_regex, '', $field->name );

                if ( $field->code_name == '' )
                {
                    $field->code_name = str_replace ( ' ', '_', JString::strtolower ( JString::trim ( $field->name ) ) );
                }

                $search_replace_pairs = array ();
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CODE_NAME_UPPER' ), 'replace' => JString::strtoupper ( JString::trim ( $field->code_name ) ) ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CODE_NAME' ), 'replace' => JString::trim ( $field->code_name ) ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_ALIAS_NAME' ), 'replace' => str_replace ( '_', '-', JString::trim ( $field->code_name ) ) ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_NAME' ), 'replace' => JString::trim ( $field->name ) ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CODE_NAME_UCFIRST' ), 'replace' => JString::ucfirst ( str_replace ( '-', '', JString::trim ( JApplication::stringURLSafe ( $field->code_name ) ) ) ) ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_DESCRIPTION' ), 'replace' => $field->description ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_DESCRIPTION_INI' ), 'replace' => str_replace ( '"', '"_QQ_"', $field->description ) ) );

                if ( isset ( $field->intro ) AND $field->intro != '' )
                {
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_INTRO' ), 'replace' => $field->intro ) );
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_INTRO_INI' ), 'replace' => str_replace ( '"', '"_QQ_"', $field->intro ) ) );
                }
                else
                {
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_INTRO' ), 'replace' => str_replace ( '"', '"_QQ_"', $field->description ) ) );
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_INTRO' ), 'replace' => $field->description ) );
                }

                // Initialise field variables
                $type = '';
                $parameters = '';
                $values_array = array ();
                $options = '';
                $language_vars = '';
                $custom_error_message = '';
                $field_filter_default = "''";
                $db_field_type = '';
                $db_field_size = '';
                $db_field_default = '';
                $field_site_value = '';
                $field_site_linked_value = '';
                $registry_entry_site_value = '';
                $registry_entry_site_linked_value = '';

                $field_admin_linked_value = '';

                $index = array_search ( $field->fieldtype_id, $this->_fieldtypes_index );
                $type = JString::strtolower ( JString::trim ( $this->_fieldtypes[ $index ]->code_name ) );

                $foreign_object = null;

                if ( $field->mysql_datatype != '' )
                {
                    $db_field_type = $field->mysql_datatype;
                }
                else
                {
                    if ( $this->_fieldtypes[ $index ]->mysql_datatype_default != '' )
                    {
                        $db_field_type = $this->_fieldtypes[ $index ]->mysql_datatype_default;
                    }
                    else
                    {
                        $db_field_type = 'VARCHAR';
                    }
                }

                if ( $field->mysql_size != '' OR in_array ( $field->mysql_datatype, array ( 'FLOAT', 'DOUBLE', 'REAL', 'DECIMAL' ) ) )
                {
                    $db_field_size = $field->mysql_size;
                }
                else
                {
                    $db_field_size = $this->_fieldtypes[ $index ]->mysql_size_default;
                }

                if ( $db_field_size != '' )
                {
                    $db_field_type .= '(' . $db_field_size . ')';
                }

                if ( $field->mysql_default != '' )
                {
                    switch ( $field->mysql_default )
                    {
                        case "'NULL'" :
                            $db_field_default = 'DEFAULT ' . JString::str_ireplace ( "'", '', $field->mysql_default );
                            break;
                        case "'CURRENT_TIMESTAMP'":
                            $db_field_default = 'NOT NULL DEFAULT ' . JString::str_ireplace ( "'", '', $field->mysql_default );
                            break;
                        default:
                            $db_field_default = 'NOT NULL DEFAULT ' . $field->mysql_default;
                            break;
                    }
                }
                else
                {
                    if ( $this->_fieldtypes[ $index ]->mysql_default_default != '' )
                    {
                        switch ( $this->_fieldtypes[ $index ]->mysql_default_default )
                        {
                            case "'NULL'" :
                                $db_field_default = 'DEFAULT ' . JString::str_ireplace ( "'", '', $this->_fieldtypes[ $index ]->mysql_default_default );
                                break;
                            case "'CURRENT_TIMESTAMP'":
                                $db_field_default = 'NOT NULL DEFAULT ' . JString::str_ireplace ( "'", '', $this->_fieldtypes[ $index ]->mysql_default_default );
                                break;
                            default:
                                $db_field_default = 'NOT NULL DEFAULT ' . $this->_fieldtypes[ $index ]->mysql_default_default;
                                break;
                        }
                    }
                    else
                    {
                        $db_field_default = '';
                    }
                }

                // ??? Temporary until the php variable type has been populated for fields and field types
                if ( !isset ( $field->php_variable_type ) OR $field->php_variable_type == '' )
                {
                    if ( isset ( $this->_fieldtypes[ $index ]->php_variable_type ) AND $this->_fieldtypes[ $index ]->php_variable_type != '' )
                    {
                        $field->php_variable_type = $this->_fieldtypes[ $index ]->php_variable_type;
                    }
                    else
                    {
                        switch ( $type )
                        {
                            case 'accesslevel':
                            case 'calendar':
                            case 'category':
                            case 'integer':
                            case 'modal':
                            case 'user':
                            case 'usegroup':
                                $field->php_variable_type = 'int';
                                break;
                            default:
                                $field->php_variable_type = 'string';
                                break;
                        }
                    }
                }
                $field_site_value = 'echo $this->item->' . JString::trim ( $field->code_name ) . ' != \'\' ? $this->item->' . JString::trim ( $field->code_name ) . ' : $empty;';

                $registry_entry_site_value = 'echo $field_array[\'' . JString::trim ( $field->code_name ) . '\'] != \'\' ? $field_array[\'' . JString::trim ( $field->code_name ) . '\'] : $empty;';

                // Expand out validation and log any errors.  Process should stop before generating.

                if ( isset ( $field->foreign_object_id ) AND $field->foreign_object_id != 0 )
                {
                    /* get the component object for the foreign object key */
                    $foreignobjectmodel = JModelLegacy::getInstance ( 'componentobject', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
                    $foreign_object = $foreignobjectmodel->getItem ( ( int ) $field->foreign_object_id );
                }

                if ( $field->required == '1' )
                {
                    $parameters .= "\t\t\t" . 'required="true"' . "\n";
                }

                if ( $field->hidden == '1' )
                {
                    $parameters .= "\t\t\t" . 'hidden="true"' . "\n";
                }

                if ( $field->readonly )
                {
                    $parameters .= "\t\t\t" . 'readonly="true"' . "\n";
                }

                if ( $field->disabled )
                {
                    $parameters .= "\t\t\t" . 'disabled="true"' . "\n";
                }

                if ( $field->multiple AND $this->_fieldtypes[ $index ]->multiple )
                {
                    $parameters .= "\t\t\t" . 'multiple="true"' . "\n";
                }

                if ( $field->validate )
                {
                    if ( isset ( $field->validation_type ) AND $field->validation_type != '' )
                    {
                        switch ( $field->validation_type )
                        {
                            case 'custom':
                                $field->field_validate_name = JString::trim ( str_replace ( '_', '', $component_object->code_name ) ) . JString::trim ( str_replace ( '_', '', $field->code_name ) );
                                $field->class .= ' validate-' . $field->field_validate_name;
                                $parameters .= "\t\t\t" . 'validate="' . $field->field_validate_name . '"' . "\n";
                                break;
                            case 'numeric':
                                $field->class .= ' validate-' . $field->validation_type;
                                break;
                            default:
                                $field->class .= ' validate-' . $field->validation_type;
                                $parameters .= "\t\t\t" . 'validate="' . $field->validation_type . '"' . "\n";
                                break;
                        }
                    }
                    else
                    {
                        if ( isset ( $this->_fieldtypes[ $index ]->validation_type_default ) AND
                                $this->_fieldtypes[ $index ]->validation_type_default != '' )
                        {
                            $field->class .= ' validate-' . $this->_fieldtypes[ $index ]->validation_type_default;
                            $parameters .= "\t\t\t" . 'validate="' . $this->_fieldtypes[ $index ]->validation_type_default . '"' . "\n";
                        }
                        else
                        {
                            $warning = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_WARNING_GEN1001_NO_VALIDATION_TYPE', $field->name, $component_object->name ), 'errorcode' => 'gen1001' );
                            $this->_progress->outputWarning ( $this->_token, $warning );
                        }
                    }
                    // Add a custom error message to the parameters if one specified
                    if ( isset ( $field->custom_error_message ) AND $field->custom_error_message != '' )
                    {
                        $parameters .= "\t\t\t" . 'message="' . strtoupper ( 'COM_' . JString::trim ( str_replace ( '_', '', $this->_component->code_name ) ) . '_' . JString::trim ( str_replace ( '_', '', $component_object->plural_code_name ) ) . '_' . $field->code_name . '_ERROR_INVALID' ) . '"' . "\n";
                        $custom_error_message = strtoupper ( 'COM_' . JString::trim ( str_replace ( '_', '', $this->_component->code_name ) ) . '_' . JString::trim ( str_replace ( '_', '', $component_object->plural_code_name ) ) . '_' . $field->code_name . '_ERROR_INVALID' ) . '="' . $field->custom_error_message . '"';
                    }
                }

                $optional_attributes = array ( 'class',
                    'size',
                    'maxlength',
                    'width',
                    'height',
                    'cols',
                    'rows',
                    'format',
                    'first',
                    'last',
                    'step',
                    'hide_none',
                    'hide_default',
                    'buttons',
                    'hide_buttons',
                    'field_filter',
                    'exclude_files',
                    'accept_file_types',
                    'directory',
                    'link',
                    'translate',
                    'client',
                    'stripext',
                    'preview',
                    'autocomplete',
                    'onclick',
                    'onchange' );
                $optional_parameters = '';
                foreach ( $optional_attributes as $attribute )
                {
                    // Temporary overrider as the Joomla Calendar field seems to only work with the full format date as below.
                    if ( $type == 'calendar' AND $attribute == 'format' )
                    {
                        $optional_parameters .= "\t\t\tformat=" . '"%Y-%m-%d %H:%M:%S"' . "\n";
                    }
                    else
                    {
                        if ( $this->_fieldtypes[ $index ]->$attribute )
                        {
                            if ( !isset ( $field->$attribute ) OR empty ( $field->$attribute ) )
                            {
                                $attribute_default = $attribute . '_default';
                                if ( isset ( $this->_fieldtypes[ $index ]->$attribute_default ) AND
                                        $this->_fieldtypes[ $index ]->$attribute_default != '' )
                                {
                                    $field->$attribute = $this->_fieldtypes[ $index ]->$attribute_default;
                                }
                            }

                            if ( isset ( $field->$attribute ) AND ! empty ( $field->$attribute ) )
                            {
                                switch ( $attribute )
                                {
                                    case 'accept_file_types' :
                                        $optional_parameters .= "\t\t\t" . 'accept="' . JString::trim ( $field->$attribute ) . '"' . "\n";
                                        break;
                                    case 'exclude_files' :
                                        $optional_parameters .= "\t\t\t" . 'exclude="' . JString::trim ( $field->$attribute ) . '"' . "\n";
                                        break;
                                    case 'field_filter' :
                                        if ( JString::trim ( $field->$attribute ) == 'safe_editor' )
                                        {
                                            $optional_parameters .= "\t\t\t" . 'filter="JComponentHelper::filterText"' . "\n";
                                        }
                                        else
                                        {
                                            $optional_parameters .= "\t\t\tfilter=" . '"' . JString::trim ( $field->$attribute ) . '"' . "\n";
                                        }
                                        break;
                                    case 'hide_button' :
                                        $optional_parameters .= "\t\t\t" . 'hide="' . JString::trim ( $field->$attribute ) . '"' . "\n";
                                        break;
                                    default :
                                        $optional_parameters .= "\t\t\t" . $attribute . '="' . JString::trim ( $field->$attribute ) . '"' . "\n";
                                        break;
                                }
                            }
                        }
                    }
                }
                $parameters = $optional_parameters . $parameters;

                if ( $field->default != '' )
                {
                    if ( $field->default == "''" OR $field->default == '""' )
                    {
                        $parameters .= "\t\t\t" . 'default=""' . "\n";
                    }
                    else
                    {
                        $parameters .= "\t\t\t" . 'default="' . $field->default . '"' . "\n";
                    }
                }
                else
                {
                    if ( isset ( $this->_fieldtypes[ $index ]->default_default ) AND
                            $this->_fieldtypes[ $index ]->default_default != '' )
                    {
                        if ( $this->_fieldtypes[ $index ]->default_default == "''" OR $this->_fieldtypes[ $index ]->default_default == '""' )
                        {
                            $parameters .= "\t\t\t" . 'default=""' . "\n";
                        }
                        else
                        {
                            $parameters .= "\t\t\t" . 'default="' . $this->_fieldtypes[ $index ]->default_default . '"' . "\n";
                        }
                    }
                    else
                    {
                        switch ( $type )
                        {
                            case 'editor':
                                $parameters .= "\t\t\t" . 'default=""' . "\n";
                                break;
                            case 'integer':
                                $parameters .= "\t\t\t" . 'default="0"' . "\n";
                            default:
                                break;
                        }
                    }
                }


                switch ( $type )
                {
                    case 'accesslevel':
                        $field_filter_default = '0';
                        $field_site_value = 'echo $this->item->' . JString::trim ( $field->code_name ) . ' != \'\' ? $this->item->' . JString::trim ( $field->code_name ) . '_title : $empty;';
                        $registry_entry_site_value = 'echo $field_array[\'' . JString::trim ( $field->code_name ) . '\'] != \'\' ? $field_array[\'' . JString::trim ( $field->code_name ) . '_title\'] : $empty;';

                        break;
                    case 'calendar':
                        $field_filter_default = '0';
                        $field_site_value = 'echo $this->item->' . JString::trim ( $field->code_name ) . ' != \'\' ? JHtml::date($this->item->' . JString::trim ( $field->code_name ) . ', \'' . $field->format . '\') : $empty;';
                        $registry_entry_site_value = 'echo $field_array[\'' . JString::trim ( $field->code_name ) . '\'] != \'\' ? JHtml::date($field_array[\'' . JString::trim ( $field->code_name ) . '\'], \'' . $field->format . '\')  : $empty;';

                        break;
                    case 'category':
                        // From 2.5 onwards the type on the xml file is 'categoryedit'
                        $type .= 'edit';

                        $field_site_value = 'echo $this->item->' . JString::trim ( $field->code_name ) . ' != \'\' ? $this->item->' . JString::trim ( $field->code_name ) . '_title : $empty;';
                        $registry_entry_site_value = 'echo $field_array[\'' . JString::trim ( $field->code_name ) . '\'] != \'\' ? $field_array[\'' . JString::trim ( $field->code_name ) . '_title\'] : $empty;';

                        $parameters .= "\t\t\t" . 'extension="' . $this->_markupText ( 'com_architectcomp' ) . '"' . "\n";
                        $parameters .= "\t\t\t" . 'addfieldpath="/administrator/components/com_categories/models/fields"' . "\n";
                        $options = "\t\t\t" . '<option value="">[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_' . strtoupper ( $field->code_name ) . '</option>' . "\n";

                        break;
                    case 'checkbox':

                        $variable = '$this->item->' . JString::trim ( $field->code_name );
                        $ident = "\t\t\t\t\t\t\t\t";
                        $field_site_value = $this->_generateValueDisplayCode ( $field->option_values, $variable, $ident, $field, $type );

                        $variable = '$field_array[\'' . JString::trim ( $field->code_name ) . '\']';
                        $ident = "\t\t\t\t\t\t\t";
                        $registry_entry_site_value = $this->_generateValueDisplayCode ( $field->option_values, $variable, $ident, $field, $type );

                        $parameters .= "\t\t\t" . 'value="1"' . "\n";
                        break;
                    case 'checkboxes':
                        $values_array = $this->_generateValuesArray ( $component_object, $field );
                        $options = $this->_generateValueOptions ( $values_array, $type );
                        $language_vars = $this->_generateValueLanguageVars ( $values_array );

                        $variable = '$this->item->' . JString::trim ( $field->code_name );
                        $ident = "\t\t\t\t\t\t\t\t";
                        $field_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );

                        $variable = '$field_array[\'' . JString::trim ( $field->code_name ) . '\']';
                        $ident = "\t\t\t\t\t\t\t";

                        $registry_entry_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );
                        break;
                    case 'color':
                        break;
                    case 'combo':
                        $values_array = $this->_generateValuesArray ( $component_object, $field );
                        $options = $this->_generateValueOptions ( $values_array, $type );
                        $language_vars = $this->_generateValueLanguageVars ( $values_array );

                        break;
                    case 'contentlanguage':
                        break;
                    case 'editor':
                        break;
                    case 'email':
                        break;
                    case 'file':
                        $field_site_value = 'echo !empty($this->item->' . JString::trim ( $field->code_name ) . ') ? \'<a title="\'.JText::_("[%%COM_ARCHITECTCOMP%%]_VIEW_FILE").\'" href="\'.JRoute::_(JUri::root().trim($this->item->' . JString::trim ( $field->code_name ) . '), false).\'" target="_blank">\'.JText::_("[%%COM_ARCHITECTCOMP%%]_VIEW_FILE").\'</a>\' : $empty;';
                        $registry_entry_site_value = 'echo !empty($field_array[\'' . JString::trim ( $field->code_name ) . '\']) ? \'<a title="\'.JText::_("[%%COM_ARCHITECTCOMP%%]_VIEW_FILE").\'" href="\'.JRoute::_(JUri::root().trim($field_array[\'' . JString::trim ( $field->code_name ) . '\']), false).\'" target="_blank">\'.JText::_("[%%COM_ARCHITECTCOMP%%]_VIEW_FILE").\'</a>\' : $empty;';

                        if ( $field->accept_file_types )
                        {
                            $parameters .= "\t\t\t" . 'accept="' . $field->accept_file_types . '"' . "\n";
                        }
                        else
                        {
                            if ( isset ( $this->_fieldtypes[ $index ]->accept_file_types_default ) AND $this->_fieldtypes[ $index ]->accept_file_types_default != '' )
                            {
                                $parameters .= "\t\t\t" . 'directory="' . $this->_fieldtypes[ $index ]->accept_file_types_default . '"' . "\n";
                            }
                        }
                        break;
                    case 'filelist':
                    case 'folderlist':
                    case 'imagelist':
                        $variable = '$this->item->' . JString::trim ( $field->code_name );
                        $ident = "\t\t\t\t\t\t\t\t";

                        $field_site_value = $this->_generateValueDisplayCode ( array (), $variable, $ident, $field, $type );

                        $variable = '$field_array[\'' . JString::trim ( $field->code_name ) . '\']';
                        $ident = "\t\t\t\t\t\t\t";

                        $registry_entry_site_value = $this->_generateValueDisplayCode ( array (), $variable, $ident, $field, $type );
                        $options = "\t\t\t" . '<option value="">[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_' . strtoupper ( $field->code_name ) . '</option>' . "\n";

                        break;
                    case 'hidden':
                        if ( $field->hidden != '1' )
                        {
                            $parameters .= "\t\t\t" . 'hidden="true"' . "\n";
                        }
                        break;
                    case 'integer':
                        $field_filter_default = '0';
                        break;
                    case 'language':
                        break;
                    case 'list':
                    case 'groupedlist':
                        $values_array = $this->_generateValuesArray ( $component_object, $field );
                        $options = $this->_generateValueOptions ( $values_array, $type );
                        $language_vars = $this->_generateValueLanguageVars ( $values_array );

                        $variable = '$this->item->' . JString::trim ( $field->code_name );
                        $ident = "\t\t\t\t\t\t\t\t";

                        $field_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );

                        $variable = '$field_array[\'' . JString::trim ( $field->code_name ) . '\']';
                        $ident = "\t\t\t\t\t\t\t";

                        $registry_entry_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );

                        break;
                    case 'media':
                        $width = (isset ( $field->width ) AND $field->width != '') ? $field->width : '100';
                        $height = (isset ( $field->height ) AND $field->height != '') ? $field->height : '50';
                        $field_site_value = 'echo $this->item->' . JString::trim ( $field->code_name ) . ' != \'\' ? \'<embed height="' . $height . '" width="' . $width . '" src="\'.$this->item->' . JString::trim ( $field->code_name ) . '.\'"/>\' : $empty;';
                        $registry_entry_site_value = 'echo $field_array[\'' . JString::trim ( $field->code_name ) . '\'] != \'\' ? \'<embed height="' . $height . '" width="' . $width . '" src="\'.$field_array[\'' . JString::trim ( $field->code_name ) . '\'].\'"/>\' : $empty;';

                        break;
                    case 'modal':
                        $field_filter_default = '0';

                        $field_site_value = 'echo JString::trim($this->item->' . $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ACRONYM' ) . '_' .
                                $this->_markupText ( 'FIELD_FOREIGN_OBJECT_CODE_NAME' ) . '_' .
                                $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ) . ');';

                        $registry_entry_site_value = 'echo JString::trim($field_array[\'' . $field->code_name . '_name\']);';

                        if ( $field->foreign_object_id > 0 )
                        {
                            $field_admin_linked_value = 'echo \'<a href="index.php?option=' . $this->_markupText ( 'com_architectcomp' ) . '&task=' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT' ) . '.edit&id=\'.$this->item->' . $field->code_name . '.\'">\'' .
                                    '.JString::trim($this->item->' . $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ACRONYM' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_CODE_NAME' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ) . ').' .
                                    '\'</a>\';';


                            $field_site_linked_value = 'echo \'<a href="\'.JRoute::_(' . $this->_markupText ( 'ArchitectComp' ) . 'HelperRoute::get' . $this->_markupText ( 'FIELD_FOREIGN_OBJECT_UCWORDS' ) . 'Route($this->item->' . $field->code_name . ', 0';
                            if ( $component_object->conditions[ 'include_language' ] == 1 )
                            {
                                $field_site_linked_value .= ', $this->item->language';
                            }
                            $field_site_linked_value .= ')).\'">\'.JString::trim($this->item->' . $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ACRONYM' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_CODE_NAME' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ) . ').' . '\'</a>\';';


                            $registry_entry_site_linked_value = 'echo \'<a href="\'.JRoute::_(' . $this->_markupText ( 'ArchitectComp' ) . 'HelperRoute::get' . $this->_markupText ( 'FIELD_FOREIGN_OBJECT_UCWORDS' ) . 'Route($field_array[\'' . $field->code_name . '\'], 0';
                            if ( $component_object->conditions[ 'include_language' ] == 1 )
                            {
                                $registry_entry_site_linked_value .= ', $this->item->language';
                            }
                            $registry_entry_site_linked_value .= ')).\'">\'.JString::trim($field_array[\'' . $field->code_name . '_name\']).' . '\'</a>\';';
                        }
                        else
                        {
                            // Shouldn't need to be used if correctly checked for IF FIELD_LINK in template but just in case set values to simple string
                            $field_admin_linked_value = 'echo JString::trim($this->item->' . $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ACRONYM' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_CODE_NAME' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ) . ');';

                            $field_site_linked_value = 'echo JString::trim($this->item->' . $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ACRONYM' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_CODE_NAME' ) . '_' .
                                    $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ) . ');';

                            $registry_entry_site_linked_value = 'echo JString::trim($field_array[\'' . $field->code_name . '_name\']);';
                        }
                        // Change the type so that modal directs to the correct modal view for the object
                        $type .= '_' . JString::strtolower ( str_replace ( '_', '', $foreign_object->plural_code_name ) );

                        break;
                    case 'password':
                        break;
                    case 'radio':
                        $values_array = $this->_generateValuesArray ( $component_object, $field );

                        $options = $this->_generateValueOptions ( $values_array, $type );
                        $language_vars = $this->_generateValueLanguageVars ( $values_array );

                        $variable = '$this->item->' . JString::trim ( $field->code_name );
                        $ident = "\t\t\t\t\t\t\t\t";
                        $field_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );

                        $variable = '$field_array[\'' . JString::trim ( $field->code_name ) . '\']';
                        $ident = "\t\t\t\t\t\t\t";

                        $registry_entry_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );

                        break;
                    case 'registry':
                        break;
                    case 'tag':
                        // Change tag field type to sql as to properly process tag type requires a new FormField class.
                        $type = 'sql';

                        $field_filter_default = '0';
                        $field->sql_query = 'SELECT `id`, `title` FROM `#__tags` WHERE `id` > 1';
                        $field->sql_key_field = 'id';
                        $field->sql_value_field = 'title';
                    case 'sql':
                        $sql_parameters = '';

                        if ( $field->sql_query )
                        {
                            $sql_parameters .= "\t\t\t" . 'query="' . htmlspecialchars ( $field->sql_query, ENT_COMPAT, 'UTF-8' ) . '"' . "\n";
                        }
                        if ( $field->sql_key_field )
                        {
                            $sql_parameters .= "\t\t\t" . 'key_field="' . $field->sql_key_field . '"' . "\n";
                        }
                        if ( $field->sql_value_field )
                        {
                            $sql_parameters .= "\t\t\t" . 'value_field="' . $field->sql_value_field . '"' . "\n";
                        }
                        $parameters .= $sql_parameters;

                        $options = "\t\t\t" . '<option value="">[%%COM_ARCHITECTCOMP%%]_[%%COMPOBJECTPLURAL%%]_SELECT_' . strtoupper ( $field->code_name ) . '</option>' . "\n";

                        $values_array = array ();

                        $variable = '$this->item->' . JString::trim ( $field->code_name );
                        $ident = "\t\t\t\t\t\t\t\t";
                        $field_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );

                        $variable = '$field_array[\'' . JString::trim ( $field->code_name ) . '\']';
                        $ident = "\t\t\t\t\t\t\t";

                        $registry_entry_site_value = $this->_generateValueDisplayCode ( $values_array, $variable, $ident, $field, $type );

                        break;
                    case 'tel':
                    case 'text':
                    case 'textarea':
                    case 'timezone':
                    case 'url':
                        break;
                    case 'user':
                        $field_filter_default = '0';
                        $field_site_value = 'echo $this->item->' . JString::trim ( $field->code_name ) . ' != \'\' ? $this->item->' . JString::trim ( $field->code_name ) . '_name : $empty;';
                        $registry_entry_site_value = 'echo $field_array[\'' . JString::trim ( $field->code_name ) . '\'] != \'\' ? $field_array[\'' . JString::trim ( $field->code_name ) . '_name\'] : $empty;';

                        break;
                    case 'usergroup':
                        $field_filter_default = '0';
                        $field_site_value = 'echo $this->item->' . JString::trim ( $field->code_name ) . ' != \'\' ? $this->item->' . JString::trim ( $field->code_name ) . '_title : $empty;';
                        $registry_entry_site_value = 'echo $field_array[\'' . JString::trim ( $field->code_name ) . '\'] != \'\' ? $field_array[\'' . JString::trim ( $field->code_name ) . '_title\'] : $empty;';

                        break;
                    default:
                        break;
                }
                $field_admin_list_value = str_replace ( '$this->item->', '$item->', $field_site_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_ADMIN_LIST_VALUE' ), 'replace' => $field_admin_list_value ) );
                $field_child_admin_list_value = str_replace ( '$this->item->', '$child->', $field_site_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_ADMIN_LIST_VALUE' ), 'replace' => $field_child_admin_list_value ) );

                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_ADMIN_LINKED_VALUE' ), 'replace' => $field_admin_linked_value ) );

                $field_admin_list_linked_value = str_replace ( '$this->item->', '$item->', $field_admin_linked_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_ADMIN_LIST_LINKED_VALUE' ), 'replace' => $field_admin_list_linked_value ) );
                $field_child_admin_list_linked_value = str_replace ( '$this->item->', '$child->', $field_admin_linked_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_ADMIN_LIST_LINKED_VALUE' ), 'replace' => $field_child_admin_list_linked_value ) );


                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SITE_VALUE' ), 'replace' => $field_site_value ) );
                $field_child_site_value = str_replace ( '$this->item->', '$child->', $field_site_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_SITE_VALUE' ), 'replace' => $field_child_site_value ) );
                $field_child_site_linked_value = str_replace ( '$this->item->', '$child->', $field_site_linked_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_SITE_LINKED_VALUE' ), 'replace' => $field_child_site_linked_value ) );

                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SITE_LINKED_VALUE' ), 'replace' => $field_site_linked_value ) );

                $field_site_list_value = str_replace ( '$this->item->', '$item->', $field_site_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SITE_LIST_VALUE' ), 'replace' => $field_site_list_value ) );
                $field_child_site_list_value = str_replace ( '$this->item->', '$child->', $field_site_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_SITE_LIST_VALUE' ), 'replace' => $field_child_site_list_value ) );

                $field_site_list_linked_value = str_replace ( '$this->item->', '$item->', $field_site_linked_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SITE_LIST_LINKED_VALUE' ), 'replace' => $field_site_list_linked_value ) );
                $field_child_site_list_linked_value = str_replace ( '$this->item->', '$child->', $field_site_linked_value );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_SITE_LIST_LINKED_VALUE' ), 'replace' => $field_child_site_list_linked_value ) );


                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'REGISTRY_ENTRY_SITE_VALUE' ), 'replace' => $registry_entry_site_value ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'REGISTRY_ENTRY_SITE_LINKED_VALUE' ), 'replace' => $registry_entry_site_linked_value ) );


                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_TYPE' ), 'replace' => $type ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_PHP_VARIABLE_TYPE' ), 'replace' => isset ( $field->php_variable_type ) ? $field->php_variable_type : 'string' ) );
                // Need to make sure something always in field default and if not numeric it is in quotes
                if ( $field->default != "''" )
                {
                    if ( !is_numeric ( $field->default ) )
                    {
                        $field->default = "'" . $field->default . "'";
                    }
                }
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_DEFAULT' ), 'replace' => isset ( $field->default ) ? $field->default : '' ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_DEFAULT_NOQUOTES' ), 'replace' => isset ( $field->default ) ? str_replace ( "'", "", $field->default ) : null ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_FILTER_DEFAULT' ), 'replace' => isset ( $field_filter_default ) ? $field_filter_default : "''" ) );

                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_START_DIRECTORY' ), 'replace' => isset ( $field->directory ) ? $field->directory : '' ) );

                if ( !empty ( $options ) )
                {
                    $options = substr ( $options, 0, 3 ) == "\t\t\t" ? substr ( $options, 3 ) : $options;
                    $options = substr ( $options, strlen ( $options ) - 1, 1 ) == "\n" ? substr ( $options, 0, strlen ( $options ) - 1 ) : $options;

                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_OPTIONS' ), 'replace' => $options ) );
                }
                else
                {
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_OPTIONS' ), 'replace' => '' ) );
                }
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_OPTIONS_LANGUAGE_VARS' ), 'replace' => isset ( $language_vars ) ? $language_vars : '' ) );

                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SQL_QUERY' ), 'replace' => isset ( $field->sql_query ) ? $db->escape ( $field->sql_query ) : '' ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SQL_KEY_FIELD' ), 'replace' => isset ( $field->sql_key_field ) ? $db->escape ( $field->sql_key_field ) : '' ) );
                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SQL_VALUE_FIELD' ), 'replace' => isset ( $field->sql_value_field ) ? $db->escape ( $field->sql_value_field ) : '' ) );

                if ( !empty ( $sql_parameters ) )
                {
                    $sql_parameters = substr ( $sql_parameters, 0, 3 ) == "\t\t\t" ? substr ( $sql_parameters, 3 ) : $sql_parameters;
                    $sql_parameters = substr ( $sql_parameters, strlen ( $sql_parameters ) - 1, 1 ) == "\n" ? substr ( $sql_parameters, 0, strlen ( $sql_parameters ) - 1 ) : $sql_parameters;

                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SQL_TEST_PARAMETERS' ), 'replace' => $sql_parameters ) );
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SQL_PARAMETERS' ), 'replace' => $sql_parameters ) );
                }
                else
                {
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SQL_TEST_PARAMETERS' ), 'replace' => '' ) );
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_SQL_PARAMETERS' ), 'replace' => '' ) );
                }

                if ( !empty ( $parameters ) )
                {
                    $parameters = substr ( $parameters, 0, 3 ) == "\t\t\t" ? substr ( $parameters, 3 ) : $parameters;
                    $parameters = substr ( $parameters, strlen ( $parameters ) - 1, 1 ) == "\n" ? substr ( $parameters, 0, strlen ( $parameters ) - 1 ) : $parameters;

                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_PARAMETERS' ), 'replace' => $parameters ) );
                }
                else
                {
                    array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_PARAMETERS' ), 'replace' => '' ) );
                }

                switch ( JString::strtoupper ( $db_field_type ) )
                {
                    case 'INTEGER' :
                    case 'INT' :
                    case 'TINYINT' :
                    case 'SMALLINT' :
                    case 'MEDIUMINT' :
                    case 'BIGINT' :
                        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_TYPECAST' ), 'replace' => '(int)' ) );
                        break;
                    case 'VARCHAR' :
                    case 'CHAR' :
                    case 'TEXT' :
                    case 'MEDIUMTEXT' :
                        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_TYPECAST' ), 'replace' => '(string)' ) );
                        break;
                    default:
                        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_TYPECAST' ), 'replace' => '' ) );
                        break;
                }

                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_DBTYPEANDSIZE' ), 'replace' => $db_field_type ) );

                array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'FIELD_DBDEFAULT' ), 'replace' => $db_field_default ) );
                $field->search_replace = $search_replace_pairs;

                if ( $field->validate == 1 )
                {
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_VALIDATION_TYPE' ), 'replace' => ( string ) ' - ' . $field->validation_type ) );

                    if ( isset ( $field->validation_type ) AND $field->validation_type == 'custom' )
                    {
                        array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_VALIDATE_NAME' ), 'replace' => ( string ) $field->field_validate_name ) );
                        if ( $field->allowed_input == '' )
                        {
                            array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_ALLOWED_INPUT' ), 'replace' => '.*' ) );
                        }
                        else
                        {
                            array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_ALLOWED_INPUT' ), 'replace' => $field->allowed_input ) );
                        }
                        $validate_fields[] = $field;
                    }
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_CUSTOM_ERROR_MESSAGE' ), 'replace' => $custom_error_message ) );
                }
                else
                {
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_VALIDATION_TYPE' ), 'replace' => '' ) );
                }

                if ( $field->filter == 1 )
                {
                    $filter_fields[] = $field;

                    if ( $field->order == 1 )
                    {
                        $order_fields[] = $field;
                    }
                    if ( is_null ( $foreign_object ) AND JString::substr ( $type, 0, 5 ) != 'modal' )
                    {
                        array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FILTER_VALUE_ARRAY' ), 'replace' => $this->_generateFilterValueArray ( $values_array, $field, $type ) ) );
                        array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_FILTER_VALUE_ARRAY' ), 'replace' => $this->_generateFilterValueArray ( $values_array, $field, $type, 'child' ) ) );
                    }
                    else
                    {
                        array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FILTER_VALUE_ARRAY' ), 'replace' => '' ) );
                        array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_CHILD_FILTER_VALUE_ARRAY' ), 'replace' => '' ) );
                    }
                }
                if ( !is_null ( $foreign_object ) AND JString::substr ( $type, 0, 5 ) == 'modal' )
                {
                    $acronym = '';
                    if ( array_key_exists ( $field->code_name, $this->_acronyms ) )
                    {
                        $acronym = $this->_acronyms[ $field->code_name ];
                    }
                    else
                    {
                        $word_array = explode ( ' ', $foreign_object->name );


                        foreach ( $word_array as $word )
                        {
                            $acronym .= JString::strtolower ( $word{0} );
                        }

                        if ( array_search ( $acronym, $this->_acronyms ) )
                        {
                            $count = 1;

                            while ( array_search ( $acronym . $count, $this->_acronyms ) )
                            {
                                $count++;
                            }
                            $acronym .= ( string ) $count;
                        }
                        $this->_acronyms[ $field->code_name ] = $acronym;
                    }


                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ACRONYM' ), 'replace' => $acronym ) );
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ACRONYM_UPPER' ), 'replace' => JString::strtoupper ( $acronym ) ) );

                    // Need to add some additional search and replace terms

                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT' ), 'replace' => str_replace ( '_', '', JString::strtolower ( $foreign_object->code_name ) ) ) );
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_CODE_NAME' ), 'replace' => JString::strtolower ( $foreign_object->code_name ) ) );
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_UCWORDS' ), 'replace' => str_replace ( ' ', '', JString::ucwords ( JString::strtolower ( str_replace ( '_', ' ', $foreign_object->code_name ) ) ) ) ) );

                    //Determine which field to use for the Foreign Objects name field
                    if ( $foreign_object->joomla_features[ 'include_name' ] == '1' )
                    {
                        array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ), 'replace' => 'name' ) );
                    }
                    else
                    {
                        if ( $foreign_object->joomla_features[ 'include_name' ] == '0' )
                        {
                            array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ), 'replace' => 'id' ) );
                        }
                        else
                        {
                            if ( $this->_component->joomla_features[ 'include_name' ] == '1' )
                            {
                                array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ), 'replace' => 'name' ) );
                            }
                            else
                            {
                                array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_LABEL_FIELD' ), 'replace' => 'id' ) );
                            }
                        }
                    }

                    //Determine which field to use for the Foreign Objects ordering field
                    if ( $foreign_object->joomla_features[ 'include_ordering' ] == '1' )
                    {
                        array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ORDERING_FIELD' ), 'replace' => 'ordering' ) );
                    }
                    else
                    {
                        if ( $foreign_object->joomla_features[ 'include_ordering' ] == '0' )
                        {
                            array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ORDERING_FIELD' ), 'replace' => 'id' ) );
                        }
                        else
                        {
                            if ( $this->_component->joomla_features[ 'include_ordering' ] == '1' )
                            {
                                array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ORDERING_FIELD' ), 'replace' => 'ordering' ) );
                            }
                            else
                            {
                                array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_ORDERING_FIELD' ), 'replace' => 'id' ) );
                            }
                        }
                    }

                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_PLURAL' ), 'replace' => JString::strtolower ( str_replace ( '_', '', $foreign_object->plural_code_name ) ) ) );
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_PLURAL_UCFIRST' ), 'replace' => JString::ucfirst ( JString::strtolower ( str_replace ( '_', '', $foreign_object->plural_code_name ) ) ) ) );
                    array_push ( $field->search_replace, array ( 'search' => $this->_markupText ( 'FIELD_FOREIGN_OBJECT_UPPER' ), 'replace' => JString::strtoupper ( $foreign_object->code_name ) ) );

                    $link_fields[] = $field;
                }


                if ( $field->search == 1 )
                {
                    $search_fields[] = $field;
                }

                if ( $type == 'registry' )
                {
                    $registry_fields[] = $field;
                }
                else
                {
                    if ( !isset ( $field->registry_field_id ) OR $field->registry_field_id == 0 )
                    {
                        $object_fields[] = $field;
                        if ( isset ( $fieldset ) AND $fieldset->id > 0 )
                        {
                            $fieldset_fields[] = $field;
                        }
                    }
                    else
                    {
                        $registry_entries[] = $field;
                    }
                }
            }
            // Update Stage 1 progress - if logging requested this will also create a log record
            $step = JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_STEP_STAGE_1', $field->name, $component_object->name );
            $this->_progress->setProgress ( $this->_token, 'stage_1', $step );
        }


        // All fields processed now find the ones to add as registry entries
        foreach ( $registry_fields as $registry_field )
        {
            $registry_field->registry_entries = array ();
            foreach ( $registry_entries as $registry_entry )
            {
                if ( isset ( $registry_entry->registry_field_id ) AND $registry_entry->registry_field_id == $registry_field->id )
                {

                    $registry_field->registry_entries[] = $registry_entry;
                }
            }
        }


        $component_object->fields = $object_fields;
        $component_object->validate_fields = $validate_fields;
        $component_object->filter_fields = $filter_fields;
        $component_object->order_fields = $order_fields;
        $component_object->link_fields = $link_fields;
        $component_object->search_fields = $search_fields;
        $component_object->registry_fields = $registry_fields;
        if ( isset ( $fieldset ) AND $fieldset->id > 0 )
        {
            $fieldset->fields = $fieldset_fields;
        }
        return true;
    }

    /**
     * Method to get all the published field type allowed
     *
     * @param	
     *
     * @return	true or false (_fieldtypes variable populated)		
     */
    protected function _getFieldTypes ()
    {
        /* get all the published field types */
        $field_types_model = JModelLegacy::getInstance ( 'fieldtypes', 'ComponentArchitectModel', array ( 'ignore_request' => true ) );
        $field_types_model->setState ( 'list.ordering', 'a.name' );
        $field_types_model->setState ( 'list.direction', 'ASC' );
        $field_types_model->setState ( 'list.select', 'a.*' );

        $this->_fieldtypes = $field_types_model->getItems ();

        if ( $this->_fieldtypes === false )
        {
            $error = array ( 'message' => JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0011_CANNOT_LOAD_FIELD_TYPES', $fieldstypemodel->getError () ), 'errorcode' => 'gen0011' );
            $this->_progress->outputError ( $this->_token, $error );

            return false;
        }
        else
        {
            foreach ( $this->_fieldtypes as $fieldtype )
            {
                $this->_fieldtypes_index[] = $fieldtype->id;
            }
            return true;
        }
    }

    /**
     * Method to set the search/replace pairs for the component
     *
     * @param	template_component_name		string	Used for template source search strings
     * @param	component					object	Used for component replacement strings	
     *
     * @return	search_replace_pairs		array	search/replace pairs for the component		
     */
    protected function _getComponentSearchPairs ( $template_component_name, $component )
    {
        $component_name = $component->name;
        $component_code_name = $component->code_name;
        $component_description = $component->description;
        if ( isset ( $component->intro ) )
        {
            $component_intro = $component->intro;
        }
        else
        {
            $component_intro = $component->description;
        }


        $search_replace_pairs = array ();
        array_push ( $search_replace_pairs, array ( 'search' => '<!-- @version' . "\t\t\t$", 'replace' => '<!-- @CAversion' . "\t\t\t" ) );
        array_push ( $search_replace_pairs, array ( 'search' => '<!-- @version' . "\t\t$", 'replace' => '<!-- @CAversion' . "\t\t" ) );
        array_push ( $search_replace_pairs, array ( 'search' => ' @version' . "\t\t\t$", 'replace' => ' @CAversion' . "\t\t" ) );
        array_push ( $search_replace_pairs, array ( 'search' => '<!-- @tempversion', 'replace' => '<!-- @version' . " \t\t\t\$Id:\$" ) );
        array_push ( $search_replace_pairs, array ( 'search' => ' @tempversion', 'replace' => ' @version' . " \t\t\$Id:\$" ) );



        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'COMPONENTAUTHOR' ), 'replace' => JString::trim ( $component->author ) ) );
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'COMPONENTWEBSITE' ), 'replace' => JString::trim ( $component->web_address ) ) );
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'COMPONENTCOPYRIGHT' ), 'replace' => JString::trim ( $component->copyright ) ) );
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'COMPONENTEMAIL' ), 'replace' => JString::trim ( $component->email ) ) );
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'COMPONENTSTARTVERSION' ), 'replace' => JString::trim ( $component->start_version ) ) );
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'COMPONENTCREATED' ), 'replace' => date ( 'F Y' ) ) );


        // e.g. 'search' => '[%%com_architectcomp%%]', 'replace' => 'com_focusgroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'com_' . str_replace ( " ", "", JString::strtolower ( $template_component_name ) ) ), 'replace' => 'com_' . str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $component_code_name ) ) ) ) );

        // e.g. 'search' => '[%%COM_ARCHITECTCOMP%%]',	 'replace' => 'COM_FOCUSGROUPS'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( 'COM_' . str_replace ( " ", "", JString::strtoupper ( $template_component_name ) ) ), 'replace' => 'COM_' . str_replace ( "_", "", str_replace ( " ", "", JString::strtoupper ( $component_code_name ) ) ) ) );

        // e.g. 'search' => '[%%ArchitectComp_name%%]', 'replace' => 'Focus Groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( $template_component_name ) ) . '_name' ), 'replace' => JString::ucwords ( $component_name ) ) );

        // e.g. 'search' => '[%%Architectcomp_name%%]', 'replace' => 'Focus groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) ) . '_name' ), 'replace' => JString::ucfirst ( JString::strtolower ( $component_name ) ) ) );

        // e.g. 'search' => '[%%architectcomp_name%%]', 'replace' => 'focus groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) . '_name' ), 'replace' => JString::strtolower ( $component_name ) ) );

        // e.g. 'search' => '[%%Architectcomp_description%%]', 'replace' => 'Focus groups description'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) ) . '_description' ), 'replace' => $component_description ) );

        // e.g. 'search' => '[%%Architectcomp_description_ini%%]', 'replace' => 'Focus groups description'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) ) . '_description_ini' ), 'replace' => str_replace ( '"', '"_QQ_"', $component_description ) ) );

        // e.g. 'search' => '[%%Architectcomp_intro%%]', 'replace' => 'Focus groups intro'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) ) . '_intro' ), 'replace' => $component_intro ) );

        // e.g. 'search' => '[%%Architectcomp_intro_ini%%]', 'replace' => 'Focus groups intro'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) ) . '_intro_ini' ), 'replace' => str_replace ( '"', '"_QQ_"', $component_intro ) ) );

        // e.g. 'search' => '[%%architectcomp%%]', 'replace' => 'focusgroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_component_name ) ) ), 'replace' => str_replace ( "_", "", str_replace ( " ", "", JString::strtolower ( $component_code_name ) ) ) ) );

        // e.g. 'search' => '[%%ARCHITECTCOMP%%]',	 'replace' => 'FOCUSGROUPS'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtoupper ( $template_component_name ) ) ), 'replace' => str_replace ( "_", "", str_replace ( " ", "", JString::strtoupper ( $component_code_name ) ) ) ) );

        // e.g. 'search' => '[%%Architectcomp%%]', 'replace' => 'Focusgroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_component_name ) ) ) ), 'replace' => JString::ucfirst ( JString::strtolower ( str_replace ( "_", "", str_replace ( " ", "", $component_code_name ) ) ) ) ) );

        // e.g. 'search' => '[%%ArchitectComp%%]', 'replace' => 'FocusGroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( $template_component_name ) ) ), 'replace' => str_replace ( "_", "", str_replace ( " ", "", JString::ucwords ( str_replace ( "_", " ", $component_code_name ) ) ) ) ) );

        // e.g. 'search' => '[%%architectComp%%]', 'replace' => 'focusGroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( lcfirst ( str_replace ( " ", "", JString::ucwords ( $template_component_name ) ) ) ), 'replace' => lcfirst ( str_replace ( "_", "", str_replace ( " ", "", JString::ucwords ( str_replace ( "_", " ", $component_code_name ) ) ) ) ) ) );

        return $search_replace_pairs;
    }

    /**
     * Method to set the search/replace pairs for a component object
     *
     * @param	template_object_name		string	Used for template source search strings
     * @param	component_object			object	Used for component object replacement strings	
     * @param	make_child_pairs			boolean	When object is achild object then add Child search pairs	
     *
     * @return	search_replace_pairs		array	search/replace pairs for the component		
     */
    protected function _getComponentObjectSearchPairs ( $template_object_name, $component_object, $make_child_pairs = false )
    {
        $db = JFactory::getDbo ();

        $object_name = $component_object->name;
        $object_description = $component_object->description;
        if ( isset ( $component_object->intro ) )
        {
            $object_intro = $component_object->intro;
        }
        else
        {
            $object_intro = $component_object->description;
        }
        $object_plural_name = $component_object->plural_name;
        $object_short_name = $component_object->short_name;
        $object_short_plural_name = $component_object->short_plural_name;
        $object_code_name = str_replace ( " ", "", $component_object->code_name );
        $object_plural_code_name = str_replace ( " ", "", $component_object->plural_code_name );
        $object_ordering = $component_object->ordering;
        if ( $make_child_pairs )
        {
            $template_object_name = 'child ' . $template_object_name;
        }


        $search_replace_pairs = array ();

        // e.g. 'search' => '[%%CompObject_name%%]', 'replace' => 'Discussion Group'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( $template_object_name ) ) . '_name' ), 'replace' => JString::ucwords ( $object_name ) ) );

        // e.g. 'search' => '[%%CompObject_plural_name%%]', 'replace' => 'Discussion Groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( $template_object_name ) ) . '_plural_name' ), 'replace' => JString::ucwords ( $object_plural_name ) ) );

        // e.g. 'search' => '[%%Compobject_name%%]', 'replace' => 'Discussion group'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucfirst ( JString::strtolower ( $template_object_name ) ) ) . '_name' ), 'replace' => JString::ucfirst ( JString::strtolower ( $object_name ) ) ) );

        // e.g. 'search' => '[%%Compobject_plural_name%%]', 'replace' => 'Discussion groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucfirst ( JString::strtolower ( $template_object_name ) ) ) . '_plural_name' ), 'replace' => JString::ucfirst ( JString::strtolower ( $object_plural_name ) ) ) );

        // e.g. 'search' => '[%%compobject_name%%]', 'replace' => 'discussion group'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) . '_name' ), 'replace' => JString::strtolower ( $object_name ) ) );

        // e.g. 'search' => '[%%compobject_plural_name%%]', 'replace' => 'discussion groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) . '_plural_name' ), 'replace' => JString::strtolower ( $object_plural_name ) ) );


        // e.g. 'search' => '[%%CompObject_short_name%%]', 'replace' => 'Group'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( $template_object_name ) ) . '_short_name' ), 'replace' => JString::ucwords ( $object_short_name ) ) );

        // e.g. 'search' => '[%%CompObject_short_plural_name%%]', 'replace' => 'Groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( $template_object_name ) ) . '_short_plural_name' ), 'replace' => JString::ucwords ( $object_short_plural_name ) ) );


        // e.g. 'search' => '[%%Compobject_short_name%%]', 'replace' => 'Group'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucfirst ( JString::strtolower ( $template_object_name ) ) ) . '_short_name' ), 'replace' => JString::ucfirst ( JString::strtolower ( $object_short_name ) ) ) );

        // e.g. 'search' => '[%%Compobject_short_plural_name%%]', 'replace' => 'Groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucfirst ( JString::strtolower ( $template_object_name ) ) ) . '_short_plural_name' ), 'replace' => JString::ucfirst ( JString::strtolower ( $object_short_plural_name ) ) ) );

        // e.g. 'search' => '[%%compobject_short_name%%]', 'replace' => 'group'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) . '_short_name' ), 'replace' => JString::strtolower ( $object_short_name ) ) );

        // e.g. 'search' => '[%%compobject_short_plural_name%%]', 'replace' => 'groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) . '_short_plural_name' ), 'replace' => JString::strtolower ( $object_short_plural_name ) ) );

        // e.g. 'search' => '[%%compobject_code_name%%]', 'replace' => 'discussion_group'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) . '_code_name' ), 'replace' => JString::strtolower ( $object_code_name ) ) );

        // e.g. 'search' => '[%%compobject_plural_code_name%%]', 'replace' => 'discussion_groups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) . '_plural_code_name' ), 'replace' => JString::strtolower ( $object_plural_code_name ) ) );

        // e.g. 'search' => '[%%Compobject_description%%]', 'replace' => 'free text'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) ) . '_description' ), 'replace' => $object_description ) );

        // e.g. 'search' => '[%%Compobject_description_ini%%]', 'replace' => 'free text'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) ) . '_description_ini' ), 'replace' => str_replace ( '"', '"_QQ_"', $object_description ) ) );

        // e.g. 'search' => '[%%Compobject_description_escaped%%]', 'replace' => 'free text'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) ) . '_description_escaped' ), 'replace' => $db->escape ( $object_description ) ) );

        // e.g. 'search' => '[%%Compobject_intro%%]', 'replace' => 'free text'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) ) . '_intro' ), 'replace' => $object_intro ) );

        // e.g. 'search' => '[%%Compobject_intro_ini%%]', 'replace' => 'free text'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) ) . '_intro_ini' ), 'replace' => str_replace ( '"', '"_QQ_"', $object_intro ) ) );

        // e.g. 'search' => '[%%Compobject_intro_escaped%%]', 'replace' => 'free text'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) ) . '_intro_escaped' ), 'replace' => $db->escape ( $object_intro ) ) );

        // e.g. 'search' => '[%%compobject_ordering%%]', 'replace' => '1'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) . '_ordering' ), 'replace' => $object_ordering ) );

        // e.g. 'search' => '[%%COMPOBJECTPLURAL%%]', 'replace' => 'DISCUSSIONGROUPS'	
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtoupper ( $template_object_name ) ) . 'PLURAL' ), 'replace' => str_replace ( "_", "", JString::strtoupper ( $object_plural_code_name ) ) ) );

        // e.g. 'search' => '[%%Compobjectplural%%]', 'replace' => 'Discussiongroups'	
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) . 'plural' ) ), 'replace' => JString::ucfirst ( JString::strtolower ( str_replace ( "_", "", $object_plural_code_name ) ) ) ) );

        // e.g. 'search' => '[%%CompObjectPlural%%]', 'replace' => 'DiscussionGroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( JString::strtolower ( $template_object_name ) ) . 'Plural' ) ), 'replace' => str_replace ( " ", "", JString::ucwords ( JString::strtolower ( str_replace ( "_", " ", $object_plural_code_name ) ) ) ) ) );

        // e.g. 'search' => '[%%CompObjectplural%%]', 'replace' => 'DiscussionGroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( JString::strtolower ( $template_object_name ) ) . 'plural' ) ), 'replace' => str_replace ( "_", "", JString::ucwords ( JString::strtolower ( $object_plural_code_name ) ) ) ) );

        // e.g. 'search' => '[%%compobjectplural%%]', 'replace' => 'discussiongroups'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) . 'plural' ), 'replace' => str_replace ( "_", "", JString::strtolower ( $object_plural_code_name ) ) ) );

        // e.g. 'search' => '[%%COMPOBJECT%%]', 'replace' => 'DISCUSSIONGROUP'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtoupper ( $template_object_name ) ) ), 'replace' => str_replace ( "_", "", JString::strtoupper ( $object_code_name ) ) ) );

        // e.g. 'search' => '[%%CompObject%%]', 'replace' => 'DiscussionGroup'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::ucwords ( JString::strtolower ( $template_object_name ) ) ) ), 'replace' => str_replace ( " ", "", JString::ucwords ( JString::strtolower ( str_replace ( "_", " ", $object_code_name ) ) ) ) ) );

        // e.g. 'search' => '[%%Compobject%%]', 'replace' => 'Discussiongroup'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( JString::ucfirst ( JString::strtolower ( str_replace ( " ", "", $template_object_name ) ) ) ), 'replace' => JString::ucfirst ( JString::strtolower ( str_replace ( "_", "", $object_code_name ) ) ) ) );

        // e.g. 'search' => '[%%compobject%%]', 'replace' => 'discussiongroup'
        array_push ( $search_replace_pairs, array ( 'search' => $this->_markupText ( str_replace ( " ", "", JString::strtolower ( $template_object_name ) ) ), 'replace' => str_replace ( "_", "", JString::strtolower ( $object_code_name ) ) ) );

        return $search_replace_pairs;
    }

    /**
     * Function to expand out the values for the field into the array parts and generate a language label
     *
     * @param	component_object	object	Component Object object from which to create label values
     * @param	field				object	Field object from which to generate options
     *
     * @return	values_array		array	Array of parts for the values		
     */
    protected function _generateValuesArray ( $component_object, $field )
    {
        $result = array ();
        switch ( $field->value_source )
        {
            // Generate from values set in the option values of the field
            case 'Option Values':
            case '':
            default:

                if ( $field->option_values == '' )
                {
                    return $result;
                }
                else
                {
                    $values_array = explode ( "\n", $field->option_values );
                    foreach ( $values_array as $value_text )
                    {
                        $result_entry = array ();
                        $value_text_array = explode ( ":", $value_text );
                        if ( count ( $value_text_array ) > 0 AND count ( $value_text_array ) < 3 )
                        {
                            if ( count ( $value_text_array ) == 1 )
                            {
                                $result_entry[ 'value' ] = '';
                                $result_entry[ 'text' ] = $value_text_array[ 0 ];
                                if ( (substr ( $result_entry[ 'text' ], 0, 1 ) === 'J' OR substr ( $result_entry[ 'text' ], 0, 4 ) === 'COM_')
                                        AND $result_entry[ 'text' ] === strtoupper ( $result_entry[ 'text' ] ) )
                                {
                                    // If all upper case begining with J then this is a Joomla! language variable
                                    // or if start is COM_ then this is language variable to be manually entered 
                                    // so leave label = text
                                    $result_entry[ 'label' ] = $result_entry[ 'text' ];
                                }
                                else
                                {
                                    $result_entry[ 'label' ] = strtoupper ( 'COM_' . JString::trim ( str_replace ( '_', '', $this->_component->code_name ) ) . '_' .
                                            JString::trim ( str_replace ( '_', '', $component_object->plural_code_name ) ) . '_' .
                                            JString::trim ( $field->code_name ) . '_VALUE_' .
                                            str_replace ( '-', '_', JApplication::stringURLSafe ( $value_text_array[ 0 ] ) ) );
                                }
                                $result_entry[ 'generic' ] = '0';
                                $result[] = $result_entry;
                            }
                            else
                            {
                                $result_entry[ 'value' ] = $value_text_array[ 0 ];
                                $result_entry[ 'text' ] = $value_text_array[ 1 ];
                                if ( (substr ( $result_entry[ 'text' ], 0, 1 ) === 'J' OR substr ( $result_entry[ 'text' ], 0, 4 ) === 'COM_')
                                        AND $result_entry[ 'text' ] === strtoupper ( $result_entry[ 'text' ] ) )
                                {
                                    // If all upper case begining with J then this is a Joomla! language variable
                                    // or if start is COM_ then this is language variable to be manually entered 
                                    // so leave label = text
                                    $result_entry[ 'label' ] = $result_entry[ 'text' ];
                                }
                                else
                                {
                                    $result_entry[ 'label' ] = strtoupper ( 'COM_' . JString::trim ( str_replace ( '_', '', $this->_component->code_name ) ) . '_' .
                                            JString::trim ( str_replace ( '_', '', $component_object->plural_code_name ) ) . '_' .
                                            JString::trim ( $field->code_name ) . '_VALUE_' .
                                            str_replace ( '-', '_', JApplication::stringURLSafe ( $value_text_array[ 1 ] ) ) );
                                }
                                $result_entry[ 'generic' ] = '0';
                                $result[] = $result_entry;
                            }
                        }
                    }
                }
                break;
        }
    }

    /**
     * Function to generate the XML for field options
     *
     * @param	values			array	Field values from which to generate options
     * @param	type			string	The field type for which we are creating a list
     *
     * @return	options			XML for a Joomla! Field		
     */
    protected function _generateValueOptions ( $values, $type = 'list' )
    {
        $options = '';
        if ( $type == 'groupedlist' )
        {
            $options .= "\t\t\t" . '<group label="' . JText::_ ( 'JALL' ) . '">' . "\n";
            $indent = "\t\t\t\t";
        }
        else
        {
            $indent = "\t\t\t";
        }

        if ( count ( $values ) == 0 )
        {
            $options .= $indent . '<option	value="1">JYES</option>' . "\n";
            $options .= $indent . '<option	value="0">JNO</option>' . "\n";
        }
        else
        {

            foreach ( $values as $value )
            {
                $options .= $indent . '<option	value="' . $value[ 'value' ] . '">' . trim ( $value[ 'label' ] ) . '</option>' . "\n";
            }
        }
        if ( $type == 'groupedlist' )
        {
            $options .= "\t\t\t" . '</group>';
        }
        return $options;
    }

    /**
     * Function to generate the language ini file strings for field values
     *
     * @param	values				array	Field values from which to generate options
     *
     * @return	language_vars	string	Strings for Joomla! Lanaguage ini files		
     */
    protected function _generateValueLanguageVars ( $values )
    {
        $language_vars = '';
        // If blank then the options will use JNO and JYEs so ignore
        if ( count ( $values ) > 0 )
        {
            foreach ( $values as $value )
            {
                // Ignore values where text = label as these are Joomla! standard language variables
                if ( $value[ 'text' ] != $value[ 'label' ] )
                {
                    if ( $value[ 'generic' ] == '0' )
                    {
                        $language_vars .= $value[ 'label' ] . '="' . trim ( $value[ 'text' ] ) . '"' . "\n";
                    }
                    else
                    {
                        $generic_value = $value[ 'label' ] . '="' . trim ( $value[ 'text' ] ) . '"' . "\n";
                        if ( !in_array ( $generic_value, $this->_generic_values ) )
                        {
                            $this->_generic_values[] = $generic_value;
                        }
                    }
                }
            }
        }
        return $language_vars;
    }

    /**
     * Function to generate the PHP code to display a selected value from a list in a display only view
     *
     * @param	values			array	Field values from which to generate options
     * @param	variable		string	The variable to use for the data value in the code
     * @param	ident			string	Tab characters to align the code
     * @param	field			object	The field currently being processed
     * @param	type			string	The type of field currently being processed
     *
     * @return			string	PHP code to display the value of a Joomla! Field		
     */
    protected function _generateValueDisplayCode ( $values, $variable, $ident, $field, $type = '' )
    {
        $phpcode = '';

        $type = ($field->multiple AND ( $type == 'list' OR $type == 'groupedlist')) ? 'list-multi' : $type;

        switch ( $type )
        {
            case 'checkboxes':
            case 'list-multi':
                if ( is_array ( $values ) AND count ( $values ) > 0 )
                {
                    $phpcode .= 'if (is_array(' . $variable . ')) :' . "\n";
                    $phpcode .= $ident . "\tif (count(" . $variable . ") > 0) : \n";
                    $phpcode .= $ident . "\t\techo '<div class=" . '"' . $type . '"' . ">';\n";
                    $phpcode .= $ident . "\t\techo '<ul>';\n";
                    $phpcode .= $ident . "\t\t" . 'foreach (' . $variable . ' as $value) :' . "\n";
                    $phpcode .= $ident . "\t\t\t" . 'switch ($value) :' . "\n";

                    foreach ( $values as $value )
                    {
                        $value[ 'value' ] == "''" ? $value[ 'value' ] = '' : $value[ 'value' ];
                        $phpcode .= $ident . "\t\t\t\tcase '" . trim ( addslashes ( $value[ 'value' ] ) ) . "':\n";
                        $phpcode .= $ident . "\t\t\t\t\techo '<li>'.JText::_('" . trim ( $value[ 'label' ] ) . "').'</li>';\n";
                        $phpcode .= $ident . "\t\t\t\t\tbreak;\n";
                    }

                    $phpcode .= $ident . "\t\t\tendswitch;\n";
                    $phpcode .= $ident . "\t\tendforeach;\n";
                    $phpcode .= $ident . "\t\techo '</ul>';\n";
                    $phpcode .= $ident . "\t\techo '</div>';\n";
                    $phpcode .= $ident . "\telse :\n";
                    $phpcode .= $ident . "\t\techo " . '$empty' . ";\n";
                    $phpcode .= $ident . "\tendif;\n";
                    $phpcode .= $ident . "endif;";
                    break;
                }
            case 'filelist':
            case 'folderlist':
            case 'imagelist':
                $phpcode .= 'if (is_array(' . $variable . ')) :' . "\n";
                $phpcode .= $ident . "\tif (count(" . $variable . ") > 0) : \n";
                $phpcode .= $ident . "\t\techo '<div class=" . '"' . $type . '"' . ">';\n";
                $phpcode .= $ident . "\t\t" . 'foreach (' . $variable . ' as $value) :' . "\n";
                $phpcode .= $ident . "\t\t\techo '<p>'" . '.$value.' . "'</p>';\n";
                $phpcode .= $ident . "\t\tendforeach;\n";
                $phpcode .= $ident . "\t\techo '</div>';\n";
                $phpcode .= $ident . "\telse :\n";
                $phpcode .= $ident . "\t\techo " . '$empty' . ";\n";
                $phpcode .= $ident . "\tendif;\n";
                $phpcode .= $ident . "else :;\n";
                $phpcode .= $ident . "\techo " . $variable . " != '' ? " . $variable . " : " . '$empty;' . "\n";
                $phpcode .= $ident . "endif;";
                break;
            case 'sql':
            case 'tag':
                $phpcode .= 'if (is_array(' . $variable . ')) :' . "\n";
                $phpcode .= $ident . "\tif (count(" . $variable . ") > 0) : \n";
                $phpcode .= $ident . "\t\techo '<div class=" . '"' . $type . '"' . ">';\n";
                $phpcode .= $ident . "\t\t" . 'foreach (' . $variable . ' as $' . $field->code_name . ') :' . "\n";
                $phpcode .= $ident . "\t\t\techo '<p>'" . '.$' . $field->code_name . "['value'].'</p>';\n";
                $phpcode .= $ident . "\t\tendforeach;\n";
                $phpcode .= $ident . "\t\techo '</div>';\n";
                $phpcode .= $ident . "\telse :\n";
                $phpcode .= $ident . "\t\techo " . '$empty' . ";\n";
                $phpcode .= $ident . "\tendif;\n";
                $phpcode .= $ident . "else :;\n";
                $phpcode .= $ident . "\techo " . $variable . " != '' ? " . $variable . " : " . '$empty;' . "\n";
                $phpcode .= $ident . "endif;";
                break;
            default:
                if ( $values == '' OR count ( $values ) == 0 )
                {
                    $phpcode .= 'switch (' . $variable . ') :' . "\n";
                    $phpcode .= $ident . "\tcase '0':\n";
                    $phpcode .= $ident . "\t\techo JText::_('JNO');\n";
                    $phpcode .= $ident . "\t\tbreak;\n";
                    $phpcode .= $ident . "\tcase '1':\n";
                    $phpcode .= $ident . "\t\techo JText::_('JYES');\n";
                    $phpcode .= $ident . "\t\tbreak;\n";
                    $phpcode .= $ident . "\tdefault:\n";
                    $phpcode .= $ident . "\t\techo JText::_('JNONE');\n";
                    $phpcode .= $ident . "\t\tbreak;\n";
                    $phpcode .= $ident . "endswitch;";
                }
                else
                {
                    $phpcode .= 'switch (' . $variable . ') :' . "\n";


                    foreach ( $values as $value )
                    {
                        $value[ 'value' ] == "''" ? $value[ 'value' ] = '' : $value[ 'value' ];
                        $phpcode .= $ident . "\tcase '" . trim ( addslashes ( $value[ 'value' ] ) ) . "':\n";
                        $phpcode .= $ident . "\t\techo JText::_('" . trim ( $value[ 'label' ] ) . "');\n";
                        $phpcode .= $ident . "\t\tbreak;\n";
                    }

                    $phpcode .= $ident . "\tdefault :\n";
                    $phpcode .= $ident . "\t\techo " . '$empty' . ";\n";
                    $phpcode .= $ident . "\t\tbreak;\n";

                    $phpcode .= $ident . "endswitch;";
                }
                break;
        }

        return $phpcode;
    }

    /**
     * Function to generate the PHP code to create a value/text array of all values for a field
     *
     * @param	values			array	Field values from which to generate options
     * @param	field			object	Field object from which to generate options
     * @param	type			string	The type of field to process
     * @param	prefix			string	A prefix to be added to Object/Table markup e.g. child
     *
     * @return			string	PHP code to create the array of values/text for a Joomla! Field		
     */
    protected function _generateFilterValueArray ( $values, $field, $type, $prefix = '' )
    {
        $phpcode = '';
        $db = JFactory::getDbo ();

        switch ( $type )
        {
            // ??? To allow for use of predefined fields the PHP Code to set up an array for these standard Joomla! fields need to be set up
            case 'accesslevel':

                break;
            case 'category':

                break;
            case 'status':

                break;

            case 'language':

                break;
            case 'user':

                break;
            case 'usergroup':

                break;
            case 'list' :
            case 'groupedlist' :
            case 'checkboxes' :
            case 'radio' :
                $phpcode .= "\t\t" . '$values = array();' . "\n";

                // Fields which are generally lists for values and text will be use the specified value and the Language file text variable name
                if ( count ( $values ) == 0 )
                {
                    $phpcode .= "\t\t" . '$values[] = ' . "array('value' => '0', 'text' => JText::_('JNO'));" . "\n";
                    $phpcode .= "\t\t" . '$values[] = ' . "array('value' => '1', 'text' => JText::_('JYES'));" . "\n";
                }
                else
                {

                    foreach ( $values as $value )
                    {
                        $value[ 'value' ] == "''" ? $value[ 'value' ] = '' : $value[ 'value' ];
                        $phpcode .= "\t\t" . '$values[] = ' . "array('value' => '" . trim ( addslashes ( $value[ 'value' ] ) ) . "', 'text' => JText::_('" . $value[ 'label' ] . "'));" . "\n";
                    }
                }
                $phpcode .= "\t\t" . 'return $values;' . "\n";

                break;
            case 'sql':
            case 'tag':
                $phpcode .= "\t\t" . '// Create a new query object.' . "\n";
                $phpcode .= "\t\t" . '$db = $this->getDbo();' . "\n";
                $phpcode .= "\t\t" . '$query = $db->getQuery(true);' . "\n\n";
                $phpcode .= "\t\t" . '$query->select(' . "'DISTINCT '." . '$db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "').' AS value');" . "\n";
                $phpcode .= "\t\t" . '$query->from($db->quoteName(' . "'#__" . $this->_markupText ( 'architectcomp' ) . '_' . $this->_markupText ( $prefix . 'compobjectplural' ) . "'));" . "\n";
                $phpcode .= "\t\t" . '$query->where($db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "').' != \'\'');" . "\n\n";
                $phpcode .= "\t\t" . '$query->order($db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "'));" . "\n\n";
                $phpcode .= "\t\t" . '// Setup the query' . "\n";
                $phpcode .= "\t\t" . '$db->setQuery($query->__toString());' . "\n\n";
                $phpcode .= "\t\t" . '// Get the values' . "\n";
                $phpcode .= "\t\t" . '$rows = $db->loadObjectList();' . "\n";
                $phpcode .= "\t\t" . '$values = array();' . "\n";
                $phpcode .= "\t\t" . 'foreach($rows as $row)' . "\n";
                $phpcode .= "\t\t" . '{' . "\n";
                $phpcode .= "\t\t\t" . '$values = array_merge($values,explode(",",$row->value));' . "\n";
                $phpcode .= "\t\t" . '}' . "\n";
                $phpcode .= "\t\t" . '// Make sure values are unique' . "\n";
                $phpcode .= "\t\t" . '$values = array_values(array_unique($values));' . "\n";
                $phpcode .= "\t\t" . '$query->clear();' . "\n\n";
                $phpcode .= "\t\t" . '// Construct the query' . "\n";
                $phpcode .= "\t\t" . '$query->select(' . "'DISTINCT '." . '$db->quoteName(' . "'list." . $db->escape ( $field->sql_key_field ) . "').' AS value, '." . '$db->quoteName(' . "'list." . $db->escape ( $field->sql_value_field ) . "').' AS text');" . "\n";
                $phpcode .= "\t\t" . '$query->from(' . "'(" . $db->escape ( $field->sql_query ) . ") AS list');" . "\n";
                $phpcode .= "\t\t" . '$query->order($db->quoteName(' . "'" . $db->escape ( $field->sql_value_field ) . "'));" . "\n\n";
                $phpcode .= "\t\t" . 'if (count($values) > 0)' . "\n\n";
                $phpcode .= "\t\t" . '{' . "\n\n";
                $phpcode .= "\t\t\t" . '$query->where($db->quoteName(' . "'list." . $db->escape ( $field->sql_key_field ) . "').' IN ('." . 'JString::trim(implode(\',\',$values),\',\')' . ".')');" . "\n\n";
                $phpcode .= "\t\t" . '}' . "\n\n";
                $phpcode .= "\t\t" . '// Setup the query' . "\n";
                $phpcode .= "\t\t" . '$db->setQuery($query->__toString());' . "\n\n";
                $phpcode .= "\t\t" . '// Return the result' . "\n";
                $phpcode .= "\t\t" . 'return $db->loadObjectList();' . "\n";
                break;
            default:
                // Not a list type field so pick up all values from the fields values in the table 
                $phpcode .= "\t\t" . '// Create a new query object.' . "\n";
                $phpcode .= "\t\t" . '$db = $this->getDbo();' . "\n";
                $phpcode .= "\t\t" . '$query = $db->getQuery(true);' . "\n\n";
                $phpcode .= "\t\t" . '// Construct the query' . "\n";
                if ( !$field->multiple )
                {
                    $phpcode .= "\t\t" . '$query->select(' . "'DISTINCT '." . '$db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "').' AS value, '." . '$db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "').' AS text');" . "\n";
                    $phpcode .= "\t\t" . '$query->from($db->quoteName(' . "'#__" . $this->_markupText ( 'architectcomp' ) . '_' . $this->_markupText ( $prefix . 'compobjectplural' ) . "'));" . "\n";
                    $phpcode .= "\t\t" . '$query->order($db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "'));" . "\n\n";
                    $phpcode .= "\t\t" . '// Setup the query' . "\n";
                    $phpcode .= "\t\t" . '$db->setQuery($query->__toString());' . "\n\n";
                    $phpcode .= "\t\t" . '// Return the result' . "\n";
                    $phpcode .= "\t\t" . 'return $db->loadObjectList();' . "\n";
                }
                else
                {
                    $phpcode .= "\t\t" . '$query->select(' . "'DISTINCT '." . '$db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "').' AS value');" . "\n";
                    $phpcode .= "\t\t" . '$query->from($db->quoteName(' . "'#__" . $this->_markupText ( 'architectcomp' ) . '_' . $this->_markupText ( $prefix . 'compobjectplural' ) . "'));" . "\n";
                    $phpcode .= "\t\t" . '$query->where($db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "').' != \'\'');" . "\n\n";
                    $phpcode .= "\t\t" . '$query->order($db->quoteName(' . "'" . $db->escape ( $field->code_name ) . "'));" . "\n\n";
                    $phpcode .= "\t\t" . '// Setup the query' . "\n";
                    $phpcode .= "\t\t" . '$db->setQuery($query->__toString());' . "\n\n";
                    $phpcode .= "\t\t" . '// Get the values' . "\n";
                    $phpcode .= "\t\t" . '$rows = $db->loadObjectList();' . "\n";
                    $phpcode .= "\t\t" . '$results = array();' . "\n";
                    $phpcode .= "\t\t" . '$values = array();' . "\n";
                    $phpcode .= "\t\t" . 'foreach($rows as $row)' . "\n";
                    $phpcode .= "\t\t" . '{' . "\n";
                    $phpcode .= "\t\t\t" . '$values = array_merge($values,explode(",",$row->value));' . "\n";
                    $phpcode .= "\t\t" . '}' . "\n";
                    $phpcode .= "\t\t" . '// Make sure values are unique' . "\n";
                    $phpcode .= "\t\t" . '$values = array_values(array_unique($values));' . "\n";
                    $phpcode .= "\t\t" . 'for ($i = 0; $i < count($values)-1; $i++)' . "\n";
                    $phpcode .= "\t\t" . '{' . "\n";
                    $phpcode .= "\t\t\t" . '$results[] = (object) array("value"=>$values[$i], "text"=>$values[$i]);' . "\n";
                    $phpcode .= "\t\t" . '}' . "\n";
                    $phpcode .= "\t\t" . '// Return the result' . "\n";
                    $phpcode .= "\t\t" . 'return $results;' . "\n";
                }
                break;
        }
        return $phpcode;
    }

    /**
     * Get a count of the number of fields for a component
     * 
     * @param		integer	Id of component
     * 
     * @return		integer Count of fields
     */
    protected function _countFields ( $component_id )
    {
        $count = 0;
        // Create a new query object.
        $db = JFactory::getDbo ();

        $query = $db->getQuery ( true );

        // Construct the query
        $query->select ( $db->quoteName ( 'id' ) );
        $query->from ( $db->quoteName ( '#__componentarchitect_componentobjects' ) );
        $query->where ( $db->quoteName ( 'component_id' ) . ' = ' . $component_id );

        // Setup the query
        $db->setQuery ( $query->__toString () );
        $component_objects = $db->loadObjectList ();

        foreach ( $component_objects as $component_object )
        {
            $query = $db->getQuery ( true );

            // Construct the query
            $query->select ( 'COUNT(' . $db->quoteName ( 'id' ) . ') as fields_count' );
            $query->from ( $db->quoteName ( '#__componentarchitect_fields' ) );
            $query->where ( $db->quoteName ( 'component_id' ) . ' = ' . $component_id );

            $query->where ( $db->quoteName ( 'component_object_id' ) . ' = ' . $component_object->id );

            try
            {
                // Set and query the database.
                $db->setQuery ( $query->__toString () );
                $row = $db->loadRow ();
            } catch ( RuntimeException $e )
            {
                throw new RuntimeException ( JText::sprintf ( 'COM_COMPONENTARCHITECT_ERROR_DATABASE_FATAL', $e->getMessage () ) );
            }

            // Double field count as they are processed twice once for Component Object and once for Fieldsets
            $count = $count + (2 * ( int ) $row[ 0 ]);
        }
        return $count;
    }

    /**
     * Get a count of the number of files being processed
     * 
     * @param		string	 Path of the files
     * @param		string	 (Optional) string to match in the name of the file to count (process wild cards of %)
     * 
     * @return		array	Count of all files
     */
    protected function _countFiles ( $file_path, $match = '' )
    {
        $count = 0;
        $text_match = str_replace ( '%', '', $match );

        if ( is_dir ( $file_path ) )
        {
            $objects = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $file_path ), RecursiveIteratorIterator::SELF_FIRST );
            foreach ( $objects as $name => $object )
            {
                if ( is_file ( $name ) AND strpos ( $name, 'index.html' ) === false )
                {
                    if ( $match == '' )
                    {
                        $count++;
                    }
                    else
                    {
                        // Get just the file name with no suffix
                        $name_array = explode ( '.', basename ( $name ) );
                        $file_name = $name_array[ 0 ];

                        if ( $match[ 0 ] == '%' )
                        {
                            if ( $match[ strlen ( $match ) - 1 ] == '%' )
                            {
                                if ( strpos ( $file_name, $text_match ) !== false )
                                {
                                    $count++;
                                }
                            }
                            else
                            {
                                if ( strpos ( $file_name, $text_match ) !== false )
                                {
                                    if ( substr ( $file_name, strpos ( $file_name, $text_match ), strlen ( $text_match ) ) == $text_match
                                            AND strpos ( $file_name, $text_match ) + strlen ( $text_match ) == strlen ( $file_name ) - 1 )
                                    {
                                        $count++;
                                    }
                                }
                            }
                        }
                        else
                        {
                            if ( $match[ strlen ( $match ) - 1 ] == '%' )
                            {
                                if ( substr ( $file_name, 0, strlen ( $text_match ) ) == $match )
                                {
                                    $count++;
                                }
                            }
                            else
                            {
                                if ( $file_name == $match )
                                {
                                    $count++;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $count;
    }

    /**
     * Get a count of the number of files excluded from being processed
     * 
     * @param		string	 Path of the code template files
     * 
     * @return		array	Count of excluded files because of conditions
     */
    protected function _countExcludedFiles ( $code_template_path )
    {
        $count = 0;

        $dir = opendir ( $code_template_path );
        while ( false !== ( $file = readdir ( $dir )) )
        {
            if ( ( $file != '.' ) AND ( $file != '..' ) )
            {
                if ( is_dir ( $code_template_path . '/' . $file ) )
                {
                    $code_template_path .= '/' . $file;
                    break;
                }
            }
        }

        // Check admin conditions to see what files are excluded
        if ( $this->_search_replace_helper->getComponentConditions ( 'generate_admin' ) == 0 )
        {
            $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/admin' ) );
        }
        else
        {
            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/admin' ), '%categor%' );  // for category helper and model files
            }

            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_admin_help' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/admin/help' ) );
            }
        }

        // Check site conditions to see what files are excluded
        if ( $this->_search_replace_helper->getComponentConditions ( 'generate_site' ) == 0 )
        {
            $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/site' ) );
        }
        else
        {
            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/site' ), 'categor%' );  // for category helper and model files
            }

            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_site_views' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/site/views' ) );
            }
            else
            {
                if ( $this->_search_replace_helper->getComponentConditions ( 'generate_site_layout_article' ) == 0 )
                {
                    $count += 2;
                }
                if ( $this->_search_replace_helper->getComponentConditions ( 'generate_site_layout_blog' ) == 0 )
                {
                    $count += 2;
                }
                if ( $this->_search_replace_helper->getComponentConditions ( 'generate_site_layout_tree' ) == 0 )
                {
                    $count += 2;
                }
                if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 0 )
                {
                    if ( $this->_search_replace_helper->getComponentConditions ( 'generate_site_views_categories' ) == 0 )
                    {
                        $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/site/views/categories' ) );
                    }

                    if ( $this->_search_replace_helper->getComponentConditions ( 'generate_site_views_category' ) == 0 )
                    {
                        $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/site/views/category' ) );
                    }
                }
            }
        }

        // Check plugin conditions to see what files are excluded
        if ( $this->_search_replace_helper->getComponentConditions ( 'generate_plugins' ) == 0 )
        {
            $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins' ) );
        }
        else
        {
            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins' ), '%categor%' );
            }

            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_plugins_events' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins/architectcomp/plg_architectcomp_events' ) );
            }

            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_plugins_itemnavigation' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins/architectcomp/plg_architectcomp_itemnavigation' ) );
            }

            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_plugins_vote' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins/architectcomp/plg_architectcomp_vote' ) );
            }

            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_plugins_search' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins/search' ) );
            }

            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_plugins_finder' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins/finder' ) );
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/plugins/architectcomp/plg_architectcomp_finder' ) );
            }
        }

        // Check module conditions to see what files are excluded
        if ( $this->_search_replace_helper->getComponentConditions ( 'generate_modules' ) == 0 )
        {
            if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 0 )
            {
                $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/modules' ), '%categor%' );  // for category helper and model files
            }

            $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/modules' ) );
        }

        if ( $this->_search_replace_helper->getComponentConditions ( 'generate_categories' ) == 0 )
        {
            $count += $this->_countFiles ( JPath::clean ( $code_template_path . '/media' ), '%categor%' );  // for category helper and model files
        }

        return $count;
    }

    /**
     * Get a count of the number of files excluded from being processed
     * 
     * @param		string	 Text to markup
     * 
     * @return		array	Count of excluded files because of conditions
     */
    protected function _markupText ( $text )
    {
        return $this->_code_template->template_markup_prefix . $text . $this->_code_template->template_markup_suffix;
    }

}

//[%%END_CUSTOM_CODE%%]
?>