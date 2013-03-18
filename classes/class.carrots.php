<?php

/**
 * Carrots is a simple PHP image gallery script, perfect to easily build a portfolio site.
 * You just have to upload it to your server along with your files and that's it.
 * No code. No databases. Just you and your FTP client.
 *
 * For PHP 5 or higher
 *
 * @version 0.21 (2012-03-18)
 * @author David López
 * @copyright 2012 David López
 * @license Released under the MIT License
 *
 */

require_once('settings.php');

class Carrots{

	private static $basePath;	
	private static $baseUrl;
	private static $galleryPath;
	private static $galleryUrl;
	private $folders = array();
	private $folder = '';
	private $images;
	
	public function __construct($folder) {
		global $settings;
		self::$basePath = dirname(dirname(__FILE__)) . '/';
		self::$baseUrl = $this->getUrl(self::$basePath) . '/';
		self::$galleryPath = self::$basePath . $settings['path'] . '/';
		self::$galleryUrl = $this->getUrl(self::$galleryPath) . '/';
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
	private function displayFoldersGrid(){
		echo '<ul class="grid">';
		foreach ($this->getFolders() as $folder) {
			// Check the folder is not empty
			if (!$cover = $this->getCover($folder)) continue;
			echo '<li>';
			echo '<a href="' . self::$baseUrl . rawurlencode($folder) . '/">';
			echo $cover;
			echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}

	// Display the cover of a folder
	private function getCover($folder){
		global $settings;

		$baseUrl = self::$galleryUrl . rawurlencode($folder) . '/';
		
		// First, check if there's stablished a cover for the folder
		foreach ($settings['extensions'] as $ext) {
			if (file_exists(self::$galleryPath . $folder . '/_cover.' . $ext)) {
				return '<img src="' . $baseUrl . '_cover.' . $ext . '" alt="'. $folder . '" />';
			}
		}

		// There's no cover, so get the images and pick the first one
		$this->setImages($folder);
		if (sizeof($this->images) > 0) {
			return '<img src="' . $baseUrl . $this->images[0] .'" alt="'. $folder . '" />';
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

		$baseUrl = self::$galleryUrl . rawurlencode($this->getFolder()) . '/';

		echo '<ul class="' . $settings['display_mode'] . '">';
		foreach ($this->images as $file) {
			if (!$settings['show_cover']) {
				if (strpos($file,'_cover') !== false) continue;
			}
			echo '<li>';
			echo '<a href="' . $baseUrl . $file . '" rel="lightbox[' . $this->getFolder() . ']">';
			echo '<img src="' . $baseUrl . $file . '" alt="' . $file . '"/>';
			echo '</a>';
			echo '</li>';
		}		
		echo '</ul>';
	}

} ?>