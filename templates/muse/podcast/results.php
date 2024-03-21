<?php foreach ($results->results as $i => $podcast) : ?>
	<?=template("muse/podcast/row.php", ["podcast" => $podcast])?>
<?php endforeach ?>

<?php if (!$results): ?>
	<p class="mt-2"><em>Sorry, no podcasts could be found</em></p>
<?php endif ?>
