<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= $meta ?? '' ?>
	<title>
		<?= $title . ' [Admin]' ?? 'Nebula [Admin]'  ?>
	</title>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/main.css" rel="stylesheet">
	<link href="/css/admin.css" rel="stylesheet">
	<?= $head ?? '' ?>
</head>

<body id="body" class="d-flex flex-column bg-light">
	<?= $navbar ?>
	<section class="d-flex flex-grow-1">
		<?= $sidebar ?>
		<main class="d-flex flex-column flex-grow-1" id="view">
			<section id="top" class="sticky-top bg-light">
				<?= $breadcrumbs ?>
				<section id="module-title" class="container-fluid">
					<h3>
						<?= $module_title ?>
					</h3>
				</section>
			</section>
			<section id="content" class="px-3 mt-2">
				<?= $content ?>
			</section>
		</main>
	</section>
	<script src="/js/htmx.min.js"></script>
	<script src="/js/bootstrap.bundle.min.js"></script>
	<script src="/js/feather.min.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/admin.js"></script>
	<?= $scripts ?? '' ?>
</body>

</html>
