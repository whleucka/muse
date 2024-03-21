<?php

namespace App\Controllers\Muse;

use App\Models\Track;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};
use ListenNotes\PodcastApi\Client;

#[Group(prefix: "/search")]
class SearchController extends Controller
{
	#[Get("/", "search.index", ["HX-Replace-Url=/search"])]
	public function index(): string
	{
		$content = template("muse/search/index.php", [
			"term" => session()->get("search_term")
		]);

        return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Post("/music", "search.music")]
	public function music(): ?string
	{
		$data = $this->validateRequest([
			"term" => ["required"],
		]);
		if (isset($data["term"])) {
			$tracks = Track::search($data["term"]);
			if ($tracks) {
				session()->set("search_term", $data["term"]);
				return template("muse/search/results.php", ["tracks" => $tracks]);
			}
		}
		session()->delete("search_term");
		return null;
	}

	#[Post("/podcast", "search.podcast")]
	public function podcast(): ?string
	{
		$data = $this->validateRequest([
			"term" => ["required"],
		]);
		if (isset($data["term"])) {
			$key = config("muse.podcast_key");
			$client = new Client($key);
			$res = $client->search([
				"q" => $data["term"],
				"type" => "episode",
				"sort_by_date" => 1,
				"language" => "English",
			]);
			$results = json_decode($res);
			if ($results) {
				return template("muse/podcast/results.php", ["results" => $results]);
			}
		}
		return null;
	}
}
