cms.init.add(['page_edit'], function () {
	var loadBehaviorData = function(behaviorId) {
		Api.get('behavior.settings', {id: behaviorId, page_id: PAGE_ID}, function(resp) {
			$('#behavor_options').html(resp.response);
		});
	};	

	var behaviorId = $('select[name="page[behavior_id]"]').change(function() {
		var id = $('option:selected', this).val();

		loadBehaviorData(id);
	}).find('option:selected').val();

	loadBehaviorData(behaviorId);
});