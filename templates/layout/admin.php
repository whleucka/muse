<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= $meta ?? '' ?>
	<title><?= $title . ' [Admin]' ?? 'Nebula [Admin]'  ?></title>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/main.css" rel="stylesheet">
	<link href="/css/admin.css" rel="stylesheet">
	<?= $head ?? '' ?>
</head>

<body>
	<main class="d-flex flex-column w-100 vh-100 p-2">
		<section id="module-title">
			<h4><?=$module_title ?></h4>
		</section>
		<section id="content" class="mt-2">
			<?= $content ?>
		</section>
	</main>
	<script src="/js/htmx.min.js"></script>
	<script src="/js/bootstrap.bundle.min.js"></script>
	<script src="/js/feather.min.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/admin.js"></script>
	<?= $scripts ?? '' ?>
</body>

</html>
