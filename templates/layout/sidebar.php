<nav id="sidebar" class="d-none d-sm-block" hx-boost="true" hx-target="main" hx-select="main" hx-swap="outerHTML">
	<div class="flex-shrink-0 p-3">
		<ul class="list-unstyled ps-0">
		<?php foreach ($links as $key => $link): ?>
			<?php if (!empty($link['children'])): ?>
			<li class="mb-1">
				<button class="btn btn-toggle align-items-center rounded" data-bs-toggle="collapse"
					data-bs-target="#link-<?=$key?>" aria-expanded="true">
				  <?=$link['label']?>
				</button>
				<div class="collapse show" id="link-<?=$key?>">
					<?php renderLinks($link['children']); ?>
				</div>
			</li>
			<?php endif ?>
		<?php endforeach ?>
		</ul>
	</div>
</nav>

<?php

function renderLinks(array $links)
{
	?>
	<ul class="btn-toggle-nav fw-normal sidebar-links pb-1 small">
	<?php foreach ($links as $child): ?>
	<li hx-boost="<?=(!is_null($child['id']) ? "true" : "false")?>" class="sidebar-link"><a href="<?=$child['link']?>" class="link-dark rounded"><?=$child['label']?></a></li>
		<?php if (!empty($child['children'])) { renderLinks($child['children']); } ?>
	<?php endforeach ?>
	</ul>
	<?php
}
