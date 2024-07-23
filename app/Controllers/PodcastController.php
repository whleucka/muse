<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};
use Error;

#[Group(prefix: "/podcast")]
class PodcastController extends Controller
{
	#[Get("/", "podcast.index", ["HX-Replace-Url=/podcast"])]
	public function index(): string
	{
		$content = template("muse/podcast/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}

    #[Get("/load", "podcast.load")]
    public function load()
    {
		$podcasts = session()->get("podcasts");
		if ($podcasts) {
			return template("muse/podcast/results.php", ["podcasts" => $podcasts]);
		}
		return null;
    }

	#[Post("/", "postcast.search")]
	public function search()
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
                $response = curl($url, "GET", $payload, $headers);
				if ($response['http_code'] === 200) {
                    $response = $response['response'];
                    $results = $response['results'];
					if (count($results) > 0) {
                        session()->set("podcasts", $results);
						return template("muse/podcast/results.php", ["podcasts" => $results]);
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
                hx_location("/server_error");
				http_response_code(500);
			}
		}
		return null;
	}

	/**
	 * Reset podcast list, clear session value
	 */
    #[Get("/reset", "playlist.reset")]
    public function reset(): string
    {
        session()->delete("podcasts");
        return $this->index();
    }
}
