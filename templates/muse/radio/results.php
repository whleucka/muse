<?php if ($radio_stations): ?>
<script>
(function() {
	updateTrackRow();
})();
</script>
<?php endif ?>

<?php foreach ($radio_stations as $i => $radio) : ?>
	<?=template("muse/radio/row.php", ["radio" => $radio])?>
<?php endforeach ?>

<?php if (!$radio_stations): ?>
	<p class="mt-4"><em>There are no stations available</em></p>
<?php endif ?>


