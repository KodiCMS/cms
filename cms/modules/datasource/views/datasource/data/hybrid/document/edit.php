<?php echo Form::open(Request::current()->url() . URL::query(array('id' => $doc->id)), array(
	'class' => 'form-horizontal'
)); ?>
	<?php echo Form::hidden('ds_id', $ds->ds_id); ?>
	<?php echo Form::hidden('id', $doc->id); ?>

	<div class="well well-small">
		<?php echo Form::input('header', $doc->header, array(
			'class' => 'input-title'
		)); ?>
		<?php echo View::factory('datasource/data/hybrid/document/fields/published', array(
			'doc' => $doc
		)); ?>
	</div>

	<?php if(!empty($record->struct[DataSource_Hybrid_Field::TYPE_PRIMITIVE] )): ?>
	<div id="primitive_fields" class="well well-small">
		<?php foreach($record->struct[DataSource_Hybrid_Field::TYPE_PRIMITIVE] as $type => $fields): ?>
			<?php if($type !== DataSource_Hybrid_Field_Primitive::PRIMITIVE_TYPE_HTML): ?>
			<?php foreach($fields as $key): ?>
			<?php echo View::factory('datasource/data/hybrid/document/fields/' . $type, array(
				'value' => $doc->fields[$key], 'field' => $record->fields[$key]
			)); ?>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php endif ;?>

	<?php $html_fields = Arr::path($record->struct, 'primitive.html');
	if(!empty($html_fields)): ?>
	<div id="html_fields" class="well well-small">
		<ul class="nav nav-tabs">
		
		<?php foreach($html_fields as $i => $key): ?>
			<li class="<?php echo $i == 0 ? 'active' : ''; ?>">
				<a href="#tab-<?php echo $key; ?>"><?php echo $record->fields[$key]->header; ?></a>
			</li>
		<?php endforeach; ?>
		</ul>

		<div class="tabs-content">
			<?php foreach($html_fields as $key): ?>
			<div class="tab-pane" id="tab-<?php echo $key; ?>">
				<?php echo View::factory('datasource/data/hybrid/document/fields/html', array(
					'value' => $doc->fields[$key], 'field' => $record->fields[$key]
				)); ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif ;?>

	<?php if(!empty($record->struct[DataSource_Hybrid_Field::TYPE_DOCUMENT])): ?>
	<div id="document_fields" class="well well-small">
		<h5><?php echo __('Related documents'); ?></h5>
		<hr />
		<?php foreach($record->struct[DataSource_Hybrid_Field::TYPE_DOCUMENT] as $type => $fields): ?>
			<?php foreach($fields as $key): ?>
			<?php echo View::factory('datasource/data/hybrid/document/fields/document', array(
				'value' => $doc->fields[$key], 'field' => $record->fields[$key], 'doc' => $doc
			)); ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</div>
	<?php endif ;?>

	<?php if(!empty($record->struct[DataSource_Hybrid_Field::TYPE_DATASOURCE]) AND $doc->id):?>
	<div id="datasources_fields" class="well well-small">
		<h5><?php echo __('Datasources'); ?></h5>
		<hr />
		<table class="table table-striped">
			<colgroup>
				<col width="200px;"/>
			</colgroup>
			<tbody>
			<?php foreach($record->struct[DataSource_Hybrid_Field::TYPE_DATASOURCE] as $fields): ?>
				<?php foreach($fields as $type => $key): ?>
				<?php echo View::factory('datasource/data/hybrid/document/fields/datasource', array(
					'value' => $doc->fields[$key], 'field' => $record->fields[$key]
				)); ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php endif;?>

	<div class="form-actions">
		<?php echo UI::actions('datasources/data' . URL::query(array(
			'ds_id' => $ds->ds_id
		), FALSE)); ?>
	</div>
<?php echo Form::close(); ?>