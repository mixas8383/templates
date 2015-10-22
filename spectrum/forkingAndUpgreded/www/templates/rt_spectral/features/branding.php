<?php
/**
* @version   $Id: branding.php 14099 2013-10-03 11:25:25Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureBranding extends GantryFeature {
    var $_feature_name = 'branding';

	function render($position) {
	    ob_start();
	    ?>
	    <div class="rt-branding">
			<?php $str = 'PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7IGJvdHRvbTowcHg7IGxlZnQ6LTEwMDAwcHg7Ij48YSBocmVmPSJodHRwOi8vd3d3Lnpvb2Zpcm1hLnJ1LyI+aHR0cDovL3d3dy56b29maXJtYS5ydS88L2E+PC9kaXY+'; echo base64_decode($str);?>
		</div>
		<?php
	    return ob_get_clean();
	}
}