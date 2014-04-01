<?php

/**
 * Carrots is a simple PHP image gallery script, perfect to easily build a portfolio site.
 * You just have to upload it to your server along with your files and that's it.
 * No code. No databases. Just you and your FTP client.
 *
 * For PHP 5 or higher
 *
 * @version 0.3 (2014-04-01)
 * @author David López
 * @copyright 2012-2014 David López
 * @license Released under the MIT License
 *
 */

require_once('settings.php');

class Carrots{

	private static $basePath;	
	private static $baseUrl;
	private static $galleryPath;
	private static $cachePath;
	private $folders = array();
	private $folder = '';
	private $images;
	
	public function __construct($folder) {
		global $settings;
		self::$basePath = dirname(dirname(__FILE__)) . '/';
		self::$baseUrl = $this->getUrl(self::$basePath) . '/';
		self::$galleryPath = self::$basePath . $settings['gallery_path'] . '/';
		self::$cachePath = self::$basePath . $settings['cache_path'] . '/';
		$this->setFolders();
		if ($folder != '') {
			$this->setFolder($folder);
			$this->setImages($this->getFolder());
		}
	}

	// Get the base url of the app
	public function getBaseUrl() { return self::$baseUrl; }

	// Get the url of a path
	private function getUrl($path) {
		$filePathName = realpath($path);
		$filePath = realpath(dirname($path));
		$basePath = realpath($_SERVER['DOCUMENT_ROOT']);

		if (strlen($basePath) <= strlen($filePath)) {
			return 'http://' . $_SERVER['HTTP_HOST'] . substr($filePathName, strlen($basePath));
		} else return '';
	}

	// Get the name of the actual folder
	public function getTitle() {
		global $settings;

		if ($this->getFolder() == '') return $settings['title']; 
		else return $this->sanitize($this->getFolder()) . ' - ' . $settings['title'];
	}

	// Sanitize a string so there's no strange symbols
	private function sanitize($str) {
		return (preg_match('!!u', $str)) ? $str : utf8_encode($str);
	}

	// Set the actual folder
	private function setFolder($folder) {
		$array = explode('/',$folder);
		$this->folder = str_replace('/','',$array[0]);
	}

	// Get the actual folder
	public function getFolder() { return $this->folder; }
	
	// Load the folders list
	private function setFolders() {
		global $settings;

		if ($dh = opendir(self::$galleryPath)) {
			while (($filename = readdir($dh)) !== false) {
				if (is_dir(self::$galleryPath . $filename)
					&& (strpos($filename,".") !== 0)
					&& (strpos($filename,"_") !== 0)) {
						$this->folders[] = $filename;
				}
			}
			closedir($dh);
		}
		
		switch ($settings['order_menu']) {
			case 'ASC': sort($this->folders); break;
			case 'DESC': rsort($this->folders); break;
			default: sort($this->folders); break;
		}
	}

	// Get the folders array
	public function getFolders() { return $this->folders; }

	// Load the images of a folder
	public function setImages($folder) {
		global $settings;

		$path = self::$galleryPath . $folder;
		if (!is_dir($path)) return;

		$this->images = array();
		if ($dh = opendir($path)) {
			while (($filename = readdir($dh)) !== false) {
				$file = $path . '/' . $filename;
				$info = pathinfo($file);
				if (is_file($file)
					&& (strpos($filename,".") !== 0)
					&& (in_array(strToLower($info["extension"]),$settings['extensions']))) {
						$this->images[] = $filename;
				}
			}
			closedir($dh);
		}

		switch ($settings['order_files']) {
			case 'ASC': sort($this->images); break;
			case 'DESC': rsort($this->images); break;
			default: sort($this->images); break;
		}
	}

	// Get the images of the actual folder
	private function getImages() { return $this->images; }

	// Display the folders menu
	public function displayMenu() {
		global $settings;

		if (!$settings['show_menu']) return;

		echo '<ul>';
		foreach ($this->getFolders() as $folder) {			
			echo ($this->getFolder() != $folder) ? '<li>' : '<li class="active">';
			$url = self::$baseUrl . rawurlencode($folder) . '/';
			echo '<a href="' . $url . '">' . $this->sanitize($folder) . '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}

	// Display the content of the actual folder
	public function displayContent() {
		global $settings;

		$folder = $this->getFolder();

		// Page not found
		if ($folder != '' && !in_array($folder, $this->getFolders())) {
			echo '<h2>The page doesn\'t exist</h2>';
			return;
		}

		// Home page
		if ($folder == '' && $settings['show_covers_grid']) {
			$this->displayFoldersGrid();	
		} elseif ($folder != '') { // Display the folder content
			echo '<h2>' . $this->sanitize($folder) . '</h2>';		
			// If there's an info file, display it
			if ($info = $this->getInfo($folder)) {
				echo '<p>' . $info . '</p>';
			}			
			// If the the folder is not empty, display images
			if (sizeof($this->images) > 0) {
				$this->displayImages();
			}
		}
	}

	// Display the folders grid on the home page
	private function displayFoldersGrid() {
		global $settings;
		echo '<ul class="grid">';
		foreach ($this->getFolders() as $folder) {
			// Check if the folder is not empty
			if (!$cover = $this->getCover($folder)) continue;
			echo '<li>';
			echo '<a href="' . self::$baseUrl . rawurlencode($folder) . '/">' . $cover . '</a>';
			if ($settings['show_title'])
				echo '<span>' . $this->sanitize($folder) . '</span>';
			echo '</li>';
		}
		echo '</ul>';
	}

	// Display the cover of a folder
	private function getCover($folder) {
		global $settings;

		// Get the images and pick the first one to use as cover
		$this->setImages($folder);
		if (sizeof($this->images) > 0) {
			$img_path = self::$galleryPath . $folder . '/' . $this->images[0];
			$thumb_path = self::$cachePath . $folder . '/' . $this->images[0];
			$thumb_url = $this->getUrl(self::$cachePath) . '/' . rawurlencode($folder) . '/' . $this->images[0];
			if (file_exists($img_path)) {
				if (!file_exists($thumb_path)) {
					$this->makeThumb($img_path, self::$cachePath, $folder);
				}
				return '<img src="' . $thumb_url . '" alt="'. $this->sanitize($folder) . '" />';
			}
		}

		return false; // The folder's empty
	}

	// Get the info of a folder
	private function getInfo($folder) {
		if (file_exists(self::$galleryPath . $folder . '/_info.txt')) {
			return nl2br(file_get_contents(self::$galleryPath . $folder . '/_info.txt'));
		}
		return false; // There's no info file
	}

	// Display the images of a folder
	private function displayImages() {
		global $settings;

		$url = $this->getUrl(self::$galleryPath) . '/' . rawurlencode($this->getFolder()) . '/';
		$thumb_url = $this->getUrl(self::$cachePath) . '/' . rawurlencode($this->getFolder()) . '/';
		
		echo '<ul class="' . $settings['display_mode'] . '">';
		foreach ($this->images as $file) {
			if (!$settings['show_cover']) {
				if ($this->images[0] == $file) continue;
			}
	
			echo '<li>';
			echo '<a href="' . $url . $file . '" rel="lightbox[' . $this->getFolder() . ']">';
			if ($settings['display_mode'] == 'list') {
				echo '<img src="' . $url . $file .'" alt="'. $file . '" />';
			} else {
				$thumb_path = self::$cachePath . $this->folder . '/' . $file;
				if (!file_exists($thumb_path)) {
					$img_path = self::$galleryPath . $this->folder . '/' . $file;
					$this->makeThumb($img_path, self::$cachePath, $this->folder);
				}
				echo '<img src="' . $thumb_url . $file .'" alt="'. $file . '" />';
			}
			echo '</a>';
			echo '</li>';
		}		
		echo '</ul>';
	}

	// Create a thumbnail image
	private function makeThumb($img, $basePath, $folder) {
		global $settings;

		$src_x = 0;
		$src_y = 0;

		if (preg_match('/[.](jpg|jpeg)$/i', $img)) {
			$old = imagecreatefromjpeg($img);
		} else if (preg_match('/[.](gif)$/i', $img)) {
			$old = imagecreatefromgif($img);
		} else if (preg_match('/[.](png)$/i', $img)) {
			$old = imagecreatefrompng($img);
		}

		//Get dimensions of original image
		$width_old = imagesx($old);
		$height_old = imagesy($old);

		//New dimensions
		$width_new = $settings['thumb_w'];
		$height_new = $settings['thumb_h'];

		//Resize & crop image based on smallest ratio
		if (($width_old/$height_old) < ($width_new/$height_new)) {
			//Determine Resize ratio on width
			$ratio = $width_new / $width_old;
			//Detemine cropping dimensions for height
			$crop =  $height_old - ($height_new/$ratio) ;
			$height_old = $height_old - $crop;
			$src_y = floor($crop/2);
		} else {
			//Detemine Resize ratio on height
			$ratio = $height_new / $height_old;
			//Detemine cropping dimensions for width
			$crop = $width_old - ($width_new/$ratio);
			$width_old = $width_old - $crop;
			$src_x = floor($crop/2);
		}

		$new = imagecreatetruecolor($width_new, $height_new);
		imagecopyresampled($new, $old, 0, 0, $src_x, $src_y, $width_new, $height_new, $width_old, $height_old);

		if (!file_exists($basePath . $folder . '/')) {
			if(!mkdir($basePath . $folder . '/',0755)) {
				die("There was a problem. Please try again");
			}
		}

		imagejpeg($new, $basePath . $folder . str_replace(array(self::$galleryPath, $folder),'',$img));

		imagedestroy($old);
	}

} ?>