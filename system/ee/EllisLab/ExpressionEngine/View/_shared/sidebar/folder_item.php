<div class="sidebar__link sidebar__link--parent <?=$class?>" data-<?=$key?>="<?=$value?>">
	<a href="<?=$url?>"<?php if ($external) echo ' rel="external"'?>>
		<?php if (!empty($icon)): ?>
			<i class="fas fa-<?=$icon?>"></i>
		<?php else: ?>
			<i class="fas fa-folder"></i>
		<?php endif; ?>
		<?=$text?>
	</a>
	<?php if ($edit || $remove): ?>
	<ul class="toolbar">
		<?php if ($edit): ?>
		<li class="edit"><a href="<?=$edit_url?>" title="<?=lang('edit')?>"></a></li>
		<?php endif; ?>
		<?php if ($remove): ?>
		<li class="remove"><a class="m-link" rel="modal-confirm-<?=$modal_name?>" href="" title="<?=lang('remove')?>" data-confirm="<?=$confirm?>" data-<?=$key?>="<?=$value?>"></a></li>
		<?php endif; ?>
	</ul>
	<?php endif; ?>
</div>
