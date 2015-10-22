/**
 * @version 		$Id: componentarchitectvalidate.js 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_componentarchitect
 * @subpackage		com_componentarchitect.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 *
 * @CAversion		Id: architectcompvalidate.js 747 2013-11-20 09:19:47Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.admin
 * @CAtemplate		joomla_3_x_enhanced (Release 1.0.0)
 * @CAcopyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @Joomlacopyright Copyright (c)2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @CAlicense		GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */
 
window.addEvent('domready', function() {
	document.formvalidator.setHandler('webaddress',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentcodename',
		function (value) {
			regex=/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componenticon16px',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componenticon48px',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentcategoriesicon16px',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentcategoriesicon48px',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgenerateadminviews',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgenerateadminhelp',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratesiteviews',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratesitelayoutarticle',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratesitelayoutblog',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratecategoriessiteviewscategories',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratecategoriessiteviewscategory',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratepluginssearch',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratepluginsfinder',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratepluginsitemnavigation',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratepluginsvote',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratepluginspagebreak',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentgeneratepluginsevents',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentincludealias',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentincludeassetaclrecord',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentincludeintro',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectpluralname',
		function (value) {
			regex=/^[0-9\s\\/\-_+&().]*[a-zA-Z][a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectcodename',
		function (value) {
			regex=/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectpluralcodename',
		function (value) {
			regex=/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectshortname',
		function (value) {
			regex=/[a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectshortpluralname',
		function (value) {
			regex=/[a-zA-Z0-9][a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjecticon16px',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjecticon48px',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgenerateadminviews',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgenerateadminhelp',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratesiteviews',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratesitelayoutarticle',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratesitelayoutblog',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratecategoriessiteviewscategories',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratecategoriessiteviewscategory',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratepluginssearch',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratepluginsfinder',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratepluginsitemnavigation',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratepluginsvote',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratepluginspagebreak',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectgeneratepluginsevents',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectincludealias',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectincludeassetaclrecord',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('componentobjectincludeintro',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('fieldsetcodename',
		function (value) {
			regex=/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('fieldcodename',
		function (value) {
			regex=/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('fieldmysqldefault',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('fieldtypecodename',
		function (value) {
			regex=/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('fieldtypemysqldefaultdefault',
		function (value) {
			regex=/.*/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('codetemplatetemplatecomponentname',
		function (value) {
			regex=/^[0-9\s]*[a-zA-Z][a-zA-Z0-9\s]+$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('codetemplatetemplateobjectname',
		function (value) {
			regex=/^[0-9\s]*[a-zA-Z][a-zA-Z0-9\s]+$/;
			return regex.test(value);
	});
	//[%%START_CUSTOM_CODE%%]
	document.formvalidator.setHandler('componentname',
		function (value) {
			regex=/^[0-9\s\\/\-_+&().]*[a-zA-Z][a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});	
	document.formvalidator.setHandler('componentobjectname',
		function (value) {
			regex=/^[0-9\s\\/\-_+&().]*[a-zA-Z][a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});
	document.formvalidator.setHandler('fieldsetname',
		function (value) {
			regex=/^[0-9\s\\/\-_+&().]*[a-zA-Z][a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});	
	document.formvalidator.setHandler('fieldname',
		function (value) {
			regex=/^[0-9\s\\/\-_+&().]*[a-zA-Z][a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});	
	document.formvalidator.setHandler('codetemplatename',
		function (value) {
			regex=/^[0-9\s\\/\-_+&().]*[a-zA-Z][a-zA-Z0-9\s\\/\-_+&().]+$/;
			return regex.test(value);
	});		
	//[%%END_CUSTOM_CODE%%]		
});
