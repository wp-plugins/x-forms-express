// JavaScript Document
if(tutorial == 'undefined' || tutorial==null)
	var tutorial = false;
var visual_editing = '';
function biuld_tabs()
	{
	var tabs_width = jQuery('ul.forms.tab_menu').outerWidth();
	
	var col_width = jQuery('div.primary-widget-area').outerWidth();
		//console.log(tabs_width + ':' +col_width);
	if(tabs_width>(col_width-100))
		{
		jQuery('div.col1 div.header').css('overflow','visible');	
		jQuery('ul.forms.tab_menu').hide();	
		jQuery('ul.forms.drop_menu').show();
		}
	else
		{
		jQuery('div.col1 div.header').css('overflow','hidden');
		jQuery('ul.forms.tab_menu').show();
		jQuery('ul.forms.drop_menu').hide();
		}
	
		
	}
var default_email_settings = jQuery('div.widget-list.settings_email').html();
function reset_all_actions(){
	jQuery('.settings_view').hide();
	jQuery('.form_field').removeClass('editing').removeClass('show_actions');
	jQuery('.form_field').css('opacity','1')
	jQuery('.form_field span').removeClass('show');
	jQuery('.current_object').text('');
	jQuery('div, label, input, select, textarea, p, h1, h2, h3, h4, h5, h6').removeClass('show_outline').removeClass('current_selection').removeClass('faded');
	jQuery('div.field_settings').hide();
	jQuery('.view_styles').hide();
	jQuery('h3.show_available_styles').removeClass('show');
	if(!jQuery('.ve_view').hasClass('active'))
		jQuery('.error_msg').hide();
	jQuery('h3.show_available_styles').text('Overall Styling Selection');
	
	jQuery('label').removeClass('ui-state-active');
	jQuery('input.radio').attr('checked',false);
	jQuery('input.check').attr('checked',false);
}

jQuery(document).ready(
function()
	{
	jQuery('div.field_holder').each(
		function()
			{
			if(jQuery(this).hasClass('required'))
				jQuery(this).find('.set_required input[name="required"]').attr('checked','checked');
				
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
	jQuery( 'div.form_setup legend' ).tooltip(
		{
			 position: {
				my: "center bottom",
				at: "center top-1",
			 }
		});
	jQuery('input').focus(function() { jQuery(this).select() });
	
	jQuery( ".avialable_fields_container " ).accordion({
		collapsible: true,
		heightStyle: "content",
		active: 0
	});	
	biuld_tabs();
	
	jQuery(window).resize(function() {
		biuld_tabs();
	});
	
	jQuery('input[name="form_name"]').val('');
	jQuery('.opened_forms li.newform').trigger('click');
	

	jQuery('.form_container select.ve_dropdown').each(
		function()
			{
			jQuery(this).parent().find('textarea').val(jQuery(this).parent().find('div.get_dropdown_items').text());
			}
		);
	jQuery('.form_container').find('div#help_text').tooltip(
		{
			 position: {
					my: "center bottom",
					at: "center top",
			 }
		});
	/*******************************/
	/******** FIELD SETTINGS *******/
	/*******************************/
	jQuery('h3.show_available_styles').click(
		function()
			{
			if(jQuery(this).hasClass('show'))
				{
				jQuery('.view_styles').hide();
				jQuery(this).removeClass('show');
				}
			else
				{
				jQuery('.view_styles').show();
				jQuery(this).addClass('show');
				}
			}
		);
	jQuery('div.overall_styling').click(
		function()
			{
			jQuery('.view_styles').hide();
			jQuery('h3.show_available_styles').removeClass('show');
			if(jQuery(this).text()=='None')
				jQuery('h3.show_available_styles').text('Overall Styling Selection');
			else
				jQuery('h3.show_available_styles').text('Currently styling all '+jQuery(this).text());
			}
		);
	jQuery('span.field_settings').live('click',
		function()
			{
			var settings = jQuery('div.settings_view');
			settings.hide();
			var title = jQuery(this).parent().parent().find('label.title').text();
			var description = jQuery(this).parent().parent().find('div#help_text').attr('title');
			var error_msg = jQuery(this).parent().parent().find('div.error_msg').text();
			jQuery('div.error_msg').hide();
			
			if(description=='...This is where the field desciption / help text will be displayed...')
					jQuery(this).parent().parent().find('div#help_text').attr('title','')
			
			if(jQuery(this).hasClass('show'))
				{
				jQuery('span.field_settings').removeClass('show');
				jQuery('.form_field').css('opacity','1');
				jQuery(this).parent().parent().removeClass('editing');
				settings.hide();
				jQuery(this).removeClass('show');
				jQuery(this).parent().parent().find('div.error_msg').hide();
				
				}
			else
				{
				jQuery('.drop-sort .form_field').css('opacity','0.3');
				jQuery(this).parent().parent().addClass('editing');
				settings.show();
				jQuery(this).addClass('show');
				
				settings.find('input[name="change_field_name"]').val(title);
				settings.find('input[name="change_field_description"]').val(description);
				settings.find('input[name="change_field_error"]').val(error_msg);
				
				settings.find('input[name="change_field_name"]').attr('data-id',jQuery(this).parent().parent().attr('data-id'));
				
				if(!description)
					jQuery(this).parent().parent().find('div#help_text').attr('title','...This is where the field desciption / help text will be displayed...')
				
				jQuery(this).parent().parent().find('div.error_msg').show();
				
				
				var pos = jQuery(this).offset();
				settings.show();
				settings.css('top',pos.top-25-settings.outerHeight()-jQuery(this).outerHeight());
				settings.css('left',pos.left-jQuery('div#adminmenuback').outerWidth()-19);
				settings.find('div.save_field_settings').attr('data-id',jQuery(this).parent().parent().attr('data-id'));
				
				jQuery(this).parent().parent().css('opacity','1');
				}
			}
		);
	
	jQuery('div.save_field_settings').live('click',
		function()
			{
			var field = jQuery('.'+ jQuery(this).attr('data-id'));
			var settings = jQuery('div.settings_view');
			change_label(settings.find('input[name="change_field_name"]'));
			field.find('label.title').text(settings.find('input[name="change_field_name"]').val());
			field.find('div#help_text').attr('title',settings.find('input[name="change_field_description"]').val());
			field.find('div.error_msg').html('<span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>'+settings.find('input[name="change_field_error"]').val());
			
			if(settings.find('input[name="change_field_description"]').val()!='')
				field.find('div#help_text').show();
			else
				field.find('div#help_text').hide();
			
			reset_all_actions();			
			}
		);
	/*******************************/
	/*******************************/
	/*******************************/
	
	jQuery('select[name="send_user_mail"]').change(
		function()
			{
			if(jQuery(this).val()=='yes')
				jQuery('.email_setup__'+jQuery('.current_canvas').text()).find('fieldset.mail_to_user_address').slideDown('fast');
			else
				jQuery('.email_setup__'+jQuery('.current_canvas').text()).find('fieldset.mail_to_user_address').slideUp('fast');
			}
		);
	
	jQuery('select[name="on_form_submission"]').change(
		function()
			{
			if(jQuery(this).val()=='show_message')
				{
				jQuery('.general_setup__'+jQuery('.current_canvas').text()).find('fieldset.on_screen_confirmation_message').slideDown('fast');
				jQuery('.general_setup__'+jQuery('.current_canvas').text()).find('fieldset.confirmation_page').slideUp('fast');
				}
			else
				{
				jQuery('.general_setup__'+jQuery('.current_canvas').text()).find('fieldset.on_screen_confirmation_message').slideUp('fast');
				jQuery('.general_setup__'+jQuery('.current_canvas').text()).find('fieldset.confirmation_page').slideDown('fast');	
				}
			}
		);
	
	
	
	
	jQuery('div.save_form').click(
		function()
			{
			reset_all_actions();
			jQuery('.error_msg').hide();
			if(!jQuery('input[name="form_name"]').val())
				{
				var obj_pos	= jQuery('input[name="form_name"]').offset();
				jQuery("html, body").animate(
						{
							scrollTop:50
						},300
					)
				show_iz_tooltip(jQuery('input[name="form_name"]'), 'Please enter a form title and click save again.');
				jQuery('div.save_form').html('<span class="save_icon">&nbsp;</span>Plaese enter a form title!');
				}
			else
				{
				jQuery('div.save_form').html('<span class="save_icon">&nbsp;</span>Please wait while saving...');
				var user_mail_fields ='';
				jQuery('.email_setup__'+jQuery('.current_canvas').text() + ' fieldset.mail_to_user_address div.iz-form-item input[type="checkbox"]').each(
					function()
						{
						
						if(jQuery(this).attr('checked')=='checked')
							user_mail_fields += jQuery(this).val()+'__'+jQuery(this).attr('id')+',';
						else
							user_mail_fields += 'unchecked__'+jQuery(this).val()+'__'+jQuery(this).attr('id')+',';
						}
					
					);
				
				jQuery('.tabs.ve_from_entries ').show();
				jQuery('.tabs.code_view ').show();
			
				
				var current_ID = jQuery('.current_canvas').text();
				var data =
						{
						action	 							: (jQuery('.form_update_id').text()) ? 'edit_form' : 'insert_form',
						table								: 'wap_x_forms',
						edit_Id								: jQuery('.form_update_id').text(),
						plugin								: 'shared',
						title								: jQuery('input[name="form_name"]').val(),
						form_fields							: jQuery('div#drop_sort__'+current_ID).html(),
						mail_to								: jQuery('.email_setup__'+current_ID+' input[name="mail_to"]').val(),
						confirmation_mail_subject			: jQuery('.email_setup__'+current_ID+' input[name="confirmation_mail_subject"]').val(),
						confirmation_mail_body				: jQuery('.email_setup__'+current_ID+' textarea[name="confirmation_mail_body"]').val(),
						from_address						: jQuery('.email_setup__'+current_ID+' input[name="from_address"]').val(),
						send_user_mail						: jQuery('.email_setup__'+current_ID+' select[name="send_user_mail"]').val(),
						user_email_field					: user_mail_fields,
						on_form_submission					: jQuery('.general_setup__'+current_ID+' select[name="on_form_submission"]').val(),
						on_screen_confirmation_message		: jQuery('.general_setup__'+current_ID+' textarea[name="on_screen_confirmation_message"]').val(),
						confirmation_page					: jQuery('.general_setup__'+current_ID+' input[name="confirmation_page"]').val(),
						google_analytics_conversion_code	: jQuery('.general_setup__'+current_ID+' textarea[name="google_analytics_conversion_code"]').val(),
						};		
					
					//console.log(user_mail_fields);
					
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							jQuery('div.save_form').html('<span class="save_icon">&nbsp;</span>Saved!');
							
								if(!jQuery('.form_update_id').text())
									{
									jQuery('.drop-sort').hide();
								
									var new_form_area = jQuery('.drop-sort.newform').clone();
									//var unique_id = Math.round(Math.random()*99999999999);
									
									new_form_area.removeClass('newform');
									new_form_area.addClass('_'+response);
									new_form_area.attr('id','drop_sort__'+response);
									new_form_area.show();
									jQuery('div.primary-widget-area').prepend(new_form_area);
								
									jQuery('.opened_forms li').removeClass('active');
									
									jQuery('ul.forms.tab_menu').prepend('<li class="form active" data-form-id="'+ response +'"  id="tab_'+ response +'">'+ jQuery('input[name="form_name"]').val() +'</li>');

									var copy_email_setup = jQuery('.settings_email.newform').clone();
									copy_email_setup.removeClass('email_setup__'+jQuery('.current_canvas').text()).removeClass('newform').addClass('email_setup__'+response);;
									jQuery('div.settings_email').next('.widget-holder').append(copy_email_setup);
									
									var copy_general_setup = jQuery('.settings_general.newform').clone();
									copy_general_setup.removeClass('general_setup__'+jQuery('.current_canvas').text()).removeClass('newform').addClass('general_setup__'+response);
									jQuery('div.settings_general').next('.widget-holder').append(copy_general_setup);
									
									jQuery('ul.forms.drop_menu li ul').prepend('<li class="form active" data-form-id="'+ response +'"  id="tab_'+ response +'">'+ jQuery('input[name="form_name"]').val() +'</li>');
									
									jQuery('.current_canvas').text(response);
									jQuery('.current_object').text('form_container_bg');
									
									jQuery('.drop-sort.newform').html('<div id="form_container" class="form_container_bg form_container ui-droppable ui-sortable"><div class="form_field ui-draggable submit_button"><div style="clear:both;"></div><div class="form_object" id="form_object"><div class="ui-button ui-widget ui-state-default ui-state-hover ui-corner-all ui-button-text-only iz-submit submit_form_entry">Submit</div><span class="click_to_edit"></span></div></div></div>');
									jQuery('.form_update_id').text(response)
									biuld_tabs();
									
									jQuery('li#tab_'+ response).trigger('click');
									
									create_droppable();
									
									}
								else
									{
									jQuery('li.active').text(jQuery('input[name="form_name"]').val());	
									}
							
							
							setTimeout(
								function()	
									{
									jQuery('div.save_form').html('<span class="save_icon">&nbsp;</span>Save Form');
									jQuery("html, body").animate(
											{
												scrollTop:50
											},300
										);
									},500
								);
							}
						);
					}
				}
			);
		
	jQuery('.form_object span.go').live('click',
		function()	
			{
			reset_all_actions();
			jQuery(this).parent().removeClass('edit');
			jQuery(this).parent().find('input').trigger('blur');
			}
		);
		
	jQuery('div#remove_form').click(
		function()
			{
			
			if(confirm('Are your sure you want to permanently delete this form?'))
				{
					
				jQuery('li#tab_' + jQuery('.current_canvas').text()).remove();
				jQuery('#drop_sort__'+jQuery('.current_canvas').text()).remove();
				var data =
						{
						action		: 'delete_form',
						Id			: jQuery('.current_canvas').text()
						};		
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							}
						);
				jQuery('.opened_forms li.newform').trigger('click');
				}
			}
		);
	
	jQuery('div.duplicate_form').click(
		function()
			{
			var current_canvas = jQuery('.current_canvas').text();
			var current_title = jQuery('input[name="form_name"]').val();
			
			jQuery('div.widget-list.settings_email.newform').html(jQuery('div.widget-list.settings_email.email_setup__'+current_canvas).html());
			jQuery('div.widget-list.settings_general.newform').html(jQuery('div.widget-list.settings_general.general_setup__'+current_canvas).html());
			
			jQuery('.current_object').text('form_container_bg');
			
			jQuery('.opened_forms li.newform').trigger('click');
			
			jQuery('.drop-sort.newform').html(jQuery('#drop_sort__'+current_canvas).html());
			
			jQuery('.opened_forms li.newform').trigger('click');
			
			jQuery('input[name="form_name"]').val('DUP - '+ current_title)
			
			
			jQuery('.tabs.ve_from_entries ').hide();
			jQuery('.tabs.code_view ').hide();
			
			create_droppable();
			
			
				/* TIME PICKER */
				jQuery('input.time').each(
					function()
						{
						jQuery(this).removeClass('hasTimepicker');
						jQuery(this).timepicker();	
						}
					);
				jQuery('.form_container .radio_button_group').each(
					function()
						{
						jQuery(this).parent().find('textarea').val(jQuery(this).parent().find('div.get_radio_items').text());
						}
					);
					
				jQuery('.form_container .check_box_group').each(
					function()
						{
						jQuery(this).parent().find('textarea').val(jQuery(this).parent().find('div.get_checkbox_items').text());
						}
					);
				
				jQuery('.form_container select.ve_dropdown').each(
					function()
						{
						jQuery(this).parent().find('textarea').val(jQuery(this).parent().find('div.get_dropdown_items').text());
						}
					);
				jQuery('.form_container input.file').each(
					function()
						{
						jQuery(this).parent().find('textarea').val(jQuery(this).parent().find('div.get_file_ext').text());
						}
					);
				jQuery('.form_container input.auto').each(
					function()
						{
						var items = jQuery(this).parent().find('div.get_auto_complete_items').text();
						jQuery(this).parent().find('textarea').val(items);
						items = items.split('\n');
						
						jQuery(this).autocomplete({
							source: items
						});	
						}
					);
				
				jQuery('.form_container').find('div#help_text').tooltip(
					{
						 position: {
								my: "center bottom",
								at: "center top",
						 }
					});
			
			}
		);
	function write_code(form_id){
		jQuery('.show_code div.php_function').html('<strong>Return output:</strong><br><code class="ui-state-default">&lt;?php XForms_ui_output('+ form_id +'); ?&gt;</code><br><strong>Echo output:</strong><br><code class="ui-state-default">&lt;?php XForms_ui_output('+ form_id +',true); ?&gt;</code>OR<code class="ui-state-default">&lt;?php echo XForms_ui_output('+ form_id +'); ?&gt;</code>');
		jQuery('.show_code div.short_code').html('<code class="ui-state-default">[XForms id="'+ form_id +'"]</code>')
	}
	
	jQuery('.opened_forms li.newform').click(
		function()
			{
			reset_all_actions();
			jQuery('input[name="current_page"]').val(0);
			jQuery('div.set_layout').removeClass('active');
			jQuery('.current_object').text('form_container_bg');
			
			jQuery('div.widget-list.settings_email').hide();
			jQuery('div.widget-list.settings_email.newform').show();
			
			jQuery('div.widget-list.settings_general').hide();
			jQuery('div.widget-list.settings_general.newform').show();
			/*if(get_current_color_sheme)
				{
				var get_current_color_sheme_2 = get_current_color_sheme.split('css/');
				var get_current_color_sheme_3 = get_current_color_sheme_2[1].split('/jquery.ui.theme.css');
				//jQuery('select[name="change_color_scheme"] option[value="'+ get_current_color_sheme_3[0] +'"]').attr('selected','selected');
				}
			*/
			jQuery('.opened_forms li').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('.drop-sort').hide();
			jQuery('.drop-sort.newform').show();
			jQuery('.forms_actions').hide();
			jQuery('.tabs.ve_from_entries ').hide();
			jQuery('.tabs.code_view').hide();
			jQuery('div.show_code').hide();
			
			jQuery('input[name="form_name"]').val('');
			jQuery('.form_update_id').text('');
			jQuery('.current_canvas').text(jQuery(this).attr('data-form-id'));

			jQuery('.overall_styling.form_container').attr('id',jQuery(this).attr('data-form-id'));
			
			if(jQuery('.tabs.ve_from_entries ').hasClass('active'))
				{
				jQuery('.tabs.normal_view').trigger('click');		
				}
			if(jQuery('.tabs.ve_from_entries ').hasClass('active'))
				{
				jQuery('.tabs.code_view').trigger('click');		
				}
			}
		);
	
	jQuery('.opened_forms li.form').live('click',
		function()
			{
			reset_all_actions();
			jQuery('input[name="current_page"]').val(0);
			jQuery('div.set_layout').removeClass('active');
			
		

			jQuery('input[name="form_name"]').val(jQuery(this).text());
			jQuery('.opened_forms li').removeClass('active');
			jQuery('li#tab_' + jQuery(this).attr('data-form-id')).addClass('active');
			jQuery('.drop-sort').hide();
			jQuery('#drop_sort__'+jQuery(this).attr('data-form-id')).find('div.field').remove();
			jQuery('#drop_sort__'+jQuery(this).attr('data-form-id')).show();
			jQuery('.forms_actions').show();
			
			jQuery('.tabs.ve_from_entries ').show();
			jQuery('.tabs.code_view').show();
			
			
			jQuery('div.widget-list.settings_email').hide();
			jQuery('div.widget-list.settings_email.email_setup__'+jQuery(this).attr('data-form-id')).show();
			
			jQuery('div.widget-list.settings_general').hide();
			jQuery('div.widget-list.settings_general.general_setup__'+jQuery(this).attr('data-form-id')).show();
			
			jQuery('.form_update_id').text(jQuery(this).attr('data-form-id'));
			jQuery('.current_canvas').text(jQuery(this).attr('data-form-id'));
			jQuery('.overall_styling.form_container').attr('id',jQuery(this).attr('data-form-id'));
			
			jQuery('.tabs.ve_from_entries .tab_description').html('&nbsp;');
			
			var get_user_mail = '';
			jQuery('div#drop_sort__'+jQuery('.current_canvas').text()).find('div.field_holder.email').each(
				function()
					{
					get_user_mail = jQuery(this).find('input.email').attr('name');
					}
				);
			if(!get_user_mail)
				jQuery('.email_setup__'+jQuery('.current_canvas').text() + ' .no_mail_error').show();
			else
				jQuery('.email_setup__'+jQuery('.current_canvas').text() + ' .no_mail_error').hide();
			
			if(jQuery('.tabs.ve_from_entries ').hasClass('active'))
				{
				jQuery('.tabs.ve_from_entries').trigger('click');		
				}
			if(jQuery('.tabs.code_view ').hasClass('active'))
				{
				jQuery('.tabs.code_view').trigger('click');		
				}
			
			write_code(jQuery(this).attr('data-form-id'));
			}
		);
	
	jQuery('a.export_csv').live('click',
		function()
			{
			document.export_csv.submit();
			}
		);
	
	jQuery('.opened_forms ul.forms').sortable(
		{ 
		containment: "parent",
		}
	);	
	
	
	/* TABS */
	jQuery('.tabs.normal_view').click(
		function()
			{
			reset_all_actions();
			jQuery('.tabs').removeClass('active');
			jQuery(this).addClass('active');
			visual_editing = false;
			jQuery(this).removeClass('show');
			jQuery('div.visual_editor').hide();
			jQuery('div.overall_styles').hide();
			jQuery('#drop_sort__'+jQuery('.current_canvas').text()).show();
			jQuery('div.form_setup').show();
			
			jQuery('div, label, input, select, textarea, p, h1, h2, h3, h4, h5, h6').removeClass('show_outline').removeClass('current_selection').removeClass('faded');
			jQuery('div.error_msg').hide();
			jQuery('div.form_entry_view').hide();
			jQuery('div.show_code').hide();
			//alert('#'+jQuery('.current_canvas').text());
			}
		);
	jQuery('.tabs.ve_view').click(
		function()
			{
			reset_all_actions();
			jQuery('.tabs').removeClass('active');
			jQuery(this).addClass('active');
			visual_editing = true;
			jQuery(this).addClass('show');
			jQuery('div.visual_editor').show();
			jQuery('div.overall_styles').show();
			jQuery('div.error_msg').show();
			jQuery('#drop_sort__'+jQuery('.current_canvas').text()).show();
			jQuery('div.form_setup').hide();
			jQuery('div.form_entry_view').hide();
			jQuery('div.show_code').hide();
			}
		);
	jQuery('.tabs.ve_from_entries ').click(
		function()
			{
			reset_all_actions();
			jQuery('.tabs').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('div.form_entry_view').show();
			jQuery('div.drop-sort').hide();
			jQuery('div.overall_styles').hide();
			jQuery('input[name="current_page"]').val(0);
			jQuery('div.show_code').hide();
			}
		);
	jQuery('.tabs.code_view ').click(
		function()
			{
			reset_all_actions();
			jQuery('.tabs').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('div.form_entry_view').hide();
			jQuery('div.drop-sort').hide();
			jQuery('div.show_code').show();
			}
		);
	
	jQuery('.ve_text').addClass('ui-state-default');
	jQuery('.ve_textarea').addClass('ui-state-default');
	jQuery('.ve_dropdown').addClass('ui-state-default');
	jQuery('.ve_email').addClass('ui-state-default');
	jQuery('.ve_phone_number').addClass('ui-state-default');
	jQuery('.ve_url').addClass('ui-state-default');
	jQuery('.ve_file').addClass('ui-state-default');
	jQuery('.ve_auto').addClass('ui-state-default');
	jQuery('.ve_date').addClass('ui-state-default');
	jQuery('.ve_time').addClass('ui-state-default');
	jQuery('.ve_numbers_only').addClass('ui-state-default');
	jQuery('.ve_text_only').addClass('ui-state-default');
	
	
	
	jQuery('select[name="change_color_scheme"]').change(
		function()
			{
			jQuery(this).parent().find('link.color_scheme').attr('href','http://webaways.com/express-themes/'+ jQuery(this).val() +'/jquery.ui.theme.css');	
			}
		);
	jQuery('select[name="change_button_layout"]').live('change',
		function()
			{
			if(jQuery(this).val()=='inline')
				{
				jQuery(this).parent().parent().find('div.radio_button_group').addClass('inline');
				jQuery(this).parent().parent().find('div.check_box_group').addClass('inline');
				}
			else
				{
				jQuery(this).parent().parent().find('div.radio_button_group').removeClass('inline');
				jQuery(this).parent().parent().find('div.check_box_group').removeClass('inline');
				}
			}
		);
	
	//jQuery('input[type="radio"]').button();
	jQuery('div.settings_layout, div.settings_color_scheme, div.settings_email, div.settings_general').click(
		function()
			{
			if(jQuery(this).hasClass('show'))
				{
				jQuery(this).removeClass('show');
				jQuery(this).next('div.widget-holder').hide();
				}
			else
				{
				jQuery(this).addClass('show');
				jQuery(this).next('div.widget-holder').show();
				}
			}
		);
		
	jQuery('.set_layout').click(
		function()
			{
			if(jQuery('.opened_forms li.newform').hasClass('active'))
				jQuery('.opened_forms li.newform').trigger('click');
				
			
			jQuery('.set_layout').removeClass('active');
			
			jQuery(this).addClass('active');
				
			//var inline_style = jQuery('#'+jQuery('.current_canvas').text()).find('style[title="inline_layout"]');
			if(jQuery(this).hasClass('one-column-full'))
				jQuery(this).parent().find('link.layout').attr('href',jQuery('#site_url').text() + '/wp-content/plugins/x-forms-express/css/layout/one-column-full.css');
				
			if(jQuery(this).hasClass('one-column-half'))
				jQuery(this).parent().find('link.layout').attr('href',jQuery('#site_url').text() + '/wp-content/plugins/x-forms-express/css/layout/one-column-half.css');
				
			if(jQuery(this).hasClass('two-column'))
				jQuery(this).parent().find('link.layout').attr('href',jQuery('#site_url').text() + '/wp-content/plugins/x-forms-express/css/layout/two-column.css');

			if(jQuery(this).hasClass('three-column'))
				jQuery(this).parent().find('link.layout').attr('href',jQuery('#site_url').text() + '/wp-content/plugins/x-forms-express/css/layout/three-column.css');
			}
		);
	jQuery('div.settings_visual_editor').click(
		function()
			{
			if(jQuery(this).hasClass('show'))
				{
				visual_editing = false;
				jQuery(this).removeClass('show');
				jQuery(this).parent().find('div.widget-holder').hide();
				jQuery('div, label, input, select, textarea, p, h1, h2, h3, h4, h5, h6').removeClass('show_outline').removeClass('current_selection').removeClass('faded');
				jQuery('div.error_msg').hide();
				}
			else
				{
				visual_editing = true;
				jQuery(this).addClass('show');
				jQuery('div.visual_editor').show();
				jQuery('div.error_msg').show();
				}
			}
		);
/*************************/
/***** Ready state *******/
/*************************/
	
	
	var pagination_data = {	
				table	 		: jQuery('div#component_table').text(),
				table_headers	: jQuery('input[name="fields"]').val(),
				page	 		: jQuery('input[name="page"]').val(),
				orderby	 		: jQuery('input[name="orderby"]').val(),
				order	 		: jQuery('input[name="order"]').val(),
				//current_page	: parseInt(jQuery('input[name="current_page"]').val()) || 0,
				form_Id			: jQuery('.current_canvas').text()		
			}
	
	jQuery('a.wafb-next-page').live('click',
		function()
			{
			var current_page = parseInt(jQuery('input[name="current_page"]').val()) || 0;
			
			if((parseInt(pagination_data.current_page)+2) > parseInt(jQuery('span.total-pages').html()))
				 return false;
				 
			current_page ++;
			jQuery('input[name="current_page"]').val(current_page);
			//function populate_list(args,table,page,plugin_alias,additional_params)
			populate_form_data_list(pagination_data.table_headers,pagination_data.table,pagination_data.page,'','',jQuery('input[name="wa_form_Id"]').val());
			}
		);
	
	jQuery('a.wafb-prev-page').live('click',
		function()
			{
			var current_page = parseInt(jQuery('input[name="current_page"]').val()) || 0;
			if(parseInt(pagination_data.current_page)<=0)
				 return false;
			
			current_page--;
			jQuery('input[name="current_page"]').val(current_page);
			populate_form_data_list(pagination_data.table_headers,pagination_data.table,pagination_data.page,'','',jQuery('input[name="wa_form_Id"]').val());
			}
		);
	jQuery('a.wafb-first-page').live('click',
		function()
			{
			var current_page = parseInt(jQuery('input[name="current_page"]').val()) || 0;	
			
			current_page = 0;
			jQuery('input[name="current_page"]').val(current_page);
			populate_form_data_list(pagination_data.table_headers,pagination_data.table,pagination_data.page,'','',jQuery('input[name="wa_form_Id"]').val());
			}
		);
		
	jQuery('a.wafb-last-page').live('click',
		function()
			{
			var current_page = parseInt(jQuery('input[name="current_page"]').val()) || 0;
			var current_page = parseInt(jQuery('span.total-pages').html())-1;
			jQuery('input[name="current_page"]').val(current_page);
			populate_form_data_list(pagination_data.table_headers,pagination_data.table,pagination_data.page,'','',jQuery('input[name="wa_form_Id"]').val());
			}
		);

	core_object.bind
		('update_form_entry',
		function(args)
			{
			
			var data = 	
				{
				action	 			: 'from_entries_table_pagination',
				plugin_alias		: jQuery('input[name="plugin_alias"]').val(),
				wa_form_Id 			: jQuery('input[name="wa_form_Id"]').val(),
				table_headers		: jQuery('input[name="fields"]').val(),
				page	 			: jQuery('input[name="page"]').val(),
				orderby	 			: jQuery('input[name="orderby"]').val(),
				order	 			: jQuery('input[name="order"]').val(),
				current_page		: jQuery('input[name="current_page"]').val(),
				additional_params	: jQuery('input[name="additional_params"]').val()
				};
				
			
			//jQuery('tbody#the-list').html('<small>Loading...  </small><img align="absmiddle" src="../wp-content/plugins/Core/images/icons/wpspin_light.gif"></td></tr>');			
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					jQuery('div.tablenav-pages').html(response);
					args = new Array();
		args['filter_name'] = 'featured_image';
		core_object.trigger('get_filter_Id', args);
					}
				);
			}
		);

	//show_iz_tooltip(jQuery('select#wap_wa_form_builder_Id'), 'Select a form to edit.');	
	
	jQuery('select[name="wap_wa_form_builder_Id"] option[value="0"]').attr('selected', true);
	
	disable_form_editor();
	
	//Show hide panels
	jQuery('div.iz-holder div.sidebar-name').toggle
		(
		function()
			{
			jQuery(this).next().hide();	
			},
		function()
			{
			jQuery(this).next().show();	
			}
		);

	jQuery('div.gears').live('click',
		function()
			{
			jQuery('div.group_element_attr').hide();
			
				
			var obj_pos	= jQuery(this).position();
				if(!obj_pos)
					return false;
				
				element_attr = jQuery(this).next('div.group_element_attr');
				
				element_attr.css({
					position:'absolute'
					});
				element_attr.css('left',(obj_pos.left+jQuery(this).outerWidth())-jQuery('div.group_element_attr').outerWidth());
				element_attr.css('top',obj_pos.top-jQuery('div.group_element_attr').outerHeight()-2);

			if(jQuery(this).hasClass('show'))
				{
				jQuery(this).removeClass('show')
				element_attr.hide();
				}
			else
				{
				jQuery('div.gears').each(
				function()
					{
					jQuery(this).removeClass('show');
					}
				);		
				element_attr.show();
				jQuery(this).addClass('show');
				}
			}
		);
			
	
				
/********************/

	
/********************/
/* Custom triggers  */
/********************/
	//Custom Event "update_list": Populate dropdown on insert, edit or delete with updated data
	/*core_object.bind
		(
		"update_list", function(args)
			{
			jQuery('select[name="parent_Id"]').html('<option>  loading...</option>');
		   
			var data =
				{
				action	 : 'populate_dropdown',
				table	 : document.addItem.table.value,
				ajax	 : true
				};
				
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					 jQuery('select[name="parent_Id"]').html(response);
					 jQuery('select[name="parent_Id"] option[value='+jQuery('input[name="selected_Id"]').val() +']').attr('selected',true);
					}
				);
			}
		);*/
	
	//Custom Event "update_custom_fields": Populate custom field widgets on edit, insert with updated data
	core_object.bind
		(
		"update_custom_fields", function(args)
			{
			show_iz_tooltip(jQuery('div.iz-custom-form-fields div.rightDrag:first'), (args['db_action']=='save') ? 'New field added.' : 'Field Updated');
			
			
			jQuery('div.widget').each
				(
				function()
					{
					if(jQuery(this).attr('data-group-label')==args['label_name'])
						{
						jQuery(this).children('div.widget-top').css('position','relative');
						jQuery(this).find('h4').css('position','relative');						
						jQuery(this).find('h4').css('z-index','3');
						jQuery(this).children('div.widget-top').prepend('<div class="highlight"></div>');
						jQuery(this).children('div.widget-top').find('div.highlight').fadeIn(800,
							function()
								{
								if(tutorial)
				add_tutorial_popup(jQuery('div.widget-liquid-right'),'<strong>Excelent!</strong> Now we have our first field. Note that new fields will always be added at the top of its type/category. <br /><br /><strong>Next</strong> we are going to add this field to a form.<br /><br /> Click on the field, hold and drag it over to the left-hand panel and release.<div class="spacer"></div><div class="extra_help go_below next nextstep">How do I edit or delete the field I\'ve just created?</div><div class="spacer"></div><div class="spacer"></div>', '<strong>Editing and deleting</strong><br /><br /><em>Editing a field</em><br />To edit a field, simply hover over it and click on "edit".<br/ > Then follow the same steps as when creating a new field.<br /><em>Note: This field will be changed on all forms saved!<br />Also note that fields just edited will always be added at the top of its type/category.</em><br /><br /><em>Deleting a Field</em><br />To delete a field, hover over it and click delete.<em>Note: This field will be deleted on all forms saved!</em>','',-50);
			
								jQuery(this).fadeOut(2500);
								}
							);
						}
					}
				);
			}
		);
	
	//Custom Event "update_form_fields": Populate form fields on edit, delete with updated data
	core_object.bind
		(
		"update_form_fields", function(args)
			{
			var data = 
				{
				action	 	: 'populate_form_fields',
				form_Id		: jQuery('div.form_Id').text(),
				edit_Id		: jQuery('div#edit_Id').text()
				};
			
			jQuery('div.drop-sort').html('<p class="description no-fields">Loading... </p>');
		
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					jQuery('div.drop-sort').html(response);	
					}
				);
			}
		);	
		
	core_object.bind
		(
		"update_default_fields", function(args)
			{
			var data = 
				{
				action	 	: 'populate_default_fields',
				plugin		: jQuery('div.form_Id').text()
				};
			//alert(jQuery('select#wap_wa_form_builder_Id').val());
			jQuery('div.drop-sort').html('<p class="description no-fields">Loading... </p>');
		
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					jQuery('div.drop-sort').html(response);	
					}
				);
			}
		);	
	core_object.bind
		(
		"update_module_fields", function(args)
			{
			var data = 
				{
				action	 	: 'populate_form_fields',
				form_Id		: jQuery('div.form_Id').text(),
				edit_Id		: jQuery('div#edit_Id').text()
				};
			
			jQuery('div.drop-sort').html('<p class="description no-fields">Loading... </p>');
		
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					jQuery('div.drop-sort').html(response);	
					}
				);
			}
		);	
/********************/
	
	
/*******************************************/
/* Creating / Edit / Delete custom fields  */
/*******************************************/
	//Reset selection
	jQuery('select[name="field-type"] option[value="0"]').attr('selected', true);
	
	
	if(tutorial)
		add_tutorial_popup(jQuery('.custom-fields.left'),'<strong>Welcome!</strong> First we are going to choose the<br> field type we want to create.<div class="spacer"></div><div class="extra_help go_right nextstep next">Thats great! but how do I know what field type to choose?</div><div class="spacer"></div><div class="spacer"></div><div class="spacer"></div>','When choosing a field type you need to consider the question your asking the user that will complete the form.<br /><br />Described below are the different field types you can create.</em><ul><li><strong>Text field</strong>:<br /> Textfields are used to capture specific information that only the user can provide i.e. "name" or "surname"<br /><br /><input type="text" value="Please enter your name" /></li><li><strong>Text Area</strong>:<br /> Text areas are used to capture large quantities information that only the user can provide i.e. "Biography" or "Residensial Address"<br /><br /><textarea>Please enter your Residensial Address</textarea></li><li><strong>Dropdown List</strong>:<br /> Dropdown list are used to capture a single value from a predifined option list i.e. "Gender" with options "male" and "female"<br /><br /><select><option>--- Select Gender ---</option><option>Male</option><option>Female</option></select></li><li><strong>Radio Button Group</strong>:<br /> Same as a dropdown list but displayes all options available i.e. "Gender" with options "male" and "female"<br /><br />Select Gender:<br /><input type="radio" name="gender"> Male<br /><input type="radio" name="gender"> Female<br /></li><li><strong>Check Box Group</strong>:<br /> Check boxes are used to capture multiple values of yes(checked) and no(unchecked) from a predifined list i.e. "House Features" with check fields such as "Pool","Garage","Remote Gate" etc<br /><br />House Features:<br /><input type="checkbox"> Pool<br /><input type="checkbox"> Garage<br /><input type="checkbox"> Remote Gate<br /></li><li><strong>Text Field Group</strong>:<br /> Text field groups are the same as text fields but are grouped together i.e. "Personal Details" with text fields such as "Name","Surname","Email" etc<br /><br />Personal details:<br /><input type="text" value="Name"><br /><input type="text" value="Surname"><br /><input type="text" value="Email"></li></ul>');
	
	//STEP 1 - Choose form element type ////////////////////////////////////////////////////////////////
	jQuery('select[name="field-type"]').change(
		function()
			{
			
			//Restore values on change
			if(jQuery(this).val()!='textgroup'){

				jQuery('div.gears').each
					(
					function()
						{
						jQuery(this).hide();
						}
					);	
				}
			else
				{
				jQuery('div.gears').each
					(
					function()
						{
						jQuery(this).show();
						}
					);		
				}
			get_backup();
			//Save new values on change
			save_backup();
			
			//Check for selection
			if(jQuery(this).val()!=0)					
				{
				
				//Display STEP 2 - Create element group //////////////////////////////////////////////////////////
				jQuery('.custom-fields-container.mid').addClass('active');
				jQuery('.custom-fields.right').html('');
				jQuery('.custom-fields-container.right').removeClass('active');
				
				//Get element group attributes
				var data = 
					{
					action	 			: 'create_group',
					input	 			: jQuery(this).val(),
					old_value	 		: jQuery('div.backup div.old-group-label').text()
					};		
				
				jQuery('.custom-fields.mid').html('<small>Loading...  </small>');
				
				jQuery.post
					(
					ajaxurl, data, function(response)
						{
						jQuery('.custom-fields.mid').html(response);
						jQuery('.custom-fields.mid input[type="text"]').val(format_illegal_chars(jQuery('div.backup div.group-label').html()));
						if(tutorial)
							add_tutorial_popup(jQuery('.custom-fields.mid'),'<strong>Very Good!</strong>...Now choose a name for your field and hit enter. <div class="spacer"></div><div class="extra_help go_right nextstep next">What is the "gear" icon for?</div><div class="spacer"></div><div class="spacer"></div>','<strong>Field Attributes!</strong><br /><span class="gears_example"></span><br />By clicking on the gears (settings) you\'ll find a few attributes that can be added to your field for form validation purposes:<ul><li><strong>Reqiured: (default "No")</strong><br />by selecting "yes" users must:<br /><br /><ul><li>Enter a value in case of a text field;</li><li>Enter a value in case of a text area;</li><li>Select a option value in case of a dropdown list</li><li>Select a option in case of a radio button group</li></ul><br /><em>Note: This is not avalable for check box groups</em></li><li><strong>Format: (default "text")</strong><br /><br /><ul><li><strong>Text</strong> - Allows all characters to be entered into field</li><li><strong>Email</strong> - Only a valid email address will be accepted by the field</li><li><strong>Number</strong> - Only a numbers will be accepted by the field.</li></ul><br /><em>Note: Only available for text fields and for each text field group item</em></ul>');
						jQuery('.custom-fields.mid input[type="text"]').focus();
						
						//Check input
						if(jQuery('input[name="group-label"]').val() !='')
							{
							//Display STEP 3 - Create element group options //////////////////////////////////////////////////////////
							add_group_options();
							add_new_option('');
							}
						}
					);
				}
			else
				{
				//Reset steps
				jQuery('.custom-fields.mid').html('');
				jQuery('.custom-fields.right').html('');
				jQuery('.custom-fields-container.mid').removeClass('active');
				jQuery('.custom-fields-container.right').removeClass('active');
				}
			}
		);
		
	//Delete custom field	
	jQuery('div.custom-field-actions span.delete span').click
		(
		function()
			{
			delete_custom_field( jQuery(this) );
			}
		);
	
	//Edit custom field	
	jQuery('div.custom-field-actions span.edit span').click
		(
		function()
			{
			clear_all();
			jQuery('span.db_action').text('Edit');
			edit_custom_field(jQuery(this));
				var obj_pos	= jQuery('div.widgets-holder-wrap').offset();
			jQuery("html, body").animate(
							{
								scrollTop:obj_pos.top
							},300
						)
			//Trigger custom event
			//core_object.trigger("update_form_fields");
			}
		);
/**********************************/
		
	
/*********************/
/* Selecting a from  */
/*********************/
	jQuery('select[name="wap_wa_form_builder_Id"]').change(
		function()
			{
			show_iz_tooltip(jQuery('div#available-widgets div.widget-holder p.description'), 'Drag a form element from here to the <br /> form on the left to add and order them.');	
			//Check selection	
			if(jQuery(this).val()=='0')
				{
				disable_form_editor();
				}
			else
				{
				enable_form_editor();
				jQuery('div.form_Id').text(jQuery(this).val());
				//Trigger custom event
				//alert('test 0');
				core_object.trigger("update_form_fields");
				//alert('test 1');
				core_object.trigger("update_default_fields");
				//alert('test 2');
				core_object.trigger("update_module_fields");
				//alert('test 3');
				}
			}
		);
/*********************/		


/*************************/
/* Drag / Drop and Sort  */
/*************************/	
		//Form creator
    create_droppable();
	
	
	/*jQuery('div.form_name span.go').click(
		function()
			{
			jQuery('.drop-sort').hide();
			
			var new_form_area = jQuery('.drop-sort.newform').clone();
			var unique_id = Math.round(Math.random()*99999999999);
			
			new_form_area.removeClass('newform');
			new_form_area.attr('id',unique_id);
			new_form_area.show();
			jQuery('div.primary-widget-area').prepend(new_form_area);
			
			jQuery('.opened_forms li').removeClass('active');
			
			jQuery('ul.forms').prepend('<li class="form active" data-form-id="'+ unique_id +'">'+ jQuery(this).parent().find('input').val() +'</li>');
			
			jQuery('.drop-sort.newform').html('');
			
			create_droppable();
			
			}
		);*/
	
	//jQuery('.opened_forms li.form').sortable();
	
				jQuery('span.add_description').live('click',
					function()
						{
						var get_description = jQuery(this).parent().find('div#help_text');
						if(jQuery(this).hasClass('edit'))
							{
							var set_value = '';
							var current_val = get_description.find('textarea').val();
							 for(i=0;i<current_val.length;i++)
								{
								if (current_val.charAt(i) == '\n')
									{
									set_value += '<br />';
									}
								else
									{
									set_value += current_val.charAt(i);
									}
								}
							get_description.html(set_value);
							get_description.find('textarea').remove(); 
							get_description.find('.save_help_text').remove(); 
							jQuery(this).removeClass('edit');
							}
						else
							{
							var set_value = get_description.html();
							get_description.html('');
							get_description.append('<textarea class="edit_description" style="min-height:250px;">'+ set_value.split('<br>').join('\n') +'</textarea><div class="sec_button save_help_text">Save</div>');
							jQuery(this).addClass('edit');
							}
						}
					);
				jQuery('div.error_msg').live('click',
					function()
						{
						if(jQuery(this).hasClass('edit'))
							{
							var set_value = jQuery(this).text();
							jQuery(this).text('');
							jQuery(this).append('<input type="text" class="edit_error_msg" value="'+ set_value +'"><span class="go"></span>');
							jQuery(this).find('input').focus();
							jQuery(this).removeClass('edit');
							}
						}
					);
				jQuery('span.set_error_message').live('click',
					function()
						{
						var get_msg = jQuery(this).parent().find('div.error_msg');
						
						if(jQuery(this).hasClass('edit'))
							{
							get_msg.hide();
							jQuery(this).removeClass('edit');
							
							}
						else
							{
							get_msg.show();
							get_msg.addClass('edit');
							jQuery(this).addClass('edit')
							get_msg.find('input').focus();
							}
						}
					);
				
				jQuery('input.edit_error_msg').live('change',
					function()
						{
						change_error_msg(jQuery(this).parent());
						}
					);
				jQuery('input.edit_error_msg').live('blur',
					function()
						{
						change_error_msg(jQuery(this).parent());
						}
					);
				function change_error_msg(get_msg)
					{
					
					get_msg.text(get_msg.find('input').val());
					get_msg.find('span.go').remove(); 
					get_msg.find('input').remove(); 
					get_msg.parent().parent().find('.set_error_message').removeClass('edit');
					get_msg.addClass('edit');
					get_msg.parent().parent().removeClass('show_actions');
					get_msg.hide();
					}
					
					
					
					
					
					
					
					
				jQuery('div.save_help_text').live('click',
					function()
						{
						jQuery(this).parent().parent().parent().removeClass('show_actions');
						jQuery(this).parent().parent().parent().find('.add_description').removeClass('edit');
						var get_description = jQuery(this).parent();
						var set_value = '';
						var current_val = get_description.find('textarea').val();
						 for(i=0;i<current_val.length;i++)
							{
							if (current_val.charAt(i) == '\n')
								{
								set_value += '<br />';
								}
							else
								{
								set_value += current_val.charAt(i);
								}
							}
						get_description.html(set_value);
						get_description.find('textarea').remove();
						jQuery(this).remove(); 			
						}
					);
			
				
				
				
				
				jQuery('div.save_dropdown_items').live('click',
					function()
						{
						jQuery(this).parent().hide();
						jQuery(this).parent().parent().find('.show_field_settings').removeClass('show');
						var items = jQuery(this).parent().find('textarea').val();
						var set_options = '<option value="0">-- Select --</option>';
						jQuery(this).parent().parent().find('div.get_dropdown_items').text(items);
						items = items.split('\n');
						for (var i = 0; i < items.length; i++)
							set_options += '<option value="'+ items[i] +'">'+ items[i] +'</option>';
							
						jQuery(this).parent().parent().find('select').html(set_options);	
						}
					);
					

				jQuery('div.save_date_format').live('click',
					function()
						{
						jQuery(this).parent().hide();
						}
					);
					
				
												
				
					
				jQuery('span.set_required input[name="required"]').live('click',
					function()
						{
							var obj = jQuery(this).parent().parent().parent();
							
							obj.removeClass('required').removeClass('not-required');				
							obj.find('span.is_required').text('');
							
							if(jQuery(this).attr('checked')=='checked')
								{
								obj.find('span.is_required').text('*');
								obj.addClass('required');
								}
							
						}
					);
					
				jQuery('span.show_field_settings').live('click',
					function()
						{
						if(jQuery(this).hasClass('show'))
							{
							jQuery(this).parent().find('div.field_settings').hide();
							jQuery(this).removeClass('show');
							}
						else
							{
							jQuery(this).parent().find('div.field_settings').show();
							jQuery(this).addClass('show');
							}
						}
					);
					
				jQuery('span.add_edit_items').live('click',
					function()
						{
						jQuery('ul.opt-list').html('');
						if(jQuery(this).hasClass('show'))
							{
							jQuery('div.custom-fields').hide();
							jQuery(this).removeClass('show');
							jQuery(this).parent().parent().removeClass('editing');
							}
						else
							{
							var element = jQuery(this).parent().parent();
							element.addClass('editing');
							if(element.hasClass('dropdown'))
								{
								element.find('option').each(
									function()
										{
										jQuery('ul.opt-list').append('<li class="iz-option"><input type="text" value="'+ jQuery(this).val() +'" style="width:200px;"><div class="remove" title="Remove option"></div><div class="sorthandle" title="Drag to position"></div></li>');
										//console.log(jQuery(this).val());
										}
									);
								}
							if(element.hasClass('radio_group'))
								{
								element.find('input[type="radio"].radio').each(
									function()
										{
										jQuery('ul.opt-list').append('<li class="iz-option"><input type="text" value="'+ jQuery(this).val() +'" style="width:200px;"><div class="remove" title="Remove option"></div><div class="sorthandle" title="Drag to position"></div></li>');
										//console.log(jQuery(this).val());
										}
									);
								}
							if(element.hasClass('check_group'))
								{
								element.find('input[type="checkbox"].check').each(
									function()
										{
										jQuery('ul.opt-list').append('<li class="iz-option"><input type="text" value="'+ jQuery(this).next('label').text() +'" style="width:200px;"><div class="remove" title="Remove option"></div><div class="sorthandle" title="Drag to position"></div></li>');
										//console.log(jQuery(this).val());
										}
									);
								}
			
			
							
			
							
							var pos = jQuery(this).offset();
							
							jQuery('div.custom-fields').show();
							jQuery('div.custom-fields').css('top',pos.top);
							jQuery('div.custom-fields').css('left',pos.left-jQuery('div#adminmenuback').outerWidth()-19);
							jQuery('div.custom-fields').find('div.save_items').attr('data-id',jQuery(this).parent().parent().attr('data-id'));
							jQuery('div.custom-fields').find('div.cancel_item_update').attr('data-id',jQuery(this).parent().parent().attr('data-id'));
							jQuery(this).addClass('show');
							}
						}
					);
				jQuery('div.custom-fields').hover(
					function()
						{
						var get_id = jQuery(this).find('div.save_items').attr('data-id');
						jQuery('.'+get_id).addClass('show_actions');
						}
					);
				//HEADINGS
				jQuery('div.form_field.heading').find('input').live('change',
					function(){ 
						change_heading(jQuery(this));
						}
					);
				jQuery('div.form_field.heading').find('input').live('blur',
					function(){ 
						change_heading(jQuery(this));
						}
					);
				
				jQuery('div.form_field.submit_button').find('input[type="text"]').live('blur',
					function(){ 
						jQuery('.iz-submit').removeClass('edit');
						jQuery(this).parent().parent().find('span.go').remove();
						jQuery(this).parent().parent().removeClass('show_actions');
						jQuery(this).remove();
						}
					);
				jQuery('div.form_field.submit_button').find('input[type="text"]').live('change',
					function(){ 
						jQuery(this).parent().parent().find('.iz-submit').text(jQuery(this).val());
						jQuery('.iz-submit').removeClass('edit');
						jQuery(this).parent().parent().find('span.go').remove();
						jQuery(this).parent().parent().removeClass('show_actions');
						jQuery(this).remove();
						}
					);
				jQuery('div.form_field.submit_button').find('input[type="text"]').live('keyup',
					function(){ 
						jQuery(this).parent().parent().find('.iz-submit').text(jQuery(this).val());
						}
					);	
				
				//TEXT PARAGRAPH
				jQuery('div.form_field.paragraph').find('textarea').live('change',
					function(){ 
						change_paragraph(jQuery(this));
						}
					);
				jQuery('div.form_field.paragraph').find('textarea').live('blur',
					function(){ 
						change_paragraph(jQuery(this));
						}
					);	
				//LABELS
				jQuery('input.edit_field_title').live('change',
					function(){ 
						change_label(jQuery(this));
						}
					);
				jQuery('input.edit_field_title').live('blur',
					function(){ 
						change_label(jQuery(this));
						}
					);
				
				
					
				jQuery('div.field_actions div.delete').live('click',
					function()	
						{
						if(jQuery(this).parent().hasClass('email'))
							{
							var mail_field = jQuery('#user_mail_'+ jQuery(this).parent().parent().find('.form_object label.title').attr('data-id'));
							mail_field.next('label').remove();
							mail_field.remove();
							}
						jQuery(this).parent().parent().parent().remove();
						}
					);
				jQuery('div.drop-sort div.form_field').live('hover',
					function()
						{
						//var actions = jQuery(this).find('div.field_actions');
						if(!visual_editing)
							{
							if(!jQuery(this).hasClass('editing'))
								{
								if(jQuery(this).hasClass('show_actions'))
									{
									jQuery(this).removeClass('show_actions');
									}
								else
									{
									jQuery(this).addClass('show_actions');
									}
								}
							}
						}
					);
				
											//ui.draggable.append('<div class="">Test</div>');
					//Sort
			/*************************/
					jQuery('ul.opt-list li div.remove').live('click',
						
						function()
							{
							jQuery(this).parent().remove();
							}
						);
					
					jQuery('div.cancel_item_update').live('click',
						function()	
							{
							jQuery('div.custom-fields').hide();
							jQuery('span.add_edit_items').removeClass('show');
							
							var get_data_id =  jQuery(this).attr('data-id');
							
							setTimeout(function()
								{ 
								jQuery('.'+ get_data_id).removeClass('editing');
								jQuery('.'+ get_data_id).removeClass('show_actions');
								},50);
							}
						);
						
					jQuery('div.save_items').live('click',
						function()
							{
							
							jQuery('div.custom-fields').hide();
							jQuery('span.add_edit_items').removeClass('show');
							
							var get_data_id =  jQuery(this).attr('data-id');
							
							setTimeout(function()
								{ 
								jQuery('.'+ get_data_id).removeClass('editing');
								jQuery('.'+ get_data_id).removeClass('show_actions');
								},50);
								
							var colors 			= ["#FF0000","#FFBF00", "#8F49FF", "#FF00BF", "#F3FF35", "#A5FF4B", "#00FF40", "#0F5BFF", "#76FFE9", "#FF5F49", "#3EBDFF"];
							var errors 			= 0;
							var opt_array 		= new Array();
							var opt_obj 		= new Object();	
							
							
							
							jQuery('ul.opt-list li input[type="text"]').each
								(
								function(index)
									{
									opt_array[index] = jQuery(this).val();
									}
								);
						
											
								for(var x=0; x<opt_array.length; x++) {
									var ob = opt_array[x];
								}
								
								
								var duplicates = find_duplicates(opt_array);
								
								//Check for empty group element option array
								if(opt_array.length==0)
									{
									show_iz_tooltip(jQuery('div.save_items'),'Item list can not be empty!');
									errors++;
									}
								
								//Check duplicate option values in options
								if(duplicates.length>0)
									{
									show_iz_tooltip(jQuery('div.save_items'),'Duplicate items found!');
						
									jQuery('ul.opt-list li input[type="text"]').each
										(
										function(index)
											{
											var dup_match = jQuery.inArray(jQuery(this).val(),duplicates);
											
											if(dup_match>=0)	
												jQuery(this).css('border','1px solid ' + colors[dup_match]);
											else 
												jQuery(this).css('border','1px dashed #E7E7E7');	
											}
										);
									errors++;
									}
							if(errors>0)
								return;
								
							///AFTER CHECKS
							var element = jQuery('.'+jQuery(this).attr('data-id'));
							
							if(element.hasClass('dropdown'))
								{
								var dropdown = element.find('select');
								dropdown.html('');
								jQuery('ul.opt-list li input[type="text"]').each
									(
									function(index)
										{
										dropdown.append('<option value="'+ jQuery(this).val()+'">' + jQuery(this).val() + '</option>');
										}
									);
								}	
							
							if(element.hasClass('radio_group'))
								{
								var radio_group = element.find('div.radio_button_group');
								var title = format_illegal_chars(element.find('label.title').text());
								radio_group.html('');
								jQuery('ul.opt-list li input[type="text"]').each
									(
									function(index)
										{
										radio_group.append('<div><input type="radio" name="' + title + '" value="'+ jQuery(this).val()+'" class="radio"><label>'+ jQuery(this).val()+'</label></div>');
										}
									);
								}
								
							if(element.hasClass('check_group'))
								{
								var check_group = element.find('div.check_box_group');
								check_group.html('');
								jQuery('ul.opt-list li input[type="text"]').each
									(
									function(index)
										{
										check_group.append('<div><input type="checkbox" name="'+ format_illegal_chars(jQuery(this).val()) +'" class="check"><label>'+ jQuery(this).val()+'</label></div>');
										}
									);
								}	
							}
					);
		
		
			jQuery(	'div.overall_styling, div.drop-sort, div.drop-sort input, div.drop-sort div.field_holder, div.drop-sort .error_msg, div.drop-sort textarea, div.drop-sort select, div.drop-sort label.title, div.drop-sort p, div.drop-sort hr, div.drop-sort h1,div.drop-sort h2,div.drop-sort h3, div.drop-sort h4, div.drop-sort h5, div.drop-sort h6').live('hover',
				function()
					{
					jQuery('div, label, input, select, textarea, p, h1, h2, h3, h4, h5, h6, hr').removeClass('show_outline');
					if(visual_editing)
						{
						if(jQuery(this).hasClass('show_outline'))
							jQuery(this).removeClass('show_outline');
						else
							jQuery(this).addClass('show_outline');
						}
					}
				);
			
			
		jQuery('div.form_object label.title').live('click',
		function()
			{
			if(!visual_editing)
				{	
				if(!jQuery(this).hasClass('edit'))
					{
					var set_value = jQuery(this).text();
					jQuery(this).text('');
					jQuery(this).parent().prepend('<input data-id="'+ jQuery(this).attr('data-id') + '" class="edit_field_title" type="text" value="'+ set_value +'"><span class="go"></span>');
					jQuery(this).parent().find('input.edit_field_title').focus(function() { jQuery(this).select() });
					
					jQuery(this).parent().find('input.edit_field_title').trigger('focus');
					
					jQuery(this).addClass('edit');
					}
				}
			}
		);
		
		jQuery('div.form_object .heading').live('click',
			function()
				{
				if(!visual_editing)
					{
					if(!jQuery(this).hasClass('edit'))
						{
						var set_value = jQuery(this).text();
						jQuery(this).text('');
						jQuery(this).parent().append('<input data-id="" class="edit" type="text" value="'+ set_value +'"><span class="go"></span>');
						jQuery(this).parent().find('input').focus(function() { jQuery(this).select() });
						jQuery(this).parent().find('input').trigger('focus');
						jQuery(this).addClass('edit');
						}
					}
				}
			);
			
		jQuery('div.form_object .iz-submit').live('click',
			function()
				{
				if(!visual_editing)
					{
					if(!jQuery(this).hasClass('edit'))
						{
						var set_value = jQuery(this).text();
						//jQuery(this).text('');
						jQuery(this).parent().prepend('<input data-id="'+ jQuery(this).attr('data-id') + '" class="edit_submit" type="text" value="'+ set_value +'"><span class="go"></span>');
						jQuery(this).parent().find('input.edit_submit').focus(function() { jQuery(this).select() });
						
						jQuery(this).parent().find('input.edit_submit').trigger('focus');
						
						jQuery(this).addClass('edit');
						}
					}
				}
			);
		
		jQuery('div.form_object p.text_paragraph').live('click',
			function()
				{
				if(!visual_editing)
					{
					if(!jQuery(this).hasClass('edit'))
						{
						var set_value = jQuery(this).html();
						jQuery(this).text('');
						jQuery(this).parent().append('<textarea data-id="" class="edit" style="min-height:250px;">'+ set_value.split('<br>').join('\n') +'</textarea>');
						jQuery(this).parent().find('textarea').focus(function() { jQuery(this).select() });
						jQuery(this).parent().find('textarea').trigger('focus');
						jQuery(this).addClass('edit');
						}
					}
				}
			);
		
			
		}
);


function create_droppable(){
	var dropSort 	= jQuery('div.drop-sort div.form_container');
        var rightDrag 	= jQuery('div#widget-list .field');
		
		
		
		//Drag
        rightDrag.draggable(
			{
			drag: function( event, ui ) { jQuery('div.drop-sort').parent().addClass('drag'); },
			stop: function( event, ui ) { jQuery('div.drop-sort').parent().removeClass('drag'); },
			stack  : '.draggable',
			revert : 'invalid', 
			tolerance: 'intersect',
			connectToSortable:dropSort,
			snap:false,
			helper : 'clone',
			containment:'.colmask'
			}
        );
		
		dropSort.droppable(
        	{
            drop   		: function(event, ui)
							{ 
							jQuery('p.description.no-fields').remove();
							
							if(move_to_container(ui.draggable, jQuery(this)))
								{
								ui.draggable.removeClass('field');
								
								
									
									ui.draggable.find('div.draggable_object').hide();
									var form_object = ui.draggable.find('div.form_object');
									var rand_id = ui.draggable.attr('data-id');
									form_object.show();
									
									if(ui.draggable.hasClass('heading') || ui.draggable.hasClass('paragraph') || ui.draggable.hasClass('divider'))
										{
										
										}
									else
										{
										ui.draggable.addClass('field_holder');
										ui.draggable.attr('id','field_holder__' + rand_id).addClass('field_holder__'+rand_id);
										}
									
									form_object.find('label.title').attr('data-id',rand_id);
									
									//form_object.find('div.help_text')		.attr('id','help_text__' + rand_id).addClass('help_text__'+rand_id);
									
									form_object.find('label.title')				.attr('id','title__' + rand_id).addClass('title__' + rand_id);
									form_object.find('div.error_msg')			.attr('id','error_msg__'+rand_id).addClass('error_msg__'+rand_id);
									form_object.find('input.text')				.attr('id','input_text__'+rand_id).addClass('input_text__'+rand_id);
									form_object.find('input.auto')				.attr('id','input_auto__'+rand_id).addClass('input_auto__'+rand_id);
									form_object.find('input.file')				.attr('id','input_file__'+rand_id).addClass('input_file__'+rand_id);
									form_object.find('input.date')				.attr('id','input_date__'+rand_id).addClass('input_date__'+rand_id);
									form_object.find('input.time')				.attr('id','input_time__'+rand_id).addClass('input_time__'+rand_id);
									form_object.find('input.email')				.attr('id','input_email__'+rand_id).addClass('input_email__'+rand_id);
									form_object.find('input.phone_number')		.attr('id','input_phone_number__'+rand_id).addClass('input_phone_number__'+rand_id);
									form_object.find('input.url')				.attr('id','input_url__'+rand_id).addClass('input_url__'+rand_id);
									form_object.find('input.text_only')			.attr('id','input_text_only__'+rand_id).addClass('input_text_only__'+rand_id);
									form_object.find('input.numbers_only')		.attr('id','input_numbers_only__'+rand_id).addClass('input_numbers_only__'+rand_id);
									form_object.find('div.radio_group')			.attr('id','radio_group__'+rand_id).addClass('radio_group__'+rand_id);
									form_object.find('div.check_group')			.attr('id','check_group__'+rand_id).addClass('check_group__'+rand_id);
									form_object.find('textarea.textarea')		.attr('id','textarea__'+rand_id).addClass('textarea__'+rand_id);
									form_object.find('select.dropdown')			.attr('id','dropdown__'+rand_id).addClass('dropdown__'+rand_id);
									form_object.find('input[name="required"]')	.attr('id','required__'+rand_id);
									form_object.find('label.required')			.attr('for','required__'+rand_id);
									
									
									
									
									
									//jQuery('input.single_check').button();	
									var get_offset = ui.draggable.find( 'div#help_text' ).offset();
									ui.draggable.find( 'div#help_text' ).tooltip(
										{
											 position: {
												my: "center bottom",
												at: "center top",
											 }
										});
								}
							jQuery(this).parent().removeClass('over');							
						  	},
            over        : function(){jQuery(this).parent().addClass('over')},
            out         : function(){jQuery(this).parent().removeClass('over')},	  
            tolerance 	: 'fit',
			helper 		: 'clone'	
        }).sortable(
			{
			start : function(event, ui){ 
				jQuery('div.form_field').addClass('editing');
				jQuery('div.form_field').removeClass('show_actions');
				
			}, 
			stop : function(event, ui){ 
				jQuery('div.form_field').removeClass('show_actions');
				jQuery('div.form_field').removeClass('editing');
			},           
			placeholder: 'iz-placeholder',
			forcePlaceholderSize : true
			}
		);
}
/*******************************************/
/*******************************************/
/**************** LIVE EDIT ****************/
/*******************************************/
/*******************************************/
function change_label(obj){
	var new_title = obj.val();
	jQuery('label.title').removeClass('edit');
	//alert(obj.parent().attr('class'));
	if(!obj.parent().hasClass('setting'))
		jQuery('label.title.title__'+obj.attr('data-id')).text(obj.val()); 
	obj.parent().parent().removeClass('show_actions');
	if(jQuery('.'+obj.attr('data-id')).hasClass('radio_group'))
		{
		jQuery('.'+obj.attr('data-id')).find('input[type="radio"]').each(
			function()
				{
				jQuery(this).attr('name',format_illegal_chars(new_title));	
				}
			);
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('textarea'))
		{
		jQuery('.'+obj.attr('data-id')).find('textarea.textarea').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('dropdown'))
		{
		jQuery('.'+obj.attr('data-id')).find('select').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('radio'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.single_radio').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('check'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.single_check').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('file'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.file').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('auto'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.auto').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('text'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.text').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('date'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.date').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('time'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.time').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('email'))
		{
		var user_mails = jQuery('.email_setup__'+jQuery('.current_canvas').text() + ' fieldset.mail_to_user_address div.iz-form-item')
		var id = obj.attr('data-id');
		var input = user_mails.find('input#user_mail_'+ id);	
		
		//console.log('input#user_mail_'+ id);
		
		input.val(format_illegal_chars(new_title));
		input.next('label').text(new_title);
		
		jQuery('.'+obj.attr('data-id')).find('input.email').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('phone'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.phone_number').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('text_only'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.text_only').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('num_only'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.numbers_only').attr('name',format_illegal_chars(new_title));	
		}
	if(jQuery('.'+obj.attr('data-id')).hasClass('url'))
		{
		jQuery('.'+obj.attr('data-id')).find('input.url').attr('name',format_illegal_chars(new_title));	
		}
		
		
	if(!obj.parent().hasClass('setting'))
		{
		obj.parent().find('span.go').remove();	
		obj.remove(); 
		}
}

function change_heading(obj){
	jQuery('.heading').removeClass('edit');
	obj.parent().parent().find('.heading').text(obj.val());
	obj.parent().parent().find('span.go').remove();
	obj.remove(); 
}

function change_paragraph(obj){
	jQuery('p.text_paragraph').removeClass('edit');
	var set_value = '';
	var current_val = obj.val();
	 for(i=0;i<current_val.length;i++)
		{
		if (current_val.charAt(i) == '\n')
			{
			set_value += '<br />';
			}
		else
			{
			set_value += current_val.charAt(i);
			}
		}
	obj.parent().parent().find('p.text_paragraph').html(set_value);
	obj.remove(); 
}








/*******************************************/
/************* Form Functions **************/
/*******************************************/

function delete_form_entry(last_update,Id){
	
	var get_color = jQuery('tr#tag-'+Id).css('background-color');
	jQuery('tr#tag-'+Id).css('background-color','#FFEBE8');
	if(confirm('Are your sure you want to permanently delete this record?'))
		{
		jQuery('tr#tag-'+Id).fadeOut('slow', function()
			{
			jQuery('tr#tag-'+Id).remove();	
			}
		);
		
		var data = 	
			{
			action	 	: 'delete_form_entry',
			last_update	: last_update
			};

		jQuery.post
			(
			ajaxurl, data, function(response)
				{ 
				core_object.trigger("update_form_entry"); 
				}
			);
		}
	else
		{
		jQuery('tr#tag-'+Id).css('background-color',get_color);
		}
}

function populate_form_data_list(args,table,page,plugin_alias,additional_params,form_Id){
	/*
	action	 			: 'populate',
		args	 			: args,
		page	 			: page,
		table				: table,
		orderby				: orderby,
		order				: order,
		current_page 		: current_page,
		additional_params	: additional_params
	*/
	//alert(form_Id);
	var data = 	
		{
		plugin_alias		: (jQuery('input[name="plugin_alias"]').val()) ? jQuery('input[name="plugin_alias"]').val() : plugin_alias,
		action	 			: 'populate_form_data_list',
		table	 			: 'wap_x_forms_meta',
		args				: (jQuery('input[name="fields"]').val()) || jQuery('input[name="table_headers"]').val() || args,
		page	 			: (jQuery('input[name="page"]').val()) ? jQuery('input[name="page"]').val() : page,
		order	 			: jQuery('input[name="order"]').val(),
		orderby	 			: jQuery('input[name="orderby"]').val(),
		current_page		: jQuery('input[name="current_page"]').val(),
		additional_params	: (jQuery('input[name="additional_params"]').val()) ? jQuery('input[name="additional_params"]').val() : additional_params,
		form_Id				: form_Id
		//args				: (jQuery('input[name="table_headers"]').val()) ? jQuery('input[name="table_headers"]').val() : args
		};
	jQuery('tbody#the-list').html('<tr><td></td><td colspan="2"><small>Loading...  </small></td></tr>');			
	jQuery.post
		(
		ajaxurl, data, function(response)
			{
			jQuery('tbody#the-list').html(response);
			core_object.trigger("update_form_entry");
			refreshHiddenColumns();
			//Reset form on insert or update
			
			resetSliderPositions(jQuery('table.resiable-columns'));
			if(jQuery('input[name="edit_Id"]').val()=='' || jQuery('input[name="edit_status"]').val()=='done')
				{
				//reset_form();
				}
			}
		);
}


function move_to_container(theObj, newContainer) {
	
	//var bool = true;
	
	//Loop through the panel and check for duplicates
	/*jQuery('div.drop-sort div.iz-sortable').each
		(
		function(index)
			{
			if(theObj.attr('data-group-label').toLowerCase() ==  jQuery(this).attr('data-group-label').toLowerCase())
				{
				show_iz_tooltip(jQuery('div.drop-sort'),'Duplicate fields not allowed.');
				bool = false;
				}
			}
		);
		*/
	//if(!bool) 
	//	return bool
	//
	//Clone draggable
	
	var set_Id = '_' + Math.round(Math.random()*99999);
	
	//Creat unique identifier
	
	
	if(theObj.hasClass('wa-sortable'))
		return;
	
	jQuery(theObj).attr('data-id', set_Id);
	
	jQuery(theObj).addClass(set_Id);
	
	theObj.addClass('wa-sortable');

	return true;         
}	

function setup_draggable(newObj) {
	
	//Add a 'remove' button for the deletion of the object/field group
	newObj.find('div.widget-title-action').empty();
	var leftDrag  = jQuery('div#widgets-left div.widgets-holder-wrap div.leftDrag');
	//Remove the object if the remove button is clicked
	jQuery('div#widgets-left div.widgets-holder-wrap div.leftDrag div.removeObj a').click
		(
		function()
			{
			var theDraggable = jQuery(this).parent().parent();
			var panel		 = jQuery(this).parent().parent().parent();
	
			theDraggable.remove();
			removeCoords(theDraggable, panel);
			}
		);
}

function disable_form_editor(){
	jQuery('div.iz-custom-form-fields div.ui-draggable').removeClass('rightDrag');
	jQuery('span.save_form_as').fadeIn('fast');
	//jQuery('div.drop-sort').html('<p class="description no-fields  no-form"><strong>Create</strong> a <strong>new form</strong> by simply <strong>dragging fields</strong> from the Custom fields sidebar on the right and <strong>dropping them here</strong>. Give the form a name and click <strong> "Save" </strong> above!</p>');
	jQuery('input#iz-save-form').removeClass('editing');
	jQuery('input#iz-save-form').addClass('new_form');
	
}

function enable_form_editor(){
	jQuery('div.iz-custom-form-fields div.ui-draggable').addClass('rightDrag');
	jQuery('span.save_form_as').fadeOut('fast');
	jQuery('input#iz-save-form').removeClass('new_form');
	jQuery('input#iz-save-form').addClass('editing');
	//jQuery('input#iz-save-form').attr('disabled',false);
}

function remove_custom_group(obj){
	jQuery(obj).parent().remove();
}
/****************************************************/




/****************************************************/
/************* Custom Field Functions ***************/
/****************************************************/

//STEP 2 - Create group
function add_group_options(){
	
	var group_label = format_illegal_chars(jQuery('input[name="group-label"]').val());
	var label_exists = false;
	var db_action = jQuery('span.db_action').text();

	if(group_label !='')
		{
		if(db_action!='Edit')
			{
			jQuery('div#widgets-right div.widget-top div.widget-title h4').each(
				function(index)
					{
					var current_label = jQuery(this).text();
					if(current_label.toLowerCase() == group_label.toLowerCase())
						{
						label_exists = true;
						show_iz_tooltip(jQuery('input[name="group-label"]'),'Sorry, Group with label name "'+ group_label +'" already exists');
						}
					}
				);
			}
		
		if(label_exists==false)
			{
			
				///Restore values on change
				get_backup();
				//Save new values on change
				save_backup();
				
				jQuery('.custom-fields-container.right').addClass('active');
				
				var data = 
					{
					action	 	: 'add_group_options',
					fieldtype	: jQuery('select[name="field-type"]').val(),
					required	: jQuery('input[name="field-req"]').val(),
					label 		: format_illegal_chars(jQuery('input[name="group-label"]').val())
					};		
				
				jQuery('.custom-fields.right').html('<small>Loading...  </small>');
					
				if(tutorial)
					{
					if(jQuery('select[name="field-type"]').val()=='text' || jQuery('select[name="field-type"]').val()=='textarea')
						add_tutorial_popup(jQuery('.custom-fields-container.right'),'<strong>Well done!</strong>...Now click the "Save Field" button below and presto!');
					if(jQuery('select[name="field-type"]').val()=='dropdown')
						add_tutorial_popup(jQuery('.custom-fields-container.right'),'<strong>Well done!</strong>...You\'ve you selected a dropdown list and therefor you\'ll need to add some options for the user to select.<br /><br />To add a option type a n value in the field labeled "Add New" below and hit enter.<br /><br /> When your done click on "Save Field" at the bottom.<div class="spacer"></div><div class="extra_help go_below next nextstep">What else should I know?</div><div class="spacer"></div><div class="spacer"></div>','<strong>Sorting and removing</strong><br />After creating a field, you\'ll notice an "up/down arrow icon" and a "X".<br /><br /><em>Sorting</em><br />You can sort the order of these field items if you click,hold then drag the item up and down using the "up/down icon"<br /><br /><em>Removing</em><br />Remove unwanted field items by clicking the "X" next to it.','',0,-30);
					if(jQuery('select[name="field-type"]').val()=='radio')
						add_tutorial_popup(jQuery('.custom-fields-container.right'),'<strong>Well done!</strong>...You\'ve you selected a radio button list and therefor you\'ll need to add some radio buttons for the user to choose from.<br /><br />To add a button type a n value in the field labeled "Add New" below and hit enter.<br /><br /> When your done click on "Save Field" at the bottom.<div class="spacer"></div><div class="extra_help go_below next nextstep">What else should I know?</div><div class="spacer"></div><div class="spacer"></div>','<strong>Sorting and removing</strong><br />After creating a field, you\'ll notice an "up/down arrow icon" and a "X".<br /><br /><em>Sorting</em><br />You can sort the order of these field items if you click,hold then drag the item up and down using the "up/down icon"<br /><br /><em>Removing</em><br />Remove unwanted field items by clicking the "X" next to it.','',0,-30);
					if(jQuery('select[name="field-type"]').val()=='textgroup')
						add_tutorial_popup(jQuery('.custom-fields-container.right'),'<strong>Well done!</strong>...You\'ve you selected a Textfield group and therefor you\'ll need to add some text fields for the user to complete.<br /><br />To add a textfield type a n value in the field labeled "Add New" below and hit enter.<br /><br /> When your done click on "Save Field" at the bottom.<div class="spacer"></div><div class="extra_help go_below next nextstep">What else should I know?</div><div class="spacer"></div><div class="spacer"></div>','<strong>Sorting and removing</strong><br />After creating a field, you\'ll notice an "up/down arrow icon" and a "X".<br /><br /><em>Sorting</em><br />You can sort the order of these field items if you click,hold then drag the item up and down using the "up/down icon"<br /><br /><em>Removing</em><br />Remove unwanted field items by clicking the "X" next to it.','',0,-30);
					if(jQuery('select[name="field-type"]').val()=='check')
						add_tutorial_popup(jQuery('.custom-fields-container.right'),'<strong>Well done!</strong>...You\'ve selected a Checkbox group and therefor you\'ll need to add some check boxes for the user to tick.<br /><br />To add a check box type a n value in the field labeled "Add New" below and hit enter.<br /><br /> When your done click on "Save Field" at the bottom.<div class="spacer"></div><div class="extra_help go_below next nextstep">What else should I know?</div><div class="spacer"></div><div class="spacer"></div>','<strong>Sorting and removing</strong><br />After creating a field, you\'ll notice an "up/down arrow icon" and a "X".<br /><br /><em>Sorting</em><br />You can sort the order of these field items if you click,hold then drag the item up and down using the "up/down icon"<br /><br /><em>Removing</em><br />Remove unwanted field items by clicking the "X" next to it.','',0,-30);
					}
					
				jQuery.post
					(
					ajaxurl, data, function(response)
						{
						if(response.length>200 && jQuery('div.backup div.group-options').html()!='')
							{
							jQuery('.custom-fields.right').html(jQuery('div.backup div.group-options').html());
							} 
						else
							{
							jQuery('.custom-fields.right').html(response);
							}
						jQuery('.custom-fields.right').show();
						add_new_option('');
						jQuery('.custom-fields.right input[type="text"]:first').focus();
						}
					);	
				
			
			}
		else
			{
			jQuery('.custom-fields.right').hide();
			jQuery('.custom-fields-container.right').removeClass('active');
			}
		}
	else
		{
		jQuery('.custom-fields.right').hide();
		jQuery('.custom-fields-container.right').removeClass('active');
		}
}

//STEP 3 - Add group options
function add_new_option(input) {
	
	if(input)
		{
		
			var unique_Id = '__' + Math.round(Math.random()*99999) +'__';
			var custum_field_items = '';
				custum_field_items += '<div class="group_element_attr" style="display:none;">';
				custum_field_items += '<label>Required</label> <input data-attr="required" type="radio" name="'+ unique_Id +'field-req" value="required">&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;';
				custum_field_items += '<input data-attr="required" type="radio" name="'+ unique_Id +'field-req" value="" checked="checked">&nbsp;&nbsp;No<br />';
				custum_field_items += '<div class="devider"></div>';
				///custum_field_items += '<label>Visibility</label><input data-attr="visibility" type="radio" name="'+ unique_Id +'field-visibility" value="Private">&nbsp;&nbsp;Private&nbsp;&nbsp;&nbsp;&nbsp;';
				////custum_field_items += '<input data-attr="visibility" type="radio" name="'+ unique_Id +'field-visibility" value="Public" checked="checked">&nbsp;&nbsp;Public<br />';
				//custum_field_items += '<div class="devider"></div>';
				custum_field_items += '<label>Format</label><input data-attr="format" type="radio" name="'+ unique_Id +'field-format" value="text" checked="checked">&nbsp;&nbsp;Text&nbsp;&nbsp;&nbsp;&nbsp;';
				custum_field_items += '<input data-attr="format" type="radio" name="'+ unique_Id +'field-format" value="email">&nbsp;&nbsp;Email&nbsp;&nbsp;&nbsp;&nbsp;';
				custum_field_items += '<input data-attr="format" type="radio" name="'+ unique_Id +'field-format" value="number">&nbsp;&nbsp;Number';
				custum_field_items += '</div>';	
				
			jQuery('ul.opt-list').append('<li class="iz-option"><input style="'+((jQuery('select[name="field-type"]').val()=="textgroup") ? 'width:165px' : 'width:200px;' )+'" data-old-val="'+ jQuery(input).val() +'" type="text" value="'+ jQuery(input).val() +'">'+ ((jQuery('select[name="field-type"]').val()=="textgroup") ? '<div class="gears"></div>'+ custum_field_items : '') +'<div title="Remove option" class="remove"></div><div title="Drag to position" class="sorthandle"></div></li>');
			jQuery(input).val('');
			jQuery('ul.opt-list').keypress
				(
				function(event)
					{
					if(event.which==13)
						{
						event.preventDefault();	
						}
					}
				);
			
		}
	
	
	
	jQuery('ul.opt-list').scrollTop(10000);
}
function format_illegal_chars(input_value){
	
	input_value = input_value.toLowerCase();
	if(input_value=='name' || input_value=='page' || input_value=='post' || input_value=='id')
		input_value = '_'+input_value;
		
	var illigal_chars = '-+=!@#$%^&*()*{}[]:;<>,.?~`|/\'';
	
	var new_value ='';
	
    for(i=0;i<input_value.length;i++)
		{
		if (illigal_chars.indexOf(input_value.charAt(i)) != -1)
			{
			input_value.replace(input_value.charAt(i),'');
			}
		else
			{
			if(input_value.charAt(i)==' ')
			new_value += '_';
			else
			new_value += input_value.charAt(i);
			}
		}
	return new_value;	
}
//Check for duplicate option values
function find_duplicates(arr) {
	var len			= arr.length,
		duplicates	= [],
		counts		= {};
	
	for (var i=0;i<len;i++)
		{
		if(arr[i].val)
			var item 	= arr[i].val; //I just changed this from arr[i] to arr[i].val, because we now pass it an object and not an array
		else
			var item 	= arr[i];
		
		var count 	= counts[item];
		
		counts[item] = counts[item] >= 1 ? counts[item] + 1 : 1;
		}
	
	for(var item in counts)
		{
		if(counts[item] > 1)
	  		duplicates.push(item);
		}
	return duplicates;
}

//Get input values and save
function save_backup(){
	jQuery('div.backup div.group-label').html( jQuery('input[name="group-label"]').val() );
	if(jQuery('.custom-fields.right').html().length>200)
		{
		jQuery('div.backup div.group-options').html( jQuery('.custom-fields.right').html() );
		}
}

//Restore in put values on change or edit
function get_backup(){
	if(jQuery('div.backup div.group-options').html()!='')
		{
		jQuery('.custom-fields.right').html(jQuery('div.backup div.group-options').html());
		jQuery('.custom-fields.right').show();
		}
	if(jQuery('div.backup div.group-label').html()!='')
		{
		if(jQuery('input[name="group-label"]').val()==jQuery('div.backup div.group-label').html())
			{
			jQuery('input[name="group-label"]').val( jQuery('div.backup div.group-label').html() );
			}
		}
}

//Populates all steps with saved data
function edit_custom_field(obj){
	
	//clear_all();
	
	jQuery('select[name="field-type"] option[value="0"]').attr('selected', true);
	
	var data = 	
		{
		action	 	: 'edit_custom_field',
		grouplabel 	: format_illegal_chars(obj.attr("data-group-label"))
		};
	
	jQuery.post
		(
		ajaxurl, data, function(response)
			{ 
			var field = jQuery.parseJSON(response); 
			
			jQuery('div.backup div.old-group-label').text(field.grouplabel);
			 
			jQuery('div.backup div.group-label').text(field.grouplabel);
			jQuery('input[name="group-label"]').val(field.grouplabel);
			
			switch(	field.type )
				{
				case 'textgroup':
				case 'check':
					jQuery('select[name="field-type"] optgroup.single-fields').attr('disabled',true);
				break;	
				default:
					jQuery('select[name="field-type"] optgroup.multi-fields').attr('disabled',true);
				break;
				}
				
			jQuery('select[name="field-type"] option[value="'+field.type+'"]').attr('selected', true);
			jQuery('select[name="field-type"]').trigger('change');
			
			var unique_Id;
			
			var custum_field_items  = '';
			
			var defualt_field  	 = '';
				defualt_field 	+= '<input name="iz-add-option" class="iz-add-options" type="text" placeholder="'+ ((field.type=='textgroup' || field.type=='check') ? 'Add new item and hit enter' : 'Add new option and hit enter' ) + '" onfocus="if(this.value==\'\'){ show_iz_tooltip(this,\'Enter a value and hit enter\'); }" onchange="add_new_option(this);">';
				defualt_field 	+= '<div class="iz-spacer"></div>';
				defualt_field 	+= '<ul class="opt-list iz-sortable ui-sortable"></ul>';
				defualt_field 	+= '<div class="iz-spacer"></div><input name="save-field-button" class="button" type="button" value="   Update Field   " onclick="save_field(this,\'update\',format_illegal_chars(jQuery(\'input[name=group-label]\').val()));">  <input class="button" type="button" value=" Cancel " onclick="clear_all();"><div style="float:right;padding-top:4px;"><a style="text-decoration:none;" href="javascript:clear_option_list();">Clear List</a></div>';

			jQuery('div.group-options').html(defualt_field);
			
			if(field.items)
				{
				for(option in field.items.reverse() )
					{
					if(field.items[option].val)
						{
						unique_Id = '__' + Math.round(Math.random()*99999) +'__';
						//console.log(field.items[option].visibility);
						//alert(field.items[option].required	);
						custum_field_items += '<div class="group_element_attr" style="display:none;">';
						custum_field_items += '<label>Required</label>';
						custum_field_items += '<input data-attr="required" '+((field.items[option].required		=='required') 	? 	'checked="checked"' : '' )+' type="radio" name="'+ unique_Id +'field-req" value="required">&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;';
						custum_field_items += '<input data-attr="required" '+((field.items[option].required		=='') 			? 	'checked="checked"' : '' )+' type="radio" name="'+ unique_Id +'field-req" value="">&nbsp;&nbsp;No<br />';
						custum_field_items += '<div class="devider"></div>';
						custum_field_items += '<label>Visibility</label>';
						custum_field_items += '<input data-attr="visibility" '+((field.items[option].visibility	=='Private') 	? 	'checked="checked"' : '' )+' type="radio" name="'+ unique_Id +'field-visibility" value="Private">&nbsp;&nbsp;Private&nbsp;&nbsp;&nbsp;&nbsp;';
						custum_field_items += '<input data-attr="visibility" '+((field.items[option].visibility	=='Public') 	? 	'checked="checked"' : '' )+' type="radio" name="'+ unique_Id +'field-visibility" value="Public">&nbsp;&nbsp;Public<br />';
						custum_field_items += '<div class="devider"></div>';
						custum_field_items += '<label>Format</label>';
						custum_field_items += '<input data-attr="format" '+((field.items[option].format			=='text') 		? 	'checked="checked"' : '' )+' type="radio" name="'+ unique_Id +'field-format" value="text">&nbsp;&nbsp;Text&nbsp;&nbsp;&nbsp;&nbsp;';
						custum_field_items += '<input data-attr="format" '+((field.items[option].format			=='email') 		? 	'checked="checked"' : '' )+' type="radio" name="'+ unique_Id +'field-format" value="email">&nbsp;&nbsp;Email&nbsp;&nbsp;&nbsp;&nbsp;';
						custum_field_items += '<input data-attr="format" '+((field.items[option].format			=='number') 	? 	'checked="checked"' : '' )+' type="radio" name="'+ unique_Id +'field-format" value="number">&nbsp;&nbsp;Number';
						custum_field_items += '</div>';	
							
						jQuery('div.group-options ul.opt-list').prepend('<li class="iz-option"><input data-old-val="'+ field.items[option].val +'" style="'+((jQuery('select[name="field-type"]').val()=="textgroup") ? 'width:165px' : 'width:200px;' )+'"'+ ((field.type=='textgroup' || field.type=='check') ? '' : '' ) +'  type="text" value="'+ field.items[option].val +'">'+ ((field.type=="textgroup") ? '<div class="gears"></div>' + custum_field_items : '') +'<div title="Remove option" class="remove"></div><div title="Drag to position" class="sorthandle"></div></li>');	
						custum_field_items  = '';
						unique_Id = '';
						}
					else
						{
						jQuery('div.group-options ul.opt-list').prepend('<li class="iz-option"><input data-old-val="'+ field.items[option] +'" style="width:200px;" '+ ((field.type=='textgroup' || field.type=='check') ? '' : '' ) +'  type="text" value="'+ field.items[option] +'">'+ ((field.type=="textgroup") ? '<div class="gears"></div>' : '') +'<div title="Remove option" class="remove"></div><div title="Drag to position" class="sorthandle"></div></li>');
						}
					}
				}
			}
		);   	
}

//Rename database column/field
function update_db_field(obj){
	var data = 
		{
		action	 		: 'update_db_field',
		old_value		: jQuery(obj).attr('data-old-val'),			
		old_grouplabel	: format_illegal_chars(jQuery('div.backup div.old-group-label').text()),	
		grouplabel 		: format_illegal_chars(jQuery('input[name="group-label"]').val()),		
		new_value		: jQuery(obj).val()
        };
		
	if(data.old_value!=data.new_value)
		{			
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				 jQuery(obj).val(data.new_value);
				}
			);
		}	
}

//Removes fields from froms and custom panel
//Adds archive flag to database field
function delete_custom_field(obj){
	
	var get_color = obj.parent().parent().parent().parent().css('background-color');	
	
	obj.parent().parent().parent().parent().css('background','#FFEBE8');	
		
	if(confirm('Are your sure you want to permanently delete this field?'))
		{
		clear_all();
		
		var data = 
			{
			action	 		: 'delete_custom_field',
			grouplabel 		: format_illegal_chars(obj.attr("data-group-label"))
			};
		
		obj.parent().parent().parent().parent().fadeOut
			(
			'slow', function()
				{
				jQuery(this).parent().remove();
				}
			); 
			
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				core_object.trigger("update_form_fields");
				}
			);   
		}
	else
		{
		obj.parent().parent().parent().parent().css('background',get_color);
		}	
}

//Delete all element group options
function clear_option_list(){
	jQuery('ul.opt-list li.iz-option').each
		(
		function()
			{	
			jQuery(this).find('div.remove').trigger('click');
			}
		);
}

//Clear all steps
function clear_all(){
	//Backup
	jQuery('div.backup div.group-options').html('');
	jQuery('div.backup div.group-label').html('');
	jQuery('div.backup div.old-group-label').text('');
	
	//Current save
	jQuery('.custom-fields.right').html('');
	jQuery('input[name="group-label"]').val('');
	
	//DB action
	jQuery('span.db_action').text('Add');
	
	//Set step 1 back to select fields
	jQuery('select[name="field-type"] option[value="0"]').attr('selected', true);
	jQuery('select[name="field-type"]').trigger('change');
	jQuery('select[name="field-type"] optgroup.single-fields').attr('disabled',false);	
	jQuery('select[name="field-type"] optgroup.multi-fields').attr('disabled',false);
}


function show_textgroup_attribs(obj){
	
			/*var obj_pos	= jQuery(obj).position();
			if(!obj_pos)
				return false;
			
			element_attr = jQuery(obj).next('div.group_element_attr');
			
			element_attr.css({
				position:'absolute'
				});
			element_attr.css('left',(obj_pos.left+jQuery(obj).outerWidth())-jQuery('div.group_element_attr').outerWidth());
			element_attr.css('top',obj_pos.top-jQuery('div.group_element_attr').outerHeight());
			element_attr.show();*/

			
}


(function ($) {
    $.widget("ui.combobox", {
        _create: function () {
            var self = this,
				select = this.element.hide(),
				selected = select.children(":selected"),
				value = selected.val() ? selected.text() : "",
				regSearch = /^[^a-zA-Z0-9]*([a-zA-Z0-9])/i,
				comboData = select.children("option").map(function () {
					if (this.value ) {
						var text = $(this).text(), 
							labelHtml = self.options.label ? self.options.label(this) : text; //allows list customization
 
						return {
							label: labelHtml,
							value: text,
							option: this
						};
					}
				});
 
            var input = this.input = $("<input type='text' />")
					.insertAfter(select)
					.val(value)
					.keydown( function( event ) {
							var keyCode = $.ui.keyCode;
							switch( event.keyCode ) {
								case keyCode.PAGE_UP:
								case keyCode.PAGE_DOWN:
								case keyCode.UP:
								case keyCode.DOWN:
								case keyCode.ENTER:
								case keyCode.NUMPAD_ENTER:
								case keyCode.TAB:
								case keyCode.ESCAPE:
									//let autocomplete handle these
									break;
								default:
									//prevent autocomplete doing anything
									event.stopImmediatePropagation();
									//only react to [a-zA-Z0-9]
									if ((event.keyCode < 91 && event.keyCode > 59)
										|| (event.keyCode < 58 && event.keyCode > 47)) {
 
										var str = String.fromCharCode(event.keyCode).toLowerCase(), currVal = input.val(), opt;
 
										//find all options whose first alpha character matches that pressed
										var matchOpt = select.children().filter(function() {
											var test = regSearch.exec(this.text);
											return (test && test.length == 2 && test[1].toLowerCase() == str);
										});
 
										if (!matchOpt.length ) return false;
 
										//if there is something selected we need to find the next in the list
										if (currVal.length) {
											var test = regSearch.exec(currVal);
											if (test && test.length == 2 && test[1].toLowerCase() == str) {
												//the next one that begins with that letter
												matchOpt.each(function(ix, el) {
													if (el.selected) {
														if ((ix + 1) <= matchOpt.length-1) {
															opt = matchOpt[ix + 1];
														}
														return false;
													}
												});
											}
										} 
 
										//fallback to the first one that begins with that character
										if (!opt)
											opt = matchOpt[0];
 
										//select that item
										opt.selected = true;
										input.val(opt.text);
 
										//if the dropdown is open, find it in the list
										if (input.autocomplete("widget").is(":visible")) {
											input.data("autocomplete").widget().children('li').each(function() {		
												var $li = $(this);
												if ($li.data("item.autocomplete").option == opt) {
													input.data("autocomplete").menu.activate(event,$li);
													return false;
												}
											});
										}
									}
									//ignore all other keystrokes
									return false;
									break;
								}
					  })
					.autocomplete({
					    delay: 0,
					    minLength: 0,
					    source: function (request, response) { response(comboData); },
					    select: function (event, ui) {
					        ui.item.option.selected = true;
					        self._trigger("selected", event, {
					            item: ui.item.option
					        });
					    },
					    change: function (event, ui) {
							if (!ui.item) {					
								var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($(this).val()) + "$", "i"),
									valid = false;
								select.children("option").each(function () {
									if ($(this).text().match(matcher)) {
										this.selected = valid = true;
										return false;
									}
								});
								if (!valid) {
									// remove invalid value, as it didn't match anything
									$(this).val("");
									select.val("");
									input.data("autocomplete").term = "";
									return false;
								}
							}
					    }
					})
					.addClass("ui-widget ui-widget-content ui-corner-left")
					.click(function() { self.button.click(); })
					.bind("autocompleteopen", function(event, ui){
						//find the currently selected item and highlight it in the list
						var opt = select.children(":selected")[0];
						input.data("autocomplete").widget().children('li').each(function() {		
							var $li = $(this);
							if ($li.data("item.autocomplete").option == opt) {
								input.data("autocomplete").menu.activate(event,$li);
								return false;
							}
						});
					});
 
            input.data("autocomplete")._renderItem = function (ul, item) {
                return $("<li></li>")
					.data("item.autocomplete", item)
					.append("<a href='#'>" + item.label + "</a>")
					.appendTo(ul);
            };
 
            this.button = $("<button type='button'>&nbsp;</button>")
					.attr("tabIndex", -1)
					.attr("title", "Show All Items")
					.insertAfter(input)
					.button({
					    icons: {
					        primary: "ui-icon-triangle-1-s"
					    },
					    text: false
					})
					.removeClass("ui-corner-all")
					.addClass("ui-corner-right ui-button-icon")
					.click(function () {
					    // close if already visible
					    if (input.autocomplete("widget").is(":visible")) {
					        input.autocomplete("close");
					        return;
					    }
 
					    // pass empty string as value to search for, displaying all results
					    input.autocomplete("search", "");
					    input.focus();
					});
        },
 
		//allows programmatic selection of combo using the option value
        setValue: function (value) {
            var $input = this.input;
            $("option", this.element).each(function () {
                if ($(this).val() == value) {
                    this.selected = true;
                    $input.val(this.text);
					return false;
                }
            });
        },
 
        destroy: function () {
            this.input.remove();
            this.button.remove();
            this.element.show();
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);