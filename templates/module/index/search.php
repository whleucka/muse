<?php if($show): ?>
<div id="filter-search">
	<input hx-get="" hx-swap="outerHTML" hx-target="#list" hx-select="#list"
		hx-trigger="input changed delay:500ms, search" name="term" value="<?=$term?>" type="search" class="form-control"
		placeholder="Search">
</div>
<?php endif ?>
