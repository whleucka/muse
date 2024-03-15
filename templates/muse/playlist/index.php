<section id="playlist" class="w-100">
	<h3 class="d-flex align-items-center">
		<span data-feather="music" class="me-2"></span>

		Playlist
		<span class="htmx-indicator ms-2" style="font-size: 0.8rem;">
			<div class="spinner-border spinner-border-sm text-success" role="status">
			</div>
		</span>
	</h3>
	<div id="load" hx-get="/playlist/load" hx-trigger="load">
	</div>
</section>
<script>
if (typeof feather !== 'undefined') {
	feather.replace();
}
</script>
