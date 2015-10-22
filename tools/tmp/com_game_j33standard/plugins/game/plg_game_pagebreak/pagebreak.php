<?php
/**
 * @version 		$Id:$
 * @name			Game (Release 1.0.0)
 * @author			 ()
 * @package			com_game
 * @subpackage		com_game.pagebreak
 * @copyright		
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @CAversion		Id: pagebreak.php 408 2014-10-19 18:31:00Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.pagebreak
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

jimport('joomla.utilities.utility');

/**
 * Page break plugin
 *
 * <b>Usage:</b>
 * <code><hr class="system-pagebreak" /></code>
 * <code><hr class="system-pagebreak" title="The page title" /></code>
 * or
 * <code><hr class="system-pagebreak" alt="The first page" /></code>
 * or
 * <code><hr class="system-pagebreak" title="The page title" alt="The first page" /></code>
 * or
 * <code><hr class="system-pagebreak" alt="The first page" title="The page title" /></code>
 *
 */
class PlgGamePagebreak extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 */
	protected $autoloadLanguage = true;


	/**
	 * Plugin that adds a pagebreak into the text and truncates text at that point
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   object   &$row     The item object.  Note $item->text is also available
	 * @param   mixed    &$params  The item  params
	 * @param   integer  $page     The 'page' number
	 *
	 * @return  mixed  Always returns void or true
	 */
	public function onItemPrepare($context, &$row, &$params, $page = 0)
	{
		$can_proceed = $context == 'com_game.item';

		if (!$can_proceed)
		{
			return;
		}

		$style = $this->params->get('style', 'pages');

		// Expression to search for.
		$regex = '#<hr(.*)class="system-pagebreak"(.*)\/>#iU';

		$input = JFactory::getApplication()->input;

		$print = $input->getBool('print');
		$show_all = $input->getBool('showall');

		if (!$this->params->get('enabled', 1))
		{
			$print = true;
		}

		if ($print)
		{
			$row->description = preg_replace($regex, '<br />', $row->description);

			return true;
		}

		// Simple performance check to determine whether bot should process further.
		if (JString::strpos($row->description, 'class="system-pagebreak') === false)
		{
			return true;
		}

		$view = $input->getString('view');
		$full = $input->getBool('fullview');

		if (!$page)
		{
			$page = 0;
		}

		if ($params->get('intro_only') OR $params->get('popup') OR $full OR $view != 'item')
		{
			$row->description = preg_replace($regex, '', $row->description);

			return;
		}

		// Find all instances of plugin and put in $matches.
		$matches = array();
		preg_match_all($regex, $row->description, $matches, PREG_SET_ORDER);

		if (($show_all AND $this->params->get('showall', 1)))
		{
			$has_toc = $this->params->get('multipage_toc', 1);

			if ($has_toc)
			{
				// Display TOC.
				$page = 1;
				$this->_createItemTOC($row, $matches, $page);
			}
			else
			{
				$row->toc = '';
			}

			$row->description = preg_replace($regex, '<br />', $row->description);

			return true;
		}

		// Split the text around the plugin.
		$text = preg_split($regex, $row->description);

		// Count the number of pages.
		$n = count($text);

		// We have found at least one plugin, therefore at least 2 pages.
		if ($n > 1)
		{
			$title	= $this->params->get('title', 1);
			$has_toc = $this->params->get('multipage_toc', 1);

			// Adds heading or title to <site> Title.
			if ($title)
			{
				if ($page)
				{
					if ($page AND @$matches[$page - 1][2])
					{
						$attrs = JUtility::parseAttributes($matches[$page - 1][1]);

						if (@$attrs['title'])
						{
							$row->page_title = $attrs['title'];
						}
					}
				}
			}

			// Reset the text, we already hold it in the $text array.
			$row->description = '';

			if ($style == 'pages')
			{
				// Display TOC.
				if ($has_toc)
				{
					$this->_createItemTOC($row, $matches, $page);
				}
				else
				{
					$row->toc = '';
				}

				// Traditional mos page navigation
				$page_nav = new JPagination($n, $page, 1);

				// Page counter.
				$row->description .= '<div class="pagenavcounter">';
				$row->description .= $page_nav->getPagesCounter();
				$row->description .= '</div>';

				// Page text.
				$text[$page] = str_replace('<hr id="system-readmore" />', '', $text[$page]);
				$row->description .= $text[$page];

				// $row->description .= '<br />';
				$row->description .= '<div class="pager">';

				// Adds navigation between pages to bottom of text.
				if ($has_toc)
				{
					$this->_createItemNavigation($row, $page, $n);
				}

				// Page links shown at bottom of page if TOC disabled.
				if (!$has_toc)
				{
					$row->description .= $page_nav->getPagesLinks();
				}

				$row->description .= '</div>';
			}
			else
			{
				$t[] = $text[0];

				$t[] = (string) JHtml::_($style . '.start', 'item' . $row->id . '-' . $style);

				foreach ($text as $key => $subtext)
				{
					if ($key >= 1)
					{
						$match = $matches[$key - 1];
						$match = (array) JUtility::parseAttributes($match[0]);

						if (isset($match['alt']))
						{
							$title	= stripslashes($match['alt']);
						}
						elseif (isset($match['title']))
						{
							$title	= stripslashes($match['title']);
						}
						else
						{
							$title	= JText::sprintf('PLG_GAME_PAGEBREAK_PAGE_NUM', $key + 1);
						}

						$t[] = (string) JHtml::_($style . '.panel', $title, 'item' . $row->id . '-' . $style . $key);
					}

					$t[] = (string) $subtext;
				}

				$t[] = (string) JHtml::_($style . '.end');

				$row->description = implode(' ', $t);
			}
		}

		return true;
	}

	/**
	 * Creates a Table of Contents for the pagebreak
	 *
	 * @param   object   &$row      The item object.  Note $item->text is also available
	 * @param   array    &$matches  Array of matches of a regex in onItemPrepare
	 * @param   integer  &$page     The 'page' number
	 *
	 * @return  void
	 */
	protected function _createItemTOC(&$row, &$matches, &$page)
	{
		$heading = isset($row->name) ? $row->name : JText::_('PLG_GAME_PAGEBREAK_NO_TITLE');
		$input = JFactory::getApplication()->input;
		$limitstart = $input->getUInt('limitstart', 0);
		$show_all = $input->getInt('showall', 0);

		// TOC header.
		$row->toc = '<div class="pull-right item-index">';

		if ($this->params->get('item_index') == 1)
		{
			$headingtext = JText::_('PLG_GAME_PAGEBREAK_INDEX_LABEL');

			if ($this->params->get('item_index_text'))
			{
				htmlspecialchars($headingtext = $this->params->get('item_index_text'));
			}

			$row->toc .= '<h3>' . $headingtext . '</h3>';
		}

		// TOC first Page link.
		$class = ($limitstart === 0 AND $show_all === 0) ? 'toclink active' : 'toclink';
		$row->toc .= '<ul class="nav nav-tabs nav-stacked">
		<li class="' . $class . '">

			<a href="' . JRoute::_(GameHelperRoute::getItemRoute($row->slug, $row->catid) . '&showall=&limitstart=') . '" class="' . $class . '">'
			. $heading .
			'</a>

		</li>
		';

		$i = 2;

		foreach ($matches as $bot)
		{
			$link = JRoute::_(GameHelperRoute::getItemRoute($row->slug, $row->catid) . '&showall=&limitstart=' . ($i - 1));

			if (@$bot[0])
			{
				$attrs2 = JUtility::parseAttributes($bot[0]);

				if (@$attrs2['alt'])
				{
					$title	= stripslashes($attrs2['alt']);
				}
				elseif (@$attrs2['title'])
				{
					$title	= stripslashes($attrs2['title']);
				}
				else
				{
					$title	= JText::sprintf('PLG_GAME_PAGEBREAK_PAGE_NUM', $i);
				}
			}
			else
			{
				$title	= JText::sprintf('PLG_GAME_PAGEBREAK_PAGE_NUM', $i);
			}

			$class = ($limitstart == $i - 1) ? 'toclink active' : 'toclink';
			$row->toc .= '
				<li>

					<a href="' . $link . '" class="' . $class . '">'
					. $title .
					'</a>

				</li>
				';
			$i++;
		}

		if ($this->params->get('showall'))
		{
			$link = JRoute::_(GameHelperRoute::getItemRoute($row->slug, $row->catid) . '&showall=1&limitstart=');
			$class = ($show_all == 1) ? 'toclink active' : 'toclink';
			$row->toc .= '
			<li>

					<a href="' . $link . '" class="' . $class . '">'
					. JText::_('PLG_GAME_PAGEBREAK_ALL_PAGES') .
					'</a>

			</li>
			';
		}

		$row->toc .= '</ul></div>';
	}

	/**
	 * Creates the navigation for the item
	 *
	 * @param   object  &$row  The item object.  Note $item->text is also available
	 * @param   int     $page  The total number of pages
	 * @param   int     $n     The page number
	 *
	 * @return  void
	 */
	protected function _createItemNavigation(&$row, $page, $n)
	{
		$space = '';

		if (JText::_('JGLOBAL_LT') OR JText::_('JGLOBAL_LT'))
		{
			$space = ' ';
		}

		if ($page < $n - 1)
		{
			$page_next = $page + 1;

			$link_next = JRoute::_(GameHelperRoute::getItemRoute($row->slug, $row->catid) . '&showall=&limitstart=' . ($page_next));
			// Next >>
			$next = '<a href="' . $link_next . '">' . JText::_('JNEXT') . $space . JText::_('JGLOBAL_GT') . JText::_('JGLOBAL_GT') . '</a>';
		}
		else
		{
			$next = JText::_('JNEXT');
		}

		if ($page > 0)
		{
			$page_prev = $page - 1 == 0 ? '' : $page - 1;

			$link_prev = JRoute::_(GameHelperRoute::getItemRoute($row->slug, $row->catid) . '&showall=&limitstart=' . ($page_prev));

			// << Prev
			$prev = '<a href="' . $link_prev . '">' . JText::_('JGLOBAL_LT') . JText::_('JGLOBAL_LT') . $space . JText::_('JPREV') . '</a>';
		}
		else
		{
			$prev = JText::_('JPREV');
		}

		$row->description .= '<ul><li>' . $prev . ' </li><li>' . $next . '</li></ul>';
	}
}
