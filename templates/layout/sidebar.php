<nav id="sidebar" hx-boost="true" hx-target="main" hx-select="main" hx-swap="outerHTML">
	<div class="flex-shrink-0 p-3">
		<ul class="list-unstyled ps-0">
			<li class="mb-1">
				<button class="btn btn-toggle align-items-center rounded" data-bs-toggle="collapse"
					data-bs-target="#home-collapse" aria-expanded="true">
					Administration
				</button>
				<div class="collapse show" id="home-collapse">
					<ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
						<li><a href="/admin/users" class="link-dark rounded">Users</a></li>
						<li><a href="/admin/sessions" class="link-dark rounded">Sessions</a></li>
					</ul>
				</div>
			</li>
			<li class="border-top my-3"></li>
			<li class="mb-1">
				<button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse"
					data-bs-target="#account-collapse" aria-expanded="true">
					Account
				</button>
				<div class="collapse show" id="account-collapse">
					<ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
						<li><a href="/admin/profile" class="link-dark rounded">Profile</a></li>
						<li><a href="/sign-out" hx-boost="false" class="link-dark rounded">Sign out</a></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
</nav>
