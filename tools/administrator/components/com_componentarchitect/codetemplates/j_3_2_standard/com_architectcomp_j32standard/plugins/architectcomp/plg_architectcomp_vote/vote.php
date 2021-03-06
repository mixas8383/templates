<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].vote
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: vote.php 417 2014-10-22 14:42:10Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.vote
 * @CAtemplate		joomla_3_2_standard (Release 1.0.4)
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
 *
 * [%%ArchitectComp%%] vote plugin class.
 */
class Plg[%%ArchitectComp%%]Vote extends JPlugin
{
	/**
	 * @var    $autoloadLanguage boolean	Load the language file on instantiation
	 */
	protected $autoloadLanguage = true;

[%%FOREACH COMPONENT_OBJECT%%]
	[%%IF GENERATE_PLUGINS%%]
		[%%IF GENERATE_PLUGINS_VOTE%%]
	/**
	 * On Before Display event procedure for Voting
	 * 
	 * @param	string			$context	Context of the paging
	 * @param	array			&$row		Passed by reference and row updated with html for prev and/or next buttons
	 * @param	json/registry	&$params	Item navigation parameters	
	 * @param	integer			$page		Current Item page		
	 * 
	 * @return  string			$html		HTML to be output
	 */			
	public function on[%%CompObject%%]BeforeDisplay($context, &$row, &$params, $page=0)
	{
		$parts = explode(".", $context);
		if ($parts[0] != '[%%com_architectcomp%%]')
		{
			return false;
		}	
		$html = '';

		if (!empty($params) AND $params->get('show_[%%compobject%%]_vote', null) AND $this->params->get('[%%compobject%%]_position', '1') == '0')
		{
			$html = $this->OutputRating($context, $row, $params, $page=0,'[%%compobject%%]');
		}

		return $html;
	}
	/**
	 * On After Display event procedure for Voting
	 * 
	 * @param	string			$context	Context of the paging
	 * @param	array			&$row		Passed by reference and row updated with html for prev and/or next buttons
	 * @param	json/registry	&$params	Item navigation parameters	
	 * @param	integer			$page		Current Item page		
	 * 
	 * @return  string			$html		HTML to be output
	 */		
	public function on[%%CompObject%%]AfterDisplay($context, &$row, &$params, $page=0)
	{
		$parts = explode(".", $context);
		if ($parts[0] != '[%%com_architectcomp%%]')
		{
			return false;
		}	
		$html = '';

		if (!empty($params) AND $params->get('show_[%%compobject%%]_vote') AND $this->params->get('[%%compobject%%]_position', '1') == '1')
		{
			$html = $this->OutputRating($context, $row, $params, $page=0,'[%%compobject%%]');
		}

		return $html;
	}	
		[%%ENDIF GENERATE_PLUGINS_VOTE%%]
	[%%ENDIF GENERATE_PLUGINS%%]
[%%ENDFOR COMPONENT_OBJECT%%]
	/**
	 * Create the rating information to be output
	 * 
	 * @param	string			$context	Context of the paging
	 * @param	array			&$row		Passed by reference and row updated with html for prev and/or next buttons
	 * @param	json/registry	&$params	Item navigation parameters	
	 * @param	integer			$page		Current Item page		
	 * @param	string			$object		Object name		
	 * 
	 * @return  string			$html		HTML to be output
	 */	
	protected function OutputRating($context, &$row, &$params, $page=0, $object)
	{
		$html = '';
		$app = JFactory::getApplication();
				
		if ($object <> '')
		{
			$rating = (int) @$row->rating;
			$rating_count =  (int) @$row->rating_count;

			$view = $app->input->getString('view', '');
			$img = '';

			// look for images in template if available
			$star_image_on = JHtml::_('image', 'system/rating_star.png', JText::_('PLG_[%%ARCHITECTCOMP%%]_VOTE_STAR_ACTIVE'), null, true);
			$star_image_off = JHtml::_('image', 'system/rating_star_blank.png', JText::_('PLG_[%%ARCHITECTCOMP%%]_VOTE_STAR_INACTIVE'), null, true);

			for ($i=0; $i < $rating; $i++)
			{
				$img .= $star_image_on;
			}
			for ($i=$rating; $i < 5; $i++)
			{
				$img .= $star_image_off;
			}
			$html .= '<div class="'.$object.'_rating">';
			$html .= '<p class="unseen element-invisible">'.JText::sprintf( 'PLG_[%%ARCHITECTCOMP%%]_VOTE_USER_RATING', $rating, $rating_count ).'</p>';
			$html .= $img;
			$html .= "</div>\n";

			[%%IF INCLUDE_STATUS%%]
			if ( ($view == $object) AND isset($row->state) AND $row->state == 1)
			[%%ELSE INCLUDE_STATUS%%]	
			if ( ($view == $object))
			[%%ENDIF INCLUDE_STATUS%%]	
			{
				$uri = JUri::getInstance();
				$uri->setQuery($uri->getQuery().'&hitcount=0');
				
				// create option list for voting select box
				$options = array();
				for($i = 1; $i < 6; $i++)
				{
					$options[] = JHTML::_('select.option', $i, JText::sprintf('PLG_[%%ARCHITECTCOMP%%]_VOTE_VOTE', $i));
				}

				// generate voting form
				$html .= '<form method="post" action="' . htmlspecialchars($uri->toString()) . '" class="form-inline">';
				$html .= '<span class="'.$object.'_vote">';
				$html .= '<label class="unseen element-invisible" for="'.$object.'_vote_' . $row->id . '">'.JText::_('PLG_[%%ARCHITECTCOMP%%]_VOTE_LABEL').'</label>';
				$html .= JHTML::_('select.genericlist', $options, 'user_rating', null, 'value', 'text', '5', $object.'_vote_'.$row->id);
				$html .= '&#160;<input class="btn btn-mini" type="submit" name="submit_vote" value="' . JText::_('PLG_[%%ARCHITECTCOMP%%]_VOTE_RATE') . '" />';
				$html .= '<input type="hidden" name="task" value="'.$object.'.vote" />';
				$html .= '<input type="hidden" name="hitcount" value="0" />';
				$html .= '<input type="hidden" name="url" value="' . htmlspecialchars($uri->toString()) . '" />';
				$html .= JHtml::_('form.token');
				$html .= '</span>';
				$html .= '</form>';				
			}
		}
		return $html;
	}
}
