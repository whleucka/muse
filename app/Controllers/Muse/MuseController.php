<?php

namespace App\Controllers\Muse;

use Nebula\Framework\Controller\Controller;
use StellarRouter\Get;
use Exception;
use App\Models\Track;

class MuseController extends Controller
{
	#[Get("/colours", "muse.colors")]
	public function colours_red(): string
	{
		return template("components/colours.php", [
			"colour" => sprintf("rgb(%s,%s,%s)", rand(0,255), rand(0,255), rand(0,255))
		]);
	}

    #[Get("/cover/{uuid}/{width}/{height}", "muse.image")]
    public function image(string $uuid, int $width, int $height): mixed
    {
        $track = Track::findByAttribute("uuid", $uuid);
        if ($track) {
            try {
                // Set headers
                $expires = 60 * 60 * 24 * 30; // about a month
                header("Cache-Control: public, max-age={$expires}");
                header("Expires: " . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
                header("Access-Control-Allow-Origin: *");
                header("Content-Type: image/png");

                $cache_directory = "/tmp/";

                // Generate a unique cache filename based on the parameters.
				$dir_name = dirname($track->name);
                $cache_filename = md5($dir_name) . '.png';
                $cache_filepath = $cache_directory . $cache_filename;

                // Check if the cached image exists.
                if (file_exists($cache_filepath)) {
                    // Serve the cached image.
                    readfile($cache_filepath);
                    exit;
                }

                $storage_path = config("path.storage");
				$filename = basename($track->meta()->cover);
				$image = $storage_path . "/covers/" . $filename;
                if (file_exists($image)) {
                    $imagick = new \imagick($image);
                    //crop and resize the image
                    $imagick->cropThumbnailImage($width, $height);
                    //remove the canvas
                    $imagick->setImagePage(0, 0, 0, 0);
                    $imagick->setImageFormat("png");
                    // Save the resized image to the cache directory.
                    $imagick->writeImage($cache_filepath);
                    echo $imagick->getImageBlob();
                    exit;
                } else {
                    // Serve the
					$public_path = config("path.public");
                    $no_album = $public_path . "/img/no-album.png";
                    readfile($no_album);
                    exit;
                }
            } catch (Exception $ex) {
                error_log("imagick error: check logs " . $ex->getMessage());
                exit;
            }
        }
		return null;
    }
}

