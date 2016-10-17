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

require_once('classes/class.carrots.php');
$carrots = new Carrots($_GET['url']);
$homeUrl = $carrots->getHomeUrl();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title><?php echo $carrots->getTitle(); ?></title>
<link rel="stylesheet" href="<?php echo $homeUrl; ?>styles/<?php echo $settings['theme']; ?>" />
<link rel="stylesheet" href="<?php echo $homeUrl; ?>libraries/slimbox2/css/slimbox2.css" />
<script src="<?php echo $homeUrl; ?>libraries/jquery/jquery-1.4.min.js"></script>
<script src="<?php echo $homeUrl; ?>libraries/slimbox2/slimbox2.js"></script>
<!--[if lt IE 9]>
<script src="<?php echo $homeUrl; ?>libraries/html5shiv/html5shiv.min.js"></script>
<![endif]-->
</head>

<body>
	<div id="wrapper">
		<header>
			<h1 class="page-title">
				<a href="<?php echo $homeUrl; ?>"><?php echo $settings['title']; ?></a>
			</h1>
		</header>

		<nav>
			<?php $carrots->displayMenu(); ?>
		</nav>

		<section class="main">
			<?php $carrots->displayContent(); ?>
		</section>

		<footer>
			<p>Powered by <a href="http://www.poweredbycarrots.net/" target="_blank">Carrots</a> <?php echo $settings['version']; ?>
		</footer>
	</div>
</body>
</html>