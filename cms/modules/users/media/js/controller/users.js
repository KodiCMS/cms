cms.init.add(['users_edit', 'users_add'], function () {
	$(window).on('tabbable', function() {
		$('input[name="user_permission"]').select2({
		placeholder: __("Click to get list of roles"),
		minimumInputLength: 0,
		multiple: true,
		ajax: {
			url: SITE_URL + 'api/user-roles.get',
			data: function(query, pageNumber, context) {
				return {
					key: query,
					fields: 'id,name'
				}
			},
			dataType: 'json',
			results: function (resp, page) {
				var roles = [];
				if(resp.response) {
					for(i in resp.response) {
						roles.push({
							id: resp.response[i]['id'],
							text: resp.response[i]['name']
						});
					}
				}

				return {results: roles};
			}
		},
		initSelection: function(element, callback) {
			element.val('');
			if (USER_ID == 0) {
				callback([{'id':1, 'text':'login'}]);
				return ;
			}
			$.ajax(SITE_URL + 'api/api-users.roles', {
				data: {
					uid: USER_ID,
					fields: 'id,name'
				},
				dataType: 'json',
			}).done(function(resp, page) {
				var roles = [];
				if(resp.response) {
					for(i in resp.response) {
						roles.push({
							id: resp.response[i]['id'],
							text: resp.response[i]['name']
						});
					}
				}

				
				callback(roles);
			});
		}
	});
	});
	
});

cms.init.add('users_profile', function () {
	var toolbar = $('.profile-toolbar');
	var toolbar_l = toolbar.text().replace(/\t/g, '').replace(/\n/g, '').replace(/&nbsp;/g, '').replace(/ /g, '').length;
	
	if(!toolbar_l) toolbar.css({'padding': 0});
})

cms.init.add('users_add', function () {
	$(function() {
		$('.spoiler-toggle').click();
	})
});
