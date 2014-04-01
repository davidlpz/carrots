<?php 

//SETTINGS FILE

$settings['version'] = 'v0.3';

// Page title
$settings['title'] = 'Carrots';

// Gallery folder's name
$settings['gallery_path'] = 'images';

// Cache folder's name
$settings['cache_path'] = 'cache';

// Theme to display
$settings['theme'] = 'centered.css';

// Order of the navegation menu
// Values: ['ASC' | 'DESC']
$settings['order_menu'] = 'ASC';

// Filetypes that can will be display
$settings['extensions'] = array();
$settings['extensions'][] = 'png';
$settings['extensions'][] = 'jpg';
$settings['extensions'][] = 'jpeg';
$settings['extensions'][] = 'gif';

// Display the folders menu 
// Values: [ true | false ]
$settings['show_menu'] = true;

// Display the folders grid at the home page 
// Values: [ true | false ]
$settings['show_covers_grid'] = true;

// Display the cover inside the gallery 
// Values: [ true | false ]
$settings['show_cover'] = true;

// Display the title of the gallery 
// Values: [ true | false ]
$settings['show_title'] = true;

// Set the width and height of the cover thumb
$settings['thumb_w'] = 165;
$settings['thumb_h'] = 125;

// Order of the files
// Values: ['ASC' | 'DESC']
$settings['order_files'] = 'ASC';

// Display images in grid or list mode 
// Values: [ 'grid' | 'list' ]
$settings['display_mode'] = 'grid';
	
?>