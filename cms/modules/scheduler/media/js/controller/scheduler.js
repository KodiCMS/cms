cms.init.add(['scheduler_index'], function () {
	var $fullcalendar = $('#calendar');

	var date = new Date(),
		dateYear = date.getFullYear(),
		dateMonth = date.getMonth(),
		dateDay = date.getDate();

	// build options
	var options = {
		header: {
			left: 'prev,next,today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: false,
		lazyFetching: false,
		disableResizing: true,
		year: dateYear,
		month: dateMonth,
		date: dateDay,
		allDaySlot: true,
		monthNames: [__('January'),__('February'),__('March'),__('April'),__('May'),__('June'),__('July'),__('August'),__('September'),__('October'),__('November'),__(
			'December')],
		dayNamesShort: [__('Sun'),__('Mon'),__('Tue'),__('Wed'),__('Thu'),__('Fri'),__('Sat')],
		buttonText: {
			prev: "<span class='fc-text-arrow'>&lsaquo;</span>",
			next: "<span class='fc-text-arrow'>&rsaquo;</span>",
			prevYear: "<span class='fc-text-arrow'>&laquo;</span>",
			nextYear: "<span class='fc-text-arrow'>&raquo;</span>",
			today: __('Today'),
			month: __('Month'),
			week: __('Week'),
			day: __('Day')
		},

		events: function(start, end, callback)
		{
			var roundedStart = Math.round(start.getTime() / 1000),
				roundedEnd = Math.round(end.getTime() / 1000);

			Api.get('scheduler', {from: roundedStart, to: roundedEnd}, function(response) {
				if( ! response.response) return;

				var events = [];
				for(i in response.response) {
					var item = response.response[i];
					item.className = 'popup fancybox.iframe';
					events.push(item);
				}
				callback(events);
			});


		}
	};

	$fullcalendar.fullCalendar(options);
});