<?php if ($show): ?>
<nav aria-label="Small page navigation" hx-boost="true" hx-target="#table" hx-select="#table" hx-swap="outerHTML">
	<ul class="pagination pagination">
		<li class="page-item <?=$current_page === 1 ? 'disabled' : ''?>"><a class="page-link" href="?page=<?=$current_page-1?>">&#706;</a></li>

		<?php if ($current_page < 6): ?>

			<?php for($i = 1; $i < 6; $i++): ?>
				<?php if ($i < $total_pages + 1) : ?>
				<li class="page-item pagination-link <?=$i === $current_page ? 'active' : ''?>" aria-current="page"><a class="page-link" href="?page=<?=$i?>"><?=$i?></a></li>
				<?php endif ?>
			<?php endfor ?>

		<?php elseif ($total_pages > 5 && $current_page > 5): ?>

			<?php if ($current_page === 1): ?>
			<li class="page-item pagination-link active" aria-current="page"><a class="page-link" href="?page=1">1</a></li>
			<?php endif ?>

			<?php if ($current_page > 1): ?>
			<li class="page-item pagination-link" aria-current="page"><a class="page-link" href="?page=1">1</a></li>
			<li class="page-item pagination-link disabled" aria-current="page"><a class="page-link" href="#">...</a></li>
			<?php endif ?>

			<?php for($i = $current_page - $side_links; $i < $current_page + 1 + $side_links; $i++): ?>
				<?php if ($i > 1 && $i < $total_pages) : ?>
				<li class="page-item pagination-link <?=$i === $current_page ? 'active' : ''?>" aria-current="page"><a class="page-link" href="?page=<?=$i?>"><?=$i?></a></li>
				<?php endif ?>
			<?php endfor ?>

			<?php if ($current_page === $total_pages): ?>
			<li class="page-item pagination-link active" aria-current="page"><a class="page-link" href="?page=<?=$total_pages?>"><?=$total_pages?></a></li>
			<?php endif ?>

			<?php if ($current_page < $total_pages): ?>
			<li class="page-item pagination-link disabled" aria-current="page"><a class="page-link" href="#">...</a></li>
			<li class="page-item pagination-link" aria-current="page"><a class="page-link" href="?page=<?=$total_pages?>"><?=$total_pages?></a></li>
			<?php endif ?>

		<?php endif ?>

		<li class="page-item <?=$current_page === $total_pages ? 'disabled' : ''?>"><a class="page-link" href="?page=<?=$current_page+1?>">&#707;</a></li>
	</ul>
</nav>
<?php endif ?>
