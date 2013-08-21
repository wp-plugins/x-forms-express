<?php
//JS Dependancies
wp_enqueue_script('jquery');
wp_enqueue_style('jquery-ui');
/***************************************/
/**********  CORE CLASSES  *************/
/***************************************/
include_once( 'class.install.php');
include_once( 'class.db.php');
include_once( 'class.admin_menu.php');
include_once( 'class.template.php');
include_once( 'class.functions.php');
/***************************************/
/****************  JS  *****************/
/***************************************/
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('wa-jquery-ui-core',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.core.min.js');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-mouse');
wp_enqueue_script('jquery-ui-resizable');
wp_enqueue_script('jquery-ui-position');
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_script('jquery-ui-draggable');
wp_enqueue_script('jquery-ui-droppable');
wp_enqueue_script('jquery-ui-accordion');
wp_enqueue_script('jquery-ui-slider');
wp_enqueue_script('jquery-ui-button');
wp_enqueue_script('jquery-ui-tooltip');
wp_enqueue_script('backbone');
wp_enqueue_script('underscore');
wp_enqueue_style('XForms-ADMIN-Dashboard', WP_PLUGIN_URL . '/x-forms-express/css/dashboard.css');
wp_enqueue_style('wa-admin-styles', WP_PLUGIN_URL .'/x-forms-express/css/admin.css');	
wp_enqueue_style('XForms-UI', WP_PLUGIN_URL . '/x-forms-express/css/ui.css');
wp_enqueue_style('defaults', WP_PLUGIN_URL . '/x-forms-express/includes/Core/css/defaults.css');
wp_enqueue_style('ui-lightness',  WP_PLUGIN_URL . '/x-forms-express/includes/Core/css/themes/ui-lightness.css');	
if(is_admin() && ( isset($_GET['page']) && stristr($_GET['page'],'wa'))){
wp_enqueue_script('wa-visual-settings', WP_PLUGIN_URL . '/x-forms-express/js/visual_settings_admin.js');
wp_enqueue_script('wa-colour-picker', WP_PLUGIN_URL . '/x-forms-express/js/colorpicker.js');
wp_enqueue_style('wa-colour-picker', WP_PLUGIN_URL . '/x-forms-express/css/colorpicker.css');
wp_enqueue_script('core-functions',WP_PLUGIN_URL . '/x-forms-express/includes/Core/js/functions.js');
//wp_enqueue_style('jquery_ui_theme',  WP_PLUGIN_URL . '/x-forms-express/includes/Core/css/jquery.ui.theme.css');
wp_enqueue_style('wa-admin-styles', WP_PLUGIN_URL .'/x-forms-express/css/admin.css');
wp_print_scripts();
}
?>