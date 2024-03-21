<nav id="navbar" class="navbar navbar-expand-sm bg-light">
  <div class="container-fluid">
    <span class="navbar-brand d-flex align-items-center"><img src="/img/logo.png" width="32" height="32" /></span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#top-nav"
      aria-controls="top-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"><span data-feather="menu"></span></span>
    </button>
    <div class="collapse navbar-collapse" id="top-nav">
      <ul class="navbar-nav me-auto mb-2 mb-sm-0">
        <li class="nav-item">
          <a class="nav-link text-secondary" href="#" hx-get="/playlist" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="music" class="me-2" width="16px"></span> Playlist</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-secondary" href="#" hx-get="/search" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="search" class="me-2" width="16px"></span> Search</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-secondary" href="#" hx-get="/radio" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="radio" class="me-2" width="16px"></span> Radio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-secondary" href="#" hx-get="/podcast" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><span data-feather="mic" class="me-2" width="16px"></span> Podcasts</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div id="top-progress">
  <div class="progress w-100 me-3">
    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="player-progress"></div>
    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="preload-progress"></div>
  </div>
</div>
