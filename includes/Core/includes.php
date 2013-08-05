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
wp_enqueue_script('wa-jquery-ui-widget',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.widget.min.js');
wp_enqueue_script('wa-jquery-ui-mouse',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.mouse.min.js');
wp_enqueue_script('wa-jquery-ui-resizable',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.resizable.min.js');
wp_enqueue_script('wa-jquery-ui-position',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.position.min.js');
wp_enqueue_script('wa-jquery-ui-sortable',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.sortable.min.js');
wp_enqueue_script('wa-jquery-ui-draggable',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.draggable.min.js');
wp_enqueue_script('wa-jquery-ui-droppable',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.droppable.min.js');
wp_enqueue_script('wa-jquery-ui-accordion',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.accordion.min.js');
wp_enqueue_script('wa-jquery-ui-slider',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.slider.min.js');
wp_enqueue_script('wa-jquery-ui-button',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.button.min.js');
wp_enqueue_script('wa-jquery-ui-tooltip',get_option('siteurl').'/wp-includes/js/jquery/ui/jquery.ui.tooltip.min.js');
wp_enqueue_script('wa-backbone', get_option('siteurl').'/wp-includes/js/backbone.min.js');
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
wp_enqueue_style('jquery_ui_theme',  WP_PLUGIN_URL . '/x-forms-express/includes/Core/css/jquery.ui.theme.css');
wp_enqueue_style('wa-admin-styles', WP_PLUGIN_URL .'/x-forms-express/css/admin.css');
}
?>