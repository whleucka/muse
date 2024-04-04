<?php

namespace App\Controllers\Muse;

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
		$radio_stations = db()->fetchAll("SELECT uuid, src_url, location, station_name,
			cover_url
			FROM radio
			ORDER BY location, station_name");

		return template("muse/radio/results.php", ["radio_stations" => $radio_stations ?? []]);
	}
}
