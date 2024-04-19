<?php if ($show): ?>
<nav aria-label="Small page navigation" hx-boost="true" hx-target="#table" hx-select="#table" hx-swap="outerHTML">
	<ul class="pagination pagination-sm">
		<li class="page-item <?=$current_page === 1 ? 'disabled' : ''?>"><a class="page-link" href="?page=<?=$current_page-1?>">Previous</a></li>

		<?php if ($current_page === 1): ?>
		<li class="page-item pagination-link active" aria-current="page"><a class="page-link" href="?page=1">1</a></li>
		<?php endif ?>

		<?php if ($current_page > 1): ?>
		<li class="page-item pagination-link" aria-current="page"><a class="page-link" href="?page=1">1</a></li>
		<li class="page-item pagination-link disabled" aria-current="page"><a class="page-link" href="#">...</a></li>
		<?php endif ?>

		<?php for($i = $current_page - 2; $i < $current_page + 3; $i++): ?>
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

		<li class="page-item <?=$current_page === $total_pages ? 'disabled' : ''?>"><a class="page-link" href="?page=<?=$current_page+1?>">Next</a></li>
	</ul>
</nav>
<?php endif ?>
