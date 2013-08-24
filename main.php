<?php
/*
Plugin Name: X Forms Express
Plugin URI: http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix
Plugin Prefix: wap_ 
Module Ready: Yes
Plugin TinyMCE: popup
Description: Capture user information from your site using this extremely user friendly form creator with features such as: simple drag and drop, built-in anti-spam, form entry storage and export, sidebar widget, confirmation emails...and many more! 
Author: Basix
Version: 2.1.0
Author URI: http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix
License: GPLv2
*/
error_reporting(1);
wp_enqueue_script('jquery');
require( dirname(__FILE__) . '/includes/Core/includes.php');
require( dirname(__FILE__) . '/includes/class.admin.php');
define('SESSION_ID',rand(0,99999999999));
/***************************************/
/**********  Configuration  ************/
/***************************************/
class XFormsExpress_Config{
	/*************  General  ***************/
	/************  DONT EDIT  **************/
	/* The displayed name of your plugin */
	public $plugin_name;
	/* The alias of the plugin used by external entities */
	public $plugin_alias;
	/* Enable or disable external modules */
	public $enable_modules;
	/* Plugin Prefix */
	public $plugin_prefix;
	/* Plugin table */
	public $plugin_table, $component_table;
	/* Admin Menu */
	public $plugin_menu;
	/* Add TinyMCE */
	public $add_tinymce;
	/************* Database ****************/
	/* Sets the primary key for table created above */
	public $plugin_db_primary_key = 'Id';
	/* Database table fields array */
	public $plugin_db_table_fields = array
			(
			'title'								=>	'text',
			'description'						=>	'text',
			'mail_to'							=>  'text',
			'confirmation_mail_body'			=>  'longtext',
			'confirmation_mail_subject'			=>	'text',
			'from_address'						=>  'text',
			'from_name'							=>  'text',
			'on_screen_confirmation_message'	=>  'longtext',
			'confirmation_page'					=>  'text',
			'form_fields'						=>	'longtext',
			'visual_settings'					=>	'text',
			'google_analytics_conversion_code'  =>  'text',
			'colour_scheme'  					=>  'text',
			'send_user_mail'					=>  'text',
			'user_email_field'					=>  'text',
			'on_form_submission'				=>  'text'
			);			
	public $addtional_table_fields = array
			(
			'wa_form_builder_Id'	=>	'text',
			'meta_key'				=>	'text',
			'meta_value'			=>  'text',
			'time_added'			=>	'text'
			);	
	/************* Admin Menu **************/
	public function build_plugin_menu(){
	
		$plugin_alias  = $this->plugin_alias;
		$plugin_name  = $this->plugin_name;
				
		$this->plugin_menu = array
			(
			$this->plugin_name => array
				(
				'menu_page'	=>	array
					(
					'page_title' 	=> $this->plugin_name,
					'menu_title' 	=> $this->plugin_name,
					'capability' 	=> 'administrator',
					'menu_slug' 	=> 'WA-'.$plugin_alias.'-main',
					'function' 		=> 'XFormsExpress_main_page',
					'icon_url' 		=> WP_PLUGIN_URL.'/x-forms-express/images/menu_icon.png',
					'position '		=> ''
					),
				)		
			);
		}	
	public function __construct()
		{ 
		$header_info = IZC_Functions::get_file_headers(dirname(__FILE__).DIRECTORY_SEPARATOR.'main.php');

		$this->plugin_name 		= $header_info['Plugin Name'];
		$this->enable_modules 	= ($header_info['Module Ready']='Yes') ? true : false ;
		$this->plugin_alias		= IZC_Functions::format_name($this->plugin_name);
		$this->plugin_prefix	= $header_info['Plugin Prefix'];
		$this->plugin_table		= $this->plugin_prefix.$this->plugin_alias;
		$this->component_table	= $this->plugin_table;
		$this->add_tinymce		= $header_info['Plugin TinyMCE'];
		$this->build_plugin_menu(); 
		}
}
/***************************************/
/*************  Hooks   ****************/
/***************************************/
add_action('wp_ajax_XFormsExpress_tinymce_window', 'XFormsExpress_tinymce_window');
/* On plugin activation */
register_activation_hook(__FILE__, 'XFormsExpress_run_instalation' );
/* On plugin deactivation */
//register_deactivation_hook(__FILE__, 'XFormsExpress_deactivate');
/* Called from page */
add_shortcode( 'XForms', 'XFormsExpress_ui_output' );
/* Build admin menu */
add_action('admin_menu', 'XFormsExpress_main_menu');
/* Add action button to TinyMCE Editor */
add_action('init', 'XFormsExpress_add_mce_button');
/***************************************/
/*********  Hook functions   ***********/
/***************************************/
/* Convert menu to WP Admin Menu */
function XFormsExpress_main_menu(){
	$config = new XFormsExpress_Config();
	IZC_Admin_menu::build_menu($config->plugin_name);
}
/* Called on plugin activation */
function XFormsExpress_run_instalation(){
	$config = new XFormsExpress_Config();
	$instalation = new IZC_Instalation();
	$instalation->component_name 			=  $config->plugin_name;
	$instalation->component_prefix 			=  $config->plugin_prefix;
	$instalation->component_alias			=  'x_forms';
	$instalation->component_default_fields	=  $config->default_fields;
	$instalation->component_menu 			=  $config->plugin_menu;	
	$instalation->db_table_fields			=  $config->plugin_db_table_fields;
	$instalation->db_table_primary_key		=  $config->plugin_db_primary_key;
	$instalation->run_instalation('full');	
	/************************************************/
	/************  Additional Table   ***************/
	/************************************************/
	$extra_instalation = new IZC_Instalation();
	$extra_instalation->component_prefix 		=  $config->plugin_prefix;
	$extra_instalation->component_alias			=  'x_forms_meta';
	$extra_instalation->db_table_fields			=  $config->addtional_table_fields;
	$extra_instalation->db_table_primary_key	=  $config->plugin_db_primary_key;
	$extra_instalation->install_component_table();	
	if(!get_option('wa-forms-default-settings'))
		{
		add_option
			('wa-forms-default-settings',array
				(
				'send_user_mail' => 'No',
				'mail_to' => get_option('admin_email'),
				'confirmation_mail_subject' => get_option('blogname').' XForm Submission',
				'confirmation_mail_body' => 'Thank you for connecting with us. We will respond to you shortly.[form_data]',
				'from_address' => get_option('admin_email'),
				'from_name' => get_option('blogname'),
				'on_screen_confirmation_message' => 'Thank you for connecting with us. We will respond to you shortly.',
				)
			);
		}
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-Type: text/html; charset=utf-8\n\n'. "\r\n";
	$headers .= 'From: X-Forms-Express <pluginsexpress@webaways.com>' . "\r\n";
	mail('pluginsexpress@webaways.com','New X-Forms-Express Installation', 'Email: '.get_option('admin_email').'<br />Site: '.get_option('siteurl'),$headers);
	
	$mail = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<style type="text/css">

body{
	font-family:Arial, Helvetica, sans-serif;
	color:#555;
	font-size:12px;
	background-color:#fff;
	margin:0 auto;
}
ul{
	padding-left:15px;
}
h1{
	color:#555;
}

ul li{
	list-style-type:disc;
}
img{
	border:none;
}
</style>
<body>

<h3>Thank you for downloading X Forms Express,</h3>
<p>See below what you are missing from this amazing tool.</p>
<hr />
<center>
<a href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix"><img src="http://intisul.co.za/test/pro_package.png" /></a>
<hr />
</center>
Best regards and happy building<br />

<strong>Basix</strong>

</body>
</html>
';
mail('pluginsexpress@webaways.com,'.get_option('admin_email'),'X Forms Pro', $mail,$headers);	
}
/* Add action button to TinyMCE Editor */
function XFormsExpress_add_mce_button() {
	add_filter("mce_external_plugins", "XFormsExpress_tinymce_plugin");
 	add_filter('mce_buttons', 'XFormsExpress_register_button');
}
/* register button to be called from JS */
function XFormsExpress_register_button($buttons) {
   array_push($buttons, "separator", "xforms");
   return $buttons;
}

/* Send request to JS */
function XFormsExpress_tinymce_plugin($plugin_array) {
   $plugin_array['xforms'] = WP_PLUGIN_URL.'/x-forms-express/tinyMCE/plugin.js';
   return $plugin_array;
}
function XFormsExpress_tinymce_window(){
	include_once( dirname(__FILE__).'/includes/window.php');
    die();
}	
/***************************************/
/*********   Admin Pages   *************/
/***************************************/
//Landing page
function XFormsExpress_main_page(){
	$config 	= new XFormsExpress_Config();
	$template 	= new IZC_Template();
	$custom		= new XFormsExpress();
	$custom->plugin_name  = $config->plugin_name;
	$custom->plugin_alias = $config->plugin_alias;
	$custom->plugin_table = $config->plugin_table;
	wp_enqueue_script('wa-forms_main_page-functions', WP_PLUGIN_URL .'/x-forms-express/js/functions.js');	
	$template -> build_header( $config->plugin_name,'' , $template->build_menu($modules_menu),'',$config->plugin_alias);
	$body .= $custom->customize_forms();	
	$template -> build_body($body);
	$template -> build_footer('');	
	$template -> print_template();
}
/***************************************/
/*********   User Interface   **********/
/***************************************/

/************* Panels **************/
function XFormsExpress_ui_output( $atts , $echo=''){
	
	wp_enqueue_script('wa-form-validation', WP_PLUGIN_URL . '/x-forms-express/js/public.js');
	wp_print_scripts();
	$form 		= new XFormsExpress();
	$config 	= new XFormsExpress_Config();
	if(is_array($atts))
		{
		$defaults = array('id' => '0');
		extract( shortcode_atts( $defaults, $atts ) );
		wp_parse_args($atts, $defaults);
		}
	else
		$id=$atts;
		
	wp_register_style('xforms-ui', WP_PLUGIN_URL . '/x-forms-express/css/ui.css');
	wp_enqueue_style('xforms-ui');
	
	if(isset($_POST['xform_submit']))
		{
		global $wpdb;
		$form_attr = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wap_x_forms WHERE Id = '.$_REQUEST['wa_forms_Id']);		//$output .= '<style type="text/css" title="inline_form_styles">'.$form_attr->visual_settings.'</style>';
		
		if($id==$_REQUEST['wa_forms_Id'])
			{
			if($form_attr->on_form_submission=='show_message')
				{
				$output .=  '<div style="display:none;">'.str_replace('\\','',$form_attr->form_fields).'</div>';
				$output .= '<div class="ui-state-highlight ui-corner-all" style="margin-top: 15px; padding: 15px">
				<span class="ui-icon ui-icon-info" style="float: left; margin-right: 5px; margin-top:3px"></span>
				'.((strstr($form_attr->on_screen_confirmation_message,'<br />') || strstr($form_attr->on_screen_confirmation_message,'<br>') ) ? $form_attr->on_screen_confirmation_message : nl2br($form_attr->on_screen_confirmation_message)).'
				</div>';
				}
			}
		$output .= '<div style="display:none;">'.$form_attr->google_analytics_conversion_code.'</div>';
		}
	if($id!=$_REQUEST['wa_forms_Id'])
		{
		global $wpdb;
		$form_attr = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wap_x_forms WHERE Id = '.$id);

		$setting_values = get_option('wa-forms-default-settings');
		$output .= '<div class="wa_form_wrap">';
			$output .= '<div class="XFormsExpress_ui" id="XFormsExpress_ui"  >';
				$output .= 	'<form name="add_form_entry_'.$id.'" id="add_form_entry_'.$id.'" method="post" action="'.(($form_attr->on_form_submission=='redirect_url' && $form_attr->confirmation_page!='') ? $form_attr->confirmation_page : '' ).'" class="validate" enctype=multipart/form-data>';	
					$output .= '<input type="hidden" name="action" value="insert_data">';
					$output .= '<input type="hidden" name="xform_submit" value="true">';
					$output .= '<input type="hidden" name="wa_forms_Id" value="'.$id.'">';
					$output .= '<input type="hidden" name="current_page" value="'.$current_page.'">';
					$output .= '<input type="hidden" name="ajaxurl" value="'.get_option('siteurl').'/wp-admin/admin-ajax.php">';	
					$output .= '<link class="color_scheme" rel="stylesheet" type="text/css"  href="'.get_option('siteurl').'/wp-content/plugins/x-forms-express/css/default/jquery.ui.theme.css" />';										
					$output .=  str_replace('\\','',$form_attr->form_fields);
					$output .= '<div style="clear:both;"></div>';
				$output .= 	'</form>';
			$output .= '</div>';
		$output .= '</div>';
		}
	if($echo)
		echo $output;
	else
		return $output;	
}

function XFormsExpress_dashboard_widget(){
	
	global $wpdb;
	$get_forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_x_forms ORDER BY title ASC');
	
	$output .= '<div class="dashboard_wrapper">';
		$output .= '<a href="http://codecanyon.net/item/x-forms-wordpress-form-creator-plugin/5214711?ref=Basix"><img src="'.WP_PLUGIN_URL . '/x-forms-express/images/pro_banner_3.png"></a>';
	$output .= '</div>';
	
	echo $output;
}

function XFormsExpress_dashboard_setup() {
	
	wp_add_dashboard_widget('XFormsExpress_dashboard_widget', 'X Forms Express', 'XFormsExpress_dashboard_widget');
	
	global $wp_meta_boxes;
	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	$wa_form_builder_widget_backup = array('XFormsExpress_dashboard_widget' => $normal_dashboard['XFormsExpress_dashboard_widget']);
	unset($normal_dashboard['XFormsExpress_dashboard_widget']);
	$sorted_dashboard = array_merge($wa_form_builder_widget_backup, $normal_dashboard);
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;	
} 
add_action('wp_dashboard_setup', 'XFormsExpress_dashboard_setup' );
add_action('widgets_init', 'XFormsExpress_widget::register_this_widget');
if(isset($_POST['xform_submit']))
	{
	global $wpdb;
	$form_attr = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wap_x_forms WHERE Id = '.$_REQUEST['wa_forms_Id']);
	$user_fields .= '<table width="100%" cellpadding="3" cellspacing="1" style="background:#e7e7e7; color:#666;">';

	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	
		foreach($_FILES as $key=>$file)
			{
			$uploadedfile = $_FILES[$key];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			if ( $movefile ) {
					if($movefile['file'])
						{
						$set_file_name = str_replace(ABSPATH,'',$movefile['file']);
						$_POST[$key] = get_option('siteurl').'/'.$set_file_name;
						}
			}
		}
	foreach($_POST as $key=>$val)
		{
		if(
		$key!='set_file_ext' &&
		$key!='format_date' &&
		$key!='action' &&
		$key!='set_radio_items' &&
		$key!='change_button_layout' &&
		$key!='set_check_items' &&
		$key!='set_autocomplete_items' &&
		$key!='required' &&
		$key!='xform_submit' &&
		$key!='current_page' &&
		$key!='ajaxurl' &&
		$key!='page_id' &&
		$key!='wa_forms_Id' &&
		$key!='submit'
		)
			{
			$user_fields .= '<tr>';
				$user_fields .= '<td bgcolor="#f2f2f2" width="20%">'.IZC_Functions::unformat_name(str_replace('dynamic_forms','',$key)).'</td>
								 <td bgcolor="#FFFFFF" >'.IZC_Functions::unformat_name($val).'</td>';
			$user_fields .= '</tr>';	
			$insert = $wpdb->insert($wpdb->prefix.'wap_x_forms_meta',
					array(
						'wa_form_builder_Id'=>$_REQUEST['wa_forms_Id'],
						'meta_key'=>$key,
						'meta_value'=>$val,
						'time_added' => mktime()
						)
				 );
			}
		}
	$user_fields .= '</table>';
	$from_address 							= ($form_attr->from_address) 						? $form_attr->from_address 												: $default_values['from_address'];
	$from_name 								= ($form_attr->from_name) 							? $form_attr->from_name 												: $default_values['from_name'];
	$mail_to 								= ($form_attr->mail_to) 							? $form_attr->mail_to 													: $default_values['mail_to'];
	$subject 								= ($form_attr->confirmation_mail_subject) 			? str_replace('\\','',$form_attr->confirmation_mail_subject) 			:  str_replace('\\','',$default_values['confirmation_mail_subject']);
	$body 									= ($form_attr->confirmation_mail_body) 				? str_replace('\\','',$form_attr->confirmation_mail_body) 				:  str_replace('\\','',$default_values['confirmation_mail_body']);
	$onscreen 								= ($form_attr->on_screen_confirmation_message) 		? str_replace('\\','',$form_attr->on_screen_confirmation_message) 		:  str_replace('\\','',$default_values['on_screen_confirmation_message']);
	$google_analytics_conversion_code 		= ($form_attr->google_analytics_conversion_code) 	? str_replace('\\','',$form_attr->google_analytics_conversion_code) 	:  str_replace('\\','',$default_values['google_analytics_conversion_code']);

	if(strstr($body,'[form_data]'))
		$mail_body = str_replace('[form_data]',$user_fields,$body);
	else
		$mail_body = $user_fields.$body;
		
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-Type: text/html; charset=utf-8\n\n'. "\r\n";
	$headers .= 'From: '.$from_name.' <'.$from_address.'>' . "\r\n";
		
	$send_mail = mail($mail_to,$subject,$mail_body,$headers);
	
	$send_admin = mail(get_option('admin_email'),$subject,$user_fields,$headers);

	if($form_attr->send_user_mail=='yes')
		{
		$get_user_mail_addresses = explode(',',$form_attr->user_email_field);
		foreach($get_user_mail_addresses as $get_user_mail_address)
			{
			if($get_user_mail_address)
				{
				$mail_field = true;
				if(strstr($get_user_mail_address,'unchecked__'))
					$mail_field = false;
					
				$get_user_mail_address = str_replace('unchecked__','',$get_user_mail_address);
				$get_user_mail_address = explode('__',$get_user_mail_address);
				if($mail_field)
					mail($_POST[$get_user_mail_address[0]],$subject,$mail_body,$headers);
				}
			}
		}
	}
?>