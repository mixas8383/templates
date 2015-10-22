<?php
/**
 * @version   $Id: RokInjectModule_Header.php 8051 2013-03-01 20:08:54Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;

class RokInjectModule_Header extends RokCommon_Header_AbstractHeader
{
	/**
	 * @var JDocument
	 */
	protected $document;

	/**
	 * @var bool
	 */
	protected $populated = false;

	public function isPopulated()
	{
		return $this->populated;
	}

	public function __construct()
	{
		parent::__construct();
		$this->document = JFactory::getDocument();
	}

	public function addScript($file, $order = self::DEFAULT_ORDER)
	{
		if (!empty($file)) {
			$this->document->addScript($file);
		}
	}

	public function addInlineScript($text, $order = self::DEFAULT_ORDER)
	{
		if (!empty($text)) {
			$this->document->addScriptDeclaration($text);
		}
	}

	public function addStyle($file, $order = self::DEFAULT_ORDER)
	{
		if (!empty($file)) {
			$this->document->addStyleSheet($file);
		}
	}

	public function addInlineStyle($text, $order = self::DEFAULT_ORDER)
	{
		if (!empty($text)) {
			$this->document->addStyleDeclaration($text);

		}
	}

	public function addDomReadyScript($js, $order = self::DEFAULT_ORDER)
	{
		if (!empty($js)) {
			$this->document->addScriptDeclaration($js);
		}
	}

	public function addLoadScript($js, $order = self::DEFAULT_ORDER)
	{
		if (!empty($js)) {
			$this->document->addScriptDeclaration($js);
		}
	}
}
