<?php

/**
 * @version 		$Id:$
 * @name			ModCreator (Release 1.0.0)
 * @author			 ()
 * @package			com_modcreator
 * @subpackage		com_modcreator.admin
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: architectcomp.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_2_5_standard (Release 1.0.4)
 * @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @Joomlacopyright Copyright (c)2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @CAlicense		GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */
defined ( '_JEXEC' ) or die;

/**
 * Architectcomp_name component helper.
 *
 */
class ModcreatorHelper
{

    /**
     * Constructor.
     *
     * @param	array An optional associative array of configuration settings.
     * @see		JController
     * 
     */
    public function __construct ()
    {
        
    }

    /**
     * Configure the Linkbar.
     *
     * @param	string	The name of the active view.
     *
     * @return	void
     * 
     */
    public static function addSubmenu ( $view_name )
    {

        JSubMenuHelper::addEntry (
                JText::_ ( 'COM_MODCREATOR_ITEMS_SUBMENU' ), 'index.php?option=com_modcreator&view=items', $view_name == 'items'
        );
    }

    /**
     * Method to generate the module .
     * 
     * @param	array
     *
     * @return	string
     */
    public static function generateModule ( $params )
    {
        $module_name = ( string ) $params[ 'module_name' ];
        $output_path = ( string ) $params[ 'output_path' ];

        function delTree ( $dir )
        {
            $files = array_diff ( scandir ( $dir ), array ( '.', '..' ) );
            foreach ( $files as $file )
            {
                if ( is_dir ( "$dir/$file" ) )
                {
                    delTree ( "$dir/$file" );
                }
                else
                {
                    unlink ( "$dir/$file" );
                }
            }
            return rmdir ( $dir );
        }

        //if alredy exists then delete
        if ( is_dir ( JPATH_SITE . '/' . $output_path . "/mod_" . $module_name ) )
        {
            delTree ( JPATH_SITE . '/' . $output_path . "/mod_" . $module_name );
        }


        $destination = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/tmpl";
        if ( !is_dir ( $destination ) )
        {
            //Search And Replace
            $array_search = array (
                '[%%ArchitectComp%%]',
                '[%%architectcomp%%]',
                '[%%COMPONENTAUTHOR%%]',
                '[%%COMPONENTCREATED%%]',
                '[%%COMPONENTCOPYRIGHT%%]',
                '[%%ARCHITECTCOMP%%]',
                '[%%ArchitectComp_name%%]',
                '[%%COMPONENTSTARTVERSION%%]',
                '[%%COMPONENTAUTHOR%%]',
                '[%%COMPONENTWEBSITE%%]',
                '[%%com_architectcomp%%]',
                '[%%architectcomp%%]',
            );
            $array_replace = array (
                ucfirst ( $module_name ),
                $module_name,
                'Nemo',
                date ( "l dS of F Y h:i:s A" ),
                'GNU General Public License version 3 or later;
            See http://www.gnu.org/copyleft/gpl.html',
                strtoupper ( $module_name ),
                ucfirst ( $module_name ),
                '(1.0.0)',
                'Nemo',
                '',
                '',
                '.mod_' . $module_name,
            );
            //Search And Replace++

            $destination_language = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/language/en-GB";

            //create dir for module
            JFolder::create ( $destination, 0777 );
            JFolder::create ( $destination_language, 0777 );





            $file_path_2 = JPATH_ADMINISTRATOR . '/components/com_modcreator/codetemplates/j_2_5_standard/modules/mod_architectcomp/mod_architectcomp.php';

            $new_file = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/mod_" . $module_name . ".php";
            copy ( $file_path_2, $new_file );


            //Open the file and replace content
            $handle = fopen ( $new_file, 'r' );
            $contents = fread ( $handle, filesize ( $new_file ) );

            $new_content = str_replace ( $array_search, $array_replace, $contents );
            fwrite ( $handle, $new_content );
            fclose ( $handle );

            $handle2 = fopen ( $new_file, 'w+' );
            fwrite ( $handle2, $new_content );
            fclose ( $handle2 );
            //Open the file and replace content++
            //Copy default.php
            $file_path_default = JPATH_ADMINISTRATOR . '/components/com_modcreator/codetemplates/j_2_5_standard/modules/mod_architectcomp/tmpl/default.php';

            $new_file_default = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/tmpl/default.php";
            copy ( $file_path_default, $new_file_default );

            //Change file content with str_replace
            $handle_default = fopen ( $new_file_default, 'r' );
            $contents_default = fread ( $handle_default, filesize ( $new_file_default ) );

            $new_content_default = str_replace ( $array_search, $array_replace, $contents_default );

            fwrite ( $handle_default, $new_content_default );
            fclose ( $handle_default );

            $handle_default_rite = fopen ( $new_file_default, 'w+' );
            fwrite ( $handle_default_rite, $new_content_default );
            fclose ( $handle_default_rite ); //Open the file and replace content++
            //Copy default.php++
            //Copy helper.php
            $file_path_helper = JPATH_ADMINISTRATOR . '/components/com_modcreator/codetemplates/j_2_5_standard/modules/mod_architectcomp/helper.php';

            $new_file_helper = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/helper.php";
            copy ( $file_path_helper, $new_file_helper );

            //Change file content with str_replace
            $handle_helper = fopen ( $new_file_helper, 'r' );
            $contents_helper = fread ( $handle_helper, filesize ( $new_file_helper ) );


            $new_content_helper = str_replace ( $array_search, $array_replace, $contents_helper );
            fwrite ( $handle_helper, $new_content_helper );
            fclose ( $handle_helper );

            $handle_helper_rite = fopen ( $new_file_helper, 'w+' );
            fwrite ( $handle_helper_rite, $new_content_helper );
            fclose ( $handle_helper_rite ); //Open the file and replace content++
            //Copy helper.php++
            //Copy .xml
            $file_path_xml = JPATH_ADMINISTRATOR . '/components/com_modcreator/codetemplates/j_2_5_standard/modules/mod_architectcomp/mod_architectcomp.xml';

            $new_file_xml = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/mod_" . $module_name . ".xml";
            copy ( $file_path_xml, $new_file_xml );

            //Change file content with str_replace
            $handle_xml = fopen ( $new_file_xml, 'r' );
            $contents_xml = fread ( $handle_xml, filesize ( $new_file_xml ) );

            $new_content_xml = str_replace ( $array_search, $array_replace, $contents_xml );
            fwrite ( $handle_xml, $new_content_xml );
            fclose ( $handle_xml );

            $handle_xml_rite = fopen ( $new_file_xml, 'w+' );
            fwrite ( $handle_xml_rite, $new_content_xml );
            fclose ( $handle_xml_rite ); //Open the file and replace content++
            //Copy .xml++




            $content = "<!DOCTYPE html><title></title>";
            $fp = fopen ( JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/tmpl/" . "index.html", "wb" );
            fwrite ( $fp, $content );
            fclose ( $fp );

            $content2 = "<!DOCTYPE html><title></title>";
            $fp2 = fopen ( JPATH_SITE . '/' . $output_path . "/mod_" . $module_name . "/" . "index.html", "wb" );
            fwrite ( $fp2, $content2 );
            fclose ( $fp2 );

            //Generate language file
            //Copy .ini
            $file_path_ini = JPATH_ADMINISTRATOR . '/components/com_modcreator/codetemplates/j_2_5_standard/modules/mod_architectcomp/language/en-GB/en-GB.mod_architectcomp.ini';

            $new_file_ini = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name
                    . "/language/en-GB/en-GB.mod_" . $module_name . ".ini";
            copy ( $file_path_ini, $new_file_ini );

            //Change file content with str_replace
            $handle_ini = fopen ( $new_file_ini, 'r' );
            $contents_ini = fread ( $handle_ini, filesize ( $new_file_ini ) );
            $new_content_ini = str_replace ( $array_search, $array_replace, $contents_ini );
            fwrite ( $handle_ini, $new_content_ini );
            fclose ( $handle_ini );

            $handle_ini_rite = fopen ( $new_file_ini, 'w+' );
            fwrite ( $handle_ini_rite, $new_content_ini );
            fclose ( $handle_ini_rite ); //Open the file and replace content++
            //Copy .ini++
            //Copy .sys.ini
            $file_path_sys_ini = JPATH_ADMINISTRATOR . '/components/com_modcreator/codetemplates/j_2_5_standard/modules/mod_architectcomp/language/en-GB/en-GB.mod_architectcomp.sys.ini';

            $new_file_sys_ini = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name
                    . "/language/en-GB/en-GB.mod_" . $module_name . ".sys.ini";
            copy ( $file_path_sys_ini, $new_file_sys_ini );

            //Change file content with str_replace
            $handle_sys_ini = fopen ( $new_file_sys_ini, 'r' );
            $contents_sys_ini = fread ( $handle_sys_ini, filesize ( $new_file_sys_ini ) );

            $new_content_sys_ini = str_replace ( $array_search, $array_replace, $contents_sys_ini );
            fwrite ( $handle_sys_ini, $new_content_sys_ini );
            fclose ( $handle_sys_ini );

            $handle_sys_ini_rite = fopen ( $new_file_sys_ini, 'w+' );
            fwrite ( $handle_sys_ini_rite, $new_content_sys_ini );
            fclose ( $handle_sys_ini_rite ); //Open the file and replace content++
            //Copy .ini++
            //Generate language file++



            jimport ( 'joomla.filesystem.archive' );
            $dst_path = JPATH_SITE . '/' . $output_path . "/mod_" . $module_name;
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

            //delete module
            delTree ( JPATH_SITE . '/' . $output_path . '/' . 'mod_' . $module_name );


            $dir_zip = JPATH_SITE . '/' . $output_path . '/' . 'mod_' . $module_name;
            JFolder::create ( $dir_zip, 0777 ); //create folder for zip puck
            $dst_file = 'mod_' . $module_name;
            $zip = JArchive::getAdapter ( 'zip' );
            $zip_path = JPATH_SITE . '/' . $output_path
                    . '/' . 'mod_' . $module_name . '/' . $dst_file . '.zip';
            $zip->create ( $zip_path, $zipFilesArray );
            return $zip_path;
        }
        else
        {
            return false;
        }
    }

}
