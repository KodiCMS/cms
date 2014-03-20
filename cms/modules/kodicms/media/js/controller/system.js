cms.init.add('system_settings', function () {
	$('.widget').on('click', '#clear-cache', function() {
		Api.get('cache.clear', {}, function() {

		});
		return false;
	});
	
	$('.widget').on('click', '#update-search-index', function() {
		Api.get('search.update_index', {}, function() {

		});
		return false;
	});
	
	$('.widget').on('click', '#clear-logs', function() {
		Api.post('log.clear_old', {}, function() {});
		return false;
	});
});

cms.init.add('system_information', function () {
	function calculateEditorHeight() {
		var conentH = cms.calculateContentHeight();
		var h = 130
		
		return conentH - h;
	}
	
	$('#phpinfo').height(calculateEditorHeight());
	$(window).resize(function() {
		$('#phpinfo').height(calculateEditorHeight())
	});
});