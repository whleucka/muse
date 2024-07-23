<section id="search" class="w-100">
	<h3 class="d-flex align-items-center">
		Search
		<span class="htmx-indicator ms-2" style="font-size: 0.8rem;">
			<div class="spinner-border spinner-border-sm text-success" role="status">
			</div>
		</span>
	</h3>
	<div id="search-input" class="mt-1">
	<form hx-post="/search/music"
		hx-indicator="#search .htmx-indicator"
		hx-target="#results">
		<?=$csrf()?>
		<div class="input-group">
			<input id="input"
				class="form-control"
				type="search"
				name="term"
				value="<?=$term?>"
				placeholder="What do you want to listen to?" />
			<button id="search-submit" type="submit" class="btn btn-app" hx-sync="closest form:abort">OK</button>
		</div>
	</form>
	</div>
	<div id="results" hx-get="/search/load" hx-indicator="#search .htmx-indicator" hx-trigger="load">
	</div>
</section>
