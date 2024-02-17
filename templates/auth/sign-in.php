<?php
// receives form
$content = template("auth/section/sign-in.php", ["form" => $form]);
echo template("layout/base.php", ["main" => $content]);
