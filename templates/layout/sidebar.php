<nav id="sidebar" class="d-none d-sm-block" hx-boost="true" hx-target="main" hx-select="main" hx-swap="outerHTML show:no-scroll">
	<div class="mb-3">
		<input type="search" class="form-control" id="filter" placeholder="Filter">
	</div>

	<div class="flex-shrink-0">
		<ul class="list-unstyled ps-0">
			<?php foreach ($links as $key => $link): ?>
			<?php if (!empty($link['children'])): ?>
			<li class="mb-1">
				<button class="btn btn-toggle align-items-center rounded parent-link"
					data-bs-toggle="collapse"
					data-bs-target="#link-<?=$key?>"
					aria-expanded="false">
					<?=$link['label']?>
				</button>
				<div class="collapse submenu" id="link-<?=$key?>">
					<ul class="btn-toggle-nav fw-normal sidebar-links pb-1 small">
						<?php renderLinks($link['children'], $link['label']); ?>
					</ul>
				</div>
			</li>
			<?php endif ?>
			<?php endforeach ?>
		</ul>
	</div>
</nav>

<?php

function renderLinks(array $links, string $parent_link, int $depth = 0)
{
	foreach ($links as $child): ?>
	<li <?=(is_null($child['id']) ? 'hx-boost="false"' : '' ) ?> class="sidebar-link"><a href="<?=$child['link']?>"
			data-title="<?=$child['label']?>"
			data-parent="<?=$parent_link?>"
			class="link-dark rounded"
			style="padding-left: <?=$depth?>px;"
			>
			<?=$child['label']?>
		</a></li>
	<?php if (!empty($child['children'])) { renderLinks($child['children'], $parent_link, $depth + 10); }
	endforeach;
}
