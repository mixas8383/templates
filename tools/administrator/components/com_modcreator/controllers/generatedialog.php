<?php
/**
 * @version			$Id: generatedialog.php 411 2014-10-19 18:39:07Z BrianWade $
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
defined ( '_JEXEC' ) or die ;

if ( version_compare ( JVERSION , '3.0' , 'lt' ) )
{
  jimport ( 'joomla.application.component.controllerform' ) ;
}
jimport ( 'joomla.filesystem.file' ) ;
jimport ( 'joomla.filesystem.folder' ) ;

class ModcreatorControllerGenerateDialog extends JControllerForm
{

  /**
   * @var		string	The prefix to use with controller messages.
   * 
   */
  protected $text_prefix = 'COM_COMPONENTARCHITECT_GENERATE_DIALOG' ;

  /**
   * Class Constructor
   *
   * @param	array	$config		An optional associative array of configuration settings.
   * @return	void

   */
  function __construct ( $config = array() )
  {
    parent::__construct ( $config ) ;
  }

  /**
   * Method to display a dialog to request parameters for a component generation
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   */
  public function display ( $cachable = false , $urlparams = false )
  {
    $app = JFactory::getApplication () ;
    // Initialise variables. cid is value from list view and id is value from record view
    $ids = JRequest::getVar ( 'cid' , array() , '' , 'array' ) ;

    if ( count ( $ids ) <= 0 )
    {
      $ids = JRequest::getVar ( 'id' , array() , '' , 'array' ) ;
    }

    if ( count ( $ids ) > 1 )
    {
      $app->enqueueMessage ( JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_ONLY_ONE_COMPONENT' ) , 'error' ) ;
      $this->setRedirect ( 'index.php?option=com_modcreator&view=components' ) ;
      return false ;
    }


    $session = JFactory::getSession () ;
    $session->set ( 'generate_component_id' , $ids[ 0 ] ) ;

    $this->setRedirect ( 'index.php?option=com_modcreator&view=generatedialog' ) ;
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
    $logging = JRequest::getInt ( 'logging' , 0 ) ;
    $description = JRequest::getString ( 'description' , '' , 'default' , null ) ;
    if ( $module_name == '' )
    {
      $msg = JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GEN0004_NO_COMPONENT_SELECTED' ) ;
      $this->setRedirect ( 'index.php?option=com_modcreator' , $msg ) ;
      return false ;
    }

    $model = $this->getModel ( 'GenerateDialog' , 'ModcreatorModel' ) ;

    $model->setState ( 'module_name' , $module_name ) ;
    $model->setState ( 'output_path' , $output_path ) ;
    $model->setState ( 'logging' , $logging ) ;
    $model->setState ( 'description' , $description ) ;

    $zip_path = $model->generateModule () ;

    if ( $zip_path )
    {
      ?>
      <script type="text/javascript">
        var link_download = <?php echo '<a href="' . $zip_path . '">Download</a>' ; ?>;
        document.getElementById("generate-download").innerHTML = link_download;
        $("#generate-download").attr("style", "display:block");
      </script>
      <?php
      $this->setRedirect ( $zip_path ) ;
    }

//
//    $document = JFactory::getDocument () ;
//
//    $js = 'YOUR JS CODE' ;
//
//    $document->addScriptDeclaration ( $js ) ;


    JFactory::getApplication ()->close () ;
  }

  /**
   * Method to cancel an edit.
   *
   * @param   string  $key  The name of the primary key of the URL variable.
   *
   * @return  boolean  True if access level checks pass, false otherwise.
   *
   */
  public function cancel ( $key = null )
  {
    $app = JFactory::getApplication () ;
    $context = 'com_modcreator.generatedialog' ;

    JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) ) ;

    $this->setMessage ( JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_CANCEL' ) ) ;

    // Clear the data in the session.
    $app->setUserState ( $context . '.data' , '' ) ;

    // Redirect to the list screen.
    $this->setRedirect ( JRoute::_ ( 'index.php?option=com_modcreator&view=items' . $this->getRedirectToListAppend () , false ) ) ;

    return true ;
  }

  //[%%END_CUSTOM_CODE%%]	
}
