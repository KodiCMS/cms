<script>
	$(function() {
		function format(state) {
			return '<a target="_blank" href="/backend/hybrid/document/view?ds_id=<?php echo $field->from_ds; ?>&id='+state.id+'">' + state.text + '</a>';
		}
		$('input[name="<?php echo $field->name; ?>"]').select2({
			placeholder: __("Type first 1 chars to find documents"),
			minimumInputLength: 1,
			multiple:true,
			formatSelection: format,
			escapeMarkup: function(m) { return m; },
			ajax: {
				url: '/api/datasource/hybrid-document.find',
				data: function(query, pageNumber, context) {
					return {
						key: query,
						<?php if(!empty($doc->id)): ?>id: <?php echo $doc->id; ?>,<?php endif; ?>
						doc_ds: <?php echo $field->from_ds; ?>
					}
				},
				dataType: 'json',
				results: function (resp, page) {
					return {results: resp.response};
				}
			},
			initSelection: function(element, callback) {
				var id = $(element).val(),
					ids = id.split(',');

				ids = _.map(ids, function(num){ return parseInt(num); });

				if (id !== "") {
					$.ajax('/api/datasource/hybrid-document.find', {
						data: {
							ids: ids,
							<?php if(!empty($doc->id)): ?>id: <?php echo $doc->id; ?>,<?php endif; ?>
							doc_ds: <?php echo $field->from_ds; ?>
						},
						dataType: 'json',
					}).done(function(resp, page) {
						selected = [];
						for(row in resp.response) {
							if(_.indexOf(ids, resp.response[row]['id']) >= 0){
								selected.push(resp.response[row]);
							}
						}
						callback(selected);
					});
				}
			}
		});
	})
</script>

<div class="control-group">
	<label class="control-label"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<div class="row-fluid">
			<div class="span9">
				<?php echo Form::hidden($field->name, implode( ',',  array_keys( $value )), array(
					'id' => $field->name, 'class' => 'span12'
				)); ?>
			</div>
			<div class="span1">
				<?php echo UI::button(__('Create new'), array(
					'href' => Route::get('datasources')->uri(array(
						'directory' => 'hybrid',
						'controller' => 'document',
						'action' => 'create'
					)) . URL::query(array('ds_id' => $field->from_ds), FALSE),
					'icon' => UI::icon('building'),
					'class' => 'btn popup fancybox.iframe',
					'data-target' => $field->name
				)); ?>
			</div>
		</div>
		
		<?php if(isset($field->hint)): ?>
		<span class="help-block"><?php echo $field->hint; ?></span>
		<?php endif; ?>
	</div>
</div>
