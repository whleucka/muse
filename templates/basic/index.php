<?php
// Receives $message
echo template("layout/base.php", [
	"content" => $message,
]);
