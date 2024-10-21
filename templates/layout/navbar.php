<nav id="navbar" class="navbar navbar-expand-md">
  <div class="container-fluid ps-2 pe-0 d-flex align-items-center">
    <a class="navbar-brand d-flex align-items-center" hx-get="/playlist" hx-target="#main" hx-select="#main" hx-swap="outerHTML"><img src="/img/logo.png" class="logo" width="32" height="32" /></a>
    <div id="search-input" class='flex-grow-1'>
        <form hx-get="/search/query"
            hx-push-url="true"
            hx-indicator="#search .htmx-indicator"
            hx-target="#main" hx-select="#main" hx-swap="outerHTML">
            <div class="input-group"> <input id="input" class="form-control" type="search"
                    name="term"
                    value=""
                    placeholder="search" />
                <button id="search-submit" type="submit" class="btn btn-app" hx-sync="closest form:abort">
                    OK
                </button>
            </div>
        </form>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#top-nav"
      aria-controls="top-nav" aria-expanded="false" aria-label="toggle navigation">
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
          <a class="nav-link text-secondary d-flex" href="#"><span id="sun-icon" class="me-2" data-feather="sun" width="14px"></span><div class="form-check form-switch"><input onclick="toggleDarkMode(event)" class="form-check-input" type="checkbox" id="dark-mode-switch" checked> <span id="moon-icon" data-feather="moon" width="12px"></span></div></a>
        </li>
      </ul>
    </div>
  </div>
</nav>
