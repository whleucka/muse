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
            $filesize = filesize($file);
            $offset = 0;
            $length = $filesize;

            if (isset($_SERVER['HTTP_RANGE'])) {
                $range = $_SERVER['HTTP_RANGE'];
                list(, $range) = explode('=', $range, 2);
                list($offset, $end) = explode('-', $range);
                $offset = intval($offset);
                if ($end) {
                    $length = intval($end) - $offset + 1;
                } else {
                    $length = $filesize - $offset;
                }
                header('HTTP/1.1 206 Partial Content');
                header("Content-Range: bytes $offset-" . ($offset + $length - 1) . "/$filesize");
            }

            header('Content-Type: audio/mpeg');
            header('Accept-Ranges: bytes');
            header("Content-Length: $length");
            header('Cache-Control: public, max-age=3600');
            header('Content-Disposition: inline; filename="audio.mp3"');
            header('Connection: keep-alive');

            $fp = fopen($file, 'rb');
            fseek($fp, $offset);
            $buffer = 8192;
            while (!feof($fp) && ($offset <= $length)) {
                $data = fread($fp, $buffer);
                echo $data;
                flush();
                $offset += $buffer;
            }
            fclose($fp);

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
