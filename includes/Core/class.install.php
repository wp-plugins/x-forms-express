<?php
if(!class_exists('IZC_Instalation'))
	{
	class IZC_Instalation{
		
		public 
		$role,
		$component_name,
		$component_prefix,
		$component_alias,
		$component_default_fields,
		$component_menu,
		$db_table_fields, 
		$db_table_primary_key,
		$error_msg;
	
		public function __construct(){}
		
		public function run_instalation($type){	
			
			if($type=='full')
				{	
				$dummy_array = array();
	
				//Arrays to be used by CORE
				add_option( 'iz-default-fields'			, 	array( $this->component_alias=>$this->component_default_fields));
				add_option( 'iz-modules-base'			, 	array( $this->component_alias,'not installed') );
				add_option( 'iz-firstrun'				,	array( $this->component_alias,true) );
				add_option( 'iz-filters' 				, 	array( $this->component_alias=>array('') ) );
				add_option( 'iz-active-modules' 		, 	array( $this->component_alias=>array('') ) );			
				add_option( 'iz-linked-modules' 		, 	array( $this->component_alias=>array('') ) );
				add_option( 'iz-module-widgets' 		, 	array( $this->component_alias=>array('') ) );
				add_option( 'iz-pluggables' 			, 	array( '' ) );
				add_option( 'iz-menus' 					, 	array( '' ) );
				add_option( 'iz-ui-positions'			, 	array( '' ) );
				
				$panels 	= get_option('iz-ui-positions', array() );
				$menus 		= get_option('iz-menus' , array());
				$pluggables = get_option('iz-pluggables',$dummy_array);
				
				if(!is_array($panels))
					$panels = array();
				//Panels
				//$new_component_panels = ;
				$set_panels = array_merge($panels , array( $this->component_alias=>array('')  ) );
				update_option('iz-ui-positions',$set_panels);		
				
				//Admin Menu
				$admin_menu = array_merge($menus,$this->component_menu);
				update_option('iz-menus',$admin_menu);
				
				//Plugables
				if(!in_array($this->component_alias,$pluggables))
					{
					array_push($pluggables,$this->component_alias);
					}
				update_option('iz-pluggables',$pluggables);
				
				$this->install_component_table();
				//$this->install_modules_directory();
				}
			if($type=='db')
				{	
				$this->install_component_table();
				}
			if($type=='module_base')
				{	
				add_option( 'iz-modules-base',array($this->component_name,'not installed'));
				//$this->install_modules_directory();
				}
			
		}
		
		public function install_modules_directory(){
			
			
		}
		
		public function install_component_table(){
	
			global $wpdb;
			
			$table_name = $wpdb->prefix . $this->component_prefix .$this->component_alias;
			
			$default_fields = array(
				'Id'				=>	'BIGINT(255) unsigned NOT NULL AUTO_INCREMENT',
				'plugin'			=>  'VARCHAR(255)',
				'publish'			=>	'int(1) unsigned DEFAULT 0',
				'added'				=>	'VARCHAR(18)  DEFAULT \'0000-00-00 00:00\'',
				'last_update'		=>	'TIMESTAMP'
				);
			
			$all_fields = array_merge($default_fields,$this->db_table_fields);
			
			if($wpdb->get_var("show tables like '".$table_name."'") != $table_name){
				
				$sql = 'CREATE TABLE `'. $table_name .'` (';	
	
					foreach($all_fields as $key => $val){
						$sql .= '`'.$key.'` '.$val.',';
					}
				$sql .= 'PRIMARY KEY (`'. $this->db_table_primary_key .'`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
				dbDelta($sql);
			}
		}
	}
}
?>