<?php

/**
 * Carrots is a simple PHP image gallery script, perfect to easily build a portfolio site.
 * You just have to upload it to your server along with your files and that's it.
 * No code. No databases. Just you and your FTP client.
 *
 * For PHP 5 or higher
 *
 * @version 0.4 (2016-10-17)
 * @author David López
 * @copyright 2012-2016 David López
 * @license Released under the MIT License
 *
 */

require_once('settings.php');

class Carrots {

	private static $basePath; // Home path
	private static $galleryPath; // Gallery folder's path
	private static $cachePath; // Cache folder's path
	private $folders = []; // List of folders
	private $folder = false; // Current folder
	private $page = 1; // Current page
	private $images = []; // Images array

	public function __construct($folder) {
		global $settings;
		self::$basePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
		self::$galleryPath = self::$basePath . $settings['gallery_path'] . DIRECTORY_SEPARATOR;
		self::$cachePath = self::$galleryPath . $settings['cache_path'] . DIRECTORY_SEPARATOR;
		$this->setFolders();
		if ($folder) {
			$this->setFolder($folder);
			$this->setImages($this->getFolder());
		}
	}

	// Get the url of a path
	private function getUrl($path) {
		$filePathName = realpath($path);
		$filePath = realpath(dirname($path));
		$basePath = realpath($_SERVER['DOCUMENT_ROOT']);

		if (strlen($basePath) <= strlen($filePath)) {
			return 'http://' . $_SERVER['HTTP_HOST'] . substr($filePathName, strlen($basePath));
		}

		return '';
	}

	private function makeUrl($folder) {
		return $this->getUrl(self::$basePath) . DIRECTORY_SEPARATOR . rawurlencode($folder) . DIRECTORY_SEPARATOR;
	}

	private function makeGalleryUrl($folder) {
		return $this->getUrl(self::$galleryPath) . DIRECTORY_SEPARATOR . rawurlencode($folder) . DIRECTORY_SEPARATOR;
	}

	private function makeCacheUrl($folder) {
		return $this->getUrl(self::$cachePath) . DIRECTORY_SEPARATOR . rawurlencode($folder) . DIRECTORY_SEPARATOR;
	}

	// Get the home url
	public function getHomeUrl() {
		return $this->getUrl(self::$basePath) . DIRECTORY_SEPARATOR;
	}

	// Get the name of the current folder
	public function getTitle() {
		global $settings;

		if (!$this->getFolder()) { // Home page
			return $settings['title'];
		} else { // You're inside a folder
			return $this->sanitize($this->getFolder()) . ' - ' . $settings['title'];
		}
	}

	// Sanitize a string so there's no strange symbols
	private function sanitize($str) {
		return (preg_match('!!u', $str)) ? $str : utf8_encode($str);
	}

	// If not home page, set the current folder
	private function setFolder($folder) {
		$array = explode(DIRECTORY_SEPARATOR, $folder);
		$this->folder = $array[0];
		if (isset($array[1]) && $array[1]) $this->page = $array[1];
	}

	// Get the current folder
	public function getFolder() {
		return $this->folder;
	}

	// Load the folders list
	private function setFolders() {
		global $settings;

		if ($dh = opendir(self::$galleryPath)) {
			while (($filename = readdir($dh)) !== false) {
				if (is_dir(self::$galleryPath . $filename)
					&& (strpos($filename, '.') !== 0)
					&& (strpos($filename, '_') !== 0)) {
						$this->folders[] = $filename;
				}
			}
			closedir($dh);
		}

		$this->folders = $this->sortList($settings['order_menu'], $this->folders);
	}

	// Get the folders list
	public function getFolders() {
		return $this->folders;
	}

	// Load the images of a folder
	public function setImages($folder) {
		global $settings;

		$path = self::$galleryPath . $folder;
		if (!is_dir($path)) return;

		$this->images = [];
		if ($dh = opendir($path)) {
			while (($filename = readdir($dh)) !== false) {
				$file = $path . DIRECTORY_SEPARATOR . $filename;
				$info = pathinfo($file);
				if (is_file($file)
					&& (strpos($filename, '.') !== 0)
					&& (in_array(strToLower($info['extension']), $settings['extensions']))) {
						$this->images[] = $filename;
				}
			}
			closedir($dh);
		}

		$this->images = $this->sortList($settings['order_files'], $this->images);
	}

	private function sortList($order, $items) {
		natcasesort($items);
		switch ($order) {
			case 'ASC':
				break;
			case 'DESC':
				$items = array_reverse($items);
				break;
			default:
				break;
		}

		return $items;
	}

	// Get the images of the actual folder
	private function getImages() {
		return $this->images;
	}

	// Display the folders menu
	public function displayMenu() {
		global $settings;

		if (!$settings['show_menu']) return;

		echo '<ul>';
		foreach ($this->folders as $folder) {
			echo ($folder != $this->getFolder()) ? '<li>' : '<li class="active">';
			echo '<a href="' . $this->makeUrl($folder) . '">' . $this->sanitize($folder) . '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}

	// Display the content of the actual folder
	public function displayContent() {
		global $settings;

		$folder = $this->getFolder();
		if (!$folder && $settings['show_covers_grid']) { // Home page
			$this->displayFoldersGrid();
		} elseif ($folder) {
			if (in_array($folder, $this->folders)) { // If the folder exists, display the content
				echo '<h2>' . $this->sanitize($folder) . '</h2>';
				// If there's an info file, display it
				if ($info = $this->getInfo($folder)) {
					echo '<p>' . $info . '</p>';
				}
				// If the folder's not empty, display images
				if (sizeof($this->images)) {
					$this->displayImages();
					if ($settings['img_per_page']) $this->displayPagination();
				}
			} else { // Page not found
				echo '<h2>The page doesn\'t exist</h2>';
				return;
			}
		}
	}

	// Display the folders grid on the home page
	private function displayFoldersGrid() {
		global $settings;
		echo '<ul class="grid">';
		foreach ($this->folders as $folder) {
			// Check if the folder is not empty
			if (!$cover = $this->getCover($folder)) continue;
			echo '<li>';
			echo '<a href="' . $this->makeUrl($folder) . '">' . $cover . '</a>';
			if ($settings['show_title'])
				echo '<span>' . $this->sanitize($folder) . '</span>';
			echo '</li>';
		}
		echo '</ul>';
	}

	// Get the cover of a folder
	private function getCover($folder) {
		global $settings;

		// Get the images and pick the first one to use as cover
		$this->setImages($folder);
		if (sizeof($this->images)) {
			return $this->getImage($folder, $this->images[0]);
		}
		return false; // The folder's empty
	}

	// Return the image tag
	private function getImage($folder, $name) {
		$img_path = self::$galleryPath . $folder . DIRECTORY_SEPARATOR . $name;
		if (file_exists($img_path)) {
			$thumb_path = self::$cachePath . $folder . DIRECTORY_SEPARATOR . $name;
			if (!file_exists($thumb_path)) {
				$this->makeThumb($img_path, $folder);
			}
			$thumb_url = $this->makeCacheUrl($folder) . $name;
			$alt = (!$this->folder) ? $folder : $name;
			return '<img src="' . $thumb_url . '" alt="'. $alt . '" />';
		}
		return false; // There's no image
	}

	// Get the info of a folder
	private function getInfo($folder) {
		if (file_exists(self::$galleryPath . $folder . DIRECTORY_SEPARATOR . '_info.txt')) {
			return nl2br(file_get_contents(self::$galleryPath . $folder . DIRECTORY_SEPARATOR . '_info.txt'));
		}

		return false; // There's no info file
	}

	// Display the images of a folder
	private function displayImages() {
		global $settings;

		$folder = $this->getFolder();
		$url = $this->makeGalleryUrl($folder);
		$aux = $settings['img_per_page']*($this->page-1);
		$total = ($settings['img_per_page']) ? $settings['img_per_page'] : count($this->images);
		echo '<ul class="' . $settings['display_mode'] . '">';
		for ($i = 0; $i + $aux < count($this->images) && $i < $total; $i++) {
			$file = $this->images[$i+$aux];
			if (!$settings['show_cover']) {
				if ($this->images[0] == $file) continue;
			}
			echo '<li>';
			echo '<a href="' . $url . $file . '" rel="lightbox[' . $folder . ']">';
			if ($settings['display_mode'] == 'list') {
				echo '<img src="' . $url . $file .'" alt="'. $file . '" />';
			} else {
				echo $this->getImage($folder, $file);
			}
			echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}

	// Display a pagination menu
	private function displayPagination() {
		global $settings;

		$total = ceil(count($this->images) / $settings['img_per_page']);
		if ($total == 1) return;
		$url = $this->makeUrl($this->getFolder());
		echo '<div class="pagination">';
		for ($i = 1; $i <= $total; $i++) {
			$class = ($this->page == $i) ? 'active' : '';
			echo '<a class="' . $class . '" href="' . $url . $i . '/">' . $i . '</a>';
		}
		echo '</div>';
	}

	// Create a thumbnail image
	private function makeThumb($img, $folder) {
		global $settings;

		// Get dimensions and type of original image
		list($w, $h, $type) = getimagesize($img);
		switch ($type) {
			case '1':
				$import_img = 'imagecreatefromgif';
				$export_img = 'imagegif';
				break;
			case '2':
				$import_img = 'imagecreatefromjpeg';
				$export_img = 'imagejpeg';
				break;
			case '3':
				$import_img = 'imagecreatefrompng';
				$export_img = 'imagepng';
				break;
			default: return false;
		}
		$old = $import_img($img);

		//New dimensions
		$nw = $settings['thumb_w'];
		$nh = $settings['thumb_h'];

		// Cropping dimensions
		$src_x = 0;
		$src_y = 0;

		//Resize & crop image based on smallest ratio
		if (($w/$h) < ($nw/$nh)) {
			//Determine new ratio on width
			$ratio = $nw / $w;
			//Detemine cropping dimensions for height
			$src_y = floor(($h - $nh/$ratio)/2);
			$h = $nh / $ratio;
		} else {
			//Detemine new ratio on height
			$ratio = $nh / $h;
			//Detemine cropping dimensions for width
			$src_x = floor(($w - $nw/$ratio)/2);
			$w = $nw / $ratio;
		}

		$new = imagecreatetruecolor($nw, $nh);
		imagecopyresampled($new, $old, 0, 0, $src_x, $src_y, $nw, $nh, $w, $h);

		if (!file_exists(self::$cachePath . $folder . DIRECTORY_SEPARATOR)) {
			if (!mkdir(self::$cachePath . $folder . DIRECTORY_SEPARATOR, 0755, true)) {
				die("There was a problem creating the cache folder. Please try again.");
			}
		}

		$img_name = str_replace(array(self::$galleryPath, $folder), '', $img);
		$export_img($new, self::$cachePath . $folder . $img_name);
		imagedestroy($old);
	}

} ?>