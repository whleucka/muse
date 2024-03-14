<?php

namespace App\Controllers\Muse;

use App\Models\Radio;
use Nebula\Framework\Controller\Controller;
use StellarRouter\{Get, Group};

#[Group(prefix: "/radio")]
class RadioController extends Controller
{
	#[Get("/", "radio.index", ["HX-Replace-Url=/radio"])]
	public function index(): string
	{
		$content = template("muse/radio/index.php");

		return $this->render("layout/base.php", ["main" => $content]);
	}

	#[Get("/load", "radio.load")]
	public function load(): string
	{
		$radio_stations = db()->fetchAll("SELECT * FROM radio");

		return template("muse/radio/results.php", ["radio_stations" => $radio_stations ?? []]);
	}

	#[Get("/station/{uuid}", "radio.station", ["api"])]
	function station(string $uuid): ?array
	{
		$station = Radio::findByAttribute("uuid", $uuid);
		if ($station) {
			return [
				"uuid" => $station->uuid,
				"cover_url" => $station->cover_url,
				"src_url" => $station->src_url,
				"location" => $station->location,
				"station_name" => $station->station_name,
			];
		}
		return null;
	}
}

