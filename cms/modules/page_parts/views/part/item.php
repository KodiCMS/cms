<script id="part-body" type="text/template">
	<div class="part" id="part<%=name %>">
		<div class="widget-header  widget-inverse">
			<h4 <?php if( ACL::check('page.parts')): ?>class="part-name"<?php endif; ?> title="<?php echo __('Double click to edit part name.'); ?>"><%=name %></h4><input type="text" class="edit-name" value="<%=name %>" />
			
			<% if ((is_protected == <?php echo Model_Page_Part::PART_PROTECTED; ?> && is_developer == 1) || is_protected == <?php echo Model_Page_Part::PART_NOT_PROTECTED; ?> ) { %>
			<div class="widget-options pull-right">
				<?php if( ACL::check('page.parts')): ?>
				<?php echo UI::button(UI::icon( 'cog' ), array(
					'class' => 'part-options-button btn btn-mini')
				); ?>
				<?php endif; ?>
				
				<% if ( is_expanded == 0 ) { %>
				<?php echo UI::button(UI::icon( 'chevron-down icon-white' ), array(
					'class' => 'part-minimize-button btn btn-mini btn-inverse')
				); ?>		
				<% } else { %>		
				<?php echo UI::button(UI::icon( 'chevron-up icon-white' ), array(
					'class' => 'part-minimize-button btn btn-mini btn-inverse')
				); ?>
				<% } %>
			</div>
			<% } %>
		</div>
		
		<% if ((is_protected == <?php echo Model_Page_Part::PART_PROTECTED; ?> && is_developer == 1) || is_protected == <?php echo Model_Page_Part::PART_NOT_PROTECTED; ?> ) { %>
		<div class="widget-content part-options">
		<?php if( ACL::check('page.parts')): ?>
			<div class="row-fluid">
				<div class="span4 item-filter-cont form-inline">
					<label><?php echo __( 'WYSIWYG' ); ?></label>
					<select class="item-filter" name="part_filter">
						<option value="">&ndash; <?php echo __( '--- none ----' ); ?> &ndash;</option>
						<?php foreach ( WYSIWYG::findAll() as $filter ): ?> 
							<option value="<?php echo $filter; ?>" <% if (filter_id == "<?php echo $filter; ?>") { print('selected="selected"')} %> ><?php echo Inflector::humanize( $filter ); ?></option>
						<?php endforeach; ?> 
					</select>
				</div>
				<div class="span4">
					<?php echo Observer::notify('part_option'); ?>
				</div>				
				<div class="span4 text-right">
					<% if ( is_developer == 1 ) { %>
					<label class="checkbox inline">
						<input type="checkbox" name="is_protected" class="is_protected" <% if (is_protected == <?php echo Model_Page_Part::PART_PROTECTED; ?>) { print('checked="checked"')} %>> <?php echo __( 'Is protected' ); ?>
					</label>
					<label class="checkbox inline">
						<input type="checkbox" name="is_indexable" class="is_indexable" <% if (is_indexable == 1) { print('checked="checked"')} %>> <?php echo __( 'Is indexable' ); ?>
					</label>
					<% } %>
					<?php echo UI::button(__( 'Remove part :part_name', array( ':part_name' => '<%= name %>' ) ), array(
						'class' => 'item-remove btn btn-mini btn-danger', 'icon' => UI::icon( 'trash icon-white' )
					) ); ?>
				</div>
				
			</div>
			<?php else: ?>
			<select class="item-filter" name="part_filter">
				<option value="">&ndash; <?php echo __( '--- none ----' ); ?> &ndash;</option>
				<?php foreach ( WYSIWYG::findAll() as $filter ): ?> 
					<option value="<?php echo $filter; ?>" <% if (filter_id == "<?php echo $filter; ?>") { print('selected="selected"')} %> ><?php echo Inflector::humanize( $filter ); ?></option>
				<?php endforeach; ?> 
			</select>
			<?php endif; ?>
		</div>
		<% } %>
		

		<% if (is_protected == <?php echo Model_Page_Part::PART_PROTECTED; ?> && is_developer == 0 ) { %>
		<div class="widget-content ">
			<p class="text-warning"><?php echo __( 'Content of page part :part_name is protected from changes.', array( ':part_name' => '<%= name %>' ) ); ?></p>
		</div>
		<% } else { %>
		<div class="widget-content  widget-nopad part-textarea" <% if ( is_expanded == 0 ) { %>style="display:none;"<% } %>>
			<textarea id="pageEditPartContent-<%= name %>" name="part_content[<%= id %>]"><%= content.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;") %></textarea>
		</div>
		<% } %>
	</div>
</script>