<?php
// The message is received from BasicController
// In this example, this template extends the layout/base.php template

$content = template("basic/message.php", [
	"message" => $message
]);

echo extend("layout/base.php", "content", $content);
