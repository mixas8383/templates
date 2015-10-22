<?php

/**
 * @tempversion$
 * @name			Mymodule (Release (1.0.0))
 * @author			Nemo ()
 * @package			
 * @subpackage		.mod_mymodule
 * @copyright		GNU General Public License version 3 or later;
            See http://www.gnu.org/copyleft/gpl.html
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: helper.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.mod_architectcomp
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

abstract class modMymoduleHelper
{

    public static function getList ( $params )
    {
        $db = JFactory::getDBO ();
        jimport ( 'joomla.utilities.date' );
        $date_now = JFactory::getDate ( time () );

        $where = array ();
        $where[] = '`t`.`state`= 1 ';
        $where[] = ' (`t`.`publish_up` <=  ' . $db->quote ( $date_now->toMySQL () ) . ')';
        $where[] = ' (`t`.`publish_down` >  ' . $db->quote ( $date_now->toMySQL () ) . ' OR `t`.`publish_down`="0000-00-00 00:00:00")';

        $whereQ = count ( $where ) ? ' WHERE (' . implode ( ') AND (', $where ) . ') ' : '';
        $limit = ($params->get ( 'count', 5 ) ) ? ' LIMIT 0 , ' . $params->get ( 'count', 5 ) : '';

        $query = '#mod_mymodule Query' . "\n"
                . ' SELECT '
                . ' `a`.`id` , '
                . ' `a`.`fulltext` '
                . ' FROM `#__content` AS `a`'
                . ' LEFT JOIN `#__categories` AS `c` ON `c`.`id` = `a`.`catid`'
                . $whereQ
                . $limit
        ;
        $db->setQuery ( $query );

        $rows = $db->loadObjectList ();

        if ( empty ( $rows ) )
        {
            return array ();
        }
        return $rows;
    }

}
