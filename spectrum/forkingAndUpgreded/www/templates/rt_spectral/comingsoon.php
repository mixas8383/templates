<?php
/**
* @version   $Id: comingsoon.php 14292 2013-10-08 11:13:09Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

// load and inititialize gantry class
require_once(dirname(__FILE__) . '/lib/gantry/gantry.php');
$gantry->init();

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$gantry->addStyle('grid-responsive.css', 5);
$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
$gantry->addLess('comingsoon.less', 'comingsoon.css', 7);

if ($gantry->browser->name == 'ie') {
	if ($gantry->browser->shortversion == 8) {
		$gantry->addScript('html5shim.js');
	}
}
$gantry->addScript('rokmediaqueries.js');

$gantry->addScript('simplecounter.js');

$gantry->addInlineScript("
	window.addEvent('load', function(){ 
		var counter = new SimpleCounter(
			'rt-comingsoon-counter',
			/* Year (full year), Month (0 to 11), Day (1, 31) */
			/* For example: Date(2016,10,1) means 1 November 2020 */			
			new Date('".$gantry->get('comingsoon-year')."','".$gantry->get('comingsoon-month')."','".$gantry->get('comingsoon-date')."'),
			{lang : {      
				d:{single:'".JText::_("DAY")."',plural:'".JText::_("DAYS")."'}, 		//days
				h:{single:'".JText::_("HOUR")."',plural:'".JText::_("HOURS")."'},     	//hours
				m:{single:'".JText::_("MINUTE")."',plural:'".JText::_("MINUTES")."'}, 	//minutes
				s:{single:'".JText::_("SECOND")."',plural:'".JText::_("SECONDS")."'} 	//seconds
			}
		});
	});
");

ob_start();
?>
<body id="rt-comingsoon" <?php echo $gantry->displayBodyTag(); ?>>
	<div id="rt-page-surround">
		<header id="rt-header-surround">
			<div class="rt-header-overlay-all"></div>
				<div class="rt-comingsoon-body">
					<div class="rt-logo-block rt-comingsoon-logo">
					    <a id="rt-logo" href="<?php echo $gantry->baseUrl; ?>"></a>
					</div>

					<?php
						$msgs = $app->getMessageQueue();
					?>
					<?php if (sizeof($msgs) > 0) : ?>	
						<div class="rt-container">
							<jdoc:include type="message" />
							<div class="clear"></div>
						</div>
					<?php endif; ?>

					<div class="rt-block">
						<h1 class="rt-sitename"><?php echo JText::_("RT_COMINGSOON_TITLE"); ?></h1>
					</div>					
					<div class="rt-block rt-counter-block">
						<div id="rt-comingsoon-counter"></div>					
					</div>
					<p class="rt-comingsoon-additional-message">
						<?php echo JText::_("RT_COMINGSOON_MESSAGE"); ?>
					</p>

					<?php if ($gantry->get('email-subscription-enabled')) : ?>
						<form class="rt-comingsoon-form" action="#">
							<input type="text" onblur="if(this.value=='') { this.value='<?php echo JText::_('RT_EMAIL') ?>'; return false; }" onfocus="if (this.value=='<?php echo JText::_('RT_EMAIL') ?>') this.value=''" value="<?php echo JText::_('RT_EMAIL') ?>" size="18" alt="<?php echo JText::_('RT_EMAIL') ?>" class="inputbox" name="email">
							<input type="submit" name="Submit" class="button" value="<?php echo JText::_("RT_SUBSCRIBE"); ?>" />
						</form>
						<div class="clear"></div>	
					<?php endif; ?>

					<h2 class="rt-authorized-form-title"><?php echo JText::_("AUTHORIZED_LOGIN"); ?></h2>

					<p class="rt-authorized-login-message">
						<?php echo JText::_("RT_AUTHORIZED_LOGIN_MESSAGE"); ?>
					</p>				

					<?php 
				        $user    = JFactory::getUser();
				        $isAdmin = $user->get('isRoot');					
					?>
					<?php if (!$isAdmin): ?>
						<form class="rt-authorized-login-form" action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
							<input name="username" id="username" class="inputbox" type="text" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" onblur="if(this.value=='') { this.value='<?php echo JText::_('JGLOBAL_USERNAME') ?>'; return false; }" onfocus="if (this.value=='<?php echo JText::_('JGLOBAL_USERNAME') ?>') this.value=''" value="<?php echo JText::_('JGLOBAL_USERNAME') ?>" />
							<input type="password" name="password" class="inputbox" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd" onblur="if(this.value=='') { this.value='<?php echo JText::_('JGLOBAL_PASSWORD') ?>'; return false; }" onfocus="if (this.value=='<?php echo JText::_('JGLOBAL_PASSWORD') ?>') this.value=''" value="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
							<input type="hidden" name="remember" class="inputbox" value="yes" id="remember" />
							<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
							<input type="hidden" name="option" value="com_users" />
							<input type="hidden" name="task" value="user.login" />
							<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
							<?php echo JHtml::_('form.token'); ?>					
						</form>
					<?php endif; ?>
					<?php if ($isAdmin): ?>
						<form class="rt-authorized-login-form" action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
							<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
							<input type="hidden" name="option" value="com_users" />
							<input type="hidden" name="task" value="user.logout" />
							<input type="hidden" name="return" value="<?php echo $return; ?>" />
							<?php echo JHtml::_('form.token'); ?>
						</form>	
					<?php endif; ?>					

				</div>
				<div class="rt-comingsoon-footer"></div>
		</header>
	</div>		
</body>


</html>
<?php

$body = ob_get_clean();
$gantry->finalize();

require_once(JPATH_LIBRARIES.'/joomla/document/html/renderer/head.php');
$header_renderer = new JDocumentRendererHead($doc);
$header_contents = $header_renderer->render(null);
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<?php echo $header_contents; ?>
	<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
	<meta name="viewport" content="width=960px">
	<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
	<meta name="viewport" content="width=1200px">
	<?php else : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php endif; ?>
</head>
<?php
$header = ob_get_clean();
echo $header.$body;