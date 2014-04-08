// Auto generated i18n lang file for lang ru-ru
// cms.addTranslation({
//	'Finish': 'Установить',
//	'Next': 'Далее',
//	'Previous': 'Назад',
//	'Loading ...': 'Загрузка ...'
//});

$(function() {
	$('#wizard').on('change', '#current-lang', function() {
	    // TODO: May need to add = SITE_URL + ...
		window.location = 'install/index?lang=' + $(this).val();
	})
	
	var password_generator_status = function() {
		var checkbox = $('#generate-password-checkbox');

		if(checkbox.is(':checked')){
			$('#password-form').hide();
		} else {
			$('#password-form').show();
		}
	}
	
	$('#wizard').on('change', '#generate-password-checkbox', password_generator_status);
	password_generator_status();
	
	function show_error($error) {
		$('#wizard .wizard-alert').remove();

		$('#wizard .body.current .widget-content')
			.append('<p class="wizard-alert alert alert-error">'+$error+'</p>');
	}
	
	$("#wizard").steps({
		labels: {
			current: "current step:",
			pagination: "Pagination",
			finish: __("Finish"),
			next: __("Next"),
			previous: __("Previous"),
			loading: __("Loading ...")
		},
		onStepChanging: function (event, currentIndex, newIndex) {
			if(currentIndex == 0 && newIndex == 1 && failed) {
				return false;
			}
	
			return true;
		},
		onFinishing: function (event, currentIndex) {
			return $("form").submit();
		}
	});
	
	function check_connect(wizard) {
		var $return = false;

		var $fields = $(':input[name*=db_]').serialize();
		$.post('check_connect', $fields, function(resp) {
			if(resp.status) {
				$(wizard).steps('next');
				$return = true;
			} else {
				cms.clear_error();
				parse_messages(resp.message, 'error');
			}
		}, 'json');
		
		return $return;
	}
	
	$('.select2-container').remove();
	cms.ui.init('select2');
})

