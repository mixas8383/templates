<?php
/**
 * @version			$Id: default.php 411 2014-10-19 18:39:07Z BrianWade $
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
//[%%START_CUSTOM_CODE%%]
// no direct access
defined ( '_JEXEC' ) or die;

// Get default values from Options
$params = $this->state->get ( 'params' );

$output_path_default = $params->get ( 'default_output_path', 'tmp' );
$zip_format_default = $params->get ( 'default_zip_format', '' );
$logging_default = $params->get ( 'default_logging', '0' );
?>
<!-- Javascript and jQuery detection. Shows a warning if either is missing -->

<noscript>
<p style="color: red;"><?php echo JText::_ ( 'COM_COMPONENTARCHITECT_WARNING_NOSCRIPT' ); ?><p>
    </noscript>
<div id="nojquerywarning">
    <p style="color: red;"><?php echo JText::_ ( 'COM_COMPONENTARCHITECT_WARNING_NOJQUERY' ); ?><p>
</div>

<script type="text/javascript">
    if (jQuery)
    {
    jQuery('#nojquerywarning').css('display', 'none');
    }
</script>
<script type="text/javascript">
// jQuery document load functions for generate progress
    jQuery(document).ready(function ()
    {
    // The return URL

    // Push some translations
    generate_translations['GENERATE-SUCCESS'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DIALOG_SUCCESS', true ) ?>';
            generate_translations['GENERATE-SUCCESS-PATH'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DIALOG_SUCCESS_PATH', true ) ?>';
            generate_translations['GENERATE-SUCCESS-ZIP'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DIALOG_SUCCESS_ZIP', true ) ?>';
            generate_translations['GENERATE-DOWNLOAD-ZIP'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DOWNLOAD_BUTTON', true ) ?>';
            generate_translations['GENERATE-FAILED'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DIALOG_FAILED', true ) ?>';
            generate_translations['GENERATE-ERROR-GENERAL'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_GENERAL', true ) ?>';
            generate_translations['GENERATE-ERROR-AJAX-LOADING'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_AJAX_LOADING', true ) ?>';
            generate_translations['GENERATE-ERROR-AJAX-INVALID-DATA'] = '<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ERROR_AJAX_INVALID_DATA', true ) ?>';
    });
            function generate_download_link()
            {
                    var jform_output_path = document.getElementById("jform_output_path").value ;
                    var jform_module_name = document.getElementById("jform_module_name").value ;
                    
                    var base_url = '<?php echo JURI::root() ;?>';
                            
var link = base_url +jform_output_path+'/mod_'+jform_module_name+'/mod_'+jform_module_name+'.zip';
                    var link_html = '<a  target="_blank"  href="'+link+'">Link To Download Zip</a>';
                    
                    document.getElementById("link_download_zip").innerHTML=link_html;
                    //generate-download
                   
            }

    Joomla.submitbutton = function (task)
    {
    if (document.formvalidator.isValid(document.id('generate-form'))) {
    Joomla.submitform(task, document.getElementById('generate-form'));
    }
    }

</script>
<a href=""></a>
<?php
if ( version_compare ( JVERSION, '3.0', 'ge' ) )
{
    ?>
    <div class="form-horizontal">
        <?php
    }
    else
    {
        ?>
        <fieldset class="adminform">
            <?php
        }
        ?>
        <div class="fltlft left" id="generate-instructions">
            <h3>
                <?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DIALOG_INSTRUCTIONS_LABEL' ); ?>
            </h3>
            <div>
                <?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DIALOG_INSTRUCTIONS_DESC' ); ?>
            </div>		
        </div>
        <div class="fltlft left" id="generate-advice" style="display: none;">
            <h3>
                <?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_DIALOG_PROGRESS_LABEL' ); ?>
            </h3>
            <div id="generate-advice-stay">
                <?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_ADVICE_STAY_ON_PAGE' ); ?>
            </div>
            <?php
            if ( !function_exists ( 'ini_set' ) )
            {
                ?>	
                <div id="generate-advice-php">
                    <?php echo JText::sprintf ( 'COM_COMPONENTARCHITECT_GENERATE_ADVICE_PHP_MAX_EXECUTION' ); ?>
                </div>
                <?php
            }
            ?>
        </div>			
        <?php
        if ( version_compare ( JVERSION, '3.0', 'ge' ) )
        {
            ?>
    </div>
    <?php
}
else
{
    ?>
    </fieldset>
    <?php
}
?>
                            <div id="link_download_zip"></div>

<form action="<?php echo JRoute::_ ( 'index.php?option=com_modcreator' ); ?>" id="generate-form" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div id="generate-setup">
        <?php
        if ( version_compare ( JVERSION, '3.0', 'ge' ) )
        {
            ?>
            <div class="form-horizontal">
                <?php
            }
            else
            {
                ?>
                <fieldset class="adminform">
                    <?php
                }
                ?>
                <div class="left">	
                    <div class="control-group" id="field_output_path">
                        <div class="control-label"><?php echo $this->form->getLabel ( 'output_path' ); ?></div>
                        <div class="controls"><?php echo $this->form->getInput ( 'output_path', null, $output_path_default ); ?></div>
                    </div>	
                    <div class="control-group" id="field_component_id">
                        <div class="control-label"><?php echo $this->form->getLabel ( 'module_name' ); ?></div>
                        <div class="controls"><?php echo $this->form->getInput ( 'module_name' ); ?></div>
                    </div>
                </div>
                <div class="left" style="clear: both; padding-top: 15px; text-align: center;">                         <button id="generate-start" type="button" class="btn btn-large btn-primary hasTip" title="<?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_BUTTON_DESC' ); ?>"
                                                                                                                               onclick="generate_module();generate_download_link();
                                                                                                                                           return false;">
                                                                                                                                   <?php echo JText::_ ( 'COM_COMPONENTARCHITECT_GENERATE_BUTTON' ); ?>
                    </button>
                </div>
                <?php
                //           generate_module();

                if ( version_compare ( JVERSION, '3.0', 'ge' ) )
                {
                    ?>
            </div>
            <?php
        }
        else
        {
            ?>
            </fieldset>
            <?php
        }
        ?>
        <input type="hidden" name="task" value="componentarchitect.generate" />
        <div id="token" style="display: none;">
            <?php echo JHtml::_ ( 'form.token' ); ?>
        </div>

    </div>
</form>
<!-- [%%END_CUSTOM_CODE%%] -->
