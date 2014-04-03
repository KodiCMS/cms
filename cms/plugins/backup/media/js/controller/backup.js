cms.init.add('backup_view', function() {
	function calculateEditorHeight() {
		var conentH = cms.content_height;
		var h = $('.widget-title').outerHeight(true) + $('.widget-header').outerHeight(true) + $('.form-actions').outerHeight(true) + 10;

		return conentH - h;
	}

	$('#highlight_content').on('filter:switch:on', function(e, editor) {
		cms.filters.exec('highlight_content', 'changeHeight', calculateEditorHeight);
	});

	$(window).resize(function() {
		$('#highlight_content').trigger('filter:switch:on');
	});
});

cms.init.add('backup_index', function() {
	$(function() {
		cms.uploader.options.acceptedFiles = '.zip,.sql';
		cms.uploader.on('success', function(file, response) {
            var self = this;
			response = $.parseJSON(response);
			if(response.code == 200)
				Api.get('backup.list', {}, function(response) {
                    $('#backups-list').html(response.response);
                    
                    self.removeFile(file);
                });
		});
        
        cms.uploader.on('error', function(file, response) {
            
        });
	});
});