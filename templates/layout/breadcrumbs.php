<nav id="breadcrumbs" aria-label="breadcrumb" class="my-2 container-fluid" hx-boost="true" hx-target="main" hx-select="main" hx-swap="outerHTML show:no-scroll">
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $i => $breadcrumb): ?>
		<?php if ($breadcrumb->path): ?><a href='/admin/<?=$breadcrumb->path?>'><?php endif ?>
		<li class="breadcrumb-item <?php if ($i === count($breadcrumbs) - 1): ?>active<?php endif ?>" <?php if ($i === count($breadcrumbs) - 1): ?>aria-current="page"<?php endif ?>><?=$breadcrumb->title?></li>
		<?php if ($breadcrumb->path): ?></a><?php endif ?>
		<?php if ($i !== count($breadcrumbs) - 1): ?>
		<span class="px-1">></span>
		<?php endif ?>
	<?php endforeach ?>
  </ol>
</nav>
