/*
	Redactor object
*/
cms.plugins.redactor = {};


// Switch on tinymce handler
cms.plugins.redactor.switchOn_handler = function( textarea_id, params )
{
	var local_params = {
		focus: true,
		//wym: true,
		autoresize: false,
		lang: LOCALE,
		minHeight: 200,
		buttonsAdd: ['|', 'elfinder'], 
		buttonsCustom: {
			elfinder: {
				title: 'elFinder', 
				callback: function(obj, event, key) {
					cms.filemanager.open(obj.$el.attr('id'));
				} 
			}
		}
	};
	
	params = $.extend(local_params, params);

	return $('#' + textarea_id).redactor(params);
};

// Switch off tinymce handler
cms.plugins.redactor.switchOff_handler = function( editor, textarea_id )
{
	editor.destroyEditor();	
};

cms.plugins.redactor.exec_handler = function( editor, command, textarea_id, data )
{
	switch(command) {
		case 'insert':
			if (/(jpg|gif|png|JPG|GIF|PNG|JPEG|jpeg)$/.test(data)){
				data = '<img src="' + data + '">';
			} else {
				data = '<a href="' + data + '">' + data + '</a>';
			}

			editor.insertHtml(data);
			break;
	}
	
	
	return true;
};

/*
	When DOM init
*/
jQuery(function(){

	cms.filters
		.add( 'redactor', cms.plugins.redactor.switchOn_handler, cms.plugins.redactor.switchOff_handler, cms.plugins.redactor.exec_handler );
});