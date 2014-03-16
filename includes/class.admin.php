<?php
/***************************************/
/***********   Ajax Calls   ************/
/***************************************/
//Core action for populating dropdown menu on updates

add_action('wp_ajax_delete_form',array('XFormsExpress','delete_form'));
add_action('wp_ajax_populate_form_data_list', array('XFormsExpress','get_form_data'));
add_action('wp_ajax_delete_form_entry', array('XFormsExpress','delete_form_entry'));
add_action('wp_ajax_from_entries_table_pagination', array('XFormsExpress','from_entries_table_pagination'));
add_action('wp_ajax_build_form_data_table', array('XFormsExpress','build_form_data_table'));
add_action('wp_ajax_export_csv',  'export_csv' );
add_action('wp_ajax_save_visual_settings_panel_position',array('XFormsExpress','save_visual_settings_panel_position') );
add_action('wp_ajax_nopriv_save_visual_settings_panel_position', array('XFormsExpress','save_visual_settings_panel_position') );

class XFormsExpress{
	
	public $data_fields;
	
	public function __construct(){
	}
	
	public function delete_form(){
			global $wpdb;
			$wpdb->query('DELETE FROM ' .$wpdb->prefix. 'wap_x_forms WHERE Id = '.$_POST['Id']);
			die();
		}	
	public function build_form_data_table($form_id=''){
		
		global $wpdb;
		if(!$form_id)
			$form_id = $_POST['form_Id'];

		$csv_data = '';
		
		$form_fields = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wap_x_forms WHERE Id='.$form_id);
		$form_data = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_x_forms_meta WHERE wa_form_builder_Id='.$form_id);
		
		$form_field_array = json_decode($form_fields->form_fields,true);
		
		foreach($form_data as $set_header)	
				{ 
				$headers[$set_header->meta_key] = ''.IZC_Functions::format_name($set_header->meta_key);
				}
			array_unique($headers);		
			$output .= '<form method="post" action="" id="posts-filter">';

				$output .= '<div class="tablenav top">';
					$output .= '<a class="export_csv"><span class="ui-icon ui-icon-arrowreturnthick-1-s"></span>Export Form Entries (csv)</a>';
					$output	.= '<div class="tablenav-pages">';
					
					$total_records = XFormsExpress::get_total_form_entries($form_id);
		
						$total_pages = ((is_float($total_records/10)) ? (floor($total_records/10))+1 : $total_records/10);
						
						$output .= '<span class="displaying-num"><strong>'.$total_records.'</strong> entries</span>';
						if($total_pages>1)
							{				
							$output .= '<span class="pagination-links">';
							$output .= '<a class="first-page wafb-first-page">&lt;&lt;</a>&nbsp;';
							$output .= '<a title="Go to the next page" class="wafb-prev-page prev-page">&lt;</a>&nbsp;';
							$output .= '<span class="paging-input"> ';
							$output .= '<span class="current-page">'.($_POST['current_page']+1).'</span> of <span class="total-pages">'.$total_pages.'</span>&nbsp;</span>';
							$output .= '<a title="Go to the next page" class="wafb-next-page next-page">&gt;</a>&nbsp;';
							$output .= '<a title="Go to the last page" class="wafb-last-page last-page">&gt;&gt;</a></span>';
							}
					$output	.= '</div>';
				$output .= '</div>';
				$output .= '<br class="clear">';
		$output .= '<table cellspacing="0" class="wp-list-table resiable-columns widefat fixed tags iz-list-table resizabletable" id="iz_col_resize">';
			$output .= '<thead>';
			$output .= '<tr>';
			foreach($headers as $header)	
					{
						$csv_data .= IZC_Functions::unformat_name($header).',';
						$output .= '<th valign="bottom" class="manage-column"><span class="">'.IZC_Functions::unformat_name($header).'</span></th>'; //<span class="sorting-indicator"></span>
					}
			$csv_data .= '
	';
			$output .= '</tr>';
			$output .= '</thead>';
			
			$output .= '<tfoot>';
			$output .= '<tr>';
			foreach($headers as $header)	
					{
					$output .= '<th valign="bottom" class="manage-column"><span class="">'.IZC_Functions::unformat_name($header).'</span></th>';
					}
			$output .= '</tr>';
			$output .= '</tfoot>';
			
			$output .= '<tbody class="list:tag" id="the-list">';
			
			$sql = 'SELECT * FROM '.$wpdb->prefix.'wap_x_forms_meta WHERE wa_form_builder_Id='.$form_id.' GROUP BY time_added ORDER BY time_added DESC
								LIMIT '.((isset($_POST['current_page'])) ? $_POST['current_page']*10 : '0'  ).',10 ';
			$results 	= $wpdb->get_results($sql);
			
			$sql2 = 'SELECT * FROM '.$wpdb->prefix.'wap_x_forms_meta WHERE wa_form_builder_Id='.$form_id.' GROUP BY time_added ORDER BY time_added DESC';
			$csv_results 	= $wpdb->get_results($sql2);

			if($results)
				{
				$i = 1;			
				foreach($results as $data)
					{
					$old_record = $data->last_update;	
					
					if($new_record!=$old_record && $i!=1)
						{
						$output .= '</tr>';	
						}
					
					if($new_record!=$old_record)
						{
						$output .= '<tr class="row parent" id="tag-'.$data->Id.'">';
						}
						$k =1;
						foreach($headers as $heading)	
							{
							$check_field = $wpdb->get_row('SELECT meta_key,meta_value FROM '.$wpdb->prefix.'wap_x_forms_meta WHERE meta_key="'.$heading.'" AND time_added="'.$data->time_added.'"');
							if($check_field)
								{
								
								$val = $check_field->meta_value;
								
								if(strstr($check_field->meta_value,'wp-content'))
									{
									$get_extension = explode('.',$check_field->meta_value);
									
									$get_file_name = explode('/',$get_extension[0]);
									
									$val = '<a href="'.$check_field->meta_value.'" target="_blank"><img width="30" src="'.WP_PLUGIN_URL.'/x-forms-express/includes/Core/images/icons/ext/'.$get_extension[count($get_extension)-1].'.png">'.$get_file_name[count($get_file_name)-1].'</a>';
									$image_extensions = array('jpg','jpeg','gif','png','bmp');
									foreach($image_extensions as $image_extension)
										{
										if(stristr($check_field->meta_value,$image_extension))
											{
											$val = '<a href="'.$check_field->meta_value.'" ><img src="'.$check_field->meta_value.'" style="max-width:100px" ></a>';
											}
										}
									}
								else
									{
									$val = $check_field->meta_value;
									}
								
								$output .= '<td class="manage-column column-'.$heading.'">'.$val.'&nbsp;';
								$output .= (($k==1) ? '<div class="row-actions"><span class="delete"><a href="javascript:delete_form_entry(\''.$data->time_added.'\',\''.$data->Id.'\');" >Delete</a></span></div>' : '' ).'</td>';
								
								$output .= '</td>';
								}
							else
								{
								$output .= '<td class="manage-column column-'.$heading.'">&nbsp;'; 
								$output .= (($k==1) ? '<div class="row-actions"><span class="delete"><a href="javascript:delete_form_entry(\''.$data->time_added.'\',\''.$data->Id.'\');" >Delete</a></span></div>' : '' ).'</td>';
								}
							$k++;
							}
					$new_record = $old_record;
					$i++;
					}
				}
			else
				{	
				$output .= '<div class="ui-state-error" style="border-radius: 7px 7px 7px 7px;margin-bottom: 10px; padding: 5px 10px;width: 98%;display:block;"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>No entries found.</div>';
			
				}
/********************/
/****** CSV *********/
/********************/
			if($csv_results)
				{
				$i = 1;			
				foreach($csv_results as $data)
					{
					$old_record = $data->last_update;
					if($new_record!=$old_record && $i!=1)
						{
						$csv_data .= '
	';
						}
					foreach($headers as $heading)	
						{
							$check_field = $wpdb->get_row('SELECT meta_key,meta_value FROM '.$wpdb->prefix.'wap_x_forms_meta WHERE meta_key="'.$heading.'" AND time_added="'.$data->time_added.'"');
							$csv_data .= $check_field->meta_value.',';
						}
					$new_record = $old_record;
					$i++;
					}
				}
			else
				{
				$csv_data .= 'no form entries';
				}
			
					
						$output .= '</tbody>';
					$output .= '</table>';
				$output .= '</form>';
				
				$output .= "<input type='hidden' name='additional_params' value='".json_encode($additional_params)."'>";
				$output .= "<input type='hidden' name='table_headers' value='".json_encode($headers)."'>";
				$output .= '<input type="hidden" name="page" value="'.$_REQUEST['page'].'">';
				$output .= '<input type="hidden" name="orderby" value="">';
				$output .= '<input type="hidden" name="order" value="desc">';
				$output .= '<input type="hidden" name="current_page" value="0">';
				$output .= '<input type="hidden" name="wa_form_Id" value="'.$_POST['form_Id'].'">';

			$output .= '<form name="export_csv" method="post" action="'.get_option('siteurl').'/wp-content/plugins/x-forms-express/includes/export.php" id="posts-filter" style="display:none;">';
				$output .= '<textarea name="csv_content">'.$csv_data.'</textarea>';	
				$output .= '<input name="_title" value="'.IZC_Database::get_title($form_id,'wap_x_forms').'">';	
			$output .= '</form>';
			
		if($_POST['form_Id'])	{
			echo $output;
			die();
		}
		else
			return $output;
	}
	public function get_form_data(){

		global $wpdb;
		$args 		= str_replace('\\','',$_POST['args']);
		$headings 	= json_decode($args);
		$sql = 'SELECT * FROM '.$wpdb->prefix.'wap_x_forms_meta WHERE wa_form_builder_Id='.$_POST['form_Id'].' GROUP BY time_added ORDER BY time_added DESC
										LIMIT '.((isset($_POST['current_page'])) ? $_POST['current_page']*10 : '0'  ).',10 ';
		$results 	= $wpdb->get_results($sql);
		if($results)
			{
			$i = 1;			
			foreach($results as $data)
				{
				$old_record = $data->last_update;	
				
				if($new_record!=$old_record && $i!=1)
					{
					//$output .= '<th class="expand" scope="row"></th>';
					$output .= '</tr>';	
					}
				
				if($new_record!=$old_record)
					{
					$output .= '<tr class="row parent" id="tag-'.$data->Id.'">';
					}
					$k =1;
					foreach($headings as $heading)	
						{
							$check_field = $wpdb->get_row('SELECT meta_key,meta_value FROM '.$wpdb->prefix.'wap_x_forms_meta WHERE meta_key="'.$heading.'" AND time_added="'.$data->time_added.'"');
							
							if($check_field)
								{
								$val = $check_field->meta_value;
								
								if(strstr($check_field->meta_value,'wp-content'))
									{
									$get_extension = explode('.',$check_field->meta_value);
									
									$get_file_name = explode('/',$get_extension[0]);
									
									$val = '<a href="'.$check_field->meta_value.'" target="_blank"><img width="30" src="'.WP_PLUGIN_URL.'/x-forms-express/includes/Core/images/icons/ext/'.$get_extension[count($get_extension)-1].'.png">'.$get_file_name[count($get_file_name)-1].'</a>';
									$image_extensions = array('jpg','jpeg','gif','png','bmp');
									foreach($image_extensions as $image_extension)
										{
										if(stristr($check_field->meta_value,$image_extension))
											{
											$val = '<a href="'.$check_field->meta_value.'" ><img src="'.$check_field->meta_value.'" style="max-width:100px" ></a>';
											}
										}
									}
								else
									{
									$val = $check_field->meta_value;
									}
								$output .= '<td class="manage-column column-'.$heading.'">'.$val.'&nbsp;';
								$output .= (($k==1) ? '<div class="row-actions"><span class="delete"><a href="javascript:delete_form_entry(\''.$data->time_added.'\',\''.$data->Id.'\');" >Delete</a></span></div>' : '' ).'</td>';

								$output .= '</td>';
								}
							else
								{
								$output .= '<td class="manage-column column-'.$heading.'">&nbsp;'; 
								$output .= (($k==1) ? '<div class="row-actions"><span class="delete"><a href="javascript:delete_form_entry(\''.$data->time_added.'\',\''.$data->Id.'\');" >Delete</a></span></div>' : '' ).'</td>';
								}
						$k++;
						}
				$new_record = $old_record;
				$i++;
				}
			}
		else
			{
			$output .= '<tr>';	
			$output .= '<td class="manage-column" colspan="'.(count($headings)).'">Sorry, No entires found</td>';
			$output .= '</tr>';
			}
			
		echo $output;
		die();
	}
	public function from_entries_table_pagination(){
		$total_records = XFormsExpress::get_total_form_entries($_POST['wa_form_Id']);
		
		$total_pages = ((is_float($total_records/10)) ? (floor($total_records/10))+1 : $total_records/10);
		
		$output .= '<span class="displaying-num"><strong>'.XFormsExpress::get_total_form_entries($_POST['wa_form_Id']).'</strong> entries</span>';
		if($total_pages>1)
			{				
			$output .= '<span class="pagination-links">';
			$output .= '<a class="first-page wafb-first-page">&lt;&lt;</a>&nbsp;';
			$output .= '<a title="Go to the next page" class="wafb-prev-page prev-page">&lt;</a>&nbsp;';
			$output .= '<span class="paging-input"> ';
			$output .= '<span class="current-page">'.($_POST['current_page']+1).'</span> of <span class="total-pages">'.$total_pages.'</span>&nbsp;</span>';
			$output .= '<a title="Go to the next page" class="wafb-next-page next-page">&gt;</a>&nbsp;';
			$output .= '<a title="Go to the last page" class="wafb-last-page last-page">&gt;&gt;</a></span>';
			}
		echo $output;
		die();
	}
	public function get_total_form_entries($wa_form_Id){
		global $wpdb;
		$get_count  = $wpdb->get_results('SELECT Id FROM '.$wpdb->prefix .'wap_x_forms_meta WHERE wa_form_builder_Id='.$wa_form_Id.' GROUP BY time_added');
		return count($get_count);
	}
	public function delete_form_entry(){
		global $wpdb;
		$wpdb->query('DELETE FROM ' .$wpdb->prefix. 'wap_x_forms_meta WHERE time_added = "'.$_POST['last_update'].'"');
		IZC_Functions::print_message( 'updated' , 'Item deleted' );
		die();
	}
	
	/***************************************/
	/*******   Customizing Forms   *********/
	/***************************************/
	public function customize_forms(){
		global $wpdb;
		$db 		= new IZC_Database();
		$template 	= new IZC_Template();
		$config 	= new XFormsExpress_Config();
		$newform_Id = rand(0,99999999999);
		
		$get_forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_x_forms ORDER BY Id DESC');
		$output .=  '<div class="settings_view" style="display:none;">
							<div class="setting"><label for="change_field_name">Field name</label><input id="change_field_name" type="text" name="change_field_name" value=""></div>											
							<div class="setting"><label for="change_field_description">Description / Help Text</label><input id="change_field_description" type="text" name="change_field_description" value=""></div>
							<div class="setting"><label for="change_field_error">Error Message <em style="font-weight:normal; font-size:11px;">(Triggered on form submission only if field is required)</em></label><input type="text"  id="change_field_error" name="change_field_error" value=""></div>
							<div class="save_field_settings sec_button">Save</div>
							<div style="clear:both;"></div>
						</div>';
				
		
		$output .= '<div class="form_update_id" style="display:none"></div>';
		$output .= '<h3 style="display:none"><span class="db_action" >Add</span> Field</h3>';
		$output .= '<div class="form_Id" style="display:none"></div>';
		$output .= '<div class="backup"><div class="old-group-label"></div><div class="group-label"></div><div class="group-options"></div></div>';
		$output .= '<div class="current_object" style="display:none">form_container_bg</div>';
		$output .= '<div class="current_canvas" style="display:none">'.$newform_Id.'</div>';
		
		$output .= '<div style="margin-top: -18px; padding-left:15px;width: 100%;" class="pro-banner">
					<a href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix"><img src="'.WP_PLUGIN_URL . '/x-forms-express/images/pro_banner_1.png"></a><a href="http://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891?ref=Basix"><img src="'.WP_PLUGIN_URL . '/x-forms-express/images/pro_banner_2.png"></a>
					</div>';
		
		$output .= '<div class="holygrail colmask"> ';
			$output .= '<div class="colmid"> ';
				$output .= '<div class="colleft"> ';
					$output .= '<div class="col1wrap"> ';
/**********************************************************************************/
/************** CENTER COLUMN *****************************************************/
/**********************************************************************************/
						$output .= '<div class="col1">';
							$output .= '<div class="header">';
						
								/*******************************/
								/*********** MENU **************/
								/*******************************/
								$output .= '<div class="opened_forms">';
									
									/*********** NEW FORM TAB **************/
									$output .= '<ul>';
										$output .= '<li class="newform creating active"  title="Add New Form" style="z-index:'.$i.';" data-form-id="'.$newform_Id.'">New Form</li>';
									$output .= '</ul>';
									
									/*********** TABS MENU **************/
									$output .= '<ul class="forms tab_menu">';
									if($get_forms)
										{									
										foreach($get_forms as $get_form)
											$output .= '<li class="form" id="tab_'.$get_form->Id.'" title="Open" data-form-id="'.$get_form->Id.'">'.$get_form->title.'</li>';
										}
									$output .= '</ul>';
									
									/*********** DROP MENU **************/
									$output .= '<ul class="forms drop_menu" style="display:none;">';
										$output .= '<li class="my_forms">MY FORMS';
											$output .= '<ul>';
											if($get_forms)
												{
												foreach($get_forms as $get_form)
													$output .= '<li id="tab_'.$get_form->Id.'" class="form" title="Open" data-form-id="'.$get_form->Id.'">'.$get_form->title.'</li>';
												}
											$output .= '</ul>';
										$output .= '</li>';
									$output .= '</ul>';
								$output .= '</div>';
								
								$output .= '<div style="clear:both;"></div>';								
							$output .= '</div>';	
							
							/***************************************/
							/*********** FORM ACTIONS **************/
							/***************************************/
							$output .= '<div class="widgets-holder-wrap ui-droppable" id="available-widgets">';
								$output .= '<div class="widgets-holder-wrap forms-canvas">';
									$output .= '<div class="sidebar-name">';
										/*********** FORM TITLE **************/
										$output .= '<div class="form_name"><label class="form_title_label" for="form_title">Form Title:</label><input type="text" id="" name="form_name" value="" placeholder="Enter new form title here..."></div>';
										
										/*********** DUP/DEL **************/
										$output .= '<div class="forms_actions" style="display:none;">';
											$output .= '<div class="duplicate_form">Duplicate Form</div>';
											$output .= '<div id="remove_form">Delete Form</div>';
										$output .= '</div>';
										
										/*********** VIEWING TABS **************/
										$output .= '<div style="clear:both;"></div>';
										$output .= '<div class="view" >';
											
											$output .= '<div class="code_view tabs" style="display:none;"><h3>Code</h3><span class="tab_description">Using this form</span></div>';
											$output .= '<div class="ve_from_entries tabs" style="display:none;"><h3>Form Entries</h3><span class="tab_description">View form entries</span></div>';
											$output .= '<div class="ve_view tabs"><h3>Design</h3><span class="tab_description">Visual Editing</span></div>';
											$output .= '<div class="normal_view tabs active"><h3>Builder</h3><span class="tab_description">Normal view</span></div>';
											
										$output .= '</div>';
										
										/*********** TOP SAVE **************/
										$output .= '<div class="save_form canvas_top"><span class="save_icon">&nbsp;</span>Save Form</div>';
										
										$output .= '<div style="clear:both;"></div>';
									$output .= '</div>';
									
									/*****************************************/
									/*********** DESIGN ACTIONS **************/
									/*****************************************/
									$output .= '<div class="overall_styles" style="display:none;">';
										$output .= '<h3 class="show_available_styles">Overall Styling Selection</h3>';
										$output .= '<strong>OR...</strong><br />Select a single field element by clicking on it in the form view below.';	
										$output .= '<div class="view_styles" style="display:none;">';
											$output .= '<div id="" class="overall_styling" >None</div>';
											
											$output .= '<h4>Form Background / wrapper </h4>';
											$output .= '<div id="form_container_bg" class="overall_styling form_container_bg" >Form Container</div>';
											
											$output .= '<h4>Field Titles/ holders/ Error messages</h4>';
											$output .= '<div id="field_holder" class="overall_styling" >Field Holders</div>';
											$output .= '<div id="ve_title" class="overall_styling" >Field Titles</div>';
											$output .= '<div id="error_msg" class="overall_styling" >Error Messages</div>';
											
											$output .= '<h4>Form Fields</h4>';
											$output .= '<div id="ve_text" class="overall_styling" >Text Fields</div>';
											$output .= '<div id="ve_email" class="overall_styling" >Email Fields</div>';
											$output .= '<div id="ve_phone_number" class="overall_styling" >Phone Number Fields</div>';
											$output .= '<div id="ve_file" class="overall_styling" >File uplaod Fields</div>';
											$output .= '<div id="ve_auto" class="overall_styling" >Autocomplete Fields</div>';
											$output .= '<div id="ve_date" class="overall_styling" >Date Fields</div>';
											$output .= '<div id="ve_time" class="overall_styling" >Time Fields</div>';
											$output .= '<div id="ve_numbers_only" class="overall_styling" >Numbers only Fields</div>';
											$output .= '<div id="ve_text_only" class="overall_styling" >Text Only Fields</div>';
											$output .= '<div id="ve_textarea" class="overall_styling" >Text Areas</div>';											
											$output .= '<div id="ve_dropdown" class="overall_styling" >Dropdowns</div>';
											
											$output .= '<h4>Headings and paragraphs</h4>';
											$output .= '<div id="ve_h1" class="overall_styling" >All Heading 1</div>';
											$output .= '<div id="ve_h2" class="overall_styling" >All Heading 2</div>';
											$output .= '<div id="ve_h3" class="overall_styling" >All Heading 3</div>';
											$output .= '<div id="ve_h4" class="overall_styling" >All Heading 4</div>';
											$output .= '<div id="ve_h5" class="overall_styling" >All Heading 5</div>';
											$output .= '<div id="ve_h6" class="overall_styling" >All Heading 6</div>';
											$output .= '<div id="ve_h6" class="overall_styling" >Paragraphs</div>';
											$output .= '<div style="clear:both;"></div>';
										$output .= '</div>';
									$output .= '</div>';
									$output .= '<div style="clear:both;"></div>';
									
									/***************************************/
									/*********** FORMS CANVAS **************/
									/***************************************/
									$output .= '<div id="primary-widget-area" class="primary-widget-area">';											
										
										/*********** FORM ENTRY VIEW **************/
										$output .= '<div class="form_entry_view" style="display:none;"><div style="margin-top: 15px; padding: 15px;width: 95%;" class="ui-state-highlight ui-corner-all">
										<span style="float: left; margin-right: 5px;" class="ui-icon ui-icon-info"></span>
										Form entries and export are only available in the full verion. Note all entries have been saved, to display them here please upgrade.<br />
<a class="get_full_version" href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix">Download full version</a>
										</div></div>';	
										
										/*********** GET CODE VIEW **************/
										$output .= '<div class="show_code" style="display:none;">';
											$output .= '<h3>PHP function</h3>';
											$output .= '<p>Add the following into your HTML or PHP scripts.</p>';
											$output .= '<div class="php_function"><strong>Return output:</strong><br />
														<code>&lt;?php XFormsExpress_ui_output(the_form_id); ?&gt;</code>
														<br /><strong>Echo output:</strong><br />
														<code>&lt;?php XFormsExpress_ui_output(the_form_id,true); ?&gt;</code>
														OR
														<code>&lt;?php echo XFormsExpress_ui_output(the_form_id); ?&gt;</code></div>';
											$output .= '<h3>Short Code</h3>';
											$output .= '<p>Add the shortcode below into your pages or posts where you want your this form to be displayed. Alternatively you can use the TinyMCE button (same look as the "X Forms" menu icon) to add forms to your pages!</p>';
											$output .= '<div class="short_code"></div>';
											$output .= '<h3>Sidebar Widget</h3>';
											$output .= '<p>Go to appearance and drag the "X Forms" widget into your desired sidebar. Select this form by name and click save.</p>';
										$output .= '</div>';	
										
											/*********** NEW FORM **************/
											$output .= '<div class="drop-sort newform" id="drop_sort__'.$newform_Id.'">';
												$output .= '<div class="form_container_bg form_container" id="form_container">';
													$output .= '<div class="form_field ui-draggable submit_button">';
													$output .= '<div style="clear:both;"></div>';
														$output .= '<div id="form_object" class="form_object">';
															$output .= '<div class="ui-button ui-widget ui-state-default ui-state-hover ui-corner-all ui-button-text-only iz-submit submit_form_entry">Submit</div><span class="click_to_edit"></span>';
														$output .= '</div>';									
													$output .= '</div>';
												$output .= '</div>';
												$output .= '<div style="clear:both;"></div>';		
											$output .= '</div>';
											
											/*********** SAVED FORMS **************/
											if($get_forms)
												{
												foreach($get_forms as $get_form)
													{
													$output .= '<div class="drop-sort drop_sort__'.$get_form->Id.'  " id="drop_sort__'.$get_form->Id.'" style="display:none;">';
													$output .= str_replace('\\','',$get_form->form_fields);
													$output .= '</div>';
													}
												}
									$output .= '</div>';
									
								$output .= '</div>';
								
								/*********** BOTTOM SAVE **************/
								$output .= '<div class="save_form canvas_bottom"><span class="save_icon">&nbsp;</span>Save Form</div>';
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div> ';
					
/**********************************************************************************/
/************** LEFT COLUMN *******************************************************/
/**********************************************************************************/			
			$output .= '<div class="col2">';
				$output .= '<div class="xforms_logo"></div>';
					/******************************************/
					/************** COMMON FIELDS *************/
					/******************************************/
					$output .= '<div class="avialable_fields_container">';		
					$output .= '<h3>';
						$output .= 'Common fields';
					$output .= '</h3>';
					
					$output .= '<div class="avialable_fields_holder">';
						$output .= '<div id="widget-list" class="iz-custom-form-fields">';				
							
							$field_actions .= '<div class="field_actions">';
								$field_actions .= '<div class="delete" title="Remove form Item"></div>';
								$field_actions .= '<div class="sort"></div>';
							$field_actions .= '</div>';
							$required .= '	<span class="click_to_edit"></span>&nbsp;&nbsp;<span class="set_required"> 
											<input type="checkbox" name="required" id="required"><label for="required" class="required">Is Required?</label>
											</span>';
							$add_description .= '<span class="field_settings"><a>Edit Settings</a></span>';

							//TEXT
							$output .= '<div class="field form_field text">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Text Field</div>';
								$output .= '</div>';
								$output .= '<div id="form_object" class="form_object" style="display:none;">';
									$output .= '<label id="ve_title" class="title ve_title">Text Field</label><span class="is_required"></span>';
									$output .= $required;
									$output .= $add_description;
									$output .= $field_actions;
									$output .= '<div id="help_text"  class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></div>';
									$output .= '<div id="error_msg" class="error_msg ui-state-error"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>Please enter a value.</div>';
									$output .= '<input id="ve_text" type="text" name="text_field" value="" class="text ve_text">';
								$output .= '</div>';
																	
							$output .= '</div>';
							
							//TEXTAREA
							$output .= '<div class="field form_field textarea">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Text Area</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
									$output .= '<label id="ve_title" class="title ve_title">Text Area</label><span class="is_required"></span>';
									$output .= $required;
									$output .= $add_description;
									$output .= $field_actions;
$output .= '<div id="help_text"  class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></div>';
									$output .= '<div id="error_msg" class="error_msg ui-state-error"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>Please enter a value.</div>';
									$output .= '<textarea name="text_area" id="ve_textarea" class="ve_textarea textarea"></textarea>';
									$output .= $settings;
								$output .= '</div>';
																	
							$output .= '</div>';
							
							//DROPDOWN
							$output .= '<div data-id="" class="field form_field dropdown">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Dropdown</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
									$output .= '<label id="ve_title" class="title ve_title">Dropdown</label><span class="is_required"></span>';
									$output .= $required;
									$output .= '<span class="show_field_settings"><a> Add/edit/remove dropdown options</a></span>';
									$output .= $add_description;
									$output .= $field_actions;
$output .= '<div id="help_text"  class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></div>';
									$output .= '<div id="error_msg" class="error_msg ui-state-error"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>Please select an option.</div>';

									$output .= '<select name="dropdown" id="ve_dropdown" class="ve_dropdown">
													<option value="0">-- Select --</option>
												</select>';
									$output .= '<div class="get_dropdown_items" style="display:none;"></div>';
									$output .= $settings;
									$output .= '<div class="field_settings" style="display:none;"><p class="description">List dropdown options in the textarea below one benief the other. For example:<br />Dropdown option 1<br />Dropdown option 2<br />Dropdown option 3</p><textarea class="set_dropdown_items" name="set_dropdown_items"></textarea><div class="save_dropdown_items sec_button">Save Dropdown Options</div><br class="clear"></div>';
								$output .= '</div>';
																	
							$output .= '</div>';
							
							//RADIO
							$output .= '<div class="field form_field radio">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title ">Radio Button</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
									$output .= '<div id="error_msg" class="error_msg ui-state-error"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>Please check this.</div>';
									$output .= '<input type="radio" name="radio_button" value="on" class="single_radio">&nbsp;&nbsp;<label class="title normal ve_single_radio" id="ve_single_radio">Radio Button</label><span class="is_required"></span>';
									$output .= $required;
									$output .= $add_description;
									$output .= $field_actions;
$output .= '<div id="help_text"  class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></div>';										
									$output .= $settings;
								$output .= '</div>';
																	
							$output .= '</div>';
							
							//CHECK
							$output .= '<div class="field form_field check">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Check Box</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
									$output .= '<div id="error_msg" class="error_msg ui-state-error"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>Please check this.</div>';
									$output .= '<input id="cbx-1" type="checkbox" name="check_box" class="single_check">&nbsp;&nbsp;<label class="title normal ve_single_check" id="ve_single_check">Check Box</label><span class="is_required"></span>';
									$output .= $required;
									
									$output .= $add_description;
									$output .= $field_actions;
$output .= '<div id="help_text"  class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></div>';										
									$output .= $settings;									
								$output .= '</div>';
																	
							$output .= '</div>';
							
														
						$output .= '</div>';
					$output .= '</div>';
					
				
					/******************************************/
					/************* OTHER ELEMENTS *************/
					/******************************************/
					$output .= '<h3>';
						$output .= 'Other Elements';
					$output .= '</h3>';
						
					$output .= '<div class="avialable_fields_holder">';
						$output .= '<p class="description"></p>';
						$output .= '<div id="widget-list" class="iz-custom-form-fields">';	
						
							//HEADINGS
							$output .= '<div class="field form_field heading h1">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Heading 1</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
$output .= $field_actions;
								
									$output .= '<h1 class="heading ve_h1" id="ve_h1">Heading</h1><span class="click_to_edit"></span>';
								$output .= '</div>';
																	
							$output .= '</div>';
							$output .= '<div class="field form_field heading h2">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Heading 2</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
$output .= $field_actions;
								
									$output .= '<h2 class="heading ve_h2" id="ve_h2">Heading</h2><span class="click_to_edit"></span>';
								$output .= '</div>';
																	
							$output .= '</div>';
							$output .= '<div class="field form_field heading h3">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Heading 3</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
$output .= $field_actions;
								
									$output .= '<h3 class="heading ve_h3" id="ve_h3">Heading</h3><span class="click_to_edit"></span>';
								$output .= '</div>';
																	
							$output .= '</div>';
							$output .= '<div class="field form_field heading h4">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Heading 4</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
$output .= $field_actions;
								
									$output .= '<h4 class="heading ve_h4" id="ve_h4">Heading</h4><span class="click_to_edit"></span>';
								$output .= '</div>';
																	
							$output .= '</div>';
							$output .= '<div class="field form_field heading h5">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Heading 5</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
$output .= $field_actions;
								
									$output .= '<h5 class="heading ve_h5" id="ve_h5">Heading</h5><span class="click_to_edit"></span>';
								$output .= '</div>';
																	
							$output .= '</div>';
							$output .= '<div class="field form_field heading h6">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Heading 6</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
$output .= $field_actions;
								
									$output .= '<h6 class="heading ve_h6" id="ve_h6">Heading</h6><span class="click_to_edit"></span>';
								$output .= '</div>';
																	
							$output .= '</div>';
							
							//TEXT PARAGRAPH
							$output .= '<div class="field form_field paragraph">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Text Paragraph</div>';
								$output .= '</div>';
								$output .= '<div class="form_object" style="display:none;">';
$output .= $field_actions;
								
									$output .= '<p class="text_paragraph ve_text_paragraph" id="ve_text_paragraph">Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph Text Paragraph </p><span class="click_to_edit"></span>';
								$output .= '</div>';
																	
							$output .= '</div>';
							
							//DIVIDER
							$output .= '<div class="field form_field divider">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Divider</div>';
								$output .= '</div>';
								
								$output .= '<div class="form_object" style="display:none;">';
								$output .= $field_actions;
									$output .= '<hr class="divider ve_divider" id="ve_divider" />';
								$output .= '</div>';
																	
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
					/******************************************/
					/************** PRE-FORMATED **************/
					/******************************************/
					$output .= '<h3>';
						$output .= 'MORE FIELDS!!';
					$output .= '</h3>';
					
					$output .= '<div class="avialable_fields_holder">';
						$output .= '<div style="margin-top: 15px; padding: 15px;width: 95%;" class="ui-state-highlight ui-corner-all">
										<span style="float: left; margin-right: 5px;" class="ui-icon ui-icon-info"></span>
										Fields available in the full version only<br />
<a class="get_full_version" href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix">Download full version</a>
										</div>';
						$output .= '<div id="widget-list" class="iz-custom-form-fields">';				
							
							//FILE
							$output .= '<div class="field form_field file">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Upload File</div>';
								$output .= '</div>';																	
							$output .= '</div>';
							
							//AUTO COMPLETE
							$output .= '<div class="field form_field auto">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Auto Complete</div>';
								$output .= '</div>';
							$output .= '</div>';
							
							//EMAIL
							$output .= '<div class="field form_field email">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Email</div>';
								$output .= '</div>';
							$output .= '</div>';
							
							//PHONE NUMBER
							$output .= '<div class="field form_field phone">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Phone Number</div>';
								$output .= '</div>';
							$output .= '</div>';
							
							//DATE
							$output .= '<div class="field form_field date">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Date-picker</div>';
								$output .= '</div>';																	
							$output .= '</div>';
							
							//TIME
							$output .= '<div class="field form_field time">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Time-picker</div>';
								$output .= '</div>';				
							$output .= '</div>';
							
							//RADIO GROUP
							$output .= '<div class="field form_field radio_group">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Radio Button Group</div>';
								$output .= '</div>';															
							$output .= '</div>';
							
							//CHECK GROUP
							$output .= '<div class="field form_field check_group">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Check Box Group</div>';
								$output .= '</div>';	
							$output .= '</div>';
							
							//NUMBERS ONLY
							$output .= '<div class="field form_field num_only">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Numbers Only</div>';
								$output .= '</div>';				
							$output .= '</div>';
							
							//TEXT ONLY
							$output .= '<div class="field form_field text_only">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">Text Only</div>';
								$output .= '</div>';				
							$output .= '</div>';
							
							//URL
							$output .= '<div class="field form_field url">';
								$output .= '<div class="draggable_object">';
									$output .= '<div class="icon"></div>';
									$output .= '<div class="title">URL</div>';
								$output .= '</div>';					
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div> ';
			
/**********************************************************************************/
/************** RIGHT COLUMN *****************************************************/
/**********************************************************************************/				
			$output .= '<div class="col3">';
				$setting_values = get_option('wa-forms-default-settings');
				$output .= '<div class="form_setup">';
					
					/***************************************/
					/********** GENERAL SETTINGS ***********/
					/***************************************/				
					$output .= '<div class="widgets-holder-wrap iz-holder" id="available-widgets">';
						$output .= '<div class="wa-sidebar-name settings_general">';
							$output .= '<div class="sidebar-name-arrow">';
							$output .= '<br>';
							$output .= '</div>';
								$output .= '<h3>';
									$output .= 'General Settings';
								$output .= '</h3>';
						$output .= '</div>';
	
						$output .= '<div class="widget-holder" style="display:none;">';
							$output .= '<p class="description"></p>';
							$output .= '<div id="widget-list" class="settings_general newform  general_setup__'.$newform_Id.' widget-list">';
	
								$output .= '<fieldset class="radio  submission_redirect_url">
												<legend title="Choose whether to display a confirmation message or redirect to a specified url after this form is submitted.">On form submission <p class="field_description ui-icon-info ui-icon" ></p></legend>
													<select name="on_form_submission">
														<option value="show_message"  selected="selected">Display confirmation message</option>
														<option value="redirect_url">Redirect to URL</option>
													</select>
											  </fieldset>
											<fieldset class="textarea on_screen_confirmation_message" style="display:block;">
												<legend title="The on-screen message to be displayed after the form is submited if a confirmation is not entered">On Screen Confirmation Message <p class="field_description ui-icon-info ui-icon" ></p></legend>
												<div class="iz-form-item" >
													<textarea name="on_screen_confirmation_message" class="">'.$setting_values['on_screen_confirmation_message'].'</textarea>
												</div>
											</fieldset>
											<fieldset class="text confirmation_page" style="display:none;">
												<legend title="This is the URL to where the the user is redirected after submitting this form.">Redirect to URL <p class="field_description ui-icon-info ui-icon" ></p></legend>
												<div class="iz-form-item" >
													 <input type="text" class=" " value="'.$setting_values['confirmation_page'].'" name="confirmation_page">
												 </div>
											  </fieldset>
											
										  <fieldset class="textarea google_analytics_conversion_code" title="Paste your Google Analytics Coonversion Code here and it will be added after a form submission.">
											<legend>Google Analytics Conversion Code <p class="field_description ui-icon-info ui-icon" ></p></legend>
												<div class="iz-form-item">
													<textarea name="google_analytics_conversion_code" class="">'.$setting_values['google_analytics_conversion_code'].'</textarea>
												</div>
										  </fieldset>
										  ';		
							 							
							$output .= '</div>';
							
							if($get_forms)
								{
								foreach($get_forms as $get_form)
									{
									$output .= '<div id="widget-list" class="settings_general general_setup__'.$get_form->Id.' widget-list" style="display:none;">';
									$output .= '
												<fieldset class="select  submission_redirect_url">
													<legend title="Choose whether to display a confirmation message or redirect to a specified url after this form is submitted.">On form submission <p class="field_description ui-icon-info ui-icon" ></p></legend>
														<select name="on_form_submission">
															<option value="show_message"  '.(($get_form->on_form_submission=='show_message') ? 'selected="selected"' : '').'>Display confirmation message</option>
															<option value="redirect_url" '.(($get_form->on_form_submission=='redirect_url' ) ? 'selected="selected"' : '').'>Redirect to URL</option>
														</select>
												</fieldset>
												<fieldset class="textarea on_screen_confirmation_message" '.(($get_form->on_form_submission=='show_message') ? 'style="display:block"' : '').'>
													<legend title="The on-screen message to be displayed after the form is submited if a confirmation is not entered">On Screen Confirmation Message <p class="field_description ui-icon-info ui-icon" ></p></legend>
													<div class="iz-form-item" >
														<textarea name="on_screen_confirmation_message" class="">'.$get_form->on_screen_confirmation_message.'</textarea>
													</div>
												</fieldset>
												<fieldset class="text confirmation_page" '.(($get_form->on_form_submission=='redirect_url') ? 'style="display:block"' : '').'>
													<legend title="This is the URL to where the the user is redirected after submitting this form.">Redirect to URL <p class="field_description ui-icon-info ui-icon" ></p></legend>
													<div class="iz-form-item" >
														<input type="text" class=" " value="'.$get_form->confirmation_page.'" name="confirmation_page">
													</div>
												</fieldset>
												<fieldset class="textarea google_analytics_conversion_code" title="Paste your Google Analytics Coonversion Code here and it will be added after a form submission.">
													<legend>Google Analytics Conversion Code <p class="field_description ui-icon-info ui-icon" ></p></legend>
													<div class="iz-form-item">
														<textarea name="google_analytics_conversion_code" class="">'.$get_form->google_analytics_conversion_code.'</textarea>
													</div>
												</fieldset>';
									$output .= '</div>';
									}
								}
						$output .= '</div>';
					
					/***************************************/
					/************* EMAIL SETUP *************/
					/***************************************/
					$output .= '<div class="widgets-holder-wrap iz-holder" id="available-widgets">';
						$output .= '<div class="wa-sidebar-name settings_email">';
							$output .= '<div class="sidebar-name-arrow">';
							$output .= '<br>';
							$output .= '</div>';
								$output .= '<h3>';
									$output .= 'Email Setup';
								$output .= '</h3>';
						$output .= '</div>';
	
						$output .= '<div class="widget-holder" style="display:none;">';							
							$output .= '<div id="widget-list" class="settings_email newform  email_setup__'.$newform_Id.' widget-list">';
								$output .= '<fieldset class="radio  confirmation_mail">
												<legend title="Choose whether to send a confirmation email to the user completing a form.">Send Confirmation Mail to user? <p class="field_description ui-icon-info ui-icon" ></p></legend>
												<div class="iz-form-item">
													<select name="send_user_mail">
														<option value="yes">Yes</option>
														<option value="no" selected="selected">No</option>
													</select>											  
												</div>
										  </fieldset>
										  <fieldset class="check mail_to_user_address" style="display:none;">
										  		<legend title="Select the custom fields (as created) to be used to send the user confirmation email.">Users mail address <p class="field_description ui-icon-info ui-icon" ></p></legend>
											  	<div class="iz-form-item">
													<div class="error_msg ui-state-error no_mail_error" style="display: block;"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>There is currently no fields in this form.<br /><br />Drag an email formated field into the form and you wil be able to select it here to be used to send user confirmation emails.</div>
											  	</div>
										  </fieldset>
										  <fieldset class="text mail_to">
												<legend title="Comma-separated list of email addresses to recieve mails when form is submitted.<br /><br />Note: The admin email specified in your wordpress general settings will always receive an email containing the form data as backup.">Mail To <p class="field_description ui-icon-info ui-icon" ></p></legend>
												<div class="iz-form-item">
													<input type="text" class=" " value="'.$setting_values['mail_to'].'" name="mail_to">
											  	</div>
										  </fieldset>
										  <fieldset class="text confirmation_mail_subject">
												<legend title="This is the subject of the mail the specified addresses will receive.">Confirmation Mail Subject <p class="field_description ui-icon-info ui-icon" ></p></legend>
												<div class="iz-form-item" >
													<input type="text" class=" " value="'.$setting_values['confirmation_mail_subject'].'" name="confirmation_mail_subject">
											  	</div>
										  </fieldset>
										  <fieldset class="textarea confirmation_mail_body">
											<legend title="Add HTML or normal text here to be sent to addresses specified in above field (Mail to) after the form is submited.<br>
												  <br>
												  Use shortcode <strong>[form_data]</strong> were mail form entry details are to be inserted.">Confirmation Mail Body <p class="field_description ui-icon-info ui-icon" ></p></legend>
											<div class="iz-form-item">
												<textarea name="confirmation_mail_body" class="">'.(($setting_values['confirmation_mail_body']=='') ? '[form_data]' : $setting_values['confirmation_mail_body'] ).'</textarea>
											  </div>
										  </fieldset>
										  <fieldset class="text from_address">
											<legend title="The address from were the mail origenates.">From Address <p class="field_description ui-icon ui-icon-info" ></p></legend>
											  <div class="iz-form-item">
												 <input type="text" class=" " value="'.$setting_values['from_address'].'" name="from_address">
											</div>
										  </fieldset>
										  <fieldset class="text from_name">
											<legend title="The name of the person/organization from were the mail origenates.">From Name <p class="field_description ui-icon-info ui-icon" ></p></legend>
											  <div class="iz-form-item">
												<input type="text" class=" " value="'.$setting_values['from_name'].'" name="from_name">
											  </div>
										  </fieldset>
										';			
							$output .= '</div>';
							
							if($get_forms)
								{
								foreach($get_forms as $get_form)
										{
										$output .= '<div id="widget-list" class="settings_email email_setup__'.$get_form->Id.' widget-list" style="display:none;">';
													$output .= '<fieldset class="radio  confirmation_mail">
															<legend title="Choose whether to send a confirmation email to the user completing a form.">Send Confirmation Mail to user? <p class="field_description ui-icon-info ui-icon" ></p></legend>
															<div class="iz-form-item">
																<select name="send_user_mail">
																	<option value="yes" '.(($get_form->send_user_mail=='yes' ) ? 'selected="selected"' : '').'>Yes</option>
																	<option value="no" '.(($get_form->send_user_mail=='no' || !$get_form->send_user_mail) ? 'selected="selected"' : '').'>No</option>
																</select>
															  </div>
														  </fieldset>
														  <fieldset class="check mail_to_user_address"  '.(($get_form->send_user_mail=='no' || !$get_form->send_user_mail) ? 'style="display:none;"' : '' ).'>
														   <legend title="Select email formated fields (as created) to be used to send the user confirmation emails.">Send to user using the following email fields: <p class="field_description ui-icon-info ui-icon" ></p></legend>
															  <div class="iz-form-item">';
																
																	$get_user_mail_addresses = explode(',',$get_form->user_email_field);	
																	if($get_form->user_email_field)
																		{
																		foreach($get_user_mail_addresses as $get_user_mail_address)
																			{
																			if($get_user_mail_address)
																				{
																				$checked = 'checked="checked"';
																				if(strstr($get_user_mail_address,'unchecked__'))
																					{
																					$checked = '';
																					}
																				$get_user_mail_address = str_replace('unchecked__','',$get_user_mail_address);
																				$get_user_mail_address = explode('__',$get_user_mail_address);
																				$output .= '<input type="checkbox" '.$checked.' class="checkbox" name="possible_mail_fields[]" value="'.$get_user_mail_address[0].'" id="user_mail__'.$get_user_mail_address[2].'"><label>'.IZC_Functions::unformat_name($get_user_mail_address[0]).'</label><br />';	
																				}
																			}
																		}
															
																$output .='</div>';
															$output .='<div class="error_msg ui-state-error no_mail_error" style="display: '.((!$get_user_mail_addresses) ? 'block' : 'none' ).';"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>There is currently no fields in this form.<br /><br />Drag an email formated field into the form and you wil be able to select it here to be used to send user confirmation emails.</div>';
															
														  $output .='</fieldset>
														  <fieldset class="text mail_to">
															<legend title="Comma-separated list of email addresses to recieve mails when form is submitted.">Mail To <p class="field_description ui-icon-info ui-icon" ></p></legend>
															<div class="iz-form-item">
																<input type="text" class=" " value="'.$get_form->mail_to.'" name="mail_to">
															  </div>
														  </fieldset>
														  <fieldset class="text confirmation_mail_subject">
															<legend title="This is the subject of the mail the specified addresses will receive.">Confirmation Mail Subject <p class="field_description ui-icon-info ui-icon" ></p></legend>
															<div class="iz-form-item" >
																 <input type="text" class=" " value="'.$get_form->confirmation_mail_subject.'" name="confirmation_mail_subject">
															  </div>
														  </fieldset>
														  <fieldset class="textarea confirmation_mail_body">
															<legend title="Add HTML or normal text here to be sent to addresses specified in above field (Mail to) after the form is submited.<br>
																  <br>
																  Use shortcode <strong>[form_data]</strong> were mail form entry details are to be inserted.">Confirmation Mail Body <p class="field_description ui-icon-info ui-icon" ></p></legend>
																	<div class="iz-form-item">
																<textarea name="confirmation_mail_body" class="">'.(($get_form->confirmation_mail_body=='') ? '[form_data]' : $get_form->confirmation_mail_body ).'</textarea>
															  </div>
														  </fieldset>
														  <fieldset class="text from_address">
															<legend title="The address from were the mail origenates.">From Address <p class="field_description ui-icon ui-icon-info" ></p></legend>
															  <div class="iz-form-item">
																 <input type="text" class=" " value="'.$get_form->from_address.'" name="from_address">
															 </div>
														  </fieldset>
														  <fieldset class="text from_name">
															<legend title="The name of the person/organization from were the mail origenates.">From Name <p class="field_description ui-icon-info ui-icon" ></p></legend>
															  <div class="iz-form-item">
																<input type="text" class=" " value="'.$get_form->from_name.'" name="from_name">
															  </div>
														  </fieldset>
														';	
									$output .= '</div>';	
									}
								}
							$output .= '</div>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '</div>';
						
						/***************************************/
						/*********** VISUAL EDITOR *************/
						/***************************************/
						$output .= '<div class="visual_editor" id="visual_editor" style="display:none;">';	
							$output .= '<div class="wa-sidebar-name settings_visual_editor">';
								$output .= '<h3>';
									$output .= 'Visual Editor';
								$output .= '</h3>';
							$output .= '</div>';
							$output .= '<div class="visual_settings" id="visual_settings" >';
			
								///////////////////* FONT */////////////////////////////
								$output .= '<h3>Font</h3>';
									$output .= '<div>';
										$output .= '<div class="setting-holder">';
											$output .= '<label>Font Style:</label>';
											$output .= '<div class="font-styles text-decoration"><u>U</u></div>';
											$output .= '<div class="font-styles font-style"><em>I</em></div>';							
											$output .= '<div class="font-styles font-weight"><strong>B</strong></div>';
										$output .= '</div>';
										$output .= '<div class="setting-holder color">';
											$output .= '<label for="color">Color:</label>';
											$output .= '<input type="text" value="" id="color" name="color" class="color">';
										$output .= '</div>';
										$output .= '<div class="setting-holder select">';
											$output .= '<label for="border-style">Font Family:</label>';
											$output .= '
											<select name="font-family" id="font-family" class="slider">
												<optgroup class="browser-safe-fonts" label="Browser-safe fonts">
													<option value="">Default</option>
													<option value="Arial">Arial</option>
													<option value="Comic Sans MS">Comic Sans MS</option>
													<option value="Courier New">Courier New</option>
													<option value="Georgia">Georgia</option>
													<option value="Impact">Impact</option>
													<option value="Times New Roman">Times New Roman</option>
													<option value="Trebuchet MS">Trebuchet MS</option>
													<option value="Verdana">Verdana</option>
												</optgroup>
												';
											if($data)
												{
												$output .= '<optgroup class="single-fields" label="Fonts used by current theme">';
													foreach($fonts  as $font)
														{
														$font 		= str_replace('"','',$font);
														$font 		= str_replace(';','',$font);
														$font_style = explode(':',$font);
														$fonts 		= explode(',',$font_style[1]);
														
														foreach($fonts as $single_font)
															$individual_fonts_array[$single_font] = $single_font;
														}
														
													$individual_fonts = array_unique($individual_fonts_array);
																					
													foreach($individual_fonts as $individual_font)				
														$output .= '<option value="'.trim($individual_font).'">'.$individual_font.'</option>';
											
												$output .= '</optgroup>';
												}
										$output .= '</select>';
										$output .= '</div>';
										$output .= '<div class="setting-holder select">';
											$output .= '<label for="text-align">Text Aling:</label>';
											$output .= '
											<select name="text-align" id="text-align" class="slider">
												<option value="none">None</option>
												<option value="left">Left</option>
												<option value="right">Right</option>
												<option value="center">Center</option>
												<option value="justify">Justify</option>
											</select>';
										$output .= '</div>';
										$output .= '<div class="setting-holder select">';
											$output .= '<label for="text-transform">Text Transform:</label>';
											$output .= '
											<select name="text-transform" id="text-transform" class="slider">
												<option value="none">Default</option>
												<option value="uppercase">Uppercase</option>
												<option value="lowercase">Lowercase</option>
												<option value="capitalize">Capitalize</option>
											</select>';
										$output .= '</div>';
										$output .= '<div class="setting-holder">';
											$output .= '<label for="font-size">Font Size:</label>';
											$output .= '<input name="font-size" id="font-size" class="slider" type="text"/>';
											$output .= '<div class="slider" id="slider-font-size"></div>';
										$output .= '</div>';
										$output .= '<div class="setting-holder">';
											$output .= '<label for="line-height">Line Height:</label>';
											$output .= '<input name="line-height" id="line-height" class="slider" type="text"/>';
											$output .= '<div class="slider" id="slider-line-height"></div>';
										$output .= '</div>';
										$output .= '<div class="setting-holder">';
											$output .= '<label for="letter-spacing">Letter Spacing:</label>';
											$output .= '<input name="letter-spacing" id="letter-spacing" class="slider" type="text"/>';
											$output .= '<div class="slider" id="slider-letter-spacing"></div>';
										$output .= '</div>';
										
									$output .= '</div>';
									
								///////////////////* DIMENTIONS */////////////////////////////
								 $output .= '<h3>Dimentions</h3>';
								 $output .= '<div>';
									$output .= '<div class="setting-holder">';
										$output .= '<label for="width">Width:</label>';
										$output .= '<input name="width" id="width" class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-width"></div>';
									$output .= '</div>';
									$output .= '<div class="setting-holder">';
										$output .= '<label for="height">Height:</label>';
										$output .= '<input name="height" id="height" class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-height"></div>';
									$output .= '</div>';
								 $output .= '</div>';	
							
								///////////////////* BACKGROUND */////////////////////////////
								 $output .= '<h3>Background</h3>';
								 $output .= '<div class="background-settings">';
									
									$output .= '<div class="setting-holder color">';
										$output .= '<label for="background-color">Background Color:</label>';
										$output .= '<input type="text" value="" id="background-color" name="background-color" class="color">';
									$output .= '</div>';
									$output .= '<div class="setting-holder select">';
										$output .= '<label for="text-transform" title="Not compatible from IE7-8">Set Gradiant (css)</label>';
										$output .= '
										<div style="margin-top: 15px; padding: 15px;width: 95%;" class="ui-state-highlight ui-corner-all">
										<span style="float: left; margin-right: 5px;" class="ui-icon ui-icon-info"></span>
										Adding gradient backrounds is only available in the full version <br />

<a class="get_full_version" href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix">Download full version</a>
										</div></div>';
									$output .= '</div>';
									
									
								///////////////////* PADDING */////////////////////////////
								 $output .= '<h3>Padding</h3>';
								 $output .= '<div>';
									$output .= '<div class="setting-holder">';
										$output .= '<label for="padding-top">Padding Top:</label>';
										$output .= '<input name="padding-top" id="padding-top"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-padding-top"></div>';
									$output .= '</div>';
									
									$output .= '<div class="setting-holder">';
										$output .= '<label for="padding-right">Padding Right:</label>';
										$output .= '<input name="padding-right" id="padding-right"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-padding-right"></div>';
									$output .= '</div>';
									
									$output .= '<div class="setting-holder">';
										$output .= '<label for="padding-bottom">Padding Bottom:</label>';
										$output .= '<input name="padding-bottom" id="padding-bottom"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-padding-bottom"></div>';
									$output .= '</div>';
									
									$output .= '<div class="setting-holder">';
										$output .= '<label for="padding-left">Padding Left:</label>';
										$output .= '<input name="padding-left" id="padding-left"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-padding-left"></div>';
									$output .= '</div>';
								 $output .= '</div>';	
									
								 ///////////////////* MARGIN */////////////////////////////
								 $output .= '<h3>Margin</h3>';
								 $output .= '<div>';
									$output .= '<div class="setting-holder">';
										$output .= '<label for="margin-top">Margin Top:</label>';
										$output .= '<input name="margin-top" id="margin-top"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-margin-top"></div>';
									$output .= '</div>';
									
									$output .= '<div class="setting-holder">';
										$output .= '<label for="margin-right">Margin Right:</label>';
										$output .= '<input name="margin-right" id="margin-right"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-margin-right"></div>';
									$output .= '</div>';
									
									$output .= '<div class="setting-holder">';
										$output .= '<label for="margin-bottom">Margin Bottom:</label>';
										$output .= '<input name="margin-bottom" id="margin-bottom"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-margin-bottom"></div>';
									$output .= '</div>';
									
									$output .= '<div class="setting-holder">';
										$output .= '<label for="margin-left">Margin Left:</label>';
										$output .= '<input name="margin-left" id="margin-left"  class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-margin-left"></div>';
									$output .= '</div>';
								$output .= '</div>';
								
								 ///////////////////* BORDER */////////////////////////////
								$output .= '<h3>Border</h3>';
								$output .= '<div>';
									$output .= '<div class="setting-holder select">';
										$output .= '<label for="border-style">Border Style:</label>';
										$output .= '
										<select name="border-style" id="border-style" class="slider">
											<option value="none">None</option>
											<option value="solid">Solid</option>
											<option value="dashed">Dashed</option>
											<option value="dotted">Dotted</option>
										</select>';
									$output .= '</div>';
									$output .= '<div class="setting-holder color">';
										$output .= '<label for="border-color">Border Color:</label>';
										$output .= '<input type="text" value="#999999" id="border-color" name="border-color" class="color">';
									$output .= '</div>';
									$output .= '<div class="setting-holder">';
										$output .= '<label for="border-width">Border Width:</label>';
										$output .= '<input name="border-width" id="border-width" class="slider" type="text"/>';
										$output .= '<div class="slider" id="slider-border-width"></div>';
									$output .= '</div>';
									$output .= '<div class="setting-holder">';
										$output .= '<label for="border-radius" title="Not compatible from IE7-8">Border Radius (css3):</label><div style="margin-top: 15px; padding: 15px;width: 95%;" class="ui-state-highlight ui-corner-all">
										<span style="float: left; margin-right: 5px;" class="ui-icon ui-icon-info"></span>
										Adding rounded corners is only available in the full version <br />

<a class="get_full_version" href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix">Download full version</a>
										</div>';
										
									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
						$output .= '</div>';
					
					
					/***************************************/
					/************* FORM LAYOUT *************/
					/***************************************/
					$output .= '<div class="widgets-holder-wrap iz-holder" id="available-widgets">';
						$output .= '<div class="wa-sidebar-name settings_layout show">';
							$output .= '<div class="sidebar-name-arrow">';
							$output .= '<br>';
							$output .= '</div>';
								$output .= '<h3>';
									$output .= 'Form Layout';
								$output .= '</h3>';
						$output .= '</div>';
	
						$output .= '<div class="widget-holder" >';
						$output .= '<link class="layout" rel="stylesheet" type="text/css" href="'.get_option('siteurl').'/wp-content/plugins/x-forms-express/css/layout/one-column-full.css"/>';
													
							$output .= '<div style="margin-top: 15px; padding: 15px;width: 95%;" class="ui-state-highlight ui-corner-all">
										<span style="float: left; margin-right: 5px;" class="ui-icon ui-icon-info"></span>
										Changing Form Layouts is only available in the full version. But you can still preview. <br />

<a class="get_full_version" href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix">Download full version</a>
										</div><div class="set_layout one-column-full">1 Column (full width)</div>';
							$output .= '<div class="set_layout one-column-half">1 Column (&frac12; width)</div>';
							$output .= '<div class="set_layout two-column">2 Columns</div>';
							$output .= '<div class="set_layout three-column">3 Columns</div>';
						$output .= '</div>';
					$output .= '</div>';
					
					/***************************************/
					/************* COLOR SCHEME ************/
					/***************************************/
					$output .= '<div class="widgets-holder-wrap iz-holder" id="available-widgets">';
						$output .= '<div class="wa-sidebar-name settings_color_scheme show">';
							$output .= '<div class="sidebar-name-arrow">';
							$output .= '<br>';
							$output .= '</div>';
								$output .= '<h3>';
									$output .= 'Colour Scheme';
								$output .= '</h3>';
						$output .= '</div>';
						
						$output .= '<div class="widget-holder" >';
						$output .= '<link class="color_scheme" rel="stylesheet" type="text/css"  href="'.get_option('siteurl').'/wp-content/plugins/x-forms-express/css/default/jquery.ui.theme.css" />';
													
							$output .= '<p class="description">Choose Color Scheme</p><div style="margin-top: 15px; padding: 15px;width: 95%;" class="ui-state-highlight ui-corner-all">
										<span style="float: left; margin-right: 5px;" class="ui-icon ui-icon-info"></span>
										Changing colour schemes is only available in the full version. But you can still preview. <br />
<a class="get_full_version" href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix">Download full version</a>
										</div>';
							$output .= '<select name="change_color_scheme" class="ui-state-default">';
								$output .= '<option value="default">Default</option>';
								$output .= '<option value="black-tie">black-tie</option>';
								$output .= '<option value="blitzer">blitzer</option>';
								$output .= '<option value="cupertino">cupertino</option>';
								$output .= '<option value="dark-hive">dark-hive</option>';
								$output .= '<option value="dot-luv">dot-luv</option>';
								$output .= '<option value="eggplant">eggplant</option>';
								$output .= '<option value="excite-bike">excite-bike</option>';
								$output .= '<option value="flick">flick</option>';
								$output .= '<option value="hot-sneaks">hot-sneaks</option>';
								$output .= '<option value="humanity">humanity</option>';
								$output .= '<option value="le-frog">le-frog</option>';
								$output .= '<option value="mint-choc">mint-choc</option>';
								$output .= '<option value="overcast">overcast</option>';
								$output .= '<option value="pepper-grinder">pepper-grinder</option>';
								$output .= '<option value="redmond">redmond</option>';
								$output .= '<option value="smoothness">smoothness</option>';
								$output .= '<option value="south-street">south-street</option>';
								$output .= '<option value="start">start</option>';
								$output .= '<option value="sunny">sunny</option>';
								$output .= '<option value="swanky-purse">swanky-purse</option>';
								$output .= '<option value="trontastic">trontastic</option>';
								$output .= '<option value="ui-darkness">ui-darkness</option>';
								$output .= '<option value="ui-lightness">ui-lightness</option>';
								$output .= '<option value="vader">vader</option>';
							$output .= '</select>';
					$output .= '</div> ';
				$output .= '</div> ';
			$output .= '</div> ';
		$output .= '</div> ';
	if(isset($_GET['form_id']))
			{
			$output .= '<script type="text/javascript">
					setTimeout(
						function()
							{
								jQuery(\'li#tab_'.$_GET['form_id'].'\').trigger(\'click\');
							},1000
						);
				</script>';	
			}
	return $output;
	}
}

class XFormsExpress_widget extends WP_Widget{
	public $name = 'X Forms';
	public $widget_desc = 'Add x-forms to your sidebars.';
	public $control_options = array('title' => '','form_id' => '',);
	function __construct(){
		$widget_options = array('classname' => __CLASS__,'description' => $this->widget_desc);
		parent::__construct( __CLASS__, $this->name,$widget_options , $this->control_options);
	}
	function widget($args, $instance){
		XFormsExpress_ui_output($instance['form_id'],true);
	}
	public function form( $instance ){
		$placeholders = array();
		foreach ( $this->control_options as $key => $val )
			{
			$placeholders[ $key .'.id' ] = $this->get_field_id( $key);
			$placeholders[ $key .'.name' ] = $this->get_field_name($key );
			if ( isset($instance[ $key ] ) )
				$placeholders[ $key .'.value' ] = esc_attr( $instance[$key] );
			else
				$placeholders[ $key .'.value' ] = $this->control_options[ $key ];
			}
		global $wpdb;
		$get_forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_x_forms ORDER BY Id DESC');
		$current_form = XFormsExpress_widget_controls::parse('[+form_id.value+]', $placeholders);
		
		$tpl  = '<input id="[+title.id+]" name="[+title.name+]" value="'.IZC_Database::get_title(XFormsExpress_widget_controls::parse('[+form_id.value+]', $placeholders),'wap_x_forms').'" class="widefat" style="width:96%;display:none;" />';
		
		if($get_forms)
			{
			$tpl .= '<label for="[+form_id.id+]">Select Form:</label><br />';
			$tpl .= '<select id="[+form_id.id+]" name="[+form_id.name+] " style="width:100%;">';
				$tpl .= '<option value="0">-- Select form --</option>';
				foreach($get_forms as $form)
					$tpl .= '<option value="'.$form->Id.'" '.(($form->Id==$current_form) ? 'selected="selected"' : '' ).'>'.$form->title.'</option>';
			$tpl .= '</select>';
			}
		else
			$tpl .=  '<p>No forms have been created yet.<br /><br /><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=WA-x_forms-main">Click here</a> or click on "X Forms" on the left-hand menu where you will be able to create a form that would be avialable here to select as a widget.</p>';
		print XFormsExpress_widget_controls::parse($tpl, $placeholders);
	}
	static function register_this_widget(){
		register_widget(__CLASS__);
	}
}
class XFormsExpress_widget_controls {
	static function parse($tpl, $hash){
   	   foreach ($hash as $key => $value)
			$tpl = str_replace('[+'.$key.'+]', $value, $tpl);
	   return $tpl;
	}
}
?>