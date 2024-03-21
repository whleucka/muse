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
	<link rel="apple-touch-icon" sizes="180x180" href="/ico/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/ico/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/ico/favicon-16x16.png">
	<link rel="manifest" href="/ico/site.webmanifest">
	<?= $head ?? '' ?>
</head>

<body>
	<main class="d-flex flex-column w-100 vh-100">
		<?=template("/layout/navbar.php")?>
		<?=template("/muse/main/index.php", ["main" => $main])?>
		<?=template("/muse/player/index.php", ["shuffle" => $shuffle, "repeat" => $repeat])?>
	</main>
	<script src="/js/htmx.min.js"></script>
	<script src="/js/bootstrap.bundle.min.js"></script>
	<script src="/js/feather.min.js"></script>
	<script src="/js/hls.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/controls.js?<?=time()?>"></script>
	<script src="/js/podcast.js?<?=time()?>"></script>
	<script src="/js/radio.js?<?=time()?>"></script>
	<script src="/js/track.js?<?=time()?>"></script>
	<script src="/js/playlist.js?<?=time()?>"></script>
	<?= $scripts ?? '' ?>
</body>

</html>
