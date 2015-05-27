<?php
//JS Dependancies
wp_enqueue_script('jquery');
/***************************************/
/**********  CORE CLASSES  *************/
/***************************************/
include_once( 'class.admin.php');
include_once( 'class.install.php');
include_once( 'class.db.php');
include_once( 'class.admin_menu.php');
include_once( 'class.template.php');
include_once( 'class.functions.php');
include('styles-font-menu/plugin.php');
//include('styles-font-menu/plugin.php');
/***************************************/
/**************  ADMIN  ****************/
/***************************************/
if(is_admin() && ( isset($_GET['page']) && ($_GET['page']=='nex-formslite-main') || $_GET['page']=='nex-formslite-form-builder')){
/***************/
/*** WP Core ***/
/***************/
/* JS */
wp_enqueue_script('wp-admin-response');
wp_enqueue_script('admin-tags');
wp_enqueue_style('widgets');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-mouse');
wp_enqueue_script('jquery-ui-resizable');
wp_enqueue_script('jquery-ui-position');
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_script('jquery-ui-draggable');
wp_enqueue_script('jquery-ui-droppable');
wp_enqueue_script('jquery-ui-accordion');
wp_enqueue_script('jquery-ui-autocomplete');
wp_enqueue_script('jquery-ui-menu');
wp_enqueue_script('jquery-ui-datepicker');
/*wp_enqueue_script('jquery-ui-slider');*/
wp_enqueue_script('jquery-ui-spinner');
wp_enqueue_script('jquery-ui-button');
wp_enqueue_script('jquery-ui-tooltip');
wp_enqueue_script('jquery-hotkeys');
wp_enqueue_script('backbone');
wp_enqueue_script('underscore');
wp_enqueue_script('jquery-form');
//}
/* CSS */
//JQUERY
wp_enqueue_style('jquery-ui');

/***************/
/*** CUSTOM  ***/
/***************/	
/* CSS */
//HOLY GRAIL
wp_enqueue_style('nex-forms-admin-holy-grail.min', plugins_url( '/x-forms-express/css/holy-grail.css'));
//BOOTSTRAP
wp_enqueue_style('nex-forms-bootstrap.min', plugins_url( '/x-forms-express/css/bootstrap.min.css'));
wp_enqueue_style('nex-forms-bootstrap-fields', plugins_url( '/x-forms-express/css/fields.css'));
//nex-forms ADMIN
wp_enqueue_style('nex-forms-admin', plugins_url('/x-forms-express/css/admin.css'));
//nex-forms UI
wp_enqueue_style('nex-forms-ui', plugins_url( '/x-forms-express/css/ui.css'));
//JQUERY UI
wp_enqueue_style('nex-forms-jQuery-UI',plugins_url( '/x-forms-express/css/jquery-ui.min.css'));
wp_enqueue_script('nex-forms-slider.min', plugins_url( '/x-forms-express/js/slider.min.js'));
/* JS */
//BOOTSTRAP
wp_enqueue_script('nex-forms-bootstrap.min',  plugins_url( '/x-forms-express/js/bootstrap.min.js'));

wp_enqueue_script('nex-forms-moment.min', plugins_url( '/x-forms-express/js/moment.min.js'));
wp_enqueue_script('nex-forms-locales.min', plugins_url( '/x-forms-express/js/locales.js'));

wp_enqueue_script('nex-forms-bootstrap-datetimepicker', plugins_url( '/x-forms-express/js/bootstrap-datetimepicker.js'));
wp_enqueue_script('nex-forms-fields', plugins_url( '/x-forms-express/js/fields.js'));
//ISOTOP
wp_enqueue_script('nex-forms-jquery.isotope.min', plugins_url( '/x-forms-express/js/jquery.isotope.min.js'));
//CUSTOM
wp_enqueue_script('nex-forms-ui', plugins_url( '/x-forms-express/js/ui.js'));
wp_enqueue_script('nex-forms-onload', plugins_url( '/x-forms-express/js/nexf-onload.js'));
wp_enqueue_script('nex-forms-form-validation', plugins_url( '/x-forms-express/js/nexf-form-validation.js'));
wp_enqueue_script('nex-forms-drag-and-drop', plugins_url( '/x-forms-express/js/nexf-admin-drag-and-drop.js'));
wp_enqueue_script('nex-forms-form-controls', plugins_url( '/x-forms-express/js/nexf-controls.js'));
//FIELD SETTINGS
wp_enqueue_script('nex-forms-field-settings-main', plugins_url( '/x-forms-express/js/main.js'));
//LOGIC
wp_enqueue_script('nex-forms-field-logic', plugins_url( '/x-forms-express/js/logic.js'));

wp_register_script('core-functions', plugins_url( '/x-forms-express/includes/Core/js/functions.js'));
	wp_enqueue_script('core-functions');
	
	wp_register_script('iz-grid', plugins_url( '/x-forms-express/includes/Core/js/iz-grid.js'));
	wp_enqueue_script ('iz-grid');
	
	wp_register_script('iz_json2',  plugins_url( '/x-forms-express/includes/Core/js/json2.min.js'));
	wp_enqueue_script('iz_json2');
	
	/*wp_register_script('jquery-ui-full',  plugins_url( '/x-forms-express/includes/Core/js/jquery-ui-full.js');
	wp_enqueue_script('jquery-ui-full');*/
	
	
	
	

/***************************************/
/**************  PUBLIC  ***************/
/***************************************/
//JS
wp_register_script('public-functions', plugins_url( '/x-forms-express/includes/Core/js/public.js'));
wp_enqueue_script('public-functions');
	
wp_register_script('core-public-functions', plugins_url( '/x-forms-express/includes/Core/js/public-functions.js'));
wp_enqueue_script('core-public-functions');

//CSS
wp_register_style('defaults', plugins_url( '/x-forms-express/includes/Core/css/defaults.css'));
wp_enqueue_style('defaults');

wp_print_scripts();
wp_print_styles();
}

/***************************************/
/**************  ADMIN  ****************/
/***************************************/
if(is_admin() && ( isset($_GET['page']) && stristr($_GET['page'],'nex-formslite-form-entries') ) ){
	/***************/
	/*** WP Core ***/
	/***************/
	//JS
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-mouse');
	wp_enqueue_script('jquery-ui-resizable');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('admin-widgets');
	wp_enqueue_script('wp-admin-response');
	wp_enqueue_script('admin-tags');
	//CSS
	wp_enqueue_style('widgets');
	wp_enqueue_style('nex-forms-font-awesome',plugins_url( '/x-forms-express/css/font-awesome.min.css'));
	wp_enqueue_style('nex-forms-bootstrap.min', plugins_url( '/x-forms-express/css/bootstrap.min.css'));
	wp_enqueue_script('nex-forms-bootstrap.min',  plugins_url( '/x-forms-express/js/bootstrap.min.js'));
	//***************/
	/*** IZ Core ***/
	/***************/
	//JS
	wp_register_script('module-widgets', plugins_url( '/x-forms-express/includes/Core/js/module-widgets.js'));
	wp_enqueue_script ('module-widgets');
	
	wp_register_script('linked-modules', plugins_url( '/x-forms-express/includes/Core/js/linked-modules.js'));
	wp_enqueue_script ('linked-modules');

	//Depenedancies for creating and managing custom events
	//wp_register_script('underscore');
	wp_enqueue_script('underscore');
	
	//wp_register_script('backbone');
	wp_enqueue_script('backbone');
	//Generic functions
	
	wp_register_script('core-functions', plugins_url( '/x-forms-express/includes/Core/js/functions.js'));
	wp_enqueue_script('core-functions');
	
	wp_register_script('iz-grid', plugins_url( '/x-forms-express/includes/Core/js/iz-grid.js'));
	wp_enqueue_script ('iz-grid');
	
	wp_register_script('iz_json2',  plugins_url( '/x-forms-express/includes/Core/js/json2.min.js'));
	wp_enqueue_script('iz_json2');
	
	wp_register_script('jquery-ui-full',  plugins_url( '/x-forms-express/includes/Core/js/jquery-ui-full.js'));
	wp_enqueue_script('jquery-ui-full');
	
	
	
	

/***************************************/
/**************  PUBLIC  ***************/
/***************************************/
//JS
wp_register_script('public-functions', plugins_url( '/x-forms-express/includes/Core/js/public.js'));
wp_enqueue_script('public-functions');
	
wp_register_script('core-public-functions', plugins_url( '/x-forms-express/includes/Core/js/public-functions.js'));
wp_enqueue_script('core-public-functions');

//CSS
wp_register_style('defaults', plugins_url( '/x-forms-express/includes/Core/css/defaults.css'));
wp_enqueue_style('defaults');


wp_print_scripts();
wp_print_styles();
}
?>