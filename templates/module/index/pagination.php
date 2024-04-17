<?php if ($show): ?>
<nav aria-label="Small page navigation" hx-boost="true" hx-target="#table-update" hx-select="#table-update" hx-swap="outerHTML">
	<ul class="pagination pagination-sm">
		<li class="page-item <?=$current_page === 1 ? 'disabled' : ''?>"><a class="page-link" href="?page=<?=$current_page-1?>">Previous</a></li>
		<?php for($i = 1; $i < $total_pages + 1; $i++): ?>
			<li class="page-item <?=$i === $current_page ? 'active' : ''?>" aria-current="page"><a class="page-link" href="?page=<?=$i?>"><?=$i?></a></li>
		<?php endfor ?>
		<li class="page-item <?=$current_page === $total_pages ? 'disabled' : ''?>"><a class="page-link" href="?page=<?=$current_page+1?>">Next</a></li>
	</ul>
</nav>
<?php endif ?>
