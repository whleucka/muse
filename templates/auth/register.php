<?php
// receives form
$content = extend("auth/section/register.php", ["form" => $form]);
echo extend("layout/base.php", ["main" => $content]);

