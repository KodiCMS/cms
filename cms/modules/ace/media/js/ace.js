/*
	Ace object
*/
cms.plugins.ace = {};

cms.plugins.ace.switchOn_handler = function( textarea_id, params )
{
	var textarea = $('#' + textarea_id).hide();
	var height = textarea.data('height') ? textarea.data('height') : 300;
	var mode = textarea.data('mode') ? textarea.data('mode') : 'php';
	var editArea = $('<div id=' + textarea_id + 'Div />')
		.insertAfter(textarea)
		.css({
			height: height,
			fontSize: 14
		});
	
	var editor = ace.edit(textarea_id + 'Div');
	editor.setValue(textarea.val());

	editor.clearSelection();
    editor.getSession().setMode("ace/mode/" + mode);
	editor.getSession().setTabSize(4);
	editor.getSession().setUseSoftTabs(false);
	editor.getSession().setUseWrapMode(true);
	editor.getSession().on('change', function(){
		textarea.val(editor.getSession().getValue());
	});
	editor.setTheme("ace/theme/textmate");
	
	function fullscreen(editArea, editor, height) {
		if(!editArea.data('fullscreen') || editArea.data('fullscreen') == 'off') {
			editArea
				.css({
					position: 'fixed',
					width: '100%', 
					height: '100%',
					top: 0, left: 0
				})
				.data('fullscreen', 'on')
		
			editor.setTheme("ace/theme/monokai");
		} else {
			editor.setTheme("ace/theme/textmate");
			editArea
				.data('fullscreen', 'off')
				.css({
					position: 'relative',
					width: 'auto', 
					height: height,
					top: 'auto', left: 'auto'
				});
		}
	}
	
	if(textarea.data('readonly') == 'on') {
//		editor.setTheme("ace/theme/monokai");
		editor.setReadOnly(true);
	} else {
		editor.commands.addCommand({
//			name: 'Save',
			bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
			exec: function(editor) {
				$('button[name="continue"]').click();
			}
		});
		
		editor.commands.addCommand({
			name: 'Full-screen',
			bindKey: {win: 'Ctrl-F',  mac: 'Command-F'},
			exec: function(editor) {
				fullscreen(editArea, editor, height)
			}
		});		
	}
	
	return editor;
};

// Switch off tinymce handler
cms.plugins.ace.switchOff_handler = function( editor, textarea_id )
{
	$('#' + textarea_id + 'Div').remove();
}

cms.plugins.ace.exec_handler = function( editor, command, textarea_id, data )
{
	switch(command) {
		case 'insert':
			editor.insert(data);
			break;
		case 'changeHeight':
			$('#' + textarea_id + 'Div')
				.css({
					height: data
				});
				
			editor.resize();
	}
	
	return true;
}

/*
	When DOM init
*/
jQuery(function(){
	cms.filters
		.add( 'ace', cms.plugins.ace.switchOn_handler, cms.plugins.ace.switchOff_handler, cms.plugins.ace.exec_handler );

	cms.filters.switchOn( 'highlight_content', 'ace' );
});

cms.init.add(['layout_edit', 'snippet_edit', 'layout_add', 'snippet_add'], function(){
	jQuery(function(){
		cms.filters.switchOn( 'textarea_content', 'ace', $('#textarea_content').data());
	});
})