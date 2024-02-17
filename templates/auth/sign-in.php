<?php
// receives form
$content = extend("auth/section/sign-in.php", ["form" => $form]);
echo extend("layout/base.php", ["main" => $content]);
