<?php
    foreach ($links as $child): ?>
        <li class="sidebar-link"><a href="<?=str_replace(".php", '', $child['link'])?>"
                title="<?=$child['label']?>"
                data-title="<?=$child['label']?>"
                data-parent="<?=$parent_link?>"
                style="padding-left: <?=$depth?>px;"
                class="link link-dark rounded truncate">
                <?=$child['label']?>
            </a></li>
        <?php if (!empty($child['children'])) { echo template("components/sidebar_links.php", ["links" => $child['children'], "parent_link" => $parent_link, "depth" => $depth + 10]); } ?>
    <?php endforeach;

