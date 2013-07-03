cms.init.add('messages_add', function(){
	
	$('input[name="to"]').select2({
		placeholder: __("Type first 2 chars"),
		minimumInputLength: 2,
		ajax: {
			url: '/api-users.like',
			data: function(query, pageNumber, context) {
				return {
					key: query, 
					sin: 'username,email,name', 
					fields:'username'
				}
			},
			dataType: 'json',
			results: function (resp, page) {
				var users = [];
				if(resp.response) {
					for(i in resp.response) {
						users.push({
							id: resp.response[i]['id'],
							text: resp.response[i]['username']
						});
					}
				}

				return {results: users};
			}
		}
	});
});

cms.init.add('messages_index', function(){
	$('.btn-remove').click(function() {
		var $cont = $(this).parent().parent();
		var $message_id = $cont.data('id');
		
		$.post('/api/user-messages.delete', { id: $message_id, uid: USER_ID }, function( resp ) {
			if(resp.response) {
				$cont.remove();
				
				if($('#MessagesList tbody tr').length == 0 )
				{
					window.location = CURRENT_URL;
				}
			}
		}, 'json');
	})
});