<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= $meta ?? '' ?>
	<title><?= $title ?? 'Muse' ?></title>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/main.css" rel="stylesheet">
	<link href="/css/muse.css?<?=time()?>" rel="stylesheet">
	<?= $head ?? '' ?>
</head>

<body>
	<main class="d-flex flex-column w-100 vh-100">
		<?=template("/layout/navbar.php")?>
		<?=template("/muse/main/index.php", ["main" => $main])?>
		<?=template("/muse/player/index.php")?>
	</main>
	<script src="/js/htmx.min.js"></script>
	<script src="/js/bootstrap.bundle.min.js"></script>
	<script src="/js/feather.min.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/controls.js?<?=time()?>"></script>
	<?= $scripts ?? '' ?>
</body>

</html>
