<?php if ($show): ?>
<div class="d-flex">
	<nav aria-label="Page navigation" hx-boost="true" hx-target="#table" hx-select="#table" hx-swap="outerHTML">
		<?php if ($total_pages > 1): ?>
		<ul class="pagination pagination">
			<li class="page-item <?=$current_page === 1 ? 'disabled' : ''?>"><a id="page-prev" class="page-link" href="?page=<?=$current_page-1?>">&#706;</a></li>

			<?php if ($current_page < 6): ?>

				<?php for($i = 1; $i < 6; $i++): ?>
					<?php if ($i < $total_pages + 1) : ?>
					<li class="page-item pagination-link <?=$i === $current_page ? 'active' : ''?>" aria-current="page"><a class="page-link" href="?page=<?=$i?>"><?=$i?></a></li>
					<?php endif ?>
				<?php endfor ?>

				<?php if ($total_pages > 5): ?>
				<li class="page-item pagination-link disabled" aria-current="page"><a class="page-link" href="#">...</a></li>
				<li class="page-item pagination-link" aria-current="page"><a class="page-link" href="?page=<?=$total_pages?>"><?=$total_pages?></a></li>
				<?php endif ?>

			<?php elseif ($total_pages > 5 && $current_page > 5): ?>

				<?php if ($current_page === 1): ?>
				<li class="page-item pagination-link active" aria-current="page"><a class="page-link" href="?page=1">1</a></li>
				<?php endif ?>

				<?php if ($current_page > 1): ?>
				<li class="page-item pagination-link" aria-current="page"><a class="page-link" href="?page=1">1</a></li>
				<li class="page-item pagination-link disabled" aria-current="page"><a class="page-link" href="#">...</a></li>
				<?php endif ?>

				<?php for($i = $current_page - $side_links; $i < $current_page + $side_links + 1; $i++): ?>
					<?php if ($i > 1 && $i < $total_pages) : ?>
					<li class="page-item pagination-link <?=$i === $current_page ? 'active' : ''?>" aria-current="page"><a class="page-link" href="?page=<?=$i?>"><?=$i?></a></li>
					<?php endif ?>
				<?php endfor ?>

				<?php if ($current_page === $total_pages): ?>
				<li class="page-item pagination-link active" aria-current="page"><a class="page-link" href="?page=<?=$total_pages?>"><?=$total_pages?></a></li>
				<?php endif ?>

				<?php if ($current_page < $total_pages): ?>
					<?php if ($current_page != $total_pages - 1): ?>
					<li class="page-item pagination-link disabled" aria-current="page"><a class="page-link" href="#">...</a></li>
					<?php endif ?>
					<li class="page-item pagination-link" aria-current="page"><a class="page-link" href="?page=<?=$total_pages?>"><?=$total_pages?></a></li>
				<?php endif ?>

			<?php endif ?>

			<li class="page-item <?=$current_page === $total_pages ? 'disabled' : ''?>"><a id="page-next" class="page-link" href="?page=<?=$current_page+1?>">&#707;</a></li>
		</ul>
		<?php endif ?>
	</nav>
	<div class="flex-grow-1"></div>
	<nav id="per-page">
		<select class="page-link text-dark" name="per_page" hx-get="?per_page"  hx-target="#table" hx-select="#table" hx-swap="outerHTML">
			<?php foreach ($per_page_options as $option): ?>
				<option <?=($per_page === $option ? 'selected' : '')?>><?=$option?></option>
			<?php endforeach ?>
		</select>
	</nav>
</div>
<script>
	var current_page = parseInt(<?=$current_page?>);
	var total_pages = parseInt(<?=$total_pages?>);
	document.onkeypress = (e) => {
		e = e || window.event;
		console.log(e.keyCode);
		if (e.keyCode === 91) {
			// key: [
			prevPage();
		} else if (e.keyCode === 93) {
			// key: ]
			nextPage();
		}
	}
	const nextPage = () => {
		if (current_page < total_pages) {
			document.getElementById("page-next").click();
		}
	}
	const prevPage = () => {
		if (current_page > 1) {
			document.getElementById("page-prev").click();
		}
	}
</script>
<?php endif ?>
