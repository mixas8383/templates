/**
 * @version 		$Id: generate.js 411 2014-10-19 18:39:07Z BrianWade $
 * @name			Component Architect (Release 1.1.3)
 * @author			Component Architect (www.componentarchitect.com)
 * @package			com_modcreator
 * @subpackage		com_modcreator.admin
 * @copyright		Copyright (c)2013 - 2014 Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */
//=============================================================================
// Generate component ajax progress functions
//=============================================================================

/** @var The AJAX proxy URL */
var generate_ajax_url = "";
        /** @var The callback function to call on error */
        var generate_error_callback = dummy_error_handler;
        var completion_processed = false;
        /** @var An array for transfer of Joomla! translation text*/
        var generate_translations = new Array();
        generate_translations['GENERATE-SUCCESS'] = "The component '%s' was successfully generated using code template '%s'.";
        generate_translations['GENERATE-SUCCESS-PATH'] = "The component's files have been placed in directory: %s";
        generate_translations['GENERATE-SUCCESS-ZIP'] = "The requested zip file has been created and can be downloaded using the Download button below.<br />Or copy the following direct link and save it to use in the Joomla! Extensions/Install.  Direct link: '%s'";
        generate_translations['GENERATE-DOWNLOAD-ZIP'] = "<a href='%s' alt='Download the generated zip file'><button type='button' class='btn btn-large btn-info'>Download</button></a>";
        generate_translations['GENERATE-FAILED'] = "There was a problem generating component '%s' using code template '%s'.  The error(s) detected are shown below.  You can also view the log file to see more details or if no log file was specified re-run the generation with log file output set.";
        generate_translations['GENERATE-ERROR-GENERAL'] = "An error has occured whilst generating the component:";
        generate_translations['GENERATE-ERROR-AJAX-LOADING'] = "<strong>AJAX Loading Error</strong>";
        generate_translations['GENERATE-ERROR-AJAX-INVALID-DATA'] = "Invalid AJAX data: ";
        /**
         * Performs an AJAX request and returns the parsed JSON output.
         * The global generate_ajax_url is used as the AJAX proxy URL.
         * If there is no errorCallback, the global generate_error_callback is used.
         * @param data An object with the query data, e.g. a serialized form
         * @param ajax_url A string giving the url to call for this ajax command
         * @param successCallback A function accepting a single object parameter, called on success
         * @param errorCallback A function accepting a single string parameter, called on failure
         */
                function doGenerateAjax(data_object, ajax_url, successCallback, errorCallback, generate_timeout)
                {
                if (generate_timeout == null) generate_timeout = 600000;
                        (function($) {
                        var generate_structure =
                        {
                        type: "POST",
                                url: ajax_url,
                                cache: false,
                                data: data_object,
                                timeout: generate_timeout,
                                success: function(msg) {
                                // Initialize
                                var junk = null;
                                        var message = "";
                                        // Get rid of junk before the data
                                        var valid_pos = msg.indexOf('###');
                                        if (valid_pos == - 1) {
                                // Valid data not found in the response
                                msg = generate_translations['GENERATE-ERROR-AJAX-INVALID-DATA'] + msg;
                                        if (errorCallback == null)
                                {
                                if (generate_error_callback != null)
                                {
                                generate_error_callback(msg);
                                }
                                }
                                else
                                {
                                errorCallback(msg);
                                }
                                return;
                                } else if (valid_pos != 0) {
                                // Data is prefixed with junk
                                junk = msg.substr(0, valid_pos);
                                        message = msg.substr(valid_pos);
                                }
                                else
                                {
                                message = msg;
                                }
                                message = message.substr(3); // Remove triple hash in the beginning

                                        // Get of rid of junk after the data
                                        valid_pos = message.lastIndexOf('###');
                                        message = message.substr(0, valid_pos); // Remove triple hash in the end

                                        try {
                                        var data = JSON && JSON.parse(message) || $.parseJSON(message);
                                        } catch (err) {
                                var err_msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
                                        if (errorCallback == null)
                                {
                                if (generate_error_callback != null)
                                {
                                generate_error_callback(err_msg);
                                }
                                }
                                else
                                {
                                errorCallback(msg);
                                }
                                return;
                                }

                                // Call the callback function
                                successCallback(data);
                                },
                                error: function(Request, textStatus, errorThrown) {
                                var message = generate_translations['GENERATE-ERROR-AJAX-LOADING'] + '<br/>';
                                        message = message + 'TTP Status:' + Request.status + ' (' + Request.statusText + ')<br/>';
                                        message = message + 'Internal status: ' + textStatus + '<br/>';
                                        message = message + 'XHR ReadyState: ' + Request.readyState + '<br/>';
                                        message = message + 'Raw server response:<br/>' + Request.responseText;
                                        if (errorCallback == null)
                                {
                                if (generate_error_callback != null)
                                {
                                generate_error_callback(message);
                                }
                                }
                                else
                                {
                                errorCallback(message);
                                }
                                }
                        };
                                $.ajax(generate_structure);
                        }) (jQuery);
                }
        /**
         * Performs an AJAX request and returns the parsed JSON output.
         * The global generate_ajax_url is used as the AJAX proxy URL.
         * If there is no errorCallback, the global generate_error_callback is used.
         * @param data An object with the query data, e.g. a serialized form
         * @param ajax_url A string giving the url to call for this ajax command
         * @param successCallback A function accepting a single object parameter, called on success
         * @param errorCallback A function accepting a single string parameter, called on failure
         */
        function doProgressAjax(data_object, ajax_url, successCallback, errorCallback, progress_timeout)
        {
        if (progress_timeout == null) progress_timeout = 10000;
                (function($) {
                var progress_structure =
                {
                type: "POST",
                        url: ajax_url,
                        cache: false,
                        data: data_object,
                        timeout: progress_timeout,
                        success: function(msg) {
                        // Initialize
                        var junk = null;
                                var message = "";
                                // Get rid of junk before the data
                                var valid_pos = msg.indexOf('###');
                                if (valid_pos == - 1) {
                        // Valid data not found in the response
                        msg = generate_translations['GENERATE-ERROR-AJAX-INVALID-DATA'] + msg;
                                if (errorCallback == null)
                        {
                        if (generate_error_callback != null)
                        {
                        generate_error_callback(msg);
                        }
                        }
                        else
                        {
                        errorCallback(msg);
                        }
                        return;
                        } else if (valid_pos != 0) {
                        // Data is prefixed with junk
                        junk = msg.substr(0, valid_pos);
                                message = msg.substr(valid_pos);
                        }
                        else
                        {
                        message = msg;
                        }
                        message = message.substr(3); // Remove triple hash in the beginning

                                // Get of rid of junk after the data
                                valid_pos = message.lastIndexOf('###');
                                message = message.substr(0, valid_pos); // Remove triple hash in the end
                                if (message == "[]" && !completion_processed)
                        {
                        // Ignore null message and reset timer
                        set_ajax_timer();
                        }
                        else
                        {
                        // Only process message if the generate-complete function has not been run yet
                        if (!completion_processed)
                        {
                        try {
                        var data = JSON && JSON.parse(message) || $.parseJSON(message);
                        } catch (err) {
                        var err_msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
                                if (errorCallback == null)
                        {
                        if (generate_error_callback != null)
                        {
                        generate_error_callback(err_msg);
                        }
                        }
                        else
                        {
                        errorCallback(msg);
                        }
                        return;
                        }

                        // Call the callback function
                        successCallback(data);
                        }
                        }
                        },
                        error: function(Request, textStatus, errorThrown) {
                        var message = generate_translations['GENERATE-ERROR-AJAX-LOADING'] + '<br/>';
                                message = message + 'TTP Status:' + Request.status + ' (' + Request.statusText + ')<br/>';
                                message = message + 'Internal status: ' + textStatus + '<br/>';
                                message = message + 'XHR ReadyState: ' + Request.readyState + '<br/>';
                                message = message + 'Raw server response:<br/>' + Request.responseText;
                                if (errorCallback == null)
                        {
                        if (generate_error_callback != null)
                        {
                        generate_error_callback(message);
                        }
                        }
                        else
                        {
                        errorCallback(message);
                        }
                        }
                };
                        $.ajax(progress_structure);
                }) (jQuery);
        }
        function set_ajax_timer()
        {
        setTimeout('generate_ajax_timer_tick()', 200);
        }

        function generate_ajax_timer_tick()
        {
        (function($){
        progress_ajax_url = 'index.php?option=com_modcreator&task=generateprogress.progress';
                // Data to send to AJAX
                var dataObj = {};
                dataObj['ajax'] = 'step';
                var token_name = $('#token > input[type=hidden]').attr("name");
                dataObj['token'] = token_name;
                dataObj[token_name] = $('input[name=' + token_name + ']').val();
                doProgressAjax(dataObj, progress_ajax_url, generate_step, generate_error, null);
        }) (jQuery);
        }

        function generate_start()
        {
        var isValid = true;
                var forms = $$('form.form-validate');
                for (var i = 0; i < forms.length; i++)
        {
        if (!document.formvalidator.isValid(forms[i]))
        {
        isValid = false;
                break;
        }
        }

        if (!isValid)
        {
        alert(Joomla.JText._('COM_COMPONENTARCHITECT_GENERATE_ERROR_ON_FORM',
                'Both a Component/Extension and a Code Template must be selected.'));
                return false;
        }

        (function($){
        // Hide the generate setup
        $('#generate-instructions').hide("fast");
                $('#generate-setup').hide("fast");
                // Show the generate progress
                $('#generate-advice').show("fast");
                $('#generate-progress').show("fast");
                $('#generate-step-stage-1').html('');
                percentage_stage_1 = '0%';
                $('#generate-percentage-stage-1 div.progress-text').html('');
                $('#generate-percentage-stage-1 div.progress-bar').css({
        'width':			percentage_stage_1,
                'background-color':	'#99D100'
        });
                $('#generate-step-stage-2').html('');
                percentage_stage_2 = '0%';
                $('#generate-percentage-stage-2 div.progress-text').html('');
                $('#generate-percentage-stage-2 div.progress-bar').css({
        'width':			percentage_stage_2,
                'background-color':	'#99D100'
        });
                $('#generate-step-stage-3').html('');
                percentage_stage_3 = '0%';
                $('#generate-percentage-stage-3 div.progress-text').html('');
                $('#generate-percentage-stage-3 div.progress-bar').css({
        'width':			percentage_stage_3,
                'background-color':	'#99D100'
        });
                completion_processed = false;
                generate_ajax_url = 'index.php?option=com_modcreator&task=generatedialog.generate';
                // Data to send to AJAX
                var dataObj = {};
                var token_name = $('#token > input[type=hidden]').attr("name");
                dataObj['token'] = token_name;
                dataObj[token_name] = $('input[name=' + token_name + ']').val();
                dataObj['component_id'] = $('#jform_component_id_id').val();
                dataObj['code_template_id'] = $('#jform_code_template_id_id').val();
                dataObj['output_path'] = $('#jform_output_path').val();
                dataObj['zip_format'] = $('#jform_zip_format').val();
                dataObj['logging'] = $('#jform_logging').val();
                dataObj['description'] = $('#jform_description').val();
                // ...and set ajax timer
                set_ajax_timer();
                doGenerateAjax(dataObj, generate_ajax_url, generate_complete, generate_error);
        }) (jQuery);
        }

       

        function generate_module()
        {
        var isValid = true;
                var forms = $$('form.form-validate');
                for (var i = 0; i < forms.length; i++)
        {
        if (!document.formvalidator.isValid(forms[i]))
        {
        isValid = false;
                break;
        }
        }

        if (!isValid)
        {
        alert(Joomla.JText._('COM_COMPONENTARCHITECT_GENERATE_ERROR_ON_FORM',
                'Both a Component/Extension and a Code Template must be selected.'));
                return false;
        }

        (function($){
        // Hide the generate setup
        $('#generate-instructions').hide("fast");
                $('#generate-setup').hide("fast");
                // Show the generate progress
                $('#generate-advice').show("fast");
                $('#generate-progress').show("fast");
                $('#generate-step-stage-1').html('');
                percentage_stage_1 = '0%';
                $('#generate-percentage-stage-1 div.progress-text').html('');
                $('#generate-percentage-stage-1 div.progress-bar').css({
        'width':			percentage_stage_1,
                'background-color':	'#99D100'
        });
                $('#generate-step-stage-2').html('');
                percentage_stage_2 = '0%';
                $('#generate-percentage-stage-2 div.progress-text').html('');
                $('#generate-percentage-stage-2 div.progress-bar').css({
        'width':			percentage_stage_2,
                'background-color':	'#99D100'
        });
                $('#generate-step-stage-3').html('');
                percentage_stage_3 = '0%';
                $('#generate-percentage-stage-3 div.progress-text').html('');
                $('#generate-percentage-stage-3 div.progress-bar').css({
        'width':			percentage_stage_3,
                'background-color':	'#99D100'
        });
                completion_processed = false;
                generate_ajax_url = 'index.php?option=com_modcreator&task=generatedialog.generateModule';
                // Data to send to AJAX
                var dataObj = {};
                var token_name = $('#token > input[type=hidden]').attr("name");
                dataObj['token'] = token_name;
                dataObj[token_name] = $('input[name=' + token_name + ']').val();
                dataObj['component_id'] = $('#jform_component_id_id').val();
                dataObj['code_template_id'] = $('#jform_code_template_id_id').val();
                dataObj['output_path'] = $('#jform_output_path').val();
                dataObj['zip_format'] = $('#jform_zip_format').val();
                dataObj['module_name'] = $('#jform_module_name').val();
                dataObj['logging'] = $('#jform_logging').val();
                dataObj['description'] = $('#jform_description').val();
                // ...and set ajax timer
                set_ajax_timer();
                doGenerateAjax(dataObj, generate_ajax_url, generate_complete, generate_error);
        }) (jQuery);
        }

        function generate_step(data)
        {
        // Update visual step progress from active domain data
        (function($){
        // Do we have errors?
        var error_message = data.completion.error;
                if (error_message != '')
        {
        // Uh-oh! An error has occurred.
        generate_error(error_message);
                if (parseInt(data.completion.is_complete) == 1)
        {
        return;
        }
        else
        {
        // Ensure all session data is cleared up
        (function($){
        progress_ajax_url = 'index.php?option=com_modcreator&task=generateprogress.progress';
                // Data to send to AJAX
                var dataObj = {};
                dataObj['ajax'] = 'complete';
                var token_name = $('#token > input[type=hidden]').attr("name");
                dataObj['token'] = token_name;
                dataObj[token_name] = $('input[name=' + token_name + ']').val();
                doAjax(dataObj, progress_ajax_url, generate_complete, generate_error);
        }) (jQuery);
        }
        }
        else
        {
        // Update percentage display for current stage
        var percentageText = 0;
                if (data.stage_1.total_count > 0)
        {
        $('#generate-step-stage-1').html(data.stage_1.current_step);
                percentage_stage_1 = Math.round((parseInt(data.stage_1.current_count) / parseInt(data.stage_1.total_count)) * 100) + '%';
                $('#generate-percentage-stage-1 div.progress-text').html(percentage_stage_1);
                $('#generate-percentage-stage-1 div.progress-bar').css({
        'width':			percentage_stage_1,
                'background-color':	'#99D100'
        });
        }

        if (data.stage_2.total_count > 0)
        {
        $('#generate-step-stage-2').html(data.stage_2.current_step);
                percentage_stage_2 = Math.round((parseInt(data.stage_2.current_count) / parseInt(data.stage_2.total_count)) * 100) + '%';
                $('#generate-percentage-stage-2 div.progress-text').html(percentage_stage_2);
                $('#generate-percentage-stage-2 div.progress-bar').css({
        'width':			percentage_stage_2,
                'background-color':	'#99D100'
        });
        }

        if (data.stage_3.total_count > 0)
        {
        $('#generate-step-stage-3').html(data.stage_3.current_step);
                percentage_stage_3 = Math.round((parseInt(data.stage_3.current_count) / parseInt(data.stage_3.total_count)) * 100) + '%';
                $('#generate-percentage-stage-3 div.progress-text').html(percentage_stage_3);
                $('#generate-percentage-stage-3 div.progress-bar').css({
        'width':			percentage_stage_3,
                'background-color':	'#99D100'
        });
        }

        // ...and set ajax timer
        set_ajax_timer();
        }
        }) (jQuery);
        }

        function generate_error(message)
        {
        (function($){
        // Setup and show error pane
        $('#generate-error-message').html(message);
                $('#generate-failed').show();
        }) (jQuery);
        }

        function generate_complete(data)
        {
        (function($){

        // Hide progress
        //$('#generate-progress').hide("fast");
        $('#generate-step-stage-1').html(data.stage_1.current_step);
                $('#generate-percentage-stage-1 div.progress-text').html('100%');
                $('#generate-percentage-stage-1 div.progress-bar').css({
        'width':			'100%',
                'background-color':	'#99D100'
        });
                $('#generate-step-stage-2').html(data.stage_2.current_step);
                $('#generate-percentage-stage-2 div.progress-text').html('100%');
                $('#generate-percentage-stage-2 div.progress-bar').css({
        'width':			'100%',
                'background-color':	'#99D100'
        });
                $('#generate-step-stage-3').html(data.stage_3.current_step);
                $('#generate-percentage-stage-3 div.progress-text').html('100%');
                $('#generate-percentage-stage-3 div.progress-bar').css({
        'width':			'100%',
                'background-color':	'#99D100'
        });
                completion_processed = true;
                // Do we have warnings?
                if (data.completion.warnings.length > 0)
        {
        $('#generate-warnings').width('100%');
                $.each(data.completion.warnings, function(i, warning){
                var newDiv = $(document.createElement('li'))
                        .html(warning)
                        .appendTo($('#generate-warnings-list'));
                });
                if ($('#generate-warnings').is(":hidden"))
        {
        $('#generate-warnings').show('fast');
        }
        }
        // Show finished pane
        $('#generate-complete').show();
                if ($('#jform_logging').val() == '1')
        {
        $('#generate-next-view-logs').show();
        }
        // Hide cancel button
        $('#generate-progress-cancel').hide("fast");
                if (data.completion.error == '')
        {
        var success_text = generate_translations['GENERATE-SUCCESS'];
                var first_sub = success_text.indexOf('%s');
                var last_sub = success_text.lastIndexOf('%s');
                var success_msg = success_text.slice(0, first_sub) + data.initialise.component_name
                + success_text.slice(first_sub + 2, last_sub) + data.initialise.code_template_name + success_text.slice(last_sub + 2);
                $('#generate-success-message').html(success_msg);
                var success_path = generate_translations['GENERATE-SUCCESS-PATH'].replace(/%s/gi, data.completion.component_path);
                $('#generate-success-path').html(success_path);
                var install_url = '';
                if (data.completion.zip_file != '')
        {
        var download_url = data.completion.zip_path + '/' + data.completion.zip_file;
                var success_zip = generate_translations['GENERATE-SUCCESS-ZIP'].replace(/%s/gi, download_url);
                $('#generate-success-zip').html(success_zip);
                $('#generate-success-zip').show();
                var download_zip = generate_translations['GENERATE-DOWNLOAD-ZIP'].replace(/%s/gi, download_url);
                $('#generate-download').html(download_zip);
                $('#generate-download').show();
        }

        install_url = data.completion.component_path;
                $('#install-url').val(install_url);
                $('#generate-install').show();
                // All text set up so lets show the block		    
                $('#generate-success').show();
                $('#generate-error-message').hide("fast");
                $('#generate-failed').hide("fast");
        }
        else
        {

        var failed_text = generate_translations['GENERATE-FAILED'];
                var failed_first_sub = failed_text.indexOf('%s');
                var failed_last_sub = failed_text.lastIndexOf('%s');
                var failed_msg = failed_text.slice(0, failed_first_sub) + data.initialise.component_name
                + failed_text.slice(failed_first_sub + 2, failed_last_sub) + data.initialise.code_template_name + failed_text.slice(failed_last_sub + 2);
                $('#generate-failed-message').html(failed_msg);
                // All text set up so lets show the block		    
                if ($('#generate-failed').is(":hidden"))
        {
        var error_message = data.completion.error;
                generate_error(error_message);
        }
        $('#generate-success').hide("fast");
        }

        }) (jQuery);
        }
        /**
         * An extremely simple error handler, dumping error messages to screen
         * @param error The error message string
         */
        function dummy_error_handler(error)
        {
        alert(generate_translations['GENERATE-ERROR-GENERAL'] + "\n" + error);
        }
