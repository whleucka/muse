<?php if($show): ?>
<div id="filter-search">
	<input hx-get="?term" hx-swap="outerHTML" hx-target="#table" hx-select="#table"
		hx-trigger="input changed delay:500ms, search" name="term" value="<?=$term?>" type="search" class="form-control"
		placeholder="Search">
</div>
<?php endif ?>
