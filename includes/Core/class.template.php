<?php
if(!class_exists('IZC_Template'))
	{
	class IZC_Template{
		
		public $page_header, $page_menu, $page_body, $page_footer;
		
		public $sidebar_header,$sidebar_body, $sidebar_footer;
		
		public $form_fields, $component_table;
		
		public $plugin_alias;
		
		public $data_fields;
		
		public function print_template(){
			echo $this->page_header.$this->page_body.$this->page_footer;
		}
		
		public function print_sidebar_template(){
			echo $this->sidebar_header.$this->sidebar_body.$this->sidebar_footer;
		}
		
		public function build_header($title='',$subtitle='',$menu='',$description='',$plugin_alias=''){

			$this->page_header  .= '<div id="plugin_alias" style="display:none;">';
			$this->page_header  .= $this->plugin_alias;
			$this->page_header  .= '</div>';
			
			$this->page_header  .= '<div id="component_table" style="display:none;">';
			$this->page_header  .= $this->component_table;
			$this->page_header  .= '</div>';
			
			$this->page_header  .= '<div id="edit_Id" style="display:none;">';
			$this->page_header  .= $_REQUEST['Id'];
			$this->page_header  .= '</div>';
			
			$this->page_header  .= '<div id="upload_path" style="display:none;">';
			$this->page_header  .=	ABSPATH.'wp-content/uploads/wa-core/';
			$this->page_header  .= '</div>';
			
			$this->page_header  .= '<div id="modules_uri" style="display:none;">';
			$this->page_header  .=	get_option('siteurl').'/wp-content/modules/';
			$this->page_header  .= '</div>';
			
			$this->page_header  .= '<div id="site_url" style="display:none;">';
			$this->page_header  .=	get_option('siteurl');
			$this->page_header  .= '</div>';
			
			$this->page_header  .= '<div id="upload_uri" style="display:none;">';
			$this->page_header  .=	get_option('siteurl').'/wp-content/uploads/wa-core/';
			$this->page_header  .= '</div>';
			
			$this->page_header  .= '<div class="wrap">';
			$this->page_header  .= '</div>';

			if($subtitle)
				$this->page_header .= '<h2 class="sub_heading">';$this->page_header .= $subtitle;$this->page_header .= '</h2>';
			
			if($description)
				$this->page_header .= '<h5>';$this->page_header .= $description;$this->page_header .= '</h5>';
			
			$this->page_header .= $menu.'<br class="clear" />';
			
			return $this->page_header;
		}
		
		public function build_menu($menu=''){
	
			$this->page_menu  = '<ul class="subsubsub">';
			$this->page_menu .= $menu;
			$this->page_menu .= '</ul>';
			return $this->page_menu;
		}
		
		public function build_body($body=''){
			$this->page_body .= $body;
			return $this->page_body;
		}
		
		public function build_footer($text=''){
			$this->page_footer  = '<div class="footer">';
			$this->page_footer .= $text;
			$this->page_footer .= '</div>';
			$this->page_footer .= '</div>';
			return $this->page_footer;
		}
		
		
		public function build_sidebar_head(){
			$this->sidebar_header .= '<div class="iz-sidebar-head">';
			$this->sidebar_header .= '<div class="iz-top ieHax"></div>';
			$this->sidebar_header .= '</div>';
		}
		
		public function build_sidebar_body($body='',$title=''){
			$this->sidebar_body .= '<div class="iz-mid ieHax">';
			$this->sidebar_body .= '<div class="sidebar-title">';
			$this->sidebar_body .= '<h1>'.$title.'</h1>';
			$this->sidebar_body .= '</div>';
			$this->sidebar_body .= $body;
			$this->sidebar_body .= '</div>';
		}
		
		public function build_sidebar_footer($text=''){
			$this->sidebar_footer .= '<div style="clear:both"> </div>';
			$this->sidebar_footer .= '<div class="iz-bottom ieHax">';
			$this->sidebar_footer .= $text;
			$this->sidebar_footer .= '</div>';
		}
		
		public function add_js($code){
			//Used for simple javascript var declarations
			echo '<script type="text/javascript">'.$code.'</script>';	
		}
	
	}
}
?>