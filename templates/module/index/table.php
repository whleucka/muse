<div class="table-responsive">
	<table class="table">
		<thead class="gradient-dark">
			<tr>
				<?php foreach ($headers as $header) : ?>
				<th scope="col">
					<?= $header ?>
				</th>
				<?php endforeach ?>
				<?php if ($show_row_actions): ?>
				<th></th>
				<?php endif ?>
			</tr>
		</thead>
		<tbody>
			<?php if ($data) : ?>
			<?php foreach ($data as $row) : ?>
			<tr>
				<?php foreach ($row as $column => $value) : ?>
				<td class="align-top">
					<?= $format($column, $value) ?>
				</td>
				<?php endforeach ?>
				<?php if ($show_row_actions): ?>
				<td class="row-action align-top">
					<div class="w-100 d-flex justify-content-end">
					<?php if ($has_row_edit($row)): ?>
					<button type="button" hx-get="/admin/<?=$module?>/<?=$row->$primary_key?>" hx-swap="outerHTML" hx-select="#content" hx-target="#content" class="btn btn-sm btn-primary ms-1">Edit</button>
					<?php endif ?>
					<?php if ($has_row_delete($row)): ?>
					<button type="button" hx-delete="/admin/<?=$module?>/<?=$row->$primary_key?>" hx-swap="outerHTML" hx-select="#content" hx-target="#content" class="btn btn-sm btn-danger ms-1" disabled>Delete</button
					<?php endif ?>
					</div>
				</td>
				<?php endif ?>
			</tr>
			<?php endforeach ?>
			<?php else : ?>
			<tr>
				<td align="center" colspan="<?=count($headers)?>"><em>There are no records</em></td>
			</tr>
			<?php endif ?>
		</tbody>
	</table>
</div>
