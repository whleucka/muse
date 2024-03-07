<nav id="navbar" class="navbar navbar-expand-sm bg-light">
  <div class="container-fluid">
    <a id="colours" class="navbar-brand">Muse</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#top-nav"
      aria-controls="top-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"><span data-feather="menu"></span></span>
    </button>
    <div class="collapse navbar-collapse" id="top-nav">
      <ul class="navbar-nav me-auto mb-2 mb-sm-0">
        <li class="nav-item">
          <a class="nav-link text-secondary" href="#" hx-get="/playlist" hx-target="#main" hx-select="#main"
            hx-swap="outerHTML">Playlist</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-secondary" href="#" hx-get="/search" hx-target="#main" hx-select="#main"
            hx-swap="outerHTML">Search</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
