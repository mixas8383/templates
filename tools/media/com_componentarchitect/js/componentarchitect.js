/**
 * @version 		$Id: componentarchitect.js 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */
 //[%%START_CUSTOM_CODE%%]
 jQuery(document).ready(function()
{
    bindListeners();
});
 function bindListeners() {
     jQuery('#jform_name').blur
     (
         function()
        {
            var name = jQuery('#jform_name').val();
            if (jQuery('input#jform_alias').length > 0)
            {
                if (jQuery('input#jform_alias').val() == '' || jQuery('input#jform_id').val() == 0)
                {
                    var alias = generateAlias(name);
                    jQuery('input#jform_alias').val(alias);
                }
            }            
            if (jQuery('input#jform_code_name').length > 0)
            {
                if (jQuery('input#jform_code_name').val() == '' || jQuery('input#jform_id').val() == 0)
                {
                    var code_name = generateCodeName(name);
                    jQuery('input#jform_code_name').val(code_name);
                }
            }
            if (jQuery('input#jform_plural_name').length > 0)
            {
                if (jQuery('input#jform_plural_name').val() == '' || jQuery('input#jform_id').val() == 0)
                {
                    var plural_name = generatePluralName(name);
                    jQuery('input#jform_plural_name').val(plural_name);
                }
            }
            if (jQuery('input#jform_plural_code_name').length > 0)
            {
                if (jQuery('input#jform_plural_code_name').val() == '' || jQuery('input#jform_id').val() == 0)
                {
                    var plural_code_name = generateCodeName(generatePluralName(name));
                    jQuery('input#jform_plural_code_name').val(plural_code_name);
                }                
            }
            if (jQuery('input#jform_short_name').length > 0)
            {
                if (jQuery('input#jform_short_name').val() == '' || jQuery('input#jform_id').val() == 0)
                {
                    var short_name = generateShortName(name);
                    jQuery('input#jform_short_name').val(short_name);
                }
            }
            if (jQuery('input#jform_short_plural_name').length > 0)
            {
                if (jQuery('input#jform_short_plural_name').val() == '' || jQuery('input#jform_id').val() == 0)
                {
                    var short_plural_name = generateShortName(generatePluralName(name));
                    jQuery('input#jform_short_plural_name').val(short_plural_name);
                }
            }                                    
        }
     );
 }
function generateAlias(name)
{
    var alias = name.replace(/[\.,-\/#!$%\^&\*;:{}=\-\"_'`~()]/g,"");
    alias = alias.replace(/\s{2,}/g," ");  
    alias = alias.replace(/\s/g,"-");  

    return alias.toLowerCase();
}
function generateCodeName(name)
{
    var code_name = name.replace(/[\.,-\/#!$%\^&\*;:{}=\-\"_'`~()]/g,"");
    code_name = code_name.replace(/\s{2,}/g," ");  
    code_name = code_name.replace(/\s/g,"_");  
    code_name = code_name.replace(/^[\d]*/,"");  
    if (code_name.charAt(0)=='_')
    {
        code_name = code_name.substr(1);
    }
    
    return code_name.toLowerCase();
}
function generatePluralName(name)
{
    // Only want to have last word in the name to make it plural
    var strArr = name.split(' ')

    var last_word = strArr[ strArr.length - 1 ];
    
    strArr[ strArr.length - 1 ] = owl.pluralise(last_word);
    
    plural_name = strArr.join(' ');
    
    plural_name = plural_name.replace(/[\.,-\/#!$%\^&\*;:{}=\-\"_'`~()]/g,"");

    return plural_name;
}
function generateShortName(name)
{
    var strArr = name.split(' ')

    var short_name = strArr[ strArr.length - 1 ];

    short_name = short_name.replace(/[\.,-\/#!$%\^&\*;:{}=\-\"_'`~()]/g,"");

    return short_name;
}

// This code is part of OWL Pluralisation.

// prepare the owl namespace.
if ( typeof owl === 'undefined' ) owl = {};

owl.pluralise = (function() {
	var userDefined = {};

	function capitaliseSame(word, sampleWord) {
		if ( sampleWord.match(/^[A-Z]/) ) {
			return word.charAt(0).toUpperCase() + word.slice(1);
		} else {
			return word;
		}
	}

	// returns a plain Object having the given keys,
	// all with value 1, which can be used for fast lookups.
	function toKeys(keys) {
		keys = keys.split(',');
		var keysLength = keys.length;
		var table = {};
		for ( var i=0; i < keysLength; i++ ) {
			table[ keys[i] ] = 1;
		}
		return table;
	}

	// words that are always singular, always plural, or the same in both forms.
	var uninflected = toKeys("aircraft,advice,blues,corn,molasses,equipment,gold,information,cotton,jewelry,kin,legislation,luck,luggage,moose,music,offspring,rice,silver,trousers,wheat,bison,bream,breeches,britches,carp,chassis,clippers,cod,contretemps,corps,debris,diabetes,djinn,eland,elk,flounder,gallows,graffiti,headquarters,herpes,high,homework,innings,jackanapes,mackerel,measles,mews,mumps,news,pincers,pliers,proceedings,rabies,salmon,scissors,sea,series,shears,species,swine,trout,tuna,whiting,wildebeest,pike,oats,tongs,dregs,snuffers,victuals,tweezers,vespers,pinchers,bellows,cattle");

	var irregular = {
		// pronouns
		I: 'we',
		you: 'you',
		he: 'they',
		it: 'they',  // or them
		me: 'us',
		you: 'you',
		him: 'them',
		them: 'them',
		myself: 'ourselves',
		yourself: 'yourselves',
		himself: 'themselves',
		herself: 'themselves',
		itself: 'themselves',
		themself: 'themselves',
		oneself: 'oneselves',

		child: 'children',
		dwarf: 'dwarfs',  // dwarfs are real; dwarves are fantasy.
		mongoose: 'mongooses',
		mythos: 'mythoi',
		ox: 'oxen',
		soliloquy: 'soliloquies',
		trilby: 'trilbys',
		person: 'people',
		forum: 'forums', // fora is ok but uncommon.

		// latin plural in popular usage.
		syllabus: 'syllabi',
		alumnus: 'alumni', 
		genus: 'genera',
		viscus: 'viscera',
		stigma: 'stigmata'
	};

	var suffixRules = [
		// common suffixes
		[ /man$/i, 'men' ],
		[ /([lm])ouse$/i, '$1ice' ],
		[ /tooth$/i, 'teeth' ],
		[ /goose$/i, 'geese' ],
		[ /foot$/i, 'feet' ],
		[ /zoon$/i, 'zoa' ],
		[ /([tcsx])is$/i, '$1es' ],

		// fully assimilated suffixes
		[ /ix$/i, 'ices' ],
		[ /^(cod|mur|sil|vert)ex$/i, '$1ices' ],
		[ /^(agend|addend|memorand|millenni|dat|extrem|bacteri|desiderat|strat|candelabr|errat|ov|symposi)um$/i, '$1a' ],
		[ /^(apheli|hyperbat|periheli|asyndet|noumen|phenomen|criteri|organ|prolegomen|\w+hedr)on$/i, '$1a' ],
		[ /^(alumn|alg|vertebr)a$/i, '$1ae' ],
		
		// churches, classes, boxes, etc.
		[ /([cs]h|ss|x)$/i, '$1es' ],

		// words with -ves plural form
		[ /([aeo]l|[^d]ea|ar)f$/i, '$1ves' ],
		[ /([nlw]i)fe$/i, '$1ves' ],

		// -y
		[ /([aeiou])y$/i, '$1ys' ],
		[ /y$/i, 'ies' ],

		// -o
		[ /([aeiou])o$/i, '$1os' ],
		[ /^(pian|portic|albin|generalissim|manifest|archipelag|ghett|medic|armadill|guan|octav|command|infern|phot|ditt|jumb|pr|dynam|ling|quart|embry|lumbag|rhin|fiasc|magnet|styl|alt|contralt|sopran|bass|crescend|temp|cant|sol|kimon)o$/i, '$1os' ],
		[ /o$/i, 'oes' ],

		// words ending in s...
		[ /s$/i, 'ses' ]
	];

	// pluralises the given singular noun.  There are three ways to call it:
	//   pluralise(noun) -> pluralNoun
	//     Returns the plural of the given noun.
	//   Example: 
	//     pluralise("person") -> "people"
	//     pluralise("me") -> "us"
	//
	//   pluralise(noun, count) -> plural or singular noun
	//   Inflect the noun according to the count, returning the singular noun
	//   if the count is 1.
	//   Examples:
	//     pluralise("person", 3) -> "people"
	//     pluralise("person", 1) -> "person"
	//     pluralise("person", 0) -> "people"
	//
	//   pluralise(noun, count, plural) -> plural or singular noun
	//   you can provide an irregular plural yourself as the 3rd argument.
	//   Example:
	//     pluralise("château", 2 "châteaux") -> "châteaux"
	function pluralise(word) {
		// handle the empty string reasonably.
		if ( word === '' ) return '';

		var lowerWord = word.toLowerCase();

		// user defined rules have the highest priority.
		if ( lowerWord in userDefined ) {
			return capitaliseSame(userDefined[lowerWord], word);
		}

		// single letters are pluralised with 's, "I got five A's on
		// my report card."
		if ( word.match(/^[A-Z]$/) ) return word + "'s";

		// some word don't change form when plural.
		if ( word.match(/fish$|ois$|sheep$|deer$|pox$|itis$/i) ) return word;
		if ( word.match(/^[A-Z][a-z]*ese$/) ) return word;  // Nationalities.
		if ( lowerWord in uninflected ) return word;

		// there's a known set of words with irregular plural forms.
		if ( lowerWord in irregular ) {
			return capitaliseSame(irregular[lowerWord], word);
		}
		
		// try to pluralise the word depending on its suffix.
		var suffixRulesLength = suffixRules.length;
		for ( var i=0; i < suffixRulesLength; i++ ) {
			var rule = suffixRules[i];
			if ( word.match(rule[0]) ) {
				return word.replace(rule[0], rule[1]);
			}
		}

		// if all else fails, just add s.
		return word + 's';
	}

	pluralise.define = function(word, plural) {
		userDefined[word.toLowerCase()] = plural;
	}

	return pluralise;

})();
 //[%%END_CUSTOM_CODE%%]

