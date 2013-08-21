// JavaScript Document
jQuery(document).ready(
function()
	{
	
	/* TOOLTIP */
		jQuery('.form_container div#help_text').each(
		function()
			{
				jQuery(this).tooltip({
			 position: {
					my: "center bottom",
					at: "center top",
			 		}
				});
			}
		);
	/* HOVER STATE */
	jQuery('.iz-submit').live('hover',
		function()
			{
			jQuery(this).addClass('ui-state-active');
			}
		);
	jQuery('.iz-submit').live('mouseleave',
		function()
			{
			jQuery(this).removeClass('ui-state-active');
			}
		);
		
	jQuery('.ve_text, .ve_textarea, .ve_dropdown, .ve_dropdown, .ve_email, .ve_phone_number, .ve_url, .ve_file, .ve_auto, .ve_date, .ve_time, .ve_numbers_only, .ve_text_only').live('hover',
		function()
			{
			jQuery(this).addClass('ui-state-hover');
			}
		);
	jQuery('.ve_text, .ve_textarea, .ve_dropdown, .ve_dropdown, .ve_email, .ve_phone_number, .ve_url, .ve_file, .ve_auto, .ve_date, .ve_time, .ve_numbers_only, .ve_text_only').live('mouseleave',
		function()
			{
			jQuery(this).removeClass('ui-state-hover');
			}
		);
	
	
	/* AUTO COMPLETE */

jQuery('.field_settings, .field_actions, .click_to_edit, .set_required, .show_field_settings, .draggable_object').remove();
/*************************/
/**** Form validation ****/
/*************************/
	jQuery('div.submit_form_entry').click(
		
		function() {
			
			validate_form(jQuery(this));
			}
		);
	}
);



			
function onError(i, v, msg) {
				if(!i.hasClass('has_errors')){
					i.closest('fieldset').prepend('<div class="error">'+ msg +'</div>');
					i.addClass('has_errors');
				}
			}
function onSuccess(i, v) {
				i.removeClass('has_errors');
				i.closest('fieldset').find('div.error').remove();
			}

function isNumber(n) {
				   if(n!='')
                  		return !isNaN(parseFloat(n)) && isFinite(n);
					
					return true;
                }

function IsValidEmail(email){
  
  if(email!=''){
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return filter.test(email);
  }
	return true;
}

function allowedChars(input_value, accceptedchars){
	var aChars = ' -_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if(accceptedchars)
		{
		switch(accceptedchars)
			{
			case 'tel': aChars = '1234567890-+() '; break;
			case 'text': aChars = ' abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';break;
			default: aChars = accceptedchars; break;
			}
		}
	var valid = false;
	var txt = input_value.toString();
	
	for(var i=0;i<txt.length;i++) {
		if (aChars.indexOf(txt.charAt(i)) != -1) {
			valid = true;
		} else {
			valid = false;
			break;
		}
	 }
	return valid;
}

function validate_url(get_url) {
        var url = get_url;
        var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        if (pattern.test(url))
            return true;
 
        return false;
}


function validate_form(object){
	
	var current_form = object.closest('div.XFormsExpress_ui').find('form');
	
	//console.log(current_form)
	
	jQuery('.error_msg').hide();
	jQuery('.error_msg').removeClass('show');
	var formdata = {
                   radios : [], //an array of already validate radio groups
                   checkboxes : [], //an array of already validate checkboxes
                   runCnt : 0, //number of times the validation has been run
                   errors: 0
               }
	
	var defaultErrorMsgs = {
                    email : 'Not a valid email address',
                    number: 'Not a valid phone number',
                    required: 'Please enter a value',
                    text: 'Only text allowed'
                }
	
	var settings = {
                'requiredClass': 'required',
                'customRegex': false,
				'errors' : 0,
                'checkboxDefault': 0,
                'selectDefault': 0,
                'beforeValidation': null,
                'onError': null,
                'onSuccess': null,
                'beforeSubmit': null, 
                'afterSubmit': null,
                'onValidationSuccess': null,
                'onValidationFailed': null
            };
	
	
	jQuery(current_form).find('input').each( function() {
		var input = jQuery(this);
		var val = input.val();                                                                                
		var name = input.attr('name');
		if(input.is('input'))
			{
			
			var type = input.attr('type');
			switch(type)
				{
				case 'text':
					if(input.parent().parent().hasClass('required')) {
					//console.log(val);
							if(val.length < 1)
								{
								settings.errors++;                               
								input.parent().parent().find('div.error_msg').show();
								input.parent().parent().find('div.error_msg').addClass('show');
								break;
								}
							 else
								{
								
								//Everything passed
								onSuccess(input, val);
								}
							}
					
				break;
				
				case 'radio':
					//avoid checking the same radio group more than once                                    
					var radioData = formdata.radios;
					//console.log(radioData);
				   
						if(input.parent().parent().hasClass('required'))
							{
							if(radioData)
								{
								if(jQuery.inArray(name, radioData) >= 0) 
									break;
								else
									{
									if(jQuery(this).attr('checked')!='checked')
										{
										settings.errors++;
										input.parent().find('div.error_msg').show();
										input.parent().find('div.error_msg').addClass('show');
										onError(input, val, defaultErrorMsgs.required);
										}
									 else
										{
										onSuccess(input, val);
										break;
										}                                           
									radioData.push(name);
									} 
								} 
							}
						
					//update the data
					//$theForm.data('jbvalidation', data);                                    
					
					//end case 'radio'
					break;
				
				 case 'checkbox':
					//avoid checking the same radio group more than once                                    
					var checkData = formdata.checkboxes;
					//console.log(radioData);
				   
						if(input.parent().parent().hasClass('required'))
							{
							if(checkData)
								{
								if(jQuery.inArray(name, checkData) >= 0) 
									break;
								else
									{
									if(jQuery(this).attr('checked')!='checked')
										{
										settings.errors++;
										input.parent().find('div.error_msg').show();
										input.parent().find('div.error_msg').addClass('show');
										onError(input, val, defaultErrorMsgs.required);
										}
									 else
										{
										onSuccess(input, val);
										break;
										}                                           
									checkData.push(name);
									} 
								} 
							}
						
				break;
				}
			}
		}
	);                       
	
   jQuery(current_form).find('textarea').each( function() {	
		if(jQuery(this).parent().parent().hasClass('required'))
			{
			if(jQuery(this).val() == '') {
				settings.errors++;
				onError(jQuery(this), jQuery(this).val(), defaultErrorMsgs.required);
				jQuery(this).parent().parent().find('div.error_msg').show();
				jQuery(this).parent().parent().find('div.error_msg').addClass('show');
				}
			 else
				{
				onSuccess(jQuery(this), jQuery(this).val());
				}
			}
  		 }
   	);
	
	jQuery(current_form).find('select').each( function() {	
		if(jQuery(this).parent().parent().hasClass('required'))
			{
			if(jQuery(this).val() == 0) {
				settings.errors++;
				onError(jQuery(this), jQuery(this).val(), defaultErrorMsgs.required);
				jQuery(this).parent().parent().find('div.error_msg').show();
				jQuery(this).parent().parent().find('div.error_msg').addClass('show');
				}
			 else
				{
				onSuccess(jQuery(this), jQuery(this).val());
				}
			}
  		 }
   	);
	   
if(settings.errors == 0) 
	{
	current_form.parent().prepend('<div style="margin-top: 15px; padding: 15px" class="ui-state-highlight ui-corner-all"><span style="float: left; margin-right: 5px; margin-top:3px" class="ui-icon ui-icon-info"></span>Submiting...</div>');
	current_form.fadeOut('fast');
	var offset = current_form.parent().offset();
	jQuery("html, body").animate(
			{
			scrollTop:offset.top-50
			},500
		)
	document.forms[current_form.attr('name')].submit();
	}
else
	{
	var offset = jQuery('div.error_msg.show').offset();
	jQuery("html, body").animate(
			{
			scrollTop:offset.top-50
			},700
		)
	return false;
	}
}