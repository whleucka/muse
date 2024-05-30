<nav id="sidebar" class="d-none d-sm-block" hx-boost="true" hx-target="#view" hx-select="#view" hx-swap="outerHTML show:no-scroll">
	<div class="mb-3">
		<input type="search" class="form-control" id="filter" placeholder="Filter" tabindex="-1">
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
						<?= template("components/sidebar_links.php", ["links" => $link["children"], "parent_link" => $link["label"], "depth" => 0]) ?>
					</ul>
				</div>
			</li>
			<?php endif ?>
			<?php endforeach ?>
		</ul>
	</div>
</nav>
