<?php
// receives form
$content = template("auth/section/register.php", ["form" => $form]);
echo template("layout/base.php", ["main" => $content]);
