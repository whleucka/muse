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
	foreach ($links as $child) {
		echo renderListItem($child, $parent_link, $depth);
		if (!empty($child['children'])) renderLinks($child['children'], $parent_link, $depth + 10);
	}
}

function renderListItem(array $child, string $parent_link, int $depth)
{
	return sprintf("<li %s class='sidebar-link'>
		<a href='%s' data-title='%s' data-parent='%s' class='link-dark rounded' style='padding-left: %spx;'>
			%s
		</a>
	</li>", is_null($child['id']) ? 'hx-boost="false"' : '', $child['link'], $child['label'], $parent_link, $depth, $child['label']);
}
