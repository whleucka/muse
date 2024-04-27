<form enctype="multipart/form-data">
	<?= $csrf() ?>
	<?php foreach ($data as $datum) : ?>
		<div class="mb-3">
			<label for="<?= $datum->column ?>" class="form-label"><?= $datum->title ?></label>
			<input type="<?= $datum->column ?>" class="form-control" name="<?= $datum->column ?>" id="<?= $datum->column ?>" value="" placeholder="">
			<?= $request_errors($datum->column, $datum->title) ?>
		</div>
	<?php endforeach ?>
	<div>
		<button hx-post="" hx-swap="outerHTML" hx-target="#content" hx-select="#content" id="create-new" type="submit" class="btn btn-success">Create</button>
		<a hx-boost="true" href="/admin/<?= $module ?>/" hx-swap="outerHTML" hx-target="#content" hx-select="#content" class="btn btn-warning ms-1">Back</a>
	</div>
</form>
