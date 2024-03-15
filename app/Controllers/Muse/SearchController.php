<?php

namespace App\Controllers\Muse;

use App\Models\Track;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group, Post};

#[Group(prefix: "/search")]
class SearchController extends Controller
{
	#[Get("/", "search.index", ["HX-Replace-Url=/search"])]
	public function index(): string
	{
		$content = template("muse/search/index.php", [
			"term" => session()->get("term")
		]);

        return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Post("/", "search.post")]
	public function post(): ?string
	{
		$data = $this->validateRequest([
			"term" => ["required"],
		]);
		if (isset($data["term"])) {
			$tracks = Track::search($data["term"]);
			if ($tracks) {
				// Set the search term
				session()->set("term", $data["term"]);
			}
			return template("muse/search/results.php", ["tracks" => $tracks]);
		}
		// Clear search term
		session()->delete("term");
		return null;
	}
}
