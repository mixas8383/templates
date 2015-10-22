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
 * @CAversion		Id: view.html.php 418 2014-10-22 14:42:36Z BrianWade $
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

jimport ( 'joomla.application.component.view' );

/**
 * View class for a list of items.
 *
 */
class ModcreatorViewItems extends JView
{

    protected $items;
    protected $pagination;
    protected $state;
    protected $can_do;

    /**
     * Display the view
     */
    public function display ( $tpl = null )
    {
        // Initialise variables.
        $this->items = $this->get ( 'Items' );
        $this->pagination = $this->get ( 'Pagination' );
        $this->state = $this->get ( 'State' );

        $this->can_do = ModcreatorItemsHelper::getActions ();

        // Check for errors.
        if ( count ( $errors = $this->get ( 'Errors' ) ) )
        {
            JError::raiseError ( 500, implode ( "\n", $errors ) );
            return false;
        }

        $this->addToolbar ();
        $this->_prepareDocument ();
        parent::display ( $tpl );
    }

    /**
     * Add the page title and toolbar.
     *
     */
    protected function addToolbar ()
    {
        JToolbarHelper::title ( JText::_ ( 'COM_MODCREATOR_ITEMS_LIST_HEADER' ), 'stack items' );


        //TODO
        //[%%START_CUSTOM_CODE%%]	
        if ( version_compare ( JVERSION, '3.0', 'lt' ) )
        {
            $bar = JToolbar::getInstance ( 'toolbar' );

            $url = 'index.php?option=com_modcreator&view=generatedialog';
            $bar->appendButton ( 'Link', 'generate', JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_BUTTON' ), $url );

//            $url = 'index.php?option=com_modcreator&view=componentwizard';
//            $bar->appendButton ( 'Link', 'componentwizard', JText::_ ( 'COM_COMPONENTARCHITECT_COMPONENT_WIZARD_BUTTON' ), $url );
            JToolBarHelper::divider ();
        }
        else
        {
            JToolBarHelper::custom ( 'componentwizard.display', 'new', '', JText::_ ( 'COM_COMPONENTARCHITECT_COMPONENT_WIZARD_BUTTON' ), false );
        }
        //[%%END_CUSTOM_CODE%%]	
        //TODO++



        if ( $this->can_do->get ( 'core.create' ) )
        {
            JToolbarHelper::addNew ( 'item.add', 'JTOOLBAR_NEW' );
        }
        if ( $this->can_do->get ( 'core.edit' ) OR $this->can_do->get ( 'core.edit.own' ) )
        {
            JToolbarHelper::editList ( 'item.edit', 'JTOOLBAR_EDIT' );
        }

        if ( $this->can_do->get ( 'core.edit.state' ) )
        {

            if ( $this->state->get ( 'filter.state' ) != 2 )
            {
                JToolbarHelper::divider ();
                JToolbarHelper::custom ( 'items.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true );
                JToolbarHelper::custom ( 'items.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true );
            }

            if ( $this->state->get ( 'filter.state' ) != -1 )
            {
                JToolbarHelper::divider ();
                if ( $this->state->get ( 'filter.state' ) != 2 )
                {
                    JToolbarHelper::archiveList ( 'items.archive', 'JTOOLBAR_ARCHIVE' );
                }
                else
                {
                    if ( $this->state->get ( 'filter.state' ) == 2 )
                    {
                        JToolbarHelper::unarchiveList ( 'items.publish', 'JTOOLBAR_UNARCHIVE' );
                    }
                }
            }
        }

        if ( $this->can_do->get ( 'core.edit.state' ) )
        {
            JToolbarHelper::custom ( 'items.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true );
        }

        if ( $this->state->get ( 'filter.state' ) == -2 )
        {
            if ( $this->can_do->get ( 'core.delete' ) )
            {
                JToolbarHelper::deleteList ( '', 'items.delete', 'JTOOLBAR_EMPTY_TRASH' );
            }

            JToolbarHelper::divider ();
        }
        else
        {
            if ( $this->can_do->get ( 'core.edit.state' ) )
            {
                JToolbarHelper::trash ( 'items.trash', 'JTOOLBAR_TRASH' );
                JToolBarHelper::divider ();
            }
        }

        if ( $this->can_do->get ( 'core.admin' ) )
        {
            JToolbarHelper::preferences ( 'com_modcreator' );
            JToolbarHelper::divider ();
        }
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument ()
    {
        // Include HTML Helpers
        JHtml::addIncludePath ( JPATH_COMPONENT . '/helpers/html' );

        // Include custom admin css
        $this->document->addStyleSheet ( JUri::root () . "administrator/components/com_modcreator/assets/css/admin.css" );

        // Add Javscript functions for field display
        JHtml::_ ( 'behavior.tooltip' );

        JHTML::_ ( 'script', 'system/multiselect.js', false, true );
    }

}
