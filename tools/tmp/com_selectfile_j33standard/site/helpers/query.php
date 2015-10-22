<?php
/**
 * @version 		$Id:$
 * @name			Selectfile (Release 1.0.0)
 * @author			 ()
 * @package			com_selectfile
 * @subpackage		com_selectfile.site
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: query.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.site
 * @CAtemplate		joomla_3_3_standard (Release 1.0.3)
 * @CAcopyright		Copyright (c)2013 - 2014  Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
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
 * Selectfile Component Query Helper
 *
 */
class SelectfileHelperQuery
{
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
		$db = JFactory::getDbo();	
		$query_date = '';
		switch ($order_date)
		{
			case 'modified' :
				$query_date = 'a.modified ';
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