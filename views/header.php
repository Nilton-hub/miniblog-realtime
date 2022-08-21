<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title><?= $title; ?></title>
		<link href="http://localhost/views/assets/css/style.css" rel="stylesheet">
		<link rel="icon" href="http://localhost:80/views/assets/images/favicon.jpg">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?= ($styles ?? ''); ?>
		<meta charset="UTF-8">
	</head>
	<body>
	<nav class="main-nav">
		<button id="toggle-menu">MENU</button>
		<ul>
			<li><a href="http://localhost/artigos">Artigos</a></li>
			<li><a href="http://localhost/">Início</a></li>
			<li><a href="http://localhost/posts">Seus Posts</a></li>
			<li>
			<?php
				if (isset($_SESSION['user'])) :
					echo '<a href="http://localhost/sair" style="">[ Sair ]</a>'; //position: absolute; top: 10px; right: 10px;
				else:
					echo '<a href="http://localhost/login" style="">[ Fazer Login ]</a>';
				endif;
			?>
			</li>
		</ul>
	</nav>
	<?php
	echo (isset($message) ? 
		'<div class="message">
			<button>&times;</button>
			' . $message . '
		</div>' : null);
	unset($_SESSION['message']);
	 ?>
