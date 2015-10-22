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
 * @CAversion		Id: controller.php 418 2014-10-22 14:42:36Z BrianWade $
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
defined ( '_JEXEC' ) or die ;

jimport ( 'joomla.application.component.controller' ) ;

class ModcreatorController extends JController
{

  /**
   * @var		string	The default view.
   * 
   */
  protected $default_view = 'generatedialog' ;

  /**
   * Method to display a view.
   *
   * @param	boolean			If true, the view output will be cached
   * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
   *
   * @return	JController		This object to support chaining.
   * 
   */
  public function display ( $cachable = false , $url_params = false )
  {
    $view = JRequest::getCmd ( 'view' , $this->default_view ) ;
    $layout = JRequest::getCmd ( 'layout' , 'default' ) ;
    $id = JRequest::getInt ( 'id' ) ;

    // Load the submenu.
    ModcreatorHelper::addSubmenu ( $view ) ;

    switch ( $view )
    {
      case 'item':
      case 'items':
        require_once JPATH_COMPONENT . '/helpers/items.php' ;
        break ;
    }
    // Check for edit form.
    switch ( $view )
    {
      case 'item':
        if ( $layout == 'edit' AND ! $this->checkEditId ( 'com_modcreator.edit.item' , $id ) )
        {

          // Somehow the person just went to the form - we don't allow that.
          $this->setError ( JText::sprintf ( 'JLIB_APPLICATION_ERROR_UNHELD_ID' , $id ) ) ;
          $this->setMessage ( $this->getError () , 'error' ) ;
          $this->setRedirect ( JRoute::_ ( 'index.php?option=com_modcreator&view=items' , false ) ) ;

          return false ;
        }
        break ;
    }
    parent::display () ;

    return $this ;
  }

  /**
   * Method to generate Module code
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   */
  public function generateModule ()
  {    
    $module_name = JRequest::getString ( 'module_name' , '' ) ;
    $output_path = str_replace ( ' ' , '_' , JRequest::getString ( 'output_path' , '' , 'default' , null ) ) ;
    if ( $module_name == '' )
    {
      $msg = JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0004_NO_COMPONENT_SELECTED' ) ;
      $this->setRedirect ( 'index.php?option=com_modcreator' , $msg ) ;
      return false ;
    }

    $params = array() ;
    $params[ 'module_name' ] = $module_name ;
    $params[ 'output_path' ] = $output_path ;

    require_once JPATH_COMPONENT . '/helpers/modcreator.php' ;

    $zip_path = ModcreatorHelper::generateModule ( $params ) ;

    $this->setRedirect ( $zip_path ) ;

    JFactory::getApplication ()->close () ;
  }

}
