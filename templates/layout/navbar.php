<nav id="navbar" class="navbar navbar-expand-sm bg-light">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" hx-get="/playlist" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><img src="/img/logo.png" width="32" height="32" /></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#top-nav"
      aria-controls="top-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"><span data-feather="menu"></span></span>
    </button>
    <div class="collapse navbar-collapse" id="top-nav">
      <ul class="navbar-nav me-auto mb-2 mb-sm-0">
        <li class="nav-item" data-link="playlist">
          <a class="nav-link text-secondary" href="#" hx-get="/playlist" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="music" class="me-2" width="16px"></span> Playlist</a>
        </li>
        <li class="nav-item" data-link="search">
          <a class="nav-link text-secondary" href="#" hx-get="/search" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="search" class="me-2" width="16px"></span> Search</a>
        </li>
        <li class="nav-item" data-link="radio">
          <a class="nav-link text-secondary" href="#" hx-get="/radio" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="radio" class="me-2" width="16px"></span> Radio</a>
        </li>
        <li class="nav-item" data-link="podcast">
          <a class="nav-link text-secondary" href="#" hx-get="/podcast" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="mic" class="me-2" width="16px"></span> Podcasts</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-secondary" href="#"><div class="form-check form-switch"><input onclick="toggleDarkMode(event)" class="form-check-input" type="checkbox" id="dark-mode-switch" checked> <span data-feather="moon" width="16px"></span></div></a>
        </li>
      </ul>
    </div>
  </div>
</nav>
