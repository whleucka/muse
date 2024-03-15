<section id="radio" class="w-100">
	<h3 class="d-flex align-items-center">
		<span data-feather="radio" class="me-2"></span>
		Radio
		<span class="htmx-indicator ms-2" style="font-size: 0.8rem;">
			<div class="spinner-border spinner-border-sm text-success" role="status">
			</div>
		</span>
	</h3>
	<div id="load" hx-get="/radio/load" class="mt-3" hx-trigger="load">
	</div>
</section>
<script>
if (typeof feather !== 'undefined') {
	feather.replace();
}
</script>

