<section id="podcasts" class="w-100">
	<h3 class="d-flex align-items-center">
		Podcasts
		<span class="htmx-indicator ms-2" style="font-size: 0.8rem;">
			<div class="spinner-border spinner-border-sm text-success" role="status">
			</div>
		</span>
	</h3>
	<div id="search-input" class="mt-1">
		<form hx-post="/search/podcast"
			hx-target="#results"
			hx-indicator="#podcasts .htmx-indicator">
			<?=$csrf()?>
			<div class="input-group">
				<input id="input"
					hx-sync="closest form:abort"
					class="form-control"
					type="search"
					name="term"
					value=""
					placeholder="What do you want to listen to?" />
				<button id="search-submit" type="submit" class="btn btn-app" hx-sync="closest form:abort">OK</button>
			</div>
		</form>
	</div>
	<div id="results" class="mt-3">
	</div>
</section>
