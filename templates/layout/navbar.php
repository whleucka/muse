<nav id="navbar" class="navbar navbar-dark bg-dark text-light sticky-top">
  <div class="container-fluid">
    <span class="navbar-brand">Nebula</span>
    <div class="ms-auto">
      <select class="d-block d-sm-none form-select" onchange="htmx.ajax('GET', event.currentTarget.value, {target: 'main', select: 'main', swap: 'outerHTML show:no-scroll'});">
        <?php foreach ($links as $link): ?>
        <optgroup label="<?=$link['label']?>">
          <?php if (!empty($link['children'])): ?>
            <?renderChildren($link['children'])?>
          <?php endif ?>
        </optgroup>
        <?php endforeach ?>
      </select>
    </div>
  </div>
</nav>

<?php

function renderChildren(array $children, int $depth = 0)
{
  foreach ($children as $child) {
    echo renderOption($child['link'], $child['label'], $depth);
    if (!empty($child['children'])) renderChildren($child['children'], $depth + 5);
  }
}

function renderOption(string $value, string $label, int $depth): string
{
  $path = explode("/", $value);
  $request_uri = strtok($_SERVER["REQUEST_URI"], "?");
  $uri = explode("/", $request_uri);
  $selected = in_array(end($path), $uri) ? "selected" : '';
  return sprintf("<option value='%s' %s>%s</option>", $value, $selected, str_repeat("&nbsp;", $depth) . $label);
}
