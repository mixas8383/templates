<?php
/**
 * @version   $Id: Zoo.php 14553 2013-10-16 20:47:04Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_Zoo extends RokSprocket_Provider_AbstarctZooBasedProvider
{
	protected static $available;

	/**
	 * @param array $filters
	 * @param array $sort_filters
	 */
	public function __construct($filters = array(), $sort_filters = array())
	{
		parent::__construct('zoo');
		require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');
		$this->setFilterChoices($filters, $sort_filters);
	}

	/**
	 * @static
	 * @return bool
	 */
	public static function isAvailable()
	{
		if (isset(self::$available)) {
			return self::$available;
		}

		if (!class_exists('JFactory')) {
			self::$available = false;
		} else {
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('a.extension_id');
			$query->from('#__extensions AS a');
			$query->where('a.type = "component"');
			$query->where('a.element = "com_zoo"');
			$query->where('a.enabled = 1');

			$db->setQuery($query);

			if ($db->loadResult()) {
				self::$available = true;
			} else {
				self::$available = false;
			}
		}
		return self::$available;

	}


	/**
	 * @param Item $zoo_item
	 * @param int $dborder
	 *
	 * @return \RokSprocket_Item
	 */
	protected function convertRawToItem($zoo_item, $dborder = 0)
	{
		$item     = new RokSprocket_Item();
		$zooapp   = App::getInstance('zoo');

		$zoo_item->app = $zooapp;
		if (isset($zoo_item->params) && !is_object($zoo_item->params)) $zoo_item->params = $zooapp->parameter->create($zoo_item->params);
		if (isset($zoo_item->elements) && !is_object($zoo_item->elements)) $zoo_item->elements = $zooapp->data->create($zoo_item->elements);

		$item->setProvider('rokface');
		$item->setId($zoo_item->id);
		$item->setTitle($zoo_item->name);
		$item->setDate($zoo_item->created);
		$item->setPublished(($zoo_item->state == 1) ? true : false);
		$primary_Category = $zoo_item->getPrimaryCategory();
		if(!is_null($primary_Category) && is_object($primary_Category)) $item->setCategory($primary_Category->name);
		$item->setMetaKey('');
		$item->setMetaDesc('');
		$item->setMetaData('');

		$texts  = array();
		$images = array();
		$links  = array();

		$elements = $zoo_item->getElements();
		foreach ($elements as $element) {
			/** @var RokSprocket_Provider_Zoo_FieldProcessorInterface $processor */
			$processor     = RokSprocket_Provider_Zoo_FieldProcessorFactory::getFieldProcessor($element->getElementType());
			$sprocket_type = RokSprocket_Provider_Zoo_FieldProcessorFactory::getSprocketType($element->getElementType());
			switch ($sprocket_type) {
				case 'image':
					if ($processor instanceof RokSprocket_Provider_Zoo_ImageFieldProcessorInterface) {
						/** @var RokSprocket_Provider_Zoo_ImageFieldProcessorInterface $processor */
						$image                           = $processor->getAsSprocketImage($element);
						$images[$image->getIdentifier()] = $image;
						if (isset($images['image_field_' . $element->identifier]) && !$item->getPrimaryImage()) {
							$item->setPrimaryImage($image);
						}
					}
					break;
				case 'link':
					if ($processor instanceof RokSprocket_Provider_Zoo_LinkFieldProcessorInterface) {
						/** @var RokSprocket_Provider_Zoo_LinkFieldProcessorInterface $processor */
						$link                          = $processor->getAsSprocketLink($element);
						$links[$link->getIdentifier()] = $link;
						if (isset($links['link_field_' . $element->identifier]) && !$item->getPrimaryLink()) {
							$item->setPrimaryLink($link);
						}
					}
					break;
				case 'text':
					/** @var RokSprocket_Provider_Zoo_FieldProcessorInterface $processor */
					$texts['text_field_' . $element->identifier] = $processor->getValue($element);
					break;
				default:
					break;
			}
		}


		$item->setImages($images);
		$item->setLinks($links);

		$params                       = RokCommon_JSON::decode($zoo_item->params);
		$desc                         = "metadata.description";
		$texts['text_field_metadesc'] = $params->$desc;
		$texts['text_field_name'] = $zoo_item->name;
		$texts                        = $this->processPlugins($texts);
		$item->setTextFields($texts);
		$text = array_values($texts);
		$text = array_shift($text);
		$item->setText($text);


		$primary_link = new RokSprocket_Item_Link();
		$primary_link->setUrl(JRoute::_('index.php?option=com_rokface&view=item&id=' . $item->getId(), true));
		$primary_link->getIdentifier('article_link');

		$item->setPrimaryLink($primary_link);

		$tags = $zoo_item->getTags();
		$item->setTags($tags);

		$item->setDbOrder($dborder);

		return $item;
	}
}

