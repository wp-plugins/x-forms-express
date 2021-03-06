<?php
//This is a action for an AJAX function to get table records
add_action('wp_ajax_populate',array('IZC_Database','build_admin_table'));
//This is a action for an AJAX function to delete table records
add_action('wp_ajax_delete_record',array('IZC_Database','delete_record'));

//DB actions
add_action('wp_ajax_do_insert', array('IZC_Database','do_insert'));
add_action('wp_ajax_do_edit', array('IZC_Database','update'));
add_action('wp_ajax_duplicate_record', array('IZC_Database','duplicate_record'));


add_action('wp_ajax_insert_nex_form', array('IZC_Database','insert_nex_form'));
add_action('wp_ajax_edit_form', array('IZC_Database','update_form'));

add_action('wp_ajax_load_nex_form', array('IZC_Database','load_nex_form'));
add_action('wp_ajax_load_nex_form_attr', array('IZC_Database','load_nex_form_attr'));
add_action('wp_ajax_load_nex_form_hidden_fields', array('IZC_Database','load_nex_form_hidden_fields'));

add_action('wp_ajax_populate_dropdown',array('IZC_Database','populate_dropdown_list'));

add_action('wp_ajax_populate_form_entry',array('IZC_Database','populate_form_entry'));

if(!class_exists('IZC_Database'))
	{
	class IZC_Database{
		
		public $plugin_table;
		public $plugin_alias;
		public $module_table;
		public $foreign_key;
		public $link_modules;
		
		function __construct(){
			if(@$_POST['action']=='batch-delete'   || @$_POST['action2']=='batch-delete')  	{ $this -> batch_delete_records(@$_POST['checked'],@$_POST['table']); }
			}
		
		
		/***************************************/
		/***********   Admin Table   ***********/
		/***************************************/
		public function build_admin_table(){
	
			global $wpdb;
			
			$args 		= str_replace('\\','',$_POST['args']);
			$headings 	= json_decode($args);
			$get_tree 	= $wpdb->prepare('SHOW FIELDS FROM '. $wpdb->prefix .$_POST['table'].' LIKE "parent_Id"');
			$tree 		= $wpdb->query($get_tree);
			
			$additional_params = json_decode(str_replace('\\','',$_POST['additional_params']),true);
			
			if(is_array($additional_params))
				{
				foreach($additional_params as $column=>$val)
					$where_str .= ' AND '.$column.'="'.$val.'"';
				}
			
			if($_POST['nex_forms_id'])
				$where_str .= ' AND nex_forms_Id='.$_POST['nex_forms_id'];
			
			
			$sql = $wpdb->prepare('SELECT * FROM '. $wpdb->prefix . $_POST['table'].' WHERE Id <> "" 
											'.(($tree) ? ' AND parent_Id="0"' : '').' 
											'.(($_POST['plugin_alias']) ? ' AND (plugin="'.$_POST['plugin_alias'].'" || plugin="shared")' : '').' 
											'.$where_str.'   
											ORDER BY 
											'.((isset($_POST['orderby']) && !empty($_POST['orderby'])) ? $_POST['orderby'].' 
											'.$_POST['order'] : 'Id DESC').' 
											LIMIT '.((isset($_POST['current_page'])) ? $_POST['current_page']*10 : '0'  ).',10 ');
			$results 	= $wpdb->get_results($sql);
			
			$output .= '<div class="modal fade" id="viewFormEntry" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:10001 !important;">
						  <div class="modal-dialog">
							<div class="modal-content">
							  <div class="modal-header alert alert-info">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="myModalLabel">Form Entry</h4>
							  </div>
							  <div class="modal-body">
								
							  </div>
							  <div class="modal-footer ">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							  </div>
							</div>
						  </div>
						</div>';
						
			$output .= '<div class="modal fade" id="getPdfAddon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:10001 !important;">
						  <div class="modal-dialog">
							<div class="modal-content">
							  <div class="modal-header alert alert-success">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="myModalLabel">Get Export to PDF add-on</h4>
							  </div>
							  <div class="modal-body">
									If you have a need to export your form entries to profesional looking PDF\'s then you need this add-on.<br />
<br />
<a class="btn btn-success" href="http://codecanyon.net/user/Basix/portfolio?ref=Basix" target="_blank">Get Export to PDF for NEX-Forms</a>
							  </div>
							  
							</div>
						  </div>
						</div>';
			
			
			if($results)
				{			
				foreach($results as $data)
					{	
					$output .= '<tr class="row parent '.((IZC_Database::has_child($data->Id,$_POST['table'])) ? 'has-child' : '').'" onClick="showHide(this,\'level-'.$data->Id.'\',1,10,'.$data->Id.');" id="tag-'.$data->Id.'">';
					$output .= '<th class="check-column" scope="row"><input type="checkbox" value="'.$data->Id.'" name="checked[]"></th>';
					
					$k=1;
					foreach($headings as $heading)	
						{
						
						if(is_object($heading))
							{
							$grouplabel = IZC_Functions::format_name($heading->grouplabel);
							$range_type = $heading->type;
							$heading = 'range';
							}
	
						$heading = IZC_Functions::format_name($heading);
						$heading = str_replace('_id','_Id',$heading);
						
						if($heading=='range')
							{
							$range_from = $grouplabel.'_rangefrom';
							$range_to 	= $grouplabel.'_rangeto';
							$val 		= '<strong>from</strong> '.(($data->$range_from) ? IZC_Functions::format_date($data->$range_from) : 'Undefined').' <strong>to</strong> '.(($data->$range_to) ? IZC_Functions::format_date($data->$range_to) : 'Undefined');
							}
						elseif($heading=='user_Id')
							{
							$val = IZC_Database::get_username($data->$heading);	
							}
						elseif($heading=='form_data')
							{
							$val = '<div class="btn btn-primary view_form_entry" data-target="#viewFormEntry" data-toggle="modal"  data-id="'.$data->Id.'">View</div>';	
							}
						else
							{
							$val = (strstr($heading,'Id')) ? IZC_Database::get_title($data->$heading,'wap_'.str_replace('_Id','',$heading)) : $data->$heading;
							
							
							$val = str_replace('\\', '', IZC_Functions::view_excerpt($val,25));
							}
						
						
						
						
						
						$output .= '<td class="manage-column column-'.$heading.'">'.(($k==1) ? '<strong>'.$val.'</strong>' : $val).'';
						$output .= (($k==1) ? '<div class="row-actions"></span><span class="delete"><a href="javascript:delete_record(\''.$data->Id.'\',\''.$_POST['table'].'\');" >Delete</a></span></div>' : '' ).'</td>';
						$k++;
						//<span class="edit"><a href="?page='.$_POST['page'].'&Id='.$data->Id.'&table='.$_POST['table'].'" class="edit-tag">Edit</a> | 
						}
					if ( is_plugin_active( 'nex-forms-export-to-pdf/main.php' ) )
						$output .= '<th class="expand" scope="row"><a target="_blank" title="PDF [new window]" href="'.WP_PLUGIN_URL . '/nex-forms-export-to-pdf/examples/main.php?entry_ID='.$data->Id.'" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> PDF</div></a></th>';
					else
						$output .= '<th class="expand" scope="row"><a target="_blank" title="Get export to PDF add-on" href="#" data-target="#getPdfAddon" data-toggle="modal" class="text-danger">inactive</a></th>';
					$output .= '</tr>';	
					
					if($tree)
						$output .= IZC_Database::build_descendants_list($data->Id, $headings, $_POST['table']);
					}
				}
			else
				{
				$output .= '<tr>';	
				$output .= '<td></td><td class="manage-column" colspan="'.(count($headings)).'">No items found</td>';
				$output .= '</tr>';
				}
				
			echo $output;
			die();
		}
		
		
		
		public function load_nex_form(){
			global $wpdb;
			$get_form = $wpdb->prepare('SELECT form_fields FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id='.$_POST['form_Id']);
			$form = $wpdb->get_row($get_form);
			echo str_replace('\\','',$form->form_fields);
			die();	
		}
		
		public function populate_form_entry(){
			global $wpdb;
			echo 'Sorry, form entries can only be viewed and exported with the <a href="http://codecanyon.net/item/nexforms-lite-wordpress-form-builder-plugin/5214711?ref=Basix" class="btn btn-xs btn-success">Pro Version</a>';
			
			die();	
		}
		
		
		public function load_nex_form_attr(){
			global $wpdb;
			$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id='.$_POST['form_Id']);
			$form = $wpdb->get_row($get_form);
			$output .= '<div class="mail_to">'.$form->mail_to.'</div>';
			$output .= '<div class="confirmation_mail_body">'.str_replace('\\','',$form->confirmation_mail_body).'</div>';
			$output .= '<div class="confirmation_mail_subject">'.str_replace('\\','',$form->confirmation_mail_subject).'</div>';
			$output .= '<div class="from_address">'.$form->from_address.'</div>';
			$output .= '<div class="from_name">'.$form->from_name.'</div>';
			$output .= '<div class="on_screen_confirmation_message">'.str_replace('\\','',$form->on_screen_confirmation_message).'</div>';
			$output .= '<div class="confirmation_page">'.$form->confirmation_page.'</div>';
			$output .= '<div class="google_analytics_conversion_code">'.str_replace('\\','',$form->google_analytics_conversion_code).'</div>';
			$output .= '<div class="send_user_mail">'.$form->send_user_mail.'</div>';
			$output .= '<div class="user_email_field">'.$form->user_email_field.'</div>';
			$output .= '<div class="on_form_submission">'.$form->on_form_submission.'</div>';
			$output .= '<div class="post_action">'.$form->post_action.'</div>';
			$output .= '<div class="post_type">'.$form->post_type.'</div>';
			$output .= '<div class="custom_url">'.$form->custom_url.'</div>';
			
			echo $output;
			die();	
		}
		
		
		public function load_nex_form_hidden_fields(){
			global $wpdb;
			$get_form = $wpdb->prepare('SELECT hidden_fields FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id='.$_POST['form_Id']);
			$form = $wpdb->get_row($get_form);
			
			$hidden_fields_raw = explode('[end]',$form->hidden_fields);
			
			foreach($hidden_fields_raw as $hidden_field)
				{
				$hidden_field = explode('[split]',$hidden_field);
				if($hidden_field[0])
					{
					$output .= '
						<div class="row hidden_field">
							<div class="col-sm-5 ">
								<label>Field Name</label><br>
								<input type="text" value="'.$hidden_field[0].'" class="form-control field_name">
							</div>
							<div class="col-sm-5">
								<label>Field Value</label><br>
								<input type="text" value="'.$hidden_field[1].'" class="form-control field_vlaue">
							</div>
							<div class="col-sm-2">
								<label>&nbsp;</label><br>
								<div class="btn btn-danger remove_hidden_field"><span class="fa fa-close"></span></div>
							</div>
						</div>';
					}
				}
			echo $output;
			die();	
		}
		
		/***************************************/
		/********   Dropdown Records   *********/
		/***************************************/
		public function populate_dropdown_list(){
			global $wpdb;
			$table 		= (isset($_POST['table'])) ? $_POST['table'] : $this->module_table;
			$plugin		= (isset($_POST['plugin_alias'])) ? $_POST['plugin_alias'] : $this->plugin_alias;
			$get_tree 	= $wpdb->prepare('SHOW FIELDS FROM '. $wpdb->prefix . $table .' LIKE "parent_Id"');
			$tree 		= $wpdb->query($get_tree);
			
			
			
			$sql = $wpdb->prepare('SELECT * FROM '. $wpdb->prefix . $table .'  WHERE 1=1 
											'.(($tree) ? ' 		AND parent_Id="0"' : '').'
											'.(($plugin) ? ' 	AND (plugin="'.$plugin.'" || plugin="shared" )' : ''));
			$results	= $wpdb->get_results($sql);
			//echo $sql;
			if(isset($_REQUEST['Id'])){
				global $wpdb;
				$get_selected  = $wpdb->prepare('SELECT '.$table.'_Id FROM '.$wpdb->prefix . $_REQUEST['table'] .' WHERE Id='.$_REQUEST['Id']);
				$selected  = $wpdb->get_var($get_selected);
			}
			
			//$output .= '<option value="0">---- Select ----</option>';
			
			if($results)
				{			
				foreach($results as $data)
					{
					$output .= '<option value="'.$data->Id.'" '.(($selected==$data->Id) ? 'selected="selected"' : '' ).'>'.(($data->title) ? $data->title : (($data->_name) ? $data->_name : (($data->mb_lacquer_ref) ? $data->mb_lacquer_ref : $data->code)) ).'</option>';
					if($tree)
						$output .= IZC_Database::build_descendants_list($data->Id, $headings, $table,0,'dropdown');
					}
				}
	
			if(isset($_POST['ajax'])) { echo $output; die(); }
			return $output;
		}
		
		public function populate_custom_dropdown_list($table,$field){
			
			global $wpdb;
	
			$get_results = $wpdb->prepare('SELECT '.$field.' FROM '. $wpdb->prefix . $table);
			$results = $wpdb->get_results($get_results);
		
			$output .= '<option value="0">---- Select ----</option>';
			
			if($results)
				{			
				foreach($results as $data)
					{
					$output .= '<option value="'.$data->$field.'" '.(($selected==$data->$field) ? 'selected="selected"' : '' ).'>'.$data->$field.'</option>';
					}
				}
			return $output;
		}
		
		
		/***************************************/
		/**********   List Records   ***********/
		/***************************************/
		public function list_items(){
			
			global $wpdb;
			
			$table = (isset($_POST['table'])) ? $_POST['table'] : $this->module_table;
			
			$get_tree 		= $wpdb->prepare('SHOW FIELDS FROM '. $wpdb->prefix . $table .' LIKE "parent_Id"');
			$tree 		= $wpdb->query($get_tree);
			
			$get_results 	= $wpdb->prepare('SELECT * FROM '. $wpdb->prefix . $table .' '.(($tree) ? ' WHERE parent_Id="0"' : '') );
			$results 	= $wpdb->get_results($get_results);
			
			if($results)
				{			
				foreach($results as $data)
					{
	
					$output .= '<li value="'.$this->get_title($data->Id,$table).'"><a href="'.get_option('siteurl').'/listings/'.$this->get_title($data->Id,$table).'-'.$table.'_Id~'.$data->Id.'">'.$data->title.'</a>';
					if($tree)
						{
						if(IZC_Database::has_child($data->Id,$table))
							{
							$output .= '<ul class="children '.((IZC_Database::count_children($data->Id,$table)>5) ? 'col2' : 'col1' ).'">';
							}
						$output .= IZC_Database::build_descendants_list($data->Id, $headings, $table,0,'list');
						
						if(IZC_Database::has_child($data->Id,$table))
							{
							$output .= '<li class="seam"> </li>';
							$output .= '</ul>';
							}
						}
					$output .= '</li>';
					}
				}
				
			if(isset($_POST['ajax'])) { echo $output; die(); }
			
			return $output;
		}
		
		
		
		public function build_descendants_list($Id, $headings, $table, $level=0, $view='table'){
	
			IZC_Database::get_ancestors($Id,$table);
	
			$ancestors 		= explode(',',$_SESSION['ancestors_ids']);
			$total_count 	= count($ancestors)-1;
			
			for($i=1;$i<=$total_count;$i++)
				$indent .= ($view=='table') ? '&mdash; ' : '&nbsp;&nbsp; ' ; $level = $i-1;;
				
	
			if(IZC_Database::has_child($Id,$table))
				{
				switch($view)
					{
					case 'table': 
						foreach(IZC_Database::get_children($Id,$table) as $child)
							{	
							$output .= '<tr id="tag-'.$child->Id.'" class="'.((IZC_Database::has_child($child->Id,$table)) ? 'has-child' : '').' row child '.IZC_Database::get_origen($child->Id,$table).'-level-'.$level.' level-'.$Id.'-'.$level.'" onClick="showHide(this,\'level-'.$child->Id.'\','.($level+1).',10,\''.IZC_Database::get_origen($child->Id,$table).'\');">';
							$output .= '<th class="check-column" scope="row"><input type="checkbox" value="'.$child->Id.'" name="checked[]"></th>';
							$k=1;
							foreach($headings as $heading)	
								{
								$heading = IZC_Functions::format_name($heading);
								$heading = str_replace('_id','_Id',$heading);
								
								$val = (stristr($heading,'id')) ? IZC_Database::get_title($child->$heading,str_replace('_Id','',$heading)) : $child->$heading;
	
								$output .= '<td class="manage-column   column-'.$val.'">'.(($k==1) ? $indent.' <strong><a href="" class="row-title">'.$val.'</a></strong>' : $val).'';
								$output .= (($k==1) ? '<div class="row-actions"><span class="edit"><a href="?page='.$_POST['page'].'&Id='.$child->Id.'&table='.$_POST['table'].'" class="edit-tag">Edit</a> | </span><span class="delete"><a href="javascript:delete_record(\''.$child->Id.'\',\''.$_POST['table'].'\');" >Delete</a></span></div>' : '' ).'</td>';
								$k++;
								}
							$output .= '<th class="expand" scope="row"></th>';
							$output .= '</tr>';
							$output .= IZC_Database::build_descendants_list($child->Id,$headings,$table,$level);
							}
					break;
					
					case 'dropdown': 
						foreach(IZC_Database::get_children($Id,$table) as $child)
							{	
							$output .= '<option value="'.$child->Id.'" >'.$indent.$child->title.'</option>';
							$output .= IZC_Database::build_descendants_list($child->Id,$headings,$table,$level,$view);
							}
					break;
					
					case 'list': 
						foreach(IZC_Database::get_children($Id,$table) as $child)
							{
							$output .= '<li class="col1" id="item-'.$child->Id.'"><a href="'.get_option('siteurl').'/listings/'.$this->get_title($child->Id,$table).'-'.$table.'_Id~'.$child->Id.'">'.$child->title.'</a>';
							
							if(IZC_Database::has_child($child->Id,$table))
								{
								$output .= '<ul class="children">';
								$output .= IZC_Database::build_descendants_list($child->Id,$headings,$table,$level,$view);
								$output .= '</ul>';
								}
								
							$output .= '</li>';
							}
					break;
					}
				return $output;
				}
		}
		
		
		/***************************************/
		/**********   Tree Traversal   *********/
		/***************************************/
		public function get_descendants($Id,$table){
			if(IZC_Database::has_child($Id,$table))
				{
				foreach(IZC_Database::get_children($Id,$table) as $child)
					{
					$_SESSION['decendants'] .= $child->Id.',';
					IZC_Database::get_descendants($child->Id,$table);
					}
				}
		}
		
		public function get_ancestors($Id,$table){
			$_SESSION['ancestors_ids']  = '';
			if(IZC_Database::is_child($Id,$table))
				{
				IZC_Database::get_ancestors(IZC_Database::get_parent($Id,$table),$table);
				$_SESSION['ancestors_ids']  .= $Id.',';
				}
		} 
	
	
		public function get_origen($Id,$table){
			$_SESSION['ancestors_ids'] = '';
			IZC_Database::get_ancestors($Id,$table);
			$ancestors 	= explode(',',$_SESSION['ancestors_ids']);
			$origen 	= $ancestors[0];
			return $origen ;
		}
	
		public function has_child($Id,$table){
			global $wpdb;
			$result = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $table ." WHERE parent_Id = '".$Id."'");
			return $wpdb->get_results($result);
		}
		public function get_children($Id,$table){
			global $wpdb;
			$result = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $table ." WHERE parent_Id = '".$Id."'");
			return $wpdb->get_results($result);
		}
		public function count_children($Id,$table){
			global $wpdb;
			$result = $wpdb->prepare("SELECT count(*) FROM " . $wpdb->prefix . $table ." WHERE parent_Id = '".$Id."'");
			return $wpdb->get_var($result);
		}
		public function is_child($Id,$table){
			global $wpdb;
			$result = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $table ." WHERE Id = '".$Id."'");
			return $wpdb->get_results($result);
		}
		public function get_parent($Id,$table){
			global $wpdb;
			$result = $wpdb->prepare("SELECT parent_Id FROM " . $wpdb->prefix . $table ." WHERE Id = '".$Id."'");
			return $wpdb->get_var($result);
		}
		
		
		/***************************************/
		/**********   Defualt Fields   *********/
		/***************************************/
		
		public function get_title($Id,$table){
			global $wpdb;
			
			$get_the_title = $wpdb->prepare("SELECT title FROM " . $wpdb->prefix . $table ." WHERE Id = '".$Id."'");
			$the_title = $wpdb->get_var($get_the_title);
	
			if(!$the_title)
				{
				$get_the_title = $wpdb->prepare("SELECT _name FROM " . $wpdb->prefix . $table ." WHERE Id = '".$Id."'");
				$the_title = $wpdb->get_var($get_the_title);
				}
		
			return $the_title;
		}
		
		public function get_username($Id){
			global $wpdb;
			$get_username = $wpdb->prepare("SELECT display_name FROM " . $wpdb->prefix . "users WHERE ID = '".$Id."'");
			$username = $wpdb->get_var($get_username);
			return $username;
		}
		
		public function get_description($Id,$table){
			global $wpdb;
			$result = $wpdb->prepare("SELECT description FROM " . $wpdb->prefix . $table ." WHERE Id = '".$Id."'");
			return $wpdb->get_var($result);
		}
		
		
		
		/***************************************/
		/*********   Database Actions   ********/
		/***************************************/	
		public function insert_nex_form(){
			
			global $wpdb;
			echo '<pre>';
			
			$get_fields 	= $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $_POST['table']);
			$fields 	= $wpdb->get_results($get_fields);
			$field_array = array();
			foreach($fields as $field)
				{
				if(isset($_POST[$field->Field]))
					{
					$field_array[$field->Field] = $_POST[$field->Field];
					}	
				}
			$data_insert = $wpdb->prepare($wpdb->insert ( $wpdb->prefix . $_POST['table'], $field_array ));
			$insert = $wpdb->query($data_insert);
			die();
		}
		
		public function do_insert(){
			global $wpdb;
			$get_fields 	= $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $_POST['table']);
			$fields 	= $wpdb->get_results($get_fields);
			$field_array = array();
			foreach($fields as $field)
				{
				if(isset($_POST[$field->Field]))
					{
					if(is_array($_POST[$field->Field]))
						$field_array[$field->Field] = json_encode($_POST[$field->Field],JSON_FORCE_OBJECT);
					else
						$field_array[$field->Field] = $_POST[$field->Field];
					}	
				}
			$data_insert = $wpdb->prepare($wpdb->insert ( $wpdb->prefix . $_POST['table'], $field_array ));
			$insert = $wpdb->query($data_insert);
			echo mysql_insert_id();
			die();
		}
		public function update(){
		global $wpdb;
		$get_fields 	= $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $_POST['table']);
		$fields 	= $wpdb->get_results($get_fields);
		$field_array = array();
		foreach($fields as $field)
			{
			if(isset($_POST[$field->Field]))
				{
				if(is_array($_POST[$field->Field]))
					$field_array[$field->Field] = json_encode($_POST[$field->Field],JSON_FORCE_OBJECT);
				else
					$field_array[$field->Field] = $_POST[$field->Field];
				}	
			}
		$update = $wpdb->prepare($wpdb->update ( $wpdb->prefix . $_POST['table'], $field_array, array(	'Id' => $_POST['edit_Id']) ));
		$do_update = $wpdb->query($update);
		echo $_POST['edit_Id'];
		die();
		}
		public function update_form(){
			global $wpdb;
			
			$get_fields 	= $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $_POST['table']);
			$fields 	= $wpdb->get_results($get_fields);
			$field_array = array();
			foreach($fields as $field)
				{
				if(isset($_POST[$field->Field]) && $field->Field!='fields' )
					{
					
					$field_array[$field->Field] = $_POST[$field->Field];
					}	
				}
			$update = $wpdb->prepare($wpdb->update ( $wpdb->prefix . $_POST['table'], $field_array, array(	'Id' => $_POST['edit_Id']) ));
		$do_update = $wpdb->query($update);
			die();
		}
		
		public function duplicate_record(){
		global $wpdb;

		$get_record = $wpdb->prepare('SELECT * FROM ' .$wpdb->prefix. $_POST['table']. ' WHERE Id = '.$_POST['Id']);
		$record = $wpdb->get_row($get_record);
		$get_fields 	= $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $_POST['table']);
		$fields 	= $wpdb->get_results($get_fields);
		$field_array = array();
		foreach($fields as $field)
			{
			$column = $field->Field;
			$field_array[$field->Field] = $record->$column;
			}
			
		//remove values not to be copied
		unset($field_array['Id']);	
		//unset($field_array['session_Id']);
		
		//var_dump($field_array);
		$data_insert = $wpdb->prepare($wpdb->insert ( $wpdb->prefix . $_POST['table'], $field_array ));
		$insert = $wpdb->query($data_insert);
		
		//header("Location: http://localhost/db_thermal/wp-admin/admin.php?page=WA-documents-contacts&Id=".mysql_insert_id()."&table=wam_contacts");
		//IZC_Functions::print_message( 'updated' , 'Record Duplicated' );
		//echo mysql_insert_id();
		die();
	}
		
		public function delete_record(){
			global $wpdb;
			IZC_Database::get_descendants($_POST['Id'],$_POST['table']);
			$decendents = explode(',',$_SESSION['decendants']);
			foreach($decendents as $child)
				{
				$delete_decendents = $wpdb->prepare('DELETE FROM ' .$wpdb->prefix. $_POST['table']. ' WHERE Id = '.$child);
				$wpdb->query($delete_decendents);
				}
			$delete = $wpdb->prepare('DELETE FROM ' .$wpdb->prefix. $_POST['table']. ' WHERE Id = '.$_POST['Id']);
			$wpdb->query($delete);
			$_SESSION['decendants'] = '';
			IZC_Functions::print_message( 'updated' , 'Item deleted' );
			die();
		}
	
		public function batch_delete_records($records,$table){
			global $wpdb;
			foreach($records as $record_Id)
				{
				IZC_Database::get_descendants($record_Id,$_POST['table']);
				$decendents = explode(',',$_SESSION['decendants']);
				foreach($decendents as $child)
					{
					$delete_decendents = $wpdb->prepare('DELETE FROM ' .$wpdb->prefix. $table. ' WHERE Id = '.$child);
					$wpdb->query($delete_decendents);
					}
				$do_delete = $wpdb->prepare('DELETE FROM ' .$wpdb->prefix. $table. ' WHERE Id = '.$record_Id);
				$delete = $wpdb->query($do_delete);
				}
			if($delete)
				IZC_Functions::add_js_function( 'print_msg(\'updated\' , \'Items deleted\')' );
		}
		
		public function alter_plugin_table($table='', $col = '', $type='text'){
			global $wpdb;
			
			$get_result = $wpdb->prepare("ALTER TABLE ".$wpdb->prefix . $table ." ADD ".$col." ".$type);
			$result = $wpdb->query($get_result);
			
		}
		
		public function alter_module_table(){
			global $wpdb;
			
			$linked_modules = get_option('iz-linked-modules', array());
			
			if(!is_array($linked_modules))
				$linked_modules = array();
			
			$i = 0;
			if(is_array($this->link_modules))
				{
				foreach($this->link_modules as $link_module=>$val)
					{
					$links[$i] = $link_module;
					$get_result = $wpdb->prepare("ALTER TABLE ".$wpdb->prefix . $link_module ." ADD ".$this->foreign_key." INT(11) unsigned NOT NULL");
					$result = $wpdb->query($get_result);
					$i++;
					IZC_Modules::create_link_purpose($link_module,$val);
					}
				}
	
			$link_array[$this->module_table] = $links; 
			$new_linked_modules = array_merge($linked_modules,$link_array);
			update_option('iz-linked-modules',$new_linked_modules);
			
			
		}
		
		public function share_item(){
			global $wpdb;
			
			$get_is_multi = $wpdb->prepare('SELECT distinct(plugin) FROM ' . $wpdb->prefix . $this->plugin_table);
			$is_multi = $wpdb->get_results($get_is_multi);
			
			if(count($is_multi)>1)
				{
				//<div class="form-field form-required"><label for="Title">Title</label><div class="iz-form-item"><input type="text" value="" name="title"></div></div>
				//$output .= '<div class="form-field ">';
					$output .= '<label >&nbsp;&nbsp;Share Item</label>';
					$output .= '<div class="iz-form-item">';
						$output .= '<input id="shared" type="radio" name="set_plugin" value="shared"><lable for="shared">&nbsp;&nbsp;Yes</label><br />';
						$output .= '<input id="private" type="radio" name="set_plugin" value="'.$this->plugin_alias.'" checked="checked"><lable for="private">&nbsp;&nbsp;No</label>';	
					$output .= '</div>';
				//$output .= '</div>';
				}
			
			return $output;
		}
		
		public function get_plugin_table(){
			global $wpdb;
			$get_fields = $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $this->plugin_table);
			$fields = $wpdb->get_results($get_fields);
			
			foreach($fields as $field)
				{
				$table_fields .= $field->Field.'<br />';
				}
			return $table_fields;
		}
		
		public function get_foreign_fields($key){
			global $wpdb;
			$get_fields = $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $this->plugin_table . " LIKE '%".$key."%'");
			$fields = $wpdb->get_results($get_fields);
			
			$foreign_fields = array();
			
			foreach($fields as $field)
				{
				array_push($foreign_fields,$field->Field);
				}
			return $foreign_fields;
		}
		
		public function get_foreign_Id($Id,$foreign_key,$table){
			global $wpdb;
			$result = $wpdb->prepare("SELECT ".$foreign_key." FROM " . $wpdb->prefix . $table ." WHERE Id = '".$Id."'");
			return $wpdb->get_var($result);
		}
		
		public function get_module_table(){
			global $wpdb;
			$get_fields = $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix . $this->module_table);
			$fields = $wpdb->get_results($get_fields);
			$table_fields = '';
			foreach($fields as $field)
				{
				$table_fields .= $field->Field.'<br />';
				}
			return $table_fields;
		}	
	}
}
?>