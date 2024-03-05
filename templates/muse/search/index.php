<section id="search" class="w-100">
	<h3 class="d-flex align-items-center">
		Search
		<span data-feather="search" class="ms-2"></span>
		<span class="htmx-indicator ms-2" style="font-size: 0.8rem;">
			<div class="spinner-border spinner-border-sm text-success" role="status">
			</div>
		</span>
	</h3>
	<form onkeydown="return event.key != 'Enter';">
		<?=$csrf()?>
		<input id="input"
			hx-post="/search"
			hx-trigger="load, input changed delay:500ms, search"
			hx-target="#results"
			hx-indicator=".htmx-indicator"
			class="form-control"
			type="search"
			name="term"
			value="<?=$term?>"
			placeholder="What do you want to listen to?" />
	</form>
	<div id="results">
	</div>
</section>
<script>
if (typeof feather !== 'undefined') {
	feather.replace();
}
</script>
