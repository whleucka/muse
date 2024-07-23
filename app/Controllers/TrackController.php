<?php

namespace App\Controllers;

use App\Models\Track;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(prefix: "/track")]
class TrackController extends Controller
{
	#[Get("/stream/{uuid}", "track.stream")]
	public function stream(string $uuid): void
	{
		$track = Track::findByAttribute("uuid", $uuid);
		if ($track && file_exists($track->name)) {
			$file = $track->meta()->mime_type !== "audio/mpeg"
				? $track->transcode()
				: $track->name;
			header("Content-Type: audio/mpeg");
			header("Content-Length: " . filesize($file));
			header("Accept-Ranges: bytes");
			header("Content-Transfer-Encoding: binary");
			readfile($file);
			exit;
		}
	}

	#[Get("/play/{uuid}", "track.play")]
	public function play(string $uuid): string
	{
		return template("muse/player/audio.php", [
			"src" => "/track/stream/$uuid",
		]);
	}
}
