<?php

namespace App\Muse\Music;

class Scan
{
	private array $extensions = [
		"mp3",
		"flac",
		"wav",
		"aac",
		"ogg",
		"aiff",
	];

	public function find(string $path): array
	{
		$files = [];
		if (is_dir($path)) {
			$entries = scandir($path);
			foreach ($entries as $entry) {
				if (in_array($entry, [".", ".."])) {
					continue;
				}

				$fullPath = $path . DIRECTORY_SEPARATOR . $entry;
				if (is_file($fullPath)) {
					$ext = pathinfo($entry, PATHINFO_EXTENSION);
					if (in_array(strtolower($ext), $this->extensions)) {
						$files[] = $fullPath;
					}
				} else {
					$files = array_merge($files, $this->find($fullPath));
				}
			}
		} else {
			throw new \InvalidArgumentException("Path '$path' is not a directory");
		}

		return $files;
	}
}
