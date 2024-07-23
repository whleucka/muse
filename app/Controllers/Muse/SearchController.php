<?php

namespace App\Controllers\Muse;

use App\Models\Track;
use Error;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};

#[Group(prefix: "/search")]
class SearchController extends Controller
{
	#[Get("/", "search.index", ["HX-Replace-Url=/search"])]
	public function index(): string
	{
		$content = template("muse/search/index.php", [
			"term" => session()->get("search_term"),
		]);

        return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Get("/load", "search.music")]
	public function music_load(): ?string
	{
        $tracks = session()->get("search_tracks");
        if ($tracks) {
		    return template("muse/search/results.php", ["tracks" => $tracks]);
        }
        return null;
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
                session()->set("search_tracks", $tracks);
				session()->set("search_term", $data["term"]);
				return template("muse/search/results.php", ["tracks" => $tracks]);
			} else {
				return "<p class='mt-3'>No results found.</p>";
			}
		}
		session()->delete("search_term");
		return null;
	}

	// This is a better example
	#[Get("/artist", "search.artist")]
	public function artist()
	{
		$artist = $this->request()->get("term");
		if ($artist) {
            $tracks = Track::search($artist, "artist");
            session()->set("search_tracks", $tracks);
		}
		// Let's go!
		header('HX-Location: {"path":"/search", "swap":"outerHTML", "target":"#main", "select":"#main"}');
	}

	#[Post("/podcast", "search.podcast")]
	public function podcast(): ?string
	{
		$data = $this->validateRequest([
			"term" => ["required"],
		]);
		if (isset($data["term"])) {
			$key = config("muse.podcast_key");
            if (!trim($key)) {
                return "<p class='mt-3'>ListenNotes API key not found</p>";
            }
			try {
                $url = "https://listen-api.listennotes.com/api/v2/search";
                $payload = [
                    "q" => $data["term"],
                    "type" => "episode",
                    "sort_by_date" => 1,
                    "language" => "English",
                ];
                $headers = ["X-ListenAPI-Key: $key"];
                $response = curlRequest($url, "GET", $payload, $headers);
				if ($response['http_code'] === 200) {
                    $response = $response['response'];
                    $results = $response['results'];
					if (count($results) > 0) {
						return template("muse/podcast/results.php", ["results" => $results]);
					} else {
						return "<p class='mt-3'>No results found.</p>";
					}
                } else {
                    return "<p class='mt-3'>Error, see logs.</p>";
                }
			} catch (Error $ex) {
				error_log("listennotes error");
				error_log(print_r([
					"term" => $data["term"],
					"message" => $ex->getMessage(),
					"file" => $ex->getFile() . ':' . $ex->getLine(),
					"trace" => $ex->getTraceAsString(),
				], true));
				header('HX-Location: {"path": "/server-error", "target": "#main", "select": "#main"}');
				http_response_code(500);
			}
		}
		return null;
	}

	#[Post("/music/reset", "search.music.reset")]
	public function reset()
	{
		session()->delete("search_term");
		session()->delete("search_tracks");
		return $this->index();
	}

}
