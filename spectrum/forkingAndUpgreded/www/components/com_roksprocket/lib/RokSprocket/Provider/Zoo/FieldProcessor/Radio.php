<?php
/**
 * @version   $Id: Radio.php 14578 2013-10-17 20:36:21Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_Zoo_FieldProcessor_Radio extends RokSprocket_Provider_Zoo_FieldProcessor_Abstract
{
	public function getValue(Element $element)
	{
		$result = false;
		$data = $element->data();

		if (is_array($data)){
		foreach ($data['option'] as $data_value) {
			foreach ($element->config->option as $object) {
				if ($object['value'] == $data_value) {
					$result = $object;
					break 2;
				}
			}
		}
		}
		return $result;
	}
}
