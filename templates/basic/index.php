<?php
// The message is received from BasicController
// In this example, this template extends the layout/base.php template
// and sets the main variable with content

$content = template("basic/component/message.php", [
	"message" => $message
]);

echo extend("layout/base.php", ["main" => $content]);
