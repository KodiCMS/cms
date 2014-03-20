<style>
	.row-container {
		padding: 25px;
		background: #fff;
		margin-bottom: 20px;
	}

		.fields-container {
			background: #eee;
			padding: 5px;
		}

			.fields-container > div { 
				margin: 5px;
				padding: 5px;
				cursor: pointer;
			}
	
	.row-container {
		position: relative;
	}
		.row-container .close {
			position: absolute;
			top: 25px; right: 25px;
		}
		
	.fields-container .ui-sortable-helper {
		border: 2px dashed #f6a828;
		background: #fff;
		width: 200px;
	}
	
	.fields-container .ui-sortable-placeholder {
		border: 2px dashed #f6a828;
		background: #fff;
		width: 100%; height: 20px;
		content: ".";
	}
</style>
<script>
	$(function() {
		$("div.fields-container").sortable({
			connectWith: "div.fields-container",
			forcePlaceholderSize: true,
			forceHelperSize: true,
			helper: "clone",
			update: function( event, ui ) {
				var data = [];
				$('.rows').each(function(i, el) {
					var row = {};
					
					console.log($('.row-container', el))
					
					if($('.row-container', el).length > 0) {
						$('.row-container', el).each(function(i, rc) {
							if($('.fields-container', rc).length > 0) {
								row[$(el).data('id')] = [];

								$('.fields-container', el).each(function(i, fc) {
									if($('div', fc).length > 0) {
										var tfc = [];
										$('div', fc).each(function(i, f) {
											tfc.push($(f).data('id'));
										});

										row[$(el).data('id')].push(tfc);
									}
								})
							}
						}
					}
					
					
					
					
					data.push(row);
				})
				
				console.log(data)
			}
		});

		$("div.rows").sortable({
			items: ".row-container"
		});

		$(".fields-container").disableSelection();
	});
</script>
<div class="row-container">
	<h4>Fields</h4>
	<div class="row-fluid">
		<div class="fields-container span12">
			<?php foreach ($fields as $field): ?>
				<div data-id="field_<?php echo $field->id; ?>"><?php echo $field->name; ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div class="rows">
	<div class="row-container" data-id="row_1">
		<h4>Row 1</h4>
		<div class="row-fluid">
			<div class="fields-container span6" data-id="col_1"></div>
			<div class="fields-container span6" data-id="col_2"></div>
		</div>
		
		<button class="close">&times;</button>
	</div>

	<div class="row-container" data-id="row_2">
		<h4>Row 2</h4>
		<div class="row-fluid">
			<div class="fields-container span6" data-id="col_1"></div>
			<div class="fields-container span6" data-id="col_2"></div>
		</div>
		
		<button class="close">&times;</button>
	</div>
</div>
