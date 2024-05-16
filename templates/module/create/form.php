<form enctype="multipart/form-data" class="pt-2">
	<?= $csrf() ?>
	<?php foreach ($data as $datum) : ?>
		<div class="row mt-2">
			<div class="col-3 form-label d-flex align-items-center">
				<span title="<?= $datum->title ?>"><?= $datum->title ?></span>
			</div>
			<div class="col-9 form-content d-flex align-items-center">
				<div class="w-100">
					<?= $control($datum->column, $old($datum->column, null), $datum->title) ?>
					<?= $request_errors($datum->column) ?>
				</div>
			</div>
		</div>
	<?php endforeach ?>
	<div class="mt-2">
		<button hx-post="" hx-swap="outerHTML" hx-target="#view" hx-select="#view" id="create-new" type="submit" class="btn btn-success">Create</button>
		<button hx-get="/admin/<?= $module ?>/" hx-swap="outerHTML" hx-target="#view" hx-select="#view" class="btn btn-secondary ms-1">Back</button>
	</div>
</form>
