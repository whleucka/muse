<?php

namespace App\Controllers;

use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};
use Error;

#[Group(prefix: "/podcast")]
class PodcastController extends Controller
{
    /**
     * Podcast index
     */
    #[Get("/", "podcast.index", ["HX-Replace-Url=/podcast"])]
    public function index(): string
    {
        $content = template("muse/podcast/index.php", ["term" => session()->get("podcast_term")]);

        return $this->render("layout/base.php", ["main" => $content, "has_more" => false]);
    }

    /**
     * Load podcast results
     */
    #[Get("/load", "podcast.load")]
    public function load()
    {
        $podcasts = session()->get("podcasts");
        if ($podcasts) {
            return template("muse/podcast/results.php", ["podcasts" => $podcasts, "has_more" => false]);
        }
        return null;
    }

    /**
     * Request next page
     */
    #[Get("/next", "podcast.next")]
    public function next()
    {
        $res = $this->fetch();
        return $res ?? null;
    }

    /**
     * cURL listennotes and search by term
     */
    private function fetch()
    {
        $key = config("muse.podcast_key");
        if (!trim($key)) {
            return "<p class='mt-3'>ListenNotes API key not found</p>";
        }
        $term = session()->get("podcast_term");
        try {
            $url = "https://listen-api.listennotes.com/api/v2/search";
            $next_offset = session()->get("podcast_offset") ?? 0;
            $payload = [
                "q" => $term,
                "type" => "episode",
                "sort_by_date" => 1,
                "language" => "English",
                "offset" => $next_offset
            ];
            $headers = ["X-ListenAPI-Key: $key"];
            $response = curl($url, "GET", $payload, $headers);
            if ($response['http_code'] === 200) {
                $response = $response['response'];
                $results = $response['results'];
                $podcasts = session()->get("podcasts") ?? [];
                $next_offset = $response['next_offset'];
                if (count($results) > 0 && $next_offset > 0) {
                    $results = [...$podcasts, ...$results];
                    session()->set("podcasts", $results);
                    session()->set("podcast_offset", $next_offset);
                    return template("muse/podcast/results.php", [
                        "podcasts" => $results,
                        "has_more" => intval($response["next_offset"]) > 0
                    ]);
                }
                return template("muse/podcast/results.php", [
                    "podcasts" => $podcasts,
                    "has_more" => intval($response["next_offset"]) > 0
                ]);
            } else {
                return "<p class='mt-3'>Error, see logs.</p>";
            }
        } catch (Error $ex) {
            error_log("listennotes error");
            error_log(print_r([
                "term" => $term,
                "message" => $ex->getMessage(),
                "file" => $ex->getFile() . ':' . $ex->getLine(),
                "trace" => $ex->getTraceAsString(),
            ], true));
            hx_location("/server_error");
            http_response_code(500);
        }
    }

    /**
     * Search for a podcast
     */
    #[Post("/", "postcast.search")]
    public function search()
    {
        $data = $this->validateRequest([
            "term" => ["required"],
        ]);
        if (isset($data["term"])) {
            session()->set("podcast_term", $data["term"]);
            return $this->fetch();
        }
        return null;
    }

    /**
     * Clear session values
     */
    private function clear()
    {
        session()->delete("podcasts");
        session()->delete("podcast_term");
        session()->delete("podcast_offset");
    }

    /**
     * Reset podcast list, clear session value
     */
    #[Get("/reset", "playlist.reset")]
    public function reset(): string
    {
        $this->clear();
        return $this->index();
    }
}
