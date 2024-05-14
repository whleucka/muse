<form enctype="multipart/form-data">
	<?= $csrf() ?>
	<?php foreach ($data as $datum) : ?>
		<div class="mb-3">
			<label for="<?= $datum->column ?>" class="form-label"><?= $datum->title ?></label>
			<?= $control($datum->column, null, $datum->title) ?>
			<?= $request_errors($datum->column) ?>
		</div>
	<?php endforeach ?>
	<div>
		<button hx-post="" hx-swap="outerHTML" hx-target="#view" hx-select="#view" id="create-new" type="submit" class="btn btn-success">Create</button>
		<button hx-get="/admin/<?= $module ?>/" hx-swap="outerHTML" hx-target="#view" hx-select="#view" class="btn btn-secondary ms-1">Back</button>
	</div>
</form>
