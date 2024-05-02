<section id="flash-messages">
	<?=$messages?>
</section>
<section id="actions" class="mb-3">
	<?php if ($actions['show_create_action']) : ?>
		<button type="button" hx-get="/admin/<?= $module ?>/create" hx-swap="outerHTML" hx-select="#content" hx-target="#content" class="btn btn-success">Create</button>
	<?php endif ?>
</section>
<section id="filters">
	<?= $filters['search'] ?>
</section>
<section id="table">
	<div id="filter-links" class="my-2">
		<?= $filters['link'] ?>
	</div>
	<?= $table ?>
	<div id="pagination">
		<?= $pagination ?>
	</div>
</section>
