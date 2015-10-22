<?php
/**
 * @version   $Id: Filter31.php 14427 2013-10-10 21:29:18Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_Joomla_Filter31 extends RokSprocket_Provider_Joomla_Filter
{
	/**
	 * @param $data
	 *
	 * @return void
	 */
	protected function tag($data)
	{
		$wheres = array();
		if (!empty($data)) {
			$this->query->select('CONCAT_WS(",", t.id) AS tag_ids, CONCAT_WS(",", t.title) AS tags');
			$this->query->join('LEFT', '#__contentitem_tag_map AS ct ON ct.content_item_id = a.id');
			$this->query->join('LEFT', '#__tags AS t ON t.id = ct.tag_id');
			foreach ($data as $match) {
				$wheres[] = $match . ' IN (CONCAT_WS(",", t.id))';
			}
			$this->filter_where[] = '(' . implode(' OR ', $wheres) . ')';

		}
	}
}
