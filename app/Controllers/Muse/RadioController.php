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
		$radio_stations = db()->fetchAll("SELECT uuid, src_url as src, location as title,
			station_name as artist, 'Muse Radio' as album, cover_url as cover
			FROM radio
			ORDER BY location, station_name");

		return template("muse/radio/results.php", ["radio_stations" => $radio_stations ?? []]);
	}

	#[Get("/station/{uuid}", "radio.station", ["api"])]
	function station(string $uuid): ?array
	{
		$station = Radio::findByAttribute("uuid", $uuid);
		if ($station) {
			return [
				"uuid" => $station->uuid,
				"src" => $station->src_url,
				"title" => $station->location,
				"artist" => $station->station_name,
				"album" => 'Muse Radio',
				"cover" => $station->cover_url,
			];
		}
		return null;
	}
}

