<?php

/**
 * Carrots is a simple PHP image gallery script, perfect to easily build a portfolio site.
 * You just have to upload it to your server along with your files and that's it.
 * No code. No databases. Just you and your FTP client.
 *
 * For PHP 5 or higher
 *
 * @version 0.2 (2013-01-28)
 * @author David López
 * @copyright 2012 David López
 * @license Released under the MIT License
 *
 */

require_once('classes/class.carrots.php');

$carrots = new Carrots($_GET['url']);
$siteUrl = $carrots->getBaseUrl();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title><?php echo $carrots->getTitle(); ?></title>
<link rel="stylesheet" href="<?php echo $siteUrl; ?>styles/<?php echo $settings['theme']; ?>" />
<link rel="stylesheet" href="<?php echo $siteUrl; ?>libraries/slimbox2/css/slimbox2.css" />
<script src="<?php echo $siteUrl; ?>libraries/jquery/jquery-1.4.min.js"></script>
<script src="<?php echo $siteUrl; ?>libraries/slimbox2/slimbox2.js"></script>
<!--[if lt IE 9]>
<script src="<?php echo $siteUrl; ?>libraries/html5shiv/html5shiv.js"></script>
<![endif]-->
</head>

<body>
	<div id="wrapper">
		<header>
			<h1>
				<a href="<?php echo $siteUrl; ?>">
					<?php echo $settings['title']; ?>
				</a>
			</h1>
		</header>

		<nav>
			<?php $carrots->displayMenu(); ?>
		</nav>

		<section>
			<?php $carrots->displayContent(); ?>	
		</section>

		<footer>
			<p>Powered by <a href="http://www.poweredbycarrots.net/" target="_blank">Carrots</a> <?php echo $settings['version']; ?>
		</footer>
	</div>
</body>
</html>