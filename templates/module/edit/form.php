<form enctype="multipart/form-data">
	<?= $csrf() ?>
	<?php foreach ($data as $datum) : ?>
		<div class="mb-3">
			<label for="<?= $datum->column ?>" class="form-label"><?= $datum->title ?></label>
			<input type="<?= $datum->column ?>" class="form-control" name="<?= $datum->column ?>" id="<?= $datum->column ?>" value="<?= $datum->value ?>" placeholder="">
			<?= $request_errors($datum->column, $datum->title) ?>
		</div>
	<?php endforeach ?>
	<div>
		<button hx-patch="" name="save" hx-swap="outerHTML" hx-target="#content" hx-select="#content" id="edit-save" type="submit" class="btn btn-primary">Save</button>
		<button hx-get="/admin/<?= $module ?>/" hx-swap="outerHTML" hx-target="#content" hx-select="#content" class="btn btn-secondary ms-1">Back</button>
	</div>
</form>
