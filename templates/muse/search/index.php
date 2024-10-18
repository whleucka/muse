<section id="search" class="w-100">
	<h3 class="d-flex align-items-center">
		Search
		<span class="htmx-indicator ms-2" style="font-size: 0.8rem;">
			<div class="spinner-border spinner-border-sm text-success" role="status">
			</div>
		</span>
	</h3>
	<div id="results" hx-get="/search/load" hx-indicator="#search .htmx-indicator" hx-trigger="load">
	</div>
</section>
