cms.init.add('page_edit', function() {
	$('.spoiler-page-fields').on('click', '.btn-add', function() {
		var container = $(this).parent().parent().parent();
		var fields = container.find('input');
		Api.put('page-field', {field: fields.serializeObject(), page_id: PAGE_ID}, function(response) {
			if(response.response)
			{
				$('.spoiler-page-fields').append(response.response);
				cms.ui.init('filemanager');
				fields.val('');
			}
		});

		return false;
	});
	
	$('.spoiler-page-fields').on('click', '.btn-remove', function() {
		var field = $(this).parent().parent();
		Api.delete('page-field', {field_id: field.data('id')}, function(response) {
			field.remove();
		});
		return false;
	});
	
	$('.page-field').on('change', 'input[name=value]', function() {
		if($(this).parent().data('id'))
			updateField($(this));
	});
	
	function updateField(field) {
		Api.post('page-field', {field_id: field.parent().data('id'), value: field.val()}, function(response) {});
	}
	
	$('#select-page-field').on('click', function() {
		var container = $(this).parent();
		
		if(container.hasClass('select-init')) {
			
			$(this).text(__('Show field select'));
			
			container
				.removeClass('select-init')
				.find('.system-field')
				.show()
				.find('input')
				.removeAttr('disabled');

			$('.select-field-container')
				.hide();
				
			$("#select-page-field-container")
				.select2("destroy")
				.attr('disabled', 'disabled');

		}else{
			$("#select-page-field-container")
				.removeAttr('disabled')
				.select2({
					placeholder: __("Select field"),
					minimumInputLength: 0,
					ajax: {
						url: SITE_URL + 'api/page-field.all',
						dataType: 'json',
						results: function (data, page) {
							return {results: data.response};
						}
					}
				});
			
			$(this).text(__('Hide field select'));
			
			container
				.addClass('select-init')
				.find('.system-field')
				.hide()
				.find('input')
				.attr('disabled', 'disabled');
				
			$('.select-field-container')
				.show();
		}
		
		
		
		return false;
	});
});
