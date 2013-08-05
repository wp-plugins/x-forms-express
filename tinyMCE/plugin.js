(function(){
	tinymce.create('tinymce.plugins.xforms', {
		init : function(ed, url){
			ed.addButton('xforms', {
				title	: 'Insert X Form',
				image	: url + '/button.png',
				cmd		: 'popup_window'
			});
			
			ed.addCommand('Add_shortcode', function(){
				ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
				tinyMCE.activeEditor.selection.setContent('[XForms]');
			});
			
			ed.addCommand('popup_window', function(){
				ed.windowManager.open({
					file 		: ajaxurl + '?action=XFormsExpress_tinymce_window',
					width 		: 400,
					height 		: 150,
					inline 		: 1
				}, {
					plugin_url 	: url // Plugin absolute URL
				});
			});
		},
	});
tinymce.PluginManager.add('xforms', tinymce.plugins.xforms);
})();