<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<ul data-level="<?php echo $level; ?>" class="unstyled">
	<?php foreach($childrens as $child): ?>
	<?php if($child instanceof Model_Page): ?>
	<li data-id="<?php echo $child->id; ?>" <?php if($child->is_expanded) echo('class="item-expanded"'); ?>>
		<div class="item">
			<div class="row-fluid">
				<div class="title span7">
					
					<?php if( $child->has_children ): ?>
					<?php
						if($child->is_expanded)
						{
							echo UI::icon( 'minus item-expander item-expander-expand');
						}
						else
						{
							echo UI::icon( 'plus item-expander');
						}
					?>
					<?php endif; ?>
					
					
					<?php if( ! AuthUser::hasPermission($child->getPermissions()) ): ?>
					<?php echo UI::icon('lock'); ?>
					<em title="/<?php echo $child->getUri(); ?>"><?php echo $child->title; ?></em>
					<?php else: ?>
					<?php 
						echo UI::icon('file');
						echo HTML::anchor( 'page/edit/'.$child->id, $child->title );
					?>
					<?php endif; ?>				
					<?php if( !empty($child->behavior_id) ): ?> <?php echo UI::label(Inflector::humanize($child->behavior_id), 'default'); ?><?php endif; ?>
					<?php echo HTML::anchor($child->getUrl(), UI::label(__('View page')), array(
						'class' => 'item-preview', 'target' => '_blank'
					)); ?>
				</div>
				<div class="date span2">
					<?php echo Date::format($child->published_on); ?>
				</div>
				<div class="status span2">
					<?php switch ($child->status_id):
						case Model_Page::STATUS_DRAFT:    echo UI::label(__('Draft'), 'info');       break;
						case Model_Page::STATUS_REVIEWED: echo UI::label(__('Reviewed'), 'info'); break;
						case Model_Page::STATUS_HIDDEN:   echo UI::label(__('Hidden'), 'default');     break;
						case Model_Page::STATUS_PUBLISHED:
							if( strtotime($child->published_on) > time() )
								echo UI::label(__('Pending'), 'success');
							else
								echo UI::label(__('Published'), 'success');
						break;
					endswitch; ?>
				</div>
				<div class="actions span1">
					<?php echo UI::button(NULL, array(
						'href' => 'page/add/'.$child->id, 'icon' => UI::icon('plus'), 
						'class' => 'btn btn-mini'
					)); ?>
					<?php 
					if( AuthUser::hasPermission($child->getPermissions()) )
					{
						echo UI::button(NULL, array(
							'href' => 'page/delete/'.$child->id, 'icon' => UI::icon('remove icon-white'), 
							'class' => 'btn btn-mini btn-confirm btn-danger'
						));
					}
					?>
				</div>
			</div>
		</div>
		
		<?php if( $child->is_expanded ) echo($child->children_rows); ?>
	<?php else: ?>
	<li>
		<div class="item">
			<div class="row-fluid">
				<div class="title span12">
					<?php echo $child; ?>
				</div>
			</div>
		</div>
	
	<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>