<?php
/**
 * @version 		$Id:$
 * @name			Example (Release 1.0.0)
 * @author			 ()
 * @package			com_example
 * @subpackage		com_example.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: query.php 418 2014-10-22 14:42:36Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
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

defined('_JEXEC') or die;

/**
 * Example Component Query Helper
 *
 */
class ExampleHelperQuery
{
	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param	string	$order_by	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * 
	 */
	public static function orderbyPrimary($order_by)
	{
		switch ($order_by)
		{
			case 'alpha' :
				$order_by = 'c.path, ';
				break;

			case 'ralpha' :
				$order_by = 'c.path DESC, ';
				break;
			case 'ordering' :
				$order_by = 'c.lft, ';
				break;
			default :
				$order_by = '';
				break;
		}

		return $order_by;
	}
	/**
	 * Translate an order code to a field for secondary ordering.
	 *
	 * @param	string	$order_by	The ordering code.
	 * @param	string	$order_date	The ordering code for the date.
	 * @param	string	$default_sec	The default field to use for secondary order by.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * 
	 */
	public static function orderbySecondary($order_by, $order_date, $default_sec = 'ordering')
	{
		$query_date = self::getQueryDate($order_date);

		switch ($order_by)
		{
			case 'date' :

				if ($query_date == '')
				{
					$order_by = 'a.'.$default_sec;
				}
				else
				{			
					$order_by = $query_date;
				}
				break;				

			case 'rdate' :
				if ($query_date == '')
				{
					$order_by = 'a.'.$default_sec;
				}
				else
				{			
					$order_by = $query_date . ' DESC ';
				}
				break;
			case 'alpha' :
				$order_by = 'a.name';
				break;
			case 'ralpha' :
				$order_by = 'a.name DESC';
				break;
			case 'hits' :
				$order_by = 'a.hits DESC';
				break;
			case 'rhits' :
				$order_by = 'a.hits';
				break;
			case 'ordering' :
				$order_by = 'a.ordering';
				break;
			case 'creator' :
				$order_by = 'created_by_name';
				break;
			case 'rcreated_by' :
				$order_by = 'created_by_name DESC';
				break;
			default :
				$order_by = 'a.'.$default_sec;

				break;
		}

		return $order_by;
	}

	/**
	 * Translate an order code to a field for ordering.
	 *
	 * @param	string	$order_date	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * 
	 */
	public static function getQueryDate($order_date) 
	{
		$query_date = '';
		switch ($order_date)
		{
			case 'modified' :
				$query_date = 'a.modified ';
				break;
			// use created if publish_up is not set
			case 'publish_up' :
				$query_date = ' CASE WHEN a.publish_up = 0 THEN a.modified ELSE a.publish_up END ';
				$query_date = ' CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END ';
				break;
			case 'created' :
				$query_date = ' a.created ';
				break;
			default :
				break;
		}

		return $query_date;
	}
	/**
	 * Method to order the intro items array for ordering
	 * down the columns instead of across.
	 * The layout always lays the introtext items out across columns.
	 * Array is reordered so that, when items are displayed in index order
	 * across columns in the layout, the result is that the
	 * desired item ordering is achieved down the columns.
	 *
	 * @param	array	$items	Array of intro items
	 * @param	integer	$num_columns	Number of columns in the layout
	 *
	 * @return	array	Reordered array to achieve desired ordering down columns
	 * 
	 */
	public static function orderDownColumns(&$items, $num_columns = 1)
	{
		$count = count($items);

		// just return the same array if there is nothing to change
		if ($num_columns == 1 OR !is_array($items) OR $count <= $num_columns)
		{
			$return = $items;
		}
		// we need to re-order the intro items array
		else
		{
			// we need to preserve the original array keys
			$keys = array_keys($items);

			$max_rows = ceil($count / $num_columns);
			$num_cells = $max_rows * $num_columns;
			$num_empty = $num_cells - $count;
			$index = array();

			// calculate number of empty cells in the array


			// fill in all cells of the array
			// put -1 in empty cells so we can skip later

			for ($row = 1, $i = 1; $row <= $max_rows; $row++)
			{
				for ($col = 1; $col <= $num_columns; $col++)
				{
					if ($num_empty > ($num_cells - $i))
					{
						// put -1 in empty cells
						$index[$row][$col] = -1;
					}
					else
					{
						// put in zero as placeholder
						$index[$row][$col] = 0;
					}
					$i++;
				}
			}

			// layout the items in column order, skipping empty cells
			$i = 0;
			for ($col = 1; ($col <= $num_columns) AND ($i < $count); $col++)
			{
				for ($row = 1; ($row <= $max_rows) AND ($i < $count); $row++)
				{
					if ($index[$row][$col] != - 1)
					{
						$index[$row][$col] = $keys[$i];
						$i++;
					}
				}
			}

			// now read the $index back row by row to get items in right row/col
			// so that they will actually be ordered down the columns (when read by row in the layout)
			$return = array();
			$i = 0;
			for ($row = 1; ($row <= $max_rows) AND ($i < $count); $row++)
			{
				for ($col = 1; ($col <= $num_columns) AND ($i < $count); $col++)
				{
					$return[$keys[$i]] = $items[$index[$row][$col]];
					$i++;
				}
			}
		}
		return $return;
	}
}