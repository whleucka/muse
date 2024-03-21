<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group};
use ListenNotes\PodcastApi\Client;

#[Group(prefix: "/podcast")]
class PodcastController extends Controller
{
	#[Get("/", "podcast.index", ["HX-Replace-Url=/podcast"])]
	public function index(): string
	{
		$content = template("muse/podcast/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Get("/podcast/{id}", "podcast.podcast", ["api"])]
	function station(string $id): ?array
	{
		$key = config("muse.podcast_key");
		$client = new Client($key);
		$res = $client->fetchEpisodeById(['id' => $id]);
		$podcast = json_decode($res);
		if ($podcast) {
			return [
				"id" => $id,
				"src" => $podcast->audio,
				"title" => $podcast->title,
				"artist" => $podcast->podcast->title,
				"album" => 'Muse Podcast',
				"cover" => $podcast->thumbnail,
			];
		}
		return null;
	}
}
